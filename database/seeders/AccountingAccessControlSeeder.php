<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccountingAccessControlSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $superAdminRoleWeb = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        $superAdminRoleEmployees = Role::where('name', 'Super Admin')->where('guard_name', 'employees')->first();
        
        $mdRoleWeb = Role::where('name', 'MD')->where('guard_name', 'web')->first();
        $mdRoleEmployees = Role::where('name', 'MD')->where('guard_name', 'employees')->first();
        
        $adminRoleWeb = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        $adminRoleEmployees = Role::where('name', 'Admin')->where('guard_name', 'employees')->first();

        // Accounting Permissions
        $permissions = [
            // Base Access
            'access_accounting' => 'Access accounting module',
            'view_financial_reports' => 'View financial reports',
            
            // GL Account Management
            'manage_accounts' => 'Manage GL accounts',
            'view_gl_accounts' => 'View GL accounts',
            'create_gl_accounts' => 'Create GL accounts',
            'edit_gl_accounts' => 'Edit GL accounts',
            'delete_gl_accounts' => 'Delete GL accounts',

            // GL Entries & Journal
            'view_gl_entries' => 'View GL entries',
            'create_journal_entries' => 'Create manual journal entries',
            'approve_gl_entries' => 'Approve pending journal entries',
            'reverse_gl_entries' => 'Reverse posted journal entries',
            'post_gl_entries' => 'Post journal entries',

            // Bank & Cash Management
            'view_bank_accounts' => 'View bank accounts',
            'create_bank_accounts' => 'Create bank accounts',
            'edit_bank_accounts' => 'Edit bank accounts',
            'view_daily_bank_positions' => 'View daily bank positions',
            'reconcile_bank_accounts' => 'Reconcile bank accounts',
            'view_cash_positions' => 'View cash positions',
            'record_cash_count' => 'Record physical cash counts',

            // Accounting Periods
            'manage_periods' => 'Manage accounting periods',
            'view_accounting_periods' => 'View accounting periods',
            'create_accounting_periods' => 'Create accounting periods',
            'close_accounting_periods' => 'Close accounting periods',
            'lock_accounting_periods' => 'Lock accounting periods',
            'reopen_accounting_periods' => 'Reopen closed periods',

            // Financial Reports
            'view_general_ledger' => 'View general ledger report',
            'view_trial_balance' => 'View trial balance report',
            'view_balance_sheet' => 'View balance sheet report',
            'view_income_statement' => 'View income statement report',
            'view_cash_flow_statement' => 'View cash flow statement',
            'export_financial_reports' => 'Export financial reports to PDF/Excel',

            // Bank Reconciliation
            'view_bank_reconciliation' => 'View bank reconciliation reports',
            'upload_bank_statements' => 'Upload bank statements',

            // Accounting Dashboard
            'view_accounting_dashboard' => 'View accounting dashboard',
            'view_financial_summary' => 'View financial summary',

            // Analysis & Reporting
            'view_accounting_reports' => 'View accounting reports',
            'view_variance_analysis' => 'View variance analysis',
            'view_aging_reports' => 'View aging reports (AR/AP)',

            // Transaction Linking
            'link_sales_to_gl' => 'Link sales transactions to GL',
            'link_purchases_to_gl' => 'Link purchases to GL',
            'link_payments_to_gl' => 'Link payments to GL',
        ];

        // Create all permissions for both guards
        foreach (['web', 'employees'] as $guard) {
            foreach ($permissions as $permission => $description) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard],
                    ['description' => $description]
                );
            }
        }

        // Get all accounting permissions
        $permissionsWeb = Permission::where('guard_name', 'web')
            ->whereIn('name', array_keys($permissions))
            ->get();

        $permissionsEmployees = Permission::where('guard_name', 'employees')
            ->whereIn('name', array_keys($permissions))
            ->get();

        // Assign ALL accounting permissions to Super Admin (web)
        if ($superAdminRoleWeb) {
            $superAdminRoleWeb->syncPermissions($permissionsWeb);
        }

        // Assign ALL accounting permissions to Super Admin (employees)
        if ($superAdminRoleEmployees) {
            $superAdminRoleEmployees->syncPermissions($permissionsEmployees);
        }

        // Assign ALL accounting permissions to MD (web)
        if ($mdRoleWeb) {
            $mdRoleWeb->syncPermissions($permissionsWeb);
        }

        // Assign ALL accounting permissions to MD (employees)
        if ($mdRoleEmployees) {
            $mdRoleEmployees->syncPermissions($permissionsEmployees);
        }

        // Assign specific accounting permissions to Admin
        $adminPermissionsWeb = Permission::where('guard_name', 'web')
            ->whereIn('name', [
                'access_accounting',
                'view_financial_reports',
                'manage_accounts',
                'view_gl_accounts',
                'create_gl_accounts',
                'edit_gl_accounts',
                'manage_periods',
                'view_accounting_periods',
                'create_accounting_periods',
                'close_accounting_periods',
                'lock_accounting_periods',
                'view_gl_entries',
                'create_journal_entries',
                'view_general_ledger',
                'view_trial_balance',
                'view_balance_sheet',
                'view_income_statement',
                'view_accounting_dashboard',
                'view_financial_summary',
            ])
            ->get();

        $adminPermissionsEmployees = Permission::where('guard_name', 'employees')
            ->whereIn('name', [
                'access_accounting',
                'view_financial_reports',
                'manage_accounts',
                'view_gl_accounts',
                'create_gl_accounts',
                'edit_gl_accounts',
                'manage_periods',
                'view_accounting_periods',
                'create_accounting_periods',
                'close_accounting_periods',
                'lock_accounting_periods',
                'view_gl_entries',
                'create_journal_entries',
                'view_general_ledger',
                'view_trial_balance',
                'view_balance_sheet',
                'view_income_statement',
                'view_accounting_dashboard',
                'view_financial_summary',
            ])
            ->get();

        if ($adminRoleWeb) {
            $adminRoleWeb->syncPermissions($adminPermissionsWeb);
        }

        if ($adminRoleEmployees) {
            $adminRoleEmployees->syncPermissions($adminPermissionsEmployees);
        }

        $this->command->info('✓ Accounting permissions configured');
        $this->command->info('✓ Super Admin: All accounting permissions');
        $this->command->info('✓ MD: All accounting permissions');
        $this->command->info('✓ Admin: Core accounting management permissions');
        $this->command->info('✓ Accountant: All accounting permissions (from AccountantRoleSeeder)');
    }
}
