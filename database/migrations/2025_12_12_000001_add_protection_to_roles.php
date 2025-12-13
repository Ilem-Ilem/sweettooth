<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add protection columns to roles table
        Schema::table('roles', function (Blueprint $table) {
            // Mark core system roles that cannot be deleted
            $table->boolean('is_protected')->default(false)->after('guard_name');
            $table->text('description')->nullable()->after('is_protected');
            $table->integer('display_order')->default(0)->after('description');
            
            $table->index('is_protected');
        });

        // Add columns to permissions for better organization
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('guard_name');
            $table->text('description')->nullable()->after('is_protected');
            $table->string('category')->default('general')->after('description');
            
            $table->index('is_protected');
            $table->index('category');
        });

        // Mark existing critical roles as protected
        \Spatie\Permission\Models\Role::query()
            ->whereIn('name', ['Super Admin', 'Managing Director', 'MD', 'Admin'])
            ->update(['is_protected' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_protected']);
            $table->dropColumn(['is_protected', 'description', 'category']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['is_protected']);
            $table->dropColumn(['is_protected', 'description', 'display_order']);
        });
    }
};
