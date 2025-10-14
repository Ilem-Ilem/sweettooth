<?php

namespace App\Livewire\SuperAdmin\Components;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Validation\Rule;

class CreateBranch extends Component
{
    public $name;
    public $code;
    public $location;
    public $country;
    public $state;
    public $city;
    public $postal_code;
    public $timezone;
    public $phone;
    public $email;
    public $description;
    public $manager_user_id;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:50|unique:branches,code',
        'location' => 'required|string|max:255',
        'country' => 'required|string|max:100',
        'state' => 'required|string|max:100',
        'city' => 'required|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'timezone' => 'nullable|string|max:50',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255|unique:branches,email',
        'description' => 'nullable|string',
        'manager_user_id' => 'nullable|exists:employees,id',
        'is_active' => 'boolean',
    ];
    protected $messages = [
        'first_name.required' => 'Please enter the first name.',
        'first_name.string' => 'The first name must be a valid text.',
        'first_name.max' => 'The first name may not be longer than 50 characters.',

        'last_name.required' => 'Please enter the last name.',
        'last_name.string' => 'The last name must be a valid text.',
        'last_name.max' => 'The last name may not be longer than 50 characters.',

        'email.required' => 'We need your email address.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already registered.',

        'phone.required' => 'Please provide a phone number.',
        'phone.string' => 'The phone number must be a valid string.',
        'phone.max' => 'The phone number may not be longer than 15 characters.',

        'address.string' => 'The address must be a valid text.',
        'address.max' => 'The address may not be longer than 255 characters.',

        'dob.required' => 'Please select a date of birth.',
        'dob.date' => 'The date of birth must be a valid date.',

        'gender.required' => 'Please select a gender.',
        'gender.in' => 'The selected gender is not valid.',

        'hire_date.required' => 'Please select a hire date.',
        'hire_date.date' => 'The hire date must be a valid date.',

        'job_title.required' => 'Please enter the job title.',
        'job_title.string' => 'The job title must be text.',
        'job_title.max' => 'The job title may not be longer than 100 characters.',
    ];

    public function createBranch()
    {
        $this->validate();

        $branch = Branch::create([
            'name' => $this->name,
            'code' => $this->code,
            'location' => $this->location,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'timezone' => $this->timezone,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'manager_user_id' => $this->manager_user_id,
            'is_active' => $this->is_active,
        ]);

        // ✅ Update manager’s branch_id if needed
        if ($this->manager_user_id) {
            $employee = Employee::find($this->manager_user_id);
            if ($employee && (is_null($employee->branch_id) || $employee->branch_id !== $branch->id)) {
                $employee->branch_id = $branch->id;
                $employee->save();
            }
        }

        $this->resetInputFields();
        $this->dispatch('close-form');
        $this->dispatch('branchCreated');
        $this->dispatch('refresh');
    }

    private function resetInputFields()
    {
        $this->name = null;
        $this->code = null;
        $this->location = null;
        $this->country = null;
        $this->state = null;
        $this->city = null;
        $this->postal_code = null;
        $this->timezone = null;
        $this->phone = null;
        $this->email = null;
        $this->description = null;
        $this->manager_user_id = null;
        $this->is_active = true;
    }

    public function render()
    {
        $manager = Employee::all();
        return view('livewire.super-admin.components.create-branch', [
            'employees' => $manager,
        ]);
    }
}
