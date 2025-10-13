<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class StockAnalytics extends Component
{
    public $selectedItemId = null;
    public $selectedItem = null;
    public $timeRange = '30'; // days
    public $chartType = 'line'; // line, area, candlestick

    public function getBranchId()
    {
        return request()->query('b_id');
    }

    public function mount()
    {
        $branchId = $this->getBranchId();

        // Select first item by default
        $firstItem = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->first();

        if ($firstItem) {
            $this->selectedItemId = $firstItem->id;
            $this->loadItemData();
        }
    }

    public function updatedSelectedItemId()
    {
        $this->loadItemData();
    }

    public function loadItemData()
    {
        if ($this->selectedItemId) {
            $branchId = $this->getBranchId();

            $this->selectedItem = Item::with(['stocks' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])->find($this->selectedItemId);
        }
    }

    public function getStockMovementData()
    {
        if (!$this->selectedItemId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        $movements = StockMovement::where('item_id', $this->selectedItemId)
            ->where('branch_id', $branchId)
            ->where('movement_date', '>=', $startDate)
            ->orderBy('movement_date', 'asc')
            ->get();

        $data = [];
        $currentStock = $this->selectedItem->getCurrentStock($branchId);

        // Build time series data
        foreach ($movements as $movement) {
            $data[] = [
                'date' => $movement->movement_date->format('Y-m-d H:i'),
                'quantity_before' => $movement->quantity_before,
                'quantity_after' => $movement->quantity_after,
                'quantity' => $movement->quantity,
                'type' => $movement->type,
                'notes' => $movement->notes,
            ];
        }

        return $data;
    }

    public function getStockSummary()
    {
        if (!$this->selectedItemId) {
            return null;
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        $summary = StockMovement::where('item_id', $this->selectedItemId)
            ->where('branch_id', $branchId)
            ->where('movement_date', '>=', $startDate)
            ->selectRaw('
                SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out,
                SUM(CASE WHEN type = "adjustment" THEN quantity ELSE 0 END) as total_adjustments,
                COUNT(*) as total_movements,
                AVG(quantity) as avg_movement
            ')
            ->first();

        return $summary;
    }

    public function getTopMovingItems()
    {
        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays(30);

        return Item::select('items.*')
            ->join('stock_movements', 'items.id', '=', 'stock_movements.item_id')
            ->where('items.branch_id', $branchId)
            ->where('stock_movements.movement_date', '>=', $startDate)
            ->groupBy('items.id')
            ->selectRaw('items.*, COUNT(stock_movements.id) as movement_count, SUM(stock_movements.quantity) as total_quantity')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $items = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $stockMovementData = $this->getStockMovementData();
        $stockSummary = $this->getStockSummary();
        $topMovingItems = $this->getTopMovingItems();

        return view('livewire.branch-dashboard.analytics.stock-analytics', [
            'items' => $items,
            'stockMovementData' => $stockMovementData,
            'stockSummary' => $stockSummary,
            'topMovingItems' => $topMovingItems,
        ]);
    }
}
