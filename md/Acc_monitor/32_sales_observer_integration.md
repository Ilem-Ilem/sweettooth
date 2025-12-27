# Sales Observer Integration Details

This guide details how the new sales observers integrate with the existing SaleObserver and related models.

## Existing SaleObserver Analysis

### Current Implementation
```php
class SaleObserver
{
    public function created(Sale $sale): void
    {
        if ($sale->status === 'completed' && $sale->isFullyPaid()) {
            $this->postToGL($sale);
        }
    }

    public function updated(Sale $sale): void
    {
        if ($sale->status === 'completed' && $sale->gl_posting_status === 'pending') {
            $this->postToGL($sale);
        }
    }
}
```

### Integration Points
- **Model**: Existing `Sale` model
- **GL Posting**: Uses `GlPostingService::postSaleTransaction()`
- **Status Checks**: Completes when fully paid

## New Sales Observers Integration

### SalesInvoiceObserver Compatibility
- **Model**: New `SalesInvoice` model (enhanced version of Sale)
- **Workflow**: Quote → Order → Invoice → Payment
- **Integration**: Can replace or extend SaleObserver

### Proposed Integration Approach
```php
// Option 1: Extend existing SaleObserver
class EnhancedSaleObserver extends SaleObserver
{
    public function created(Sale $sale): void
    {
        parent::created($sale);
        // Add new workflow logic if Sale model is enhanced
    }
}

// Option 2: Use SalesInvoiceObserver for new workflow
class SalesInvoiceObserver
{
    protected SaleObserver $saleObserver;

    public function __construct(SaleObserver $saleObserver)
    {
        $this->saleObserver = $saleObserver;
    }

    public function created(SalesInvoice $invoice): void
    {
        // New logic for invoice workflow
        $this->updateCustomerBalance($invoice);
        
        // If compatible, trigger existing sale posting
        if ($this->mapsToExistingSale($invoice)) {
            // Simulate SaleObserver logic
        }
    }
}
```

### Workflow Mapping
- **Existing**: Direct sale creation → completion → GL posting
- **New**: Customer → Quote → Order → Invoice → Delivery → Payment → GL posting
- **Integration**: New workflow can create Sale records for existing observer compatibility

### GL Posting Coordination
- Ensure no double-posting by checking unique identifiers
- Use same GlPostingService methods
- Maintain consistent error handling

### Database Integration
- Existing `sales` table maps to new `sales_invoices` table
- Add migration to link or migrate data
- Preserve existing sale records

### Testing Integration
```php
// Test both workflows
class SalesIntegrationTest
{
    public function test_existing_sale_workflow()
    {
        // Test SaleObserver
    }

    public function test_new_invoice_workflow()
    {
        // Test SalesInvoiceObserver
    }

    public function test_mixed_workflow()
    {
        // Test using both
    }
}
```

## Recommendations
- Run existing SaleObserver tests before integration
- Implement feature flag to enable new workflow
- Monitor GL posting for duplicates
- Document migration path for existing sales data