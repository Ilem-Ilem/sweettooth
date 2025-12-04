<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\StockMovement;
use App\Models\Stock;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Carbon\Carbon;

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
    public $filterShift = '';
    public $filterDepartment = '';
    #[Url(keep: true)]
    public $viewMode = 'table';

    #[Url(keep: true)]
    public ?string $b_id = null;

    protected $listeners = ['refresh' => '$refresh'];

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

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;

        return redirect()->route('branch-dashboard.analytics.stock-movement', [
            'b_id' => $this->b_id ?? request()->get('b_id'),
            'movementType' => $this->movementType,
            'viewMode' => $this->viewMode
        ]);
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedItem', 'movementType', 'dateFrom', 'dateTo', 'filterShift', 'filterDepartment', 'searchTerm'])) {
            $this->resetPage();
        }
    }

    private function getAnalyticsSummary($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $baseQuery = StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$dateFrom, $dateTo]);

        $filteredQuery = clone $baseQuery;
        $this->applyFiltersToQuery($filteredQuery);

        $todayQuery = StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('movement_date', today());

        $daysDiff = $dateFrom->diffInDays($dateTo);
        $previousPeriodQuery = StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [
                $dateFrom->copy()->subDays($daysDiff + 1),
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

    private function applyFiltersToQuery($query)
    {
        $query->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
            ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
            ->when($this->searchTerm, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('stock.item', fn ($ssq) => $ssq->where('name', 'like', "%{$this->searchTerm}%")
                        ->orWhere('sku', 'like', "%{$this->searchTerm}%"))
                        ->orWhereHas('mover', fn ($ssq) => $ssq->where('name', 'like', "%{$this->searchTerm}%"))
                        ->orWhere('notes', 'like', "%{$this->searchTerm}%");
                });
            })
            ->when($this->filterShift, fn ($q) => $q->whereHasMorph('reference', ['App\Models\ItemRequest'], fn ($sq) => $sq->where('shift', $this->filterShift)))
            ->when($this->filterDepartment, fn ($q) => $q->whereHasMorph('reference', ['App\Models\ItemRequest'], fn ($sq) => $sq->where('department_id', $this->filterDepartment)));
    }

    private function getDailyBreakdown($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
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

    private function getMostMovedItem($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->select('stock_id', DB::raw('COUNT(*) as movement_count'))
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('stock_id')
            ->orderByDesc('movement_count')
            ->with('stock.item')
            ->first();
    }

    private function getMostActiveUser($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->select('moved_by_id', 'moved_by_type', DB::raw('COUNT(*) as operation_count'))
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereNotNull('moved_by_id')
            ->whereNotNull('moved_by_type')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('moved_by_id', 'moved_by_type')
            ->orderByDesc('operation_count')
            ->with('mover')
            ->first();
    }

    private function getPeakActivityHours($branchId)
    {
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::query()
            ->select(DB::raw('HOUR(movement_date) as hour'), DB::raw('COUNT(*) as count'))
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(3)
            ->get()
            ->map(fn ($item) => [
                'hour' => $item->hour,
                'count' => $item->count,
                'formatted' => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00',
            ]);
    }

    // FIXED: Activity Feed now shows latest 20 movements (ignores filters)
    private function getActivityFeed($branchId, $limit = 20)
    {
        return StockMovement::with(['stock.item', 'mover', 'reference'])
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('movement_date', 'desc')
            ->limit($limit)
            ->get()
            ->append('department_name');
    }

    public function getAvailableItems()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');

        return Stock::with('item')
            ->where('branch_id', $branchId)
            ->when($this->itemSearch, fn ($q) => $q->whereHas('item', fn ($sq) => $sq->where('name', 'like', "%{$this->itemSearch}%")->orWhere('sku', 'like', "%{$this->itemSearch}%")))
            ->limit(50)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->item->name,
                'sku' => $s->item->sku,
                'uom' => $s->item->uom,
            ]);
    }

    // FIXED: CSV export — safe, no more crashes
    public function exportCsv()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo   = Carbon::parse($this->dateTo)->endOfDay();

        $movements = StockMovement::with(['stock.item', 'mover', 'reference'])
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->orderBy('movement_date', 'desc')
            ->get()
            ->append('department_name');

        // Apply filters manually
        $movements = $movements->filter(function ($m) {
            if ($this->selectedItem && $m->stock_id != $this->selectedItem) return false;
            if ($this->movementType && $m->type != $this->movementType) return false;
            if ($this->searchTerm) {
                $haystack = strtolower("{$m->stock->item->name} {$m->stock->item->sku} {$m->mover?->name} {$m->notes}");
                if (!str_contains($haystack, strtolower($this->searchTerm))) return false;
            }
            return true;
        });

        $csvData = [[
            'Date', 'Time', 'Item Name', 'SKU', 'Type', 'Quantity Change',
            'Qty Before', 'Qty After', 'UOM', 'Moved By', 'Department', 'Shift', 'Reference', 'Notes'
        ]];

        foreach ($movements as $m) {
            $ref = $m->reference;
            $isRequest = $ref instanceof \App\Models\ItemRequest;

            $csvData[] = [
                $m->movement_date->format('Y-m-d'),
                $m->movement_date->format('H:i:s'),
                $m->stock->item->name ?? 'N/A',
                $m->stock->item->sku ?? 'N/A',
                ucfirst($m->type),
                ($m->isInbound() ? '+' : '-') . number_format(abs($m->quantity), 2),
                number_format($m->quantity_before, 2),
                number_format($m->quantity_after, 2),
                $m->stock->item->uom ?? '',
                $m->mover?->name ?? 'System',
                $m->department_name ?? 'N/A',
                $isRequest ? ucfirst($ref->shift ?? '') : 'N/A',
                $ref ? ($ref->request_number ?? $ref->reference_number ?? "#{$m->reference_id}") : 'N/A',
                $m->notes ?? '',
            ];
        }

        $filename = 'stock-movements-' . now()->format('Y-m-d-His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) fputcsv($handle, $row);
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(fn () => print $csv, $filename, ['Content-Type' => 'text/csv']);
    }

    public function getTopMovedItems()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
            ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_moved')
            ->groupBy('stock_id')
            ->orderByDesc('total_moved')
            ->limit(10)
            ->get();
    }

    public function getMovementTypeDistribution()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
            ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->selectRaw('type, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get();
    }

    public function getVelocityAnalysis()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return StockMovement::with(['stock.item'])
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
            ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->whereIn('type', ['out', 'transfer'])
            ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('stock_id')
            ->orderByDesc('movement_count')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo   = Carbon::parse($this->dateTo)->endOfDay();

        // Main table with filters + pagination
        $query = StockMovement::with(['stock.item', 'mover', 'reference'])
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->orderBy('movement_date', 'desc');

        $this->applyFiltersToQuery($query);

        $movements = $query->paginate(15);
        $movements->through(fn ($m) => $m->append('department_name'));

        $activityFeed = $this->viewMode === 'feed'
            ? $this->getActivityFeed($branchId)
            : collect();

        return view('livewire.branch-dashboard.analytics.stock-movement-analytics', [
            'movements'        => $movements,
            'movementTypes'    => ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return'],
            'availableItems'   => $this->getAvailableItems(),
            'analytics'        => $this->getAnalyticsSummary($branchId),
            'typeDistribution' => $this->getMovementTypeDistribution(),
            'topMovedItems'    => $this->getTopMovedItems(),
            'velocityAnalysis' => $this->getVelocityAnalysis(),
            'activityFeed'     => $activityFeed,
            'departments'      => Department::orderBy('name')->get(),
        ]);
    }
}
