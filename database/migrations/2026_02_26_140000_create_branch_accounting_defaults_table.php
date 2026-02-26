<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_accounting_defaults', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->string('key');
            $table->unsignedBigInteger('gl_account_id');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->onDelete('restrict');
            $table->unique(['branch_id', 'key']);
            $table->index(['branch_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_accounting_defaults');
    }
};
