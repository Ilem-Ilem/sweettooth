<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class StockValuation extends Component
{
    use WithPagination;

    public $selectedCategory = '';
    public $searchTerm = '';
    public $sortBy = 'value';
    public $sortDirection = 'desc';
    public $viewMode = 'all';

    protected $queryString = ['selectedCategory', 'searchTerm', 'sortBy', 'sortDirection', 'viewMode'];

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function getValuationSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')->where('branch_id', $branchId)->get();

        $totalAvailableValue = $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost);
        $totalReservedValue = $stocks->sum(fn($s) => $s->quantity_reserved * $s->average_cost);
        $totalDamagedValue = $stocks->sum(fn($s) => $s->quantity_damaged * $s->average_cost);

        return [
            'total_value' => $totalAvailableValue + $totalReservedValue + $totalDamagedValue,
            'available_value' => $totalAvailableValue,
            'reserved_value' => $totalReservedValue,
            'damaged_value' => $totalDamagedValue,
            'total_items' => $stocks->count(),
        ];
    }

    public function getCategoryValuation()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->get();

        $categoryValues = $stocks->groupBy('item.category')->map(function ($items) {
            return $items->sum(fn($s) => ($s->quantity_available + $s->quantity_reserved) * $s->average_cost);
        });

        return [
            'labels' => $categoryValues->keys()->map(fn($cat) => str_replace('_', ' ', ucfirst($cat)))->toArray(),
            'series' => $categoryValues->values()->toArray(),
        ];
    }

    public function getTopValueItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->get()
            ->map(function ($stock) {
                $stock->total_value = ($stock->quantity_available + $stock->quantity_reserved) * $stock->average_cost;
                return $stock;
            })
            ->sortByDesc('total_value')
            ->take(10);
    }

    public function getLowStockItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->where('quantity_available', '<', 100)
            ->get()
            ->map(function ($stock) {
                $stock->total_value = ($stock->quantity_available + $stock->quantity_reserved) * $stock->average_cost;
                $stock->available_value = $stock->quantity_available * $stock->average_cost;
                return $stock;
            })
            ->sortBy([[$this->sortBy, $this->sortDirection === 'desc' ? SORT_DESC : SORT_ASC]])
            ->values();
    }

    public function getCategoryGroupedData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->get()
            ->map(function ($stock) {
                $stock->total_value = ($stock->quantity_available + $stock->quantity_reserved) * $stock->average_cost;
                $stock->available_value = $stock->quantity_available * $stock->average_cost;
                return $stock;
            });

        return $stocks->groupBy('item.category')->map(function ($items) {
            return [
                'category' => $items->first()->item->category,
                'items' => $items,
                'total_value' => $items->sum('total_value'),
                'item_count' => $items->count(),
                'total_qty' => $items->sum(fn($s) => $s->quantity_available + $s->quantity_reserved),
            ];
        })->sortByDesc('total_value')->values();
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->get()
            ->map(function ($stock) {
                $stock->total_value = ($stock->quantity_available + $stock->quantity_reserved) * $stock->average_cost;
                $stock->available_value = $stock->quantity_available * $stock->average_cost;
                return $stock;
            })
            ->sortBy([[$this->sortBy, $this->sortDirection === 'desc' ? SORT_DESC : SORT_ASC]])
            ->values();

        $paginatedStocks = new \Illuminate\Pagination\LengthAwarePaginator(
            $stocks->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 15),
            $stocks->count(),
            15,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $categories = ['raw_material', 'packaging', 'consumable', 'equipment'];

        // Get data based on view mode
        $viewData = match($this->viewMode) {
            'top' => $this->getTopValueItems(),
            'category' => $this->getCategoryGroupedData(),
            'low' => $this->getLowStockItems(),
            default => $paginatedStocks->items(),
        };

        return view('livewire.branch-dashboard.analytics.stock-valuation', [
            'stocks' => $paginatedStocks,
            'topItems' => $this->getTopValueItems(),
            'lowStockItems' => $this->getLowStockItems(),
            'categoryData' => $this->getCategoryGroupedData(),
            'categories' => $categories,
            'summary' => $this->getValuationSummary(),
            'categoryValuation' => $this->getCategoryValuation(),
        ]);
    }
}
