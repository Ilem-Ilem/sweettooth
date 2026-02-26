<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Target branch ID for all accounting data
     */
    private string $targetBranchId = '019c850c-7294-72ea-9ec5-1b00d6b0bdd8';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tables that have branch_id column and need to be updated
        $tablesWithBranchId = [
            'gl_accounts',
            'bank_accounts',
            'accounting_entries',
            'account_transfers',
            'expense_claims',
            'expense_claim_items',
            'sales_quotes',
            'sales_quote_items',
            'sales_orders',
            'sales_order_items',
            'credit_notes',
            'credit_note_items',
            'delivery_notes',
            'delivery_note_items',
            'billable_time_entries',
            'withholding_tax_receipts',
            'late_payment_fees',
            'purchase_quotes',
            'purchase_quote_items',
            'purchase_orders',
            'purchase_order_items',
            'debit_notes',
            'debit_note_items',
            'goods_receipts',
            'goods_receipt_items',
            'inventory_adjustments',
            'production_orders',
            'production_order_components',
            'inventory_kits',
            'inventory_kit_components',
        ];

        foreach ($tablesWithBranchId as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'branch_id')) {
                // For gl_accounts, handle duplicates by deleting records that would violate unique constraint
                if ($table === 'gl_accounts') {
                    // Get all account_numbers that already exist for the target branch
                    $existingAccounts = DB::table($table)
                        ->where('branch_id', $this->targetBranchId)
                        ->pluck('account_number')
                        ->toArray();
                    
                    // Delete records that would cause duplicates
                    if (!empty($existingAccounts)) {
                        DB::table($table)
                            ->whereNull('branch_id')
                            ->whereIn('account_number', $existingAccounts)
                            ->delete();
                    }
                }
                
                DB::table($table)
                    ->whereNull('branch_id')
                    ->update(['branch_id' => $this->targetBranchId]);
            }
        }

        // Handle accounting_periods separately - add branch_id if not exists
        if (Schema::hasTable('accounting_periods')) {
            if (!Schema::hasColumn('accounting_periods', 'branch_id')) {
                Schema::table('accounting_periods', function (Blueprint $table) {
                    $table->uuid('branch_id')->nullable()->after('id');
                    $table->foreign('branch_id')->references('id')->on('branches');
                });
            }

            // Update existing periods
            DB::table('accounting_periods')
                ->whereNull('branch_id')
                ->update(['branch_id' => $this->targetBranchId]);
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
