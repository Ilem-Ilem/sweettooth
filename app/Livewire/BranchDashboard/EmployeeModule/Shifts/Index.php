<?php

namespace App\Livewire\BranchDashboard\EmployeeModule\Shifts;


use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\{Layout, Url};
use App\Models\EmployeeShift;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    protected function getModelClass(): string
    {
        return EmployeeShift::class;
    }
    
    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }
    
    public function render()
    {
        return view('livewire.branch-dashboard.employee-module.shifts.index');
    }
}
