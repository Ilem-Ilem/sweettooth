### Laravel Database Migrations for Manager.io-Like Features

To replicate core structures (especially focusing on **inventory system** integration) in a Laravel application, below are sample migration files. These cover key tables: customers, suppliers, inventory items, locations, kits, transfers, write-offs, purchases/sales (invoices/orders), and basic accounting links (e.g., journal entries simplified).

Use `php artisan make:migration` to create these, then run `php artisan migrate`.

#### 1. Inventory Items Table
```php
Schema::create('inventory_items', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique()->nullable(); // SKU
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('unit_name')->nullable(); // e.g., pieces, kg
    $table->decimal('default_buy_price', 15, 2)->default(0);
    $table->decimal('default_sell_price', 15, 2)->default(0);
    $table->decimal('current_average_cost', 15, 2)->default(0); // For COGS
    $table->decimal('quantity_on_hand', 15, 4)->default(0);
    $table->timestamps();
});
```

#### 2. Inventory Locations Table
```php
Schema::create('inventory_locations', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., Warehouse A
    $table->text('address')->nullable();
    $table->timestamps();
});

Schema::create('inventory_item_locations', function (Blueprint $table) { // Pivot for quantities per location
    $table->id();
    $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_location_id')->constrained()->cascadeOnDelete();
    $table->decimal('quantity', 15, 4)->default(0);
    $table->timestamps();
});
```

#### 3. Inventory Kits Table (Bundling)
```php
Schema::create('inventory_kits', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique()->nullable();
    $table->string('name');
    $table->decimal('kit_price', 15, 2)->default(0); // Bundle price
    $table->timestamps();
});

Schema::create('inventory_kit_components', function (Blueprint $table) { // Pivot
    $table->id();
    $table->foreignId('inventory_kit_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
    $table->decimal('quantity', 15, 4);
    $table->timestamps();
});
```

#### 4. Inventory Transfers and Write-offs
```php
Schema::create('inventory_transfers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
    $table->foreignId('from_location_id')->nullable()->constrained('inventory_locations');
    $table->foreignId('to_location_id')->nullable()->constrained('inventory_locations');
    $table->decimal('quantity', 15, 4);
    $table->date('transfer_date');
    $table->text('notes')->nullable();
    $table->timestamps();
});

Schema::create('inventory_write_offs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
    $table->decimal('quantity', 15, 4);
    $table->decimal('cost', 15, 2); // Value written off
    $table->date('write_off_date');
    $table->text('reason')->nullable();
    $table->timestamps();
});
```

#### 5. Supporting Tables (Customers, Suppliers, Invoices for Integration)
```php
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable();
    $table->text('address')->nullable();
    $table->timestamps();
});

Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable();
    $table->text('address')->nullable();
    $table->timestamps();
});

Schema::create('sales_invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->nullable()->constrained();
    $table->date('invoice_date');
    $table->decimal('total', 15, 2);
    $table->string('status')->default('draft'); // draft, sent, paid
    $table->timestamps();
});

Schema::create('sales_invoice_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_invoice_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items');
    $table->foreignId('inventory_kit_id')->nullable()->constrained('inventory_kits');
    $table->decimal('quantity', 15, 4);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('line_total', 15, 2);
    $table->timestamps();
});

// Similar for purchase_invoices and lines
```

#### 6. Production Orders (Basic Manufacturing)
```php
Schema::create('production_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('output_inventory_item_id')->constrained('inventory_items'); // Resulting item
    $table->decimal('output_quantity', 15, 4);
    $table->date('production_date');
    $table->timestamps();
});

Schema::create('production_order_components', function (Blueprint $table) {
    $table->id();
    $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('input_inventory_item_id')->constrained('inventory_items');
    $table->decimal('quantity_used', 15, 4);
    $table->timestamps();
});
```

Add more tables as needed (e.g., for taxes, journal entries, assets).
