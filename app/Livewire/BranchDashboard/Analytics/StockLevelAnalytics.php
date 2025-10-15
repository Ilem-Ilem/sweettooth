<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class StockLevelAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $selectedCategory = '';
    public $selectedItem = null;
    public $searchTerm = '';
    public $healthFilter = '';

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'selectedCategory',
        'healthFilter',
        'searchTerm'
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

    public function updatedHealthFilter()
    {
        $this->resetPage();
    }

    public function selectItem($itemId)
    {
        $this->selectedItem = Stock::with(['item'])
            ->where('branch_id', Auth::guard('employees')->user()->branch_id)
            ->where('item_id', $itemId)
            ->first();
    }

    public function clearItemSelection()
    {
        $this->selectedItem = null;
    }

    public function getStockLevelChartData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->selectedCategory);
                });
            })
            ->get();

        $categories = [];
        $available = [];
        $reserved = [];
        $damaged = [];
        $reorderLevels = [];

        foreach ($stocks as $stock) {
            $categories[] = $stock->item->name;
            $available[] = (float) $stock->quantity_available;
            $reserved[] = (float) $stock->quantity_reserved;
            $damaged[] = (float) $stock->quantity_damaged;
            $reorderLevels[] = (float) $stock->item->reorder_level ?? 0;
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Available',
                    'data' => $available,
                ],
                [
                    'name' => 'Reserved',
                    'data' => $reserved,
                ],
                [
                    'name' => 'Damaged',
                    'data' => $damaged,
                ],
                [
                    'name' => 'Reorder Level',
                    'data' => $reorderLevels,
                    'type' => 'line',
                ],
            ],
        ];
    }

    public function getHealthStatusDistribution()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $distribution = Stock::where('branch_id', $branchId)
            ->selectRaw('health_status, COUNT(*) as count')
            ->groupBy('health_status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'good' => '#10B981',
            'warning' => '#F59E0B',
            'critical' => '#EF4444',
            'expired' => '#6B7280',
        ];

        foreach ($distribution as $item) {
            $labels[] = ucfirst($item->health_status);
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'series' => $data,
            'colors' => array_values($colors),
        ];
    }

    public function getCategoryDistribution()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $distribution = Stock::with('item')
            ->where('branch_id', $branchId)
            ->get()
            ->groupBy('item.category')
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'labels' => $distribution->keys()->map(fn($cat) => str_replace('_', ' ', ucfirst($cat)))->toArray(),
            'series' => $distribution->values()->toArray(),
        ];
    }

    public function getStockSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->get();

        return [
            'total_items' => $stocks->count(),
            'total_available' => $stocks->sum('quantity_available'),
            'total_reserved' => $stocks->sum('quantity_reserved'),
            'total_damaged' => $stocks->sum('quantity_damaged'),
            'low_stock_count' => $stocks->filter(function ($stock) {
                return $stock->quantity_available < ($stock->item->reorder_level ?? 0);
            })->count(),
            'critical_items' => $stocks->where('health_status', 'critical')->count(),
            'expired_items' => $stocks->where('health_status', 'expired')->count(),
        ];
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with(['item'])
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
            ->when($this->healthFilter, function ($query) {
                $query->where('health_status', $this->healthFilter);
            })
            ->latest()
            ->paginate(10);

        $categories = ['raw_material', 'packaging', 'consumable', 'equipment'];
        $healthStatuses = ['good', 'warning', 'critical', 'expired'];

        return view('livewire.branch-dashboard.analytics.stock-level-analytics', [
            'stocks' => $stocks,
            'categories' => $categories,
            'healthStatuses' => $healthStatuses,
            'summary' => $this->getStockSummary(),
            'stockLevelChart' => $this->getStockLevelChartData(),
            'healthDistribution' => $this->getHealthStatusDistribution(),
            'categoryDistribution' => $this->getCategoryDistribution(),
        ]);
    }
}
