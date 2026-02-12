<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class InventoryStoreRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $inventoryManager = Role::firstOrCreate([
            'name' => 'Inventory Manager',
            'guard_name' => $guard,
        ], [
            'description' => 'Inventory and stock management',
            'display_order' => 13,
            'level' => 3,
        ]);
        $inventoryManager->syncPermissions([
            'view-inventory', 'manage-inventory', 'manage-suppliers',
            'manage-purchases', 'manage-stock-takes', 'view-inventory-reports',
            'view-reports', 'view-analytics',
        ]);

        $inventorySupervisor = Role::firstOrCreate([
            'name' => 'Inventory Supervisor',
            'guard_name' => $guard,
        ], [
            'description' => 'Inventory team supervisor',
            'display_order' => 22,
            'level' => 2,
        ]);
        $inventorySupervisor->syncPermissions([
            'view-inventory', 'manage-inventory', 'view-inventory-reports', 'view-reports',
        ]);

        $inventoryStaff = Role::firstOrCreate([
            'name' => 'Inventory Staff',
            'guard_name' => $guard,
        ], [
            'description' => 'Inventory staff',
            'display_order' => 32,
            'level' => 1,
        ]);
        $inventoryStaff->syncPermissions([
            'view-inventory',
        ]);

        $this->command->info('Inventory roles seeded successfully!');
    }
}
