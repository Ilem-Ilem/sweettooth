<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccountantRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Accountant role for unified web guard system
        $accountantRole = Role::firstOrCreate(
            ['name' => 'Accountant', 'guard_name' => 'web'],
            ['is_protected' => false]
        );

        // Accounting Permissions
        $permissions = [
            // GL Account Management
            'view_gl_accounts' => 'View GL accounts',
            'create_gl_accounts' => 'Create GL accounts',
            'edit_gl_accounts' => 'Edit GL accounts',
            'delete_gl_accounts' => 'Delete GL accounts',

            // GL Entries & Journal
            'view_gl_entries' => 'View GL entries',
            'create_gl_entries' => 'Create manual journal entries',
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

        // Create all permissions for web guard
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Create all permissions for employees guard
        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Get all accounting permissions for unified web guard system
        $allPermissions = Permission::where('guard_name', 'web')
            ->whereIn('name', array_keys($permissions))
            ->get();

        // Assign all accounting permissions to accountant role
        $accountantRole->syncPermissions($allPermissions);

        $this->command->info('Accountant role created for unified system with ' . count($allPermissions) . ' permissions');
    }
}
