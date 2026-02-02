<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountingAccessControlSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles (unified system uses web guard only)
        $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        $mdRole = Role::where('name', 'MD')->where('guard_name', 'web')->first();
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();

        // Accounting Permissions
        $permissions = [
            // Base Access
            'access_accounting' => 'Access accounting module',
            'view_financial_reports' => 'View financial reports',

            // General Ledger Account Management
            'manage_accounts' => 'Manage General Ledger accounts',
            'view_gl_accounts' => 'View General Ledger accounts',
            'create_gl_accounts' => 'Create General Ledger accounts',
            'edit_gl_accounts' => 'Edit General Ledger accounts',
            'delete_gl_accounts' => 'Delete General Ledger accounts',

            // General Ledger Entries & Journal
            'view_gl_entries' => 'View General Ledger entries',
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
            'link_sales_to_gl' => 'Link sales transactions to General Ledger',
            'link_purchases_to_gl' => 'Link purchases to General Ledger',
            'link_payments_to_gl' => 'Link payments to General Ledger',
        ];

        // Create all permissions for unified web guard system
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Get all accounting permissions
        $permissionsWeb = Permission::where('guard_name', 'web')
            ->whereIn('name', array_keys($permissions))
            ->get();

        // Assign ALL accounting permissions to Super Admin (unified system)
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($permissionsWeb);
        }

        // Assign ALL accounting permissions to MD (unified system)
        if ($mdRole) {
            $mdRole->syncPermissions($permissionsWeb);
        }

        // Assign specific accounting permissions to Admin
        $adminPermissions = Permission::where('guard_name', 'web')
            ->whereIn('name', [
                'access_accounting',
                'view_financial_reports',
                'view_gl_accounts',
                'manage_accounts',
                'manage_periods',
                'create_journal_entries',
                'reconcile_bank_accounts',
                'view_accounting_dashboard',
                'view_financial_summary',
                'view_general_ledger',
                'view_trial_balance',
                'view_balance_sheet',
                'view_income_statement',
                'view_cash_flow_statement',
            ])
            ->get();

        if ($adminRole) {
            $adminRole->syncPermissions($adminPermissions);
        }

        $this->command->info('✓ Accounting permissions configured');
        $this->command->info('✓ Super Admin: All accounting permissions');
        $this->command->info('✓ MD: All accounting permissions');
        $this->command->info('✓ Admin: Core accounting management permissions');
        $this->command->info('✓ Accountant: All accounting permissions (from AccountantRoleSeeder)');
    }
}
