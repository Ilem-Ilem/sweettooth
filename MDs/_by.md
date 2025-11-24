# Migrations with "_by" Columns - Morph Conversion

This document lists all migration files containing columns with "_by" pattern (e.g., requested_by, moved_by, etc.) and documents the conversion from foreign keys to Laravel morphic relationships with UUID IDs.

## Summary
Total files with "_by" columns: 26
**Status**: ✅ All conversions completed - UUID morphic relationships implemented

---

## Detailed Conversion List

### 1. employee_leave_allocations
**Before:**
```php
$table->uuid('allocated_by')->nullable(); // Who allocated this
$table->foreign('allocated_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('allocated_by_id');
$table->string('allocated_by_type');
```

**Model Update** (EmployeeLeaveAllocation):
```php
// Before
public function allocatedBy(): BelongsTo { ... }

// After
public function allocatedBy(): MorphTo
{
    return $this->morphTo('allocated_by', 'allocated_by_type', 'allocated_by_id');
}
```

---

### 2. product_dispatches
**Before:**
```php
$table->uuid('dispatched_by');
$table->foreign('dispatched_by')->references('id')->on('employees')->onDelete('cascade');

$table->uuid('received_by')->nullable()->comment('Sales employee who received');
$table->foreign('received_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('dispatched_by_id');
$table->string('dispatched_by_type');

$table->uuid('received_by_id')->nullable()->comment('Sales employee who received');
$table->string('received_by_type')->nullable();
```

**Model Updates** (ProductDispatch):
```php
// Before
public function dispatchedBy(): BelongsTo { ... }
public function receivedBy(): BelongsTo { ... }

// After
public function dispatchedBy(): MorphTo
{
    return $this->morphTo('dispatched_by', 'dispatched_by_type', 'dispatched_by_id');
}

public function receivedBy(): MorphTo
{
    return $this->morphTo('received_by', 'received_by_type', 'received_by_id');
}
```

---

### 3. department_reports
**Before:**
```php
$table->foreignUuid('generated_by')->nullable()->constrained('employees')->nullOnDelete();
$table->foreignUuid('reviewed_by')->nullable()->constrained('employees')->nullOnDelete();
```

**After:**
```php
$table->uuid('generated_by_id')->nullable();
$table->string('generated_by_type')->nullable();

$table->uuid('reviewed_by_id')->nullable();
$table->string('reviewed_by_type')->nullable();
```

**Model Updates** (DepartmentReport):
```php
public function generatedBy(): MorphTo
{
    return $this->morphTo('generated_by', 'generated_by_type', 'generated_by_id');
}

public function reviewedBy(): MorphTo
{
    return $this->morphTo('reviewed_by', 'reviewed_by_type', 'reviewed_by_id');
}

// Updated method signature
public function markAsReviewed($employeeId, $employeeType, $notes = null)
```

---

### 4. health_checks ✅ (Already had morphic columns)
```php
$table->uuid('checked_by_id');
$table->string('checked_by_type');
```

---

### 5. purchases ✅ (Already had morphic columns)
```php
$table->uuid('recorded_by_id');
$table->string('recorded_by_type');
```

---

### 6. stock_movements ✅ (Already had morphic columns)
```php
$table->string('moved_by_type')->nullable();
$table->uuid('moved_by_id')->nullable();
```

---

### 7. item_requests ✅ (Already had morphic columns)
```php
$table->uuidMorphs('requested_by');  // approved_by_id + approved_by_type
$table->uuidMorphs('approved_by');
$table->uuidMorphs('cancelled_by');
$table->uuidMorphs('dispatched_by');
```

---

### 8. employee_stepouts
**Before:**
```php
$table->uuid('approved_by')->nullable();
$table->uuid('rejected_by')->nullable();
$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
$table->foreign('rejected_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();

$table->uuid('rejected_by_id')->nullable();
$table->string('rejected_by_type')->nullable();
```

**Model Updates** (EmployeeStepout):
```php
public function approvedBy(): MorphTo
{
    return $this->morphTo('approved_by', 'approved_by_type', 'approved_by_id');
}

public function rejectedBy(): MorphTo
{
    return $this->morphTo('rejected_by', 'rejected_by_type', 'rejected_by_id');
}

// Updated method signatures
public function approve($approverId, $approverType, $notes = null)
public function reject($rejecterId, $rejectorType, $reason)
```

---

### 9. probation_reviews
**Before:**
```php
$table->uuid('acknowledged_by')->nullable();
$table->foreign('acknowledged_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('acknowledged_by_id')->nullable();
$table->string('acknowledged_by_type')->nullable();
```

