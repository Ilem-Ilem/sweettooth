<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('tax');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (! Schema::hasColumn('sale_items', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->nullable()->after('unit_price');
            }
            if (! Schema::hasColumn('sale_items', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
            if (Schema::hasColumn('sale_items', 'vat_rate')) {
                $table->dropColumn('vat_rate');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
        });
    }
};
