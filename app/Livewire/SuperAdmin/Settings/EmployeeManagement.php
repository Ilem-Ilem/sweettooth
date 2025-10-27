<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalEmployeeManagement;

class EmployeeManagement extends Component
{
    public $rolesCreate = true;
    public $rolesEdit = true;
    public $permissionsPos = true;
    public $permissionsInventory = true;
    public $permissionsReports = true;
    public $staffAdd = true;
    public $staffEdit = true;
    public $staffDelete = true;
    public $departmentSales = true;
    public $departmentWarehouse = true;
    public $shiftScheduling = true;
    public $pinLogin = true;

    public function mount()
    {
        $settings = GlobalEmployeeManagement::first();
        
        if ($settings) {
            $roles = $settings->roles ?? [];
            $this->rolesCreate = in_array('create', $roles);
            $this->rolesEdit = in_array('edit', $roles);
            
            $permissions = $settings->permissions ?? [];
            $this->permissionsPos = in_array('pos', $permissions);
            $this->permissionsInventory = in_array('inventory', $permissions);
            $this->permissionsReports = in_array('reports', $permissions);
            
            $staff = $settings->staff_profiles ?? [];
            $this->staffAdd = in_array('add', $staff);
            $this->staffEdit = in_array('edit', $staff);
            $this->staffDelete = in_array('delete', $staff);
            
            $departments = $settings->departments ?? [];
            $this->departmentSales = in_array('sales', $departments);
            $this->departmentWarehouse = in_array('warehouse', $departments);
            
            $this->shiftScheduling = $settings->shift_scheduling === 'enabled';
            $this->pinLogin = $settings->pin_login === 'enabled';
        }
    }

    public function save()
    {
        $roles = [];
        if ($this->rolesCreate) $roles[] = 'create';
        if ($this->rolesEdit) $roles[] = 'edit';
        
        $permissions = [];
        if ($this->permissionsPos) $permissions[] = 'pos';
        if ($this->permissionsInventory) $permissions[] = 'inventory';
        if ($this->permissionsReports) $permissions[] = 'reports';
        
        $staff = [];
        if ($this->staffAdd) $staff[] = 'add';
        if ($this->staffEdit) $staff[] = 'edit';
        if ($this->staffDelete) $staff[] = 'delete';
        
        $departments = [];
        if ($this->departmentSales) $departments[] = 'sales';
        if ($this->departmentWarehouse) $departments[] = 'warehouse';

        GlobalEmployeeManagement::updateOrCreate(
            ['id' => 1],
            [
                'roles' => $roles,
                'permissions' => $permissions,
                'staff_profiles' => $staff,
                'departments' => $departments,
                'shift_scheduling' => $this->shiftScheduling ? 'enabled' : 'disabled',
                'pin_login' => $this->pinLogin ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'Employee Management settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.employee-management');
    }
}
