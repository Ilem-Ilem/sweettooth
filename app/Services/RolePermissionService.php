<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    const CACHE_TTL = 600;

    const PROTECTED_ROLES = ['Super Admin', 'Managing Director', 'MD', 'Admin'];

    /**
     * Get current authenticated user
     */
    public static function user(?string $guard = null): ?Model
    {
        if ($guard) {
            return Auth::guard($guard)->user();
        }

        return Auth::guard('web')->user() ?? Auth::guard('web')->user();
    }

    /**
     * Get the current guard name
     */
    public static function getGuardName(): ?string
    {
        if (Auth::guard('web')->check()) {
            return 'employees';
        }
        if (Auth::guard('web')->check()) {
            return 'web';
        }

        return null;
    }

    /**
     * Check if role is protected (system core role)
     */
    public static function isProtectedRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();

        return $role && $role->is_protected;
    }

    /**
     * Check if permission is protected
     */
    public static function isProtectedPermission(string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();

        return $permission && $permission->is_protected;
    }

    /**
     * Create new role with validation
     *
     * @throws \Exception
     */
    public static function createRole(
        string $name,
        string $guardName,
        array $permissions = [],
        string $description = ''
    ): Role {
        // Validate role name
        if (empty(trim($name))) {
            throw new \Exception('Role name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new \Exception('Role name too long (max 255 characters)');
        }

        // Check if role already exists
        if (Role::where('name', $name)->where('guard_name', $guardName)->exists()) {
            throw new \Exception("Role '{$name}' already exists for guard '{$guardName}'");
        }

        // Log role creation
        Log::info('Role created', [
            'name' => $name,
            'guard' => $guardName,
            'created_by' => self::user()?->id,
            'created_at' => now(),
        ]);

        $role = Role::create([
            'name' => $name,
            'guard_name' => $guardName,
            'is_protected' => false,
            'description' => $description,
        ]);

        // Add permissions
        if (! empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        self::clearCache();

        return $role;
    }

    /**
     * Update role with protection checks
     *
     * @throws \Exception
     */
    public static function updateRole(int $roleId, array $updates): Role
    {
        $role = Role::findOrFail($roleId);

        // Prevent modifying protected roles
        if ($role->is_protected && ! self::isSuperAdmin()) {
            throw new \Exception("Cannot modify protected role: {$role->name}");
        }

        // Log update
        Log::info('Role updated', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'updated_by' => self::user()?->id,
            'changes' => $updates,
            'updated_at' => now(),
        ]);

        $role->update($updates);
        self::clearCache();

        return $role;
    }

    /**
     * Delete role with protection
     *
     * @throws \Exception
     */
    public static function deleteRole(int $roleId): bool
    {
        $role = Role::findOrFail($roleId);

        // Prevent deleting protected roles
        if ($role->is_protected) {
            throw new \Exception("Cannot delete protected role: {$role->name}");
        }

        // Check if role is assigned to users
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            throw new \Exception(
                "Cannot delete role assigned to {$userCount} user(s). ".
                'Remove the role from all users first.'
            );
        }

        // Log deletion
        Log::warning('Role deleted', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'deleted_by' => self::user()?->id,
            'deleted_at' => now(),
        ]);

        $deleted = $role->delete();
        self::clearCache();

        return $deleted;
    }

    /**
     * Assign role to user with validation
     *
     * @throws \Exception
     */
    public static function assignRoleToUser(Model $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        // Check authorization
        if (! self::canAssignRole($role)) {
            throw new \Exception(
                "You don't have permission to assign '{$roleName}' role"
            );
        }

        // Validate department-role compatibility
        self::validateRoleForDepartment($user, $roleName);

        // Log assignment
        Log::info('Role assigned to user', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_name' => $roleName,
            'assigned_by' => self::user()?->id,
            'assigned_at' => now(),
        ]);

        $user->assignRole($roleName);
        self::clearCache();

        return true;
    }

    /**
     * Remove role from user with validation
     *
     * @throws \Exception
     */
    public static function removeRoleFromUser(Model $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        // Prevent removing protected roles from last admin
        if ($role->is_protected && $user->hasRole($roleName)) {
            $adminCount = Role::where('name', $roleName)
                ->withCount('users')
                ->first()
                ->users_count ?? 0;

            if ($adminCount <= 1) {
                throw new \Exception(
                    "Cannot remove last '{$roleName}' from the system. ".
                    'At least one user must have this role.'
                );
            }
        }

        // Log removal
        Log::info('Role removed from user', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_name' => $roleName,
            'removed_by' => self::user()?->id,
            'removed_at' => now(),
        ]);

        $user->removeRole($roleName);
        self::clearCache();

        return true;
    }

    /**
     * Check if current user can assign a specific role
     */
    public static function canAssignRole(Role $role): bool
    {
        $user = self::user();

        if (! $user) {
            return false;
        }

        // Only Super Admin can assign protected roles
        if ($role->is_protected && ! $user->hasAnyRole(['Super Admin', 'MD'])) {
            return false;
        }

        return true;
    }

    /**
     * Sync permissions for a role with validation
     *
     * @throws \Exception
     */
    public static function syncRolePermissions(int $roleId, array $permissionIds): Role
    {
        $role = Role::findOrFail($roleId);

        // Prevent modifying protected roles
        if ($role->is_protected && ! self::isSuperAdmin()) {
            throw new \Exception("Cannot modify permissions for protected role: {$role->name}");
        }

        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', 'web')
            ->get();

        // Log permission sync
        Log::info('Role permissions synced', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'permission_count' => count($permissionIds),
            'synced_by' => self::user()?->id,
            'synced_at' => now(),
        ]);

        $role->syncPermissions($permissions);
        self::clearCache();

        return $role;
    }

    /**
     * Create permission with validation
     *
     * @throws \Exception
     */
    public static function createPermission(
        string $name,
        string $guardName,
        string $description = '',
        string $category = 'general'
    ): Permission {
        if (empty(trim($name))) {
            throw new \Exception('Permission name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new \Exception('Permission name too long (max 255 characters)');
        }

        // Validate permission naming convention
        if (! preg_match('/^[a-z0-9\-]+$/', $name)) {
            throw new \Exception(
                'Permission name must contain only lowercase letters, numbers, and hyphens'
            );
        }

        if (Permission::where('name', $name)->where('guard_name', $guardName)->exists()) {
            throw new \Exception("Permission '{$name}' already exists");
        }

        // Log creation
        Log::info('Permission created', [
            'name' => $name,
            'guard' => $guardName,
            'category' => $category,
            'created_by' => self::user()?->id,
            'created_at' => now(),
        ]);

        $permission = Permission::create([
            'name' => $name,
            'guard_name' => $guardName,
            'description' => $description,
            'category' => $category,
            'is_protected' => false,
        ]);

        self::clearCache();

        return $permission;
    }

    /**
     * Delete permission with validation
     *
     * @throws \Exception
     */
    public static function deletePermission(int $permissionId): bool
    {
        $permission = Permission::findOrFail($permissionId);

        // Prevent deleting protected permissions
        if ($permission->is_protected) {
            throw new \Exception("Cannot delete protected permission: {$permission->name}");
        }

        // Check if permission is in use
        $roleCount = $permission->roles()->count();
        if ($roleCount > 0) {
            throw new \Exception(
                "Cannot delete permission assigned to {$roleCount} role(s). ".
                'Remove the permission from all roles first.'
            );
        }

        // Log deletion
        Log::warning('Permission deleted', [
            'permission_id' => $permissionId,
            'permission_name' => $permission->name,
            'deleted_by' => self::user()?->id,
            'deleted_at' => now(),
        ]);

        $deleted = $permission->delete();
        self::clearCache();

        return $deleted;
    }

    /**
     * Get all roles grouped by protection status
     */
    public static function getRolesByProtection(string $guardName = 'web'): array
    {
        return Cache::remember("roles_by_protection_{$guardName}", self::CACHE_TTL, function () use ($guardName) {
            $roles = Role::where('guard_name', $guardName)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();

            return [
                'protected' => $roles->where('is_protected', true)->values()->toArray(),
                'custom' => $roles->where('is_protected', false)->values()->toArray(),
            ];
        });
    }

    /**
     * Get all permissions grouped by category
     */
    public static function getPermissionsByCategory(string $guardName = 'web'): array
    {
        return Cache::remember("permissions_by_category_{$guardName}", self::CACHE_TTL, function () use ($guardName) {
            return Permission::where('guard_name', $guardName)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category')
                ->mapWithKeys(function ($permissions, $category) {
                    return [$category => $permissions->toArray()];
                })
                ->toArray();
        });
    }

    /**
     * Clear all role/permission caches
     */
    public static function clearCache(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('roles_by_protection_web');
        Cache::forget('permissions_by_category_employees');
        Cache::forget('permissions_by_category_web');
    }

    /**
     * Check if current user is super admin
     * Super Admin role can be in either 'web' or 'employees' guard
     */
    public static function isSuperAdmin(): bool
    {
        // Check web guard first (web users are super admins)
        $webUser = Auth::guard('web')->user();
        if ($webUser && $webUser->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin'], 'web')) {
            return true;
        }

        // Check employees guard
        $employeeUser = Auth::guard('web')->user();
        if ($employeeUser && $employeeUser->hasAnyRole(['Super Admin', 'MD'], 'employees')) {
            return true;
        }

        return false;
    }

    /**
     * Get role change history
     */
    public static function getRoleHistory(int $roleId, int $limit = 50): array
    {
        return \DB::table('audit_logs')
            ->where('model_type', Role::class)
            ->where('model_id', $roleId)
            ->where('event', 'updated')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get all users with a specific role
     */
    public static function getUsersWithRole(string $roleName, ?string $guardName = null): array
    {
        $query = Role::where('name', $roleName);

        if ($guardName) {
            $query->where('guard_name', $guardName);
        }

        $role = $query->first();

        if (! $role) {
            return [];
        }

        return $role->users()->get()->toArray();
    }

    /**
     * Get all permissions for a role
     */
    public static function getRolePermissions(int $roleId): array
    {
        $role = Role::findOrFail($roleId);

        return $role->permissions()->get()->toArray();
    }

    /**
     * Check if role has all required permissions
     */
    public static function roleHasAllPermissions(int $roleId, array $permissionNames): bool
    {
        $role = Role::findOrFail($roleId);
        $rolePermissions = $role->permissions()->pluck('name')->toArray();

        foreach ($permissionNames as $permission) {
            if (! in_array($permission, $rolePermissions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get role by name with guard
     */
    public static function getRoleByName(string $name, string $guardName = 'web'): ?Role
    {
        return Role::where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    /**
     * Verify system has at least one super admin
     */
    public static function hasSuperAdmin(): bool
    {
        return Role::whereIn('name', ['Super Admin', 'MD'])
            ->withCount('users')
            ->get()
            ->sum('users_count') > 0;
    }

    /**
     * Get role hierarchy level
     */
    public static function getRoleLevel(string $role): int
    {
        $hierarchy = [
            // Level 5 - Executive
            'Super Admin' => 5,
            'MD' => 5,
            'Managing Director' => 5,

            // Level 4 - Management
            'Admin' => 4,
            'Head of Production' => 4,
            'Sales Manager' => 4,
            'HR Manager' => 4,
            'Inventory Manager' => 4,

            // Level 3 - Department Heads/Supervisors
            'Chef' => 3,
            'Head of Gelato' => 3,
            'Confectionaries Manager' => 3,
            'Till Supervisor' => 3,
            'Corner Store Manager' => 3,

            // Level 2 - Officers
            'HR Officer' => 2,
            'Stock Controller' => 2,
            'Store Keeper' => 2,

            // Level 1 - Staff
            'Kitchen Staff' => 1,
            'Gelato Production Staff' => 1,
            'Confectionaries Production Staff' => 1,
            'Cashier' => 1,
            'Corner Store Staff' => 1,
            'Confectionaries Sales Staff' => 1,
        ];

        return $hierarchy[$role] ?? 0;
    }

    /**
     * Get role-to-department mappings
     * IMPORTANT: Role names must match database exactly (case-sensitive)
     * but validation is case-insensitive for flexibility
     */
    public static function getRoleDepartmentMappings(): array
    {
        return [
            // Base roles (can be assigned to any department)
            'Employee' => ['*'],
            'Staff' => ['*'],
            'Viewer' => ['*'],

            // Production roles
            'Kitchen Staff' => ['Kitchen'],
            'Chef' => ['Kitchen'],
            'Gelato Production Staff' => ['Gelato Production'],
            'Head of Gelato' => ['Gelato Production'],
            'Confectioneries Production Staff' => ['Confectioneries Production'],
            'Confectioneries Manager' => ['Confectioneries Production'],
            'Production Helper' => ['Kitchen', 'Gelato Production', 'Confectioneries Production'],

            // Sales roles
            'Cashier' => ['Till'],
            'Junior Cashier' => ['Till'],
            'Till Supervisor' => ['Till'],
            'Corner Store Staff' => ['Corner Store'],
            'Corner Store Manager' => ['Corner Store'],
            'Confectioneries Sales Staff' => ['Confectioneries Sales'],
            'Sales Manager' => ['Till', 'Corner Store', 'Confectioneries Sales'],
            'Sales Associate' => ['Till', 'Corner Store', 'Confectioneries Sales'],
            'Sales Supervisor' => ['Till', 'Corner Store', 'Confectioneries Sales'],

            // Support roles
            'Stock Controller' => ['Inventory/Store'],
            'Store Keeper' => ['Inventory/Store'],
            'Warehouse Manager' => ['Inventory/Store'],
            'Inventory Clerk' => ['Inventory/Store'],
            'Inventory Manager' => ['Inventory/Store'],
            'Store Manager' => ['Corner Store', 'Confectioneries Sales', 'Inventory/Store'],
            'Store Supervisor' => ['Corner Store', 'Confectioneries Sales'],
            'HR Officer' => ['HR'],
            'HR Manager' => ['HR'],
            'Accounting Manager' => ['Accounting'],
            'Manager' => ['*'],

            // Admin roles (can be assigned to any department)
            'Admin' => ['*'],
            'Super Admin' => ['*'],
            'Managing Director' => ['*'],
            'MD' => ['*'],
            'Head of Production' => ['*'],
            'Supervisor' => ['*'],
        ];
    }

    /**
     * Validate that a role is compatible with user's department
     *
     * @throws \Exception
     */
    public static function validateRoleForDepartment(Model $user, string $roleName): void
    {
        // Get user's department
        if (! isset($user->department_id) || ! $user->department_id) {
            throw new \Exception('User must be assigned to a department before assigning roles');
        }

        $department = \App\Models\Department::find($user->department_id);
        if (! $department) {
            throw new \Exception('User department not found');
        }

        // Get role-to-department mappings
        $roleToDepartments = self::getRoleDepartmentMappings();

        // Check if role exists in mapping (case-insensitive)
        $foundRole = null;
        foreach ($roleToDepartments as $mappedRole => $depts) {
            if (strcasecmp($mappedRole, $roleName) === 0) {
                $foundRole = $mappedRole;
                break;
            }
        }

        if ($foundRole === null) {
            throw new \Exception("Role '{$roleName}' is not defined for department assignment");
        }

        $allowedDepts = $roleToDepartments[$foundRole];

        // Check if role is allowed in all departments
        if (in_array('*', $allowedDepts)) {
            return;
        }

        // Check if user's department is in the allowed list
        if (! in_array($department->name, $allowedDepts)) {
            $deptList = count($allowedDepts) === 1
                ? $allowedDepts[0]
                : implode(', ', array_slice($allowedDepts, 0, -1)).' or '.end($allowedDepts);

            throw new \Exception(
                "The \"{$roleName}\" role is only available for {$deptList} department staff. ".
                "Please choose a different role for this employee."
            );
        }
    }
}
