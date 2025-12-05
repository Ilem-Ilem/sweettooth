# Callback System - Issues & Inconsistencies

## Critical Issues

### 1. ⚠️ POLYMORPHIC TYPE MISMATCH

**Location**: Database migrations vs Model relationships

**The Problem**:
```php
// Migration creates polymorphic columns:
$table->uuid('recorded_by_id');
$table->string('recorded_by_type');

// But Model treats it as simple FK:
public function recordedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by');
}
```

**What This Means**:
- Model expects `recorded_by` column (INT/UUID)
- Migration creates `recorded_by_id` (UUID) + `recorded_by_type` (string)
- This is a **POLYMORPHIC RELATIONSHIP** that the model doesn't implement

**Impact**:
- Data may not insert correctly
- Queries on `recorded_by` fail silently
- Need to verify actual table structure

**Fix Required**:
```php
// Option A: Update Model to use morphTo
public function recordedBy()
{
    return $this->morphTo('recorded_by', 'recorded_by_type', 'recorded_by_id');
}

// Option B: Update Migration to be simple FK
$table->uuid('recorded_by');
$table->foreign('recorded_by')->references('id')->on('employees');
// Remove the recorded_by_type column
```

**Status**: 🔴 MUST INVESTIGATE - affects data integrity

---

### 2. ⚠️ DUPLICATE METHOD IN ProductionCallback

**Location**: `/app/Models/ProductionCallback.php` lines 66-78

```php
// Line 66-69: recordedBy method
public function recordedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by');
}

// Line 74-77: createdBy method (IDENTICAL)
public function createdBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by');
}
```

**Problem**: Two methods doing the same thing
**Impact**: Confuses developers, increases code maintenance
**Fix**: Remove `createdBy()`, use only `recordedBy()`

**Status**: 🟡 EASY FIX

---

### 3. ⚠️ ORPHANED CALLBACKS POSSIBLE

**Location**: `/database/migrations/2025_11_06_175546_make_product_dispatch_id_nullable_in_product_dispatch_callbacks_table.php`

```php
// product_dispatch_id is now NULLABLE
$table->unsignedBigInteger('product_dispatch_id')->nullable()->change();
```

**Problem**: Callbacks can exist without referencing a dispatch
```sql
INSERT INTO product_dispatch_callbacks 
(product_dispatch_id, sales_shift_id, product_id, recorded_by, ...) 
VALUES (NULL, 123, 'uuid-xxx', 'emp-123', ...);
```

**Impact**:
- Can't trace back to original dispatch
- Relationship `productDispatch()` returns NULL
- Data becomes orphaned
- Unclear why callback exists

**When This Happens**:
- When a return doesn't match a specific dispatch
- Over-stock corrections
- Ad-hoc inventory adjustments

**Fix**:
- Document when/why product_dispatch_id is NULL
- Create helper method to handle NULL case
- Add validation when querying

**Status**: 🟡 NEEDS DOCUMENTATION

**Suggested Fix**:
```php
// In ProductDispatchCallback model
public function getDispatchLinkAttribute()
{
    if ($this->product_dispatch_id) {
        return $this->productDispatch;
    }
    return null; // Orphaned callback - check notes field
}

// In views, use:
@if($callback->dispatchLink)
    ...dispatch info...
@else
    <span class="text-yellow-600">Orphaned - See notes</span>
@endif
```

---

### 4. ⚠️ STOCK UPDATE LOGIC IN LIVEWIRE COMPONENTS

**Location**: Multiple Livewire components handle stock updates

**Problem**: Business logic (stock updates) is in UI components, not models

```php
// Current (Bad):
// ApproveCallbacks.php (Livewire)
protected function handleStockImpact(ProductDispatchCallback $callback)
{
    // 50+ lines of stock update logic
}

// Should be (Good):
// ProductDispatchCallback model
public function complete()
{
    $this->update(['status' => 'completed']);
    $this->handleStockImpact();  // Model handles it
}
```

**Impact**:
- Stock updates don't trigger in tests
- API changes can break stock logic
- Hard to reuse logic
- Testing is difficult

**Affected Locations**:
1. `Production/Callbacks/ApproveCallbacks.php` line 267-312
2. `Inventory/Callbacks/ApproveCallbacks.php` line 340-405

**Fix Required**:
```php
// Move to Model
class ProductDispatchCallback extends Model
{
    public function complete()
    {
        if ($this->status !== 'received_by_production') {
            throw new Exception('Cannot complete non-received callback');
        }
        
        DB::transaction(function () {
            $this->updateProductStock();
            $this->updateDailyProduce();
            $this->update(['status' => 'completed']);
        });
    }
    
    private function updateProductStock()
    {
        // ... stock logic
    }
    
    private function updateDailyProduce()
    {
        // ... produce logic
    }
}
```

