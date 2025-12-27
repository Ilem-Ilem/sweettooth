# Observers for Sales and Receivables Integration

This guide explains how observers will automate sales and receivables processes, integrating with inventory, accounting, and workflow management.

## Sales Workflow Observers

### 1. SalesQuoteObserver

Handles quote-to-order conversion automation.

```php
class SalesQuoteObserver
{
    public function updated(SalesQuote $quote): void
    {
        if ($quote->wasChanged('status') && $quote->status === 'accepted') {
            $this->createSalesOrder($quote);
        }
    }

    private function createSalesOrder(SalesQuote $quote): void
    {
        SalesOrder::create([
            'customer_id' => $quote->customer_id,
            'sales_quote_id' => $quote->id,
            'order_date' => now(),
            'total' => $quote->total,
            // Copy line items
        ]);
    }
}
```

### 2. SalesOrderObserver

Manages order fulfillment and invoice creation.

```php
class SalesOrderObserver
{
    public function updated(SalesOrder $order): void
    {
        if ($order->wasChanged('status')) {
            match($order->status) {
                'confirmed' => $this->reserveInventory($order),
                'shipped' => $this->createDeliveryNote($order),
                'completed' => $this->createSalesInvoice($order),
            };
        }
    }

    private function reserveInventory(SalesOrder $order): void
    {
        foreach ($order->lines as $line) {
            // Reserve inventory quantities
        }
    }

    private function createDeliveryNote(SalesOrder $order): void
    {
        DeliveryNote::create([
            'sales_order_id' => $order->id,
            // ...
        ]);
    }

    private function createSalesInvoice(SalesOrder $order): void
    {
        SalesInvoice::create([
            'customer_id' => $order->customer_id,
            'sales_order_id' => $order->id,
            // ...
        ]);
    }
}
```

### 3. SalesInvoiceObserver

Handles invoicing, payments, and aging.

```php
class SalesInvoiceObserver
{
    protected GlPostingService $glPostingService;

    public function created(SalesInvoice $invoice): void
    {
        $this->updateCustomerBalance($invoice);
        $this->scheduleDueDateChecks($invoice);
    }

    public function updated(SalesInvoice $invoice): void
    {
        if ($invoice->wasChanged('status') && $invoice->status === 'paid') {
            $this->postToGL($invoice);
            $this->updateInventory($invoice);
        }
    }

    private function updateCustomerBalance(SalesInvoice $invoice): void
    {
        $invoice->customer->increment('balance', $invoice->total);
    }

    private function postToGL(SalesInvoice $invoice): void
    {
        try {
            if ($invoice->gl_posting_status !== 'pending') return;
            
            $this->glPostingService->postSalesInvoice($invoice);
            
            $invoice->update(['gl_posting_status' => 'posted']);
        } catch (Exception $e) {
            $invoice->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);
        }
    }

    private function updateInventory(SalesInvoice $invoice): void
    {
        foreach ($invoice->lines as $line) {
            if ($line->inventory_item_id) {
                $line->inventoryItem->decrement('quantity_on_hand', $line->quantity);
                // Update COGS
            }
        }
    }

    private function scheduleDueDateChecks(SalesInvoice $invoice): void
    {
        // Schedule late fee calculation
    }
}
```

### 4. CreditNoteObserver

Manages credit note processing and reversals.

```php
class CreditNoteObserver
{
    public function created(CreditNote $creditNote): void
    {
        $this->updateCustomerBalance($creditNote);
        $this->adjustOriginalInvoice($creditNote);
    }

    private function updateCustomerBalance(CreditNote $creditNote): void
    {
        $creditNote->customer->decrement('balance', $creditNote->total);
    }

    private function adjustOriginalInvoice(CreditNote $creditNote): void
    {
        if ($creditNote->sales_invoice_id) {
            // Adjust original invoice totals
        }
    }
}
```

### 5. BillableTimeEntryObserver

Integrates time tracking with invoicing.

```php
class BillableTimeEntryObserver
{
    public function updated(BillableTimeEntry $entry): void
    {
        if ($entry->wasChanged('billed') && $entry->billed) {
            $this->addToInvoice($entry);
        }
    }

    private function addToInvoice(BillableTimeEntry $entry): void
    {
        // Add unbilled time to customer invoices
    }
}
```

## Registration

```php
public function boot()
{
    SalesQuote::observe(SalesQuoteObserver::class);
    SalesOrder::observe(SalesOrderObserver::class);
    SalesInvoice::observe(SalesInvoiceObserver::class);
    CreditNote::observe(CreditNoteObserver::class);
    BillableTimeEntry::observe(BillableTimeEntryObserver::class);
}
```

## Benefits

- Automated workflow progression
- Real-time inventory and financial updates
- Consistent order-to-cash process
- Reduced manual intervention and errors

This creates a seamless sales and receivables management system.