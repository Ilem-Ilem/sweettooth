# Callback System Fixes - Priority 1 (Critical)

## Overview
These fixes must be implemented first as they address security vulnerabilities and data integrity issues.

---

## Fix 1: Missing Authorization in Callback Actions

### Current Code (VULNERABLE)
```php
// app/Livewire/BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php:172
public function approveCallback($callbackId)
{
    try {
        DB::beginTransaction();
        
        $callback = ProductionCallback::find($callbackId); // ← NO BRANCH CHECK
        
        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }
        
        // NO AUTHORIZATION CHECK HERE
        
        $employeeId = $this->getEmployeeId(); // ← NO VALIDATION
        if (!$employeeId) {
            $this->toast()->error('Employee not found.')->send();
            return;
        }
        
        $callback->approve($employeeId);
        // ...
    }
}
```

### Problem
1. No verification that callback belongs to user's branch
2. No role check (can any employee approve?)
3. `getEmployeeId()` doesn't validate if user is logged in
4. No audit trail

### Solution

**Step 1: Update getEmployeeId() method**
```php
/**
 * Get authenticated employee with validation
 */
protected function getEmployeeId()
{
    $employee = auth()->guard('employees')->user();
    
    if (!$employee) {
        throw new \Exception('Employee not authenticated. Please log in.');
    }
    
    // Verify employee belongs to this branch
    if ($employee->branch_id != $this->getBranchId()) {
        throw new \Exception('Unauthorized: Employee not in this branch.');
    }
    
    return $employee->id;
}
```

**Step 2: Add authorization to approveCallback()**
```php
public function approveCallback($callbackId)
{
    try {
        DB::beginTransaction();
        
        // 1. Validate callback exists and belongs to this branch
        $callback = ProductionCallback::whereHas('shift', function($q) {
            $q->where('branch_id', $this->getBranchId());
        })->find($callbackId);
        
        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }
        
        // 2. Check authorization
        $employeeId = $this->getEmployeeId();
        
        // 3. Verify callback can be approved
        if (!$callback->canBeApproved()) {
            $this->toast()->error('Callback cannot be approved. Current status: ' . $callback->formatted_status)->send();
            return;
        }
        
        // 4. Perform approval with audit logging
        $callback->approve($employeeId);
        
        // 5. Log audit trail
        AuditService::log(
            auth()->guard('employees')->user(),
            'approve',
            $callback,
            "Approved callback #{$callback->id} for {$callback->item_name ?? 'unknown'}. Quantity: {$callback->quantity} {$callback->uom}",
            'completed'
        );
        
        // ... rest of code
    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Failed: ' . $e->getMessage())->send();
    }
}
```

**Step 3: Apply same pattern to rejectCallback() and completeCallback()**
```php
public function rejectCallback()
{
    try {
        if (empty($this->rejectReason) || strlen($this->rejectReason) < 10) {
            $this->toast()->error('Rejection reason must be at least 10 characters.')->send();
            return;
        }
        
        DB::beginTransaction();
        
        // Validate callback and branch
        $callback = ProductionCallback::whereHas('shift', function($q) {
            $q->where('branch_id', $this->getBranchId());
        })->find($this->callbackToReject);
        
        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }
        
        $employeeId = $this->getEmployeeId();
        
        if (!$callback->canBeApproved()) {
            $this->toast()->error('Callback cannot be rejected. Current status: ' . $callback->formatted_status)->send();
            return;
        }
        
        $callback->reject($employeeId, $this->rejectReason);
        
        // Audit log
        AuditService::log(
            auth()->guard('employees')->user(),
            'reject',
            $callback,
            "Rejected callback #{$callback->id}. Reason: {$this->rejectReason}",
            'completed'
        );
        
        DB::commit();
        
        $this->toast()->success('Callback rejected successfully.')->send();
        $this->closeRejectModal();
        $this->dispatch('$refresh');
        
    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Failed: ' . $e->getMessage())->send();
    }
}
```

---

## Fix 2: DailyProduce.save() Missing in handleFinishedProductCallback()

### Current Code (BUG)
```php
// Line 378-405
protected function handleFinishedProductCallback(ProductionCallback $callback): void
{
    if (!$callback->product_id) {
        throw new \Exception('Finished product callback missing product_id');
    }

    $recipe = \App\Models\Recipe::where('product_id', $callback->product_id)->first();

    if (!$recipe) {
        throw new \Exception('Recipe not found for product ID: ' . $callback->product_id);
    }

    $dailyProduce = \App\Models\DailyProduce::where('shift_id', $callback->shift_id)
        ->where('recipe_id', $recipe->id)
        ->first();

    if (!$dailyProduce) {
        throw new \Exception('DailyProduce record not found...');
    }

    $dailyProduce->callback_quantity = $dailyProduce->callback_quantity + $callback->quantity;
    
    $dailyProduce->updateCalculations(); // ← BUG: updateCalculations() doesn't save!
}
```

