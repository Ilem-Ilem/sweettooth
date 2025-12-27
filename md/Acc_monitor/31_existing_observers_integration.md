# Integration of New Observers with Existing Observers

This guide examines how the new Manager.io-inspired observers integrate with the existing Laravel observers in the system, ensuring compatibility and avoiding conflicts.

## Existing Observers Review

### SaleObserver
- **Triggers**: `created` and `updated` on Sale model
- **GL Posting**: Posts to GL when sale is completed and fully paid
- **Status**: Updates `gl_posting_status`
- **Integration Points**: 
  - Works with inventory deductions (via SalesInvoiceObserver in new system)
  - Compatible with existing sales workflow

### PurchaseObserver
- **Triggers**: `updated` on Purchase model
- **GL Posting**: Posts when purchase status changes to 'approved'
- **Status**: Updates `gl_posting_status`
- **Integration Points**:
  - Complements new PurchaseInvoiceObserver
  - Existing purchase model may need enhancement for full workflow

### StockMovementObserver
- **Triggers**: `created` on StockMovement model
- **GL Posting**: Posts damage/shrinkage adjustments
- **Types**: Handles 'damage' and 'shrinkage' movements
- **Integration Points**:
  - Works alongside new InventoryWriteOffObserver
  - Existing StockMovement model covers basic adjustments

## Integration Strategy

### 1. Model Compatibility
- Existing `Sale` model maps to new `SalesInvoice` with enhancements
- Existing `Purchase` model maps to new `PurchaseInvoice` workflow
- Existing `StockMovement` integrates with new inventory adjustments

### 2. GL Posting Coordination
- All observers use the same `GlPostingService`
- Avoid duplicate posting by checking `gl_posting_status`
- New observers extend existing posting logic

### 3. Status Management
- Maintain existing status fields
- Add new status fields for enhanced workflows (e.g., `matching_status` for purchases)

### 4. Error Handling
- Consistent error logging and status updates
- Non-blocking errors to prevent transaction failures

## Potential Conflicts and Resolutions

### Duplicate GL Posting
- **Issue**: Multiple observers might post the same transaction
- **Resolution**: Check posting status and use idempotent operations

### Model Field Conflicts
- **Issue**: New migrations might conflict with existing fields
- **Resolution**: Review existing schema before adding new fields

### Workflow Overlap
- **Issue**: Existing sale/purchase flows vs. new quote-order-invoice flows
- **Resolution**: Make new workflows optional, enhance existing ones

## Enhanced Integration Examples

### Sales Integration
```php
// Existing SaleObserver remains, new SalesInvoiceObserver adds
class SalesInvoiceObserver extends SaleObserver
{
    public function created(SalesInvoice $invoice): void
    {
        parent::created($invoice); // Call existing logic if applicable
        $this->updateCustomerBalance($invoice);
        // Additional new logic
    }
}
```

### Purchase Integration
```php
// Extend existing PurchaseObserver
class PurchaseInvoiceObserver
{
    public function updated(PurchaseInvoice $invoice): void
    {
        // Check if existing PurchaseObserver logic applies
        if ($this->shouldUseExistingLogic($invoice)) {
            // Integrate with existing posting
        }
        $this->performThreeWayMatch($invoice);
    }
}
```

### Inventory Integration
```php
// StockMovementObserver handles basic adjustments
// New observers handle complex inventory operations
class InventoryWriteOffObserver
{
    public function created(InventoryWriteOff $writeOff): void
    {
        // Create StockMovement for GL posting
        StockMovement::create([
            'type' => 'adjustment',
            // Map fields
        ]);
        // Additional write-off logic
    }
}
```

## Migration Path

1. **Audit Existing Observers**: Ensure all are documented and understood
2. **Test Integrations**: Run tests with both old and new observers active
3. **Gradual Rollout**: Enable new features incrementally
4. **Monitor GL Posting**: Verify no duplicate or missing entries

## Recommendations

- Keep existing observers for backward compatibility
- Use composition over inheritance for observer integration
- Document all observer interactions
- Implement feature flags for new observer activation

This ensures smooth integration while preserving existing functionality.