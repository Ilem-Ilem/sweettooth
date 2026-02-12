<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = Permission::where('guard_name', $guard)->get();

        $rolePermissions = [
            'Super Admin' => ['*'],
            'MD' => ['*'],
            'Managing Director' => ['*'],
            'Admin' => ['*'],

            'Head of Production' => [
                'view-production', 'manage-production', 'manage-recipes', 'manage-quality',
                'view-production-reports', 'view-inventory',
                'view-reports', 'view-analytics',
            ],
            'Sales Manager' => [
                'view-sales', 'process-sales', 'manage-sales', 'manage-refunds', 'manage-discounts',
                'view-sales-reports', 'view-inventory',
                'view-reports', 'view-analytics',
            ],
            'HR Manager' => [
                'manage-organization', 'view-employees', 'manage-employees',
                'view-departments', 'manage-departments',
                'manage-roles-assignments', 'manage-staff-schedule',
                'manage-leave', 'manage-payroll', 'view-hr-reports',
                'view-reports', 'view-analytics',
            ],
            'Inventory Manager' => [
                'view-inventory', 'manage-inventory', 'manage-suppliers',
                'manage-purchases', 'manage-stock-takes', 'view-inventory-reports',
                'view-reports', 'view-analytics',
            ],
            'Accounting Manager' => [
                'view-accounting', 'manage-accounting', 'reconcile-accounts',
                'manage-bank-accounts', 'manage-accounting-periods', 'view-financial-reports',
                'view-reports', 'view-analytics',
            ],

            'Production Supervisor' => [
                'view-production', 'manage-production', 'view-inventory',
                'view-reports',
            ],
            'Sales Supervisor' => [
                'view-sales', 'process-sales', 'view-sales-reports', 'view-inventory',
                'view-reports',
            ],
            'Inventory Supervisor' => [
                'view-inventory', 'manage-inventory', 'view-inventory-reports', 'view-reports',
            ],
            'HR Officer' => [
                'view-employees', 'manage-employees', 'manage-leave', 'view-hr-reports',
            ],
            'Accountant' => [
                'view-accounting', 'reconcile-accounts', 'view-financial-reports', 'view-reports',
            ],

            'Production Staff' => [
                'view-production',
            ],
            'Sales Staff' => [
                'view-sales', 'process-sales',
            ],
            'Inventory Staff' => [
                'view-inventory',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
            if (! $role) {
                continue;
            }

            if (in_array('*', $permissions, true)) {
                $role->syncPermissions($allPermissions);
            } else {
                $role->syncPermissions($permissions);
            }
        }

        $this->command->info('Roles permissions synchronized successfully!');
    }
}
