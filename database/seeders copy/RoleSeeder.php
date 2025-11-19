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

        // Executive Level Roles
        $managingDirector = Role::create(['name' => 'Managing Director', 'guard_name' => $guard]);
        $managingDirector->givePermissionTo(Permission::where('guard_name', $guard)->get());

        // Management Level Roles
        $headOfProduction = Role::create(['name' => 'Head of Production', 'guard_name' => $guard]);
        $headOfProduction->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-department-reports', 'approve-production', 'manage-staff-schedule', 'view-analytics',
            'view-stock-levels', 'view-employees', 'view-departments',
        ]);

        $salesManager = Role::create(['name' => 'Sales Manager', 'guard_name' => $guard]);
        $salesManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-department-reports', 'manage-staff-schedule', 'view-analytics',
            'view-stock-levels', 'view-employees', 'view-departments',
        ]);

        $hrManager = Role::create(['name' => 'HR Manager', 'guard_name' => $guard]);
        $hrManager->givePermissionTo([
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'view-departments', 'view-branches', 'view-roles', 'assign-roles',
            'view-department-reports', 'manage-staff-schedule', 'view-analytics',
        ]);

        $inventoryManager = Role::create(['name' => 'Inventory Manager', 'guard_name' => $guard]);
        $inventoryManager->givePermissionTo([
            'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
            'view-department-reports', 'view-analytics', 'view-employees', 'view-departments',
        ]);

        // Department Head / Supervisor Roles
        $chef = Role::create(['name' => 'Chef', 'guard_name' => $guard]);
        $chef->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-stock-levels', 'view-employees', 'view-daily-sales',
        ]);

        $headOfGelato = Role::create(['name' => 'Head of Gelato', 'guard_name' => $guard]);
        $headOfGelato->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'view-stock-levels', 'view-employees',
        ]);

        $confectionariesManager = Role::create(['name' => 'Confectionaries Manager', 'guard_name' => $guard]);
        $confectionariesManager->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production', 'manage-recipes',
            'process-sale', 'view-daily-sales', 'view-stock-levels', 'view-employees',
        ]);

        $tillSupervisor = Role::create(['name' => 'Till Supervisor', 'guard_name' => $guard]);
        $tillSupervisor->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-stock-levels', 'view-employees',
        ]);

        $cornerStoreManager = Role::create(['name' => 'Corner Store Manager', 'guard_name' => $guard]);
        $cornerStoreManager->givePermissionTo([
            'process-sale', 'issue-refund', 'view-daily-sales', 'close-register',
            'view-stock-levels', 'view-employees',
        ]);

        // Staff Level Roles
        $kitchenStaff = Role::create(['name' => 'Kitchen Staff', 'guard_name' => $guard]);
        $kitchenStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $gelatoStaff = Role::create(['name' => 'Gelato Production Staff', 'guard_name' => $guard]);
        $gelatoStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $confectionariesStaff = Role::create(['name' => 'Confectionaries Production Staff', 'guard_name' => $guard]);
        $confectionariesStaff->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'view-stock-levels',
        ]);

        $cashier = Role::create(['name' => 'Cashier', 'guard_name' => $guard]);
        $cashier->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $cornerStoreStaff = Role::create(['name' => 'Corner Store Staff', 'guard_name' => $guard]);
        $cornerStoreStaff->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $confectionariesSalesStaff = Role::create(['name' => 'Confectionaries Sales Staff', 'guard_name' => $guard]);
        $confectionariesSalesStaff->givePermissionTo([
            'process-sale', 'view-daily-sales', 'view-stock-levels',
        ]);

        $stockController = Role::create(['name' => 'Stock Controller', 'guard_name' => $guard]);
        $stockController->givePermissionTo([
            'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
        ]);

        $storeKeeper = Role::create(['name' => 'Store Keeper', 'guard_name' => $guard]);
        $storeKeeper->givePermissionTo([
            'receive-stock', 'view-stock-levels',
        ]);

        $hrOfficer = Role::create(['name' => 'HR Officer', 'guard_name' => $guard]);
        $hrOfficer->givePermissionTo([
            'view-employees', 'create-employees', 'edit-employees',
            'view-departments', 'view-branches',
        ]);

        $this->command->info('✅ '.Role::where('guard_name', $guard)->count().' employee roles created successfully with permissions.');
    }
}
