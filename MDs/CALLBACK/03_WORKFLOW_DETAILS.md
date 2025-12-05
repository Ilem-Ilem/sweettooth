# Callback Workflows - Detailed Process

## Workflow 1: ProductDispatchCallback (Sales → Production)

### Scenario: Sales Department Returns Damaged Products

```
SALES TEAM                          PRODUCTION TEAM
    │                                    │
    │  1. Receives dispatch             │
    │  2. Detects damage/issue          │
    │  3. Creates callback ──────────→  │
    │     (Status: PENDING)              │
    │                                    │
    │                        4. Reviews callback
    │                        5. Approves return
    │                     (Status: APPROVED_BY_PRODUCTION)
    │                                    │
    │  6. Receives approval notice       │
    │  7. Updates sales stock            │
    │                        8. Marks as received
    │                     (Status: RECEIVED_BY_PRODUCTION)
    │                                    │
    │  9. Stock finalized               │
    │                        10. Completes callback
    │                     (Status: COMPLETED)
```

### Implementation: CreateDispatchCallback.php

**File**: `/app/Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php`

#### Component Properties
```php
selectedSalesShiftId      // Currently selected shift
currentSalesShiftId       // Active shift for employee
availableShifts[]         // Last 30 days of shifts
callbackQuantity          // How much to return
callbackReason            // Why returning (enum)
callbackNotes             // Free text notes
selectedDispatch          // Which dispatch to return from
showCallbackModal          // Modal visibility
```

#### Key Methods

**Mount Process**
```php
mount() {
    $this->loadAvailableShifts()    // Get last 30 days
    $this->loadCurrentSalesShift()  // Find active shift
    // Priority: active shift > first in list
}
```

**Data Retrieval**
```php
getRowsProperty() {
    // Get dispatches from selected shift
    // Filter by status = 'received' (only received items can be returned)
    // Allow search by product name/SKU
    // Paginate results
}

getAvailableQuantity($dispatch) {
    // Calculate: received_qty - sum(existing callbacks)
    // Prevents over-returning
}
```

**Callback Creation**
```php
submitCallback() {
    // Validate quantity > 0
    // Validate reason in allowed options
    // Check quantity doesn't exceed available
    
    ProductDispatchCallback::create([
        'product_dispatch_id' => $dispatch->id,
        'sales_shift_id'      => $shift->id,
        'product_id'          => $dispatch->product_id,
        'recorded_by'         => $employee->id,
        'quantity'            => $this->callbackQuantity,
        'uom'                 => $dispatch->uom,
        'reason'              => $this->callbackReason,
        'status'              => 'pending',              // Initial status
        'notes'               => $this->callbackNotes,
        'callback_time'       => now(),
    ]);
}
```

#### Reason Options
```php
'expired'           // Product expired
'damaged'           // Physical damage
'quality_issue'     // Quality problems
'customer_return'   // Customer returned it
'over_received'     // Received more than ordered
'wrong_item'        // Wrong product received
'other'             // Other reasons
```

#### Availability Calculation Example
```
Dispatch received: 100 units
Callback #1 (pending): 20 units
Callback #2 (approved): 15 units
Callback #3 (completed): 10 units
────────────────────────
Available to return: 100 - (20+15+10) = 55 units
```

---

## Workflow 2: ProductionCallback (Production → Inventory)

### Scenario A: Raw Material Damage Report

```
PRODUCTION TEAM                     INVENTORY TEAM
    │                                    │
    │  1. Discovers damaged materials   │
    │  2. Creates raw material callback │
    │     (Status: PENDING)              │
    │                                    │
    │                        3. Reviews details
    │                        4. Approves return
    │                     (Status: APPROVED_BY_INVENTORY)
    │                        5. Updates Stock:
    │                           - Decreases quantity_available
    │                           - Increases quantity_damaged
    │                           - Creates StockMovement log
    │                                    │
    │  6. Notified of approval          │
    │  7. Production continues...       │
    │                        8. Marks as completed
    │                     (Status: COMPLETED)
```

### Scenario B: Finished Product Reject Report

```
PRODUCTION TEAM                     INVENTORY TEAM
    │                                    │
    │  1. Finds quality issues          │
    │     in produced batch             │
    │  2. Creates finished product      │
    │     callback (Status: PENDING)    │
    │                                    │
    │                        3. Approves rejection
    │                     (Status: APPROVED_BY_INVENTORY)
    │                        4. Updates DailyProduce:
    │                           - Increases callback_qty
    │                           - Updates closing_qty
    │                           - Recalculates variance
    │                                    │
    │  5. Stock adjusted automatically  │
    │                        6. Completes callback
    │                     (Status: COMPLETED)
```

