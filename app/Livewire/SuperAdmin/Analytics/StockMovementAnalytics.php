<?php

namespace App\Livewire\SuperAdmin\Analytics;

use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Branch;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Department;
use Livewire\WithPagination;
use App\Models\StockMovement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class StockMovementAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $movementType = '';
    public $selectedItem = null;
    public $searchTerm = '';
    public $itemSearch = '';
    public $filterShift = '';
    public $filterDepartment = '';

    #[Url(keep:true)]
    public $branchId;

    // View mode: 'table', 'feed'
    public $viewMode = 'table';

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
        $this->branchId = Branch::first()->id; 
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function updatedSelectedItem()
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

    public function updatedFilterShift()
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatedbranchId($branchId){
       $this->getAvailableItems();
       $this->getTopMovedItems();
       $this->getMovementTypeDistribution();
       $this->getVelocityAnalysis();
    }
    /**
     * Get comprehensive analytics summary
     */
    private function getAnalyticsSummary($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $baseQuery = StockMovement::query()
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo]);

        // Apply filters
        $filteredQuery = clone $baseQuery;
        $this->applyFiltersToQuery($filteredQuery);

        // Today's movements
        $todayQuery = StockMovement::query()
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereDate('movement_date', today());

        // Compare with previous period
        $daysDiff = $dateFrom->diffInDays($dateTo);

        $previousPeriodQuery = StockMovement::query()
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [
                $dateFrom->copy()->subDays($daysDiff)->startOfDay(),
                $dateFrom->copy()->subDay()->endOfDay()
            ]);

        return [
            'today' => [
                'stock_in' => $todayQuery->clone()->whereIn('type', ['in', 'return'])->sum('quantity'),
                'stock_out' => abs($todayQuery->clone()->whereIn('type', ['out', 'damaged', 'transfer'])->sum('quantity')),
                'transfers' => $todayQuery->clone()->where('type', 'transfer')->count(),
                'total_movements' => $todayQuery->count(),
            ],
            'period' => [
                'stock_in' => $filteredQuery->clone()->whereIn('type', ['in', 'return'])->sum('quantity'),
                'stock_out' => abs($filteredQuery->clone()->whereIn('type', ['out', 'damaged', 'transfer'])->sum('quantity')),
                'transfers' => $filteredQuery->clone()->where('type', 'transfer')->count(),
                'total_movements' => $filteredQuery->count(),
                'adjustments' => $filteredQuery->clone()->where('type', 'adjustment')->count(),
                'damaged' => abs($filteredQuery->clone()->where('type', 'damaged')->sum('quantity')),
            ],
            'previous_period' => [
                'total_movements' => $previousPeriodQuery->count(),
            ],
            'latest_movement' => $filteredQuery->latest('movement_date')->first()?->movement_date,
            'most_moved_item' => $this->getMostMovedItem($branchId),
            'most_active_user' => $this->getMostActiveUser($branchId),
            'peak_hours' => $this->getPeakActivityHours($branchId),
            'daily_breakdown' => $this->getDailyBreakdown($branchId),
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFiltersToQuery($query)
    {
        $query->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
            ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('stock.item', function ($subQuery) {
                        $subQuery->where('name', 'like', '%'.$this->searchTerm.'%')
                            ->orWhere('sku', 'like', '%'.$this->searchTerm.'%');
                    })
                    ->orWhereHas('mover', function ($subQuery) {
                        $subQuery->where('name', 'like', '%'.$this->searchTerm.'%');
                    })
                    ->orWhere('notes', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->when($this->filterShift, function ($q) {
                $q->whereHasMorph('reference', ['App\Models\ItemRequest'], function ($subQuery) {
                    $subQuery->where('shift', $this->filterShift);
                });
            })
            ->when($this->filterDepartment, function ($q) {
                $q->whereHasMorph('reference', ['App\Models\ItemRequest'], function ($subQuery) {
                    $subQuery->where('department_id', $this->filterDepartment);
                });
            });
    }

    /**
     * Get daily breakdown of movements
     */
    private function getDailyBreakdown($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('DATE(movement_date) as date')
            ->selectRaw('SUM(CASE WHEN type IN ("in", "return") THEN quantity ELSE 0 END) as stock_in')
            ->selectRaw('ABS(SUM(CASE WHEN type IN ("out", "transfer", "damaged") THEN quantity ELSE 0 END)) as stock_out')
            ->selectRaw('COUNT(*) as total_movements')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();
    }

    /**
     * Get most moved item
     */
    private function getMostMovedItem($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->select('stock_id', DB::raw('COUNT(*) as movement_count'))
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('stock_id')
            ->orderByDesc('movement_count')
            ->with('stock.item')
            ->first();
    }

    /**
     * Get most active user
     */
    private function getMostActiveUser($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->select('moved_by', DB::raw('COUNT(*) as operation_count'))
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereNotNull('moved_by')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('moved_by')
            ->orderByDesc('operation_count')
            ->with('mover')
            ->first();
    }

    /**
     * Get peak activity hours
     */
    private function getPeakActivityHours($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $hourlyActivity = StockMovement::query()
            ->select(DB::raw('HOUR(movement_date) as hour'), DB::raw('COUNT(*) as count'))
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(3)
            ->get();

        return $hourlyActivity->map(function ($item) {
            return [
                'hour' => $item->hour,
                'count' => $item->count,
                'formatted' => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00',
            ];
        });
    }

    /**
     * Get activity feed
     */
    private function getActivityFeed($branchId, $limit = 20)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with([
            'stock.item',
            'mover',
            'reference',
            'reference.department'
        ])
        ->whereHas('stock', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->orderBy('movement_date', 'desc')
        ->limit($limit)
        ->get();
    }

    public function getAvailableItems()
    {
        $branchId = $this->branchId;

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
                    'uom' => $stock->item->unitOfMeasure?->symbol,
                ];
            });
    }

    /**
     * Export to CSV
     */
    public function exportCsv()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $query = StockMovement::with([
            'stock.item',
            'mover',
            'reference',
            'reference.department'
        ])
        ->whereHas('stock', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->whereBetween('movement_date', [$dateFrom, $dateTo]);

        $this->applyFiltersToQuery($query);

        $movements = $query->orderBy('movement_date', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'Date',
            'Time',
            'Item Name',
            'SKU',
            'Type',
            'Quantity Change',
            'Qty Before',
            'Qty After',
            'UOM',
            'Moved By',
            'Department',
            'Shift',
            'Reference',
            'Notes'
        ];

        foreach ($movements as $movement) {
            $request = ($movement->reference && $movement->reference instanceof \App\Models\ItemRequest)
                ? $movement->reference
                : null;

            $csvData[] = [
                $movement->movement_date->format('Y-m-d'),
                $movement->movement_date->format('H:i:s'),
                $movement->stock->item->name ?? 'N/A',
                $movement->stock->item->sku ?? 'N/A',
                ucfirst($movement->type),
                ($movement->isInbound() ? '+' : '-') . number_format(abs($movement->quantity), 2),
                number_format($movement->quantity_before, 2),
                number_format($movement->quantity_after, 2),
                $movement->stock->item->uom ?? '',
                $movement->mover->name ?? 'N/A',
                $request?->department->name ?? 'N/A',
                $request ? ucfirst($request->shift) : 'N/A',
                $request ? $request->request_number : ($movement->reference_id ? '#' . $movement->reference_id : 'N/A'),
                $movement->notes ?? ''
            ];
        }

        $filename = 'stock-movement-analytics-' . now()->format('Y-m-d-His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get top moved items (text-based, no chart)
     */
    public function getTopMovedItems()
    {
        $branchId = $this->branchId;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->when($this->movementType, function ($query) {
                $query->where('type', $this->movementType);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_moved')
            ->groupBy('stock_id')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get();
    }

    /**
     * Get movement type distribution (text-based, no chart)
     */
    public function getMovementTypeDistribution()
    {
        $branchId = $this->branchId;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->when($this->movementType, function ($query) {
                $query->where('type', $this->movementType);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('type, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get velocity analysis (fastest moving items)
     */
    public function getVelocityAnalysis()
    {
        $branchId = $this->branchId;
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('stock_id', $this->selectedItem);
            })
            ->when($this->movementType, function ($query) {
                $query->where('type', $this->movementType);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('type', ['out', 'transfer'])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('stock_id')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function getBranches(){
        return Branch::all();
    }

    public function render()
    {
        $branchId = Branch::first()->id;

        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $query = StockMovement::with(['stock.item', 'mover', 'reference', 'reference.department'])
            ->whereHas('stock', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereBetween('movement_date', [$dateFrom, $dateTo]);

        // Apply filters
        $this->applyFiltersToQuery($query);

        $movements = $query->latest('movement_date')->paginate(15);

        $movementTypes = ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return'];

        // Get all analytics data
        $analytics = $this->getAnalyticsSummary($branchId);
        $typeDistribution = $this->getMovementTypeDistribution();
        $topMovedItems = $this->getTopMovedItems();
        $velocityAnalysis = $this->getVelocityAnalysis();

        // Get activity feed for feed view
        $activityFeed = $this->viewMode === 'feed' ? $this->getActivityFeed($branchId) : collect();

        // Get departments for filters
        $departments = Department::orderBy('name')->get();

        return view('livewire.super-admin.analytics.stock-movement-analytics', [
            'movements' => $movements,
            'movementTypes' => $movementTypes,
            'availableItems' => $this->getAvailableItems(),
            'analytics' => $analytics,
            'typeDistribution' => $typeDistribution,
            'topMovedItems' => $topMovedItems,
            'velocityAnalysis' => $velocityAnalysis,
            'activityFeed' => $activityFeed,
            'departments' => $departments,
        ]);
    }
}
