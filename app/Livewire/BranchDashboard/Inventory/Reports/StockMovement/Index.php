<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\StockMovement;

use App\Models\DepartmentReport;
use App\Services\Reports\StockMovementReportService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title, On};
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Stock Movement Report')]
class Index extends Component
{
    use Interactions;

    public $branchId;
    public $periodFilter = 'week';
    public $customDateFrom;
    public $customDateTo;
    public $departmentId;
    public $reportData = null;
    public $summaryMetrics = [];
    public $chartsData = [];
    public $isLoading = false;
    public $generatedReport = null;
    public $showReportModal = false;

    public function mount()
    {
        $this->branchId = current_branch_id();
        $this->departmentId = session('selected_department_id');
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
            $service = new StockMovementReportService();

            $service->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $this->reportData = $service->getReportData();
            $this->summaryMetrics = $service->generateSummaryMetrics($this->reportData);
            $this->chartsData = $service->generateChartsData($this->reportData);

            $this->toast()->success('Stock movement report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: ' . $e->getMessage())->send();
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
            $service = new StockMovementReportService();

            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth('employees')->id());

            $this->showReportModal = true;
            $this->toast()->success('Stock movement report saved successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: ' . $e->getMessage())->send();
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
            $this->toast()->error('Error: ' . $e->getMessage())->send();
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
        return view('livewire.branch-dashboard.inventory.reports.stock-movement.index');
    }
}
