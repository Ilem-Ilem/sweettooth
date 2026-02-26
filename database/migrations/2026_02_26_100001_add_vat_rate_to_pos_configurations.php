<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('global_pos_configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('global_pos_configurations', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(0)->after('online_shop_sync');
            }
        });

        Schema::table('branch_pos_configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('branch_pos_configurations', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->nullable()->after('receipt_custom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branch_pos_configurations', function (Blueprint $table) {
            if (Schema::hasColumn('branch_pos_configurations', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
        });

        Schema::table('global_pos_configurations', function (Blueprint $table) {
            if (Schema::hasColumn('global_pos_configurations', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
        });
    }
};
