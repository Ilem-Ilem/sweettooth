<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('gl_accounts', 'branch_id')) {
                $table->uuid('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->index('branch_id');
            }

            $table->dropUnique(['account_number']);
            $table->unique(['branch_id', 'account_number']);
        });
    }

    public function down(): void
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'account_number']);

            if (Schema::hasColumn('gl_accounts', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['branch_id']);
                $table->dropColumn('branch_id');
            }

            $table->unique(['account_number']);
        });
    }
};
