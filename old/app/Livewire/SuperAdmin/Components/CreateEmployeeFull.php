<?php
namespace App\Livewire\SuperAdmin\Components;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEmployeeFull extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $isEditing = false;
    public $employeeId = null;

    // Employee fields
    public $employee_number, $name, $email, $phone;
    public $address, $date_of_birth, $gender, $nationality;
    public $emergency_contact_name, $emergency_contact_phone;
    public $position, $hire_date, $status = 'active';
    public $probation_end_date, $shift_preference;
    public $salary, $hourly_rate, $tax_id, $bank_account;
    public $allergies, $profile_photo;
    public $branch_id, $department_id;

    protected $listeners = ['open-create-employee-modal' => 'openModal'];

    public function mount()
    {
        $this->generateEmployeeNumber();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->isOpen = true;
        $this->generateEmployeeNumber();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function openForEdit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $this->resetForm();
        $this->isEditing = true;
        $this->isOpen = true;
        $this->employeeId = $employee->id;

        $this->employee_number         = $employee->employee_number;
        $this->name                    = $employee->name;
        $this->email                   = $employee->email;
        $this->phone                   = $employee->phone;
        $this->address                 = $employee->address;
        $this->date_of_birth           = $employee->date_of_birth;
        $this->gender                  = $employee->gender;
        $this->nationality             = $employee->nationality;
        $this->emergency_contact_name  = $employee->emergency_contact_name;
        $this->emergency_contact_phone = $employee->emergency_contact_phone;
        $this->position                = $employee->position;
        $this->hire_date               = $employee->hire_date;
        $this->status                  = $employee->status;
        $this->probation_end_date      = $employee->probation_end_date;
        $this->shift_preference        = $employee->shift_preference;
        $this->salary                  = $employee->salary;
        $this->hourly_rate             = $employee->hourly_rate;
        $this->tax_id                  = $employee->tax_id;
        $this->bank_account            = $employee->bank_account;
        $this->allergies               = $employee->allergies;
        $this->branch_id               = $employee->branch_id;
        $this->department_id           = $employee->department_id;
    }

    private function generateEmployeeNumber()
    {
        $lastEmployee          = Employee::latest('created_at')->first();
        $lastNumber            = $lastEmployee ? intval(substr($lastEmployee->employee_number, 3)) : 0;
        $this->employee_number = 'EMP' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function createEmployee()
    {
        $this->validate([
            'employee_number'         => 'required|string|max:50|unique:employees,employee_number',
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|max:255|unique:employees,email',
            'phone'                   => 'nullable|string|max:50',
            'address'                 => 'nullable|string',
            'date_of_birth'           => 'nullable|date|before:today',
            'gender'                  => 'nullable|in:male,female,other,prefer_not_to_say',
            'nationality'             => 'nullable|string|max:100',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'position'                => 'required|string|max:255',
            'hire_date'               => 'required|date',
            'status'                  => 'required|in:active,inactive,terminated,on_probation,on_leave',
            'probation_end_date'      => 'nullable|date|after:hire_date',
            'shift_preference'        => 'nullable|in:morning,afternoon,night,rotating,flexible',
            'salary'                  => 'nullable|numeric|min:0',
            'hourly_rate'             => 'nullable|numeric|min:0',
            'tax_id'                  => 'nullable|string|max:50',
            'bank_account'            => 'nullable|string|max:100',
            'allergies'               => 'nullable|string',
            'profile_photo'           => 'nullable|image|max:2048',
            'branch_id'               => 'nullable|exists:branches,id',
            'department_id'           => 'nullable|exists:departments,id',
        ]);

        $data = [
            'id'                      => Str::uuid(),
            'employee_number'         => $this->employee_number,
            'name'                    => $this->name,
            'email'                   => $this->email,
            'phone'                   => $this->phone,
            'address'                 => $this->address,
            'date_of_birth'           => $this->date_of_birth,
            'gender'                  => $this->gender,
            'nationality'             => $this->nationality,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'position'                => $this->position,
            'hire_date'               => $this->hire_date,
            'status'                  => $this->status,
            'probation_end_date'      => $this->probation_end_date,
            'shift_preference'        => $this->shift_preference,
            'salary'                  => $this->salary,
            'hourly_rate'             => $this->hourly_rate,
            'tax_id'                  => $this->tax_id,
            'bank_account'            => $this->bank_account,
            'allergies'               => $this->allergies,
            'branch_id'               => $this->branch_id,
            'department_id'           => $this->department_id,
        ];

        // Handle profile photo upload
        if ($this->profile_photo) {
            $data['profile_photo'] = $this->profile_photo->store('employees', 'public');
        }

        Employee::create($data);

        $this->dispatch('employee-created', 'Employee created successfully!');
        $this->dispatch('refresh');
        $this->closeModal();
    }

    public function updateEmployee()
    {
        $this->validate([
            'employee_number'         => 'required|string|max:50|unique:employees,employee_number,' . $this->employeeId,
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|max:255|unique:employees,email,' . $this->employeeId,
            'phone'                   => 'nullable|string|max:50',
            'address'                 => 'nullable|string',
            'date_of_birth'           => 'nullable|date',
            'gender'                  => 'nullable|in:male,female,other,prefer_not_to_say',
            'nationality'             => 'nullable|string|max:100',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'position'                => 'required|string|max:255',
            'hire_date'               => 'required|date',
            'status'                  => 'required|in:active,inactive,terminated,on_probation,on_leave',
            'probation_end_date'      => 'nullable|date',
            'shift_preference'        => 'nullable|in:morning,afternoon,night,rotating,flexible',
            'salary'                  => 'nullable|numeric|min:0',
            'hourly_rate'             => 'nullable|numeric|min:0',
            'tax_id'                  => 'nullable|string|max:50',
            'bank_account'            => 'nullable|string|max:100',
            'allergies'               => 'nullable|string',
            'profile_photo'           => 'nullable|image|max:2048',
            'branch_id'               => 'nullable|exists:branches,id',
            'department_id'           => 'nullable|exists:departments,id',
        ]);

        $employee = Employee::findOrFail($this->employeeId);

        $data = [
            'employee_number'         => $this->employee_number,
            'name'                    => $this->name,
            'email'                   => $this->email,
            'phone'                   => $this->phone,
            'address'                 => $this->address,
            'date_of_birth'           => $this->date_of_birth,
            'gender'                  => $this->gender,
            'nationality'             => $this->nationality,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'position'                => $this->position,
            'hire_date'               => $this->hire_date,
            'status'                  => $this->status,
            'probation_end_date'      => $this->probation_end_date,
            'shift_preference'        => $this->shift_preference,
            'salary'                  => $this->salary,
            'hourly_rate'             => $this->hourly_rate,
            'tax_id'                  => $this->tax_id,
            'bank_account'            => $this->bank_account,
            'allergies'               => $this->allergies,
            'branch_id'               => $this->branch_id,
            'department_id'           => $this->department_id,
        ];

        if ($this->profile_photo) {
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $data['profile_photo'] = $this->profile_photo->store('employees', 'public');
        }

        $employee->update($data);

        $this->dispatch('employee-updated', 'Employee updated successfully!');
        $this->dispatch('refresh');
        $this->closeModal();
    }

    private function resetForm()
    {
        $this->employeeId              = null;
        $this->isEditing               = false;
        $this->employee_number         = null;
        $this->name                    = null;
        $this->email                   = null;
        $this->phone                   = null;
        $this->address                 = null;
        $this->date_of_birth           = null;
        $this->gender                  = null;
        $this->nationality             = null;
        $this->emergency_contact_name  = null;
        $this->emergency_contact_phone = null;
        $this->position                = null;
        $this->hire_date               = null;
        $this->status                  = 'active';
        $this->probation_end_date      = null;
        $this->shift_preference        = null;
        $this->salary                  = null;
        $this->hourly_rate             = null;
        $this->tax_id                  = null;
        $this->bank_account            = null;
        $this->allergies               = null;
        $this->profile_photo           = null;
        $this->branch_id               = null;
        $this->department_id           = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.super-admin.components.create-employee-full', [
            'branches'    => Branch::where('is_active', true)->get(),
            'departments' => Department::all(),
        ]);
    }
}
