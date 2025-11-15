<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Stock;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Url};

#[Layout('components.layouts.app.branch-dashboard')]
class AlertsDashboard extends Component
{
    public $alertType = '';

    #[Url(keep:true)]
    public $b_id;

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    public function getAllAlerts()
    {
        $branchId = $this->b_id ?? request()->get('b_id');
        $stocks = Stock::with('item')->where('branch_id', $branchId)->get();
        $alerts = collect();

        // Critical and Expired Items
        foreach ($stocks as $stock) {
            if ($stock->health_status === 'expired') {
                $alerts->push([
                    'type' => 'critical',
                    'category' => 'expired',
                    'priority' => 1,
                    'message' => "{$stock->item->name} has expired",
                    'item' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'date' => $stock->expiry_date,
                    'action' => 'Remove from inventory immediately',
                ]);
            } elseif ($stock->health_status === 'critical') {
                $alerts->push([
                    'type' => 'critical',
                    'category' => 'health',
                    'priority' => 2,
                    'message' => "{$stock->item->name} is in critical condition",
                    'item' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'action' => 'Inspect and take appropriate action',
                ]);
            }
        }

        // Low Stock Alerts
        foreach ($stocks as $stock) {
            if ($stock->quantity_available < ($stock->item->reorder_level ?? 0) && $stock->quantity_available > 0) {
                $alerts->push([
                    'type' => 'warning',
                    'category' => 'low_stock',
                    'priority' => 3,
                    'message' => "{$stock->item->name} is below reorder level",
                    'item' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'current' => $stock->quantity_available,
                    'reorder_level' => $stock->item->reorder_level,
                    'action' => 'Reorder stock',
                ]);
            } elseif ($stock->quantity_available <= 0) {
                $alerts->push([
                    'type' => 'critical',
                    'category' => 'out_of_stock',
                    'priority' => 2,
                    'message' => "{$stock->item->name} is out of stock",
                    'item' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'action' => 'Urgent reorder required',
                ]);
            }
        }

        // Expiring Soon (within 30 days)
        foreach ($stocks as $stock) {
            if ($stock->expiry_date && \Carbon\Carbon::parse($stock->expiry_date)->lte(now()->addDays(30)) && \Carbon\Carbon::parse($stock->expiry_date)->gt(now())) {
                $alerts->push([
                    'type' => 'warning',
                    'category' => 'expiring_soon',
                    'priority' => 3,
                    'message' => "{$stock->item->name} will expire soon",
                    'item' => $stock->item->name,
                    'sku' => $stock->item->sku,
                    'date' => $stock->expiry_date,
                    'days_left' => \Carbon\Carbon::parse($stock->expiry_date)->diffInDays(now()),
                    'action' => 'Use or dispose before expiry',
                ]);
            }
        }

        // High Damaged Stock
        foreach ($stocks as $stock) {
            if ($stock->quantity_damaged > 0) {
                $percentage = ($stock->quantity_damaged / ($stock->quantity_available + $stock->quantity_damaged + $stock->quantity_reserved)) * 100;
                if ($percentage > 10) {
                    $alerts->push([
                        'type' => 'warning',
                        'category' => 'damaged',
                        'priority' => 4,
                        'message' => "{$stock->item->name} has high damaged stock",
                        'item' => $stock->item->name,
                        'sku' => $stock->item->sku,
                        'damaged_qty' => $stock->quantity_damaged,
                        'percentage' => round($percentage, 1),
                        'action' => 'Investigate and address quality issues',
                    ]);
                }
            }
        }

        // Pending Requests
        $pendingRequests = ItemRequest::where('branch_id', $branchId)
            ->where('status', 'pending')
            ->where('request_date', '<', now()->subDays(2))
            ->count();

        if ($pendingRequests > 0) {
            $alerts->push([
                'type' => 'info',
                'category' => 'pending_requests',
                'priority' => 5,
                'message' => "{$pendingRequests} requests pending for more than 2 days",
                'count' => $pendingRequests,
                'action' => 'Review and process pending requests',
            ]);
        }

        return $alerts->sortBy('priority')->when($this->alertType, function ($collection) {
            return $collection->where('category', $this->alertType);
        });
    }

    public function getAlertSummary()
    {
        $alerts = $this->getAllAlerts();

        return [
            'total' => $alerts->count(),
            'critical' => $alerts->where('type', 'critical')->count(),
            'warning' => $alerts->where('type', 'warning')->count(),
            'info' => $alerts->where('type', 'info')->count(),
            'expired' => $alerts->where('category', 'expired')->count(),
            'low_stock' => $alerts->where('category', 'low_stock')->count(),
            'out_of_stock' => $alerts->where('category', 'out_of_stock')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.branch-dashboard.analytics.alerts-dashboard', [
            'alerts' => $this->getAllAlerts(),
            'summary' => $this->getAlertSummary(),
        ]);
    }
}