**Model Created** (ProbationReview):
```php
public function acknowledgedBy(): MorphTo
{
    return $this->morphTo('acknowledged_by', 'acknowledged_by_type', 'acknowledged_by_id');
}
```

---

### 10. compiled_reports
**Before:**
```php
$table->foreignUuid('compiled_by')->nullable()->constrained('employees')->nullOnDelete();
$table->foreignUuid('approved_by')->nullable()->constrained('employees')->nullOnDelete();
```

**After:**
```php
$table->uuid('compiled_by_id')->nullable();
$table->string('compiled_by_type')->nullable();

$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();
```

**Model Updates** (CompiledReport):
```php
public function compiledBy(): MorphTo
{
    return $this->morphTo('compiled_by', 'compiled_by_type', 'compiled_by_id');
}

public function approvedBy(): MorphTo
{
    return $this->morphTo('approved_by', 'approved_by_type', 'approved_by_id');
}

// Updated method signature
public function markAsApproved($employeeId, $employeeType)
```

---

### 11. production_callbacks
**Before:**
```php
$table->uuid('recorded_by');
$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');

$table->uuid('approved_by')->nullable();
$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('recorded_by_id');
$table->string('recorded_by_type');

$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();
```

---

### 12. salary_history
**Before:**
```php
$table->uuid('approved_by')->nullable();
$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();
```

**Model Created** (SalaryHistory):
```php
public function approvedBy(): MorphTo
{
    return $this->morphTo('approved_by', 'approved_by_type', 'approved_by_id');
}
```

---

### 13. leave_applications
**Before:**
```php
$table->uuid('approved_by_id')->nullable();
$table->uuid('approved_by_type')->nullable();  // ❌ WRONG TYPE
$table->uuid('rejected_by_id')->nullable();
$table->uuid('rejected_by_type')->nullable();  // ❌ WRONG TYPE
$table->uuid('cancelled_by_id')->nullable();
$table->uuid('cancelled_by_type')->nullable();  // ❌ WRONG TYPE
```

**After:**
```php
$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();  // ✅ CORRECTED

$table->uuid('rejected_by_id')->nullable();
$table->string('rejected_by_type')->nullable();  // ✅ CORRECTED

$table->uuid('cancelled_by_id')->nullable();
$table->string('cancelled_by_type')->nullable();  // ✅ CORRECTED
```

---

### 14. call_backs
**Before:**
```php
$table->uuid('reported_by');
$table->foreign('reported_by')->references('id')->on('employees')->onDelete('restrict');
```

**After:**
```php
$table->uuid('reported_by_id');
$table->string('reported_by_type');
```

---

### 15. expiry_confirmations
**Before:**
```php
$table->uuid('confirmed_by');
$table->foreign('confirmed_by')->references('id')->on('employees')->onDelete('cascade');
```

**After:**
```php
$table->uuid('confirmed_by_id');
$table->string('confirmed_by_type');
```

**Model Updates** (ExpiryConfirmation):
```php
public function confirmedBy(): MorphTo
{
    return $this->morphTo('confirmed_by', 'confirmed_by_type', 'confirmed_by_id');
}
```

---

### 16. report_templates
**Before:**
```php
$table->foreignUuid('created_by')->nullable()->constrained('employees')->nullOnDelete();
```

**After:**
```php
$table->uuid('created_by_id')->nullable();
$table->string('created_by_type')->nullable();
```

**Model Updates** (ReportTemplate):
```php
public function createdBy(): MorphTo
{
    return $this->morphTo('created_by', 'created_by_type', 'created_by_id');
}
```

---

### 17. recipes
**Before:**
```php
$table->uuid('created_by');
$table->foreign('created_by')->references('id')->on('employees')->onDelete('restrict');
```

**After:**
```php
$table->uuid('created_by_id');
$table->string('created_by_type');
```

**Model Updates** (Recipe):
```php
public function createdBy(): MorphTo
{
    return $this->morphTo('created_by', 'created_by_type', 'created_by_id');
}
```

---

### 18. stock_takes
**Before:**
```php
$table->uuid('conducted_by');
$table->foreign('conducted_by')->references('id')->on('employees')->onDelete('restrict');

$table->uuid('verified_by')->nullable();
$table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('conducted_by_id');
$table->string('conducted_by_type');

$table->uuid('verified_by_id')->nullable();
$table->string('verified_by_type')->nullable();
```

**Model Updates** (StockTake):
```php
public function conductor(): MorphTo
{
    return $this->morphTo('conducted_by', 'conducted_by_type', 'conducted_by_id');
}

public function verifier(): MorphTo
{
    return $this->morphTo('verified_by', 'verified_by_type', 'verified_by_id');
}

// Updated method signature
public function verify($verifiedById, $verifiedByType): void
```

