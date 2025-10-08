<?php

namespace App\Livewire\SuperAdmin\Roles\Comoponent;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRole extends Component
{

    // Role form
    public string $roleName = '';
    public string $roleGuard = 'web';
    public array $selectedPermissions = [];
    public bool $isEditing = false;
public bool $showRoleModal = false;
        public function openRoleModal()
    {
        $this->isEditing = false;
        $this->resetRoleForm();
        $this->showRoleModal = true;
    }

    public function editRole($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->isEditing = true;
        $this->selectedRoleId = $roleId;
        $this->roleName = $role->name;
        $this->roleGuard = $role->guard_name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->showRoleModal = true;
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->resetRoleForm();
    }

    public function resetRoleForm()
    {
        $this->roleName = '';
        $this->roleGuard = 'web';
        $this->selectedPermissions = [];
        $this->selectedRoleId = null;
        $this->isEditing = false;
    }

    public function saveRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255',
            'roleGuard' => 'required|string',
        ]);

        if ($this->isEditing && $this->selectedRoleId) {
            $role = Role::findOrFail($this->selectedRoleId);
            $role->update([
                'name' => $this->roleName,
                'guard_name' => $this->roleGuard,
            ]);
            $message = 'Role updated successfully!';
        } else {
            $role = Role::create([
                'name' => $this->roleName,
                'guard_name' => $this->roleGuard,
            ]);
            $message = 'Role created successfully!';
        }

        // Sync permissions - get Permission models by IDs
        $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
        $role->syncPermissions($permissions);

        $this->toast()->success($message)->send();
        $this->closeRoleModal();
    }

    public function render()
    {
        return view('livewire.super-admin.roles.comoponent.create-role');
    }
}
