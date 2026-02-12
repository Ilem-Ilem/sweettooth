<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear existing roles for this guard to avoid conflicts
        Role::where('guard_name', $guard)->delete();

        // ===== SUPER ADMIN ROLES (Full Access) =====
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => $guard,
            'description' => 'Full system access with all permissions',
            'display_order' => 1,
            'level' => 5,
            'is_protected' => true,
        ]);

        $md = Role::create([
            'name' => 'MD',
            'guard_name' => $guard,
            'description' => 'Managing Director - Executive level',
            'display_order' => 2,
            'level' => 5,
            'is_protected' => true,
        ]);

        $managingDirector = Role::create([
            'name' => 'Managing Director',
            'guard_name' => $guard,
            'description' => 'Managing Director with full operational control',
            'display_order' => 3,
            'level' => 5,
            'is_protected' => true,
        ]);

        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => $guard,
            'description' => 'Administrative access',
            'display_order' => 4,
            'level' => 4,
            'is_protected' => true,
        ]);

        // Give super admin roles ALL permissions
        $allPermissions = Permission::where('guard_name', $guard)->get();
        foreach ([$superAdmin, $md, $managingDirector, $admin] as $role) {
            $role->syncPermissions($allPermissions);
        }

        // ===== DEPARTMENT MANAGERS =====
        $headOfProduction = Role::create([
            'name' => 'Head of Production',
            'guard_name' => $guard,
            'description' => 'Production department manager',
            'display_order' => 10,
            'level' => 3,
        ]);
        $headOfProduction->syncPermissions([
            'view-production', 'manage-production', 'manage-recipes', 'manage-quality',
            'view-production-reports', 'view-inventory',
            'view-reports', 'view-analytics',
        ]);

        $salesManager = Role::create([
            'name' => 'Sales Manager',
            'guard_name' => $guard,
            'description' => 'Sales department manager',
            'display_order' => 11,
            'level' => 3,
        ]);
        $salesManager->syncPermissions([
            'view-sales', 'process-sales', 'manage-sales', 'manage-refunds', 'manage-discounts',
            'view-sales-reports', 'view-inventory',
            'view-reports', 'view-analytics',
        ]);

        $hrManager = Role::create([
            'name' => 'HR Manager',
            'guard_name' => $guard,
            'description' => 'Human Resources manager',
            'display_order' => 12,
            'level' => 3,
        ]);
        $hrManager->syncPermissions([
            'manage-organization', 'view-employees', 'manage-employees',
            'view-departments', 'manage-departments',
            'manage-roles-assignments', 'manage-staff-schedule',
            'manage-leave', 'manage-payroll', 'view-hr-reports',
            'view-reports', 'view-analytics',
        ]);

        $inventoryManager = Role::create([
            'name' => 'Inventory Manager',
            'guard_name' => $guard,
            'description' => 'Inventory and stock management',
            'display_order' => 13,
            'level' => 3,
        ]);
        $inventoryManager->syncPermissions([
            'view-inventory', 'manage-inventory', 'manage-suppliers',
            'manage-purchases', 'manage-stock-takes', 'view-inventory-reports',
            'view-reports', 'view-analytics',
        ]);

        $accountingManager = Role::create([
            'name' => 'Accounting Manager',
            'guard_name' => $guard,
            'description' => 'Accounting and financial management',
            'display_order' => 14,
            'level' => 3,
        ]);
        $accountingManager->syncPermissions([
            'view-accounting', 'manage-accounting', 'reconcile-accounts',
            'manage-bank-accounts', 'manage-accounting-periods', 'view-financial-reports',
            'view-reports', 'view-analytics',
        ]);

        // ===== SUPERVISORS =====
        $productionSupervisor = Role::create([
            'name' => 'Production Supervisor',
            'guard_name' => $guard,
            'description' => 'Production team supervisor',
            'display_order' => 20,
            'level' => 2,
        ]);
        $productionSupervisor->syncPermissions([
            'view-production', 'manage-production', 'view-production-reports', 'view-inventory',
            'view-reports',
        ]);

        $salesSupervisor = Role::create([
            'name' => 'Sales Supervisor',
            'guard_name' => $guard,
            'description' => 'Sales team supervisor',
            'display_order' => 21,
            'level' => 2,
        ]);
        $salesSupervisor->syncPermissions([
            'view-sales', 'process-sales', 'view-sales-reports', 'view-inventory',
            'view-reports',
        ]);

        $inventorySupervisor = Role::create([
            'name' => 'Inventory Supervisor',
            'guard_name' => $guard,
            'description' => 'Inventory team supervisor',
            'display_order' => 22,
            'level' => 2,
        ]);
        $inventorySupervisor->syncPermissions([
            'view-inventory', 'manage-inventory', 'view-inventory-reports', 'view-reports',
        ]);

        $hrOfficer = Role::create([
            'name' => 'HR Officer',
            'guard_name' => $guard,
            'description' => 'HR officer',
            'display_order' => 23,
            'level' => 2,
        ]);
        $hrOfficer->syncPermissions([
            'view-employees', 'manage-employees', 'manage-leave', 'view-hr-reports',
        ]);

        $accountant = Role::create([
            'name' => 'Accountant',
            'guard_name' => $guard,
            'description' => 'Accounting operations',
            'display_order' => 24,
            'level' => 2,
        ]);
        $accountant->syncPermissions([
            'view-accounting', 'reconcile-accounts', 'view-financial-reports', 'view-reports',
        ]);

        // ===== STAFF =====
        $productionStaff = Role::create([
            'name' => 'Production Staff',
            'guard_name' => $guard,
            'description' => 'Production staff',
            'display_order' => 30,
            'level' => 1,
        ]);
        $productionStaff->syncPermissions([
            'view-production', 'view-production-reports',
        ]);

        $salesStaff = Role::create([
            'name' => 'Sales Staff',
            'guard_name' => $guard,
            'description' => 'Sales staff',
            'display_order' => 31,
            'level' => 1,
        ]);
        $salesStaff->syncPermissions([
            'view-sales', 'process-sales',
        ]);

        $inventoryStaff = Role::create([
            'name' => 'Inventory Staff',
            'guard_name' => $guard,
            'description' => 'Inventory staff',
            'display_order' => 32,
            'level' => 1,
        ]);
        $inventoryStaff->syncPermissions([
            'view-inventory',
        ]);

        echo '✅ '.Role::where('guard_name', $guard)->count()." roles created successfully with permissions assigned.\n";
    }
}
