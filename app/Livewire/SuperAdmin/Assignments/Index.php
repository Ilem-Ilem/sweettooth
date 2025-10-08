<?php

namespace App\Livewire\SuperAdmin\Assignments;

use App\Livewire\BaseComponent;
use App\Models\PositionAssignment;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Department;

class Index extends BaseComponent
{
    // Filter for viewing assignments
    public ?string $filterBranch = null;
    public ?string $filterDepartment = null;

    // Form fields for creating assignment
    public ?string $selected_branch_id = null;
    public ?int $selected_department_id = null;
    public ?string $selected_employee_id = null;
    public string $assignment_type = 'department_head';
    public ?string $start_date = null;
    public bool $is_active = true;
    public string $notes = '';

    // Data collections
    public $branches;
    public $departments;
    public $employees;
    public $managerPosition = null;

    protected function getModelClass(): string
    {
        return PositionAssignment::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function mount()
    {
        $this->branches = Branch::all();
        $this->departments = collect();
        $this->employees = collect();
        $this->start_date = now()->format('Y-m-d');

        // Get or create a generic Manager position
        $this->managerPosition = Position::firstOrCreate(
            ['name' => 'Department Manager'],
            [
                'level' => 2,
                'description' => 'Manages a specific department',
                'role_id' => \Spatie\Permission\Models\Role::where('name', 'Department Manager')
                    ->where('guard_name', 'employees')
                    ->first()?->id
            ]
        );
    }

    public function updatedSelectedBranchId($value)
    {
        // Reset dependent fields
        $this->selected_department_id = null;
        $this->selected_employee_id = null;
        $this->employees = collect();

        if ($value) {
            // Load departments for this branch
            $this->departments = Department::where('branch_id', $value)->get();
        } else {
            $this->departments = collect();
        }
    }

    public function updatedSelectedDepartmentId($value)
    {
        // Reset dependent fields
        $this->selected_employee_id = null;

        if ($value) {
            // Load employees for this department
            $this->employees = Employee::where('department_id', $value)
                ->where('branch_id', $this->selected_branch_id)
                ->get();
        } else {
            $this->employees = collect();
        }
    }

    public function updatedFilterBranch($value)
    {
        $this->filterDepartment = null;
        $this->resetPage();
    }

    protected function getFilteredQuery()
    {
        return PositionAssignment::query()
            ->with(['employee', 'position', 'branch', 'department'])
            ->when($this->filterBranch, function ($query) {
                $query->where('branch_id', $this->filterBranch);
            })
            ->when($this->filterDepartment, function ($query) {
                $query->where('department_id', $this->filterDepartment);
            })
            ->orderBy('created_at', 'desc');
    }

    public function assignManager()
    {
        $this->validate([
            'selected_branch_id' => 'required|exists:branches,id',
            'selected_department_id' => 'required|exists:departments,id',
            'selected_employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Check if this employee already has an active assignment for this department
        $existingAssignment = PositionAssignment::where('employee_id', $this->selected_employee_id)
            ->where('department_id', $this->selected_department_id)
            ->where('assignment_type', 'department_head')
            ->where('is_active', true)
            ->first();

        if ($existingAssignment) {
            $this->toast()->error('This employee is already assigned as manager of this department!')->send();
            return;
        }

        // Create the assignment
        PositionAssignment::create([
            'employee_id' => $this->selected_employee_id,
            'position_id' => $this->managerPosition->id,
            'branch_id' => $this->selected_branch_id,
            'department_id' => $this->selected_department_id,
            'assignment_type' => 'department_head',
            'start_date' => $this->start_date,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ]);

        $this->toast()->success('Manager assigned successfully!')->send();
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->selected_branch_id = null;
        $this->selected_department_id = null;
        $this->selected_employee_id = null;
        $this->start_date = now()->format('Y-m-d');
        $this->is_active = true;
        $this->notes = '';
        $this->departments = collect();
        $this->employees = collect();
    }

    public function removeAssignment($id)
    {
        $assignment = PositionAssignment::findOrFail($id);

        $this->dialog()
            ->question('Remove Assignment', 'Are you sure you want to remove this manager assignment?')
            ->confirm('Confirm', 'confirmedRemove', ['id' => $id])
            ->cancel('Cancel')
            ->send();
    }

    public function confirmedRemove($data)
    {
        $assignment = PositionAssignment::findOrFail($data['id']);
        $assignment->delete();

        $this->dialog()->success('Success', 'Manager assignment removed successfully!')->send();
    }

    public function deactivateAssignment($id)
    {
        $assignment = PositionAssignment::findOrFail($id);
        $assignment->update([
            'is_active' => false,
            'end_date' => now(),
        ]);

        $this->toast()->success('Assignment deactivated successfully!')->send();
    }

    public function activateAssignment($id)
    {
        $assignment = PositionAssignment::findOrFail($id);
        $assignment->update([
            'is_active' => true,
            'end_date' => null,
        ]);

        $this->toast()->success('Assignment activated successfully!')->send();
    }

    public function render()
    {
        $assignments = $this->getFilteredQuery()->paginate(10);
        $filterBranches = Branch::all();
        $filterDepartments = $this->filterBranch
            ? Department::where('branch_id', $this->filterBranch)->get()
            : collect();

        return view('livewire.super-admin.assignments.index', [
            'assignments' => $assignments,
            'filterBranches' => $filterBranches,
            'filterDepartments' => $filterDepartments,
        ]);
    }
}
