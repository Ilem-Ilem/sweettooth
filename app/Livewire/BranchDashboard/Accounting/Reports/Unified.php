<?php

namespace App\Livewire\BranchDashboard\Accounting\Reports;

use App\Models\DepartmentReport;
use App\Services\Reports\ReportRegistry;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Accounting Reports')]
class Unified extends Component
{
    use Interactions;

    public ?string $branchId = null;
    public string $periodFilter = 'month';
    public ?string $customDateFrom = null;
    public ?string $customDateTo = null;

    public ?string $reportKey = null;
    public $reportData = null;
    public $summaryMetrics = [];
    public $tablesData = [];
    public $narrative = [];
    public bool $isLoading = false;
    public $generatedReport = null;
    public $savedReports = [];

    public $availableReports = [];

    public function mount(): void
    {
        $this->branchId = current_branch_id();
        $this->loadReports();
        $this->setDateRange();
        $this->refreshSavedReports();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId): void
    {
        $this->branchId = $branchId;
        $this->clearReportData();
        $this->refreshSavedReports();
    }

    private function loadReports(): void
    {
        $allReports = ReportRegistry::availableForUser(auth()->user());
        $this->availableReports = array_values(array_filter($allReports, function ($report) {
            return ($report['meta']['category'] ?? null) === 'accounting';
        }));

        if (!$this->reportKey && !empty($this->availableReports)) {
            $this->reportKey = $this->availableReports[0]['key'] ?? null;
        }
    }

    public function updatedReportKey(): void
    {
        $this->clearReportData();
        $this->refreshSavedReports();
    }

    public function updatedPeriodFilter(): void
    {
        $this->setDateRange();
    }

    public function setDateRange(): void
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

    public function generatePreview(): void
    {
        $resolved = $this->resolveReport();
        if (!$resolved) {
            return;
        }

        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        $this->isLoading = true;

        try {
            $serviceClass = $resolved['service'];
            $definitionClass = $resolved['definition'];

            $service = (new $serviceClass())->useDefinition(new $definitionClass());
            $service->forBranch($this->branchId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $payload = $service->getReportData();
            $this->reportData = $payload['report_data'] ?? $payload;
            $this->summaryMetrics = $payload['summary_metrics'] ?? ($this->reportData['summary_metrics'] ?? []);
            $this->tablesData = $payload['tables'] ?? [];
            $this->narrative = $payload['narrative'] ?? [];

            $this->toast()->success('Accounting report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateReport(): void
    {
        $resolved = $this->resolveReport();
        if (!$resolved) {
            return;
        }

        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        try {
            $serviceClass = $resolved['service'];
            $definitionClass = $resolved['definition'];

            $service = (new $serviceClass())->useDefinition(new $definitionClass());
            $this->generatedReport = $service
                ->forBranch($this->branchId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->toast()->success('Report saved successfully')->send();
            $this->refreshSavedReports();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        }
    }

    public function loadSavedReport(string $reportId): void
    {
        $report = DepartmentReport::query()
            ->where('branch_id', $this->branchId)
            ->findOrFail($reportId);

        $payload = $report->report_data ?? [];
        $this->reportData = $payload;
        $this->summaryMetrics = $report->summary_metrics ?? ($payload['summary_metrics'] ?? []);
        $this->tablesData = $payload['tables'] ?? [];
        $this->narrative = $payload['narrative'] ?? [];
        $this->generatedReport = $report;
    }

    public function submitForReview(string $reportId): void
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);
            $report->update(['status' => 'pending_review']);
            $this->toast()->success('Report submitted for review')->send();
            $this->refreshSavedReports();
        } catch (\Exception $e) {
            $this->toast()->error('Error: '.$e->getMessage())->send();
        }
    }

    private function refreshSavedReports(): void
    {
        if (!$this->reportKey) {
            $this->savedReports = [];
            return;
        }

        $resolved = ReportRegistry::resolve($this->reportKey, auth()->user());
        if (!$resolved) {
            $this->savedReports = [];
            return;
        }

        $meta = $resolved['meta'] ?? [];
        $this->savedReports = DepartmentReport::query()
            ->where('branch_id', $this->branchId)
            ->where('report_category', $meta['category'] ?? null)
            ->where('report_type', $meta['type'] ?? null)
            ->orderBy('report_date', 'desc')
            ->limit(10)
            ->get();
    }

    private function resolveReport(): ?array
    {
        if (!$this->reportKey) {
            $this->toast()->warning('Please select a report type.')->send();
            return null;
        }

        $resolved = ReportRegistry::resolve($this->reportKey, auth()->user());
        if (!$resolved) {
            $this->toast()->error('Selected report is not available.')->send();
            return null;
        }

        return $resolved;
    }

    private function clearReportData(): void
    {
        $this->reportData = null;
        $this->summaryMetrics = [];
        $this->tablesData = [];
        $this->narrative = [];
        $this->generatedReport = null;
    }

    public function render()
    {
        return view('livewire.branch-dashboard.accounting.reports.unified');
    }
}
