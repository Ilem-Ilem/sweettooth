<?php

namespace App\Livewire\SuperAdmin\EmployeeModule;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class CreateEmployee extends BaseComponent
{
    use WithFileUploads;

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
    public string $position = '';
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

    public function mount()
    {
        $this->hire_date = date('Y-m-d');
    }

    public function updatedBranchId($value)
    {
        // Reset department when branch changes
        $this->department_id = null;

        // Generate employee number based on selected branch
        if ($value) {
            $this->employee_number = $this->generateEmployeeNumber($value);
        }
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    // Branch creation methods
    public function openCreateBranchModal()
    {
        $this->resetBranchForm();
        $this->showCreateBranchModal = true;
    }

    public function closeCreateBranchModal()
    {
        $this->showCreateBranchModal = false;
        $this->resetBranchForm();
    }

    public function resetBranchForm()
    {
        $this->branch_name = '';
        $this->branch_code = '';
        $this->branch_location = '';
        $this->branch_phone = null;
        $this->branch_email = null;
    }

    public function saveBranch()
    {
        $this->validate([
            'branch_name' => 'required|string|max:255|unique:branches,name',
            'branch_code' => 'required|string|max:50|unique:branches,code',
            'branch_location' => 'required|string|max:255',
            'branch_phone' => 'nullable|string|max:50',
            'branch_email' => 'nullable|email|unique:branches,email',
        ]);

        $branch = Branch::create([
            'name' => $this->branch_name,
            'code' => $this->branch_code,
            'location' => $this->branch_location,
            'phone' => $this->branch_phone,
            'email' => $this->branch_email,
            'is_active' => true,
        ]);

        $this->branch_id = $branch->id;
        $this->toast()->success('Branch created successfully!')->send();
        $this->closeCreateBranchModal();
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
            'branch_id' => $this->dept_branch_id,
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
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'nationality' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
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

        $data = [
            'branch_id' => $this->branch_id,
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
            'position' => $this->position,
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
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];

        if ($this->profile_photo) {
            $data['profile_photo'] = $this->profile_photo->store('employee-photos', 'public');
        }

        $employee = Employee::create($data);

        // Assign roles to employee
        if (!empty($this->selectedRoles)) {
            $employee->syncRoles($this->selectedRoles);
        }

        $this->toast()->success('Employee created successfully!')->send();
        return redirect()->route('super-admin.employee.index');
    }

    private function generateEmployeeNumber($branchId)
    {
        $branch = Branch::find($branchId);
        if (!$branch) {
            return '';
        }

        // Get branch code without hyphens (e.g., LHO-001 becomes LHO001)
        $branchCode = str_replace('-', '', strtoupper($branch->code));

        // Get the last employee for this branch
        $lastEmployee = Employee::where('branch_id', $branchId)
            ->where('employee_number', 'like', 'EMP-' . $branchCode . '-%')
            ->orderBy('employee_number', 'desc')
            ->first();

        if ($lastEmployee) {
            // Extract the last 4 digits from the employee number
            $lastNumber = intval(substr($lastEmployee->employee_number, -4));
            return 'EMP-' . $branchCode . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return 'EMP-' . $branchCode . '-0001';
    }

    public function render()
    {
        $branches = Branch::where('is_active', true)->get();
        $departments = $this->branch_id
            ? Department::where('branch_id', $this->branch_id)->orWhereNull('branch_id')->get()
            : Department::all();
        $roles = Role::where('guard_name', 'employees')->get();

        return view('livewire.super-admin.employee-module.create-employee', [
            'branches' => $branches,
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }
}