**Status**: 🔴 HIGH PRIORITY

---

### 5. ⚠️ NO AUTOMATIC STOCK UPDATES IN ProductionCallback

**Location**: `Inventory/Callbacks/ApproveCallbacks.php`

**Problem**: Stock updates happen in Livewire component, not in model

```php
// Current:
public function approveCallback($id)
{
    $callback = ProductionCallback::find($id);
    
    if ($callback->isRawMaterial()) {
        $this->handleRawMaterialCallback($callback);  // Manual update
    }
    
    $callback->approve($employeeId);  // Only updates status
}

// Should be:
public function approveCallback($id)
{
    $callback = ProductionCallback::find($id);
    $callback->approveWithStockUpdate($employeeId);  // All-in-one
}
```

**Impact**:
- Stock doesn't update if called from API/queue
- Can't use `$callback->approve()` alone
- Stock logic is hidden in component
- Inconsistent behavior

**Status**: 🔴 HIGH PRIORITY

---

### 6. ⚠️ MISSING VALIDATION - QUANTITY LIMITS

**Location**: Multiple Livewire components

**Issue**: Some components validate quantity, others don't

```php
// ApproveCallbacks (Sales) - NO QUANTITY VALIDATION
public function submitCallback()
{
    // Only checks: quantity > 0 and reason exists
    // Does NOT check against dispatch received_quantity
}

// CreateInventoryCallback (Production) - HAS VALIDATION
public function submitCallback()
{
    if ($this->callbackQuantity > $dailyProduce->produced_quantity) {
        throw error;  // Prevents over-return
    }
}
```

**Impact**:
- Sales can return more than received
- Creates negative stock situations
- Incomplete audit trail

**Fix**: Add quantity validation everywhere

```php
public function getAvailableQuantity($callback) {
    if ($callback instanceof ProductDispatchCallback) {
        return $callback->productDispatch->received_quantity 
               - $callback->where('dispatch_id', $callback->dispatch_id)
                         ->sum('quantity');
    }
}
```

**Status**: 🟡 MEDIUM PRIORITY

---

### 7. ⚠️ MISSING INDEX FOR BRANCH FILTERING

**Location**: ApproveCallbacks components (both)

**Problem**: No index on branch filtering paths

```php
// Current query:
ProductDispatchCallback::whereHas('productDispatch.salesShift', function ($q) {
    $q->where('branch_id', $branchId);
})->get();
```

**Issue**: 
- Uses nested relationship to reach branch_id
- No index for this path
- N+1 queries on large datasets
- Slow with many callbacks

**Solution**:
```sql
-- Add index
ALTER TABLE product_dispatch_callbacks 
ADD INDEX idx_branch_callback (sales_shift_id, callback_time);

-- Or add branch_id directly to table
ALTER TABLE product_dispatch_callbacks 
ADD COLUMN branch_id UNSIGNED BIG INT;

-- Then index it
ALTER TABLE product_dispatch_callbacks 
ADD INDEX idx_branch (branch_id, callback_time);
```

**Status**: 🟡 PERFORMANCE ISSUE

---

### 8. ⚠️ MISSING ERROR HANDLING - NULL RELATIONSHIPS

**Location**: Views and components

**Problem**: Code assumes relationships exist

```blade
<!-- In view - crashes if productDispatch is null -->
{{ $row->productDispatch->salesShift->shift_type }}

<!-- Should be -->
{{ optional($row->productDispatch)->salesShift->shift_type ?? 'N/A' }}
```

**Current State**: Views use proper optional() chaining ✅
**But**: Components don't validate before passing to views

**Status**: 🟡 MINOR - Views are OK, but add validation in components

---

## Inconsistencies

### A. Status Names Vary Between Models

**ProductDispatchCallback**:
```php
'pending'
'approved_by_production'
'received_by_production'
'completed'
```

**ProductionCallback**:
```php
'pending'
'approved_by_inventory'
'completed'
'rejected'
```

**Inconsistency**: Different approval role names (`approved_by_production` vs `approved_by_inventory`)

**Impact**: Hard to create unified queries/reports

**Fix**: Standardize status names across all callbacks

```php
// Proposed:
'pending'
'approved'
'completed'
'rejected'
```

### B. Reason Enums Don't Align

**ProductDispatchCallback**:
- expired, damaged, quality_issue, customer_return, over_received, wrong_item, other

**ProductionCallback (Raw Material)**:
- damaged, expired, quality_issue, wrong_batch, contamination, other

**ProductionCallback (Finished Product)**:
- damaged, quality_issue, contamination, other

**Inconsistency**: Overlapping but different reasons
**Impact**: Hard to create unified reporting

---

### C. Employee ID Types Inconsistent

**Migration**:
```php
$table->uuid('recorded_by_id');      // UUID
$table->string('recorded_by_type');  // String (polymorphic)
```

