<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates comprehensive permissions for all system modules and features.
     * All permissions follow the pattern: verb-noun (e.g., view-employees, create-roles)
     */
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear existing permissions for this guard
        Permission::where('guard_name', $guard)->delete();

        $permissions = [
            // ===== SYSTEM/ADMIN PERMISSIONS (15) =====
            ['name' => 'view-roles', 'description' => 'View all roles', 'category' => 'system'],
            ['name' => 'create-roles', 'description' => 'Create new roles', 'category' => 'system'],
            ['name' => 'edit-roles', 'description' => 'Edit roles', 'category' => 'system'],
            ['name' => 'delete-roles', 'description' => 'Delete roles', 'category' => 'system'],
            ['name' => 'assign-roles', 'description' => 'Assign roles to users', 'category' => 'system'],
            ['name' => 'view-permissions', 'description' => 'View all permissions', 'category' => 'system'],
            ['name' => 'manage-permissions', 'description' => 'Manage permissions', 'category' => 'system'],
            ['name' => 'view-branches', 'description' => 'View all branches', 'category' => 'system'],
            ['name' => 'create-branches', 'description' => 'Create new branches', 'category' => 'system'],
            ['name' => 'edit-branches', 'description' => 'Edit branch information', 'category' => 'system'],
            ['name' => 'delete-branches', 'description' => 'Delete branches', 'category' => 'system'],
            ['name' => 'manage-settings', 'description' => 'Manage system settings', 'category' => 'system'],
            ['name' => 'view-audit-logs', 'description' => 'View audit logs', 'category' => 'system'],
            ['name' => 'view-activity-logs', 'description' => 'View activity logs', 'category' => 'system'],
            ['name' => 'manage-system', 'description' => 'Full system management', 'category' => 'system'],

            // ===== HR/EMPLOYEE PERMISSIONS (16) =====
            ['name' => 'view-employees', 'description' => 'View employee list', 'category' => 'hr'],
            ['name' => 'create-employees', 'description' => 'Create new employees', 'category' => 'hr'],
            ['name' => 'edit-employees', 'description' => 'Edit employee information', 'category' => 'hr'],
            ['name' => 'delete-employees', 'description' => 'Delete employee records', 'category' => 'hr'],
            ['name' => 'view-departments', 'description' => 'View departments', 'category' => 'hr'],
            ['name' => 'create-departments', 'description' => 'Create departments', 'category' => 'hr'],
            ['name' => 'edit-departments', 'description' => 'Edit departments', 'category' => 'hr'],
            ['name' => 'delete-departments', 'description' => 'Delete departments', 'category' => 'hr'],
            ['name' => 'manage-staff-schedule', 'description' => 'Manage employee schedules', 'category' => 'hr'],
            ['name' => 'manage-leave', 'description' => 'Manage employee leave', 'category' => 'hr'],
            ['name' => 'approve-leave', 'description' => 'Approve leave requests', 'category' => 'hr'],
            ['name' => 'view-payroll', 'description' => 'View payroll information', 'category' => 'hr'],
            ['name' => 'manage-payroll', 'description' => 'Manage payroll', 'category' => 'hr'],
            ['name' => 'view-hr-reports', 'description' => 'View HR reports', 'category' => 'hr'],
            ['name' => 'manage-roles-assignments', 'description' => 'Manage employee role assignments', 'category' => 'hr'],
            ['name' => 'view-employee-details', 'description' => 'View detailed employee information', 'category' => 'hr'],

            // ===== PRODUCTION PERMISSIONS (14) =====
            ['name' => 'view-production-queue', 'description' => 'View production queue', 'category' => 'production'],
            ['name' => 'create-production-order', 'description' => 'Create production orders', 'category' => 'production'],
            ['name' => 'start-production', 'description' => 'Start production batches', 'category' => 'production'],
            ['name' => 'complete-production', 'description' => 'Complete production batches', 'category' => 'production'],
            ['name' => 'approve-production', 'description' => 'Approve production', 'category' => 'production'],
            ['name' => 'manage-recipes', 'description' => 'Create and edit recipes', 'category' => 'production'],
            ['name' => 'view-recipes', 'description' => 'View recipes', 'category' => 'production'],
            ['name' => 'view-production-reports', 'description' => 'View production analytics', 'category' => 'production'],
            ['name' => 'manage-quality-control', 'description' => 'Manage quality control', 'category' => 'production'],
            ['name' => 'view-batch-history', 'description' => 'View production batch history', 'category' => 'production'],
            ['name' => 'edit-production-order', 'description' => 'Edit production orders', 'category' => 'production'],
            ['name' => 'cancel-production', 'description' => 'Cancel production orders', 'category' => 'production'],
            ['name' => 'view-production-cost', 'description' => 'View production costs', 'category' => 'production'],
            ['name' => 'manage-production-settings', 'description' => 'Manage production settings', 'category' => 'production'],

            // ===== INVENTORY PERMISSIONS (15) =====
            ['name' => 'view-stock-levels', 'description' => 'View stock levels', 'category' => 'inventory'],
            ['name' => 'receive-stock', 'description' => 'Receive inventory', 'category' => 'inventory'],
            ['name' => 'transfer-stock', 'description' => 'Transfer stock between locations', 'category' => 'inventory'],
            ['name' => 'adjust-inventory', 'description' => 'Adjust inventory counts', 'category' => 'inventory'],
            ['name' => 'create-purchase-order', 'description' => 'Create purchase orders', 'category' => 'inventory'],
            ['name' => 'approve-purchase-order', 'description' => 'Approve purchase orders', 'category' => 'inventory'],
            ['name' => 'view-inventory-reports', 'description' => 'View inventory reports', 'category' => 'inventory'],
            ['name' => 'manage-suppliers', 'description' => 'Manage suppliers', 'category' => 'inventory'],
            ['name' => 'view-stock-valuation', 'description' => 'View stock valuation', 'category' => 'inventory'],
            ['name' => 'manage-stock-categories', 'description' => 'Manage stock categories', 'category' => 'inventory'],
            ['name' => 'view-reorder-levels', 'description' => 'View reorder levels', 'category' => 'inventory'],
            ['name' => 'manage-reorder-levels', 'description' => 'Manage reorder levels', 'category' => 'inventory'],
            ['name' => 'write-off-stock', 'description' => 'Write off stock items', 'category' => 'inventory'],
            ['name' => 'view-stock-history', 'description' => 'View stock transaction history', 'category' => 'inventory'],
            ['name' => 'manage-inventory-settings', 'description' => 'Manage inventory settings', 'category' => 'inventory'],

            // ===== SALES PERMISSIONS (12) =====
            ['name' => 'view-sales-dashboard', 'description' => 'Access sales dashboard', 'category' => 'sales'],
            ['name' => 'process-sale', 'description' => 'Process sales transactions', 'category' => 'sales'],
            ['name' => 'issue-refund', 'description' => 'Issue refunds', 'category' => 'sales'],
            ['name' => 'view-daily-sales', 'description' => 'View daily sales', 'category' => 'sales'],
            ['name' => 'close-register', 'description' => 'Close cash registers', 'category' => 'sales'],
            ['name' => 'view-sales-reports', 'description' => 'View sales analytics', 'category' => 'sales'],
            ['name' => 'manage-sales-discounts', 'description' => 'Manage sales discounts', 'category' => 'sales'],
            ['name' => 'view-sales-transactions', 'description' => 'View sales transactions', 'category' => 'sales'],
            ['name' => 'edit-sales-transactions', 'description' => 'Edit sales transactions', 'category' => 'sales'],
            ['name' => 'void-sales-transactions', 'description' => 'Void sales transactions', 'category' => 'sales'],
            ['name' => 'manage-payment-methods', 'description' => 'Manage payment methods', 'category' => 'sales'],
            ['name' => 'view-till-records', 'description' => 'View till/register records', 'category' => 'sales'],

            // ===== ACCOUNTING PERMISSIONS (14) =====
            ['name' => 'view-chart-accounts', 'description' => 'View chart of accounts', 'category' => 'accounting'],
            ['name' => 'create-accounts', 'description' => 'Create general ledger accounts', 'category' => 'accounting'],
            ['name' => 'edit-accounts', 'description' => 'Edit general ledger accounts', 'category' => 'accounting'],
            ['name' => 'view-gl-entries', 'description' => 'View general ledger entries', 'category' => 'accounting'],
            ['name' => 'create-gl-entries', 'description' => 'Create GL entries', 'category' => 'accounting'],
            ['name' => 'post-gl-entries', 'description' => 'Post GL entries', 'category' => 'accounting'],
            ['name' => 'reverse-gl-entries', 'description' => 'Reverse GL entries', 'category' => 'accounting'],
            ['name' => 'view-accounting-reports', 'description' => 'View accounting reports', 'category' => 'accounting'],
            ['name' => 'reconcile-accounts', 'description' => 'Reconcile bank accounts', 'category' => 'accounting'],
            ['name' => 'manage-bank-accounts', 'description' => 'Manage bank accounts', 'category' => 'accounting'],
            ['name' => 'view-trial-balance', 'description' => 'View trial balance', 'category' => 'accounting'],
            ['name' => 'view-financial-statements', 'description' => 'View financial statements', 'category' => 'accounting'],
            ['name' => 'manage-accounting-period', 'description' => 'Manage accounting periods', 'category' => 'accounting'],
            ['name' => 'view-account-reconciliation', 'description' => 'View account reconciliation', 'category' => 'accounting'],

            // ===== REPORTING/ANALYTICS PERMISSIONS (10) =====
            ['name' => 'view-analytics', 'description' => 'View analytics dashboard', 'category' => 'reporting'],
            ['name' => 'view-department-reports', 'description' => 'View department reports', 'category' => 'reporting'],
            ['name' => 'generate-reports', 'description' => 'Generate custom reports', 'category' => 'reporting'],
            ['name' => 'export-reports', 'description' => 'Export reports to files', 'category' => 'reporting'],
            ['name' => 'schedule-reports', 'description' => 'Schedule automated reports', 'category' => 'reporting'],
            ['name' => 'view-dashboard', 'description' => 'View main dashboard', 'category' => 'reporting'],
            ['name' => 'view-branch-reports', 'description' => 'View branch-specific reports', 'category' => 'reporting'],
            ['name' => 'view-kpi-metrics', 'description' => 'View KPI metrics', 'category' => 'reporting'],
            ['name' => 'export-data', 'description' => 'Export system data', 'category' => 'reporting'],
            ['name' => 'view-activity-timeline', 'description' => 'View activity timeline', 'category' => 'reporting'],

            // ===== DASHBOARD ACCESS PERMISSIONS =====
            ['name' => 'view_inventory_dashboard', 'description' => 'Access inventory dashboard', 'category' => 'dashboard'],
            ['name' => 'view_production_dashboard', 'description' => 'Access production dashboard', 'category' => 'dashboard'],
            ['name' => 'view_sales_dashboard', 'description' => 'Access sales dashboard', 'category' => 'dashboard'],
            ['name' => 'view_corner_store_dashboard', 'description' => 'Access corner store dashboard', 'category' => 'dashboard'],
            ['name' => 'view_hr_dashboard', 'description' => 'Access HR dashboard', 'category' => 'dashboard'],
            ['name' => 'view_admin_dashboard', 'description' => 'Access admin dashboard', 'category' => 'dashboard'],
            ['name' => 'view_super_admin_dashboard', 'description' => 'Access super admin dashboard', 'category' => 'dashboard'],

            // ===== ORGANIZATIONAL MANAGEMENT PERMISSIONS =====
            ['name' => 'manage_organization', 'description' => 'Manage organization (employees, departments)', 'category' => 'organization'],
            ['name' => 'manage_roles', 'description' => 'Manage user roles and permissions', 'category' => 'organization'],
            ['name' => 'manage_branches', 'description' => 'Manage branches', 'category' => 'organization'],
            ['name' => 'manage_settings', 'description' => 'Manage system settings', 'category' => 'organization'],
            ['name' => 'view_reports', 'description' => 'View system reports', 'category' => 'organization'],

            // ===== ACCOUNTING ACCESS PERMISSIONS =====
            ['name' => 'access_accounting', 'description' => 'Access accounting module', 'category' => 'accounting'],
            ['name' => 'view_financial_reports', 'description' => 'View financial reports', 'category' => 'accounting'],
            ['name' => 'manage_accounts', 'description' => 'Manage chart of accounts', 'category' => 'accounting'],
            ['name' => 'manage_periods', 'description' => 'Manage accounting periods', 'category' => 'accounting'],
            ['name' => 'create_journal_entries', 'description' => 'Create journal entries', 'category' => 'accounting'],
            ['name' => 'reconcile_bank_accounts', 'description' => 'Reconcile bank accounts', 'category' => 'accounting'],
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => $guard,
                'description' => $permission['description'] ?? '',
                'category' => $permission['category'] ?? 'general',
            ]);
        }

        echo "✅ " . count($permissions) . " permissions created successfully.\n";
        echo "   Permissions added include: view-sales-dashboard (sales)\n";
    }
}
