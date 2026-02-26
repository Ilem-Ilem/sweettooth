<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\GlAccount;
use App\Models\BranchAccountingDefault;
use Illuminate\Database\Seeder;

class BranchAccountingDefaultsSeeder extends Seeder
{
    /**
     * All required accounting default keys and their account configurations
     */
    protected array $defaultConfigs = [
        // Sales & Revenue
        'sales_revenue' => [
            'account_number' => '4010',
            'account_name' => 'Sales Revenue',
            'account_type' => 'revenue',
            'normal_balance' => 'credit',
        ],
        'sales_tax_payable' => [
            'account_number' => '2020',
            'account_name' => 'Sales Tax Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        
        // Receivables
        'accounts_receivable' => [
            'account_number' => '1100',
            'account_name' => 'Accounts Receivable',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        
        // COGS & Inventory
        'cogs' => [
            'account_number' => '5010',
            'account_name' => 'Cost of Goods Sold',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'inventory_asset' => [
            'account_number' => '1220',
            'account_name' => 'Inventory Asset',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'inventory' => [
            'account_number' => '1210',
            'account_name' => 'Inventory',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        
        // Cash & Bank
        'cash_on_hand' => [
            'account_number' => '1010',
            'account_name' => 'Cash on Hand',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'bank_main' => [
            'account_number' => '1020',
            'account_name' => 'Bank Account - Main',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        
        // Payables
        'accounts_payable' => [
            'account_number' => '2010',
            'account_name' => 'Accounts Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        
        // Inventory Adjustments
        'adjustment_damage' => [
            'account_number' => '5020',
            'account_name' => 'Inventory Damage Expense',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'adjustment_shrinkage' => [
            'account_number' => '5030',
            'account_name' => 'Inventory Shrinkage Expense',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'adjustment_write_off' => [
            'account_number' => '5040',
            'account_name' => 'Inventory Write-off',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        
        // Payroll
        'payroll_expense' => [
            'account_number' => '6010',
            'account_name' => 'Payroll Expense',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'payroll_payable' => [
            'account_number' => '2030',
            'account_name' => 'Payroll Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        'payroll_tax_payable' => [
            'account_number' => '2040',
            'account_name' => 'Payroll Tax Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        'payroll_deduction_payable' => [
            'account_number' => '2050',
            'account_name' => 'Payroll Deductions Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        
        // Tax
        'tax_payable' => [
            'account_number' => '2060',
            'account_name' => 'Tax Payable',
            'account_type' => 'liability',
            'normal_balance' => 'credit',
        ],
        
        // Fixed Assets
        'fixed_asset' => [
            'account_number' => '1510',
            'account_name' => 'Fixed Assets',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'depreciation_expense' => [
            'account_number' => '7010',
            'account_name' => 'Depreciation Expense',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'accumulated_depreciation' => [
            'account_number' => '1520',
            'account_name' => 'Accumulated Depreciation',
            'account_type' => 'asset',
            'normal_balance' => 'credit',
        ],
        
        // Production
        'inventory_finished_goods' => [
            'account_number' => '1230',
            'account_name' => 'Finished Goods Inventory',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'inventory_raw_materials' => [
            'account_number' => '1240',
            'account_name' => 'Raw Materials Inventory',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'inventory_wip' => [
            'account_number' => '1250',
            'account_name' => 'Work in Progress',
            'account_type' => 'asset',
            'normal_balance' => 'debit',
        ],
        'labor_direct' => [
            'account_number' => '6020',
            'account_name' => 'Direct Labor',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
        'overhead_manufacturing' => [
            'account_number' => '6030',
            'account_name' => 'Manufacturing Overhead',
            'account_type' => 'expense',
            'normal_balance' => 'debit',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        
        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Skipping BranchAccountingDefaultsSeeder.');
            return;
        }

        foreach ($branches as $branch) {
            $this->seedBranchDefaults($branch);
        }

        $this->command->info('Branch accounting defaults seeded successfully for ' . $branches->count() . ' branch(es).');
    }

    /**
     * Seed defaults for a specific branch
     */
    protected function seedBranchDefaults(Branch $branch): void
    {
        foreach ($this->defaultConfigs as $key => $config) {
            // Check if default already exists
            $existing = BranchAccountingDefault::forBranch($branch->id)
                ->where('key', $key)
                ->first();

            if ($existing) {
                continue; // Skip if already exists
            }

            // Find or create the GL account
            $glAccount = GlAccount::firstOrCreate(
                [
                    'account_number' => $config['account_number'],
                    'branch_id' => $branch->id,
                ],
                [
                    'account_name' => $config['account_name'],
                    'account_type' => $config['account_type'],
                    'normal_balance' => $config['normal_balance'],
                    'is_header' => false,
                    'is_active' => true,
                ]
            );

            // Create the default
            BranchAccountingDefault::create([
                'branch_id' => $branch->id,
                'key' => $key,
                'gl_account_id' => $glAccount->id,
            ]);
        }
    }
}
