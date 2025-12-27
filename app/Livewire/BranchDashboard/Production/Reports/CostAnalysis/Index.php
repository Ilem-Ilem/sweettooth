<?php

namespace App\Livewire\BranchDashboard\Production\Reports\CostAnalysis;

use App\Models\DepartmentReport;
use App\Services\Reports\CostAnalysisReportService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Cost Analysis Report')]
class Index extends Component
{
    use Interactions;

    public $periodFilter = 'week';
    public $customDateFrom;
    public $customDateTo;
    public $departmentId;

    // Report data
    public $reportData = null;
    public $summaryMetrics = [];
    public $chartsData = [];
    public $isLoading = false;

    // Generated report
    public $generatedReport = null;
    public $showReportModal = false;

    public function mount()
    {
        $this->customDateFrom = Carbon::now()->startOfWeek()->toDateString();
        $this->customDateTo = Carbon::now()->endOfWeek()->toDateString();
        $this->departmentId = session('selected_department_id');
    }

    public function updatedPeriodFilter()
    {
        $this->setDateRange();
        if ($this->periodFilter !== 'custom') {
            $this->generatePreview();
        }
    }

    public function setDateRange()
    {
        switch ($this->periodFilter) {
            case 'today':
                $this->customDateFrom = $this->customDateTo = Carbon::today()->toDateString();
                break;
            case 'yesterday':
                $this->customDateFrom = $this->customDateTo = Carbon::yesterday()->toDateString();
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
            $service = new CostAnalysisReportService();

            $service->forBranch(current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $this->reportData = $service->getReportData();
            $this->summaryMetrics = $this->reportData['summary_metrics'] ?? $service->getSummaryMetrics($this->reportData);
            $this->chartsData = $service->getChartsData($this->reportData);

            $this->toast()->success('Cost analysis report generated successfully')->send();
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
            $service = new CostAnalysisReportService();

            $this->generatedReport = $service
                ->forBranch(current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->showReportModal = true;
            $this->toast()->success('Report generated and saved successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error generating report: ' . $e->getMessage())->send();
        }
    }

    public function submitForReview($reportId)
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);

            if (!$report->isEditable()) {
                $this->toast()->error('Report cannot be edited in its current state')->send();
                return;
            }

            $report->update(['status' => 'pending_review']);

            $this->toast()->success('Report submitted for review')->send();
            $this->showReportModal = false;
            $this->generatedReport = null;
        } catch (\Exception $e) {
            $this->toast()->error('Error submitting report: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.reports.cost-analysis.index');
    }
}
