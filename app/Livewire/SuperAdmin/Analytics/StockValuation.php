<?php

namespace App\Livewire\SuperAdmin\Analytics;

use App\Models\Branch;
use App\Models\StockMovement;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class StockValuation extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $selectedBranch = '';
    public $movementType = '';
    public $selectedItem = null;
    public $searchTerm = '';
    public $selectedCategory = '';
    public $sortColumn = 'total_value';
    public $sortDirection = 'desc';
    public $viewMode = 'table'; // table, cards, list, bars, accordion

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'movementType',
        'selectedItem'
    ];

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function getAvailableItems()
    {
        $query = Stock::with('item');

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        return $query
            ->when($this->itemSearch, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->itemSearch . '%')
                      ->orWhere('sku', 'like', '%' . $this->itemSearch . '%');
                });
            })
            ->limit(50)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'name' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'uom' => $stock->item->uom,
                ];
            });
    }

    public function getCategoryValuation()
    {
        $query = Stock::with('item');

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        if ($this->selectedCategory) {
            $query->whereHas('item', function ($q) {
                $q->where('category', $this->selectedCategory);
            });
        }

        $stocks = $query
            ->selectRaw('item_id, SUM(quantity_available * average_cost) as available_value, SUM((quantity_available + quantity_reserved) * average_cost) as total_value, COUNT(*) as item_count')
            ->groupBy('item_id')
            ->get();

        return [
            'labels' => $stocks->map(fn($s) => $s->item->category)->toArray(),
            'series' => $stocks->pluck('total_value')->toArray(),
        ];
    }

    public function getTopItems()
    {
        $query = Stock::with('item');

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        if ($this->selectedCategory) {
            $query->whereHas('item', function ($q) {
                $q->where('category', $this->selectedCategory);
            });
        }

        return $query
            ->orderByDesc(DB::raw('(quantity_available + quantity_reserved) * average_cost'))
            ->limit(10)
            ->get();
    }

    public function getStocks()
    {
        $query = Stock::with('item');

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        if ($this->selectedCategory) {
            $query->whereHas('item', function ($q) {
                $q->where('category', $this->selectedCategory);
            });
        }

        if ($this->searchTerm) {
            $query->whereHas('item', function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $query->orderBy($this->sortColumn, $this->sortDirection);

        return $query;
    }

    public function getSummary()
    {
        $query = Stock::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        if ($this->selectedCategory) {
            $query->whereHas('item', function ($q) {
                $q->where('category', $this->selectedCategory);
            });
        }

        $stocks = $query->get();

        return [
            'total_value' => $stocks->sum(fn($s) => ($s->quantity_available + $s->quantity_reserved) * $s->average_cost),
            'available_value' => $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost),
            'reserved_value' => $stocks->sum(fn($s) => $s->quantity_reserved * $s->average_cost),
            'damaged_value' => $stocks->sum(fn($s) => $s->quantity_damaged * $s->average_cost),
            'total_items' => $stocks->count(),
        ];
    }

    public function getCategories()
    {
        return Stock::with('item')
            ->when($this->selectedBranch, function ($query) {
                $query->where('branch_id', $this->selectedBranch);
            })
            ->get()
            ->pluck('item.category')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getCategoryGroupedData()
    {
        $stocks = $this->getStocks()->get();
        
        $grouped = $stocks->groupBy(fn($s) => $s->item->category)->map(function ($items) {
            $totalValue = $items->sum(fn($s) => ($s->quantity_available + $s->quantity_reserved) * $s->average_cost);
            return [
                'category' => $items->first()->item->category,
                'items' => $items,
                'total_value' => $totalValue,
                'item_count' => $items->count(),
                'total_qty' => $items->sum(fn($s) => $s->quantity_available + $s->quantity_reserved),
            ];
        })->sortByDesc('total_value')->values();

        return $grouped;
    }

    public function render()
    {
        return view('livewire.super-admin.analytics.stock-valuation', [
            'summary' => $this->getSummary(),
            'categories' => $this->getCategories(),
            'stocks' => $this->getStocks()->paginate(15),
            'categoryValuation' => $this->getCategoryValuation(),
            'topItems' => $this->getTopItems(),
            'categoryGroupedData' => $this->getCategoryGroupedData(),
        ]);
    }
}
