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
        Schema::create('employee_stepouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('reason', ['restroom', 'water_break', 'prayer', 'emergency', 'medical', 'other'])->default('restroom');
            $table->text('reason_details')->nullable();
            $table->timestamp('request_time');
            $table->timestamp('approved_time')->nullable();
            $table->timestamp('stepout_time')->nullable(); // Actual time they left
            $table->timestamp('return_time')->nullable(); // Actual time they returned
            $table->integer('duration_minutes')->nullable(); // Calculated duration
            $table->integer('expected_duration_minutes')->default(15); // Expected duration
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed', 'overdue'])->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->text('approval_notes')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_overdue')->default(false); // If they exceeded expected time
            $table->integer('overdue_minutes')->nullable();
            $table->text('overdue_reason')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('employees')->onDelete('set null');

            $table->index(['employee_id', 'request_time']);
            $table->index(['status', 'request_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_stepouts');
    }
};
