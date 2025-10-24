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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_id');
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->uuid('sold_by');
            $table->foreign('sold_by')->references('id')->on('employees')->onDelete('restrict');
            $table->string('sale_number')->unique();
            $table->timestamp('sale_time');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            $table->enum('order_type', ['dine_in', 'takeaway', 'glovo', 'transfer'])->default('dine_in');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'department_id', 'sale_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