### Alternative: Rejection Path

```
PRODUCTION REQUESTS → INVENTORY TEAM
                           │
                    Reviews callback
                    ↓
                Finds it acceptable
                    ↓
           Rejects with reason
           │
    (Status: REJECTED)
           │
    Production receives notification
    Production can investigate reason
```

### Implementation: CreateInventoryCallback.php

**File**: `/app/Livewire/BranchDashboard/Production/Callbacks/CreateInventoryCallback.php`

#### Component Properties
```php
selectedShiftId         // Production shift being reported
callbackType            // 'raw_material' or 'finished_product'
selectedItemId          // For raw materials (nullable)
selectedProductId       // For finished products (nullable)
callbackQuantity        // How much damaged
callbackReason          // Why (enum based on type)
callbackNotes           // Free text description
showCallbackModal        // Modal visibility
```

#### Callback Type Options

**Raw Material**
```php
'damaged'           // Physically damaged
'expired'           // Expiration date passed
'quality_issue'     // Quality standards not met
'wrong_batch'       // Incorrect batch received
'contamination'     // Contaminated/unfit for use
'other'             // Other issues
```

**Finished Product**
```php
'damaged'           // Physical damage
'quality_issue'     // Doesn't meet specs
'contamination'     // Contaminated batch
'other'             // Other issues
```

#### Data Retrieval

**Raw Materials**
```php
getRawMaterialsProperty() {
    // Get ItemRequests for this shift
    // Get ItemDispatches with received_time not null
    // Show dispatched items available for return
    // Pagination support
}
```

**Finished Products**
```php
getFinishedProductsProperty() {
    // Get DailyProduce records for shift
    // Only where produced_quantity > 0
    // Pagination support
}
```

#### Callback Creation
```php
submitCallback() {
    // Type-specific validation
    
    if (raw_material) {
        // No strict quantity limit (may have been partially used)
    }
    
    if (finished_product) {
        // Check: quantity <= daily_produce.produced_quantity
    }
    
    ProductionCallback::create([
        'shift_id'      => $shift->id,
        'source_type'   => $sourceType,          // raw_material_from_stock | finished_product_reject
        'item_id'       => $item_id,             // if raw material
        'product_id'    => $product_id,          // if finished product
        'recorded_by'   => $employee->id,
        'quantity'      => $callbackQuantity,
        'uom'           => $uom,
        'reason'        => $callbackReason,
        'status'        => 'pending',
        'notes'         => $callbackNotes,
        'callback_time' => now(),
    ]);
}
```

### Implementation: ApproveCallbacks.php (Inventory)

**File**: `/app/Livewire/BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php`

#### Approval Methods

**Approve Callback**
```php
approveCallback($id) {
    $callback = ProductionCallback::find($id);
    
    if ($callback->isRawMaterial()) {
        handleRawMaterialCallback($callback);
    } elseif ($callback->isFinishedProduct()) {
        handleFinishedProductCallback($callback);
    }
    
    $callback->approve($employeeId);  // Sets status & timestamps
}
```

#### Stock Impact: Raw Material
```php
handleRawMaterialCallback($callback) {
    $stock = Stock::where('item_id', $callback->item_id)
                  ->where('branch_id', $branch_id)
                  ->first();
    
    $stock->update([
        'quantity_available' => max(0, $stock->quantity_available - $callback->quantity),
        'quantity_damaged'   => $stock->quantity_damaged + $callback->quantity,
    ]);
    
    StockMovement::create([
        'stock_id'      => $stock->id,
        'type'          => 'callback',
        'quantity'      => -$callback->quantity,
        'reference_type'=> 'production_callback',
        'reference_id'  => $callback->id,
        'notes'         => 'Production callback: ' . $callback->reason,
        'movement_date' => now(),
    ]);
}
```

#### Stock Impact: Finished Product
```php
handleFinishedProductCallback($callback) {
    $recipe = Recipe::where('product_id', $callback->product_id)->first();
    
    $dailyProduce = DailyProduce::where('shift_id', $callback->shift_id)
                               ->where('recipe_id', $recipe->id)
                               ->first();
    
    if ($dailyProduce) {
        $dailyProduce->callback_quantity += $callback->quantity;
        $dailyProduce->updateCalculations();  // Recalculate variance
    }
}
```

#### Rejection Path
```php
rejectCallback($id, $reason) {
    $callback = ProductionCallback::find($id);
    
    $callback->reject($employeeId, $reason);  // Sets status='rejected'
    
    // Appends rejection reason to notes
    // Production can see why it was rejected
}
```

---

## Workflow 3: Production Approves Sales Returns

