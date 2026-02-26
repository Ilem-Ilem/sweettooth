<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('cogs_account_id')->nullable()->after('cash_account_id')->constrained('gl_accounts')->nullOnDelete();
            $table->foreignId('inventory_account_id')->nullable()->after('cogs_account_id')->constrained('gl_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['cogs_account_id']);
            $table->dropForeign(['inventory_account_id']);
            $table->dropColumn(['cogs_account_id', 'inventory_account_id']);
        });
    }
};
