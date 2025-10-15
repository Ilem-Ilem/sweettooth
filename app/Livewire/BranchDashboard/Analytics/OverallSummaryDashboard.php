<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Purchase;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class OverallSummaryDashboard extends Component
{
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getOverallSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->get();
        $totalValue = $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost);

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
            'low_stock_items' => $stocks->filter(fn($s) => $s->quantity_available < ($s->item->reorder_level ?? 0))->count(),
            'critical_items' => $stocks->where('health_status', 'critical')->count() + $stocks->where('health_status', 'expired')->count(),
            'total_purchases' => $purchases->count(),
            'total_purchase_value' => $purchases->sum('total_cost'),
            'total_movements' => $movements->count(),
            'stock_in' => $movements->where('type', 'in')->sum('quantity'),
            'stock_out' => $movements->where('type', 'out')->sum('quantity'),
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'completed_requests' => $requests->where('status', 'completed')->count(),
        ];
    }

    public function getStockHealthOverview()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

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
        $branchId = Auth::guard('employees')->user()->branch_id;

        return StockMovement::with(['stock.item', 'mover'])
            ->whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->latest('movement_date')
            ->limit(10)
            ->get();
    }

    public function getTopAlerts()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::with('item')->where('branch_id', $branchId)->get();

        $alerts = [];

        foreach ($stocks as $stock) {
            if ($stock->health_status === 'expired') {
                $alerts[] = [
                    'type' => 'critical',
                    'message' => "{$stock->item->name} has expired",
                    'item' => $stock->item->name,
                ];
            } elseif ($stock->health_status === 'critical') {
                $alerts[] = [
                    'type' => 'critical',
                    'message' => "{$stock->item->name} is in critical condition",
                    'item' => $stock->item->name,
                ];
            } elseif ($stock->quantity_available < ($stock->item->reorder_level ?? 0)) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "{$stock->item->name} is below reorder level",
                    'item' => $stock->item->name,
                ];
            }
        }

        return collect($alerts)->take(10);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.analytics.overall-summary-dashboard', [
            'summary' => $this->getOverallSummary(),
            'healthOverview' => $this->getStockHealthOverview(),
            'recentActivity' => $this->getRecentActivity(),
            'alerts' => $this->getTopAlerts(),
        ]);
    }
}
