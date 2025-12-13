<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'employees';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Executive Level Roles
        $managingDirector = Role::create([
            'name' => 'Managing Director',
            'guard_name' => $guard,
            'is_protected' => true,
            'description' => 'Executive level with full operational control. Cannot be deleted.',
            'display_order' => 3,
        ]);
        $managingDirector->givePermissionTo(Permission::where('guard_name', $guard)->get());

        $md = Role::create([
            'name' => 'MD',
            'guard_name' => $guard,
            'is_protected' => true,
            'description' => 'Managing Director - Executive level with full operational control. Cannot be deleted.',
            'display_order' => 2,
        ]);
        $md->givePermissionTo(Permission::where('guard_name', $guard)->get());

        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => $guard,
            'is_protected' => true,
            'description' => 'Full system access with all permissions. Cannot be deleted.',
            'display_order' => 1,
        ]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', $guard)->get());

        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => $guard,
            'is_protected' => true,
            'description' => 'Administrative access to system settings. Cannot be deleted.',
            'display_order' => 4,
        ]);
        $admin->givePermissionTo(Permission::where('guard_name', $guard)->get());

        // Management Level Roles
        $headOfProduction = Role::create([
            'name' => 'Head of Production',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 10,
        ]);
        $headOfProduction->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-department-reports', 'approve-production', 'manage-staff-schedule', 'view-analytics',
            'view-stock-levels', 'view-employees', 'view-departments',
        ]);

        $salesManager = Role::create([
            'name' => 'Sales Manager',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 11,
        ]);
        $salesManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-department-reports', 'manage-staff-schedule', 'view-analytics',
            'view-stock-levels', 'view-employees', 'view-departments',
        ]);

        $hrManager = Role::create([
            'name' => 'HR Manager',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 12,
        ]);
        $hrManager->givePermissionTo([
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'view-departments', 'view-branches', 'view-roles', 'assign-roles',
            'view-department-reports', 'manage-staff-schedule', 'view-analytics',
        ]);

        $inventoryManager = Role::create([
            'name' => 'Inventory Manager',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 13,
        ]);
        $inventoryManager->givePermissionTo([
            'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
            'view-department-reports', 'view-analytics', 'view-departments',
        ]);

        // Department Head / Supervisor Roles
        $chef = Role::create([
            'name' => 'Chef',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 20,
        ]);
        $chef->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-stock-levels', 'view-employees', 'view-daily-sales',
        ]);

        $headOfGelato = Role::create([
            'name' => 'Head of Gelato',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 21,
        ]);
        $headOfGelato->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-stock-levels', 'view-employees',
        ]);

        $confectionariesManager = Role::create([
            'name' => 'Confectionaries Manager',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 22,
        ]);
        $confectionariesManager->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'process-sale', 'view-daily-sales', 'view-stock-levels', 'view-employees',
        ]);

        $tillSupervisor = Role::create([
            'name' => 'Till Supervisor',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 23,
        ]);
        $tillSupervisor->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-stock-levels', 'view-employees',
        ]);

        $cornerStoreManager = Role::create([
            'name' => 'Corner Store Manager',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 24,
        ]);
        $cornerStoreManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-stock-levels', 'view-employees',
        ]);

        // Staff Level Roles
        $kitchenStaff = Role::create([
            'name' => 'Kitchen Staff',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 30,
        ]);
        $kitchenStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $gelatoStaff = Role::create([
            'name' => 'Gelato Production Staff',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 31,
        ]);
        $gelatoStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $confectionariesStaff = Role::create([
            'name' => 'Confectionaries Production Staff',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 32,
        ]);
        $confectionariesStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $cashier = Role::create([
            'name' => 'Cashier',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 33,
        ]);
        $cashier->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $cornerStoreStaff = Role::create([
            'name' => 'Corner Store Staff',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 34,
        ]);
        $cornerStoreStaff->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $confectionariesSalesStaff = Role::create([
            'name' => 'Confectionaries Sales Staff',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 35,
        ]);
        $confectionariesSalesStaff->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $stockController = Role::create([
            'name' => 'Stock Controller',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 36,
        ]);
        $stockController->givePermissionTo([
            'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
        ]);

        $storeKeeper = Role::create([
            'name' => 'Store Keeper',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 37,
        ]);
        $storeKeeper->givePermissionTo([
            'receive-stock', 'view-stock-levels',
        ]);

        $hrOfficer = Role::create([
            'name' => 'HR Officer',
            'guard_name' => $guard,
            'is_protected' => false,
            'display_order' => 38,
        ]);
        $hrOfficer->givePermissionTo([
            'view-employees', 'create-employees', 'edit-employees',
            'view-departments', 'view-branches',
        ]);

        $this->command->info('✅ '.Role::where('guard_name', $guard)->count().' employee roles created successfully with permissions.');
    }
}
