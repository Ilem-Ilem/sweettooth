<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\GlAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing bank accounts
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BankAccount::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get a sample General Ledger account for bank accounts
        $bankGlAccount = GlAccount::where('account_category', 'bank')->first();
        if (!$bankGlAccount) {
            $bankGlAccount = GlAccount::create([
                'account_number' => '1050',
                'account_name' => 'Main Bank Account',
                'account_type' => 'asset',
                'account_category' => 'bank',
                'description' => 'Main bank account for the business',
                'normal_balance' => 'debit',
                'is_header' => false,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]);
        }

        // Create sample bank accounts
        $bankAccounts = [
            [
                'bank_name' => 'Access Bank',
                'bank_code' => '044',
                'account_number' => '0012345678',
                'account_type' => 'checking',
                'gl_account_id' => $bankGlAccount->id,
                'opening_balance' => 500000.00,
                'is_active' => true,
            ],
            [
                'bank_name' => 'GTBank',
                'bank_code' => '057',
                'account_number' => '0087654321',
                'account_type' => 'savings',
                'gl_account_id' => $bankGlAccount->id,
                'opening_balance' => 300000.00,
                'is_active' => true,
            ],
            [
                'bank_name' => 'First Bank',
                'bank_code' => '011',
                'account_number' => '0054321678',
                'account_type' => 'checking',
                'gl_account_id' => $bankGlAccount->id,
                'opening_balance' => 750000.00,
                'is_active' => true,
            ],
        ];

        foreach ($bankAccounts as $accountData) {
            BankAccount::create($accountData);
        }

        $this->command->info('Bank accounts created successfully!');
    }
}