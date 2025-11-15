<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Purchase;
use App\Models\ItemRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Url};
use Carbon\Carbon;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class OverallSummaryDashboard extends Component
{
    use Interactions;

    public $dateFrom;
    public $dateTo;
    public $departmentFilter = null;
    public $categoryFilter = null;
    public $autoRefresh = false;

    #[Url(keep:true)]
    public $b_id;

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    // Previous period metrics for comparison
    public $previousStockValue = 0;
    public $stockValueChange = 0;
    public $stockValueChangePercentage = 0;

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->dispatch('refresh-analytics');
    }

    public function updatedDateTo()
    {
        $this->dispatch('refresh-analytics');
    }

    public function refresh()
    {
        $this->dispatch('refresh-analytics');
        $this->toast()->success('Analytics refreshed successfully!')->send();
    }

    public function getOverallSummary()
    {
        $branchId = $this->b_id;
        $dateFrom = Carbon::parse($this->dateFrom);
        $dateTo = Carbon::parse($this->dateTo);

        $stocks = Stock::where('branch_id', $branchId)->with('item')->get();
        $totalValue = $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost);

        // Calculate previous period for comparison
        $periodDays = $dateTo->diffInDays($dateFrom);
        $previousDateFrom = $dateFrom->copy()->subDays($periodDays);
        $previousDateTo = $dateFrom->copy()->subDay();

        // Approximate previous stock value
        $previousMovements = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$previousDateFrom, $previousDateTo])
            ->get();

        $valueDifference = $previousMovements->where('type', 'in')->sum('quantity') -
                          abs($previousMovements->where('type', 'out')->sum('quantity'));

        $this->previousStockValue = max(0, $totalValue - ($valueDifference * 10)); // Simplified estimation
        $this->stockValueChange = $totalValue - $this->previousStockValue;
        $this->stockValueChangePercentage = $this->previousStockValue > 0
            ? round(($this->stockValueChange / $this->previousStockValue) * 100, 2)
            : 0;

        $purchases = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $movements = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_stock_value' => $totalValue,
            'total_items' => $stocks->count(),
            'low_stock_items' => $stocks->filter(fn($s) => $s->isBelowReorderLevel())->count(),
            'critical_items' => $stocks->where('health_status', 'critical')->count(),
            'expired_items' => $stocks->filter(fn($s) => $s->expiry_date && $s->expiry_date->isPast())->count(),
            'total_purchases' => $purchases->count(),
            'total_purchase_value' => $purchases->sum('total_cost'),
            'total_movements' => $movements->count(),
            'stock_in' => $movements->where('type', 'in')->sum('quantity'),
            'stock_out' => abs($movements->where('type', 'out')->sum('quantity')),
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'completed_requests' => $requests->whereIn('status', ['approved', 'dispatched', 'completed'])->count(),
            'previous_stock_value' => $this->previousStockValue,
            'stock_value_change' => $this->stockValueChange,
            'stock_value_change_percentage' => $this->stockValueChangePercentage,
        ];
    }

    public function getStockHealthOverview()
    {
        $branchId = $this->b_id ?? request()->get('b_id');

        $distribution = Stock::where('branch_id', $branchId)
            ->selectRaw('health_status, COUNT(*) as count')
            ->groupBy('health_status')
            ->get();

        return [
            'labels' => $distribution->pluck('health_status')->map(fn($s) => ucfirst($s))->toArray(),
            'series' => $distribution->pluck('count')->toArray(),
        ];
    }

    public function getRecentActivity()
    {
        $branchId = $this->b_id ?? request()->get('b_id');


        return StockMovement::with(['stock.item', 'mover'])
            ->whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->latest('movement_date')
            ->limit(10)
            ->get();
    }

    public function getTopAlerts()
    {
        $branchId = $this->b_id ?? request()->get('b_id');

        $stocks = Stock::with('item')->where('branch_id', $branchId)->get();

        $alerts = [];

        foreach ($stocks as $stock) {
            if (!$stock->item) continue;

            if ($stock->expiry_date && $stock->expiry_date->isPast()) {
                $alerts[] = [
                    'priority' => 1,
                    'type' => 'expired',
                    'icon' => '⏰',
                    'message' => "{$stock->item->name} has expired",
                    'item' => $stock->item->name,
                    'item_id' => $stock->item->id,
                    'action' => 'Remove',
                ];
            } elseif ($stock->health_status === 'critical') {
                $alerts[] = [
                    'priority' => 1,
                    'type' => 'critical',
                    'icon' => '🔴',
                    'message' => "{$stock->item->name} in critical condition",
                    'item' => $stock->item->name,
                    'item_id' => $stock->item->id,
                    'action' => 'View Item',
                ];
            } elseif ($stock->isBelowReorderLevel()) {
                $alerts[] = [
                    'priority' => 2,
                    'type' => 'warning',
                    'icon' => '⚠️',
                    'message' => "{$stock->item->name} below reorder level",
                    'item' => $stock->item->name,
                    'item_id' => $stock->item->id,
                    'action' => 'Restock',
                ];
            } elseif ($stock->expiry_date && $stock->expiry_date->isFuture() && $stock->expiry_date->diffInDays(now()) <= 7) {
                $alerts[] = [
                    'priority' => 2,
                    'type' => 'expiring',
                    'icon' => '🟠',
                    'message' => "{$stock->item->name} expiring in {$stock->expiry_date->diffInDays(now())} days",
                    'item' => $stock->item->name,
                    'item_id' => $stock->item->id,
                    'action' => 'Use Soon',
                ];
            }
        }

        return collect($alerts)->sortBy('priority')->take(15);
    }

    public function getInsights()
    {
        $branchId = $this->b_id ?? request()->get('b_id');

        $summary = $this->getOverallSummary();
        $insights = [];

        // Insight 1: Stock value change
        if ($summary['stock_value_change_percentage'] != 0) {
            $direction = $summary['stock_value_change_percentage'] > 0 ? 'increased' : 'decreased';
            $insights[] = [
                'type' => $summary['stock_value_change_percentage'] > 0 ? 'positive' : 'negative',
                'icon' => $summary['stock_value_change_percentage'] > 0 ? '📈' : '📉',
                'message' => "Inventory value {$direction} by " . abs($summary['stock_value_change_percentage']) . "% since last period.",
            ];
        }

        // Insight 2: Top depleting items
        $topDepletingItems = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->where('type', 'out')
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->select('stock_id', DB::raw('SUM(ABS(quantity)) as total_out'))
            ->groupBy('stock_id')
            ->orderByDesc('total_out')
            ->limit(3)
            ->with('stock.item')
            ->get();

        if ($topDepletingItems->isNotEmpty()) {
            $itemNames = $topDepletingItems->pluck('stock.item.name')->filter()->take(3)->implode(', ');
            if ($itemNames) {
                $insights[] = [
                    'type' => 'info',
                    'icon' => '📦',
                    'message' => "Top 3 items with highest usage: {$itemNames}",
                ];
            }
        }

        // Insight 3: Purchase activity
        if ($summary['total_purchases'] == 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'message' => "No purchases recorded in the selected period.",
            ];
        }

        // Insight 4: Low stock alert
        if ($summary['low_stock_items'] > 0) {
            $insights[] = [
                'type' => 'critical',
                'icon' => '🔴',
                'message' => "{$summary['low_stock_items']} item(s) below reorder level - restocking recommended.",
            ];
        }

        // Insight 5: Expired items
        if ($summary['expired_items'] > 0) {
            $insights[] = [
                'type' => 'critical',
                'icon' => '⏰',
                'message' => "{$summary['expired_items']} item(s) have expired and should be removed.",
            ];
        }

        // Insight 6: Average daily consumption
        $days = max(1, Carbon::parse($this->dateTo)->diffInDays(Carbon::parse($this->dateFrom)));
        $avgDailyConsumption = round($summary['stock_out'] / $days, 2);
        if ($avgDailyConsumption > 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => '📊',
                'message' => "Average daily consumption: {$avgDailyConsumption} units.",
            ];
        }

        return collect($insights);
    }

    public function getStockHealthTable()
    {
       $branchId = $this->b_id ?? request()->get('b_id');


        return Stock::where('branch_id', $branchId)
            ->with('item')
            ->get()
            ->map(function ($stock) {
                $item = $stock->item;
                if (!$item) return null;

                $reorderLevel = $item->reorder_level ?? 100;
                $healthPercentage = $reorderLevel > 0
                    ? min(100, round(($stock->quantity_available / $reorderLevel) * 100, 0))
                    : 100;

                $status = 'good';
                $statusIcon = '🟢';
                $statusColor = 'green';

                if ($healthPercentage < 30 || $stock->health_status === 'critical') {
                    $status = 'critical';
                    $statusIcon = '🔴';
                    $statusColor = 'red';
                } elseif ($healthPercentage < 60) {
                    $status = 'low';
                    $statusIcon = '🟡';
                    $statusColor = 'yellow';
                } elseif ($healthPercentage < 80) {
                    $status = 'moderate';
                    $statusIcon = '🟠';
                    $statusColor = 'orange';
                }

                $lastMovement = StockMovement::where('stock_id', $stock->id)
                    ->latest('movement_date')
                    ->first();

                return [
                    'id' => $stock->id,
                    'item_name' => $item->name,
                    'stock_level' => $stock->quantity_available,
                    'reorder_level' => $reorderLevel,
                    'status' => $status,
                    'status_icon' => $statusIcon,
                    'status_color' => $statusColor,
                    'health_percentage' => $healthPercentage,
                    'last_movement' => $lastMovement ? $lastMovement->movement_date->format('d M Y') : 'N/A',
                    'uom' => $item->uom ?? 'units',
                ];
            })
            ->filter()
            ->sortBy('health_percentage')
            ->take(15)
            ->values();
    }

    public function getDepartmentBreakdown()
    {
        $branchId = $this->b_id ?? request()->get('b_id');


        return Stock::where('branch_id', $branchId)
            ->with('item')
            ->get()
            ->groupBy(function ($stock) {
                // Category is a string field on the Item model, not a relationship
                return $stock->item->category ?? 'Uncategorized';
            })
            ->map(function ($stocks, $category) {
                $stockValue = $stocks->sum(function ($stock) {
                    return ($stock->quantity_available ?? 0) * ($stock->average_cost ?? 0);
                });

                $stockIds = $stocks->pluck('id');
                $movements = StockMovement::whereIn('stock_id', $stockIds)
                    ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
                    ->get();

                $stockIn = $movements->where('type', 'in')->sum('quantity');
                $stockOut = abs($movements->where('type', 'out')->sum('quantity'));

                $lowItems = $stocks->filter(fn($s) => $s->isBelowReorderLevel())->count();

                $requests = ItemRequest::whereHas('requestDetails', function ($query) use ($stocks) {
                    $query->whereIn('item_id', $stocks->pluck('item_id'));
                })->whereBetween('request_date', [$this->dateFrom, $this->dateTo])->count();

                return [
                    'category' => ucfirst($category),
                    'stock_value' => $stockValue,
                    'stock_in' => $stockIn,
                    'stock_out' => $stockOut,
                    'low_items' => $lowItems,
                    'requests' => $requests,
                    'item_count' => $stocks->count(),
                ];
            })
            ->sortByDesc('stock_value')
            ->take(10)
            ->values();
    }

    public function getPerformanceMetrics()
    {
       $branchId = $this->b_id ?? request()->get('b_id');

        $summary = $this->getOverallSummary();
        $days = max(1, Carbon::parse($this->dateTo)->diffInDays(Carbon::parse($this->dateFrom)));

        // Fastest moving item
        $fastestMoving = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->where('type', 'out')
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->select('stock_id', DB::raw('SUM(ABS(quantity)) as total'))
            ->groupBy('stock_id')
            ->orderByDesc('total')
            ->with('stock.item')
            ->first();

        // Slowest moving item
        $slowestMoving = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->where('type', 'out')
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->select('stock_id', DB::raw('SUM(ABS(quantity)) as total'))
            ->groupBy('stock_id')
            ->orderBy('total')
            ->with('stock.item')
            ->first();

        // Most requested item
        $mostRequested = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->with('requestDetails.item')
            ->get()
            ->flatMap(fn($request) => $request->requestDetails)
            ->groupBy('item_id')
            ->map(function ($details) {
                return [
                    'item' => $details->first()->item,
                    'total_requested' => $details->sum('quantity_requested'),
                ];
            })
            ->sortByDesc('total_requested')
            ->first();

        return [
            'average_turnover_rate' => round($summary['stock_out'] / $days, 2),
            'fastest_moving' => $fastestMoving ? [
                'name' => $fastestMoving->stock->item->name ?? 'N/A',
                'quantity' => abs($fastestMoving->total),
            ] : null,
            'slowest_moving' => $slowestMoving ? [
                'name' => $slowestMoving->stock->item->name ?? 'N/A',
                'quantity' => abs($slowestMoving->total),
            ] : null,
            'most_requested' => $mostRequested ? [
                'name' => $mostRequested['item']->name ?? 'N/A',
                'quantity' => $mostRequested['total_requested'],
            ] : null,
            'expired_vs_active_percentage' => $summary['total_items'] > 0
                ? round(($summary['expired_items'] / $summary['total_items']) * 100, 2)
                : 0,
        ];
    }

    public function exportPDF()
    {
        $this->toast()->info('PDF export feature coming soon')->send();
    }

    public function exportCSV()
    {
        $this->toast()->info('CSV export feature coming soon')->send();
    }

    public function render()
    {
        return view('livewire.branch-dashboard.analytics.overall-summary-dashboard', [
            'summary' => $this->getOverallSummary(),
            'healthOverview' => $this->getStockHealthOverview(),
            'recentActivity' => $this->getRecentActivity(),
            'alerts' => $this->getTopAlerts(),
            'insights' => $this->getInsights(),
            'stockHealthTable' => $this->getStockHealthTable(),
            'departmentBreakdown' => $this->getDepartmentBreakdown(),
            'performanceMetrics' => $this->getPerformanceMetrics(),
        ]);
    }
}
