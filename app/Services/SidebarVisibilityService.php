<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Collection;

/**
 * Sidebar Visibility Service (Simplified)
 *
 * Controls sidebar menu visibility based on:
 * 1. User's DEPARTMENT (determines what features they see)
 * 2. User's ROLE LEVEL (determines what they can do)
 *
 * Role Levels:
 * 5 = Super Admin (everything)
 * 4 = Admin (all departments in branch)
 * 3 = Manager (all departments in category)
 * 2 = Supervisor (own department + reports)
 * 1 = Staff (own department, basic access)
 */
class SidebarVisibilityService
{
    // Role levels
    public const LEVEL_SUPER_ADMIN = 5;
    public const LEVEL_ADMIN = 4;
    public const LEVEL_MANAGER = 3;
    public const LEVEL_SUPERVISOR = 2;
    public const LEVEL_STAFF = 1;

    /**
     * Get user's role level (1-5)
     */
    public static function getRoleLevel(?User $user = null): int
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return 0;
        }

        // Check by role level column first (new system)
        $role = $user->roles()->orderByDesc('level')->first();

        if ($role && isset($role->level) && $role->level > 0) {
            return (int) $role->level;
        }

        // Fallback: check by role name (old system compatibility)
        if ($user->hasRole('Super Admin')) return self::LEVEL_SUPER_ADMIN;
        if ($user->hasRole('Admin')) return self::LEVEL_ADMIN;

        $managerRoles = [
            'Manager', 'Head of Production', 'Chef', 'Head of Gelato',
            'Confectioneries Manager', 'Sales Manager', 'HR Manager',
            'Inventory Manager', 'Corner Store Manager', 'MD', 'Managing Director'
        ];
        if ($user->hasAnyRole($managerRoles)) return self::LEVEL_MANAGER;

        $supervisorRoles = ['Supervisor', 'Till Supervisor', 'Sales Supervisor', 'Stock Controller'];
        if ($user->hasAnyRole($supervisorRoles)) return self::LEVEL_SUPERVISOR;

        return self::LEVEL_STAFF;
    }

    /**
     * Check if user is Super Admin (level 5)
     */
    public static function isSuperAdmin(?User $user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    /**
     * Check if user is Admin or higher (level 4+)
     */
    public static function isAdmin(?User $user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    /**
     * Check if user is Manager or higher (level 3+)
     */
    public static function isManager(?User $user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    /**
     * Check if user is Supervisor or higher (level 2+)
     */
    public static function isSupervisor(?User $user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPERVISOR;
    }

    /**
     * Get user's department category name
     */
    public static function getDepartmentCategory(?User $user = null): ?string
    {
        $user = $user ?? auth()->user();
        return $user?->department?->category?->name;
    }

    /**
     * Get departments user can access in sidebar
     */
    public static function getAccessibleDepartments(?User $user = null): Collection
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return collect();
        }

        $level = self::getRoleLevel($user);
        $branchId = session('current_branch_id') ?? $user->branch_id;

        // Level 5: Super Admin sees all departments (across all branches if needed)
        if ($level >= self::LEVEL_SUPER_ADMIN) {
            $query = Department::where('is_active', true);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            return $query->with('category')->orderBy('name')->get();
        }

        // Level 4: Admin sees all departments in their branch
        if ($level >= self::LEVEL_ADMIN) {
            return Department::where('branch_id', $branchId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        // Level 3: Manager sees all departments in same category
        if ($level >= self::LEVEL_MANAGER) {
            $categoryId = $user->department?->category_id;
            return Department::where('branch_id', $branchId)
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        // Level 1-2: Supervisor/Staff see only their department
        $dept = $user->department;
        return $dept ? collect([$dept->load('category')]) : collect();
    }

    /**
     * Get visible menu sections based on department and role level
     */
    public static function getVisibleSections(?User $user = null): array
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return [];
        }

        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);
        $deptName = $user->department?->name;

        return [
            // Dashboard - everyone
            'dashboard' => true,

            // Department-specific sections (based on category)
            'production' => $category === 'Production' || $level >= self::LEVEL_ADMIN,
            'sales' => $category === 'Sales' || $level >= self::LEVEL_ADMIN,
            'inventory' => ($category === 'Support' && str_contains($deptName ?? '', 'Inventory')) || $level >= self::LEVEL_ADMIN,
            'hr' => ($category === 'Support' && $deptName === 'HR') || $level >= self::LEVEL_ADMIN,
            'accounting' => ($category === 'Support' && str_contains($deptName ?? '', 'Account')) || $level >= self::LEVEL_ADMIN,

            // Role-level sections
            'reports' => $level >= self::LEVEL_SUPERVISOR,
            'analytics' => $level >= self::LEVEL_SUPERVISOR,
            'staff_schedule' => $level >= self::LEVEL_SUPERVISOR,

            // Admin sections (level 4+)
            'organization' => $level >= self::LEVEL_ADMIN,
            'administration' => $level >= self::LEVEL_ADMIN,
            'user_management' => $level >= self::LEVEL_ADMIN,
            'department_management' => $level >= self::LEVEL_ADMIN,

            // Super Admin only (level 5)
            'roles_permissions' => $level >= self::LEVEL_SUPER_ADMIN,
            'branch_management' => $level >= self::LEVEL_SUPER_ADMIN,
            'system_settings' => $level >= self::LEVEL_SUPER_ADMIN,
            'audit_logs' => $level >= self::LEVEL_SUPER_ADMIN,
        ];
    }

    // =========================================================================
    // LEGACY COMPATIBILITY METHODS (for existing blade templates)
    // These map old method names to new logic
    // =========================================================================

    public static function canSeeAdministration($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    public static function canSeeOrganization($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);

        // Admin+ OR HR roles can see organization
        return $level >= self::LEVEL_ADMIN
            || ($user && $user->hasAnyRole(['HR Manager', 'HR Officer']));
    }

    public static function canSeeEmployeeManagement($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        // HR department, HR roles, or Admin+
        return ($category === 'Support' && $user?->department?->name === 'HR')
            || ($user && $user->hasAnyRole(['HR Manager', 'HR Officer']))
            || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeDepartments($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    public static function canSeeLeaveManagement($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        return ($category === 'Support' && $user?->department?->name === 'HR') || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeAuditManagement($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    public static function canSeeInventory($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);
        $deptName = $user?->department?->name ?? '';

        return (str_contains($deptName, 'Inventory') || str_contains($deptName, 'Store'))
            || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeInventoryManagement($user = null): bool
    {
        return self::canSeeInventory($user) && self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    public static function canSeeInventoryCallbacks($user = null): bool
    {
        return self::canSeeInventory($user) && self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    public static function canSeeAnalytics($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPERVISOR;
    }

    public static function canSeeProduction($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        return $category === 'Production' || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeProductionCallbacks($user = null): bool
    {
        return self::canSeeProduction($user) && self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    public static function canSeeSalesManagement($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        return $category === 'Sales' || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeInventoryDashboard($user = null): bool
    {
        return self::canSeeInventory($user);
    }

    public static function canSeeSalesManagerItems($user = null): bool
    {
        return self::canSeeSalesManagement($user) && self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    public static function canSeeReporting($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPERVISOR;
    }

    public static function canSeeAccounting($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $deptName = $user?->department?->name ?? '';

        return str_contains($deptName, 'Account') || $level >= self::LEVEL_ADMIN;
    }

    public static function canSeeRoleAssignments($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    public static function canSeeRolesPermissions($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    public static function canSeeBranchManagement($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    public static function canSeeMDReports($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    public static function canSeeSettings($user = null): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    // =========================================================================
    // DEPARTMENT-RESTRICTED ROLE CHECKS (for sidebar department filtering)
    // =========================================================================

    public static function isDepartmentRestrictedProductionRole($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        // Production category and below Manager level = restricted to own dept
        return $category === 'Production' && $level < self::LEVEL_MANAGER;
    }

    public static function isProductionAdminRole($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        // Admin+ OR Manager in Production
        return $level >= self::LEVEL_ADMIN || ($category === 'Production' && $level >= self::LEVEL_MANAGER);
    }

    public static function isDepartmentRestrictedSalesRole($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        // Sales category and below Manager level = restricted to own dept
        return $category === 'Sales' && $level < self::LEVEL_MANAGER;
    }

    public static function isSalesAdminRole($user = null): bool
    {
        $user = $user ?? auth()->user();
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);

        // Admin+ OR Manager in Sales
        return $level >= self::LEVEL_ADMIN || ($category === 'Sales' && $level >= self::LEVEL_MANAGER);
    }

    // =========================================================================
    // PRODUCTION MENU ITEMS
    // =========================================================================

    public static function getProductionDepartments(?User $user = null): Collection
    {
        return self::getAccessibleDepartments($user)
            ->filter(fn($d) => $d->category?->name === 'Production');
    }

    // =========================================================================
    // SALES MENU ITEMS
    // =========================================================================

    public static function getSalesDepartments(?User $user = null): Collection
    {
        return self::getAccessibleDepartments($user)
            ->filter(fn($d) => $d->category?->name === 'Sales');
    }
}
