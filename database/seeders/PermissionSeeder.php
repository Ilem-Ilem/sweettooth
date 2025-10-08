<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'guard_name' => 'employees'
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
                'guard_name' => 'web'
            ]);
        }

        // Create default roles and assign permissions

        // Employee guard roles
        $cashier = Role::create([
            'name' => 'Cashier',
            'guard_name' => 'employees'
        ]);
        $cashier->givePermissionTo([
            'view-employee-dashboard',
            'view-profile',
            'edit-profile',
            'view-orders',
            'create-orders',
            'process-orders',
            'view-products',
            'view-customers',
        ]);

        $inventoryManager = Role::create([
            'name' => 'Inventory Manager',
            'guard_name' => 'employees'
        ]);
        $inventoryManager->givePermissionTo([
            'view-employee-dashboard',
            'view-profile',
            'edit-profile',
            'view-products',
            'create-products',
            'edit-products',
            'manage-inventory',
            'view-reports',
            'generate-reports',
        ]);

        $branchManager = Role::create([
            'name' => 'Branch Manager',
            'guard_name' => 'employees'
        ]);
        $branchManager->givePermissionTo(Permission::where('guard_name', 'employees')->pluck('name'));

        // Web guard roles
        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web'
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
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'web')->pluck('name'));
    }
}
