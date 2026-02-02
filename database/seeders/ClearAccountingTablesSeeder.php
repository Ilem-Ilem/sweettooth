<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearAccountingTablesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('gl_entries')->truncate();
        DB::table('bank_reconciliations')->truncate();
        DB::table('daily_bank_transactions')->truncate();
        DB::table('daily_bank_positions')->truncate();
        DB::table('account_transfers')->truncate();
        DB::table('branch_accounting_cash')->truncate();
        DB::table('global_accounting_cash')->truncate();
        DB::table('bank_accounts')->truncate();
        DB::table('gl_accounts')->truncate();
        DB::table('accounting_periods')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Accounting tables cleared successfully!');
    }
}