# Purchases and Payables Features Integration to Laravel System

This guide explains how to integrate purchases and payables features from Manager.io into the existing Laravel-based accounting system.

## Features Covered
- Suppliers
- Purchase Quotes
- Purchase Orders
- Purchase Invoices
- Debit Notes
- Goods Receipts

## Implementation Steps

### 1. Database Migrations

Enhance existing supplier table and add purchase-related tables:

```php
// Assuming suppliers table exists, enhance if needed
Schema::table('suppliers', function (Blueprint $table) {
    $table->string('tax_id')->nullable()->after('address');
    $table->integer('payment_terms')->default(30)->after('tax_id'); // days
});

// Purchase Quotes
Schema::create('purchase_quotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained();
    $table->date('quote_date');
    $table->date('valid_until')->nullable();
    $table->decimal('total', 15, 2);
    $table->string('status')->default('draft'); // draft, sent, accepted, rejected
    $table->timestamps();
});

Schema::create('purchase_quote_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('purchase_quote_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->nullable()->constrained();
    $table->string('description');
    $table->decimal('quantity', 15, 4);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('line_total', 15, 2);
    $table->timestamps();
});

// Purchase Orders
Schema::create('purchase_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained();
    $table->foreignId('purchase_quote_id')->nullable()->constrained();
    $table->date('order_date');
    $table->date('expected_date')->nullable();
    $table->decimal('total', 15, 2);
    $table->string('status')->default('pending'); // pending, confirmed, received
    $table->timestamps();
});

Schema::create('purchase_order_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->nullable()->constrained();
    $table->decimal('quantity', 15, 4);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('line_total', 15, 2);
    $table->timestamps();
});

// Assuming purchase_invoices and lines exist, enhance
Schema::table('purchase_invoices', function (Blueprint $table) {
    $table->foreignId('purchase_order_id')->nullable()->constrained()->after('supplier_id');
    $table->date('due_date')->nullable()->after('invoice_date');
    $table->decimal('tax_amount', 15, 2)->default(0)->after('total');
});

// Debit Notes
Schema::create('debit_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained();
    $table->foreignId('purchase_invoice_id')->nullable()->constrained();
    $table->date('debit_note_date');
    $table->decimal('total', 15, 2);
    $table->text('reason')->nullable();
    $table->timestamps();
});

Schema::create('debit_note_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('debit_note_id')->constrained()->cascadeOnDelete();
    $table->string('description');
    $table->decimal('quantity', 15, 4);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('line_total', 15, 2);
    $table->timestamps();
});

// Goods Receipts
Schema::create('goods_receipts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('purchase_order_id')->nullable()->constrained();
    $table->foreignId('purchase_invoice_id')->nullable()->constrained();
    $table->date('receipt_date');
    $table->string('receipt_number')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

Schema::create('goods_receipt_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('goods_receipt_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->constrained();
    $table->foreignId('purchase_order_line_id')->nullable()->constrained();
    $table->decimal('quantity_received', 15, 4);
    $table->decimal('quantity_accepted', 15, 4);
    $table->timestamps();
});
```

### 2. Models and Relationships

```php
class Supplier extends Model {
    public function purchaseQuotes() { return $this->hasMany(PurchaseQuote::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function purchaseInvoices() { return $this->hasMany(PurchaseInvoice::class); }
    public function debitNotes() { return $this->hasMany(DebitNote::class); }
}

class PurchaseQuote extends Model {
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function lines() { return $this->hasMany(PurchaseQuoteLine::class); }
}

class PurchaseOrder extends Model {
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function quote() { return $this->belongsTo(PurchaseQuote::class, 'purchase_quote_id'); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
}

class PurchaseInvoice extends Model {
    public function order() { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id'); }
    public function debitNotes() { return $this->hasMany(DebitNote::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
}

class DebitNote extends Model {
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function invoice() { return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id'); }
}

class GoodsReceipt extends Model {
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); }
    public function lines() { return $this->hasMany(GoodsReceiptLine::class); }
}

class GoodsReceiptLine extends Model {
    public function goodsReceipt() { return $this->belongsTo(GoodsReceipt::class); }
    public function inventoryItem() { return $this->belongsTo(InventoryItem::class); }
    public function purchaseOrderLine() { return $this->belongsTo(PurchaseOrderLine::class); }
}
```

### 3. Controllers and Workflow

- `PurchaseQuoteController`
- `PurchaseOrderController`
- `PurchaseInvoiceController` (enhance existing)
- `DebitNoteController`
- `GoodsReceiptController`

Implement quote-to-order-to-invoice-to-receipt workflow.

### 4. Integration Points

- **Workflow**: Quotes → Orders → Goods Receipts → Invoices
- **Inventory**: Increase stock on goods receipt, update average costs
- **Accounting**: Auto-create journal entries for purchases, debit notes
- **Three-way matching**: Compare order, receipt, and invoice quantities/prices
- **Supplier management**: Track payment terms, outstanding balances

### 5. Additional Features

- Automated purchase order generation from low stock alerts
- Quality control in goods receipts
- Purchase analytics and supplier performance tracking
- Integration with payment processing for payables

This completes the purchase cycle with proper procurement management.