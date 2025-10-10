<?php

namespace App\Livewire\SuperAdmin\EmployeeModule;

use Livewire\Component;
use App\Models\Employee;

class EmployeeDetails extends Component
{
    public $employee;

    public function mount($employee_number, $id){
        $employee = Employee::with(['department', 'branch'])->
        where('id', '=',  $id)->where('employee_number', '=', $employee_number)->firstOrFail();
        $this->employee = $employee;
    }

    public function render()
    {
        return view('livewire.super-admin.employee-module.employee-details', ['employee'=>$this->employee]);
    }
}
