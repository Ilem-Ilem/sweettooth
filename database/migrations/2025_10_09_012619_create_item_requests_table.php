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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->uuid('requested_by');
            $table->foreign('requested_by')->references('id')->on('employees')->onDelete('restrict');
            $table->string('request_number')->unique();
            $table->date('request_date');
            $table->enum('shift', ['morning', 'afternoon'])->nullable();
            $table->enum('status', ['pending', 'approved', 'partially_dispatched', 'completed', 'cancelled'])->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};
