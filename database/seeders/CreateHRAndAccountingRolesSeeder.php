<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class CreateHRAndAccountingRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create HR Manager Role
        $hrManager = Role::firstOrCreate(
            ['name' => 'HR Manager', 'guard_name' => 'web'],
            ['description' => 'Manages employee records, organization, and leave management']
        );

        // Create Accounting Manager Role
        $accountingManager = Role::firstOrCreate(
            ['name' => 'Accounting Manager', 'guard_name' => 'web'],
            ['description' => 'Manages financial records and accounting']
        );

        // Create Production Helper Role
        $productionHelper = Role::firstOrCreate(
            ['name' => 'Production Helper', 'guard_name' => 'web'],
            ['description' => 'Views production callbacks and inventory callbacks (filtered by department)']
        );

        // Define permissions for HR Manager
        $hrPermissions = [
            // Organization
            'view_organization',
            'edit_organization',
            'create_organization',
            'delete_organization',
            
            // Employee Management
            'view_employees',
            'create_employee',
            'edit_employee',
            'delete_employee',
            'manage_employee_roles',
            'manage_employee_shifts',
            
            // Leave Management
            'view_leave_applications',
            'approve_leave',
            'reject_leave',
            'create_leave_type',
            'edit_leave_type',
            'delete_leave_type',
            'view_leave_balance',
        ];

        // Define permissions for Accounting Manager
        $accountingPermissions = [
            // Accounting
            'view_all_accounting', // Can see all production regardless of department
            'view_gl_accounts',
            'create_gl_entry',
            'view_transactions',
            'view_reports',
            'export_accounting_data',
            
            // Payroll (if needed)
            'view_payroll',
            'process_payroll',
            'view_salary_history',
        ];

        // Define permissions for Production Helper
        $productionHelperPermissions = [
            'view_production_callbacks',
            'view_dispatch_callbacks',
            'view_inventory_callbacks',
            // All filtered by department
        ];

        // Create all permissions if they don't exist
        foreach (array_merge($hrPermissions, $accountingPermissions, $productionHelperPermissions) as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => str_replace('_', ' ', ucwords($permission))]
            );
        }

        // Assign permissions to roles
        $hrManager->syncPermissions($hrPermissions);
        $accountingManager->syncPermissions($accountingPermissions);
        $productionHelper->syncPermissions($productionHelperPermissions);

        echo "Created HR Manager, Accounting Manager, and Production Helper roles with permissions\n";
    }
}
