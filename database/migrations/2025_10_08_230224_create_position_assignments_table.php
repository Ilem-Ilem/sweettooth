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
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->uuid('branch_id')->nullable(); // Assigned to specific branch
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreignId('department_id')->nullable(); // Assigned to specific department
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->enum('assignment_type', ['branch_admin', 'department_head', 'regular'])->default('regular');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure one employee can't have duplicate active assignments
            $table->unique(['employee_id', 'position_id', 'branch_id', 'department_id'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_assignments');
    }
};
