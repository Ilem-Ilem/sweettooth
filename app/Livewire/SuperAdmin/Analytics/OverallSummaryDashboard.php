<?php

namespace App\Livewire\SuperAdmin\Analytics;

use App\Models\Branch;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Purchase;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class OverallSummaryDashboard extends Component
{
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?string $selectedBranch = null;

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getOverallSummary()
    {
        $stocksQuery = Stock::query();
        $purchasesQuery = Purchase::query();
        $movementsQuery = StockMovement::query();
        $requestsQuery = ItemRequest::query();

        // Apply branch filter if selected
        if ($this->selectedBranch) {
            $stocksQuery->where('branch_id', $this->selectedBranch);
            $purchasesQuery->where('branch_id', $this->selectedBranch);
            $movementsQuery->whereHas('stock', fn($q) => $q->where('branch_id', $this->selectedBranch));
            $requestsQuery->where('branch_id', $this->selectedBranch);
        }

        $stocks = $stocksQuery->get();
        $totalValue = $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost);

        $purchases = $purchasesQuery
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $movements = $movementsQuery
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $requests = $requestsQuery
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
        $query = Stock::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $distribution = $query
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
        $query = StockMovement::with(['stock.item', 'mover']);

        if ($this->selectedBranch) {
            $query->whereHas('stock', fn($q) => $q->where('branch_id', $this->selectedBranch));
        }

        return $query
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->latest('movement_date')
            ->limit(10)
            ->get();
    }

    public function getTopAlerts()
    {
        $query = Stock::with('item');

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $stocks = $query->get();

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
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.analytics.overall-summary-dashboard', [
            'summary' => $this->getOverallSummary(),
            'healthOverview' => $this->getStockHealthOverview(),
            'recentActivity' => $this->getRecentActivity(),
            'alerts' => $this->getTopAlerts(),
            'branches' => $branches,
        ]);
    }
}
