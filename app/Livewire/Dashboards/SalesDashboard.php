<?php

namespace App\Livewire\Dashboards;

use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class SalesDashboard extends BaseDashboard
{
    public function mount()
    {
        parent::mount();
        // Verify user has sales access
        $this->verifyAccess();
    }

    /**
     * Verify user has access to sales dashboard
     */
    private function verifyAccess(): void
    {
        $role = $this->getUserRoleName();
        // Normalize role name to snake_case for comparison
        $normalizedRole = strtolower(str_replace(' ', '_', $role ?? ''));
        
        $allowedRoles = [
            'sales_manager',
            'till_supervisor',
            'cashier',
            'corner_store_manager',
            'corner_store_staff',
            'confectionaries_sales_staff',
        ];

        // Allow access if user has allowed role OR is super admin
        $isAllowed = in_array($normalizedRole, $allowedRoles) || is_super_admin();
        
        if (!$isAllowed) {
            // Log for debugging
            \Log::warning('Unauthorized sales dashboard access', [
                'user_id' => $this->user?->id,
                'role' => $role,
                'normalized_role' => $normalizedRole,
                'all_roles' => $this->user?->roles?->pluck('name')->toArray() ?? [],
                'is_super_admin' => is_super_admin(),
            ]);
            abort(403, 'Unauthorized access to sales dashboard. Your role (' . ($role ?? 'none') . ') does not have access to this dashboard.');
        }
    }

    /**
     * Get total sales for date range
     */
    public function getTotalSales(): float
    {
        return $this->remember('total_sales', function () {
            $range = $this->getDateRange();
            
            // Query from sales/transactions table if exists
            // For now, using estimated calculation
            return DB::table('transactions')
                ->where('branch_id', $this->getBranchId())
                ->whereBetween('created_at', [$range['from'], $range['to']])
                ->sum('total_amount') ?? 0;
        });
    }

    /**
     * Get transaction count
     */
    public function getTransactionCount(): int
    {
        return $this->remember('transaction_count', function () {
            $range = $this->getDateRange();
            
            return DB::table('transactions')
                ->where('branch_id', $this->getBranchId())
                ->whereBetween('created_at', [$range['from'], $range['to']])
                ->count();
        });
    }

    /**
     * Get today's sales
     */
    public function getTodaySales(): float
    {
        return DB::table('transactions')
            ->where('branch_id', $this->getBranchId())
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount') ?? 0;
    }

    /**
     * Get today's transaction count
     */
    public function getTodayTransactionCount(): int
    {
        return DB::table('transactions')
            ->where('branch_id', $this->getBranchId())
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    /**
     * Get average transaction value
     */
    public function getAverageTransactionValue(): float
    {
        $count = $this->getTodayTransactionCount();
        if ($count === 0) {
            return 0;
        }

        return $this->getTodaySales() / $count;
    }

    /**
     * Get top selling items
     */
    public function getTopSellingItems($limit = 10)
    {
        return $this->remember('top_selling_items_' . $limit, function () use ($limit) {
            return DB::table('transaction_items')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->where('transaction_items.branch_id', $this->getBranchId())
                ->whereDate('transaction_items.created_at', Carbon::today())
                ->groupBy('transaction_items.product_id', 'products.name')
                ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.total_amount) as total_sales')
                ->orderByDesc('total_qty')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions($limit = 10)
    {
        return $this->remember('recent_transactions_' . $limit, function () use ($limit) {
            return DB::table('transactions')
                ->where('branch_id', $this->getBranchId())
                ->whereDate('created_at', Carbon::today())
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get sales trend by hour
     */
    public function getSalesByHour()
    {
        return $this->remember('sales_by_hour', function () {
            return DB::table('transactions')
                ->where('branch_id', $this->getBranchId())
                ->whereDate('created_at', Carbon::today())
                ->selectRaw("DATE_FORMAT(created_at, '%H:00') as hour, COUNT(*) as transactions, SUM(total_amount) as sales")
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();
        });
    }

    /**
     * Get sales alerts
     */
    public function getSalesAlerts()
    {
        $alerts = [];
        $todaySales = $this->getTodaySales();
        
        // Example: Alert if sales are low
        if ($todaySales < 100) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Sales',
                'message' => 'Today\'s sales are below average',
            ];
        }

        return $alerts;
    }

    public function render()
    {
        try {
            return view('livewire.dashboards.sales.dashboard', [
                'totalSales' => $this->getTotalSales(),
                'transactionCount' => $this->getTransactionCount(),
                'todaySales' => $this->getTodaySales(),
                'todayTransactionCount' => $this->getTodayTransactionCount(),
                'averageTransactionValue' => $this->getAverageTransactionValue(),
                'topSellingItems' => $this->getTopSellingItems(),
                'recentTransactions' => $this->getRecentTransactions(),
                'salesByHour' => $this->getSalesByHour(),
                'salesAlerts' => $this->getSalesAlerts(),
            ]);
        } catch (\Exception $e) {
            $this->handleError('loading sales dashboard', $e);
            return view('livewire.dashboards.error');
        }
    }
}
