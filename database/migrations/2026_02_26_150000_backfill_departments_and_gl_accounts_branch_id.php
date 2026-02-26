<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $targetBranchId = '019c850c-7294-72ea-9ec5-1b00d6b0bdd8';

    public function up(): void
    {
        if (Schema::hasTable('departments') && Schema::hasColumn('departments', 'branch_id')) {
            DB::table('departments')
                ->whereNull('branch_id')
                ->update(['branch_id' => $this->targetBranchId]);
        }

        if (Schema::hasTable('gl_accounts') && Schema::hasColumn('gl_accounts', 'branch_id')) {
            DB::table('gl_accounts')
                ->whereNull('branch_id')
                ->update(['branch_id' => $this->targetBranchId]);
        }
    }

    public function down(): void
    {
        // Not reversible safely.
    }
};
