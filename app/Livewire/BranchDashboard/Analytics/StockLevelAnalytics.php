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
use Carbon\Carbon;

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
    }

    public function updatedHealthFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedItem()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
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

    /**
     * Get stock summary with extended metrics
     */
    public function getStockSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->with('item')->get();

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
            'good_health_items' => $stocks->where('health_status', 'good')->count(),
            'warning_items' => $stocks->where('health_status', 'warning')->count(),
            'out_of_stock' => $stocks->where('quantity_available', '<=', 0)->count(),
        ];
    }

    /**
     * Get health status breakdown with detailed stats
     */
    public function getHealthStatusBreakdown()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::where('branch_id', $branchId)
            ->selectRaw('health_status, COUNT(*) as count, SUM(quantity_available) as total_qty')
            ->groupBy('health_status')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->health_status,
                    'count' => $item->count,
                    'total_qty' => $item->total_qty,
                ];
            });
    }

    /**
     * Get category distribution with metrics
     */
    public function getCategoryBreakdown()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->get()
            ->groupBy('item.category')
            ->map(function ($group, $category) {
                return [
                    'category' => $category,
                    'count' => $group->count(),
                    'total_available' => $group->sum('quantity_available'),
                    'total_value' => $group->sum(function ($stock) {
                        return $stock->quantity_available * ($stock->item->price ?? 0);
                    }),
                    'low_stock' => $group->filter(function ($stock) {
                        return $stock->quantity_available < ($stock->item->reorder_level ?? 0);
                    })->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    /**
     * Get turnover rate analysis
     */
    public function getTurnoverAnalysis()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_moved')
            ->groupBy('stock_id')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $stock = Stock::with('item')->find($item->stock_id);
                $avgStock = $stock->quantity_available ?? 1;
                $turnoverRate = $avgStock > 0 ? ($item->total_moved / $avgStock) : 0;

                return [
                    'item_name' => $stock->item->name ?? 'Unknown',
                    'sku' => $stock->item->sku ?? 'N/A',
                    'movement_count' => $item->movement_count,
                    'total_moved' => $item->total_moved,
                    'current_stock' => $avgStock,
                    'turnover_rate' => $turnoverRate,
                    'velocity' => $turnoverRate > 2 ? 'High' : ($turnoverRate > 0.5 ? 'Moderate' : 'Low'),
                ];
            });
    }

    /**
     * Get low stock items needing reorder
     */
    public function getReorderRecommendations()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->whereRaw('quantity_available < COALESCE((SELECT reorder_level FROM items WHERE items.id = stocks.item_id), 0)')
            ->orderBy('quantity_available', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($stock) {
                $reorderLevel = $stock->item->reorder_level ?? 0;
                $suggestedQty = max($reorderLevel * 2 - $stock->quantity_available, $reorderLevel);

                return [
                    'item' => $stock->item,
                    'current_qty' => $stock->quantity_available,
                    'reorder_level' => $reorderLevel,
                    'suggested_qty' => $suggestedQty,
                    'urgency' => $stock->quantity_available <= 0 ? 'Critical' :
                                ($stock->quantity_available <= $reorderLevel * 0.5 ? 'High' : 'Medium'),
                ];
            });
    }

    /**
     * Get stock level trend data (daily summary)
     */
    public function getStockLevelTrend()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $movements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('DATE(movement_date) as date')
            ->selectRaw('SUM(CASE WHEN type IN ("in", "return") THEN quantity ELSE 0 END) as stock_in')
            ->selectRaw('ABS(SUM(CASE WHEN type IN ("out", "transfer", "damaged") THEN quantity ELSE 0 END)) as stock_out')
            ->selectRaw('COUNT(*) as movements')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get()
            ->reverse();

        return $movements->map(function ($day) {
            $netChange = $day->stock_in - $day->stock_out;
            return [
                'date' => Carbon::parse($day->date)->format('M d'),
                'stock_in' => $day->stock_in,
                'stock_out' => $day->stock_out,
                'net_change' => $netChange,
                'movements' => $day->movements,
            ];
        });
    }

    /**
     * Get comparison with previous period
     */
    public function getPeriodComparison()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();
        $daysDiff = $dateFrom->diffInDays($dateTo);

        // Current period
        $currentMovements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('SUM(ABS(quantity)) as total_moved')
            ->selectRaw('COUNT(*) as movement_count')
            ->first();

        // Previous period
        $previousMovements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [
                $dateFrom->copy()->subDays($daysDiff)->startOfDay(),
                $dateFrom->copy()->subDay()->endOfDay()
            ])
            ->selectRaw('SUM(ABS(quantity)) as total_moved')
            ->selectRaw('COUNT(*) as movement_count')
            ->first();

        $currentQty = $currentMovements->total_moved ?? 0;
        $previousQty = $previousMovements->total_moved ?? 0;
        $currentCount = $currentMovements->movement_count ?? 0;
        $previousCount = $previousMovements->movement_count ?? 0;

        return [
            'current_qty' => $currentQty,
            'previous_qty' => $previousQty,
            'qty_change' => $currentQty - $previousQty,
            'qty_change_percent' => $previousQty > 0 ? (($currentQty - $previousQty) / $previousQty) * 100 : 0,
            'current_movements' => $currentCount,
            'previous_movements' => $previousCount,
            'movement_change' => $currentCount - $previousCount,
            'movement_change_percent' => $previousCount > 0 ? (($currentCount - $previousCount) / $previousCount) * 100 : 0,
        ];
    }

    /**
     * Generate smart insights
     */
    public function getSmartInsights()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;
        $summary = $this->getStockSummary();
        $insights = [];

        // Stock level insight
        if ($summary['low_stock_count'] > 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'message' => "You have {$summary['low_stock_count']} items below reorder level that need attention."
            ];
        }

        // Critical items insight
        if ($summary['critical_items'] > 0) {
            $insights[] = [
                'type' => 'critical',
                'icon' => '🔴',
                'message' => "{$summary['critical_items']} items are in critical condition. Immediate action required."
            ];
        }

        // Positive insight
        if ($summary['good_health_items'] > ($summary['total_items'] * 0.7)) {
            $percentage = round(($summary['good_health_items'] / $summary['total_items']) * 100);
            $insights[] = [
                'type' => 'success',
                'icon' => '✅',
                'message' => "{$percentage}% of your stock is in good health. Great inventory management!"
            ];
        }

        // Out of stock insight
        if ($summary['out_of_stock'] > 0) {
            $insights[] = [
                'type' => 'critical',
                'icon' => '❌',
                'message' => "{$summary['out_of_stock']} items are completely out of stock."
            ];
        }

        return $insights;
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

        return view('livewire.branch-dashboard.analytics.stock-level-analytics', [
            'stocks' => $stocks,
            'categories' => $categories,
            'healthStatuses' => $healthStatuses,
            'availableItems' => $this->getAvailableItems(),
            'summary' => $this->getStockSummary(),
            'healthBreakdown' => $this->getHealthStatusBreakdown(),
            'categoryBreakdown' => $this->getCategoryBreakdown(),
            'turnoverAnalysis' => $this->getTurnoverAnalysis(),
            'reorderRecommendations' => $this->getReorderRecommendations(),
            'stockLevelTrend' => $this->getStockLevelTrend(),
            'periodComparison' => $this->getPeriodComparison(),
            'smartInsights' => $this->getSmartInsights(),
        ]);
    }
}
