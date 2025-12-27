# Purchase Observer Integration Details

This guide details how the new purchase observers integrate with the existing PurchaseObserver.

## Existing PurchaseObserver Analysis

### Current Implementation
```php
class PurchaseObserver
{
    public function updated(Purchase $purchase): void
    {
        if ($purchase->status === 'approved' && $purchase->gl_posting_status === 'pending') {
            $this->postToGL($purchase);
        }
    }
}
```

### Integration Points
- **Model**: Existing `Purchase` model
- **GL Posting**: Uses `GlPostingService::postPurchaseTransaction()`
- **Trigger**: Status change to 'approved'

## New Purchase Observers Integration

### PurchaseInvoiceObserver Compatibility
- **Model**: New `PurchaseInvoice` model
- **Workflow**: Quote → Order → Goods Receipt → Invoice → Approval
- **Integration**: Extends existing approval-based posting

### Proposed Integration Approach
```php
// Extend existing logic
class EnhancedPurchaseObserver extends PurchaseObserver
{
    public function updated(Purchase $purchase): void
    {
        parent::updated($purchase);
        // Add three-way matching if applicable
    }
}

// New workflow observer
class PurchaseInvoiceObserver
{
    public function updated(PurchaseInvoice $invoice): void
    {
        if ($invoice->status === 'approved' && $invoice->matching_status === 'matched') {
            $this->postToGL($invoice);
        }
    }

    private function postToGL(PurchaseInvoice $invoice): void
    {
        // Use similar logic to PurchaseObserver
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

### Workflow Mapping
- **Existing**: Purchase creation → approval → GL posting
- **New**: Supplier → Quote → Order → Receipt → Invoice → Matching → Approval → GL posting
- **Integration**: New workflow can create Purchase records or extend existing

### Goods Receipt Integration
- New `GoodsReceipt` observer updates inventory
- Complements existing purchase approval
- Ensures stock is received before invoice approval

### Three-Way Matching
- New feature not in existing observer
- Adds validation before GL posting
- Enhances existing approval process

### Database Integration
- Existing `purchases` table
- New tables: `purchase_quotes`, `purchase_orders`, `goods_receipts`, `purchase_invoices`
- Link via foreign keys

### Testing Integration
```php
class PurchaseIntegrationTest
{
    public function test_existing_purchase_workflow()
    {
        // Test PurchaseObserver
    }

    public function test_new_procurement_workflow()
    {
        // Test full quote-to-invoice workflow
    }
}
```

## Recommendations
- Existing PurchaseObserver handles basic posting
- New observers add procurement workflow
- Enable gradually with feature flags
- Ensure inventory updates don't conflict