<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\StockTurnover;

use App\Models\DepartmentReport;
use App\Services\Reports\Definitions\InventoryStockTurnoverDefinition;
use App\Services\Reports\StockTurnoverReportService;
use App\Livewire\Traits\RequiresDepartmentSelection;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Stock Turnover Report')]
class Index extends Component
{
    use Interactions, RequiresDepartmentSelection;

    public $branchId;

    public $periodFilter = 'month';

    public $customDateFrom;

    public $customDateTo;

    public $departmentId;

    public $reportData = null;

    public $summaryMetrics = [];

    public $chartsData = [];

    public $tablesData = [];

    public $narrative = [];

    public $isLoading = false;

    public $generatedReport = null;

    public $showReportModal = false;

    public function mount()
    {
        $this->branchId = current_branch_id();
        $this->departmentId = session('selected_department_id');
        $this->initDepartments($this->branchId);
        $this->setDateRange();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->branchId = $branchId;
        $this->initDepartments($branchId);
        $this->generatePreview(); // Regenerate report for new branch
    }

    public function setDateRange()
    {
        switch ($this->periodFilter) {
            case 'week':
                $this->customDateFrom = Carbon::now()->startOfWeek()->toDateString();
                $this->customDateTo = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'month':
                $this->customDateFrom = Carbon::now()->startOfMonth()->toDateString();
                $this->customDateTo = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'quarter':
                $this->customDateFrom = Carbon::now()->startOfQuarter()->toDateString();
                $this->customDateTo = Carbon::now()->endOfQuarter()->toDateString();
                break;
            case 'year':
                $this->customDateFrom = Carbon::now()->startOfYear()->toDateString();
                $this->customDateTo = Carbon::now()->endOfYear()->toDateString();
                break;
        }
    }

    public function generatePreview()
    {
        if (! $this->ensureDepartmentSelected('preview')) {
            return;
        }

        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        $this->isLoading = true;

        try {
            $service = (new StockTurnoverReportService())
                ->useDefinition(new InventoryStockTurnoverDefinition());

            $service->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $payload = $service->getReportData();
            $this->reportData = $payload['report_data'] ?? $payload;
            $this->summaryMetrics = $payload['summary_metrics'] ?? ($this->reportData['summary_metrics'] ?? []);
            $this->chartsData = $payload['charts_data'] ?? [];
            $this->tablesData = $payload['tables'] ?? [];
            $this->narrative = $payload['narrative'] ?? [];

            $this->toast()->success('Stock turnover report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateReport()
    {
        if (! $this->ensureDepartmentSelected('generate')) {
            return;
        }

        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        try {
            $service = (new StockTurnoverReportService())
                ->useDefinition(new InventoryStockTurnoverDefinition());

            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->showReportModal = true;
            $this->toast()->success('Stock turnover report saved successfully')->send();
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

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.reports.stock-turnover.index');
    }
}
