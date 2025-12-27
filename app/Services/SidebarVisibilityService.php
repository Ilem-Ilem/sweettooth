<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Sidebar Visibility Control Service
 *
 * Controls which sidebar menu items are visible based on user roles and permissions.
 * Integrates with the permission system to show/hide menu sections.
 */
class SidebarVisibilityService
{
    /**
     * Check if user is a super admin
     * Super admins should see everything
     */
    public static function isSuperAdmin(): bool
    {
        return is_super_admin();
    }

    /**
     * Check if user can see Administration section
     * Super Admins (web guard) should always see this
     */
    public static function canSeeAdministration(Model $user): bool
    {
        // Super admins see everything
        if (self::isSuperAdmin()) {
            return true;
        }

        // For employees guard, check roles
        return $user->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin']);
    }

    /**
     * Check if user can see Organization section
     * Admin, Super Admin, MD, and HR Manager can see this
     */
    public static function canSeeOrganization(Model $user): bool
    {
        // Super Admin, Admin, MD can see everything
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyRole(['Admin', 'MD', 'Managing Director', 'HR Manager']);
    }

    /**
     * Check if user can see Employee Management
     */
    public static function canSeeEmployeeManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-employees')
            || $user->can('create-employees')
            || $user->can('edit-employees')
            || $user->can('manage_organization')
            || $user->hasAnyRole(['Super Admin', 'Admin', 'HR Manager', 'HR Officer']);
    }

    /**
     * Check if user can see Departments
     */
    public static function canSeeDepartments(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // Hide departments for HR Manager/Officer (they don't need to manage departments)
        if ($user->hasAnyRole(['HR Manager', 'HR Officer'])) {
            return false;
        }

        return $user->can('view-departments')
            || $user->hasAnyRole(['Super Admin', 'admin', 'manager']);
    }

    /**
     * Check if user can see Leave Management
     */
    public static function canSeeLeaveManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('manage-leave')
            || $user->can('approve-leave')
            || $user->can('manage_organization')
            || $user->hasAnyRole(['Super Admin', 'leave_manager', 'HR Manager', 'HR Officer']);
    }

    /**
     * Check if user can see Audit Management
     */
    public static function canSeeAuditManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // Hide audit for HR Manager/Officer
        if ($user->hasAnyRole(['HR Manager', 'HR Officer'])) {
            return false;
        }

        return $user->can('view-audit-logs')
            || $user->hasAnyRole(['Super Admin', 'auditor']);
    }

    /**
     * Check if user can see Inventory section
     * Only inventory-specific roles should see this section
     */
    public static function canSeeInventory(Model $user): bool
    {
        // Super Admin, Admin, MD can see everything - don't show for them as separate section
        if (self::isSuperAdmin()) {
            return false;
        }
        if ($user->hasAnyRole(['Admin', 'MD', 'Managing Director'])) {
            return false;
        }

        // Only inventory roles should see inventory section
        return $user->hasAnyRole(['Inventory Manager', 'Store Keeper', 'Stock Controller', 'Store Manager']);
    }

    /**
     * Check if user can see Inventory Management subsection
     */
    public static function canSeeInventoryManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // Hide for super admins/admins/MD as they see everything
        if ($user->hasAnyRole(['Admin', 'MD', 'Managing Director'])) {
            return false;
        }

        // Only show for inventory-specific roles
        return $user->hasAnyRole(['Inventory Manager'])
            && ($user->can('view-stock-levels')
                || $user->can('receive-stock')
                || $user->can('adjust-inventory'));
    }

    /**
     * Check if user can see Inventory Callbacks
     */
    public static function canSeeInventoryCallbacks(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // Hide for super admins/admins/MD as they see everything
        if ($user->hasAnyRole(['Admin', 'MD', 'Managing Director'])) {
            return false;
        }

        // Only show for inventory-specific roles
        return $user->hasAnyRole(['Inventory Manager'])
            && ($user->can('view-callbacks')
                || $user->can('approve-callbacks'));
    }

    /**
     * Check if user can see Analytics section
     */
    public static function canSeeAnalytics(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // HR Manager should not see analytics (only organization items)
        if ($user->hasRole('HR Manager')) {
            return false;
        }

        return $user->can('view-analytics')
            || $user->hasAnyRole(['Super Admin', 'reporting_manager', 'admin']);
    }

    /**
     * Check if user can see Production section
     * Only production-specific roles should see this section
     */
    public static function canSeeProduction(Model $user): bool
    {
        // Super admins should see everything including production
        if (self::isSuperAdmin()) {
            return true;
        }

        // Admin, MD see everything - don't show separate sections
        if ($user->hasAnyRole(['Admin', 'MD', 'Managing Director'])) {
            return false;
        }

        // Only production roles should see production section
        return $user->hasAnyRole([
            'Head of Production',
            'Chef',
            'Head of Gelato',
            'Confectioneries Manager',
            'Kitchen Staff',
            'Gelato Production Staff',
            'Confectioneries Production Staff',
        ]);
    }

    /**
     * Check if user can see Production Callbacks
     */
    public static function canSeeProductionCallbacks(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-callbacks')
            || $user->can('approve-callbacks')
            || $user->hasAnyRole([
                'Super Admin',
                'Head of Production',
                'Chef',
                'Head of Gelato',
                'Confectioneries Manager',
                'Admin',
            ]);
    }

    /**
     * Check if user can see Sales Management section
     */
    public static function canSeeSalesManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        // Explicitly exclude production roles from seeing sales
        $productionRoles = [
            'Head of Production',
            'Chef',
            'Head of Gelato',
            'Confectionaries Manager',
            'Kitchen Staff',
            'Gelato Production Staff',
            'Confectionaries Production Staff',
        ];

        if ($user->hasAnyRole($productionRoles)) {
            return false;
        }

        return $user->can('process-sale')
            || $user->can('view-daily-sales')
            || $user->can('close-register')
            || $user->hasAnyRole([
                'Super Admin',
                'Sales Manager',
                'Till Supervisor',
                'Cashier',
                'Corner Store Manager',
                'Corner Store Staff',
                'Confectionaries Sales Staff',
                'Admin',
            ]);
    }

    /**
     * Check if user can see Inventory Dashboard
     * Sales roles and production roles should NOT see inventory dashboard (they have access via their own sections)
     */
    public static function canSeeInventoryDashboard(Model $user): bool
    {
        // Super Admin, Admin, MD see everything - don't show separate inventory
        if (self::isSuperAdmin()) {
            return false;
        }
        if ($user->hasAnyRole(['Admin', 'MD', 'Managing Director', 'Super Admin'])) {
            return true;
        }

        // Exclude sales-only roles from seeing inventory
        $salesOnlyRoles = [
            'Till Supervisor',
            'Cashier',
            'Corner Store Manager',
            'Corner Store Staff',
            'Confectionaries Sales Staff',
        ];

        if ($user->hasAnyRole($salesOnlyRoles)) {
            return false;
        }

        // Exclude production roles from seeing inventory (they access stock via production section)
        $productionRoles = [
            'Head of Production',
            'Chef',
            'Head of Gelato',
            'Confectionaries Manager',
            'Kitchen Staff',
            'Gelato Production Staff',
            'Confectionaries Production Staff',
        ];

        if ($user->hasAnyRole($productionRoles)) {
            return false;
        }

        return self::canSeeInventory($user);
    }

    /**
     * Check if user can see Sales Management static items (manager level)
     */
    public static function canSeeSalesManagerItems(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-stock-levels')
            || $user->hasAnyRole(['Super Admin', 'Sales Manager', 'Admin']);
    }

    /**
     * Check if user can see Reporting section
     */
    public static function canSeeReporting(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-reports')
            || $user->can('generate-reports')
            || $user->hasAnyRole(['Super Admin', 'reporting_manager', 'admin']);
    }

    /**
     * Check if user can see Accounting section
     */
    public static function canSeeAccounting(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('access_accounting')
            || $user->can('view_financial_reports')
            || $user->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'admin', 'accountant']);
    }

    /**
     * Check if user can see Role Assignments
     */
    public static function canSeeRoleAssignments(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('assign-roles')
            || $user->can('manage_organization')
            || $user->hasAnyRole(['Super Admin', 'admin', 'HR Manager', 'HR Officer']);
    }

    /**
     * Check if user can see Roles & Permissions link
     */
    public static function canSeeRolesPermissions(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-roles')
            || $user->hasAnyRole(['Super Admin', 'admin']);
    }

    /**
     * Check if user can see Branch Management
     */
    public static function canSeeBranchManagement(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-branches')
            || $user->hasAnyRole(['Super Admin', 'admin']);
    }

    /**
     * Check if user can see MD Reports
     */
    public static function canSeeMDReports(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('view-reports')
            || $user->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'admin']);
    }

    /**
     * Check if user can see System Settings
     */
    public static function canSeeSettings(Model $user): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return $user->can('manage-settings')
            || $user->hasAnyRole(['Super Admin', 'admin']);
    }

    /**
     * Check if user is department restricted production role
     */
    public static function isDepartmentRestrictedProductionRole(Model $user): bool
    {
        return $user->hasAnyRole([
            'Chef',
            'Head of Gelato',
            'Confectionaries Manager',
            'Kitchen Staff',
            'Gelato Production Staff',
            'Confectionaries Production Staff',
        ]);
    }

    /**
     * Check if user is production admin role
     */
    public static function isProductionAdminRole(Model $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Head of Production', 'Admin']);
    }

    /**
     * Check if user is department restricted sales role
     */
    public static function isDepartmentRestrictedSalesRole(Model $user): bool
    {
        return $user->hasAnyRole([
            'Till Supervisor',
            'Cashier',
            'Corner Store Manager',
            'Corner Store Staff',
            'Confectionaries Sales Staff',
        ]);
    }

    /**
     * Check if user is sales admin role
     */
    public static function isSalesAdminRole(Model $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Sales Manager', 'Admin']);
    }

    /**
     * Get all visible menu sections for user
     */
    public static function getVisibleSections(Model $user): array
    {
        return [
            'administration' => self::canSeeAdministration($user),
            'organization' => self::canSeeOrganization($user),
            'employee_management' => self::canSeeEmployeeManagement($user),
            'departments' => self::canSeeDepartments($user),
            'leave_management' => self::canSeeLeaveManagement($user),
            'audit_management' => self::canSeeAuditManagement($user),
            'inventory' => self::canSeeInventory($user),
            'analytics' => self::canSeeAnalytics($user),
            'production' => self::canSeeProduction($user),
            'sales' => self::canSeeSalesManagement($user),
            'reporting' => self::canSeeReporting($user),
            'accounting' => self::canSeeAccounting($user),
        ];
    }
}
