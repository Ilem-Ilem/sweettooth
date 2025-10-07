<?php

namespace App\Livewire\SuperAdmin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Support\Str;

class CreateEmployee extends Component
{
    public $name,
           $email,
           $phone,
           $position,
           $hire_date,
           $date_of_birth,
           $gender,
           $nationality,
           $address;

    protected $rules = [
        'name'            => 'required|string|max:255',
        'email'           => 'required|email|unique:employees,email',
        'phone'           => 'nullable|string|max:50',
        'position'        => 'required|string|max:255',
        'hire_date'       => 'required|date',
        'date_of_birth'   => 'nullable|date',
        'gender'          => 'nullable|in:male,female,other,prefer_not_to_say',
        'nationality'     => 'nullable|string|max:100',
        'address'         => 'nullable|string',
    ];

    protected $messages = [
        'employee_number.unique'   => 'This employee number is already in use.',
        'employee_number.max'      => 'Employee number cannot exceed 50 characters.',
        'name.required'            => 'Please enter the full name.',
        'name.max'                 => 'Full name cannot exceed 255 characters.',
        'email.required'           => 'Please enter the email address.',
        'email.email'              => 'Please enter a valid email address.',
        'email.unique'             => 'This email address is already in use.',
        'phone.max'                => 'Phone number cannot exceed 50 characters.',
        'position.required'        => 'Please enter the position.',
        'position.max'             => 'Position cannot exceed 255 characters.',
        'hire_date.required'       => 'Please enter the hire date.',
        'hire_date.date'           => 'Please enter a valid hire date.',
        'date_of_birth.date'       => 'Please enter a valid date of birth.',
        'gender.in'                => 'Please select a valid gender option.',
        'nationality.max'          => 'Nationality cannot exceed 100 characters.',
    ];

    public function createEmployee()
    {
        $this->validate();

        Employee::create([
            'id'              => Str::uuid(),
            'employee_number' => 0,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'position'        => $this->position,
            'hire_date'       => $this->hire_date,
            'date_of_birth'   => $this->date_of_birth,
            'gender'          => $this->gender,
            'nationality'     => $this->nationality,
            'address'         => $this->address,
        ]);

        session()->flash('message', '✅ Employee created successfully.');

        $this->resetForm();
        $this->dispatch("Employee-created");
    }

    private function resetForm(): void
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'position',
            'hire_date',
            'date_of_birth',
            'gender',
            'nationality',
            'address',
        ]);
    }

    public function render()
    {
        return view('livewire.super-admin.components.create-employee');
    }
}
