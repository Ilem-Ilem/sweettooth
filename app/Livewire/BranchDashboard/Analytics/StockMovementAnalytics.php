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

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'movementType',
        'searchTerm'
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

    public function updatedMovementType()
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

    public function getMovementTrendData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $movements = StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
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
            $inData[] = $items->where('type', 'in')->sum('total_quantity');
            $outData[] = $items->where('type', 'out')->sum('total_quantity');
            $adjustmentData[] = $items->where('type', 'adjustment')->sum('total_quantity');
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
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_movements' => $movements->count(),
            'total_in' => $movements->where('type', 'in')->sum('quantity'),
            'total_out' => $movements->where('type', 'out')->sum('quantity'),
            'total_adjustments' => $movements->where('type', 'adjustment')->count(),
            'total_damaged' => $movements->where('type', 'damaged')->sum('quantity'),
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
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('stock.item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->movementType, function ($query) {
                $query->where('type', $this->movementType);
            })
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->latest('movement_date')
            ->paginate(15);

        $movementTypes = ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return'];

        return view('livewire.branch-dashboard.analytics.stock-movement-analytics', [
            'movements' => $movements,
            'movementTypes' => $movementTypes,
            'summary' => $this->getMovementSummary(),
            'trendData' => $this->getMovementTrendData(),
            'typeDistribution' => $this->getMovementTypeDistribution(),
            'topMovedItems' => $this->getTopMovedItems(),
            'velocityAnalysis' => $this->getVelocityAnalysis(),
        ]);
    }
}
