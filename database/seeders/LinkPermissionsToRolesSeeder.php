<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class LinkPermissionsToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we're using web guard
        $guard = 'web';

        // Get or create all roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => $guard]);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => $guard]);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => $guard]);
        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => $guard]);

        // ===== SUPER ADMIN: Gets ALL permissions =====
        $superAdminPerms = [
            // System Admin
            'manage.users',
            'manage.roles',
            'manage.permissions',
            'manage.branches',
            'manage.departments',
            'manage.categories',
            'view.audit_trail',
            'manage.settings',
            'manage.system_config',

            // All Production
            'view.daily_produce',
            'record.daily_produce',
            'edit.daily_produce',
            'delete.daily_produce',
            'view.recipes',
            'edit.recipes',
            'delete.recipes',
            'manage.recipes',
            'view.products',
            'edit.products',
            'delete.products',
            'view.requests',
            'create.requests',
            'edit.requests',
            'approve.requests',
            'reject.requests',
            'view.stock_takes',
            'conduct.stock_take',
            'verify.stock_take',
            'report.production',

            // All Sales
            'view.pos',
            'process.sale',
            'edit.sale',
            'void.sale',
            'refund.sale',
            'view.my_sales',
            'view.sales_analytics',
            'manage.price_override',
            'report.sales',

            // All Inventory
            'view.stock_levels',
            'record.stock_movement',
            'adjust.stock',
            'approve.stock_movement',
            'report.inventory',
            'manage.stock_items',

            // All HR
            'manage.employees',
            'view.employees',
            'edit.employee_profile',
            'manage.leave',
            'approve.leave',
            'view.attendance',
            'report.hr',

            // All Accounting
            'view.financial_reports',
            'manage.accounts',
            'approve.transactions',
            'view.audit_trail',
            'report.accounting',

            // All Callbacks/Dispatch
            'view.callbacks',
            'record.callback',
            'manage.callbacks',
            'view.dispatches',
            'manage.dispatches',
        ];

        $this->assignPermissionsToRole($superAdmin, $superAdminPerms);
        $this->command->info('✅ Super Admin: ' . count($superAdminPerms) . ' permissions assigned');

        // ===== ADMIN: Branch-wide access =====
        $adminPerms = [
            // User Management (branch-level)
            'view.users',
            'edit.users',
            'manage.departments',
            'view.audit_trail',

            // All Production
            'view.daily_produce',
            'record.daily_produce',
            'edit.daily_produce',
            'view.recipes',
            'edit.recipes',
            'view.products',
            'edit.products',
            'view.requests',
            'create.requests',
            'edit.requests',
            'approve.requests',
            'view.stock_takes',
            'conduct.stock_take',
            'verify.stock_take',
            'report.production',

            // All Sales
            'view.pos',
            'process.sale',
            'edit.sale',
            'void.sale',
            'refund.sale',
            'view.my_sales',
            'view.sales_analytics',
            'manage.price_override',
            'report.sales',

            // All Inventory
            'view.stock_levels',
            'record.stock_movement',
            'adjust.stock',
            'approve.stock_movement',
            'report.inventory',
            'manage.stock_items',

            // All HR (branch-level)
            'view.employees',
            'manage.leave',
            'view.attendance',
            'report.hr',

            // All Accounting (branch-level)
            'view.financial_reports',
            'report.accounting',
        ];

        $this->assignPermissionsToRole($admin, $adminPerms);
        $this->command->info('✅ Admin: ' . count($adminPerms) . ' permissions assigned');

        // ===== MANAGER: Department control =====
        $managerPerms = [
            // Dashboard & reporting
            'view.dashboard',
            'report.production',
            'report.sales',
            'report.inventory',

            // Production (if in production dept)
            'view.daily_produce',
            'record.daily_produce',
            'edit.daily_produce',
            'view.recipes',
            'edit.recipes',
            'view.products',
            'edit.products',
            'view.requests',
            'create.requests',
            'edit.requests',
            'approve.requests',
            'view.stock_takes',
            'conduct.stock_take',
            'verify.stock_take',

            // Sales (if in sales dept)
            'view.pos',
            'process.sale',
            'edit.sale',
            'void.sale',
            'view.my_sales',
            'view.sales_analytics',

            // Inventory (if in inventory dept)
            'view.stock_levels',
            'record.stock_movement',
            'adjust.stock',
            'approve.stock_movement',

            // HR (if in HR dept)
            'view.employees',
            'manage.leave',

            // Callbacks/Dispatch
            'view.callbacks',
            'record.callback',
            'manage.callbacks',
            'view.dispatches',
            'manage.dispatches',
        ];

        $this->assignPermissionsToRole($manager, $managerPerms);
        $this->command->info('✅ Manager: ' . count($managerPerms) . ' permissions assigned');

        // ===== SUPERVISOR: Limited management =====
        $supervisorPerms = [
            // Dashboard
            'view.dashboard',
            'view.reports',

            // Production (if assigned)
            'view.daily_produce',
            'record.daily_produce',
            'view.recipes',
            'view.products',
            'view.requests',
            'create.requests',
            'view.stock_takes',

            // Sales (if assigned)
            'view.pos',
            'process.sale',
            'view.my_sales',
            'view.sales_analytics',
            'report.sales',

            // Inventory (if assigned)
            'view.stock_levels',
            'record.stock_movement',
            'view.callbacks',
            'record.callback',

            // HR (if assigned)
            'view.employees',
            'view.attendance',
        ];

        $this->assignPermissionsToRole($supervisor, $supervisorPerms);
        $this->command->info('✅ Supervisor: ' . count($supervisorPerms) . ' permissions assigned');

        // ===== STAFF: Basic operational access =====
        $staffPerms = [
            // Dashboard
            'view.dashboard',

            // Production (if assigned)
            'view.daily_produce',
            'record.daily_produce',
            'view.recipes',
            'view.products',

            // Sales (if assigned)
            'view.pos',
            'process.sale',
            'view.my_sales',

            // Inventory (if assigned)
            'view.stock_levels',
            'record.stock_movement',
            'view.callbacks',

            // HR (if assigned)
            'view.employees',
        ];

        $this->assignPermissionsToRole($staff, $staffPerms);
        $this->command->info('✅ Staff: ' . count($staffPerms) . ' permissions assigned');

        $this->command->info('✅ All roles linked to permissions successfully!');
    }

    /**
     * Assign multiple permissions to a role
     */
    private function assignPermissionsToRole(Role $role, array $permissionNames): void
    {
        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['guard_name' => 'web']
            );
            $role->givePermissionTo($permission);
        }
    }
}
