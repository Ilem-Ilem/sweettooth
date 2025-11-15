<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\Reorder;

use App\Models\DepartmentReport;
use App\Services\Reports\ReorderReportService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title, On};
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Reorder Report')]
class Index extends Component
{
    use Interactions;

    public $branchId;
    public $periodFilter = 'today';
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
        $this->generatePreview(); // Auto-generate on load
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->branchId = $branchId;
        $this->generatePreview(); // Regenerate report for new branch
    }

    public function setDateRange()
    {
        $this->customDateFrom = Carbon::today()->toDateString();
        $this->customDateTo = Carbon::today()->toDateString();
    }

    public function generatePreview()
    {
        $this->isLoading = true;

        try {
            $service = new ReorderReportService();

            $service->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $this->reportData = $service->getReportData();
            $this->summaryMetrics = $service->generateSummaryMetrics($this->reportData);
            $this->chartsData = $service->generateChartsData($this->reportData);

            $this->toast()->success('Reorder report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: ' . $e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateReport()
    {
        try {
            $service = new ReorderReportService();

            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth('employees')->id());

            $this->showReportModal = true;
            $this->toast()->success('Reorder report saved successfully')->send();
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

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.reports.reorder.index');
    }
}
