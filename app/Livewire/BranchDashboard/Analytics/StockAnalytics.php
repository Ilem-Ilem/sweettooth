<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Department;
use App\Models\Item;
use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class StockAnalytics extends Component
{
    use WithPagination;

    public $selectedItemId = null;
    public $selectedItem = null;
    public $timeRange = 'month';
    public $dateFrom;
    public $dateTo;

    #[Url(keep: true)]
    public $b_id;

    public $trendChartType = 'line';
    public $trendViewMode = 'chart';
    public $typesChartType = 'bar';
    public $typesViewMode = 'chart';
    public $deptChartType = 'bar';
    public $deptViewMode = 'chart';
    public $freqChartType = 'line';
    public $freqViewMode = 'chart';

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');

        $branchId = $this->getBranchId();

        $firstItem = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->first();

        if ($firstItem) {
            $this->selectedItemId = $firstItem->id;
            $this->loadItemData();
            $this->dispatch('charts-updated');
        }
    }

    public function updated($property)
    {
        if ($property === 'selectedItemId') {
            $this->loadItemData();
            $this->resetPage();
            $this->dispatch('charts-updated');
        } elseif (in_array($property, [
            'timeRange',
            'dateFrom',
            'dateTo',
            'trendChartType',
            'trendViewMode',
            'typesChartType',
            'typesViewMode',
            'deptChartType',
            'deptViewMode',
            'freqChartType',
            'freqViewMode'
        ])) {
            $this->resetPage();
            $this->dispatch('charts-updated');
        }
    }

    public function loadItemData()
    {
        if ($this->selectedItemId) {
            $branchId = $this->getBranchId();
            $this->selectedItem = Item::with(['stocks' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])->find($this->selectedItemId);
        } else {
            $this->selectedItem = null;
        }
    }

    public function getStockMovementTrend()
    {
        if (!$this->selectedItemId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $stock = Stock::where('branch_id', $branchId)
            ->where('item_id', $this->selectedItemId)
            ->first();

        if (!$stock) {
            return [];
        }

        $query = StockMovement::where('stock_id', $stock->id)
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->orderBy('movement_date', 'asc');

        if ($this->trendViewMode === 'table') {
            return $query->paginate(10, ['*'], 'trendPage');
        }

        $movements = $query->get();
        return $movements->map(function ($movement) {
            return [
                'date' => $movement->movement_date->format('Y-m-d H:i'),
                'quantity_before' => (float) $movement->quantity_before,
                'quantity_after' => (float) $movement->quantity_after,
                'quantity' => (float) $movement->quantity,
                'type' => $movement->type,
            ];
        })->toArray();
    }

    public function getStockSummary()
    {
        if (!$this->selectedItemId) {
            return null;
        }

        $branchId = $this->getBranchId();
        $stock = Stock::where('branch_id', $branchId)
            ->where('item_id', $this->selectedItemId)
            ->first();

        if (!$stock) {
            return null;
        }

        $summary = StockMovement::where('stock_id', $stock->id)
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('
                SUM(CASE WHEN type IN ("in", "return") THEN quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN type IN ("out", "transfer") THEN quantity ELSE 0 END) as total_out,
                SUM(CASE WHEN type = "adjustment" THEN quantity ELSE 0 END) as total_adjustments,
                SUM(CASE WHEN type = "damaged" THEN quantity ELSE 0 END) as total_damaged,
                COUNT(*) as total_movements,
                AVG(quantity) as avg_movement
            ')
            ->first();

        $summary->current_available = $stock->quantity_available;
        $summary->current_reserved = $stock->quantity_reserved;
        $summary->current_damaged = $stock->quantity_damaged;
        $summary->total_stock = $stock->quantity_available + $stock->quantity_reserved + $stock->quantity_damaged;

        return $summary;
    }

    public function getDepartmentUsage()
    {
        if (!$this->selectedItemId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $query = ItemDispatch::select('departments.name as department_name', DB::raw('SUM(item_dispatches.quantity) as total_quantity'))
            ->join('item_requests', 'item_dispatches.request_id', '=', 'item_requests.id')
            ->join('departments', 'item_requests.department_id', '=', 'departments.id')
            ->where('item_dispatches.item_id', $this->selectedItemId)
            ->where('item_dispatches.branch_id', $branchId)
            ->whereBetween('item_dispatches.dispatch_time', [$this->dateFrom, $this->dateTo])
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total_quantity');

        return $this->deptViewMode === 'table' ? $query->paginate(10, ['*'], 'deptPage') : $query->get();
    }

    public function getMovementsByType()
    {
        if (!$this->selectedItemId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $stock = Stock::where('branch_id', $branchId)
            ->where('item_id', $this->selectedItemId)
            ->first();

        if (!$stock) {
            return [];
        }

        $query = StockMovement::where('stock_id', $stock->id)
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->select('type', DB::raw('SUM(quantity) as total'))
            ->groupBy('type');

        if ($this->typesViewMode === 'table') {
            return $query->paginate(10, ['*'], 'typesPage');
        }

        return $query->get()->mapWithKeys(function ($item) {
            return [$item->type => (float) $item->total];
        })->toArray();
    }

    public function getTopMovingItems()
    {
        $branchId = $this->getBranchId();
        return Item::select('items.id', 'items.name', 'items.sku')
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->join('stock_movements', 'stocks.id', '=', 'stock_movements.stock_id')
            ->where('items.branch_id', $branchId)
            ->where('items.status', 'active')
            ->whereBetween('stock_movements.movement_date', [$this->dateFrom, $this->dateTo])
            ->groupBy('items.id', 'items.name', 'items.sku')
            ->selectRaw('COUNT(stock_movements.id) as movement_count, SUM(ABS(stock_movements.quantity)) as total_quantity')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->get();
    }

    public function getLowStockItems()
    {
        $branchId = $this->getBranchId();
        return Item::select('items.*', 'stocks.quantity_available', 'stocks.quantity_reserved')
            ->join('stocks', 'items.id', '=', 'stocks.item_id')
            ->where('items.branch_id', $branchId)
            ->where('items.status', 'active')
            ->whereNotNull('items.reorder_level')
            ->whereRaw('stocks.quantity_available < items.reorder_level')
            ->orderBy('stocks.quantity_available', 'asc')
            ->limit(10)
            ->get();
    }

    public function getStockValue()
    {
        $branchId = $this->getBranchId();
        $totalValue = Stock::where('branch_id', $branchId)
            ->selectRaw('SUM((quantity_available + quantity_reserved) * average_cost) as total_value')
            ->first();
        return $totalValue->total_value ?? 0;
    }

    public function getSelectedItemStockValue()
    {
        if (!$this->selectedItemId) {
            return 0;
        }

        $branchId = $this->getBranchId();
        $stock = Stock::where('branch_id', $branchId)
            ->where('item_id', $this->selectedItemId)
            ->first();
        return $stock ? ($stock->quantity_available + $stock->quantity_reserved) * $stock->average_cost : 0;
    }

    public function getMovementFrequency()
    {
        if (!$this->selectedItemId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $stock = Stock::where('branch_id', $branchId)
            ->where('item_id', $this->selectedItemId)
            ->first();

        if (!$stock) {
            return [];
        }

        $query = StockMovement::where('stock_id', $stock->id)
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(movement_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date');

        return $this->freqViewMode === 'table' ? $query->paginate(10, ['*'], 'freqPage') : $query->get();
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $items = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $departments = Department::where('branch_id', $branchId)
            ->orWhereNull('branch_id')
            ->orderBy('name')
            ->get();

        return view('livewire.branch-dashboard.analytics.stock-analytics', [
            'items' => $items,
            'departments' => $departments,
            'stockMovementTrend' => $this->getStockMovementTrend(),
            'stockSummary' => $this->getStockSummary(),
            'departmentUsage' => $this->getDepartmentUsage(),
            'movementsByType' => $this->getMovementsByType(),
            'topMovingItems' => $this->getTopMovingItems(),
            'lowStockItems' => $this->getLowStockItems(),
            'totalStockValue' => $this->getStockValue(),
            'selectedItemStockValue' => $this->getSelectedItemStockValue(),
            'movementFrequency' => $this->getMovementFrequency(),
        ]);
    }
}
