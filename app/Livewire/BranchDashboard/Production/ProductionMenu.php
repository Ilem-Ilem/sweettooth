<?php

namespace App\Livewire\BranchDashboard\Production;

use Livewire\Component;
use App\Models\Department;
use App\Models\DepartmentPage;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.branch-dashboard')]
class ProductionMenu extends Component
{
    public $selectedDepartment = null;
    public $departments = [];
    public $pages = [];

    public function mount()
    {
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $employee = Auth::guard('employees')->user();
        $branchId = $employee->branch_id;

        // Get ALL production departments (either for this branch or global departments with NULL branch_id)
        $allDepts = Department::where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            })
            ->with('category')
            ->with(['pages' => function($q) {
                $q->where('is_active', true)->orderBy('order')->orderBy('name');
            }])
            ->get();

        // Filter to only Production category departments
        $this->departments = collect();
        foreach ($allDepts as $dept) {
            if ($dept->category && trim($dept->category->name) === 'Production') {
                $this->departments->push($dept);
            }
        }

        // Auto-select first department if none selected
        if (!$this->selectedDepartment && $this->departments->isNotEmpty()) {
            $this->selectedDepartment = $this->departments->first()->id;
            $this->loadPages();
        }
    }

    public function selectDepartment($departmentId)
    {
        $this->selectedDepartment = $departmentId;
        $this->loadPages();
    }

    public function loadPages()
    {
        if ($this->selectedDepartment) {
            $this->pages = DepartmentPage::where('department_id', $this->selectedDepartment)
                ->active()
                ->ordered()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.production-menu');
    }
}
