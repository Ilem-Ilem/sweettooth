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
        ]);

        $md = Role::create([
            'name' => 'MD',
            'guard_name' => $guard,
            'description' => 'Managing Director - Executive level',
            'display_order' => 2,
        ]);

        $managingDirector = Role::create([
            'name' => 'Managing Director',
            'guard_name' => $guard,
            'description' => 'Managing Director with full operational control',
            'display_order' => 3,
        ]);

        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => $guard,
            'description' => 'Administrative access',
            'display_order' => 4,
        ]);

        // Give super admin roles ALL permissions
        $allPermissions = Permission::where('guard_name', $guard)->get();
        foreach ([$superAdmin, $md, $managingDirector, $admin] as $role) {
            $role->givePermissionTo($allPermissions);
        }

        // ===== DEPARTMENT HEAD ROLES =====
        $headOfProduction = Role::create([
            'name' => 'Head of Production',
            'guard_name' => $guard,
            'description' => 'Head of Production department',
            'display_order' => 10,
        ]);
        $headOfProduction->givePermissionTo([
            'view-production-queue', 'create-production-order', 'start-production',
            'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
            'view-production-reports', 'manage-quality-control', 'view-batch-history',
            'edit-production-order', 'cancel-production', 'view-production-cost',
            'view-stock-levels', 'view-employees', 'view-departments',
            'manage-staff-schedule', 'view-analytics', 'view-dashboard',
            'view-department-reports', 'view-hr-reports',
        ]);

        $salesManager = Role::create([
            'name' => 'Sales Manager',
            'guard_name' => $guard,
            'description' => 'Sales Manager',
            'display_order' => 11,
        ]);
        $salesManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-sales-reports', 'manage-sales-discounts', 'view-sales-transactions',
            'edit-sales-transactions', 'void-sales-transactions', 'manage-payment-methods',
            'view-till-records', 'view-stock-levels', 'view-employees', 'view-departments',
            'manage-staff-schedule', 'view-analytics', 'view-dashboard',
            'view-department-reports', 'view-hr-reports',
        ]);

        $hrManager = Role::create([
            'name' => 'HR Manager',
            'guard_name' => $guard,
            'description' => 'Human Resources Manager',
            'display_order' => 12,
        ]);
        $hrManager->givePermissionTo([
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
            'manage-staff-schedule', 'manage-leave', 'approve-leave', 'view-payroll',
            'manage-payroll', 'view-hr-reports', 'manage-roles-assignments',
            'view-employee-details', 'view-analytics', 'view-dashboard',
            'view-department-reports', 'view-branches', 'manage_organization',
        ]);

        $inventoryManager = Role::create([
            'name' => 'Inventory Manager',
            'guard_name' => $guard,
            'description' => 'Inventory and Stock Management',
            'display_order' => 13,
        ]);
        $inventoryManager->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
            'create-purchase-order', 'approve-purchase-order', 'view-inventory-reports',
            'manage-suppliers', 'view-stock-valuation', 'manage-stock-categories',
            'view-reorder-levels', 'manage-reorder-levels', 'write-off-stock',
            'view-stock-history', 'view-production-queue', 'view-sales-transactions',
            'view-analytics', 'view-dashboard', 'view-department-reports',
        ]);

        // ===== SUPERVISOR/TEAM LEAD ROLES =====
        $supervisor = Role::create([
            'name' => 'Supervisor',
            'guard_name' => $guard,
            'description' => 'Team Supervisor',
            'display_order' => 20,
        ]);
        $supervisor->givePermissionTo([
            'view-employees', 'view-departments', 'manage-staff-schedule',
            'view-production-queue', 'start-production', 'complete-production',
            'view-recipes', 'view-stock-levels', 'view-sales-transactions',
            'view-analytics', 'view-dashboard', 'view-department-reports',
        ]);

        $tillSupervisor = Role::create([
            'name' => 'Till Supervisor',
            'guard_name' => $guard,
            'description' => 'Till/Register Supervisor',
            'display_order' => 21,
        ]);
        $tillSupervisor->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-sales-transactions', 'manage-payment-methods', 'view-till-records',
            'view-analytics', 'view-dashboard',
        ]);

        // ===== SPECIALIST ROLES =====
        $chef = Role::create([
            'name' => 'Chef',
            'guard_name' => $guard,
            'description' => 'Production Chef',
            'display_order' => 30,
        ]);
        $chef->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-recipes', 'manage-recipes', 'view-batch-history',
            'view-stock-levels', 'manage-quality-control',
        ]);

        $headOfGelato = Role::create([
            'name' => 'Head of Gelato',
            'guard_name' => $guard,
            'description' => 'Gelato Production Lead',
            'display_order' => 31,
        ]);
        $headOfGelato->givePermissionTo([
            'view-production-queue', 'create-production-order', 'start-production',
            'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
            'view-production-reports', 'manage-quality-control', 'view-batch-history',
            'view-stock-levels',
        ]);

        $confectionariesManager = Role::create([
            'name' => 'Confectioneries Manager',
            'guard_name' => $guard,
            'description' => 'Confectioneries Production Manager',
            'display_order' => 32,
        ]);
        $confectionariesManager->givePermissionTo([
            'view-production-queue', 'create-production-order', 'start-production',
            'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
            'view-production-reports', 'manage-quality-control', 'view-batch-history',
            'view-stock-levels',
        ]);

        // ===== STANDARD EMPLOYEE ROLES =====
        $productionStaff = Role::create([
            'name' => 'Kitchen Staff',
            'guard_name' => $guard,
            'description' => 'Kitchen/Production Staff',
            'display_order' => 40,
        ]);
        $productionStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-recipes',
        ]);

        $gelatoStaff = Role::create([
            'name' => 'Gelato Production Staff',
            'guard_name' => $guard,
            'description' => 'Gelato Production Staff',
            'display_order' => 41,
        ]);
        $gelatoStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-recipes',
        ]);

        $confectionariesStaff = Role::create([
            'name' => 'Confectioneries Production Staff',
            'guard_name' => $guard,
            'description' => 'Confectioneries Production Staff',
            'display_order' => 42,
        ]);
        $confectionariesStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-recipes',
        ]);

        $cashier = Role::create([
            'name' => 'Cashier',
            'guard_name' => $guard,
            'description' => 'Sales Cashier',
            'display_order' => 43,
        ]);
        $cashier->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-sales-transactions',
            'view-till-records', 'view-stock-levels',
        ]);

        $cornerStoreManager = Role::create([
            'name' => 'Corner Store Manager',
            'guard_name' => $guard,
            'description' => 'Corner Store Manager',
            'display_order' => 44,
        ]);
        $cornerStoreManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-sales-reports', 'view-sales-transactions', 'manage-payment-methods',
            'view-till-records', 'view-stock-levels', 'receive-stock',
        ]);

        $cornerStoreStaff = Role::create([
            'name' => 'Corner Store Staff',
            'guard_name' => $guard,
            'description' => 'Corner Store Staff',
            'display_order' => 45,
        ]);
        $cornerStoreStaff->givePermissionTo([
            'view-sales-dashboard', 'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $stockController = Role::create([
            'name' => 'Stock Controller',
            'guard_name' => $guard,
            'description' => 'Stock/Inventory Controller',
            'display_order' => 46,
        ]);
        $stockController->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
            'view-inventory-reports', 'view-reorder-levels', 'view-stock-history',
            'view-stock-valuation',
        ]);

        $storeKeeper = Role::create([
            'name' => 'Store Keeper',
            'guard_name' => $guard,
            'description' => 'Store Keeper/Warehouse Staff',
            'display_order' => 47,
        ]);
        $storeKeeper->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'transfer-stock',
            'view-inventory-reports',
        ]);

        $hrOfficer = Role::create([
            'name' => 'HR Officer',
            'guard_name' => $guard,
            'description' => 'HR Officer',
            'display_order' => 48,
        ]);
        $hrOfficer->givePermissionTo([
            'view-employees', 'view-departments', 'view-payroll',
            'view-hr-reports', 'manage-leave', 'view-employee-details',
        ]);

        // ===== GENERIC EMPLOYEE ROLE =====
        $employee = Role::create([
            'name' => 'Employee',
            'guard_name' => $guard,
            'description' => 'Standard Employee',
            'display_order' => 50,
        ]);
        $employee->givePermissionTo([
            'view-dashboard', 'view-activity-timeline',
        ]);

        // ===== VIEWER/REPORTING ONLY ROLE =====
        $viewer = Role::create([
            'name' => 'Viewer',
            'guard_name' => $guard,
            'description' => 'Read-only access to reports and dashboards',
            'display_order' => 99,
        ]);
        $viewer->givePermissionTo([
            'view-dashboard', 'view-analytics', 'view-sales-reports',
            'view-inventory-reports', 'view-hr-reports', 'view-department-reports',
            'export-reports', 'view-kpi-metrics',
        ]);

        echo '✅ '.Role::where('guard_name', $guard)->count()." roles created successfully with permissions assigned.\n";
    }
}
