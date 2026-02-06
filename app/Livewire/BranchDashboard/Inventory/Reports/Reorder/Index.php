<?php

namespace App\Livewire\BranchDashboard\Inventory\Reports\Reorder;

use App\Models\DepartmentReport;
use App\Services\Reports\Definitions\InventoryReorderDefinition;
use App\Services\Reports\ReorderReportService;
use App\Livewire\Traits\RequiresDepartmentSelection;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Reorder Report')]
class Index extends Component
{
    use Interactions, RequiresDepartmentSelection;

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
        $this->branchId = current_branch_id();
        $this->departmentId = session('selected_department_id');
        $this->initDepartments($this->branchId);
        $this->setDateRange();
        $this->generatePreview(); // Auto-generate on load
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
        $this->customDateFrom = Carbon::today()->toDateString();
        $this->customDateTo = Carbon::today()->toDateString();
    }

    public function generatePreview()
    {
        if (! $this->ensureDepartmentSelected('preview')) {
            return;
        }

        $this->isLoading = true;

        try {
            $service = (new ReorderReportService())
                ->useDefinition(new InventoryReorderDefinition());

            $service->forBranch($this->branchId)
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $payload = $service->getReportData();
            $this->reportData = $payload['report_data'] ?? $payload;
            $this->summaryMetrics = $payload['summary_metrics'] ?? ($this->reportData['summary_metrics'] ?? []);
            $this->chartsData = $payload['charts_data'] ?? [];
            $this->tablesData = $payload['tables'] ?? [];
            $this->narrative = $payload['narrative'] ?? [];

            $this->toast()->success('Reorder report generated successfully')->send();
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
