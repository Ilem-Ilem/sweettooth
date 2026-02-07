<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\Reorder;

use App\Models\DepartmentReport;
use App\Services\Reports\Definitions\InventoryReorderDefinition;
use App\Services\Reports\ReorderReportService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Reorder Report')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $branchId;

    public $periodFilter = 'today';

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
        $this->branchId = $this->b_id ?: current_branch_id();
        $this->departmentId = null;
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
            $service = (new ReorderReportService())
                ->useDefinition(new InventoryReorderDefinition());

            $service->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $payload = $service->getReportData();
            $rawReport = $payload['report_data'] ?? $payload;
            $this->reportData = $rawReport['report_data'] ?? $rawReport;
            $this->summaryMetrics = $payload['summary_metrics'] ?? ($rawReport['summary_metrics'] ?? []);
            $this->chartsData = $payload['charts_data'] ?? ($rawReport['charts_data'] ?? []);
            $this->tablesData = $rawReport['tables'] ?? [];
            $this->narrative = $rawReport['narrative'] ?? [];

            $this->toast()->success('Reorder report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateReport()
    {
        try {
            $service = (new ReorderReportService())
                ->useDefinition(new InventoryReorderDefinition());

            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->showReportModal = true;
            $this->toast()->success('Reorder report saved successfully')->send();
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

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.reports.reorder.index');
    }
}
