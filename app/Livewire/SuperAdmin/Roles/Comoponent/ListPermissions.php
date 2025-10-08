<?php

namespace App\Livewire\SuperAdmin\Roles\Comoponent;

use Livewire\Component;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ListPermissions extends Component
{
    public bool $showPermissionsModal = false;

    public ?int $selectedRoleId = null;

    public array $rolePermissions = [];

    public function viewPermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->selectedRoleId = $roleId;
        $this->rolePermissions = $role->permissions->toArray();
        $this->showPermissionsModal = true;
    }



    public function closePermissionsModal()
    {
        $this->showPermissionsModal = false;
        $this->selectedRoleId = null;
        $this->rolePermissions = [];
    }


    public function render()
    {
        return view('livewire.super-admin.roles.comoponent.list-permissions');
    }
}
