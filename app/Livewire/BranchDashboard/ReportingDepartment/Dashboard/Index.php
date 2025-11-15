<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\Dashboard;

use App\Models\DepartmentReport;
use App\Models\CompiledReport;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Title, Url};

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Reporting Department Dashboard')]
class Index extends Component
{
    public $stats = [];

    #[Url(keep: true)]
    public $b_id;

    public function mount()
    {
        $this->b_id = $this->b_id ?? current_branch_id();
        $this->loadStats();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->loadStats();
    }

    public function loadStats()
    {
        // Support both super admin (b_id) and employee (current_branch_id)
        $branchId = $this->b_id ?? current_branch_id();

        $this->stats = [
            'pending_review' => DepartmentReport::forBranch($branchId)
                ->where('status', 'pending_review')
                ->count(),

            'reviewed' => DepartmentReport::forBranch($branchId)
                ->where('status', 'reviewed')
                ->count(),

            'compiled_today' => CompiledReport::forBranch($branchId)
                ->whereDate('compilation_date', Carbon::today())
                ->count(),

            'sent_to_md' => CompiledReport::forBranch($branchId)
                ->where('status', 'sent_to_md')
                ->count(),

            'production_reports' => DepartmentReport::forBranch($branchId)
                ->byCategory('production')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count(),

            'sales_reports' => DepartmentReport::forBranch($branchId)
                ->byCategory('sales')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count(),

            'inventory_reports' => DepartmentReport::forBranch($branchId)
                ->byCategory('inventory')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.branch-dashboard.reporting-department.dashboard.index');
    }
}
