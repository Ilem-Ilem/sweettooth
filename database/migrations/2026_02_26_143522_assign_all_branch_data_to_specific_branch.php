<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Target branch ID for all data
     */
    private string $targetBranchId = '019c850c-7294-72ea-9ec5-1b00d6b0bdd8';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all tables in the database
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $dbName;

        foreach ($tables as $table) {
            $tableName = $table->{$tableKey};
            
            // Check if table has branch_id column
            if (Schema::hasColumn($tableName, 'branch_id')) {
                // Handle tables with unique constraints that include branch_id
                if ($tableName === 'gl_accounts') {
                    $this->handleGlAccountsDuplicates();
                }
                
                // Update null branch_ids to target branch
                DB::table($tableName)
                    ->whereNull('branch_id')
                    ->update(['branch_id' => $this->targetBranchId]);
            }
        }
    }

    /**
     * Handle duplicate account numbers in gl_accounts
     */
    private function handleGlAccountsDuplicates(): void
    {
        // Get all account_numbers that already exist for the target branch
        $existingAccounts = DB::table('gl_accounts')
            ->where('branch_id', $this->targetBranchId)
            ->pluck('account_number')
            ->toArray();
        
        // Delete records that would cause duplicates
        if (!empty($existingAccounts)) {
            DB::table('gl_accounts')
                ->whereNull('branch_id')
                ->whereIn('account_number', $existingAccounts)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we don't know the previous branch_ids
    }
};
