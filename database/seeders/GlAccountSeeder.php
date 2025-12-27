<?php

namespace Database\Seeders;

use App\Models\GlAccount;
use Illuminate\Database\Seeder;

class GlAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing accounts
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        GlAccount::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accounts = $this->getChartOfAccounts();

        // Create all accounts without parent references first
        foreach ($accounts as $account) {
            // Remove parent_account_id for now to avoid foreign key issues
            unset($account['parent_account_id']);
            GlAccount::create($account);
        }
    }

    /**
     * Get the complete chart of accounts
     */
    private function getChartOfAccounts(): array
    {
        return [
            // ==================== ASSET ACCOUNTS (1000-1999) ====================

            // 1100 - Current Assets (Header)
            [
                'account_number' => '1100',
                'account_name' => 'Current Assets',
                'account_type' => 'asset',
                'account_category' => 'Current Assets',
                'description' => 'Short-term assets expected to convert to cash within one year',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1101 - Cash on Hand
            [
                'account_number' => '1101',
                'account_name' => 'Cash on Hand',
                'account_type' => 'asset',
                'account_category' => 'Cash & Equivalents',
                'description' => 'Physical cash held at branches/locations',
                'parent_account_id' => 1,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1110 - Cash in Bank - Main Account
            [
                'account_number' => '1110',
                'account_name' => 'Cash in Bank - Main',
                'account_type' => 'asset',
                'account_category' => 'Cash & Equivalents',
                'description' => 'Primary operating bank account',
                'parent_account_id' => 1,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1120 - Cash in Bank - Petty Cash
            [
                'account_number' => '1120',
                'account_name' => 'Cash in Bank - Petty Cash',
                'account_type' => 'asset',
                'account_category' => 'Cash & Equivalents',
                'description' => 'Petty cash/imprest account',
                'parent_account_id' => 1,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1200 - Accounts Receivable
            [
                'account_number' => '1200',
                'account_name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'account_category' => 'Receivables',
                'description' => 'Credit sales awaiting payment',
                'parent_account_id' => 1,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1210 - Allowance for Bad Debts
            [
                'account_number' => '1210',
                'account_name' => 'Allowance for Bad Debts',
                'account_type' => 'asset',
                'account_category' => 'Receivables',
                'description' => 'Contra-asset account for doubtful receivables',
                'parent_account_id' => 1,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 1300 - Inventory Assets (Header)
            [
                'account_number' => '1300',
                'account_name' => 'Inventory Assets',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Raw materials, WIP, and finished goods inventory',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1310 - Raw Materials
            [
                'account_number' => '1310',
                'account_name' => 'Raw Materials Inventory',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Raw materials held for production',
                'parent_account_id' => 8,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1320 - Work in Process
            [
                'account_number' => '1320',
                'account_name' => 'Work in Process Inventory',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Partially completed production items',
                'parent_account_id' => 8,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1330 - Finished Goods
            [
                'account_number' => '1330',
                'account_name' => 'Finished Goods Inventory',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Completed products ready for sale',
                'parent_account_id' => 8,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1340 - Supplies
            [
                'account_number' => '1340',
                'account_name' => 'Supplies Inventory',
                'account_type' => 'asset',
                'account_category' => 'Inventory',
                'description' => 'Consumable supplies and materials',
                'parent_account_id' => 8,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1500 - Fixed Assets (Header)
            [
                'account_number' => '1500',
                'account_name' => 'Fixed Assets',
                'account_type' => 'asset',
                'account_category' => 'Fixed Assets',
                'description' => 'Long-term tangible assets',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1510 - Equipment
            [
                'account_number' => '1510',
                'account_name' => 'Equipment',
                'account_type' => 'asset',
                'account_category' => 'Fixed Assets',
                'description' => 'Production and operational equipment',
                'parent_account_id' => 12,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1520 - Accumulated Depreciation - Equipment
            [
                'account_number' => '1520',
                'account_name' => 'Accumulated Depreciation - Equipment',
                'account_type' => 'asset',
                'account_category' => 'Fixed Assets',
                'description' => 'Contra-asset account for equipment depreciation',
                'parent_account_id' => 12,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 1530 - Furniture & Fixtures
            [
                'account_number' => '1530',
                'account_name' => 'Furniture & Fixtures',
                'account_type' => 'asset',
                'account_category' => 'Fixed Assets',
                'description' => 'Office furniture and fixtures',
                'parent_account_id' => 12,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 1540 - Accumulated Depreciation - Furniture
            [
                'account_number' => '1540',
                'account_name' => 'Accumulated Depreciation - Furniture',
                'account_type' => 'asset',
                'account_category' => 'Fixed Assets',
                'description' => 'Contra-asset account for furniture depreciation',
                'parent_account_id' => 12,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== LIABILITY ACCOUNTS (2000-2999) ====================

            // 2100 - Current Liabilities (Header)
            [
                'account_number' => '2100',
                'account_name' => 'Current Liabilities',
                'account_type' => 'liability',
                'account_category' => 'Current Liabilities',
                'description' => 'Short-term obligations due within one year',
                'is_header' => true,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2101 - Accounts Payable
            [
                'account_number' => '2101',
                'account_name' => 'Accounts Payable',
                'account_type' => 'liability',
                'account_category' => 'Payables',
                'description' => 'Amounts owed to suppliers for goods/services',
                'parent_account_id' => 18,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2102 - Accrued Expenses
            [
                'account_number' => '2102',
                'account_name' => 'Accrued Expenses',
                'account_type' => 'liability',
                'account_category' => 'Payables',
                'description' => 'Expenses incurred but not yet paid',
                'parent_account_id' => 18,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2110 - Sales Tax Payable
            [
                'account_number' => '2110',
                'account_name' => 'Sales Tax Payable',
                'account_type' => 'liability',
                'account_category' => 'Tax Liabilities',
                'description' => 'Sales tax collected from customers',
                'parent_account_id' => 18,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2120 - VAT Payable
            [
                'account_number' => '2120',
                'account_name' => 'VAT Payable',
                'account_type' => 'liability',
                'account_category' => 'Tax Liabilities',
                'description' => 'Value Added Tax liability',
                'parent_account_id' => 18,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2200 - Short-term Debt (Header)
            [
                'account_number' => '2200',
                'account_name' => 'Short-term Debt',
                'account_type' => 'liability',
                'account_category' => 'Debt',
                'description' => 'Short-term loans and borrowings',
                'is_header' => true,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2201 - Short-term Loans
            [
                'account_number' => '2201',
                'account_name' => 'Short-term Loans',
                'account_type' => 'liability',
                'account_category' => 'Debt',
                'description' => 'Short-term borrowings from financial institutions',
                'parent_account_id' => 24,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 2210 - Current Portion of Long-term Debt
            [
                'account_number' => '2210',
                'account_name' => 'Current Portion of Long-term Debt',
                'account_type' => 'liability',
                'account_category' => 'Debt',
                'description' => 'Portion of long-term debt due within 12 months',
                'parent_account_id' => 24,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== EQUITY ACCOUNTS (3000-3999) ====================

            // 3100 - Owner's Equity (Header)
            [
                'account_number' => '3100',
                'account_name' => "Owner's Equity",
                'account_type' => 'equity',
                'account_category' => 'Equity',
                'description' => 'Owners investment and accumulated earnings',
                'is_header' => true,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 3101 - Capital Stock
            [
                'account_number' => '3101',
                'account_name' => 'Capital Stock',
                'account_type' => 'equity',
                'account_category' => 'Equity',
                'description' => 'Owners equity investment',
                'parent_account_id' => 27,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 3110 - Retained Earnings
            [
                'account_number' => '3110',
                'account_name' => 'Retained Earnings',
                'account_type' => 'equity',
                'account_category' => 'Equity',
                'description' => 'Accumulated profits from prior periods',
                'parent_account_id' => 27,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 3120 - Current Period Earnings
            [
                'account_number' => '3120',
                'account_name' => 'Current Period Earnings',
                'account_type' => 'equity',
                'account_category' => 'Equity',
                'description' => 'Profit/Loss for the current accounting period',
                'parent_account_id' => 27,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== REVENUE ACCOUNTS (4000-4999) ====================

            // 4100 - Product Sales (Header)
            [
                'account_number' => '4100',
                'account_name' => 'Product Sales',
                'account_type' => 'revenue',
                'account_category' => 'Sales Revenue',
                'description' => 'Revenue from product sales',
                'is_header' => true,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4110 - Product Sales - Main
            [
                'account_number' => '4110',
                'account_name' => 'Product Sales - Main',
                'account_type' => 'revenue',
                'account_category' => 'Sales Revenue',
                'description' => 'Primary product sales revenue',
                'parent_account_id' => 32,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4111 - Product Sales - Secondary
            [
                'account_number' => '4111',
                'account_name' => 'Product Sales - Secondary',
                'account_type' => 'revenue',
                'account_category' => 'Sales Revenue',
                'description' => 'Secondary/ancillary product sales revenue',
                'parent_account_id' => 32,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4200 - Service Revenue
            [
                'account_number' => '4200',
                'account_name' => 'Service Revenue',
                'account_type' => 'revenue',
                'account_category' => 'Service Revenue',
                'description' => 'Revenue from services rendered',
                'is_header' => false,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4300 - Other Income (Header)
            [
                'account_number' => '4300',
                'account_name' => 'Other Income',
                'account_type' => 'other_income',
                'account_category' => 'Other Income',
                'description' => 'Miscellaneous revenue sources',
                'is_header' => true,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4310 - Discount Received
            [
                'account_number' => '4310',
                'account_name' => 'Discount Received',
                'account_type' => 'other_income',
                'account_category' => 'Other Income',
                'description' => 'Purchase discounts and rebates received',
                'parent_account_id' => 37,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // 4311 - Interest Income
            [
                'account_number' => '4311',
                'account_name' => 'Interest Income',
                'account_type' => 'other_income',
                'account_category' => 'Other Income',
                'description' => 'Interest earned on bank accounts',
                'parent_account_id' => 37,
                'normal_balance' => 'credit',
                'is_active' => true,
            ],

            // ==================== COST OF GOODS SOLD (5000-5999) ====================

            // 5100 - Cost of Goods Sold (Header)
            [
                'account_number' => '5100',
                'account_name' => 'Cost of Goods Sold',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Direct costs of products sold',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 5110 - COGS - Production
            [
                'account_number' => '5110',
                'account_name' => 'COGS - Production',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Cost of internally produced goods sold',
                'parent_account_id' => 41,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 5120 - COGS - Purchased
            [
                'account_number' => '5120',
                'account_name' => 'COGS - Purchased',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'COGS',
                'description' => 'Cost of purchased goods sold',
                'parent_account_id' => 41,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 5200 - Inventory Adjustments (Header)
            [
                'account_number' => '5200',
                'account_name' => 'Inventory Adjustments',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'Inventory',
                'description' => 'Inventory valuation adjustments',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 5210 - Inventory Writeoff
            [
                'account_number' => '5210',
                'account_name' => 'Inventory Writeoff',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'Inventory',
                'description' => 'Obsolete or damaged inventory write-offs',
                'parent_account_id' => 45,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 5211 - Obsolescence Adjustment
            [
                'account_number' => '5211',
                'account_name' => 'Obsolescence Adjustment',
                'account_type' => 'cost_of_goods_sold',
                'account_category' => 'Inventory',
                'description' => 'Adjustment for obsolete inventory',
                'parent_account_id' => 45,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // ==================== EXPENSE ACCOUNTS (6000-6999) ====================

            // 6100 - Production Expenses (Header)
            [
                'account_number' => '6100',
                'account_name' => 'Production Expenses',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Indirect production and manufacturing expenses',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6110 - Raw Materials Used
            [
                'account_number' => '6110',
                'account_name' => 'Raw Materials Used',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Raw materials consumed in production',
                'parent_account_id' => 49,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6120 - Direct Labor
            [
                'account_number' => '6120',
                'account_name' => 'Direct Labor',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Wages of production workers',
                'parent_account_id' => 49,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6130 - Manufacturing Overhead
            [
                'account_number' => '6130',
                'account_name' => 'Manufacturing Overhead',
                'account_type' => 'expense',
                'account_category' => 'Production',
                'description' => 'Indirect manufacturing costs and overhead',
                'parent_account_id' => 49,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6200 - Operating Expenses (Header)
            [
                'account_number' => '6200',
                'account_name' => 'Operating Expenses',
                'account_type' => 'expense',
                'account_category' => 'Operating',
                'description' => 'General operating and administrative expenses',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6210 - Salaries & Wages
            [
                'account_number' => '6210',
                'account_name' => 'Salaries & Wages',
                'account_type' => 'expense',
                'account_category' => 'Payroll',
                'description' => 'Employee salaries and wages (non-production)',
                'parent_account_id' => 55,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6220 - Rent/Lease
            [
                'account_number' => '6220',
                'account_name' => 'Rent/Lease Expense',
                'account_type' => 'expense',
                'account_category' => 'Facilities',
                'description' => 'Rent or lease expenses for buildings/facilities',
                'parent_account_id' => 55,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6230 - Utilities
            [
                'account_number' => '6230',
                'account_name' => 'Utilities Expense',
                'account_type' => 'expense',
                'account_category' => 'Facilities',
                'description' => 'Electricity, water, gas, and other utilities',
                'parent_account_id' => 55,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6240 - Maintenance & Repairs
            [
                'account_number' => '6240',
                'account_name' => 'Maintenance & Repairs',
                'account_type' => 'expense',
                'account_category' => 'Facilities',
                'description' => 'Building and equipment maintenance and repairs',
                'parent_account_id' => 55,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6250 - Transportation & Delivery
            [
                'account_number' => '6250',
                'account_name' => 'Transportation & Delivery',
                'account_type' => 'expense',
                'account_category' => 'Distribution',
                'description' => 'Product delivery and transportation costs',
                'parent_account_id' => 55,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6300 - Administrative Expenses (Header)
            [
                'account_number' => '6300',
                'account_name' => 'Administrative Expenses',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'General administrative and overhead expenses',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6310 - Depreciation
            [
                'account_number' => '6310',
                'account_name' => 'Depreciation Expense',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Depreciation of fixed assets',
                'parent_account_id' => 65,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6320 - Office Supplies
            [
                'account_number' => '6320',
                'account_name' => 'Office Supplies',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Office supplies and consumables',
                'parent_account_id' => 65,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6330 - Insurance
            [
                'account_number' => '6330',
                'account_name' => 'Insurance Expense',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Business insurance premiums',
                'parent_account_id' => 65,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6340 - Professional Fees
            [
                'account_number' => '6340',
                'account_name' => 'Professional Fees',
                'account_type' => 'expense',
                'account_category' => 'Administrative',
                'description' => 'Legal, accounting, and consulting fees',
                'parent_account_id' => 65,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6400 - Sales & Marketing (Header)
            [
                'account_number' => '6400',
                'account_name' => 'Sales & Marketing',
                'account_type' => 'expense',
                'account_category' => 'Sales & Marketing',
                'description' => 'Advertising and sales promotion expenses',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6410 - Advertising
            [
                'account_number' => '6410',
                'account_name' => 'Advertising Expense',
                'account_type' => 'expense',
                'account_category' => 'Sales & Marketing',
                'description' => 'Advertising and promotional expenses',
                'parent_account_id' => 73,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 6420 - Sales Commissions
            [
                'account_number' => '6420',
                'account_name' => 'Sales Commissions',
                'account_type' => 'expense',
                'account_category' => 'Sales & Marketing',
                'description' => 'Sales commissions and incentives',
                'parent_account_id' => 73,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // ==================== TAX ACCOUNTS (7000-7999) ====================

            // 7100 - Taxes (Header)
            [
                'account_number' => '7100',
                'account_name' => 'Tax Expense',
                'account_type' => 'tax',
                'account_category' => 'Taxes',
                'description' => 'Income and other tax expenses',
                'is_header' => true,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 7110 - Income Tax Expense
            [
                'account_number' => '7110',
                'account_name' => 'Income Tax Expense',
                'account_type' => 'tax',
                'account_category' => 'Taxes',
                'description' => 'Current and deferred income tax expense',
                'parent_account_id' => 81,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],

            // 7120 - Sales Tax Expense
            [
                'account_number' => '7120',
                'account_name' => 'Sales Tax Expense',
                'account_type' => 'tax',
                'account_category' => 'Taxes',
                'description' => 'Sales and excise tax expenses',
                'parent_account_id' => 81,
                'normal_balance' => 'debit',
                'is_active' => true,
            ],
        ];
    }
}
