# Callback System - Quick Reference

## File Structure

```
app/
├── Models/
│   ├── ProductDispatchCallback.php      (Sales → Production returns)
│   ├── ProductionCallback.php           (Production → Inventory issues)
│   └── ProductCallback.php              (LEGACY - not used)
│
├── Livewire/BranchDashboard/
│   ├── SalesDashboard/Callbacks/
│   │   └── CreateDispatchCallback.php   (Sales creates return)
│   │
│   ├── Production/Callbacks/
│   │   ├── CreateInventoryCallback.php  (Production reports issue)
│   │   └── ApproveCallbacks.php         (Production approves sales returns)
│   │
│   └── Inventory/Callbacks/
│       └── ApproveCallbacks.php         (Inventory approves production issues)
│
├── Views/livewire/branch-dashboard/
│   ├── sales-dashboard/callbacks/
│   │   └── create-dispatch-callback.blade.php
│   ├── production/callbacks/
│   │   ├── create-inventory-callback.blade.php
│   │   └── approve-callbacks.blade.php
│   └── inventory/callbacks/
│       └── approve-callbacks.blade.php
│
database/migrations/
├── 2025_10_21_073703_create_product_callbacks_table.php         (LEGACY)
├── 2025_11_02_021715_create_product_dispatch_callbacks_table.php
├── 2025_11_02_104432_create_production_callbacks_table.php
└── 2025_11_06_175546_make_product_dispatch_id_nullable...php
```

---

## Database Tables

### product_dispatch_callbacks
**Rows**: Sales → Production returns

| Column | Type | Purpose |
|--------|------|---------|
| id | PK | Unique ID |
| product_dispatch_id | FK (nullable) | Original dispatch |
| sales_shift_id | FK | Sales shift returning |
| product_id | FK | Product being returned |
| recorded_by | UUID | Who in sales reported |
| quantity | DECIMAL | How much |
| uom | VARCHAR | Unit |
| reason | ENUM | Why (expired, damaged, etc.) |
| status | ENUM | Current state (pending → completed) |
| approved_by | UUID (nullable) | Production who approved |
| approved_at | TIMESTAMP | When approved |
| received_by | UUID (nullable) | Production who received |
| received_at | TIMESTAMP | When received |
| notes | TEXT | Additional info |
| callback_time | TIMESTAMP | When reported |
| created_at, updated_at | | Standard timestamps |

**Indexes**: (product_dispatch_id, status), (sales_shift_id, callback_time), (status, callback_time)

### production_callbacks
**Rows**: Production → Inventory damage/quality reports

| Column | Type | Purpose |
|--------|------|---------|
| id | PK | Unique ID |
| shift_id | FK | Production shift |
| source_type | ENUM | Type: raw_material_from_stock or finished_product_reject |
| item_id | FK (nullable) | Raw material ID |
| product_id | FK (nullable) | Finished product ID |
| recorded_by | UUID | Who in production reported |
| quantity | DECIMAL | Amount damaged |
| uom | VARCHAR | Unit |
| reason | ENUM | Why (damaged, expired, contamination, etc.) |
| status | ENUM | Current state (pending, approved_by_inventory, completed, rejected) |
| approved_by | UUID (nullable) | Inventory who approved |
| approved_at | TIMESTAMP | When approved |
| notes | TEXT | Additional info |
| callback_time | TIMESTAMP | When reported |
| created_at, updated_at | | Standard timestamps |

**Indexes**: (shift_id, callback_time), (status, callback_time), (source_type, item_id)

---

## Status Flows

### ProductDispatchCallback
```
pending
  ↓ approve()
approved_by_production
  ↓ markAsReceived()
received_by_production
  ↓ complete()
completed
```

### ProductionCallback
```
pending
  ├─ approve() → approved_by_inventory
  │               ↓ complete()
  │              completed
  │
  └─ reject() → rejected
```

---

## Key Methods

### ProductDispatchCallback

