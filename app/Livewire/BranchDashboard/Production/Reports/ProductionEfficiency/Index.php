<?php

namespace App\Livewire\BranchDashboard\Production\Reports\ProductionEfficiency;

use App\Models\DepartmentReport;
use App\Services\Reports\ProductionEfficiencyReportService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Title, Url};
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Production Efficiency Report')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    // Filters
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
        $this->b_id = $this->b_id ?? current_branch_id();
        $this->departmentId = session('selected_department_id');
        $this->setDateRange();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    /**
     * Set date range based on filter.
     */
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
            case 'custom':
                // Keep existing custom dates
                break;
        }
    }

    /**
     * Generate report preview.
     */
    public function generatePreview()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        $this->isLoading = true;

        try {
            $service = new ProductionEfficiencyReportService();

            $service->forBranch($this->b_id ?? current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $this->reportData = $service->getReportData();
            $this->summaryMetrics = $service->generateSummaryMetrics($this->reportData);
            $this->chartsData = $service->generateChartsData($this->reportData);

            $this->toast()->success('Report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error generating report: ' . $e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Generate and save report.
     */
    public function generateReport()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        try {
            $service = new ProductionEfficiencyReportService();

            $this->generatedReport = $service
                ->forBranch($this->b_id ?? current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth('employees')->id());

            $this->showReportModal = true;
            $this->toast()->success('Report generated and saved successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error generating report: ' . $e->getMessage())->send();
        }
    }

    /**
     * Submit report for review.
     */
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

    /**
     * Export report to CSV.
     */
    public function exportCsv()
    {
        if (!$this->reportData) {
            $this->toast()->warning('Please generate a report first')->send();
            return;
        }

        // This will be implemented with export functionality
        $this->toast()->info('CSV export will be implemented')->send();
    }

    /**
     * Export report to PDF.
     */
    public function exportPdf()
    {
        if (!$this->reportData) {
            $this->toast()->warning('Please generate a report first')->send();
            return;
        }

        // This will be implemented with export functionality
        $this->toast()->info('PDF export will be implemented')->send();
    }

    /**
     * Refresh report data.
     */
    public function refresh()
    {
        $this->generatePreview();
    }

    /**
     * Update period filter.
     */
    public function updatedPeriodFilter()
    {
        $this->setDateRange();
        if ($this->periodFilter !== 'custom') {
            $this->generatePreview();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.reports.production-efficiency.index');
    }
}