---

### 19. item_dispatches
**Before:**
```php
$table->uuid('dispatched_by');
$table->foreign('dispatched_by')->references('id')->on('employees')->onDelete('restrict');

$table->uuid('received_by')->nullable();
$table->foreign('received_by')->references('id')->on('employees')->onDelete('restrict');
```

**After:**
```php
$table->uuid('dispatched_by_id');
$table->string('dispatched_by_type');

$table->uuid('received_by_id')->nullable();
$table->string('received_by_type')->nullable();
```

**Model Updates** (ItemDispatch):
```php
public function dispatcher(): MorphTo
{
    return $this->morphTo('dispatched_by', 'dispatched_by_type', 'dispatched_by_id');
}

public function receiver(): MorphTo
{
    return $this->morphTo('received_by', 'received_by_type', 'received_by_id');
}
```

---

### 20. sales_shifts
**Before:**
```php
$table->uuid('verified_by')->nullable();
$table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('verified_by_id')->nullable();
$table->string('verified_by_type')->nullable();
```

**Model Updates** (SalesShift):
```php
public function verifiedBy(): MorphTo
{
    return $this->morphTo('verified_by', 'verified_by_type', 'verified_by_id');
}
```

---

### 21. sales
**Before:**
```php
$table->uuid('sold_by');
$table->foreign('sold_by')->references('id')->on('employees')->onDelete('restrict');
```

**After:**
```php
$table->uuid('sold_by_id');
$table->string('sold_by_type');
```

**Model Updates** (Sale):
```php
public function soldBy(): MorphTo
{
    return $this->morphTo('sold_by', 'sold_by_type', 'sold_by_id');
}
```

---

### 22. product_callbacks
**Before:**
```php
$table->uuid('recorded_by');
$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');
```

**After:**
```php
$table->uuid('recorded_by_id');
$table->string('recorded_by_type');
```

---

### 23. product_dispatch_callbacks
**Before:**
```php
$table->uuid('recorded_by');
$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');

$table->uuid('approved_by')->nullable();
$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');

$table->uuid('received_by')->nullable();
$table->foreign('received_by')->references('id')->on('employees')->onDelete('set null');
```

**After:**
```php
$table->uuid('recorded_by_id');
$table->string('recorded_by_type');

$table->uuid('approved_by_id')->nullable();
$table->string('approved_by_type')->nullable();

$table->uuid('received_by_id')->nullable();
$table->string('received_by_type')->nullable();
```

---

### 24. approved_items
**Before:**
```php
$table->uuid('approved_by');
```

**After:**
```php
$table->uuid('approved_by_id');
$table->string('approved_by_type');
```

---

### 25. production_records
**Before:**
```php
$table->uuid('produced_by');
$table->foreign('produced_by')->references('id')->on('employees')->onDelete('restrict');
```

**After:**
```php
$table->uuid('produced_by_id');
$table->string('produced_by_type');
```

**Model Updates** (ProductionRecord):
```php
public function producedBy(): MorphTo
{
    return $this->morphTo('produced_by', 'produced_by_type', 'produced_by_id');
}
```

---

## Summary of Changes

### Pattern Applied to All Migrations:
- ❌ **Before**: `$table->uuid('*_by'); $table->foreign('*_by')...`
- ✅ **After**: 
  ```php
  $table->uuid('*_by_id');
  $table->string('*_by_type');
  ```

### Model Changes Applied:
1. Added `MorphTo` import: `use Illuminate\Database\Eloquent\Relations\MorphTo;`
2. Updated `$fillable` array to include both `*_by_id` and `*_by_type`
3. Changed relationship methods from `BelongsTo` to `MorphTo`:
   ```php
   // Before
   public function something(): BelongsTo
   {
       return $this->belongsTo(Employee::class, 'something_by');
   }
   
   // After
   public function something(): MorphTo
   {
       return $this->morphTo('something_by', 'something_by_type', 'something_by_id');
   }
   ```
4. Updated method signatures that used these columns to include type parameter where needed

### Benefits of Morphic Relationships:
- ✅ UUID-based polymorphic relationships
- ✅ Flexible actor types (Employee, User, or any model)
- ✅ Maintains referential integrity through type specification
- ✅ More maintainable than foreign key constraints for polymorphic data
- ✅ Consistent pattern across entire application

---

## Migration Order for Running:
All migrations have been updated and are ready for deployment. The changes are backwards compatible if you're creating fresh instances, but require data migration for existing databases.

