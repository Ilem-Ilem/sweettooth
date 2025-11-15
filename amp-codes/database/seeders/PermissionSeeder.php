<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'create.user',
            'read.user',
            'update.user',
            'delete.user',
            
            // Employee management
            'create.employee',
            'read.employee',
            'update.employee',
            'delete.employee',
            
            // Department management
            'create.department',
            'read.department',
            'update.department',
            'delete.department',
            
            // Product management
            'create.product',
            'read.product',
            'update.product',
            'delete.product',
            
            // Recipe management
            'create.recipe',
            'read.recipe',
            'update.recipe',
            'delete.recipe',
            
            // Inventory management
            'create.item',
            'read.item',
            'update.item',
            'delete.item',
            
            // Sales management
            'create.sale',
            'read.sale',
            'update.sale',
            'delete.sale',
            
            // Shift management
            'create.shift',
            'read.shift',
            'update.shift',
            'delete.shift',
            
            // Production
            'create.production',
            'read.production',
            'update.production',
            'delete.production',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Assign all permissions to admin
        $adminRole->syncPermissions($permissions);

        // Assign manager permissions
        $managerPermissions = [
            'read.user', 'update.user',
            'read.employee', 'create.employee', 'update.employee',
            'read.department',
            'read.product', 'create.product', 'update.product',
            'read.recipe', 'create.recipe', 'update.recipe',
            'read.item', 'update.item',
            'read.sale', 'create.sale', 'update.sale',
            'read.shift', 'create.shift', 'update.shift',
            'read.production', 'create.production', 'update.production',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Assign supervisor permissions
        $supervisorPermissions = [
            'read.employee',
            'read.product',
            'read.recipe',
            'read.item',
            'read.sale',
            'read.shift',
            'read.production', 'create.production', 'update.production',
        ];
        $supervisorRole->syncPermissions($supervisorPermissions);

        // Assign employee permissions
        $employeePermissions = [
            'read.product',
            'read.recipe',
            'read.sale',
            'read.shift',
        ];
        $employeeRole->syncPermissions($employeePermissions);
    }
}
