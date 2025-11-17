<?php

namespace App\Livewire\BranchDashboard\EmployeeModule\RolePermission;

use App\Livewire\BaseComponent;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use TallStackUi\Traits\Interactions;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use Interactions, WithPagination;
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Modal states
    public bool $showPermissionsModal = false;
    public bool $showRoleModal = false;
    public bool $showCreatePermissionModal = false;
    public bool $showStandalonePermissionModal = false;
    public ?int $selectedRoleId = null;
    public array $rolePermissions = [];

    // Role form
    public string $roleName = '';
    public string $roleGuard = 'web';
    public array $selectedPermissions = [];
    public bool $isEditing = false;

    // Permission form
    public string $permissionName = '';
    public string $permissionGuard = 'web';

    // Standalone permission form
    public string $standalonePermissionName = '';
    public string $standalonePermissionGuard = 'employee';

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    protected function getModelClass(): string
    {
        return Role::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('guard_name', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->advancedSearch = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->resetPage();
    }

    // Export methods
    public function exportExcel()
    {
        $roles = $this->getFilteredQuery()->get();

        // Create CSV content
        $csv = "ID,Name,Guard,Created At\n";
        foreach ($roles as $role) {
            $csv .= "{$role->id},{$role->name},{$role->guard_name},{$role->created_at}\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'roles-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    // Modal methods
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

        // Sync permissions - only permissions with matching guard
        $permissions = Permission::whereIn('id', $this->selectedPermissions)
            ->where('guard_name', $this->roleGuard)
            ->get();
        $role->syncPermissions($permissions);

        $this->toast()->success($message)->send();
        $this->closeRoleModal();
    }

    // Delete methods
    public function deleteRole($roleId)
    {
        $this->selectedRoleId = $roleId;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this role?')
            ->confirm('Confirm', 'confirmedDeleteRole', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDeleteRole', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDeleteRole(string $message): void
    {
        if ($this->selectedRoleId) {
            Role::findOrFail($this->selectedRoleId)->delete();
            $this->dialog()->success('Success', 'Role deleted successfully!')->send();
            $this->selectedRoleId = null;
        }
    }

    public function cancelledDeleteRole(string $message): void
    {
        $this->selectedRoleId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Bulk Delete
    public function bulkDeleteRoles(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' role(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Role::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' role(s) deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function updatedRoleGuard()
    {
        // Clear selected permissions when guard changes
        $this->selectedPermissions = [];
    }

    public function toggleAllPermissions()
    {
        $allPermissionIds = Permission::where('guard_name', $this->roleGuard)->pluck('id')->toArray();

        if (count($this->selectedPermissions) === count($allPermissionIds)) {
            // Deselect all
            $this->selectedPermissions = [];
        } else {
            // Select all
            $this->selectedPermissions = $allPermissionIds;
        }
    }

    // Permission creation methods
    public function openCreatePermissionModal()
    {
        $this->permissionName = '';
        $this->permissionGuard = $this->roleGuard; // Match the role's guard
        $this->showCreatePermissionModal = true;
    }

    public function closeCreatePermissionModal()
    {
        $this->showCreatePermissionModal = false;
        $this->permissionName = '';
        $this->permissionGuard = 'web';
    }

    public function createPermission()
    {
        $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name',
            'permissionGuard' => 'required|string',
        ]);

        $permission = Permission::create([
            'name' => $this->permissionName,
            'guard_name' => $this->permissionGuard,
        ]);

        // Automatically add to selected permissions
        $this->selectedPermissions[] = $permission->id;

        $this->toast()->success('Permission created and added to role!')->send();
        $this->closeCreatePermissionModal();
    }

    // Standalone permission methods
    public function openStandalonePermissionModal()
    {
        $this->standalonePermissionName = '';
        $this->standalonePermissionGuard = 'employee';
        $this->showStandalonePermissionModal = true;
    }

    public function closeStandalonePermissionModal()
    {
        $this->showStandalonePermissionModal = false;
        $this->standalonePermissionName = '';
        $this->standalonePermissionGuard = 'employee';
    }

    public function createStandalonePermission()
    {
        $this->validate([
            'standalonePermissionName' => 'required|string|max:255|unique:permissions,name',
            'standalonePermissionGuard' => 'required|string',
        ]);

        Permission::create([
            'name' => $this->standalonePermissionName,
            'guard_name' => $this->standalonePermissionGuard,
        ]);

        $this->toast()->success('Permission created successfully!')->send();
        $this->closeStandalonePermissionModal();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        // Filter permissions by the selected guard
        $allPermissions = Permission::where('guard_name', $this->roleGuard)->get();

        return view('livewire.branch-dashboard.employee-module.role-permission.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Role Name'],
                ['index' => 'guard_name', 'label' => 'Guard'],
                ['index' => 'created_at', 'label' => 'Created At'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'allPermissions' => $allPermissions,
        ]);
    }
}

