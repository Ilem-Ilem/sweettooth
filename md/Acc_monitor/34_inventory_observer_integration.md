# Stock/Inventory Observer Integration Details

This guide details how the new inventory observers integrate with the existing StockMovementObserver.

## Existing StockMovementObserver Analysis

### Current Implementation
```php
class StockMovementObserver
{
    public function created(StockMovement $movement): void
    {
        if (in_array($movement->type, ['damage', 'shrinkage'])) {
            $this->postToGL($movement);
        }
    }
}
```

### Integration Points
- **Model**: Existing `StockMovement` model
- **GL Posting**: Only for damage/shrinkage adjustments
- **Types**: Limited to specific adjustment types

## New Inventory Observers Integration

### InventoryWriteOffObserver Compatibility
- **Model**: New `InventoryWriteOff` model
- **Function**: Handles various write-off reasons
- **Integration**: Can create StockMovement records for GL posting

### InventoryTransferObserver
- **Model**: New `InventoryTransfer` model
- **Function**: Location-to-location transfers
- **Integration**: Updates quantities without GL impact (unless shrinkage)

### ProductionOrderObserver
- **Model**: New `ProductionOrder` model
- **Function**: Manufacturing workflow
- **Integration**: Deducts components, adds finished goods

### Proposed Integration Approach
```php
// StockMovementObserver remains for basic adjustments
// New observers handle complex operations

class InventoryWriteOffObserver
{
    public function created(InventoryWriteOff $writeOff): void
    {
        // Create StockMovement for GL posting
        StockMovement::create([
            'stock_id' => $writeOff->inventory_item_id,
            'type' => 'adjustment',
            'quantity' => -$writeOff->quantity,
            'cost' => $writeOff->cost,
            'reason' => $writeOff->reason,
        ]);
        
        // StockMovementObserver will handle GL posting
    }
}

class InventoryTransferObserver
{
    public function created(InventoryTransfer $transfer): void
    {
        // Update locations without creating StockMovement (no GL impact)
        $this->updateLocationQuantities($transfer);
        
        // Only create StockMovement if transfer causes shrinkage
    }
}
```

### Inventory Updates
- Existing system may have basic inventory tracking
- New observers enhance with locations, kits, production
- Ensure quantity updates don't conflict

### GL Posting Coordination
- StockMovementObserver handles GL for adjustments
- New observers create StockMovement records when needed
- Avoid duplicate posting

### Database Integration
- Existing `stock_movements` table
- New tables: `inventory_items`, `inventory_locations`, `inventory_transfers`, etc.
- May need to migrate existing inventory data

### Production Integration
- New `ProductionOrder` observer manages component deduction
- Complements existing inventory adjustments
- Adds manufacturing capabilities

### Testing Integration
```php
class InventoryIntegrationTest
{
    public function test_existing_stock_movement()
    {
        // Test StockMovementObserver
    }

    public function test_new_inventory_operations()
    {
        // Test transfers, write-offs, production
    }

    public function test_gl_posting_integration()
    {
        // Ensure GL posting works for both old and new
    }
}
```

## Recommendations
- Keep StockMovementObserver for existing adjustments
- New observers extend inventory capabilities
- Test quantity updates thoroughly
- Monitor for GL posting conflicts