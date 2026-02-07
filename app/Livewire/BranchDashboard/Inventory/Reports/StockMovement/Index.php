<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\StockMovement;

use App\Models\DepartmentReport;
use App\Models\StockMovement;
use App\Services\Reports\Definitions\InventoryStockMovementDefinition;
use App\Services\Reports\StockMovementReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Stock Movement Report')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $branchId;

    public $periodFilter = 'week';

    public $customDateFrom;

    public $customDateTo;

    public $departmentId = null;

    public $reportData = null;

    public $summaryMetrics = [];

    public $chartsData = [];

    public $tablesData = [];

    public $narrative = [];

    public $isLoading = false;

    public $generatedReport = null;

    public $showReportModal = false;

    public array $debugInfo = [];

    public function mount()
    {
        $this->branchId = $this->b_id ?: current_branch_id();
        $this->setDateRange();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->branchId = $branchId;
        $this->generatePreview(); // Regenerate report for new branch
    }

    public function setDateRange()
    {
        switch ($this->periodFilter) {
            case 'today':
                $this->customDateFrom = Carbon::today()->toDateString();
                $this->customDateTo = Carbon::today()->toDateString();
                break;
            case 'yesterday':
                $this->customDateFrom = Carbon::yesterday()->toDateString();
                $this->customDateTo = Carbon::yesterday()->toDateString();
                break;
            case 'week':
                $this->customDateFrom = Carbon::now()->startOfWeek()->toDateString();
                $this->customDateTo = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'month':
                $this->customDateFrom = Carbon::now()->startOfMonth()->toDateString();
                $this->customDateTo = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->customDateFrom = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $this->customDateTo = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
        }
    }

    public function generatePreview()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        $this->isLoading = true;

        try {
            $service = (new StockMovementReportService())
                ->useDefinition(new InventoryStockMovementDefinition());

            $service->forBranch($this->branchId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $payload = $service->getReportData();
            $rawReport = $payload['report_data'] ?? $payload;
            $this->reportData = $rawReport['report_data'] ?? $rawReport;
            $this->summaryMetrics = $payload['summary_metrics'] ?? ($rawReport['summary_metrics'] ?? []);
            $this->chartsData = $payload['charts_data'] ?? ($rawReport['charts_data'] ?? []);
            $this->tablesData = $rawReport['tables'] ?? [];
            $this->narrative = $rawReport['narrative'] ?? [];
            $this->debugInfo = $this->buildDebugInfo();

            $this->toast()->success('Stock movement report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateReport()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        try {
            $service = (new StockMovementReportService())
                ->useDefinition(new InventoryStockMovementDefinition());

            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->showReportModal = true;
            $this->toast()->success('Stock movement report saved successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        }
    }

    public function submitForReview($reportId)
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);
            $report->update(['status' => 'pending_review']);
            $this->toast()->success('Report submitted for review')->send();
            $this->showReportModal = false;
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        }
    }

    public function updatedPeriodFilter()
    {
        $this->setDateRange();
        if ($this->periodFilter !== 'custom') {
            $this->generatePreview();
        }
    }

    protected function buildDebugInfo(): array
    {
        $dateFrom = Carbon::parse($this->customDateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->customDateTo)->endOfDay();

        $baseQuery = StockMovement::query()
            ->where(function ($q) {
                $q->where('branch_id', $this->branchId)
                    ->orWhereHas('stock', function ($sq) {
                        $sq->where('branch_id', $this->branchId);
                    });
            })
            ->whereBetween(
                \DB::raw('COALESCE(movement_date, created_at)'),
                [$dateFrom, $dateTo]
            );

        $total = (clone $baseQuery)->count();
        $inbound = (clone $baseQuery)->whereIn('type', ['in', 'return'])->count();
        $outbound = (clone $baseQuery)->whereIn('type', ['out', 'damaged', 'transfer'])->count();
        $first = (clone $baseQuery)->min('movement_date');
        $last = (clone $baseQuery)->max('movement_date');

        $stockBranchMovements = StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween(\DB::raw('COALESCE(movement_date, created_at)'), [$dateFrom, $dateTo])
            ->count();
        $stockBranchCreated = StockMovement::query()
            ->whereHas('stock', fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween(\DB::raw('COALESCE(movement_date, created_at)'), [$dateFrom, $dateTo])
            ->count();
        $movementBranchMovements = StockMovement::query()
            ->where('branch_id', $this->branchId)
            ->whereBetween(\DB::raw('COALESCE(movement_date, created_at)'), [$dateFrom, $dateTo])
            ->count();
        $movementBranchCreated = StockMovement::query()
            ->where('branch_id', $this->branchId)
            ->whereBetween(\DB::raw('COALESCE(movement_date, created_at)'), [$dateFrom, $dateTo])
            ->count();
        $nullStockId = StockMovement::query()
            ->where('branch_id', $this->branchId)
            ->whereNull('stock_id')
            ->count();
        $overallMovements = StockMovement::query()->count();
        $topMovementBranches = StockMovement::query()
            ->select('branch_id', DB::raw('COUNT(*) as c'))
            ->groupBy('branch_id')
            ->orderByDesc('c')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [$row->branch_id, (int) $row->c])
            ->toArray();
        $topStockBranches = DB::table('stock_movements')
            ->join('stocks', 'stocks.id', '=', 'stock_movements.stock_id')
            ->select('stocks.branch_id', DB::raw('COUNT(*) as c'))
            ->groupBy('stocks.branch_id')
            ->orderByDesc('c')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [$row->branch_id, (int) $row->c])
            ->toArray();

        return [
            'branch_id' => $this->branchId,
            'from' => $dateFrom->toDateTimeString(),
            'to' => $dateTo->toDateTimeString(),
            'total' => $total,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'first_movement' => $first,
            'last_movement' => $last,
            'stock_branch_by_movement_date' => $stockBranchMovements,
            'stock_branch_by_created_at' => $stockBranchCreated,
            'movement_branch_by_movement_date' => $movementBranchMovements,
            'movement_branch_by_created_at' => $movementBranchCreated,
            'null_stock_id_count' => $nullStockId,
            'overall_movements' => $overallMovements,
            'top_movement_branches' => $topMovementBranches,
            'top_stock_branches' => $topStockBranches,
        ];
    }

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.reports.stock-movement.index');
    }
}
