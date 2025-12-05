# Callback Models - Detailed Analysis

## ProductDispatchCallback Model

**File**: `/app/Models/ProductDispatchCallback.php`  
**Table**: `product_dispatch_callbacks`  
**Purpose**: Track products returned from Sales to Production

### Properties & Relationships

#### Fillable Properties
```php
'product_dispatch_id'    // Link to original dispatch
'sales_shift_id'         // Sales shift sending back
'product_id'             // Product being returned
'recorded_by'            // Sales employee ID
'quantity'               // Quantity being returned
'uom'                    // Unit of measure
'reason'                 // Enum: Why it's being returned
'status'                 // Current status
'approved_by'            // Production employee ID
'approved_at'            // When approved
'received_by'            // Production employee ID
'received_at'            // When received
'notes'                  // Additional notes
'callback_time'          // When callback was created
```

#### Casts (Type Conversions)
```php
'quantity' => 'decimal:2'        // Money-safe precision
'callback_time' => 'datetime'    // ISO timestamp
'approved_at' => 'datetime'
'received_at' => 'datetime'
```

#### Relationships
```php
productDispatch()     // BelongsTo ProductDispatch
salesShift()          // BelongsTo SalesShift
product()             // BelongsTo Product
recordedBy()          // BelongsTo Employee (who created)
approvedBy()          // BelongsTo Employee (who approved)
receivedBy()          // BelongsTo Employee (who received)
```

### Scopes

| Scope | Usage | Returns |
|-------|-------|---------|
| `pending()` | Get pending approvals | WHERE status = 'pending' |
| `approved()` | Get approved callbacks | WHERE status = 'approved_by_production' |
| `received()` | Get received items | WHERE status = 'received_by_production' |
| `completed()` | Get completed callbacks | WHERE status = 'completed' |
| `bySalesShift()` | Filter by sales shift | WHERE sales_shift_id = ? |
| `byProduct()` | Filter by product | WHERE product_id = ? |

### Methods

#### State Checkers
```php
canBeApproved()      // bool - Returns true if status === 'pending'
canBeReceived()      // bool - Returns true if status === 'approved_by_production'
```

#### Actions
```php
approve($employeeId)          // Sets status to 'approved_by_production'
markAsReceived($employeeId)   // Sets status to 'received_by_production'
complete()                    // Sets status to 'completed'
```

#### Attributes
```php
formatted_reason      // Returns ucwords(str_replace('_', ' ', $reason))
formatted_status      // Returns ucwords(str_replace('_', ' ', $status))
```

---

## ProductionCallback Model

**File**: `/app/Models/ProductionCallback.php`  
**Table**: `production_callbacks`  
**Purpose**: Track materials/products returned from Production to Inventory

### Properties & Relationships

#### Fillable Properties
```php
'shift_id'           // Production shift reporting the issue
'source_type'        // Enum: 'raw_material_from_stock' or 'finished_product_reject'
'item_id'            // Raw material item ID (nullable)
'product_id'         // Finished product ID (nullable)
'recorded_by'        // Production employee ID
'quantity'           // Quantity being reported
'uom'                // Unit of measure
'reason'             // Enum: Why it's defective
'status'             // Current status
'approved_by'        // Inventory employee ID
'approved_at'        // When approved
'notes'              // Additional notes
'callback_time'      // When callback was created
```

#### Casts
```php
'quantity' => 'decimal:2'
'callback_time' => 'datetime'
'approved_at' => 'datetime'
```

#### Relationships
```php
shift()          // BelongsTo Shift (production shift)
item()           // BelongsTo Item (raw material)
product()        // BelongsTo Product (finished product)
recipe()         // BelongsTo Recipe (for product callbacks)
recordedBy()     // BelongsTo Employee (who reported)
createdBy()      // BelongsTo Employee (who reported - DUPLICATE)
approvedBy()     // BelongsTo Employee (who approved in inventory)
```

### Issues Found ⚠️

**Issue #1: Duplicate Relationship**
```php
// Both do the same thing:
public function recordedBy()  -> BelongsTo(Employee, 'recorded_by')
public function createdBy()   -> BelongsTo(Employee, 'recorded_by')
```
**Impact**: Confusing API, redundant code  
**Fix**: Remove `createdBy()`, use only `recordedBy()`

### Scopes

| Scope | Usage | Returns |
|-------|-------|---------|
| `pending()` | Get pending approvals | WHERE status = 'pending' |
| `approved()` | Get approved callbacks | WHERE status = 'approved_by_inventory' |
| `completed()` | Get completed callbacks | WHERE status = 'completed' |
| `rejected()` | Get rejected callbacks | WHERE status = 'rejected' |
| `byShift()` | Filter by production shift | WHERE shift_id = ? |
| `rawMaterial()` | Get raw material callbacks | WHERE source_type = 'raw_material_from_stock' |
| `finishedProduct()` | Get finished product callbacks | WHERE source_type = 'finished_product_reject' |

### Methods

#### Type Checkers
```php
isRawMaterial()       // bool - Checks source_type
isFinishedProduct()   // bool - Checks source_type
```

#### State Checkers
```php
canBeApproved()       // bool - Returns true if status === 'pending'
```

#### Actions
```php
approve($employeeId)              // Sets to 'approved_by_inventory', timestamps
reject($employeeId, $reason = null)  // Sets to 'rejected', appends reason to notes
complete()                        // Sets to 'completed'
```

