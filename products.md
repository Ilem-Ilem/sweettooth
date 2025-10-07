
<?php

// 1. Recipes Table Migration
// File: 2025_09_30_200001_create_recipes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->string('product_name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->enum('product_type', ['gelato_base', 'gelato_flavor', 'pastry', 'hot_kitchen', 'beverage']);
            $table->decimal('cost_per_unit', 10, 4)->default(0);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units'])->default('pcs');
            $table->decimal('yield_quantity', 10, 2)->default(1); // How many units this recipe produces
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'inactive', 'testing'])->default('active');
            $table->uuid('created_by');
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};

// 2. Recipe Ingredients Table Migration
// File: 2025_09_30_200002_create_recipe_ingredients_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->decimal('quantity', 12, 4); // Quantity needed per unit of recipe
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units']);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};

// 3. Shifts Table Migration
// File: 2025_09_30_200003_create_shifts_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
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
            $table->enum('status', ['active', 'closed', 'submitted'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'department_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

// 4. Daily Produces Table Migration (Production Dashboard)
// File: 2025_09_30_200004_create_daily_produces_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_produces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->date('produce_date');
            $table->enum('shift_type', ['morning', 'afternoon']);
            
            // Opening - what they met from previous shift/day
            $table->decimal('opening_quantity', 12, 2)->default(0);
            
            // Requested - items requested from inventory for this production
            $table->decimal('requested_quantity', 12, 2)->default(0);
            
            // Produced - quantity produced in this shift
            $table->decimal('produced_quantity', 12, 2)->default(0);
            
            // Sent Out - quantity sent to sales departments
            $table->decimal('sent_out_quantity', 12, 2)->default(0);
            
            // Order - what was ordered but not yet sent
            $table->decimal('order_quantity', 12, 2)->default(0);
            
            // Call Back - items that were bad/rejected
            $table->decimal('callback_quantity', 12, 2)->default(0);
            
            // Closing - what's left at end of shift (auto-calculated)
            $table->decimal('closing_quantity', 12, 2)->default(0);
            
            // Expected Closing - system calculated based on formula
            $table->decimal('expected_closing', 12, 2)->default(0);
            
            // Variance
            $table->decimal('variance', 12, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['shift_id', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_produces');
    }
};

// 5. Production Records Table (Detailed production logs)
// File: 2025_09_30_200005_create_production_records_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_produce_id');
            $table->foreign('daily_produce_id')->references('id')->on('daily_produces')->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->uuid('produced_by');
            $table->foreign('produced_by')->references('id')->on('employees')->onDelete('restrict');
            $table->decimal('quantity_produced', 12, 2);
            $table->decimal('quantity_approved', 12, 2)->default(0);
            $table->decimal('quantity_rejected', 12, 2)->default(0);
            $table->timestamp('production_time');
            $table->enum('quality_status', ['excellent', 'good', 'acceptable', 'rejected'])->default('good');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_records');
    }
};

// 6. Production Requests Table (Requests from inventory)
// File: 2025_09_30_200006_create_production_requests_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('item_request_id');
            $table->foreign('item_request_id')->references('id')->on('item_requests')->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id')->nullable();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('set null');
            $table->decimal('planned_production_quantity', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_requests');
    }
};

// 7. Call Backs Table (Bad/rejected items)
// File: 2025_09_30_200007_create_call_backs_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('call_backs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->string('callback_type'); // 'inventory_item', 'produced_item'
            $table->unsignedBigInteger('reference_id'); // item_id or recipe_id
            $table->decimal('quantity', 12, 2);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units']);
            $table->enum('reason', ['expired', 'damaged', 'quality_issue', 'contaminated', 'other']);
            $table->text('description')->nullable();
            $table->uuid('reported_by');
            $table->foreign('reported_by')->references('id')->on('employees')->onDelete('restrict');
            $table->timestamp('callback_time');
            $table->enum('action_taken', ['disposed', 'returned_to_supplier', 'reprocessed', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_backs');
    }
};

// 8. Raw Material Utilization Table
// File: 2025_09_30_200008_create_raw_material_utilizations_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_material_utilizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->decimal('quantity_required', 12, 4); // Per recipe unit
            $table->decimal('quantity_used', 12, 4); // Actual used
            $table->decimal('units_produced', 12, 2); // How many recipe units produced
            $table->decimal('variance', 12, 4)->default(0); // Difference
            $table->enum('variance_type', ['within_tolerance', 'over_used', 'under_used'])->default('within_tolerance');
            $table->decimal('cost_impact', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['shift_id', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_utilizations');
    }
};
