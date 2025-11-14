<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\Dashboard;

use App\Models\DepartmentReport;
use App\Models\CompiledReport;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Reporting Department Dashboard')]
class Index extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $branchId = auth('employees')->user()->branch_id;

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
