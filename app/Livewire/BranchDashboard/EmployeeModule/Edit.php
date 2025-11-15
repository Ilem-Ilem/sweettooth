<?php

namespace App\Livewire\BranchDashboard\EmployeeModule;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\{Layout, Url, On};

#[Layout('components.layouts.app.branch-dashboard')]
class Edit extends BaseComponent
{
    use WithFileUploads;

    public $employeeId;
    #[Url(keep: true)]
    public  $b_id;
    // Employee form fields
    public ?string $branch_id = null;
    public ?string $department_id = null;
    public string $employee_number = '';
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $date_of_birth = null;
    public ?string $gender = null;
    public string $nationality = 'Nigerian';
    public ?string $emergency_contact_name = null;
    public ?string $emergency_contact_phone = null;
    public ?string $hire_date = null;
    public ?string $termination_date = null;
    public string $status = 'active';
    public ?string $probation_end_date = null;
    public ?string $shift_preference = null;
    public ?float $salary = null;
    public ?float $hourly_rate = null;
    public ?string $tax_id = null;
    public ?string $bank_account = null;
    public ?string $allergies = null;
    public $profile_photo = null;
    public ?string $existing_photo = null;
    public ?string $last_performance_review_date = null;
    public ?float $performance_rating = null;
    public array $selectedRoles = [];

    // Modal states for creating branch/department
    public bool $showCreateBranchModal = false;
    public bool $showCreateDepartmentModal = false;

    // Branch form fields
    public string $branch_name = '';
    public string $branch_code = '';
    public string $branch_location = '';
    public ?string $branch_phone = null;
    public ?string $branch_email = null;

    // Department form fields
    public string $dept_name = '';
    public ?string $dept_branch_id = null;
    public string $dept_type = 'production';
    public ?string $dept_description = null;

    protected function getModelClass(): string
    {
        return Employee::class;
    }

    public function mount($id)
    {
        // Set b_id from current branch context
        $this->b_id = current_branch_id();

        $employee = Employee::findOrFail($id);

        $this->employeeId = $employee->id;
        $this->branch_id = $employee->branch_id;
        $this->department_id = $employee->department_id;
        $this->employee_number = $employee->employee_number;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->address = $employee->address;
        $this->date_of_birth = $employee->date_of_birth;
        $this->gender = $employee->gender;
        $this->nationality = $employee->nationality ?? 'Nigerian';
        $this->emergency_contact_name = $employee->emergency_contact_name;
        $this->emergency_contact_phone = $employee->emergency_contact_phone;
        $this->hire_date = $employee->hire_date;
        $this->termination_date = $employee->termination_date;
        $this->status = $employee->status;
        $this->probation_end_date = $employee->probation_end_date;
        $this->shift_preference = $employee->shift_preference;
        $this->salary = $employee->salary;
        $this->hourly_rate = $employee->hourly_rate;
        $this->tax_id = $employee->tax_id;
        $this->bank_account = $employee->bank_account;
        $this->allergies = $employee->allergies;
        $this->existing_photo = $employee->profile_photo;
        $this->last_performance_review_date = $employee->last_performance_review_date;
        $this->performance_rating = $employee->performance_rating;
        $this->selectedRoles = $employee->roles->pluck('name')->toArray();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        // Note: We don't reload employee data on branch change when editing
        // as we're editing a specific employee regardless of current branch context
    }

    public function updatedBranchId($value)
    {
        // Reset department when branch changes
        $this->department_id = null;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }



    // Department creation methods
    public function openCreateDepartmentModal()
    {
        $this->resetDepartmentForm();
        $this->showCreateDepartmentModal = true;
    }

    public function closeCreateDepartmentModal()
    {
        $this->showCreateDepartmentModal = false;
        $this->resetDepartmentForm();
    }

    public function resetDepartmentForm()
    {
        $this->dept_name = '';
        $this->dept_branch_id = null;
        $this->dept_type = 'production';
        $this->dept_description = null;
    }

    public function saveDepartment()
    {
        $this->validate([
            'dept_name' => 'required|string|max:255',
            'dept_branch_id' => 'nullable|exists:branches,id',
            'dept_type' => 'required|in:production,sales',
            'dept_description' => 'nullable|string',
        ]);

        $department = Department::create([
            'name' => $this->dept_name,
            'branch_id' => $this->b_id,
            'type' => $this->dept_type,
            'description' => $this->dept_description,
        ]);

        $this->department_id = $department->id;
        $this->toast()->success('Department created successfully!')->send();
        $this->closeCreateDepartmentModal();
    }

    public function saveEmployee()
    {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employeeId,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeId,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'nationality' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated,on_probation,on_leave',
            'probation_end_date' => 'nullable|date',
            'shift_preference' => 'nullable|in:morning,afternoon,night,rotating,flexible',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:100',
            'allergies' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
            'last_performance_review_date' => 'nullable|date',
            'performance_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $employee = Employee::findOrFail($this->employeeId);

        $data = [
            'branch_id' => $this->b_id,
            'department_id' => $this->department_id,
            'employee_number' => $this->employee_number,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'hire_date' => $this->hire_date,
            'termination_date' => $this->termination_date,
            'status' => $this->status,
            'probation_end_date' => $this->probation_end_date,
            'shift_preference' => $this->shift_preference,
            'salary' => $this->salary,
            'hourly_rate' => $this->hourly_rate,
            'tax_id' => $this->tax_id,
            'bank_account' => $this->bank_account,
            'allergies' => $this->allergies,
            'last_performance_review_date' => $this->last_performance_review_date,
            'performance_rating' => $this->performance_rating,
        ];

        if ($this->profile_photo) {
            $data['profile_photo'] = $this->profile_photo->store('employee-photos', 'public');
        }

        $employee->update($data);

        // Sync roles
        if (!empty($this->selectedRoles)) {
            $employee->syncRoles($this->selectedRoles);
        } else {
            $employee->syncRoles([]);
        }

        $this->toast()->success('Employee updated successfully!')->send();
        return redirect()->route('branch-dashboard.employees.index',  ['b_id'=>$this->b_id]);
    }

    public function render()
    {
        $branches = Branch::where('is_active', true)->get();
        $departments = $this->branch_id
            ? Department::where('branch_id', $this->branch_id)->orWhereNull('branch_id')->get()
            : Department::all();
        $roles = Role::where('guard_name', 'employees')->get();

        return view('livewire.branch-dashboard.employee-module.edit', [
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }
}