| Method | Parameters | Returns | Purpose |
|--------|-----------|---------|---------|
| canBeApproved() | - | bool | Check if can approve |
| canBeReceived() | - | bool | Check if can receive |
| approve() | $employeeId | bool | Set to approved_by_production |
| markAsReceived() | $employeeId | bool | Set to received_by_production |
| complete() | - | bool | Set to completed |
| formatted_reason | - | string | Human-readable reason |
| formatted_status | - | string | Human-readable status |
| Scope: pending() | - | Builder | Get pending callbacks |
| Scope: approved() | - | Builder | Get approved callbacks |
| Scope: received() | - | Builder | Get received callbacks |
| Scope: completed() | - | Builder | Get completed callbacks |
| Scope: bySalesShift($id) | $id | Builder | Filter by shift |
| Scope: byProduct($id) | $id | Builder | Filter by product |

### ProductionCallback

| Method | Parameters | Returns | Purpose |
|--------|-----------|---------|---------|
| isRawMaterial() | - | bool | Check type |
| isFinishedProduct() | - | bool | Check type |
| canBeApproved() | - | bool | Check if pending |
| approve() | $employeeId | bool | Set to approved_by_inventory |
| reject() | $employeeId, $reason | bool | Set to rejected |
| complete() | - | bool | Set to completed |
| formatted_source_type | - | string | Human-readable type |
| formatted_reason | - | string | Human-readable reason |
| item_name | - | string | Item or product name |
| Scope: pending() | - | Builder | Get pending |
| Scope: approved() | - | Builder | Get approved |
| Scope: completed() | - | Builder | Get completed |
| Scope: rejected() | - | Builder | Get rejected |
| Scope: rawMaterial() | - | Builder | Get raw material returns |
| Scope: finishedProduct() | - | Builder | Get product rejects |
| Scope: byShift($id) | $id | Builder | Filter by shift |

---

## Relationships

### ProductDispatchCallback Relations
```php
$callback->productDispatch    // ProductDispatch
$callback->salesShift         // SalesShift
$callback->product            // Product
$callback->recordedBy         // Employee
$callback->approvedBy         // Employee
$callback->receivedBy         // Employee
```

### ProductionCallback Relations
```php
$callback->shift              // Shift
$callback->item               // Item (raw material)
$callback->product            // Product (finished)
$callback->recipe             // Recipe
$callback->recordedBy         // Employee
$callback->approvedBy         // Employee
```

---

## Reason Options

### ProductDispatchCallback Reasons
- `expired` - Product expired
- `damaged` - Physical damage
- `quality_issue` - Quality problems
- `customer_return` - Returned by customer
- `over_received` - Received more than expected
- `wrong_item` - Wrong product received
- `other` - Other reasons

### ProductionCallback Reasons

**Raw Material**:
- `damaged` - Damage
- `expired` - Expired
- `quality_issue` - Quality
- `wrong_batch` - Wrong batch
- `contamination` - Contaminated
- `other` - Other

**Finished Product**:
- `damaged` - Damage
- `quality_issue` - Quality
- `contamination` - Contaminated
- `other` - Other

---

## Common Queries

### Get Pending Approvals
```php
// Production approving sales returns
ProductDispatchCallback::pending()->get();

// Inventory approving production issues
ProductionCallback::pending()->get();
```

### Get By Status
```php
ProductDispatchCallback::where('status', 'approved_by_production')->get();
ProductionCallback::approved()->get();  // approved_by_inventory
```

### Get By Type
```php
ProductionCallback::rawMaterial()->get();
ProductionCallback::finishedProduct()->get();
```

### Get For Shift
```php
ProductDispatchCallback::bySalesShift($shiftId)->get();
ProductionCallback::byShift($shiftId)->get();
```

### Get By Product
```php
ProductDispatchCallback::byProduct($productId)->get();
```

### Get Completed This Week
```php
ProductDispatchCallback::completed()
    ->where('callback_time', '>=', now()->subWeek())
    ->get();
```

### Get With Relationships (Eager Load)
```php
ProductDispatchCallback::with([
    'productDispatch.salesShift',
    'product',
    'recordedBy',
    'approvedBy',
    'receivedBy'
])->get();

ProductionCallback::with([
    'shift',
    'item',
    'product',
    'recordedBy',
    'approvedBy'
])->get();
```

---

## Workflow Examples

