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
        // Add progress tracking fields to daily_produces table
        Schema::table('daily_produces', function (Blueprint $table) {
            $table->decimal('progress_percentage', 5, 2)->default(0.00)->after('variance');
            $table->timestamp('estimated_completion_time')->nullable()->after('progress_percentage');
            $table->timestamp('actual_completion_time')->nullable()->after('estimated_completion_time');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('actual_completion_time');
            $table->uuid('assigned_to')->nullable()->after('priority');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->index(['progress_percentage', 'status']);
            $table->index(['estimated_completion_time']);
        });

        // Add progress tracking fields to production_records table
        Schema::table('production_records', function (Blueprint $table) {
            $table->enum('progress_stage', [
                'started',
                'ingredients_collected',
                'processing',
                'quality_check',
                'packaging',
                'ready_for_dispatch'
            ])->default('started')->after('dispatch_status');

            $table->timestamp('stage_start_time')->nullable()->after('progress_stage');
            $table->timestamp('stage_end_time')->nullable()->after('stage_start_time');
            $table->integer('expected_duration_minutes')->nullable()->after('stage_end_time');
            $table->decimal('progress_percentage', 5, 2)->default(0.00)->after('expected_duration_minutes');

            $table->index(['progress_stage', 'created_at']);
            $table->index(['progress_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_produces', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn([
                'progress_percentage',
                'estimated_completion_time',
                'actual_completion_time',
                'priority',
                'assigned_to'
            ]);
        });

        Schema::table('production_records', function (Blueprint $table) {
            $table->dropColumn([
                'progress_stage',
                'stage_start_time',
                'stage_end_time',
                'expected_duration_minutes',
                'progress_percentage'
            ]);
        });
    }
};