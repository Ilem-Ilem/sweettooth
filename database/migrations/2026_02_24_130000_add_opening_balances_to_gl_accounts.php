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
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->decimal('opening_debit', 15, 2)->default(0)->after('description');
            $table->decimal('opening_credit', 15, 2)->default(0)->after('opening_debit');
            $table->date('opening_balance_date')->nullable()->after('opening_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropColumn(['opening_debit', 'opening_credit', 'opening_balance_date']);
        });
    }
};
