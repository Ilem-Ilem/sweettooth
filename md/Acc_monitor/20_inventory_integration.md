# Inventory Management Features Integration to Laravel System

This guide explains how to enhance the existing inventory system with additional Manager.io features.

## Features Covered
- Inventory Items (enhance existing)
- Inventory Transfers
- Inventory Write-offs
- Production Orders

## Implementation Steps

### 1. Database Enhancements

Build on existing inventory tables:

```php
// Assuming inventory_items, inventory_locations, inventory_transfers, inventory_write_offs exist
// Enhance inventory_items if needed
Schema::table('inventory_items', function (Blueprint $table) {
    $table->string('category')->nullable()->after('unit_name');
    $table->boolean('track_stock')->default(true)->after('quantity_on_hand');
});

// Production Orders (enhance existing if basic exists)
Schema::table('production_orders', function (Blueprint $table) {
    $table->string('status')->default('planned')->after('production_date'); // planned, in_progress, completed
    $table->decimal('total_cost', 15, 2)->default(0)->after('output_quantity');
});

// Add if not existing
Schema::create('inventory_adjustments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
    $table->string('type'); // transfer, write_off, adjustment
    $table->decimal('quantity_change', 15, 4);
    $table->decimal('cost_impact', 15, 2)->nullable();
    $table->date('adjustment_date');
    $table->text('reason')->nullable();
    $table->timestamps();
});
```

### 2. Model Enhancements

```php
class InventoryItem extends Model {
    // Existing relationships
    public function transfers() { return $this->hasMany(InventoryTransfer::class); }
    public function writeOffs() { return $this->hasMany(InventoryWriteOff::class); }
    public function productionOutputs() { return $this->hasMany(ProductionOrder::class, 'output_inventory_item_id'); }
    public function adjustments() { return $this->hasMany(InventoryAdjustment::class); }
}

class InventoryTransfer extends Model {
    // Existing
    public function item() { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
}

class ProductionOrder extends Model {
    public function outputItem() { return $this->belongsTo(InventoryItem::class, 'output_inventory_item_id'); }
    public function components() { return $this->hasMany(ProductionOrderComponent::class); }

    // Cost calculation
    public function calculateTotalCost() {
        return $this->components->sum(function($component) {
            return $component->quantity_used * $component->inventoryItem->current_average_cost;
        });
    }
}

class ProductionOrderComponent extends Model {
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class); }
    public function inventoryItem() { return $this->belongsTo(InventoryItem::class, 'input_inventory_item_id'); }
}
```

### 3. Business Logic Enhancements

- **Transfers**: Update location-specific quantities, create adjustment records
- **Write-offs**: Reduce quantity, record cost impact, create journal entries for inventory loss
- **Production Orders**: Deduct component quantities, add output quantity, calculate costs

### 4. Controllers and Views

Enhance existing `InventoryController` with methods for:
- Transfer management
- Write-off processing
- Production order creation and completion

Create views for production workflows.

### 5. Integration Points

- **Sales/Purchases**: Auto-adjust stock levels
- **Production**: Convert components to finished goods
- **Reporting**: Stock valuation, movement history
- **Costing**: Weighted average updates on receipts

### 6. Additional Features

- Stock alerts for low/high levels
- Batch/lot tracking
- Serial number management
- FIFO/LIFO costing methods
- Integration with barcode scanning

This builds on the existing inventory foundation with advanced management capabilities.