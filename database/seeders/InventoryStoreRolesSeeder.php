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

        // ===== INVENTORY MANAGEMENT ROLES =====
        $inventoryManager = Role::firstOrCreate([
            'name' => 'Inventory Manager',
            'guard_name' => $guard,
        ], [
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
            'view_inventory_dashboard',
        ]);

        // ===== STORE/WAREHOUSE ROLES =====
        $storeKeeper = Role::firstOrCreate([
            'name' => 'Store Keeper',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Keeper/Warehouse Staff',
            'display_order' => 47,
        ]);

        $storeKeeper->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'transfer-stock',
            'view-inventory-reports',
            'view_inventory_dashboard',
        ]);

        $warehouseManager = Role::firstOrCreate([
            'name' => 'Warehouse Manager',
            'guard_name' => $guard,
        ], [
            'description' => 'Warehouse Manager with supervisory responsibilities',
            'display_order' => 14,
        ]);

        $warehouseManager->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
            'create-purchase-order', 'view-inventory-reports',
            'view-reorder-levels', 'view-stock-history',
            'view-analytics', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        // ===== STORE MANAGEMENT ROLES =====
        $storeManager = Role::firstOrCreate([
            'name' => 'Store Manager',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Manager overseeing retail operations',
            'display_order' => 15,
        ]);

        $storeManager->givePermissionTo([
            'view-stock-levels', 'transfer-stock', 'view-inventory-reports',
            'view-sales-transactions', 'view-analytics', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        $storeSupervisor = Role::firstOrCreate([
            'name' => 'Store Supervisor',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Supervisor assisting with daily operations',
            'display_order' => 35,
        ]);

        $storeSupervisor->givePermissionTo([
            'view-stock-levels', 'transfer-stock', 'view-inventory-reports',
            'view-sales-transactions', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        $cashier = Role::firstOrCreate([
            'name' => 'Cashier',
            'guard_name' => $guard,
        ], [
            'description' => 'Cashier handling sales transactions',
            'display_order' => 49,
        ]);

        $cashier->givePermissionTo([
            'view-stock-levels', 'view-sales-transactions', 'view-dashboard',
        ]);

        $inventoryClerk = Role::firstOrCreate([
            'name' => 'Inventory Clerk',
            'guard_name' => $guard,
        ], [
            'description' => 'Inventory Clerk for data entry and basic inventory tasks',
            'display_order' => 48,
        ]);

        $inventoryClerk->givePermissionTo([
            'view-stock-levels', 'receive-stock', 'view-inventory-reports',
            'view-reorder-levels', 'view-stock-history',
            'view_inventory_dashboard',
        ]);

        // ===== STORE MANAGEMENT ROLES =====
        $storeManager = Role::firstOrCreate([
            'name' => 'Store Manager',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Manager overseeing retail operations',
            'display_order' => 15,
        ]);

        $storeManager->givePermissionTo([
            'view-stock-levels', 'transfer-stock', 'view-inventory-reports',
            'view-sales-transactions', 'view-analytics', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        $storeSupervisor = Role::firstOrCreate([
            'name' => 'Store Supervisor',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Supervisor assisting with daily operations',
            'display_order' => 35,
        ]);

        $storeSupervisor->givePermissionTo([
            'view-stock-levels', 'transfer-stock', 'view-inventory-reports',
            'view-sales-transactions', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        $cashier = Role::firstOrCreate([
            'name' => 'Cashier',
            'guard_name' => $guard,
        ], [
            'description' => 'Cashier handling sales transactions',
            'display_order' => 49,
        ]);

        $cashier->givePermissionTo([
            'view-stock-levels', 'view-sales-transactions', 'view-dashboard',
        ]);

        $storeSupervisor = Role::firstOrCreate([
            'name' => 'Store Supervisor',
            'guard_name' => $guard,
        ], [
            'description' => 'Store Supervisor assisting with daily operations',
            'display_order' => 35,
        ]);

        $storeSupervisor->givePermissionTo([
            'view-stock-levels', 'transfer-stock', 'view-inventory-reports',
            'view-sales-transactions', 'view-dashboard', 'view-department-reports',
            'view_inventory_dashboard',
        ]);

        $cashier = Role::firstOrCreate([
            'name' => 'Cashier',
            'guard_name' => $guard,
        ], [
            'description' => 'Cashier handling sales transactions',
            'display_order' => 49,
        ]);

        $cashier->givePermissionTo([
            'view-stock-levels', 'view-sales-transactions', 'view-dashboard',
        ]);

        $this->command->info('Inventory and Store Management roles seeded successfully!');
    }
}
