# Observers for Purchases and Payables Integration

This guide explains how observers will automate purchase and payables processes, integrating with inventory, accounting, and supplier management.

## Purchase Workflow Observers

### 1. PurchaseQuoteObserver

Handles quote-to-order conversion.

```php
class PurchaseQuoteObserver
{
    public function updated(PurchaseQuote $quote): void
    {
        if ($quote->wasChanged('status') && $quote->status === 'accepted') {
            $this->createPurchaseOrder($quote);
        }
    }

    private function createPurchaseOrder(PurchaseQuote $quote): void
    {
        PurchaseOrder::create([
            'supplier_id' => $quote->supplier_id,
            'purchase_quote_id' => $quote->id,
            // Copy details
        ]);
    }
}
```

### 2. PurchaseOrderObserver

Manages order processing and goods receipt creation.

```php
class PurchaseOrderObserver
{
    public function updated(PurchaseOrder $order): void
    {
        if ($order->wasChanged('status')) {
            match($order->status) {
                'approved' => $this->updateBudget($order),
                'received' => $this->createGoodsReceipt($order),
                'completed' => $this->createPurchaseInvoice($order),
            };
        }
    }

    private function createGoodsReceipt(PurchaseOrder $order): void
    {
        GoodsReceipt::create([
            'purchase_order_id' => $order->id,
            // Initialize with order quantities
        ]);
    }

    private function createPurchaseInvoice(PurchaseOrder $order): void
    {
        PurchaseInvoice::create([
            'supplier_id' => $order->supplier_id,
            'purchase_order_id' => $order->id,
            // ...
        ]);
    }
}
```

### 3. GoodsReceiptObserver

Handles inventory updates upon goods receipt.

```php
class GoodsReceiptObserver
{
    public function created(GoodsReceipt $receipt): void
    {
        $this->updateInventory($receipt);
        $this->updatePurchaseOrderStatus($receipt);
    }

    private function updateInventory(GoodsReceipt $receipt): void
    {
        foreach ($receipt->lines as $line) {
            $item = $line->inventoryItem;
            $item->increment('quantity_on_hand', $line->quantity_received);
            
            // Update average cost
            $this->updateAverageCost($item, $line);
        }
    }

    private function updateAverageCost(InventoryItem $item, GoodsReceiptLine $line): void
    {
        $totalValue = ($item->quantity_on_hand * $item->current_average_cost) + 
                     ($line->quantity_received * $line->unit_price);
        $newQuantity = $item->quantity_on_hand + $line->quantity_received;
        $item->update([
            'current_average_cost' => $totalValue / $newQuantity
        ]);
    }

    private function updatePurchaseOrderStatus(GoodsReceipt $receipt): void
    {
        // Mark order as received if fully receipted
    }
}
```

### 4. PurchaseInvoiceObserver

Manages invoicing and three-way matching.

```php
class PurchaseInvoiceObserver
{
    protected GlPostingService $glPostingService;

    public function created(PurchaseInvoice $invoice): void
    {
        $this->updateSupplierBalance($invoice);
        $this->performThreeWayMatch($invoice);
    }

    public function updated(PurchaseInvoice $invoice): void
    {
        if ($invoice->wasChanged('status') && $invoice->status === 'approved') {
            $this->postToGL($invoice);
        }
    }

    private function updateSupplierBalance(PurchaseInvoice $invoice): void
    {
        $invoice->supplier->increment('balance', $invoice->total);
    }

    private function performThreeWayMatch(PurchaseInvoice $invoice): void
    {
        // Compare invoice vs order vs receipt
        $matched = $this->checkMatching($invoice);
        $invoice->update(['matching_status' => $matched ? 'matched' : 'unmatched']);
    }

    private function postToGL(PurchaseInvoice $invoice): void
    {
        try {
            if ($invoice->gl_posting_status !== 'pending') return;
            
            $this->glPostingService->postPurchaseInvoice($invoice);
            $invoice->update(['gl_posting_status' => 'posted']);
        } catch (Exception $e) {
            $invoice->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);
        }
    }
}
```

### 5. DebitNoteObserver

Handles purchase returns and adjustments.

```php
class DebitNoteObserver
{
    public function created(DebitNote $debitNote): void
    {
        $this->updateSupplierBalance($debitNote);
        $this->adjustOriginalInvoice($debitNote);
    }

    private function updateSupplierBalance(DebitNote $debitNote): void
    {
        $debitNote->supplier->decrement('balance', $debitNote->total);
    }
}
```

## Registration

```php
public function boot()
{
    PurchaseQuote::observe(PurchaseQuoteObserver::class);
    PurchaseOrder::observe(PurchaseOrderObserver::class);
    GoodsReceipt::observe(GoodsReceiptObserver::class);
    PurchaseInvoice::observe(PurchaseInvoiceObserver::class);
    DebitNote::observe(DebitNoteObserver::class);
}
```

## Benefits

- Automated procure-to-pay workflow
- Real-time inventory cost updates
- Consistent supplier balance tracking
- Quality control through matching processes

This ensures efficient and accurate purchase management.