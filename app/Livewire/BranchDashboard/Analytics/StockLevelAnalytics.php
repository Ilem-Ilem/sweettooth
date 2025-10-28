<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public $itemSearch = '';
    public $healthFilter = '';
    public $trendData;
    public $healthDistribution;
    public $turnoverAnalysis;

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'selectedCategory',
        'healthFilter',
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
        $this->updateChartData();
    }

    public function updatedHealthFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedItem()
    {
        $this->resetPage();
        $this->updateChartData();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
        $this->updateChartData();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
        $this->updateChartData();
    }

    public function updateChartData()
    {
        $this->trendData = $this->getStockLevelTrendData();
        $this->healthDistribution = $this->getHealthStatusDistribution();
        $this->turnoverAnalysis = $this->getTurnoverAnalysis();

        $this->dispatch('chartsUpdated', [
            'trendData' => $this->trendData,
            'healthDistribution' => $this->healthDistribution,
            'turnoverAnalysis' => $this->turnoverAnalysis
        ]);
    }

    public function getAvailableItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
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

    public function getStockLevelTrendData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        // Get stock movements over time to track stock level changes
        $movements = StockMovement::with('stock.item')
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(movement_date) as date, stock_id, quantity_after')
            ->orderBy('date')
            ->orderBy('movement_date')
            ->get();

        if ($this->selectedItem) {
            // Single item trend
            $dates = [];
            $levels = [];

            foreach ($movements->groupBy('date') as $date => $dayMovements) {
                $dates[] = \Carbon\Carbon::parse($date)->format('M d');
                $levels[] = $dayMovements->last()->quantity_after ?? 0;
            }

            return [
                'categories' => $dates,
                'series' => [
                    ['name' => 'Stock Level', 'data' => $levels],
                ],
            ];
        } else {
            // Overall stock trend (total available)
            $dailyLevels = Stock::where('branch_id', $branchId)
                ->selectRaw('DATE(updated_at) as date, SUM(quantity_available) as total_available')
                ->whereBetween('updated_at', [$this->dateFrom, $this->dateTo])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $dates = [];
            $levels = [];

            foreach ($dailyLevels as $day) {
                $dates[] = \Carbon\Carbon::parse($day->date)->format('M d');
                $levels[] = $day->total_available;
            }

            return [
                'categories' => $dates,
                'series' => [
                    ['name' => 'Total Stock', 'data' => $levels],
                ],
            ];
        }
    }

    public function getHealthStatusDistribution()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $distribution = Stock::where('branch_id', $branchId)
            ->when($this->selectedItem, function ($query) {
                $query->where('id', $this->selectedItem);
            })
            ->selectRaw('health_status, COUNT(*) as count')
            ->groupBy('health_status')
            ->get();

        return [
            'labels' => $distribution->pluck('health_status')->map(fn($type) => ucfirst($type))->toArray(),
            'series' => $distribution->pluck('count')->toArray(),
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

    public function getTurnoverAnalysis()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        // Get items with most stock movement (turnover)
        $items = StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('stock_id, COUNT(*) as turnover_count, SUM(ABS(quantity)) as total_moved')
            ->groupBy('stock_id')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get();

        return [
            'labels' => $items->map(fn($item) => $item->stock->item->name ?? 'Unknown')->toArray(),
            'series' => [
                [
                    'name' => 'Total Quantity Moved',
                    'data' => $items->pluck('total_moved')->map(fn($val) => abs((float)$val))->toArray(),
                ],
            ],
        ];
    }

    public function getTopLowStockItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->whereRaw('quantity_available < COALESCE((SELECT reorder_level FROM items WHERE items.id = stocks.item_id), 0)')
            ->orderBy('quantity_available', 'asc')
            ->limit(10)
            ->get();
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
            ->paginate(15);

        $categories = ['raw_material', 'packaging', 'consumable', 'equipment'];
        $healthStatuses = ['good', 'warning', 'critical', 'expired'];

        // Store chart data in public properties for JavaScript access
        $this->trendData = $this->getStockLevelTrendData();
        $this->healthDistribution = $this->getHealthStatusDistribution();
        $this->turnoverAnalysis = $this->getTurnoverAnalysis();

        return view('livewire.branch-dashboard.analytics.stock-level-analytics', [
            'stocks' => $stocks,
            'categories' => $categories,
            'healthStatuses' => $healthStatuses,
            'availableItems' => $this->getAvailableItems(),
            'summary' => $this->getStockSummary(),
            'trendData' => $this->trendData,
            'healthDistribution' => $this->healthDistribution,
            'categoryDistribution' => $this->getCategoryDistribution(),
            'turnoverAnalysis' => $this->turnoverAnalysis,
            'lowStockItems' => $this->getTopLowStockItems(),
        ]);
    }
}
