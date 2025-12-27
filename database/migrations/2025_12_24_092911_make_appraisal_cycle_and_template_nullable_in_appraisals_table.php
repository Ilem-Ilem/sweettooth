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
        Schema::table('appraisals', function (Blueprint $table) {
            $table->foreignId('appraisal_cycle_id')->nullable()->change();
            $table->foreignId('template_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisals', function (Blueprint $table) {
            $table->foreignId('appraisal_cycle_id')->nullable(false)->change();
            $table->foreignId('template_id')->nullable(false)->change();
        });
    }
};
