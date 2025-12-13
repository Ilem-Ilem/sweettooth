<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates standardized permissions for the 'employees' guard with proper categorization.
     * All permissions follow the pattern: verb-noun (e.g., view-employees, create-roles)
     */
    public function run(): void
    {
        $guard = 'employees';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // System/Admin Permissions (Protected)
        $systemPermissions = [
            // Role Management
            ['name' => 'view-roles', 'description' => 'View all roles and their permissions', 'category' => 'system', 'protected' => true],
            ['name' => 'create-roles', 'description' => 'Create new roles', 'category' => 'system', 'protected' => true],
            ['name' => 'edit-roles', 'description' => 'Edit role details and permissions', 'category' => 'system', 'protected' => true],
            ['name' => 'delete-roles', 'description' => 'Delete custom roles', 'category' => 'system', 'protected' => true],
            ['name' => 'assign-roles', 'description' => 'Assign roles to users', 'category' => 'system', 'protected' => true],

            // Permission Management
            ['name' => 'view-permissions', 'description' => 'View all permissions', 'category' => 'system', 'protected' => true],
            ['name' => 'create-permissions', 'description' => 'Create new permissions', 'category' => 'system', 'protected' => true],
            ['name' => 'edit-permissions', 'description' => 'Edit permission details', 'category' => 'system', 'protected' => true],
            ['name' => 'delete-permissions', 'description' => 'Delete custom permissions', 'category' => 'system', 'protected' => true],

            // Branch Management
            ['name' => 'view-branches', 'description' => 'View all branches', 'category' => 'system', 'protected' => false],
            ['name' => 'create-branches', 'description' => 'Create new branches', 'category' => 'system', 'protected' => false],
            ['name' => 'edit-branches', 'description' => 'Edit branch information', 'category' => 'system', 'protected' => false],
            ['name' => 'delete-branches', 'description' => 'Delete branches', 'category' => 'system', 'protected' => false],

            // Settings
            ['name' => 'manage-settings', 'description' => 'Manage system settings', 'category' => 'system', 'protected' => false],
            ['name' => 'view-audit-logs', 'description' => 'View system audit logs', 'category' => 'system', 'protected' => false],
        ];

        // Employee/HR Permissions
        $hrPermissions = [
            ['name' => 'view-employees', 'description' => 'View employee list and details', 'category' => 'hr', 'protected' => false],
            ['name' => 'create-employees', 'description' => 'Create new employees', 'category' => 'hr', 'protected' => false],
            ['name' => 'edit-employees', 'description' => 'Edit employee information', 'category' => 'hr', 'protected' => false],
            ['name' => 'delete-employees', 'description' => 'Delete employee records', 'category' => 'hr', 'protected' => false],
            ['name' => 'view-departments', 'description' => 'View departments', 'category' => 'hr', 'protected' => false],
            ['name' => 'manage-staff-schedule', 'description' => 'Manage employee schedules and shifts', 'category' => 'hr', 'protected' => false],
            ['name' => 'manage-leave', 'description' => 'Manage employee leave and allocations', 'category' => 'hr', 'protected' => false],
            ['name' => 'approve-leave', 'description' => 'Approve or reject leave requests', 'category' => 'hr', 'protected' => false],
        ];

        // Production Permissions
        $productionPermissions = [
            ['name' => 'view-production-queue', 'description' => 'View production queue and items', 'category' => 'production', 'protected' => false],
            ['name' => 'create-production', 'description' => 'Create production requests', 'category' => 'production', 'protected' => false],
            ['name' => 'start-production', 'description' => 'Start production for queued items', 'category' => 'production', 'protected' => false],
            ['name' => 'complete-production', 'description' => 'Mark production as complete', 'category' => 'production', 'protected' => false],
            ['name' => 'approve-production', 'description' => 'Approve completed production', 'category' => 'production', 'protected' => false],
            ['name' => 'manage-recipes', 'description' => 'Create and edit recipes', 'category' => 'production', 'protected' => false],
            ['name' => 'view-production-reports', 'description' => 'View production reports and analytics', 'category' => 'production', 'protected' => false],
        ];

        // Inventory Permissions
        $inventoryPermissions = [
            ['name' => 'view-stock-levels', 'description' => 'View current stock levels', 'category' => 'inventory', 'protected' => false],
            ['name' => 'receive-stock', 'description' => 'Receive and log incoming inventory', 'category' => 'inventory', 'protected' => false],
            ['name' => 'transfer-stock', 'description' => 'Transfer stock between locations', 'category' => 'inventory', 'protected' => false],
            ['name' => 'adjust-inventory', 'description' => 'Adjust inventory counts and values', 'category' => 'inventory', 'protected' => false],
            ['name' => 'create-purchase-order', 'description' => 'Create purchase orders', 'category' => 'inventory', 'protected' => false],
            ['name' => 'approve-purchase-order', 'description' => 'Approve purchase orders', 'category' => 'inventory', 'protected' => false],
            ['name' => 'view-inventory-reports', 'description' => 'View inventory reports and analytics', 'category' => 'inventory', 'protected' => false],
        ];

        // Sales/POS Permissions
        $salesPermissions = [
            ['name' => 'process-sale', 'description' => 'Process sales transactions', 'category' => 'sales', 'protected' => false],
            ['name' => 'view-daily-sales', 'description' => 'View daily sales data', 'category' => 'sales', 'protected' => false],
            ['name' => 'issue-refund', 'description' => 'Issue refunds for transactions', 'category' => 'sales', 'protected' => false],
            ['name' => 'close-register', 'description' => 'Close and reconcile cash register', 'category' => 'sales', 'protected' => false],
            ['name' => 'manage-customers', 'description' => 'Manage customer records', 'category' => 'sales', 'protected' => false],
            ['name' => 'view-sales-reports', 'description' => 'View sales reports and analytics', 'category' => 'sales', 'protected' => false],
        ];

        // Reports & Analytics Permissions
        $reportPermissions = [
            ['name' => 'view-analytics', 'description' => 'View system analytics and dashboards', 'category' => 'reports', 'protected' => false],
            ['name' => 'view-department-reports', 'description' => 'View department-specific reports', 'category' => 'reports', 'protected' => false],
            ['name' => 'generate-reports', 'description' => 'Generate custom reports', 'category' => 'reports', 'protected' => false],
            ['name' => 'export-data', 'description' => 'Export data to external formats', 'category' => 'reports', 'protected' => false],
        ];

        // Callbacks & Quality Permissions
        $qualityPermissions = [
            ['name' => 'view-callbacks', 'description' => 'View quality callbacks and issues', 'category' => 'quality', 'protected' => false],
            ['name' => 'create-callback', 'description' => 'Create quality callbacks', 'category' => 'quality', 'protected' => false],
            ['name' => 'approve-callbacks', 'description' => 'Approve quality callbacks', 'category' => 'quality', 'protected' => false],
            ['name' => 'resolve-callbacks', 'description' => 'Resolve and close callbacks', 'category' => 'quality', 'protected' => false],
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $systemPermissions,
            $hrPermissions,
            $productionPermissions,
            $inventoryPermissions,
            $salesPermissions,
            $reportPermissions,
            $qualityPermissions
        );

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission['name'],
                    'guard_name' => $guard,
                ],
                [
                    'description' => $permission['description'] ?? null,
                    'category' => $permission['category'] ?? 'general',
                    'is_protected' => $permission['protected'] ?? false,
                ]
            );
        }

        $this->command->info('✅ ' . count($allPermissions) . ' permissions created/updated successfully.');
        
        // Display summary
        $summary = [
            'System' => count($systemPermissions),
            'HR' => count($hrPermissions),
            'Production' => count($productionPermissions),
            'Inventory' => count($inventoryPermissions),
            'Sales' => count($salesPermissions),
            'Reports' => count($reportPermissions),
            'Quality' => count($qualityPermissions),
        ];

        foreach ($summary as $category => $count) {
            $this->command->info("  {$category}: {$count} permissions");
        }
    }
}
