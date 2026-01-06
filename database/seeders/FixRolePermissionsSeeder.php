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

        // Define complete permission sets for each role
        $rolePermissions = [
            // ===== SUPER ADMIN ROLES (Full Access) =====
            'Super Admin' => ['*'],
            'MD' => ['*'],
            'Managing Director' => ['*'],
            'Admin' => ['*'],

            // ===== SALES ROLES - COMPLETE PERMISSIONS =====
            'Sales Manager' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                'close-register', 'view-sales-reports', 'manage-sales-discounts',
                'view-sales-transactions', 'edit-sales-transactions', 'void-sales-transactions',
                'manage-payment-methods', 'view-till-records',
                // Staff management
                'view-employees', 'view-departments', 'manage-staff-schedule',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-branch-reports', 'view-kpi-metrics', 'generate-reports',
                'export-reports', 'view-activity-logs', 'view-activity-timeline',
                // Inventory
                'view-stock-levels',
                // Dashboard access
                'view_sales_dashboard', 'view_reports',
            ],

            'Sales Supervisor' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                'close-register', 'view-sales-reports', 'manage-sales-discounts',
                'view-sales-transactions', 'edit-sales-transactions', 'view-till-records',
                'manage-payment-methods',
                // Staff management
                'view-employees', 'view-departments', 'manage-staff-schedule', 'approve-leave',
                'manage-leave',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'generate-reports', 'view-activity-logs', 'view-activity-timeline',
                // Inventory
                'view-stock-levels',
                // Dashboard access
                'view_sales_dashboard',
            ],

            'Sales Associate' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions', 'view-till-records',
                // Inventory
                'view-stock-levels',
                // Reports
                'view-analytics', 'view-dashboard',
            ],

            'Cashier' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions', 'view-till-records',
                // Inventory
                'view-stock-levels',
                // Dashboard access
                'view_sales_dashboard',
            ],

            'Junior Cashier' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-transactions', 'view-till-records',
                // Inventory
                'view-stock-levels',
            ],

            'Corner Store Manager' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                'close-register', 'view-sales-reports', 'view-sales-transactions',
                'manage-payment-methods', 'view-till-records', 'edit-sales-transactions',
                // Inventory management
                'view-stock-levels', 'adjust-inventory', 'view-inventory-reports',
                'receive-stock', 'transfer-stock', 'view-stock-history',
                // Staff management
                'view-employees', 'manage-staff-schedule',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'generate-reports', 'view-activity-logs',
                // Dashboard access
                'view_corner_store_dashboard', 'view_sales_dashboard', 'view_inventory_dashboard',
            ],

            'Corner Store Staff' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-transactions', 'view-till-records',
                // Inventory viewing
                'view-stock-levels',
                // Minimal reporting
                'view-dashboard',
            ],

            'Confectionaries Manager' => [
                // Production operations
                'view-production-queue', 'create-production-order', 'start-production',
                'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
                'view-production-reports', 'view-batch-history', 'edit-production-order',
                'manage-quality-control', 'view-production-cost',
                // Sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales', 'view-sales-reports',
                'view-sales-transactions', 'view-till-records',
                // Inventory
                'view-stock-levels', 'adjust-inventory', 'view-inventory-reports',
                // Staff management
                'view-employees', 'manage-staff-schedule',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'generate-reports', 'view-activity-logs',
                // Dashboard access
                'view_production_dashboard', 'view_sales_dashboard', 'view_reports',
            ],

            'Confectionaries Production Staff' => [
                // Production operations
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'view-production-reports', 'view-batch-history',
                // Inventory viewing
                'view-stock-levels',
                // Dashboard access
                'view_production_dashboard',
            ],

            // ===== PRODUCTION ROLES - COMPLETE PERMISSIONS =====
            'Head of Production' => [
                // Production operations - full access
                'view-production-queue', 'create-production-order', 'start-production',
                'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
                'view-production-reports', 'manage-quality-control', 'view-batch-history',
                'edit-production-order', 'cancel-production', 'view-production-cost',
                'manage-production-settings',
                // Staff management
                'view-employees', 'view-departments', 'manage-staff-schedule', 'manage-leave',
                'approve-leave',
                // Inventory
                'view-stock-levels', 'view-inventory-reports',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-branch-reports', 'view-hr-reports', 'generate-reports',
                'view-activity-logs', 'view-activity-timeline',
                // Dashboard access
                'view_production_dashboard', 'view_reports',
            ],

            'Chef' => [
                // Production operations
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'manage-recipes', 'view-production-reports', 'view-batch-history',
                'approve-production', 'manage-quality-control',
                // Inventory viewing
                'view-stock-levels',
                // Dashboard access
                'view_production_dashboard',
                // Basic reporting
                'view-dashboard', 'view-analytics',
            ],

            'Head of Gelato' => [
                // Production operations
                'view-production-queue', 'create-production-order', 'start-production',
                'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
                'view-production-reports', 'manage-quality-control', 'view-batch-history',
                // Inventory viewing
                'view-stock-levels',
                // Staff management
                'manage-staff-schedule', 'view-employees',
                // Reports
                'view-analytics', 'view-dashboard',
                // Dashboard access
                'view_production_dashboard',
            ],

            'Gelato Production Staff' => [
                // Production operations
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'view-production-reports', 'view-batch-history',
                // Inventory viewing
                'view-stock-levels',
                // Dashboard access
                'view_production_dashboard',
            ],

            'Kitchen Staff' => [
                // Production operations
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'view-production-reports', 'view-batch-history',
                // Inventory viewing
                'view-stock-levels',
                // Dashboard access
                'view_production_dashboard',
            ],

            // ===== HR ROLES - COMPLETE PERMISSIONS =====
            'HR Manager' => [
                // Employee management - full access
                'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
                'view-employee-details', 'manage-staff-schedule',
                // Department management
                'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
                // Leave management
                'manage-leave', 'approve-leave', 'view-hr-reports',
                // Payroll management
                'view-payroll', 'manage-payroll',
                // Role management
                'manage-roles-assignments',
                // Organization management
                'manage_organization', 'manage_branches', 'view-branches',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-branch-reports', 'view-activity-logs', 'view-activity-timeline',
                'generate-reports', 'export-reports', 'view-kpi-metrics',
                // Dashboard access
                'view_hr_dashboard', 'view_reports',
            ],

            'HR Officer' => [
                // Employee management - limited
                'view-employees', 'edit-employees', 'view-employee-details',
                // Department viewing
                'view-departments', 'create-departments', 'edit-departments',
                // Leave management
                'manage-leave', 'approve-leave', 'view-hr-reports',
                // Payroll viewing
                'view-payroll',
                // Organization viewing
                'manage_organization', 'manage_branches', 'view-branches',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-activity-logs',
                // Dashboard access
                'view_hr_dashboard', 'view_reports',
            ],

            // ===== INVENTORY ROLES - COMPLETE PERMISSIONS =====
            'Inventory Manager' => [
                // Stock management - full access
                'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
                'view-inventory-reports', 'view-stock-valuation', 'view-stock-history',
                'write-off-stock', 'manage-stock-categories', 'manage-reorder-levels',
                'view-reorder-levels', 'manage-inventory-settings',
                // Purchase orders
                'create-purchase-order', 'approve-purchase-order',
                // Suppliers
                'manage-suppliers', 'view-suppliers', 'create-suppliers', 'edit-suppliers',
                'delete-suppliers',
                // Production viewing
                'view-production-queue',
                // Sales viewing
                'view-sales-transactions',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-branch-reports', 'generate-reports', 'export-reports',
                'view-activity-logs', 'view-activity-timeline',
                // Dashboard access
                'view_inventory_dashboard', 'view_reports',
            ],

            'Stock Controller' => [
                // Stock management - view and adjust
                'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
                'view-inventory-reports', 'view-stock-valuation', 'view-stock-history',
                'view-reorder-levels', 'manage-reorder-levels',
                // Suppliers viewing
                'view-suppliers',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
            ],

            'Store Keeper' => [
                // Stock management - basic
                'view-stock-levels', 'receive-stock', 'transfer-stock',
                'view-inventory-reports',
                // Dashboard access
                'view_inventory_dashboard',
            ],

            'Warehouse Manager' => [
                // Stock management
                'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
                'view-inventory-reports', 'view-stock-valuation', 'view-stock-history',
                'write-off-stock', 'manage-reorder-levels', 'view-reorder-levels',
                // Suppliers viewing
                'view-suppliers',
                // Staff management
                'view-employees', 'manage-staff-schedule',
                // Analytics
                'view-analytics', 'view-dashboard',
                // Dashboard access
                'view_inventory_dashboard',
            ],

            'Inventory Clerk' => [
                // Stock management - limited
                'view-stock-levels', 'receive-stock', 'view-inventory-reports',
                'view-reorder-levels',
                // Dashboard access
                'view_inventory_dashboard',
            ],

            // ===== MANAGEMENT ROLES - COMPLETE PERMISSIONS =====
            'Supervisor' => [
                // Employee management - team only
                'view-employees', 'view-departments', 'manage-staff-schedule',
                'manage-leave', 'approve-leave',
                // Production viewing
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'view-production-reports',
                // Sales viewing
                'view-sales-transactions',
                // Inventory viewing
                'view-stock-levels',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-activity-logs', 'view-activity-timeline',
                // Dashboard access
                'view_production_dashboard',
            ],

            'Till Supervisor' => [
                // Sales operations
                'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                'close-register', 'view-sales-reports', 'view-sales-transactions',
                'manage-payment-methods', 'view-till-records', 'edit-sales-transactions',
                // Staff management
                'manage-staff-schedule', 'approve-leave', 'manage-leave',
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-activity-logs', 'generate-reports',
                // Dashboard access
                'view_sales_dashboard',
            ],

            'Store Manager' => [
                // Sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions', 'view-till-records',
                // Inventory
                'view-stock-levels', 'adjust-inventory',
                // Staff management
                'view-employees', 'manage-staff-schedule',
                // Analytics
                'view-analytics', 'view-dashboard',
                // Dashboard access
                'view_sales_dashboard', 'view_inventory_dashboard',
            ],

            'Store Supervisor' => [
                // Sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions',
                // Inventory
                'view-stock-levels',
                // Staff management
                'manage-staff-schedule',
                // Analytics
                'view-analytics', 'view-dashboard',
            ],

            // ===== GENERIC ROLES =====
            'Employee' => [
                'view-dashboard', 'view-activity-timeline', 'manage-leave',
                'view-employees', 'view-stock-levels',
            ],

            'Viewer' => [
                'view-dashboard', 'view-analytics', 'view-sales-reports',
                'view-inventory-reports', 'view-hr-reports', 'view-department-reports',
                'export-reports', 'view-kpi-metrics', 'view-activity-timeline',
                'view_reports',
            ],
        ];

        $this->command->info('Syncing role permissions...');
        $bar = $this->command->getOutput()->createProgressBar(count($rolePermissions));
        $bar->start();

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->command->warn("\nRole '$roleName' not found, skipping...");
                $bar->advance();
                continue;
            }

            // Handle wildcard permissions for super admins
            if (in_array('*', $permissions)) {
                $allPermissions = Permission::where('guard_name', $guard)->get();
                $role->syncPermissions($allPermissions);
            } else {
                // Validate and sync permissions
                $validPermissions = Permission::whereIn('name', $permissions)
                    ->where('guard_name', $guard)
                    ->get();

                $invalidPerms = array_diff($permissions, $validPermissions->pluck('name')->toArray());
                if ($invalidPerms) {
                    $this->command->warn("\nRole '$roleName' has invalid permissions: " . implode(', ', $invalidPerms));
                }

                $role->syncPermissions($validPermissions);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        // Display summary
        $this->displaySummary();
    }

    private function displaySummary()
    {
        $this->command->info("\n✅ Role permissions synchronized successfully!\n");
        $this->command->info('Role Permission Summary:');
        $this->command->line(str_repeat('=', 80));

        $roles = Role::with('permissions')->orderBy('name')->get();

        foreach ($roles as $role) {
            $permCount = $role->permissions->count();
            $status = $permCount > 0 ? '✓' : '✗';
            $this->command->line(sprintf(
                "%s %-35s: %3d permissions",
                $status,
                $role->name,
                $permCount
            ));
        }

        $this->command->line(str_repeat('=', 80));
    }
}
