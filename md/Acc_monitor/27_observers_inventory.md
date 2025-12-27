# Observers for Inventory Integration

This guide explains how observers will automate inventory management processes, ensuring real-time stock tracking and cost calculations.

## Inventory Observers

### 1. InventoryTransferObserver

Handles stock movements between locations.

```php
class InventoryTransferObserver
{
    public function created(InventoryTransfer $transfer): void
    {
        $this->updateLocationQuantities($transfer);
        $this->createInventoryAdjustment($transfer);
    }

    private function updateLocationQuantities(InventoryTransfer $transfer): void
    {
        // Decrease from location
        if ($transfer->from_location_id) {
            $pivot = $transfer->item->locations()
                ->where('inventory_location_id', $transfer->from_location_id)
                ->first();
            if ($pivot) {
                $pivot->decrement('quantity', $transfer->quantity);
            }
        }

        // Increase to location
        if ($transfer->to_location_id) {
            $transfer->item->locations()
                ->where('inventory_location_id', $transfer->to_location_id)
                ->increment('quantity', $transfer->quantity);
        }
    }

    private function createInventoryAdjustment(InventoryTransfer $transfer): void
    {
        InventoryAdjustment::create([
            'inventory_item_id' => $transfer->inventory_item_id,
            'type' => 'transfer',
            'quantity_change' => 0, // Net zero for transfers
            'adjustment_date' => $transfer->transfer_date,
            'reason' => 'Transfer between locations',
        ]);
    }
}
```

### 2. InventoryWriteOffObserver

Manages stock reductions and GL posting.

```php
class InventoryWriteOffObserver
{
    protected GlPostingService $glPostingService;

    public function created(InventoryWriteOff $writeOff): void
    {
        $this->updateInventory($writeOff);
        $this->postToGL($writeOff);
    }

    private function updateInventory(InventoryWriteOff $writeOff): void
    {
        $writeOff->item->decrement('quantity_on_hand', $writeOff->quantity);
        
        InventoryAdjustment::create([
            'inventory_item_id' => $writeOff->inventory_item_id,
            'type' => 'write_off',
            'quantity_change' => -$writeOff->quantity,
            'cost_impact' => $writeOff->cost,
            'adjustment_date' => $writeOff->write_off_date,
            'reason' => $writeOff->reason,
        ]);
    }

    private function postToGL(InventoryWriteOff $writeOff): void
    {
        try {
            $this->glPostingService->postInventoryWriteOff($writeOff);
        } catch (Exception $e) {
            \Log::error('Failed to post write-off to GL', [
                'write_off_id' => $writeOff->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

### 3. ProductionOrderObserver

Handles manufacturing processes.

```php
class ProductionOrderObserver
{
    public function updated(ProductionOrder $order): void
    {
        if ($order->wasChanged('status')) {
            match($order->status) {
                'in_progress' => $this->deductComponents($order),
                'completed' => $this->addFinishedGoods($order),
            };
        }
    }

    private function deductComponents(ProductionOrder $order): void
    {
        foreach ($order->components as $component) {
            $component->inventoryItem->decrement('quantity_on_hand', $component->quantity_used);
        }
    }

    private function addFinishedGoods(ProductionOrder $order): void
    {
        $order->outputItem->increment('quantity_on_hand', $order->output_quantity);
        
        // Update cost
        $totalCost = $order->calculateTotalCost();
        $order->update(['total_cost' => $totalCost]);
        
        // Update average cost of finished good
        $this->updateFinishedGoodCost($order);
    }

    private function updateFinishedGoodCost(ProductionOrder $order): void
    {
        // Adjust cost based on production
    }
}
```

### 4. InventoryItemObserver

Maintains stock levels and alerts.

```php
class InventoryItemObserver
{
    public function updated(InventoryItem $item): void
    {
        if ($item->wasChanged('quantity_on_hand')) {
            $this->checkStockLevels($item);
            $this->updateValuation($item);
        }
    }

    private function checkStockLevels(InventoryItem $item): void
    {
        if ($item->quantity_on_hand <= $item->min_stock_level) {
            // Send low stock alert
            \Log::warning('Low stock alert', ['item_id' => $item->id]);
        }
    }

    private function updateValuation(InventoryItem $item): void
    {
        $valuation = $item->quantity_on_hand * $item->current_average_cost;
        $item->update(['current_valuation' => $valuation]);
    }
}
```

## Registration

```php
public function boot()
{
    InventoryTransfer::observe(InventoryTransferObserver::class);
    InventoryWriteOff::observe(InventoryWriteOffObserver::class);
    ProductionOrder::observe(ProductionOrderObserver::class);
    InventoryItem::observe(InventoryItemObserver::class);
}
```

## Benefits

- Real-time stock accuracy
- Automatic cost calculations
- Proactive stock level monitoring
- Seamless production integration

This ensures comprehensive inventory control with minimal manual intervention.