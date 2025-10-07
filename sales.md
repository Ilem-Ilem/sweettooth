<?php

// 1. Product Categories Table Migration
// File: 2025_09_30_300001_create_product_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['food', 'beverage', 'dessert', 'gelato', 'pastry', 'other']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};

// 2. Products Table Migration
// File: 2025_09_30_300002_create_products_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->unsignedBigInteger('recipe_id')->nullable();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('set null');
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->enum('source', ['kitchen', 'gelato', 'pastry', 'hot_kitchen', 'in_house']); // in_house = produced in sales dept
            $table->boolean('available_for_glovo')->default(false);
            $table->boolean('available_for_transfer')->default(true);
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

// 3. Tables Table Migration (Restaurant tables)
// File: 2025_09_30_300003_create_tables_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->string('table_number')->unique();
            $table->integer('capacity')->default(4);
            $table->enum('status', ['available', 'occupied', 'reserved', 'needs_cleaning'])->default('available');
            $table->enum('location', ['indoor', 'outdoor', 'vip', 'regular'])->default('regular');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};

// 4. Sales Shifts Table
// File: 2025_09_30_300004_create_sales_shifts_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_shifts', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('shift_number')->unique();
            $table->date('shift_date');
            $table->enum('shift_type', ['morning', 'afternoon', 'night']);
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->decimal('opening_cash', 10, 2)->default(0);
            $table->decimal('closing_cash', 10, 2)->default(0);
            $table->decimal('expected_cash', 10, 2)->default(0);
            $table->decimal('cash_variance', 10, 2)->default(0);
            $table->enum('status', ['active', 'closed', 'submitted', 'verified'])->default('active');
            $table->uuid('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_shifts');
    }
};

// 5. Sales Table Migration
// File: 2025_09_30_300005_create_sales_table.php

return new class extends Migration {
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
            $table->unsignedBigInteger('table_id')->nullable();
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
            $table->string('table_number')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

// 6. Sale Items Table Migration
// File: 2025_09_30_300006_create_sale_items_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

// 7. Payments Table Migration
// File: 2025_09_30_300007_create_payments_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'pos', 'transfer', 'card', 'mobile'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->timestamp('payment_time');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

// 8. Table Orders Table Migration
// File: 2025_09_30_300008_create_table_orders_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('table_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->unsignedBigInteger('sales_shift_id');
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
            $table->uuid('served_by');
            $table->foreign('served_by')->references('id')->on('employees')->onDelete('restrict');
            $table->string('order_number')->unique();
            $table->timestamp('order_time');
            $table->integer('guest_count')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('order_status', ['pending', 'in_progress', 'ready', 'served', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->boolean('is_cleared')->default(false);
            $table->timestamp('cleared_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_orders');
    }
};

// 9. Table Order Items Table
// File: 2025_09_30_300009_create_table_order_items_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('table_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_order_id');
            $table->foreign('table_order_id')->references('id')->on('table_orders')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_order_items');
    }
};

// 10. Product Stock Table (for sales departments)
// File: 2025_09_30_300010_create_product_stocks_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_id');
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->date('stock_date');
            $table->enum('shift_type', ['morning', 'afternoon']);
            
            // Opening - from previous shift/day
            $table->decimal('opening_quantity', 12, 2)->default(0);
            
            // Addition - received from kitchen or other sources
            $table->decimal('addition_quantity', 12, 2)->default(0);
            
            // Call Back - items rejected/returned
            $table->decimal('callback_quantity', 12, 2)->default(0);
            
            // Redress - items that needed to be fixed/adjusted
            $table->decimal('redress_quantity', 12, 2)->default(0);
            
            // Total Available
            $table->decimal('total_available', 12, 2)->default(0);
            
            // Transfer - sent to other departments
            $table->decimal('transfer_quantity', 12, 2)->default(0);
            
            // Glovo - sold through Glovo
            $table->decimal('glovo_quantity', 12, 2)->default(0);
            
            // Quantity Sold - regular sales
            $table->decimal('quantity_sold', 12, 2)->default(0);
            
            // Closing - what's left
            $table->decimal('closing_quantity', 12, 2)->default(0);
            
            // Amount - total sales amount for this product
            $table->decimal('amount', 12, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['sales_shift_id', 'product_id', 'stock_date', 'shift_type'], 'unique_product_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};

// 11. Transfers Table (between departments)
// File: 2025_09_30_300011_create_transfers_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('from_department_id');
            $table->foreign('from_department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->unsignedBigInteger('to_department_id');
            $table->foreign('to_department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->string('transfer_number')->unique();
            $table->uuid('transferred_by');
            $table->foreign('transferred_by')->references('id')->on('employees')->onDelete('restrict');
            $table->uuid('received_by')->nullable();
            $table->foreign('received_by')->references('id')->on('employees')->onDelete('set null');
            $table->decimal('quantity', 12, 2);
            $table->timestamp('transfer_time');
            $table->timestamp('received_time')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'received', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

// 12. Kitchen Orders Table (orders from sales to production)
// File: 2025_09_30_300012_create_kitchen_orders_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kitchen_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_id');
            $table->foreign('sales_shift_id')->references('id')->on('sales_shifts')->onDelete('cascade');
            $table->unsignedBigInteger('production_department_id');
            $table->foreign('production_department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->string('order_number')->unique();
            $table->uuid('ordered_by');
            $table->foreign('ordered_by')->references('id')->on('employees')->onDelete('restrict');
            $table->decimal('quantity_ordered', 12, 2);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->timestamp('order_time');
            $table->timestamp('expected_ready_time')->nullable();
            $table->timestamp('actual_ready_time')->nullable();
            $table->timestamp('received_time')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_orders');
    }
};