### Problem
- `updateCalculations()` updates object properties but doesn't persist to database
- Callback approved but production calculations never updated
- Data integrity issue

### Solution
```php
protected function handleFinishedProductCallback(ProductionCallback $callback): void
{
    if (!$callback->product_id) {
        throw new \Exception('Finished product callback missing product_id');
    }

    // 1. Find recipe with eager loading
    $recipe = \App\Models\Recipe::where('product_id', $callback->product_id)
        ->firstOrFail(); // Use firstOrFail for better error message

    // 2. Find or fail daily produce
    $dailyProduce = \App\Models\DailyProduce::where('shift_id', $callback->shift_id)
        ->where('recipe_id', $recipe->id)
        ->firstOrFail();

    // 3. Update quantity
    $oldCallbackQty = $dailyProduce->callback_quantity;
    $dailyProduce->callback_quantity = $oldCallbackQty + $callback->quantity;
    
    // 4. Recalculate and SAVE
    $dailyProduce->updateCalculations();
    $dailyProduce->save(); // ← ADD THIS LINE
    
    // 5. Log the movement
    \App\Models\StockMovement::create([
        'reference_type' => 'App\\Models\\DailyProduce',
        'reference_id' => $dailyProduce->id,
        'type' => 'callback',
        'quantity' => -$callback->quantity,
        'notes' => "Production callback approved: {$callback->reason} (Callback #{$callback->id})",
        'movement_date' => now(),
    ]);
}
```

---

## Fix 3: ProductionCallback Model - Duplicate Methods

### Current Code (REDUNDANT)
```php
// Line 67-78
public function recordedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by');
}

public function createdBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by'); // SAME THING
}
```

### Solution
```php
// Remove createdBy() - use recordedBy() everywhere
// Update blade templates to use recordedBy consistently
```

### Files to Update
1. `resources/views/livewire/branch-dashboard/inventory/callbacks/approve-callbacks.blade.php` - Change all `createdBy` to `recordedBy`
2. `resources/views/livewire/branch-dashboard/production/callbacks/` - Same change

---

## Fix 4: Validation in Model Approve/Reject Methods

### Current Code (WEAK)
```php
// Line 171-184
public function approve($employeeId): bool
{
    if (! $this->canBeApproved()) {
        return false; // ← Just returns false silently
    }

    $this->update([
        'status' => 'approved_by_inventory',
        'approved_by' => $employeeId,
        'approved_at' => now(),
    ]);

    return true;
}
```

### Solution
```php
public function approve($employeeId): bool
{
    if (! $this->canBeApproved()) {
        throw new \Exception("Callback status {$this->status} cannot be approved");
    }
    
    // Validate employee exists
    if (!\App\Models\Employee::find($employeeId)) {
        throw new \Exception("Employee ID {$employeeId} not found");
    }
    
    // Validate callback has required data
    if (!$this->shift_id) {
        throw new \Exception("Callback missing shift information");
    }
    
    if ($this->isRawMaterial() && !$this->item_id) {
        throw new \Exception("Raw material callback missing item_id");
    }
    
    if ($this->isFinishedProduct() && !$this->product_id) {
        throw new \Exception("Finished product callback missing product_id");
    }

    $this->update([
        'status' => 'approved_by_inventory',
        'approved_by' => $employeeId,
        'approved_at' => now(),
    ]);

    return true;
}
```

---

## Implementation Checklist

- [ ] Update `getEmployeeId()` in both ApproveCallbacks components
- [ ] Add branch verification to all callback query methods
- [ ] Add try-catch in all public action methods
- [ ] Fix `handleFinishedProductCallback()` to call save()
- [ ] Remove `createdBy()` method from ProductionCallback model
- [ ] Update blade templates to use `recordedBy`
- [ ] Add AuditService logging calls to approve/reject/complete methods
- [ ] Test authorization: try approving callback from wrong branch (should fail)
- [ ] Test validation: approve without employee logged in (should fail)
- [ ] Test persistence: verify DailyProduce saved after callback approval

---

## Testing Commands

```php
// Test in tinker
$callback = \App\Models\ProductionCallback::find(1);
$callback->approve(999); // Should throw "Employee not found"

// Test authorization
auth()->guard('employees')->setUser($employee_from_branch_b);
$component->approveCallback($callback_from_branch_a); // Should fail
```
