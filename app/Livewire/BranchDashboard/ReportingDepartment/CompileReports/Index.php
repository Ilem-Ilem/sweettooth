<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\CompileReports;

use App\Models\DepartmentReport;
use App\Services\Reports\ReportCompilationService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Compile Reports')]
class Index extends Component
{
    use Interactions, WithPagination;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $selectedReports = [];

    public $compilationTitle = '';

    public $compilationDescription = '';

    public $periodFrom;

    public $periodTo;

    public $filterCategory = 'all';

    public $filterStatus = 'reviewed';

    public $showCompileModal = false;

    public $isCompiling = false;

    public function mount()
    {
        $this->b_id = $this->b_id ?? current_branch_id();
        $this->periodFrom = Carbon::now()->startOfMonth()->toDateString();
        $this->periodTo = Carbon::now()->endOfMonth()->toDateString();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    public function toggleReport($reportId)
    {
        if (in_array($reportId, $this->selectedReports)) {
            $this->selectedReports = array_diff($this->selectedReports, [$reportId]);
        } else {
            $this->selectedReports[] = $reportId;
        }
    }

    public function selectAll()
    {
        $reports = $this->getAvailableReports();
        $this->selectedReports = $reports->pluck('id')->toArray();
        $this->toast()->success('All reports selected')->send();
    }

    public function deselectAll()
    {
        $this->selectedReports = [];
        $this->toast()->info('All reports deselected')->send();
    }

    public function toggleSelectAll()
    {
        if (empty($this->selectedReports)) {
            $this->selectAll();
        } else {
            $this->deselectAll();
        }
    }

    public function openCompileModal()
    {
        if (empty($this->selectedReports)) {
            $this->toast()->warning('Please select at least one report to compile')->send();

            return;
        }

        // Auto-generate title based on selected reports
        $reportCount = count($this->selectedReports);
        $this->compilationTitle = 'Compiled Report - '.Carbon::now()->format('F Y')." ({$reportCount} reports)";
        $this->compilationDescription = 'Comprehensive report compilation for '.Carbon::now()->format('F Y');

        $this->showCompileModal = true;
    }

    public function compileReports()
    {
        $this->validate([
            'compilationTitle' => 'required|string|max:255',
            'periodFrom' => 'required|date',
            'periodTo' => 'required|date|after_or_equal:periodFrom',
        ]);

        if (empty($this->selectedReports)) {
            $this->toast()->error('Please select reports to compile')->send();

            return;
        }

        $this->isCompiling = true;

        try {
            $compilationService = new ReportCompilationService;

            $compiledReport = $compilationService->compile(
                $this->selectedReports,
                $this->compilationTitle,
                $this->compilationDescription,
                $this->b_id ?? current_branch_id(),
                auth()->id(),
                $this->periodFrom,
                $this->periodTo
            );

            $this->toast()->success('Reports compiled successfully!')->send();
            $this->showCompileModal = false;
            $this->selectedReports = [];

            // Redirect to view compiled report with b_id parameter
            return redirect()->route('branch-dashboard.reporting.compiled.view', [
                'id' => $compiledReport->id,
                'b_id' => $this->b_id,
            ]);

        } catch (\Exception $e) {
            $this->toast()->error('Error compiling reports: '.$e->getMessage())->send();
        } finally {
            $this->isCompiling = false;
        }
    }

    public function getAvailableReports()
    {
        $query = DepartmentReport::query()
            ->with(['department', 'generatedBy'])
            ->forBranch($this->b_id ?? current_branch_id())
            ->whereBetween('report_date', [$this->periodFrom, $this->periodTo]);

        if ($this->filterCategory !== 'all') {
            $query->byCategory($this->filterCategory);
        }

        if ($this->filterStatus !== 'all') {
            $query->byStatus($this->filterStatus);
        }

        return $query->orderBy('report_date', 'desc')->get();
    }

    public function render()
    {
        $availableReports = $this->getAvailableReports();

        $categoryCounts = [
            'production' => $availableReports->where('report_category', 'production')->count(),
            'sales' => $availableReports->where('report_category', 'sales')->count(),
            'inventory' => $availableReports->where('report_category', 'inventory')->count(),
        ];

        return view('livewire.branch-dashboard.reporting-department.compile-reports.index', [
            'availableReports' => $availableReports,
            'categoryCounts' => $categoryCounts,
        ]);
    }
}
