<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['sales_shift_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['department_id']);
        });

        // Make fields nullable
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_shift_id')->nullable()->change();
            $table->uuid('branch_id')->nullable()->change();
            $table->unsignedBigInteger('department_id')->nullable()->change();
        });

        // Re-add foreign keys with nullable constraint
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // Update the status enum to include 'hold'
        DB::statement("DROP TABLE IF EXISTS sales_backup");
        DB::statement("CREATE TABLE sales_backup AS SELECT * FROM sales");

        Schema::dropIfExists('sales');

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_id')->nullable();
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('set null');
            $table->uuid('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->uuid('sold_by');
            $table->foreign('sold_by')->references('id')->on('employees')->onDelete('restrict');
            $table->string('sale_number')->unique();
            $table->timestamp('sale_time');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded', 'hold'])->default('completed');
            $table->enum('order_type', ['dine-in', 'takeaway', 'delivery', 'dine_in', 'glovo', 'transfer'])->default('dine_in');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'department_id', 'sale_time']);
        });

        // Restore data if any exists
        $backupExists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='sales_backup'");
        if (!empty($backupExists)) {
            DB::statement("INSERT INTO sales SELECT * FROM sales_backup");
            DB::statement("DROP TABLE sales_backup");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes if needed
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['sales_shift_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['department_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_shift_id')->nullable(false)->change();
            $table->uuid('branch_id')->nullable(false)->change();
            $table->unsignedBigInteger('department_id')->nullable(false)->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }
};
