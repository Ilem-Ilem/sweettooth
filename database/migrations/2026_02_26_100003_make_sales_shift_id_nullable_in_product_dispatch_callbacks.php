<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_dispatch_callbacks', function (Blueprint $table) {
            $table->dropForeign(['sales_shift_id']);
            $table->unsignedBigInteger('sales_shift_id')->nullable()->change();
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product_dispatch_callbacks', function (Blueprint $table) {
            $table->dropForeign(['sales_shift_id']);
            $table->unsignedBigInteger('sales_shift_id')->nullable(false)->change();
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
        });
    }
};
