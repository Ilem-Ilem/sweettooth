<?php

namespace App\Livewire\Dashboards;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class InventoryDashboard extends BaseDashboard
{
    public function mount()
    {
        parent::mount();
        // Verify user has inventory access
        $this->verifyAccess();
    }

    /**
     * Verify user has access to inventory dashboard
     */
    private function verifyAccess(): void
    {
        $role = $this->getUserRoleName();
        $allowedRoles = [
            'inventory_manager',
            'stock_controller',
            'store_keeper',
        ];

        // Allow access if user has allowed role OR is super admin
        $isAllowed = in_array($role, $allowedRoles) || is_super_admin();
        
        if (!$isAllowed) {
            abort(403, 'Unauthorized access to inventory dashboard');
        }
    }

    /**
     * Get total stock value
     */
    public function getTotalStockValue(): float
    {
        return $this->remember('total_stock_value', function () {
            return Stock::where('branch_id', $this->getBranchId())
                ->join('items', 'stocks.item_id', '=', 'items.id')
                ->selectRaw('SUM(stocks.quantity * items.last_unit_price) as total')
                ->value('total') ?? 0;
        });
    }

    /**
     * Get total items in stock
     */
    public function getTotalItems(): int
    {
        return $this->remember('total_items', function () {
            return Stock::where('branch_id', $this->getBranchId())
                ->where('quantity', '>', 0)
                ->count();
        });
    }

    /**
     * Get count of low stock items
     */
    public function getLowStockCount(): int
    {
        return $this->remember('low_stock_count', function () {
            return Stock::where('branch_id', $this->getBranchId())
                ->join('items', 'stocks.item_id', '=', 'items.id')
                ->whereRaw('stocks.quantity <= items.reorder_point')
                ->count();
        });
    }

    /**
     * Get recent stock movements
     */
    public function getRecentMovements($limit = 10)
    {
        return $this->remember('recent_movements_' . $limit, function () use ($limit) {
            return StockMovement::where('branch_id', $this->getBranchId())
                ->with('item', 'createdBy')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems($limit = 15)
    {
        return $this->remember('low_stock_items_' . $limit, function () use ($limit) {
            return Stock::where('branch_id', $this->getBranchId())
                ->join('items', 'stocks.item_id', '=', 'items.id')
                ->selectRaw('items.id, items.name, items.sku, items.reorder_point, stocks.quantity, items.last_unit_price')
                ->whereRaw('stocks.quantity <= items.reorder_point')
                ->orderBy('stocks.quantity', 'asc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get total stock movements count today
     */
    public function getTodayMovementsCount(): int
    {
        return $this->remember('today_movements_count', function () {
            return StockMovement::where('branch_id', $this->getBranchId())
                ->whereDate('created_at', Carbon::today())
                ->count();
        });
    }

    /**
     * Get stock health check status
     */
    public function getHealthStatus(): string
    {
        $lowStockCount = $this->getLowStockCount();
        $totalItems = $this->getTotalItems();

        if ($totalItems === 0) {
            return 'warning';
        }

        $percentage = ($lowStockCount / $totalItems) * 100;

        if ($percentage > 30) {
            return 'critical';
        } elseif ($percentage > 10) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Get items by category (for chart)
     */
    public function getItemsByCategory()
    {
        return $this->remember('items_by_category', function () {
            return Stock::where('branch_id', $this->getBranchId())
                ->join('items', 'stocks.item_id', '=', 'items.id')
                ->groupBy('items.category_id')
                ->selectRaw('items.category_id, COUNT(DISTINCT stocks.item_id) as count, SUM(stocks.quantity) as total_qty')
                ->get();
        });
    }

    /**
     * Get critical alerts
     */
    public function getCriticalAlerts()
    {
        $alerts = [];
        $lowStockCount = $this->getLowStockCount();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} items are below reorder point",
                'action' => 'view-low-stock',
            ];
        }

        $outOfStockCount = Stock::where('branch_id', $this->getBranchId())
            ->where('quantity', 0)
            ->count();

        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Out of Stock',
                'message' => "{$outOfStockCount} items are out of stock",
                'action' => 'view-out-of-stock',
            ];
        }

        return $alerts;
    }

    public function render()
    {
        try {
            return view('livewire.dashboards.inventory.dashboard', [
                'totalStockValue' => $this->getTotalStockValue(),
                'totalItems' => $this->getTotalItems(),
                'lowStockCount' => $this->getLowStockCount(),
                'todayMovementsCount' => $this->getTodayMovementsCount(),
                'healthStatus' => $this->getHealthStatus(),
                'recentMovements' => $this->getRecentMovements(),
                'lowStockItems' => $this->getLowStockItems(),
                'criticalAlerts' => $this->getCriticalAlerts(),
            ]);
        } catch (\Exception $e) {
            $this->handleError('loading inventory dashboard', $e);
            return view('livewire.dashboards.error');
        }
    }
}