### Example 1: Sales Returns Damaged Product
```php
// 1. Sales creates callback
$callback = ProductDispatchCallback::create([
    'product_dispatch_id' => 123,
    'sales_shift_id' => 456,
    'product_id' => 'uuid-xxx',
    'recorded_by' => 'emp-id',
    'quantity' => 10,
    'uom' => 'KG',
    'reason' => 'damaged',
    'status' => 'pending',
    'callback_time' => now(),
]);

// 2. Production approves
$callback->approve('emp-production-id');
// Now status = 'approved_by_production'

// 3. Production marks received
$callback->markAsReceived('emp-production-id');
// Now status = 'received_by_production'

// 4. Production completes and updates stock
$callback->complete();
// Manually call stock update (see improvements)
// Now status = 'completed'
```

### Example 2: Production Reports Damaged Raw Material
```php
// 1. Production creates callback
$callback = ProductionCallback::create([
    'shift_id' => 789,
    'source_type' => 'raw_material_from_stock',
    'item_id' => 100,
    'recorded_by' => 'emp-prod-id',
    'quantity' => 5,
    'uom' => 'KG',
    'reason' => 'contamination',
    'status' => 'pending',
    'callback_time' => now(),
]);

// 2. Inventory approves (and stock updates)
$callback->approve('emp-inventory-id');
// Manually update stock (see improvements)
// Now status = 'approved_by_inventory'

// 3. Inventory completes
$callback->complete();
// Now status = 'completed'
```

### Example 3: Inventory Rejects Callback
```php
// 1. Callback created
$callback = ProductionCallback::create([
    'shift_id' => 789,
    'source_type' => 'finished_product_reject',
    'product_id' => 'uuid-yyy',
    'recorded_by' => 'emp-prod-id',
    'quantity' => 20,
    'reason' => 'quality_issue',
    'status' => 'pending',
]);

// 2. Inventory rejects
$callback->reject('emp-inventory-id', 'Quality standards were actually met, product is acceptable');
// Now status = 'rejected'
// Notes include rejection reason

// 3. Production sees rejection and investigates
```

---

## Important Notes

⚠️ **CRITICAL ISSUES** (See 04_ISSUES_AND_INCONSISTENCIES.md):
- Polymorphic type mismatch in migrations
- Stock logic in Livewire, not models
- No automatic stock updates
- Missing quantity validation

✅ **WORKING WELL**:
- Multi-stage approval workflow
- Employee tracking
- Status transitions
- Detailed reason tracking
- Views have good null handling

⚠️ **NEEDS DOCUMENTATION**:
- When product_dispatch_id is NULL (orphaned callbacks)
- How stock is calculated
- Branch filtering approach

---

## Testing Commands

```bash
# Check pending approvals
php artisan tinker
>>> ProductDispatchCallback::pending()->count()

# Check specific status
>>> ProductionCallback::where('status', 'rejected')->get()

# Eager load with relationships
>>> ProductDispatchCallback::with('productDispatch', 'recordedBy')->first()

# Get statistics
>>> ProductDispatchCallback::selectRaw('status, COUNT(*) as count')->groupBy('status')->get()
```

---

## Debugging

### If Callbacks Aren't Showing
```php
// Check branch filtering
ProductDispatchCallback::whereHas('productDispatch.salesShift', function ($q) {
    $q->where('branch_id', $branchId);
})->count();

// Check status
ProductDispatchCallback::where('status', 'pending')->count();

// Check with eager load
ProductDispatchCallback::with('productDispatch')->first()->productDispatch;
```

### If Stock Isn't Updating
```php
// Check if callback completed
$callback = ProductDispatchCallback::find($id);
dd($callback->status);  // Should be 'completed'

// Check ProductStock
ProductStock::where('sales_shift_id', $callback->sales_shift_id)
            ->where('product_id', $callback->product_id)
            ->first();

// Check DailyProduce
DailyProduce::where('shift_id', $productDispatch->shift_id)->first();
```

### If Relationship Missing
```php
// Verify polymorphic setup
$callback = ProductDispatchCallback::first();
dd($callback->recorded_by_id, $callback->recorded_by_type);

// Or simple FK
dd($callback->recorded_by);
```

---

## Related Documentation
- **01_SYSTEM_OVERVIEW.md** - High-level architecture
- **02_MODEL_ANALYSIS.md** - Detailed model documentation
- **03_WORKFLOW_DETAILS.md** - Step-by-step workflows
- **04_ISSUES_AND_INCONSISTENCIES.md** - Problems found
- **05_CRITICAL_IMPROVEMENTS.md** - How to fix issues
