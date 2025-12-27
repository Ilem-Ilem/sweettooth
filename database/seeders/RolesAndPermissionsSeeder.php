<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data in development
        if (app()->environment(['local', 'development'])) {
            $this->command->info('Clearing existing roles and assignments...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('role_has_permissions')->truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_permissions')->truncate();
            DB::table('roles')->truncate();
            // Don't truncate permissions - they are created by PermissionSeeder
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Permissions should be created by PermissionSeeder
        $this->command->info('Using existing permissions...');

        // Create roles and assign permissions
        $roles = $this->getRolesWithPermissions();
        $this->command->info('Creating roles and assigning permissions...');

        $roleBar = $this->command->getOutput()->createProgressBar(count($roles));
        $roleBar->start();

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => 'web',
            ]);

            if (isset($roleData['permissions'])) {
                // Handle wildcard permissions for super-admin
                if (strtolower($roleData['name']) === 'super-admin' || strtolower($roleData['name']) === 'super admin') {
                    if (in_array('*', $roleData['permissions'])) {
                        // Give all permissions to super-admin
                        $allPermissions = Permission::all();
                        $role->givePermissionTo($allPermissions);
                    }
                } else {
                    $role->givePermissionTo($roleData['permissions']);
                }
            }

            $roleBar->advance();
        }
        $roleBar->finish();
        $this->command->newLine();

        $this->command->info('✅ Roles and permissions seeded successfully!');
    }

    protected function getRolesWithPermissions(): array
    {
        return [
            [
                'name' => 'Super Admin',
                'permissions' => ['*'], // All permissions
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    // System permissions
                    'view-roles', 'create-roles', 'edit-roles', 'delete-roles', 'assign-roles',
                    'view-permissions', 'manage-permissions', 'view-branches', 'create-branches',
                    'edit-branches', 'delete-branches', 'manage-settings', 'view-audit-logs',
                    'view-activity-logs', 'manage-system',

                    // HR permissions
                    'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
                    'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
                    'manage-staff-schedule', 'manage-leave', 'approve-leave', 'view-payroll',
                    'manage-payroll', 'view-hr-reports', 'manage-roles-assignments', 'view-employee-details',

                    // Production permissions
                    'view-production-queue', 'create-production-order', 'start-production',
                    'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
                    'view-production-reports', 'manage-quality-control', 'view-batch-history',
                    'edit-production-order', 'cancel-production', 'view-production-cost', 'manage-production-settings',

                    // Inventory permissions
                    'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
                    'create-purchase-order', 'approve-purchase-order', 'view-inventory-reports',
                    'manage-suppliers', 'view-stock-valuation', 'manage-stock-categories',
                    'view-reorder-levels', 'manage-reorder-levels', 'write-off-stock', 'view-stock-history',
                    'manage-inventory-settings',

                    // Sales permissions
                    'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                    'close-register', 'view-sales-reports', 'manage-sales-discounts', 'view-sales-transactions',
                    'edit-sales-transactions', 'void-sales-transactions', 'manage-payment-methods', 'view-till-records',

                    // Accounting permissions
                    'view-chart-accounts', 'create-accounts', 'edit-accounts', 'view-gl-entries',
                    'create-gl-entries', 'post-gl-entries', 'reverse-gl-entries', 'view-accounting-reports',
                    'reconcile-accounts', 'manage-bank-accounts', 'view-trial-balance', 'view-financial-statements',
                    'manage-accounting-period', 'view-account-reconciliation',

                    // Reporting permissions
                    'view-analytics', 'view-department-reports', 'generate-reports', 'export-reports',
                    'schedule-reports', 'view-dashboard', 'view-branch-reports', 'view-kpi-metrics',
                    'export-data', 'view-activity-timeline',

                    // Organization management
                    'manage_organization', 'manage_roles', 'manage_branches', 'manage_settings', 'view_reports',

                    // Dashboard access
                    'view_inventory_dashboard', 'view_production_dashboard', 'view_sales_dashboard',
                    'view_corner_store_dashboard', 'view_hr_dashboard', 'view_admin_dashboard',

                    // Accounting access
                    'access_accounting', 'view_financial_reports', 'manage_accounts',
                    'manage_periods', 'create_journal_entries', 'reconcile_bank_accounts',
                ],
            ],
            [
                'name' => 'branch-manager',
                'permissions' => [
                    // Limited employee management (own branch)
                    'view-employees', 'edit-employees',

                    // Branch viewing (own branch only)
                    'view-branches',

                    // Department management (own branch)
                    'view-departments', 'create-departments', 'edit-departments',

                    // Sales management (own branch)
                    'view-sales-dashboard', 'process-sale', 'view-daily-sales', 'view-sales-reports',

                    // Inventory management (own branch)
                    'view-stock-levels', 'adjust-inventory', 'view-inventory-reports',

                    // Production management (own branch)
                    'view-production-queue', 'start-production', 'view-production-reports',

                    // Recipe viewing
                    'view-recipes',

                    // Reports (own branch)
                    'view-analytics', 'generate-reports', 'export-reports',

                    // Leave management (own branch)
                    'manage-leave', 'approve-leave',

                    // Audit (own branch)
                    'view-audit-logs',

                    // Dashboard access
                    'view_inventory_dashboard', 'view_production_dashboard', 'view-sales-dashboard', 'view_sales_dashboard',
                ],
            ],
            [
                'name' => 'supervisor',
                'permissions' => [
                    // Limited employee viewing
                    'view-employees',

                    // Branch viewing
                    'view-branches',

                    // Department viewing
                    'view-departments',

                    // Sales management (own branch, limited editing)
                    'process-sale', 'view-daily-sales', 'view-sales-reports',

                    // Inventory management (viewing only)
                    'view-stock-levels', 'view-inventory-reports',

                    // Production viewing
                    'view-production-queue', 'view-production-reports',

                    // Recipe viewing
                    'view-recipes',

                    // Reports (own branch)
                    'view-analytics', 'generate-reports',

                    // Leave management (own team)
                    'manage-leave', 'approve-leave',

                    // Audit (limited)
                    'view-audit-logs',

                    // Dashboard access
                    'view_inventory_dashboard', 'view_production_dashboard', 'view-sales-dashboard', 'view_sales_dashboard',
                ],
            ],
            [
                'name' => 'employee',
                'permissions' => [
                    // Basic viewing
                    'view-branches',
                    'view-departments',

                    // Inventory viewing
                    'view-stock-levels',

                    // Production viewing
                    'view-production-queue',

                    // Recipe viewing
                    'view-recipes',

                    // Reports (limited)
                    'view-analytics',

                    // Leave management (own)
                    'manage-leave',

                    // Dashboard access
                    'view_inventory_dashboard', 'view_production_dashboard',
                ],
            ],
            [
                'name' => 'HR Manager',
                'permissions' => [
                    // HR permissions
                    'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
                    'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
                    'manage-leave', 'approve-leave', 'view-payroll', 'view-hr-reports',
                    'manage-roles-assignments', 'view-employee-details',

                    // Organization management
                    'manage_organization', 'manage_branches',

                    // Dashboard access
                    'view_hr_dashboard', 'view_inventory_dashboard', 'view_production_dashboard', 'view-sales-dashboard', 'view_sales_dashboard',

                    // Reports
                    'view_reports',
                ],
            ],
            [
                'name' => 'HR Officer',
                'permissions' => [
                    // HR permissions (limited)
                    'view-employees', 'edit-employees',
                    'view-departments', 'create-departments', 'edit-departments',
                    'manage-leave', 'approve-leave', 'view-hr-reports',
                    'view-employee-details',

                    // Organization management
                    'manage_organization', 'manage_branches',

                    // Dashboard access
                    'view_hr_dashboard', 'view_inventory_dashboard', 'view_production_dashboard', 'view-sales-dashboard', 'view_sales_dashboard',

                    // Reports
                    'view_reports',
                ],
            ],
            [
                'name' => 'Accountant',
                'permissions' => [
                    // Accounting permissions
                    'view-chart-accounts', 'create-accounts', 'edit-accounts',
                    'view-gl-entries', 'create-gl-entries', 'post-gl-entries',
                    'view-accounting-reports', 'reconcile-accounts', 'manage-bank-accounts',
                    'view-trial-balance', 'view-financial-statements', 'manage-accounting-period',

                    // Accounting access
                    'access_accounting', 'view_financial_reports', 'manage_accounts',
                    'manage_periods', 'create_journal_entries', 'reconcile_bank_accounts',
                ],
            ],
            [
                'name' => 'Head of Production',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'create-production-order', 'start-production', 'complete-production',
                    'approve-production', 'manage-recipes', 'view-recipes', 'view-production-reports',
                    'view-batch-history', 'edit-production-order', 'cancel-production',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory permissions
                    'view-stock-levels', 'view-inventory-reports',

                    // Reports
                    'view_reports',
                ],
            ],
            [
                'name' => 'Chef',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',
                    'approve-production',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Kitchen Staff',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Head of Gelato',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',
                    'approve-production',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Gelato Production Staff',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Confectionaries Manager',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',
                    'approve-production',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory permissions
                    'view-stock-levels', 'adjust-inventory', 'view-inventory-reports',

                    // Sales permissions
                    'view-sales-dashboard', 'process-sale', 'view-daily-sales', 'view-sales-reports',

                    // Reports
                    'view_reports',
                ],
            ],
            [
                'name' => 'Confectionaries Production Staff',
                'permissions' => [
                    // Production permissions
                    'view-production-queue', 'start-production', 'complete-production',
                    'view-recipes', 'view-production-reports', 'view-batch-history',

                    // Dashboard access
                    'view_production_dashboard',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Confectionaries Sales Staff',
                'permissions' => [
                    // Sales permissions
                    'process-sale', 'view-daily-sales', 'view-sales-reports',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Cashier',
                'permissions' => [
                    // Sales permissions
                    'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                    'view-sales-reports', 'view-sales-transactions',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Sales Manager',
                'permissions' => [
                    // Sales permissions
                    'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                    'close-register', 'view-sales-reports', 'manage-sales-discounts',
                    'view-sales-transactions', 'edit-sales-transactions', 'void-sales-transactions',
                    'manage-payment-methods', 'view-till-records',

                    // Dashboard access
                    'view-sales-dashboard', 'view_sales_dashboard',

                    // Reports
                    'view_reports',
                ],
            ],
            [
                'name' => 'Corner Store Staff',
                'permissions' => [
                    // Sales permissions (limited)
                    'process-sale', 'view-daily-sales', 'view-sales-reports',

                    // Inventory viewing
                    'view-stock-levels',
                ],
            ],
            [
                'name' => 'Corner Store Manager',
                'permissions' => [
                    // Sales permissions
                    'view-sales-dashboard', 'process-sale', 'issue-refund', 'view-daily-sales',
                    'close-register', 'view-sales-reports', 'view-sales-transactions',

                    // Inventory viewing
                    'view-stock-levels', 'adjust-inventory',

                    // Dashboard access
                    'view_corner_store_dashboard', 'view-sales-dashboard', 'view_sales_dashboard', 'view_inventory_dashboard',
                ],
            ],
        ];
    }
}
