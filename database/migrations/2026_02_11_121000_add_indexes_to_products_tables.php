<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['branch_id', 'is_active'], 'idx_products_branch_active');
            $table->index(['product_type_id', 'is_active'], 'idx_products_type_active');
            $table->index(['is_active', 'is_available'], 'idx_products_status');
            $table->index(['name', 'sku'], 'idx_products_name_sku');
            $table->index('created_at', 'idx_products_created_at');
            $table->index(['branch_id', 'product_type_id', 'is_active'], 'idx_products_branch_type_active');
        });

        Schema::table('product_types', function (Blueprint $table) {
            $table->index(['department_id', 'is_active'], 'idx_product_types_dept_active');
            $table->index('is_active', 'idx_product_types_active');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->index(['slug', 'category_id'], 'idx_departments_slug_category');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_branch_active');
            $table->dropIndex('idx_products_type_active');
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_name_sku');
            $table->dropIndex('idx_products_created_at');
            $table->dropIndex('idx_products_branch_type_active');
        });

        Schema::table('product_types', function (Blueprint $table) {
            $table->dropIndex('idx_product_types_dept_active');
            $table->dropIndex('idx_product_types_active');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('idx_departments_slug_category');
        });
    }
};
