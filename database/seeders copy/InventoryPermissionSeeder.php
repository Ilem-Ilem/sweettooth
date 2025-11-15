<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define inventory permissions
        $permissions = [
            // Item Management
            'view-items',
            'create-items',
            'edit-items',
            'delete-items',

            // Purchase Management
            'view-purchases',
            'create-purchases',
            'edit-purchases',
            'delete-purchases',
            'approve-purchases',

            // Stock Management
            'view-stocks',
            'adjust-stocks',
            'transfer-stocks',
            'view-stock-movements',

            // Item Request Management
            'view-item-requests',
            'create-item-requests',
            'edit-item-requests',
            'approve-item-requests',
            'reject-item-requests',

            // Item Dispatch Management
            'view-item-dispatches',
            'create-item-dispatches',
            'receive-item-dispatches',

            // Stock Take Management
            'view-stock-takes',
            'create-stock-takes',
            'conduct-stock-takes',
            'verify-stock-takes',

            // Health Check Management
            'view-health-checks',
            'create-health-checks',
            'edit-health-checks',

            // Reports
            'view-inventory-reports',
            'export-inventory-reports',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - Full access to all inventory features
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'view-items', 'create-items', 'edit-items', 'delete-items',
                'view-purchases', 'create-purchases', 'edit-purchases', 'delete-purchases', 'approve-purchases',
                'view-stocks', 'adjust-stocks', 'transfer-stocks', 'view-stock-movements',
                'view-item-requests', 'create-item-requests', 'edit-item-requests', 'approve-item-requests', 'reject-item-requests',
                'view-item-dispatches', 'create-item-dispatches', 'receive-item-dispatches',
                'view-stock-takes', 'create-stock-takes', 'conduct-stock-takes', 'verify-stock-takes',
                'view-health-checks', 'create-health-checks', 'edit-health-checks',
                'view-inventory-reports', 'export-inventory-reports',
            ]);
        }

        // Managing Director - Full access except deletions
        $managingDirector = Role::where('name', 'managing-director')->first();
        if ($managingDirector) {
            $managingDirector->givePermissionTo([
                'view-items', 'create-items', 'edit-items',
                'view-purchases', 'create-purchases', 'edit-purchases', 'approve-purchases',
                'view-stocks', 'adjust-stocks', 'transfer-stocks', 'view-stock-movements',
                'view-item-requests', 'approve-item-requests', 'reject-item-requests',
                'view-item-dispatches', 'create-item-dispatches',
                'view-stock-takes', 'verify-stock-takes',
                'view-health-checks',
                'view-inventory-reports', 'export-inventory-reports',
            ]);
        }

        // Inventory Manager - Full inventory control
        $inventoryManager = Role::where('name', 'inventory-manager')->first();
        if ($inventoryManager) {
            $inventoryManager->givePermissionTo([
                'view-items', 'create-items', 'edit-items',
                'view-purchases', 'create-purchases', 'edit-purchases',
                'view-stocks', 'adjust-stocks', 'transfer-stocks', 'view-stock-movements',
                'view-item-requests', 'approve-item-requests', 'reject-item-requests',
                'view-item-dispatches', 'create-item-dispatches',
                'view-stock-takes', 'create-stock-takes', 'conduct-stock-takes', 'verify-stock-takes',
                'view-health-checks', 'create-health-checks', 'edit-health-checks',
                'view-inventory-reports', 'export-inventory-reports',
            ]);
        }

        // Store Keeper - Operational inventory tasks
        $storeKeeper = Role::where('name', 'store-keeper')->first();
        if ($storeKeeper) {
            $storeKeeper->givePermissionTo([
                'view-items',
                'view-purchases',
                'view-stocks', 'view-stock-movements',
                'view-item-requests',
                'view-item-dispatches', 'create-item-dispatches', 'receive-item-dispatches',
                'view-stock-takes', 'conduct-stock-takes',
                'view-health-checks', 'create-health-checks',
            ]);
        }

        // Head of Production - Request and receive items
        $headOfProduction = Role::where('name', 'head-of-production')->first();
        if ($headOfProduction) {
            $headOfProduction->givePermissionTo([
                'view-items',
                'view-stocks',
                'view-item-requests', 'create-item-requests',
                'view-item-dispatches', 'receive-item-dispatches',
                'view-inventory-reports',
            ]);
        }

        // Kitchen Staff - View and request items
        $kitchenStaff = Role::where('name', 'kitchen-staff')->first();
        if ($kitchenStaff) {
            $kitchenStaff->givePermissionTo([
                'view-items',
                'view-item-requests', 'create-item-requests',
                'view-item-dispatches', 'receive-item-dispatches',
            ]);
        }

        // Sales Manager - View inventory for planning
        $salesManager = Role::where('name', 'sales-manager')->first();
        if ($salesManager) {
            $salesManager->givePermissionTo([
                'view-items',
                'view-stocks',
                'view-inventory-reports',
            ]);
        }

        // Cashier - View items only
        $cashier = Role::where('name', 'cashier')->first();
        if ($cashier) {
            $cashier->givePermissionTo([
                'view-items',
            ]);
        }
    }
}