### Process

```
SALES RETURNS CALLBACK                PRODUCTION APPROVES
    (Status: PENDING)                       │
         │                                  │
         └──→ Production receives notice   │
              Reviews product condition    │
                 │                          │
                 ├─→ Approves              │ → Status: APPROVED_BY_PRODUCTION
                 │     ├─ Receives         → Status: RECEIVED_BY_PRODUCTION
                 │     └─ Completes       → Status: COMPLETED
                 │
                 └─→ Rejects (future)     → Status: REJECTED
```

### Implementation: ApproveCallbacks.php (Production)

**File**: `/app/Livewire/BranchDashboard/Production/Callbacks/ApproveCallbacks.php`

#### Approval Process
```php
approveCallback($id) {
    $callback = ProductDispatchCallback::find($id);
    
    // Status: pending → approved_by_production
    $callback->approve($employeeId);  // Sets approved_by, approved_at
}
```

#### Receipt Process
```php
receiveCallback($id) {
    $callback = ProductDispatchCallback::find($id);
    
    // Status: approved_by_production → received_by_production
    $callback->markAsReceived($employeeId);  // Sets received_by, received_at
}
```

#### Completion Process
```php
completeCallback($id) {
    $callback = ProductDispatchCallback::find($id);
    
    // Status: received_by_production → completed
    handleStockImpact($callback);  // Update ProductStock and DailyProduce
    $callback->complete();
}
```

#### Stock Impact for Sales Return Completion
```php
handleStockImpact($callback) {
    // 1. Update ProductStock (Sales inventory)
    $productStock = ProductStock::where('sales_shift_id', $callback->sales_shift_id)
                               ->where('product_id', $callback->product_id)
                               ->first();
    
    // Increase callback_quantity (for returned items)
    $productStock->callback_quantity += $callback->quantity;
    $productStock->save();  // Triggers calculation of total_available
    
    // 2. Update DailyProduce (Production inventory)
    // Find original production shift that created this product
    $productDispatch = $callback->productDispatch;
    $recipe = Recipe::where('product_id', $callback->product_id)->first();
    
    $dailyProduce = DailyProduce::where('shift_id', $productDispatch->shift_id)
                               ->where('recipe_id', $recipe->id)
                               ->first();
    
    if ($dailyProduce) {
        $dailyProduce->callback_quantity += $callback->quantity;
        $dailyProduce->closing_quantity += $callback->quantity;
        $dailyProduce->updateCalculations();
    }
}
```

---

## Status Progression Summary

### ProductDispatchCallback
```
pending
   ↓ (Production approves)
approved_by_production
   ↓ (Production receives)
received_by_production
   ↓ (Production completes - updates stock)
completed
```

**Cannot Skip Stages**: Must follow sequence

### ProductionCallback
```
pending
   ├─ (Inventory approves) → approved_by_inventory
   │                           ↓ (Inventory completes)
   │                        completed
   │
   └─ (Inventory rejects) → rejected
                             (No further action)
```

**Two Outcomes**: Approve & Complete OR Reject

---

## Critical Data Mappings

### When Sales Returns Product to Production

```
ProductDispatchCallback
├─ product_dispatch_id → Points to original dispatch
├─ sales_shift_id → Which sales shift is returning
├─ product_id → What product
├─ recorded_by → Who in sales reported it
├─ reason → Why returning (expired, damaged, etc.)
└─ status → Current stage in approval

↓ (When Production approves)

ProductStock (Sales)
├─ callback_quantity += returned_quantity
└─ total_available = auto-calculated

DailyProduce (Production)
├─ callback_quantity += returned_quantity
├─ closing_quantity += returned_quantity
└─ variance = recalculated
```

### When Production Reports Damage to Inventory

```
ProductionCallback (Raw Material)
├─ shift_id → Production shift
├─ source_type = 'raw_material_from_stock'
├─ item_id → Which raw material
├─ quantity → How much damaged
├─ reason → Why (damaged, expired, etc.)
└─ status → Current approval stage

↓ (When Inventory approves)

Stock (Inventory)
├─ quantity_available -= damaged_quantity
├─ quantity_damaged += damaged_quantity
└─ StockMovement created for audit

────────────────────────

ProductionCallback (Finished Product)
├─ shift_id → Production shift
├─ source_type = 'finished_product_reject'
├─ product_id → Which product
├─ quantity → How much rejected
├─ reason → Why (damaged, quality, etc.)
└─ status → Approval stage

↓ (When Inventory approves)

DailyProduce
├─ callback_quantity += rejected_quantity
├─ expected_closing = recalculated
└─ variance = recalculated
```
