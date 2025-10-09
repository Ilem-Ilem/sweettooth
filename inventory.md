```php
<?php

// 1. Items Table Migration
// File: 2025_09_30_100001_create_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('category', ['raw_material', 'packaging', 'consumable', 'equipment']);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units', 'bags', 'cartons']);
            $table->text('description')->nullable();
            $table->decimal('reorder_level', 10, 2)->nullable();
            $table->decimal('max_stock_level', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

// 2. Purchases Table Migration
// File: 2025_09_30_100002_create_purchases_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->uuid('recorded_by');
            $table->foreign('recorded_by')->references('id')->on('employees')->onDelete('restrict');
            $table->string('purchase_number')->unique();
            $table->date('purchase_date');
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->decimal('total_fob_fc', 12, 2)->default(0);
            $table->decimal('total_fob_ngn', 12, 2)->default(0);
            $table->decimal('other_costs', 12, 2)->default(0);
            $table->decimal('landing_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('currency', 3)->default('NGN');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->enum('payment_status', ['paid', 'partial', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

// 3. Purchase Items Table Migration
// File: 2025_09_30_100003_create_purchase_items_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->decimal('quantity', 12, 2);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units', 'bags', 'cartons']);
            $table->decimal('fob_fc', 12, 2)->default(0);
            $table->decimal('fob_ngn', 12, 2)->default(0);
            $table->decimal('other_costs', 12, 2)->default(0);
            $table->decimal('landing_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('cost_per_unit', 12, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};

// 4. Stocks Table Migration
// File: 2025_09_30_100004_create_stocks_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->decimal('quantity_available', 12, 2)->default(0);
            $table->decimal('quantity_reserved', 12, 2)->default(0);
            $table->decimal('quantity_damaged', 12, 2)->default(0);
            $table->decimal('average_cost', 12, 4)->default(0);
            $table->date('last_stock_take_date')->nullable();
            $table->enum('health_status', ['good', 'warning', 'critical', 'expired'])->default('good');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

// 5. Stock Movements Table Migration
// File: 2025_09_30_100005_create_stock_movements_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('quantity_before', 12, 2);
            $table->decimal('quantity_after', 12, 2);
            $table->string('reference_type')->nullable(); // 'purchase', 'dispatch', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->uuid('moved_by');
            $table->foreign('moved_by')->references('id')->on('employees')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

// 6. Item Requests Table Migration
// File: 2025_09_30_100006_create_item_requests_table.php

return new class extends Migration {
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

    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};

// 7. Item Request Details Table
// File: 2025_09_30_100007_create_item_request_details_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('item_requests')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->decimal('quantity_requested', 12, 2);
            $table->decimal('quantity_approved', 12, 2)->default(0);
            $table->decimal('quantity_dispatched', 12, 2)->default(0);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units', 'bags', 'cartons']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_request_details');
    }
};

// 8. Item Dispatches Table Migration
// File: 2025_09_30_100008_create_item_dispatches_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_dispatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('item_requests')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->uuid('dispatched_by');
            $table->foreign('dispatched_by')->references('id')->on('employees')->onDelete('restrict');
            $table->uuid('received_by');
            $table->foreign('received_by')->references('id')->on('employees')->onDelete('restrict');
            $table->decimal('quantity', 12, 2);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units', 'bags', 'cartons']);
            $table->timestamp('dispatch_time');
            $table->timestamp('received_time')->nullable();
            $table->enum('shift', ['morning', 'afternoon'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_dispatches');
    }
};

// 9. Stock Takes Table Migration
// File: 2025_09_30_100009_create_stock_takes_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_takes', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->string('stock_take_number')->unique();
            $table->date('stock_take_date');
            $table->enum('type', ['daily', 'weekly', 'monthly', 'annual', 'ad_hoc']);
            $table->uuid('conducted_by');
            $table->foreign('conducted_by')->references('id')->on('employees')->onDelete('restrict');
            $table->enum('status', ['in_progress', 'completed', 'verified'])->default('in_progress');
            $table->uuid('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_takes');
    }
};

// 10. Stock Take Details Table
// File: 2025_09_30_100010_create_stock_take_details_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_take_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_take_id');
            $table->foreign('stock_take_id')->references('id')->on('stock_takes')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->decimal('system_quantity', 12, 2);
            $table->decimal('physical_quantity', 12, 2);
            $table->decimal('variance', 12, 2);
            $table->enum('variance_type', ['surplus', 'shortage', 'match'])->default('match');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_take_details');
    }
};

// 11. Health Checks Table Migration
// File: 2025_09_30_100011_create_health_checks_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('health_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->uuid('checked_by');
            $table->foreign('checked_by')->references('id')->on('employees')->onDelete('restrict');
            $table->date('check_date');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged', 'expired']);
            $table->decimal('quantity_affected', 12, 2)->nullable();
            $table->text('observations')->nullable();
            $table->text('action_taken')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_checks');
    }
};
