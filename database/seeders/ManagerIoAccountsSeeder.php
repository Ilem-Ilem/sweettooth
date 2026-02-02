<?php

namespace Database\Seeders;

use App\Models\GlAccount;
use Illuminate\Database\Seeder;

class ManagerIoAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Adds General Ledger accounts required by the Manager.io-inspired accounting features.
     * These accounts supplement the existing chart of accounts.
     */
    public function run(): void
    {
        $accounts = $this->getAdditionalAccounts();

        foreach ($accounts as $account) {
            GlAccount::firstOrCreate(
                ['account_number' => $account['account_number']],
                $account
            );
        }

        $this->command->info('Manager.io General Ledger accounts seeded successfully.');
    }

    /**
     * Get additional accounts for Manager.io features
     */
    private function getAdditionalAccounts(): array
    {
        return [
            // ==================== ASSET ACCOUNTS ====================

            // Cash accounts used by General Ledger Posting Service
            [
                'account_number' => '1010',
                'account_name' => 'Cash - Head Office',
                'account_type' => 'asset',
                'account_category' => 'Cash & Equivalents',
                'description' => 'Cash on hand at head office',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '1050',
                'account_name' => 'Bank Account - Main',
                'account_type' => 'asset',
                'account_category' => 'Cash & Equivalents',
                'description' => 'Primary bank operating account',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // Accounts Receivable (alternate number for credit notes)
            [
                'account_number' => '1100',
                'account_name' => 'Accounts Receivable - Trade',
                'account_type' => 'asset',
                'account_category' => 'Receivables',
                'description' => 'Trade receivables from customers',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // Inventory accounts for production
            [
                'account_number' => '1200',
                'account_name' => 'Inventory - Raw Materials',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Raw materials inventory for production',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '1210',
                'account_name' => 'Inventory - Work in Progress',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Work in progress inventory',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '1220',
                'account_name' => 'Inventory - Finished Goods',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Finished goods ready for sale',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // ==================== LIABILITY ACCOUNTS ====================

            // Accounts Payable
            [
                'account_number' => '2010',
                'account_name' => 'Accounts Payable - Trade',
                'account_type' => 'liability',
                'account_category' => 'Payables',
                'description' => 'Trade payables to suppliers',
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // Tax Liabilities
            [
                'account_number' => '2020',
                'account_name' => 'Sales Tax Payable',
                'account_type' => 'liability',
                'account_category' => 'Tax Liabilities',
                'description' => 'Sales tax collected from customers',
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== EQUITY ACCOUNTS ====================

            [
                'account_number' => '3020',
                'account_name' => 'Retained Earnings',
                'account_type' => 'equity',
                'account_category' => 'Equity',
                'description' => 'Accumulated retained earnings',
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== REVENUE ACCOUNTS ====================

            [
                'account_number' => '4010',
                'account_name' => 'Sales Revenue',
                'account_type' => 'revenue',
                'account_category' => 'Sales Revenue',
                'description' => 'Revenue from product and service sales',
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== COGS ACCOUNTS ====================

            [
                'account_number' => '5010',
                'account_name' => 'Cost of Goods Sold - Sales',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Cost of goods sold from sales',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '5020',
                'account_name' => 'Inventory Damage Loss',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Loss from damaged inventory',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '5030',
                'account_name' => 'Inventory Shrinkage Loss',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Loss from inventory shrinkage',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '5040',
                'account_name' => 'Inventory Write-off',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Inventory write-off losses',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '5050',
                'account_name' => 'Inventory Adjustment',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'General inventory adjustments',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // ==================== EXPENSE ACCOUNTS ====================

            // Production accounts
            [
                'account_number' => '6100',
                'account_name' => 'Direct Labor - Production',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Direct labor costs in production',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6200',
                'account_name' => 'Manufacturing Overhead',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Manufacturing overhead costs',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // Expense claim categories
            [
                'account_number' => '6300',
                'account_name' => 'Travel Expenses',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Travel and transportation expenses',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6310',
                'account_name' => 'Meals & Entertainment',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Meals and entertainment expenses',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6320',
                'account_name' => 'Office Supplies Expense',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Office supplies and materials',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6330',
                'account_name' => 'Communication Expenses',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Phone, internet, and communication costs',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6340',
                'account_name' => 'Accommodation Expenses',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Lodging and accommodation costs',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6350',
                'account_name' => 'Professional Services',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Professional and consulting fees',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
            [
                'account_number' => '6900',
                'account_name' => 'Other Expenses',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Miscellaneous operating expenses',
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
        ];
    }
}
