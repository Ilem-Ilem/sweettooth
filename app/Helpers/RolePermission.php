<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Employee;
use App\Models\User;

class RolePermission
{
    // Cache duration in seconds (10 minutes)
    private const CACHE_TTL = 600;

    /**
     * Get the current authenticated user (either User or Employee)
     */
    public static function user(?string $guard = null)
    {
        if ($guard) {
            return Auth::guard($guard)->user();
        }

        // Unified system - all users authenticated via web guard
        return Auth::guard('web')->user();
    }

    /**
     * Get the guard name for the current authenticated user
     */
    public static function getGuardName(): ?string
    {
        // Unified system - all users authenticated via web guard
        if (Auth::guard('web')->check()) {
            return 'web';
        }

        return null;
    }

    /**
     * Check if user has a specific role
     */
    public static function hasRole(string $role, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasRole($role);
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole(array $roles, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Check if user has all of the specified roles
     */
    public static function hasAllRoles(array $roles, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasAllRoles($roles);
    }

    /**
     * Check if user has a specific permission
     */
    public static function hasPermission(string $permission, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has any of the specified permissions
     */
    public static function hasAnyPermission(array $permissions, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the specified permissions
     */
    public static function hasAllPermissions(array $permissions, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Check if user is a super admin
     */
    public static function isSuperAdmin(?string $guard = null): bool
    {
        return self::hasRole('Super Admin', $guard);
    }

    /**
     * Check if user is an admin
     */
    public static function isAdmin(?string $guard = null): bool
    {
        return self::hasAnyRole(['Admin', 'Super Admin'], $guard);
    }

    /**
     * Check if user is a managing director (MD role)
     */
    public static function isManagingDirector(?string $guard = null): bool
    {
        return self::hasAnyRole(['Managing Director', 'MD'], $guard);
    }

    /**
     * Check if user is a manager (any manager role)
     */
    public static function isManager(?string $guard = null): bool
    {
        $managerRoles = [
            'Managing Director',
            'Head of Production',
            'Sales Manager',
            'HR Manager',
            'Accounting Manager',
            'Inventory Manager',
            'Confectionaries Manager',
            'Corner Store Manager'
        ];

        return self::hasAnyRole($managerRoles, $guard);
    }

    /**
     * Check if user is an HR Manager
     */
    public static function isHRManager(?string $guard = null): bool
    {
        return self::hasRole('HR Manager', $guard);
    }

    /**
     * Check if user is an Accounting Manager
     */
    public static function isAccountingManager(?string $guard = null): bool
    {
        return self::hasRole('Accounting Manager', $guard);
    }

    /**
     * Check if user is a Production Helper
     */
    public static function isProductionHelper(?string $guard = null): bool
    {
        return self::hasRole('Production Helper', $guard);
    }

    /**
     * Check if user has cross-department access (for accounting purposes)
     */
    public static function hasCrossDepartmentAccess(?string $guard = null): bool
    {
        return self::isAccountingManager($guard) || self::isSuperAdmin($guard) || self::isManagingDirector($guard);
    }

    /**
     * Check if user is a supervisor/head
     */
    public static function isSupervisor(?string $guard = null): bool
    {
        $supervisorRoles = [
            'Chef',
            'Head of Gelato',
            'Till Supervisor',
        ];

        return self::hasAnyRole($supervisorRoles, $guard);
    }

    /**
     * Check if user is staff level
     */
    public static function isStaff(?string $guard = null): bool
    {
        $staffRoles = [
            'Kitchen Staff',
            'Gelato Production Staff',
            'Confectionaries Production Staff',
            'Cashier',
            'Corner Store Staff',
            'Confectionaries Sales Staff',
            'Stock Controller',
            'Store Keeper',
            'HR Officer',
        ];

        return self::hasAnyRole($staffRoles, $guard);
    }

    /**
     * Get all roles for the current user
     */
    public static function getUserRoles(?string $guard = null): array
    {
        $user = self::user($guard);

        if (!$user) {
            return [];
        }

        return $user->getRoleNames()->toArray();
    }

    /**
     * Get all permissions for the current user
     */
    public static function getUserPermissions(?string $guard = null): array
    {
        $user = self::user($guard);

        if (!$user) {
            return [];
        }

        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get all direct permissions (not through roles) for the current user
     */
    public static function getDirectPermissions(?string $guard = null): array
    {
        $user = self::user($guard);

        if (!$user) {
            return [];
        }

        return $user->getDirectPermissions()->pluck('name')->toArray();
    }

    /**
     * Get all permissions through roles
     */
    public static function getPermissionsViaRoles(?string $guard = null): array
    {
        $user = self::user($guard);

        if (!$user) {
            return [];
        }

        return $user->getPermissionsViaRoles()->pluck('name')->toArray();
    }

    /**
     * Get all available roles for a specific guard (with caching)
     */
    public static function getAllRoles(?string $guard = null): array
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        $cacheKey = "roles_{$guardName}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($guardName) {
            return Role::where('guard_name', $guardName)
                ->orderBy('name')
                ->get()
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                        'permissions_count' => $role->permissions()->count(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get all available permissions for a specific guard (with caching)
     */
    public static function getAllPermissions(?string $guard = null): array
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        $cacheKey = "permissions_{$guardName}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($guardName) {
            return Permission::where('guard_name', $guardName)
                ->orderBy('name')
                ->pluck('name')
                ->toArray();
        });
    }

    /**
     * Get permissions grouped by category
     */
    public static function getPermissionsByCategory(?string $guard = null): array
    {
        $permissions = self::getAllPermissions($guard);
        $grouped = [];

        foreach ($permissions as $permission) {
            // Extract category from permission name (e.g., 'view-employees' -> 'employees')
            $parts = explode('-', $permission);
            $action = $parts[0] ?? 'other';
            $resource = implode('-', array_slice($parts, 1)) ?: 'general';

            if (!isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }

            $grouped[$resource][] = $permission;
        }

        ksort($grouped);
        return $grouped;
    }

    /**
     * Assign role to a user
     */
    public static function assignRole($user, string $role): bool
    {
        try {
            $user->assignRole($role);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove role from a user
     */
    public static function removeRole($user, string $role): bool
    {
        try {
            $user->removeRole($role);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sync roles for a user (removes all current roles and assigns new ones)
     */
    public static function syncRoles($user, array $roles): bool
    {
        try {
            $user->syncRoles($roles);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Give permission to a user
     */
    public static function givePermission($user, string $permission): bool
    {
        try {
            $user->givePermissionTo($permission);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revoke permission from a user
     */
    public static function revokePermission($user, string $permission): bool
    {
        try {
            $user->revokePermissionTo($permission);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sync permissions for a user
     */
    public static function syncPermissions($user, array $permissions): bool
    {
        try {
            $user->syncPermissions($permissions);
            self::clearUserCache($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a role exists
     */
    public static function roleExists(string $role, ?string $guard = null): bool
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        return Role::where('name', $role)
            ->where('guard_name', $guardName)
            ->exists();
    }

    /**
     * Check if a permission exists
     */
    public static function permissionExists(string $permission, ?string $guard = null): bool
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        return Permission::where('name', $permission)
            ->where('guard_name', $guardName)
            ->exists();
    }

    /**
     * Get a role by name
     */
    public static function getRole(string $name, ?string $guard = null): ?Role
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        return Role::where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    /**
     * Get a permission by name
     */
    public static function getPermission(string $name, ?string $guard = null): ?Permission
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        return Permission::where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    /**
     * Get users with a specific role
     */
    public static function getUsersWithRole(string $role, ?string $guard = null): array
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        $modelClass = $guardName === 'web' ? User::class : Employee::class;

        return $modelClass::role($role)->get()->toArray();
    }

    /**
     * Get users with a specific permission
     */
    public static function getUsersWithPermission(string $permission, ?string $guard = null): array
    {
        $guardName = $guard ?? self::getGuardName() ?? 'employees';

        $modelClass = $guardName === 'web' ? User::class : Employee::class;

        return $modelClass::permission($permission)->get()->toArray();
    }

    /**
     * Clear all role/permission caches
     */
    public static function clearCache(): void
    {
        // Clear Spatie's permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear our custom caches
        Cache::forget('roles_employees');
        Cache::forget('roles_web');
        Cache::forget('permissions_employees');
        Cache::forget('permissions_web');
    }

    /**
     * Clear cache for a specific user
     */
    private static function clearUserCache($user): void
    {
        // Spatie automatically clears its cache when roles/permissions change
        // But we can force it
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Get hierarchical level of a role (for authorization logic)
     */
    public static function getRoleLevel(string $role): int
    {
        $hierarchy = [
            // Level 5 - Executive
            'Super Admin' => 5,
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
     * Check if user's role level is higher than specified level
     */
    public static function hasRoleLevel(int $minLevel, ?string $guard = null): bool
    {
        $user = self::user($guard);

        if (!$user) {
            return false;
        }

        $userRoles = $user->getRoleNames();

        foreach ($userRoles as $role) {
            if (self::getRoleLevel($role) >= $minLevel) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can manage another user (based on role hierarchy)
     */
    public static function canManageUser($targetUser, ?string $guard = null): bool
    {
        $currentUser = self::user($guard);

        if (!$currentUser || !$targetUser) {
            return false;
        }

        // Super admins and Managing Directors can manage anyone
        if (self::isSuperAdmin() || self::isManagingDirector()) {
            return true;
        }

        // Get highest role level for both users
        $currentUserLevel = 0;
        foreach ($currentUser->getRoleNames() as $role) {
            $currentUserLevel = max($currentUserLevel, self::getRoleLevel($role));
        }

        $targetUserLevel = 0;
        foreach ($targetUser->getRoleNames() as $role) {
            $targetUserLevel = max($targetUserLevel, self::getRoleLevel($role));
        }

        // Can manage if current user has higher level
        return $currentUserLevel > $targetUserLevel;
    }

    /**
     * Get role description (helpful for UI)
     */
    public static function getRoleDescription(string $role): string
    {
        $descriptions = [
            'Super Admin' => 'Full system access with all permissions',
            'Admin' => 'Administrative access to system settings',
            'Managing Director' => 'Executive level with full operational control',
            'Head of Production' => 'Oversees all production operations',
            'Sales Manager' => 'Manages sales operations and staff',
            'HR Manager' => 'Manages human resources and employee operations',
            'Inventory Manager' => 'Manages inventory and stock control',
            'Chef' => 'Leads kitchen operations and production',
            'Head of Gelato' => 'Manages gelato production department',
            'Confectionaries Manager' => 'Manages confectionaries production and sales',
            'Till Supervisor' => 'Supervises till operations and cashiers',
            'Corner Store Manager' => 'Manages corner store operations',
            'Kitchen Staff' => 'Executes kitchen production tasks',
            'Gelato Production Staff' => 'Produces gelato products',
            'Confectionaries Production Staff' => 'Produces confectionary products',
            'Cashier' => 'Processes sales transactions',
            'Corner Store Staff' => 'Handles corner store sales',
            'Confectionaries Sales Staff' => 'Sells confectionary products',
            'Stock Controller' => 'Controls and manages stock inventory',
            'Store Keeper' => 'Maintains warehouse and stock',
            'HR Officer' => 'Handles HR administrative tasks',
        ];

        return $descriptions[$role] ?? 'No description available';
    }

    /**
     * Check if user has access to a specific module
     */
    public static function canAccessModule(string $module, ?string $guard = null): bool
    {
        $modulePermissions = [
            'production' => ['view-production-queue', 'start-production', 'complete-production', 'manage-recipes'],
            'sales' => ['process-sale', 'view-daily-sales'],
            'inventory' => ['view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory'],
            'employees' => ['view-employees', 'create-employees', 'edit-employees'],
            'reports' => ['view-reports', 'generate-reports', 'view-analytics'],
            'settings' => ['view-settings', 'edit-settings', 'view-system-settings'],
        ];

        if (!isset($modulePermissions[$module])) {
            return false;
        }

        return self::hasAnyPermission($modulePermissions[$module], $guard);
    }
}