#### Attributes
```php
formatted_source_type  // ucwords(str_replace('_', ' ', $source_type))
formatted_reason       // ucwords(str_replace('_', ' ', $reason))
formatted_status       // ucwords(str_replace('_', ' ', $status))
item_name              // Returns item->name or product->name dynamically
```

---

## Database Migrations Analysis

### Migration 1: `create_product_dispatch_callbacks_table`
**File**: `2025_11_02_021715_create_product_dispatch_callbacks_table.php`

```sql
- product_dispatch_id (UNSIGNED BIG INT, FK to product_dispatches)
- sales_shift_id (UNSIGNED BIG INT, FK to sales_shifts)
- product_id (UUID, FK to products)
- recorded_by (UUID, polymorphic type)
- recorded_by_type (string, polymorphic type)
- quantity (DECIMAL 12,2)
- uom (VARCHAR 50)
- reason (ENUM - 7 options)
- status (ENUM - 4 options, default 'pending')
- approved_by_id (UUID, nullable)
- approved_by_type (string, nullable)
- approved_at (TIMESTAMP, nullable)
- received_by_id (UUID, nullable)
- received_by_type (string, nullable)
- received_at (TIMESTAMP, nullable)
- notes (TEXT, nullable)
- callback_time (TIMESTAMP, nullable)
- timestamps (created_at, updated_at)
```

**Issues Found**:
- Uses polymorphic types (`recorded_by_id`, `recorded_by_type`) but model uses simple foreign key
- Mismatch between model property name `recorded_by` (int) and migration `recorded_by_id` (uuid)

### Migration 2: `create_production_callbacks_table`
**File**: `2025_11_02_104432_create_production_callbacks_table.php`

```sql
- shift_id (UNSIGNED BIG INT, FK to shifts)
- source_type (ENUM - 2 options)
- item_id (UNSIGNED BIG INT, FK to items, nullable)
- product_id (UUID, FK to products, nullable)
- recorded_by (UUID, polymorphic type)
- recorded_by_type (string, polymorphic type)
- quantity (DECIMAL 12,2)
- uom (VARCHAR 50)
- reason (ENUM - 6 options)
- status (ENUM - 4 options, default 'pending')
- approved_by_id (UUID, nullable)
- approved_by_type (string, nullable)
- approved_at (TIMESTAMP, nullable)
- notes (TEXT, nullable)
- callback_time (TIMESTAMP, nullable)
- timestamps (created_at, updated_at)
```

**Same Issue**: Polymorphic types in schema don't match model usage

### Migration 3: `make_product_dispatch_id_nullable`
**File**: `2025_11_06_175546_make_product_dispatch_id_nullable_in_product_dispatch_callbacks_table.php`

**Purpose**: Allow callbacks without dispatch reference + add 'over_stock' reason

**Changes**:
```php
// product_dispatch_id now nullable
$table->unsignedBigInteger('product_dispatch_id')->nullable()->change();

// reason enum expanded
ALTER TABLE product_dispatch_callbacks MODIFY reason ENUM(
    'expired', 'damaged', 'quality_issue', 'customer_return', 
    'over_received', 'over_stock', 'wrong_item', 'other'
);
```

**Impact**: Allows callbacks not tied to a specific dispatch (orphaned callbacks)

### Migration 4: `create_product_callbacks_table` (Legacy)
**File**: `2025_10_21_073703_create_product_callbacks_table.php`

**Status**: ⚠️ LEGACY - Not used in current system

```sql
- product_stock_id
- product_id
- sales_shift_id
- recorded_by (UUID + type)
- quantity, reason, notes
- callback_time
- timestamps
```

**Should be**: Reviewed for deprecation or replaced with ProductDispatchCallback

---

## Inconsistencies & Issues Summary

| Issue | Severity | Location | Solution |
|-------|----------|----------|----------|
| Polymorphic type mismatch | MEDIUM | Migrations vs Models | Use simple foreign keys or update models |
| Duplicate `createdBy()` method | LOW | ProductionCallback | Remove redundant method |
| Legacy ProductCallback table | MEDIUM | Unused migration | Deprecate or document clearly |
| Orphaned callbacks possible | MEDIUM | product_dispatch_callbacks | Document implications |
| No stock update trigger | MEDIUM | ProductDispatchCallback | Manual handling required |
| Missing reason in ProductionCallback | LOW | Model | 'over_stock' only in ProductDispatchCallback |

---

## Critical Method Issues

### ProductDispatchCallback::completeCallback()
**Location**: `ApproveCallbacks.php` line 267  
**Issue**: Manually handles stock impact - should be in model method

```php
// Current: Done in Livewire component
protected function handleStockImpact(ProductDispatchCallback $callback)

// Should be: Model method
public function complete()  // with automatic stock handling
```

### ProductionCallback::approve()
**Issue**: No automatic stock updates
**Current**: Inventory must manually call `handleRawMaterialCallback()`
**Should**: Auto-update in approve() method

---

## Recommendations

### High Priority
1. **Fix polymorphic types**: Align migrations with actual model usage
2. **Add stock update methods**: Put in models, not Livewire
3. **Remove duplicate methods**: Clean up `createdBy()`
4. **Document orphaned callbacks**: Explain when product_dispatch_id is null

### Medium Priority
1. **Consolidate legacy table**: Deprecate ProductCallback
2. **Add validation**: Ensure quantity doesn't exceed available
3. **Add event listeners**: Dispatch events on status changes
4. **Add scopes for branch filtering**: Multi-branch support

### Nice to Have
1. **Add exportable trait**: Excel/CSV export capability
2. **Add soft deletes**: Archive instead of hard delete
3. **Add version tracking**: Change history