**Model**:
```php
'recorded_by' // Expects INT or UUID?
```

**Inconsistency**: Type mismatch
**Impact**: Data insertion/retrieval issues

**Status**: Same as Issue #1

---

## Error Scenarios

### Scenario 1: Complete Without Receipt

```php
// This SHOULD fail but might not:
$callback = ProductDispatchCallback::find(1);
$callback->status = 'pending';  // Skip approval/receipt
$callback->complete();  // Oops - just marks as completed

// Current protection:
// ✅ Good - completeCallback() in Livewire checks status
// ❌ Bad - Model complete() doesn't validate
```

**Fix**: Add validation in model method
```php
public function complete()
{
    if ($this->status !== 'received_by_production') {
        throw new InvalidStateException('Must be received first');
    }
    
    $this->update(['status' => 'completed']);
}
```

### Scenario 2: Approve Then Reject

```php
// Production approves, then wants to change mind
$callback->status = 'approved_by_production';
$callback->reject();  // Should this work?

// Current: reject() only works if status === 'pending'
// ✅ Good - prevents invalid state
// Issue: No way to undo approval
```

### Scenario 3: Over-Return Products

```
Dispatch received: 100 units
Return 1: 50 units (pending)
Return 2: 60 units (pending) ← OVER-RETURNS!
Sale shift available: 100 - 50 = 50
But Return 2 is 60 units
```

**Current**: ApproveCallbacks checks this, but not validated in model
**Fix**: Add validation in ProductDispatchCallback

---

## Missing Features

### Feature 1: Callback History/Audit Trail
- No tracking of status changes
- No history of who approved/rejected
- Can't see rejection reasons clearly

**Solution**: Add event listeners
```php
class ProductionCallback extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($model) {
            CallbackHistory::create([
                'callback_id' => $model->id,
                'old_status' => $model->getOriginal('status'),
                'new_status' => $model->status,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);
        });
    }
}
```

### Feature 2: Callback Notifications
- No alerts when callback created
- No alerts when action needed
- No email notifications

**Solution**: Add event listeners + queue jobs

### Feature 3: Bulk Actions
- Can't approve multiple callbacks at once
- Time-consuming for high-volume
- No batch operations

---

## Data Quality Issues

### Issue 1: Orphaned Callbacks
```sql
-- Find callbacks without dispatch
SELECT * FROM product_dispatch_callbacks 
WHERE product_dispatch_id IS NULL;
```

### Issue 2: Callbacks Without Employee Records
```sql
-- Find callbacks with deleted employees
SELECT * FROM product_dispatch_callbacks pdc
LEFT JOIN employees e ON pdc.recorded_by = e.id
WHERE e.id IS NULL;
```

### Issue 3: Callbacks with Non-Existent Products
```sql
-- Find callbacks referencing deleted products
SELECT * FROM product_dispatch_callbacks pdc
LEFT JOIN products p ON pdc.product_id = p.id
WHERE p.id IS NULL;
```

---

## Performance Issues

### Query 1: N+1 Problem in ApproveCallbacks
```php
foreach ($callbacks as $callback) {
    $callback->productDispatch;  // Query for each row
    $callback->salesShift;       // Query for each row
    $callback->product;          // Query for each row
    $callback->approvedBy;       // Query for each row
}
```

**Fix**: Use eager loading
```php
ProductDispatchCallback::with([
    'productDispatch.salesShift',
    'product',
    'approvedBy'
])->get();
```

### Query 2: Slow Status Aggregation
```php
// Current in views:
$pending = ProductDispatchCallback::where('status', 'pending')->count();
$approved = ProductDispatchCallback::where('status', 'approved_by_production')->count();
$received = ProductDispatchCallback::where('status', 'received_by_production')->count();
// = 3 queries
```

**Fix**: Use single query with grouping
```php
$stats = ProductDispatchCallback::selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->pluck('count', 'status')
    ->toArray();
```

---

## Summary Table

| Issue | Severity | Type | Fix Effort |
|-------|----------|------|-----------|
| Polymorphic mismatch | CRITICAL | Data | 4 hours |
| Duplicate method | LOW | Code | 15 min |
| Orphaned callbacks | MEDIUM | Design | 2 hours |
| Stock logic in UI | CRITICAL | Architecture | 8 hours |
| No auto stock updates | CRITICAL | Business | 4 hours |
| Missing quantity validation | MEDIUM | Logic | 2 hours |
| Missing branch index | MEDIUM | Performance | 1 hour |
| Inconsistent statuses | MEDIUM | Design | 3 hours |
| No audit trail | MEDIUM | Feature | 4 hours |
| N+1 queries | MEDIUM | Performance | 1 hour |

**Total Priority Effort**: ~32 hours
**Critical Issues**: 3 (polymorphic, stock logic, auto updates)
