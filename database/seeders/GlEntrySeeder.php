<?php

namespace Database\Seeders;

use App\Models\GlEntry;
use App\Models\GlAccount;
use App\Models\AccountingPeriod;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GlEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing General Ledger entries
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        GlEntry::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get some active General Ledger accounts
        $cashAccount = GlAccount::where('account_number', '1101')->first(); // Cash on Hand
        $bankAccount = GlAccount::where('account_number', '1110')->first(); // Cash in Bank - Main
        $salesAccount = GlAccount::where('account_number', '4110')->first(); // Product Sales - Main
        $inventoryAccount = GlAccount::where('account_number', '1310')->first(); // Raw Materials
        $accountsPayable = GlAccount::where('account_number', '2101')->first(); // Accounts Payable
        $salaryExpense = GlAccount::where('account_number', '6210')->first(); // Salaries & Wages

        if (!$cashAccount || !$bankAccount || !$salesAccount || !$inventoryAccount || !$accountsPayable || !$salaryExpense) {
            $this->command->error('Required General Ledger accounts not found. Please run ChartOfAccountsSeeder and GlAccountSeeder first.');
            return;
        }

        // Get an open accounting period
        $period = AccountingPeriod::where('status', 'open')->first();
        if (!$period) {
            $this->command->error('No open accounting period found. Please run AccountingPeriodSeeder first.');
            return;
        }

        // Create sample General Ledger entries
        $entries = [
            // Sales transactions
            [
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sales',
                'reference_number' => 'SALE-001',
                'description' => 'Cash sales for the day',
                'debit' => 150000.00,
                'credit' => 0.00,
                'entry_date' => Carbon::now()->subDays(5),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
            [
                'gl_account_id' => $salesAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sales',
                'reference_number' => 'SALE-001',
                'description' => 'Sales revenue from cash sales',
                'debit' => 0.00,
                'credit' => 150000.00,
                'entry_date' => Carbon::now()->subDays(5),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],

            // Purchase transaction
            [
                'gl_account_id' => $inventoryAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase',
                'reference_number' => 'PUR-001',
                'description' => 'Purchase of raw materials',
                'debit' => 75000.00,
                'credit' => 0.00,
                'entry_date' => Carbon::now()->subDays(4),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
            [
                'gl_account_id' => $accountsPayable->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase',
                'reference_number' => 'PUR-001',
                'description' => 'Accounts payable for raw materials',
                'debit' => 0.00,
                'credit' => 75000.00,
                'entry_date' => Carbon::now()->subDays(4),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],

            // Salary payment
            [
                'gl_account_id' => $salaryExpense->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'expense',
                'reference_number' => 'SAL-001',
                'description' => 'Monthly salary payment',
                'debit' => 500000.00,
                'credit' => 0.00,
                'entry_date' => Carbon::now()->subDays(3),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
            [
                'gl_account_id' => $bankAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'expense',
                'reference_number' => 'SAL-001',
                'description' => 'Bank payment for salary',
                'debit' => 0.00,
                'credit' => 500000.00,
                'entry_date' => Carbon::now()->subDays(3),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],

            // Additional sales
            [
                'gl_account_id' => $bankAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sales',
                'reference_number' => 'SALE-002',
                'description' => 'Bank deposit from sales',
                'debit' => 200000.00,
                'credit' => 0.00,
                'entry_date' => Carbon::now()->subDays(2),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
            [
                'gl_account_id' => $salesAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sales',
                'reference_number' => 'SALE-002',
                'description' => 'Sales revenue from bank deposit',
                'debit' => 0.00,
                'credit' => 200000.00,
                'entry_date' => Carbon::now()->subDays(2),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],

            // Utility payment
            [
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'expense',
                'reference_number' => 'UTL-001',
                'description' => 'Utility payment',
                'debit' => 0.00,
                'credit' => 30000.00,
                'entry_date' => Carbon::now()->subDay(),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
            [
                'gl_account_id' => GlAccount::where('account_number', '6230')->first()->id ?? $salaryExpense->id, // Utilities expense
                'accounting_period_id' => $period->id,
                'entry_type' => 'expense',
                'reference_number' => 'UTL-001',
                'description' => 'Utility expense',
                'debit' => 30000.00,
                'credit' => 0.00,
                'entry_date' => Carbon::now()->subDay(),
                'status' => 'posted',
                'entered_by_id' => 1,
            ],
        ];

        foreach ($entries as $entryData) {
            GlEntry::create($entryData);
        }

        $this->command->info('General Ledger entries created successfully!');
    }
}