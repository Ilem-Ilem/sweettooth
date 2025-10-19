<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\StockMovement;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class StockMovementAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $movementType = '';
    public $selectedItem = null;
    public $searchTerm = '';
    public $itemSearch = '';
    public $trendData;
    public $typeDistribution;
    public $velocityAnalysis;

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'movementType',
        'selectedItem'
    ];

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedSelectedItem()
    {
        $this->resetPage();
        $this->updateChartData();
    }

    public function updatedMovementType()
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
        $this->trendData = $this->getMovementTrendData();
        $this->typeDistribution = $this->getMovementTypeDistribution();
        $this->velocityAnalysis = $this->getVelocityAnalysis();

        $this->dispatch('chartsUpdated', [
            'trendData' => $this->trendData,
            'typeDistribution' => $this->typeDistribution,
            'velocityAnalysis' => $this->velocityAnalysis
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

    public function getMovementTrendData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $movements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(movement_date) as date, type, SUM(quantity) as total_quantity')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $dates = [];
        $inData = [];
        $outData = [];
        $adjustmentData = [];

        $groupedByDate = $movements->groupBy('date');

        foreach ($groupedByDate as $date => $items) {
            $dates[] = \Carbon\Carbon::parse($date)->format('M d');

            // Stock In: 'in' and 'return' types
            $inData[] = $items->whereIn('type', ['in', 'return'])->sum('total_quantity');

            // Stock Out: 'out', 'transfer', 'damaged' types (use absolute values)
            $outData[] = abs($items->whereIn('type', ['out', 'transfer', 'damaged'])->sum('total_quantity'));

            // Adjustments: 'adjustment' type
            $adjustmentData[] = abs($items->where('type', 'adjustment')->sum('total_quantity'));
        }

        return [
            'categories' => $dates,
            'series' => [
                ['name' => 'Stock In', 'data' => $inData],
                ['name' => 'Stock Out', 'data' => $outData],
                ['name' => 'Adjustments', 'data' => $adjustmentData],
            ],
        ];
    }

    public function getMovementTypeDistribution()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $distribution = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return [
            'labels' => $distribution->pluck('type')->map(fn($type) => ucfirst($type))->toArray(),
            'series' => $distribution->pluck('count')->toArray(),
        ];
    }

    public function getTopMovedItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('stock_id, SUM(ABS(quantity)) as total_moved')
            ->groupBy('stock_id')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get();
    }

    public function getMovementSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $movements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_movements' => $movements->count(),
            'total_in' => $movements->whereIn('type', ['in', 'return'])->sum('quantity'),
            'total_out' => abs($movements->whereIn('type', ['out', 'transfer', 'damaged'])->sum('quantity')),
            'total_adjustments' => $movements->where('type', 'adjustment')->count(),
            'total_damaged' => abs($movements->where('type', 'damaged')->sum('quantity')),
            'total_transfers' => $movements->where('type', 'transfer')->count(),
        ];
    }

    public function getVelocityAnalysis()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $items = StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->whereIn('type', ['out', 'dispatch'])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(quantity) as total_quantity')
            ->groupBy('stock_id')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->get();

        return [
            'labels' => $items->map(fn($item) => $item->stock->item->name)->toArray(),
            'series' => [
                [
                    'name' => 'Movement Frequency',
                    'data' => $items->pluck('movement_count')->toArray(),
                ],
            ],
        ];
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $movements = StockMovement::with(['stock.item', 'mover'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->when($this->movementType, function ($query) {
                $query->where('type', $this->movementType);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->latest('movement_date')
            ->paginate(15);

        $movementTypes = ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return'];

        // Store chart data in public properties for JavaScript access
        $this->trendData = $this->getMovementTrendData();
        $this->typeDistribution = $this->getMovementTypeDistribution();
        $this->velocityAnalysis = $this->getVelocityAnalysis();

        return view('livewire.branch-dashboard.analytics.stock-movement-analytics', [
            'movements' => $movements,
            'movementTypes' => $movementTypes,
            'availableItems' => $this->getAvailableItems(),
            'summary' => $this->getMovementSummary(),
            'trendData' => $this->trendData,
            'typeDistribution' => $this->typeDistribution,
            'topMovedItems' => $this->getTopMovedItems(),
            'velocityAnalysis' => $this->velocityAnalysis,
        ]);
    }
}
