```php 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 1. Product Types Table
class CreateProductTypesTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., gelato_base, gelato_flavor, pastry, hot_kitchen, beverage
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
}

// 2. Products Table
class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('product_type_id');
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('restrict');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('price', 10, 2); // Price tag for the product
            $table->integer('shelf_life_days')->default(0); // Days after production before expiration
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units'])->default('pcs');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}

// 3. Recipes Table
class CreateRecipesTable extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->decimal('cost_per_unit', 10, 4)->default(0);
            $table->decimal('yield_quantity', 10, 2)->default(1); // How many product units this recipe produces
            $table->integer('preparation_time')->nullable(); // in minutes
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
}

// 4. Recipe Steps Table
class CreateRecipeStepsTable extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->integer('step_number');
            $table->text('description');
            $table->integer('estimated_time')->nullable(); // in minutes
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['recipe_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_steps');
    }
}

// 5. Recipe Ingredients Table
class CreateRecipeIngredientsTable extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->unsignedBigInteger('item_id')->nullable(); // Reference to raw item
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->unsignedBigInteger('sub_recipe_id')->nullable(); // Reference to sub-recipe
            $table->foreign('sub_recipe_id')->references('id')->on('recipes')->onDelete('restrict');
            $table->decimal('quantity', 12, 4); // Quantity needed per yield unit of parent recipe
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
}

// 6. Shifts Table
class CreateShiftsTable extends Migration
{
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
}

// 7. Daily Produces Table
class CreateDailyProducesTable extends Migration
{
    public function up(): void
    {
        Schema::create('daily_produces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('recipe_id');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->date('produce_date');
            $table->enum('shift_type', ['morning', 'afternoon']);
            $table->decimal('opening_quantity', 12, 2)->default(0); // From previous shift/day
            $table->decimal('planned_quantity', 12, 2)->default(0); // Planned production
            $table->decimal('max_possible_quantity', 12, 2)->default(0); // Based on available ingredients
            $table->decimal('produced_quantity', 12, 2)->default(0); // Actual produced
            $table->decimal('sent_out_quantity', 12, 2)->default(0); // Sent to sales
            $table->decimal('order_quantity', 12, 2)->default(0); // Ordered but not sent
            $table->decimal('callback_quantity', 12, 2)->default(0); // Bad/rejected items
            $table->decimal('closing_quantity', 12, 2)->default(0); // Left at end of shift
            $table->decimal('expected_closing', 12, 2)->default(0); // System-calculated
            $table->decimal('variance', 12, 2)->default(0); // Difference
            $table->text('production_shortfall_reason')->nullable(); // Reason for shortfall
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['shift_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_produces');
    }
}

// 8. Production Records Table
class CreateProductionRecordsTable extends Migration
{
    public function up(): void
    {
        Schema::create('production_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_produce_id');
            $table->foreign('daily_produce_id')->references('id')->on('daily_produces')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
}

// 9. Production Requests Table
class CreateProductionRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->unsignedBigInteger('item_request_id');
            $table->foreign('item_request_id')->references('id')->on('item_requests')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
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
}

// 10. Call Backs Table
class CreateCallBacksTable extends Migration
{
    public function up(): void
    {
        Schema::create('call_backs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->string('callback_type'); // 'inventory_item', 'produced_item'
            $table->unsignedBigInteger('reference_id'); // item_id or product_id
            $table->decimal('quantity', 12, 2);
            $table->enum('uom', ['grams', 'kg', 'liters', 'ml', 'pcs', 'units']);
            $table->enum('reason', ['expired', 'damaged', 'quality_issue', 'contaminated', 'deformed', 'lost', 'other']);
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
}

// 11. Raw Material Utilization Table
class CreateRawMaterialUtilizationsTable extends Migration
{
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
            $table->decimal('units_produced', 12, 2); // How many product units produced
            $table->decimal('variance', 12, 4)->default(0); // Difference
            $table->enum('variance_type', ['within_tolerance', 'over_used', 'under_used'])->default('within_tolerance');
            $table->text('variance_reason')->nullable(); // Reason for variance, e.g., loss or deformation
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
}

// Register migrations to run in sequence
return [
    new CreateProductTypesTable(),
    new CreateProductsTable(),
    new CreateRecipesTable(),
    new CreateRecipeStepsTable(),
    new CreateRecipeIngredientsTable(),
    new CreateShiftsTable(),
    new CreateDailyProducesTable(),
    new CreateProductionRecordsTable(),
    new CreateProductionRequestsTable(),
    new CreateCallBacksTable(),
    new CreateRawMaterialUtilizationsTable(),
];