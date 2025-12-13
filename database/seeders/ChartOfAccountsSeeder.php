<?php

namespace Database\Seeders;

use App\Models\GlAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ASSETS (1000-1900)
            ['account_number' => '1010', 'account_name' => 'Cash - Head Office', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1020', 'account_name' => 'Cash - Branch A', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1030', 'account_name' => 'Cash - Branch B', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1040', 'account_name' => 'Petty Cash', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1050', 'account_name' => 'Bank Account - Main', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1060', 'account_name' => 'Bank Account - Branch A', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1070', 'account_name' => 'Bank Account - Branch B', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1100', 'account_name' => 'Accounts Receivable', 'account_type' => 'asset', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1200', 'account_name' => 'Inventory - Raw Materials', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1210', 'account_name' => 'Inventory - Work in Progress', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1220', 'account_name' => 'Inventory - Finished Goods', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1300', 'account_name' => 'Fixed Assets - Equipment', 'account_type' => 'asset', 'account_category' => 'fixed_assets', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1310', 'account_name' => 'Fixed Assets - Building', 'account_type' => 'asset', 'account_category' => 'fixed_assets', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '1400', 'account_name' => 'Accumulated Depreciation', 'account_type' => 'asset', 'account_category' => 'depreciation', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '1500', 'account_name' => 'Prepaid Expenses', 'account_type' => 'asset', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false],

            // LIABILITIES (2000-2900)
            ['account_number' => '2010', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2020', 'account_name' => 'Sales Tax Payable', 'account_type' => 'liability', 'account_category' => 'tax_payable', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2030', 'account_name' => 'Income Tax Payable', 'account_type' => 'liability', 'account_category' => 'tax_payable', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2040', 'account_name' => 'Employee Withholding Payable', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2100', 'account_name' => 'Short-term Loan', 'account_type' => 'liability', 'account_category' => 'loans', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2200', 'account_name' => 'Long-term Loan', 'account_type' => 'liability', 'account_category' => 'loans', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '2300', 'account_name' => 'Accrued Expenses', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false],

            // EQUITY (3000-3900)
            ['account_number' => '3010', 'account_name' => 'Capital Stock / Owner\'s Capital', 'account_type' => 'equity', 'account_category' => 'capital', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '3020', 'account_name' => 'Retained Earnings', 'account_type' => 'equity', 'account_category' => 'retained_earnings', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '3030', 'account_name' => 'Dividends', 'account_type' => 'equity', 'account_category' => 'capital', 'normal_balance' => 'debit', 'is_header' => false],

            // REVENUE (4000-4900)
            ['account_number' => '4010', 'account_name' => 'Sales Revenue - Retail', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '4020', 'account_name' => 'Sales Revenue - Production', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '4030', 'account_name' => 'Service Revenue', 'account_type' => 'revenue', 'account_category' => 'service_revenue', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '4040', 'account_name' => 'Other Income', 'account_type' => 'revenue', 'account_category' => 'other_income', 'normal_balance' => 'credit', 'is_header' => false],
            ['account_number' => '4050', 'account_name' => 'Discount Given (Contra Revenue)', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'debit', 'is_header' => false],

            // COST OF GOODS SOLD (5000-5900)
            ['account_number' => '5010', 'account_name' => 'Cost of Goods Sold', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'cogs', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '5020', 'account_name' => 'Inventory Write-down / Damage Loss', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'inventory_adjustment', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '5030', 'account_name' => 'Shrinkage Loss', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'inventory_adjustment', 'normal_balance' => 'debit', 'is_header' => false],

            // OPERATING EXPENSES (6000-6900)
            ['account_number' => '6010', 'account_name' => 'Salary Expense - Management', 'account_type' => 'expense', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6020', 'account_name' => 'Salary Expense - Staff', 'account_type' => 'expense', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6030', 'account_name' => 'Utilities (Electricity, Water)', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6040', 'account_name' => 'Rent', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6050', 'account_name' => 'Advertising & Marketing', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6060', 'account_name' => 'Office Supplies', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6070', 'account_name' => 'Maintenance & Repairs', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6080', 'account_name' => 'Transportation & Logistics', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '6090', 'account_name' => 'Insurance', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false],

            // ADMINISTRATIVE EXPENSES (7000-7900)
            ['account_number' => '7010', 'account_name' => 'Professional Fees (Accounting, Legal)', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '7020', 'account_name' => 'Audit Fees', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '7030', 'account_name' => 'Bank Charges', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '7040', 'account_name' => 'Software & IT', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '7050', 'account_name' => 'Office Equipment', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '7060', 'account_name' => 'Depreciation Expense', 'account_type' => 'expense', 'account_category' => 'depreciation', 'normal_balance' => 'debit', 'is_header' => false],

            // FINANCE COSTS (8000-8900)
            ['account_number' => '8010', 'account_name' => 'Interest Expense', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '8020', 'account_name' => 'Exchange Loss/Gain', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '8030', 'account_name' => 'Finance Charges', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false],

            // TAX ACCOUNTS (9000-9900)
            ['account_number' => '9010', 'account_name' => 'Income Tax Expense', 'account_type' => 'tax', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false],
            ['account_number' => '9020', 'account_name' => 'VAT Expense (Input VAT)', 'account_type' => 'tax', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false],
        ];

        foreach ($accounts as $account) {
            GlAccount::create($account);
        }
    }
}
