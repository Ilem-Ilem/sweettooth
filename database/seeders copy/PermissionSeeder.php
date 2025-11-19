<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for employee guard
        $employeePermissions = [
            // Dashboard permissions
            'view-employee-dashboard',
            'view-analytics',

            // Profile permissions
            'view-profile',
            'edit-profile',

            // Production permissions
            'view-production-queue',
            'start-production',
            'complete-production',
            'approve-production',
            'manage-recipes',

            // Sales permissions
            'process-sale',
            'issue-refund',
            'view-daily-sales',
            'close-register',

            // Inventory permissions
            'receive-stock',
            'transfer-stock',
            'adjust-inventory',
            'view-stock-levels',

            // Employee permissions
            'view-employees',
            'create-employees',
            'edit-employees',
            'delete-employees',
            'assign-roles',

            // Department & Branch permissions
            'view-departments',
            'view-branches',
            'view-roles',

            // Reports & Scheduling
            'view-department-reports',
            'manage-staff-schedule',

            // Order permissions
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'process-orders',
            'cancel-orders',

            // Product permissions
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'manage-inventory',

            // Customer permissions
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',

            // Report permissions
            'view-reports',
            'generate-reports',
            'export-reports',

            // Settings permissions
            'view-settings',
            'edit-settings',
        ];

        foreach ($employeePermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'employees',
            ]);
        }

        // Create permissions for web guard (admin/super-admin)
        $webPermissions = [
            // Employee management
            'view-employees',
            'create-employees',
            'edit-employees',
            'delete-employees',

            // Role & Permission management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',

            // Branch management
            'view-branches',
            'create-branches',
            'edit-branches',
            'delete-branches',

            // System settings
            'view-system-settings',
            'edit-system-settings',
            'view-audit-logs',
        ];

        foreach ($webPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create default web guard roles
        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $admin->givePermissionTo([
            'view-employees',
            'create-employees',
            'edit-employees',
            'view-roles',
            'view-permissions',
            'view-branches',
            'create-branches',
            'edit-branches',
        ]);

        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'web')->pluck('name'));

        $this->command->info('✅ '.Permission::count().' permissions created successfully.');
        $this->command->info('✅ '.Role::where('guard_name', 'web')->count().' web roles created successfully.');
    }
}
