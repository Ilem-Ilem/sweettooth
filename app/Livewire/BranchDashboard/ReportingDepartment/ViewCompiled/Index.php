<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\ViewCompiled;

use App\Models\CompiledReport;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Title, Url};

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('View Compiled Report')]
class Index extends Component
{
    #[Url(keep: true)]
    public $b_id;

    public $compiledReport;
    public $reportId;

    public function mount($id)
    {
        $this->b_id = $this->b_id ?? current_branch_id();
        $this->reportId = $id;
        $this->loadReport();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    public function loadReport()
    {
        $this->compiledReport = CompiledReport::with([
            'branch',
            'compiledBy',
            'approvedBy',
            'mdUser',
            'departmentReports.department',
            'departmentReports.generatedBy'
        ])
            ->where('branch_id', $this->b_id ?? current_branch_id())
            ->findOrFail($this->reportId);
    }

    public function render()
    {
        $productionReports = $this->compiledReport->departmentReports()
            ->where('report_category', 'production')
            ->with('department', 'generatedBy')
            ->get();

        $salesReports = $this->compiledReport->departmentReports()
            ->where('report_category', 'sales')
            ->with('department', 'generatedBy')
            ->get();

        $inventoryReports = $this->compiledReport->departmentReports()
            ->where('report_category', 'inventory')
            ->with('department', 'generatedBy')
            ->get();

        return view('livewire.branch-dashboard.reporting-department.view-compiled.index', [
            'productionReports' => $productionReports,
            'salesReports' => $salesReports,
            'inventoryReports' => $inventoryReports,
        ]);
    }
}
