# State Management and Data Integrity Issues in Callback System

## 1. Workflow State Management Faults

### Problem Description
The callback system suffers from complex status transitions (4-5 states) with inconsistent validation. There is no state machine pattern implemented - status changes are scattered across methods without centralized logic.

### Specific Code Evidence
- In `ProductDispatchCallback.php`, the `approve()` method (lines 163-184) and `markAsReceived()` method (lines 191-213) handle state transitions but lack proper guards
- In `ProductionCallback.php`, the `approve()` method (lines 176-205) has similar issues
- The `CallbackStatus.php` enum defines valid transitions but isn't consistently enforced

### Evidence of Inconsistent Validation
```php
// From ProductDispatchCallback.php - lacks proper state transition validation
public function approve($actor = null): bool
{
    if (! $this->canBeApproved()) {
        return false;
    }
    // ... approval logic without proper state machine
}
```

### Impact
- Users can perform invalid state transitions
- Data inconsistency across the system
- Difficult to maintain and debug workflow logic

### Solution: Implement Proper State Machine
Use the spatie/laravel-model-states library to centralize all transitions in the model with guards:

```php
// Example implementation
use Spatie\ModelStates\HasStates;

class ProductDispatchCallback extends Model
{
    use HasStates;
    
    public function state(): State
    {
        return $this->state;
    }
    
    public function canBeApproved(): bool
    {
        return $this->state->canTransitionTo(ApprovedState::class);
    }
}
```

### Race Conditions in Concurrent Status Updates
#### Problem Description
No optimistic locking or version control exists, allowing multiple users to approve/receive the same callback simultaneously.

#### Specific Code Evidence
- In `ApproveCallbacks.php`, the `approveCallback()` method (lines 130-159) uses a database transaction but doesn't lock the specific record
- The `receiveCallback()` method (lines 161-189) has the same issue

#### Impact
- Double-processing of callbacks
- Inconsistent data across the system

#### Solution: Add Record-Level Locking
```php
// Improved approve method with record locking
public function approve($actor = null): bool
{
    if (! $this->canBeApproved()) {
        return false;
    }

    $actor = $actor ?? current_actor();
    if (! $actor) {
        throw new RuntimeException('No authenticated actor found');
    }

    // Lock the record during update
    $this->lockForUpdate();
    
    $this->update([
        'status' => 'approved_by_production',
        'approved_by_id' => $actor->id,
        'approved_by_type' => get_class($actor),
        'approved_at' => now(),
    ]);

    return true;
}
```

## 2. Data Integrity and Validation Faults

### Problem Description
Insufficient quantity validation allows callbacks to exceed available quantities in some paths but not others.

### Specific Code Evidence
- In `CreateDispatchCallback.php:235-239`, quantity validation exists in the `submitCallback()` method
- In `ProductDispatchCallback.php:347-368`, the `validateQuantity()` method has complex logic but may not be called consistently

### Validation Code Example
```php
// From CreateDispatchCallback.php - validation logic
public function submitCallback()
{
    $this->validate([
        'callbackQuantity' => 'required|numeric|min:0.01',
        'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,over_received,wrong_item,other',
    ]);

    // Additional validation for available quantity
    $availableQty = $this->getAvailableQuantity($this->selectedDispatch);
    if ($this->callbackQuantity > $availableQty) {
        $this->toast()->error("Callback quantity cannot exceed available quantity ({$availableQty}).")->send();
        return;
    }
    // ... rest of the method
}
```

### Impact
- Over-callbacking leads to negative inventory
- Financial losses due to untracked inventory discrepancies

### Solution: Always Validate Against Available Quantities
```php
// Enhanced validation method
public function validateQuantity(): bool
{
    if (! $this->product_dispatch_id) {
        // Orphaned callback - no dispatch reference
        return true;
    }

    if (! $this->productDispatch) {
        throw new RuntimeException('No dispatch found for callback');
    }

    $totalCallbacks = self::where('product_dispatch_id', $this->product_dispatch_id)
        ->where('id', '!=', $this->id ?? 'fake-id')
        ->whereIn('status', ['pending', 'approved_by_production', 'received_by_production', 'completed'])
        ->sum('quantity');

    $available = $this->productDispatch->received_quantity - $totalCallbacks;

    if ($this->quantity > $available) {
        throw new ValidationException(
            "Callback quantity ({$this->quantity}) exceeds available ({$available})"
        );
    }

    return true;
}
```

### Missing Foreign Key Constraints
#### Problem Description
Callbacks reference dispatches/shifts without enforced relationships.

#### Evidence
- From `export.sql`, foreign key constraints exist but may not be properly enforced in all cases
- The `ProductDispatchCallback` model has relationships defined but no explicit constraint checking

#### Impact
- Orphaned records in the database
- Data inconsistency and referential integrity issues

#### Solution: Add Proper Database Constraints
```sql
-- Add foreign key constraints
ALTER TABLE callbacks 
ADD CONSTRAINT fk_callbacks_dispatches 
FOREIGN KEY (dispatch_id) REFERENCES dispatches(id) 
ON DELETE CASCADE;

ALTER TABLE callbacks 
ADD CONSTRAINT fk_callbacks_shifts 
FOREIGN KEY (shift_id) REFERENCES shifts(id) 
ON DELETE SET NULL;
```

### No Audit Trail for Status Changes
#### Problem Description
Status updates don't log who changed what and when.

#### Evidence
- The `CallbackApprovalService.php` has audit logging but it's not consistently applied across all state changes
- Manual status updates bypass the audit system

#### Impact
- No accountability for changes
- Difficult to debug issues and track changes

#### Solution: Add Comprehensive Audit Trail System
```php
// Using owen-it/laravel-auditing
use OwenIt\Auditing\Contracts\Auditable;

class ProductDispatchCallback extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $auditInclude = [
        'status',
        'quantity',
        'notes',
        'updated_by'
    ];
    
    // Override the auditable attributes to include custom logic
    public function transformAudit(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
```

## Implementation Checklist

- [ ] Implement state machine pattern using spatie/laravel-model-states
- [ ] Add database locking for concurrent updates
- [ ] Create centralized quantity validation system
- [ ] Add foreign key constraints to database
- [ ] Implement audit trail system
- [ ] Create stock reservation mechanism
- [ ] Add proper error handling for validation failures
- [ ] Ensure all state transitions go through proper validation