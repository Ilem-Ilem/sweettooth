# Sales and Receivables Features Integration to Laravel System

This guide explains how to integrate sales and receivables features from Manager.io into the existing Laravel-based accounting system.

## Features Covered
- Customers
- Sales Quotes
- Sales Orders
- Sales Invoices
- Credit Notes
- Delivery Notes
- Late Payment Fees
- Billable Time
- Withholding Tax Receipts

## Implementation Steps

### 1. Database Migrations

Extend the existing customer and sales invoice tables, adding new ones:

```php
// Assuming customers table already exists, enhance if needed
Schema::table('customers', function (Blueprint $table) {
    $table->string('tax_id')->nullable()->after('address');
    $table->decimal('credit_limit', 15, 2)->default(0)->after('tax_id');
});

// Sales Quotes
Schema::create('sales_quotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained();
    $table->date('quote_date');
    $table->date('valid_until')->nullable();
    $table->decimal('total', 15, 2);
    $table->string('status')->default('draft'); // draft, sent, accepted, rejected
    $table->timestamps();
});

Schema::create('sales_quote_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_quote_id')->constrained()->cascadeOnDelete();
    $table->foreignId('inventory_item_id')->nullable()->constrained();
    $table->string('description');
    $table->decimal('quantity', 15, 4);
    $table->decimal('unit_price', 15, 2);
    $table->decimal('line_total', 15, 2);
    $table->timestamps();
});

// Sales Orders
Schema::create('sales_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained();
    $table->foreignId('sales_quote_id')->nullable()->constrained();
    $table->date('order_date');
    $table->date('due_date')->nullable();
    $table->decimal('total', 15, 2);
    $table->string('status')->default('pending'); // pending, confirmed, shipped
    $table->timestamps();
});

// Similar structure for sales_order_lines

// Assuming sales_invoices and sales_invoice_lines already exist, enhance
Schema::table('sales_invoices', function (Blueprint $table) {
    $table->foreignId('sales_order_id')->nullable()->constrained()->after('customer_id');
    $table->date('due_date')->nullable()->after('invoice_date');
    $table->decimal('tax_amount', 15, 2)->default(0)->after('total');
});

// Credit Notes
Schema::create('credit_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained();
    $table->foreignId('sales_invoice_id')->nullable()->constrained();
    $table->date('credit_note_date');
    $table->decimal('total', 15, 2);
    $table->text('reason')->nullable();
    $table->timestamps();
});

// Similar for credit_note_lines

// Delivery Notes
Schema::create('delivery_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_order_id')->nullable()->constrained();
    $table->foreignId('sales_invoice_id')->nullable()->constrained();
    $table->date('delivery_date');
    $table->text('delivery_address')->nullable();
    $table->string('status')->default('pending');
    $table->timestamps();
});

// Late Payment Fees
Schema::create('late_payment_fees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_invoice_id')->constrained();
    $table->decimal('fee_amount', 15, 2);
    $table->date('fee_date');
    $table->timestamps();
});

// Billable Time
Schema::create('billable_time_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->foreignId('project_id')->nullable()->constrained(); // Assuming projects table
    $table->date('entry_date');
    $table->decimal('hours', 8, 2);
    $table->decimal('hourly_rate', 15, 2);
    $table->text('description')->nullable();
    $table->boolean('billed')->default(false);
    $table->timestamps();
});

// Withholding Tax Receipts
Schema::create('withholding_tax_receipts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_invoice_id')->constrained();
    $table->decimal('withheld_amount', 15, 2);
    $table->decimal('tax_rate', 5, 2);
    $table->date('receipt_date');
    $table->timestamps();
});
```

### 2. Models and Relationships

Enhance existing models and add new ones:

```php
class Customer extends Model {
    public function salesQuotes() { return $this->hasMany(SalesQuote::class); }
    public function salesOrders() { return $this->hasMany(SalesOrder::class); }
    public function salesInvoices() { return $this->hasMany(SalesInvoice::class); }
    public function creditNotes() { return $this->hasMany(CreditNote::class); }
}

class SalesQuote extends Model {
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lines() { return $this->hasMany(SalesQuoteLine::class); }
}

class SalesOrder extends Model {
    public function customer() { return $this->belongsTo(Customer::class); }
    public function quote() { return $this->belongsTo(SalesQuote::class, 'sales_quote_id'); }
    public function deliveryNotes() { return $this->hasMany(DeliveryNote::class); }
}

class SalesInvoice extends Model {
    // Enhance with relationships to orders, credit notes, etc.
    public function order() { return $this->belongsTo(SalesOrder::class, 'sales_order_id'); }
    public function creditNotes() { return $this->hasMany(CreditNote::class); }
    public function lateFees() { return $this->hasMany(LatePaymentFee::class); }
    public function withholdingReceipts() { return $this->hasMany(WithholdingTaxReceipt::class); }
}

class CreditNote extends Model {
    public function customer() { return $this->belongsTo(Customer::class); }
    public function invoice() { return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id'); }
}

class DeliveryNote extends Model {
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function salesInvoice() { return $this->belongsTo(SalesInvoice::class); }
}

class BillableTimeEntry extends Model {
    public function employee() { return $this->belongsTo(Employee::class); }
    public function project() { return $this->belongsTo(Project::class); }
}

class WithholdingTaxReceipt extends Model {
    public function salesInvoice() { return $this->belongsTo(SalesInvoice::class); }
}
```

### 3. Controllers and Workflow

Create controllers for quote-to-order-to-invoice workflow:
- `SalesQuoteController`
- `SalesOrderController`
- `SalesInvoiceController` (enhance existing)
- `CreditNoteController`
- `DeliveryNoteController`
- `BillableTimeController`

Implement business logic for converting quotes to orders, orders to invoices, etc.

### 4. Integration Points

- **Workflow**: Quotes → Orders → Invoices → Delivery Notes
- **Inventory**: Deduct stock on order confirmation or invoice posting
- **Accounting**: Auto-create journal entries for sales, credit notes, fees
- **Billable Time**: Link to invoices for time-based billing
- **Taxes**: Calculate and withhold taxes automatically
- **Aging Reports**: Track overdue invoices and apply late fees

### 5. Additional Features

- Email integration for sending quotes/invoices
- PDF generation for documents
- Approval workflows for quotes/orders
- Customer credit limit checks
- Automated late fee calculation

This extends the sales process with comprehensive order management and receivables tracking.