# Auditflow Quick Reference

**TL;DR:** Use `AuditService` for all audit operations. All 25+ *_by models work seamlessly.

---

## Most Common Patterns

### 1. Log Any Action (1 line)
```php
AuditService::log(current_actor(), 'create', $product, 'Product added');
```

### 2. Log Sensitive Action (needs approval)
```php
$result = AuditService::logSensitiveAction(
    current_actor(), 'delete', $product, 'Discontinued item'
);
if ($result['status'] === 'pending') {
    // Wait for approval
}
```

### 3. Update Actor Reference (safe *_by updates)
```php
AuditService::updateActorReference(
    $dispatch, 'dispatched_by', $employee
);
```

### 4. Bulk Update Actor References
```php
AuditService::bulkUpdateActorReference(
    ProductDispatch::class, $ids, 'dispatched_by', $employee
);
```

### 5. Approve & Execute
```php
$request->approve(auth()->user());
AuditService::executeApprovedAction($request, auth()->user());
```

### 6. Query Audit Trail
```php
AuditService::getLogsFor($product);  // What happened to this product
AuditService::getLogsByActor($user); // What did this person do
AuditService::getPendingApprovals(); // What's waiting for approval
```

---

## Import
```php
use App\Services\AuditService;
// or use helper function
audit(...);
```

---

## Models Covered (25+)

✅ All of these work out of the box:

**Employee:** EmployeeLeaveAllocation, EmployeeStepout, LeaveApplication, ProbationReview, SalaryHistory

**Inventory:** HealthCheck, Purchase, StockMovement, StockTake, ItemDispatch, ItemRequest

**Production:** DepartmentReport, ProductDispatch, ProductionRecord, ProductionCallback, ProductDispatchCallback

**Reporting:** CompiledReport, ReportTemplate

**Sales:** Sale, SalesShift, ExpiryConfirmation, Recipe, ApprovedItem, CallBack

---

## Common Actions

```php
'create', 'update', 'delete'           // Basic
'approve', 'reject', 'cancel'          // Workflow
'record_production', 'dispatch'        // Domain-specific
'price_change', 'stock_adjustment'     // Sensitive
```

---

## Metadata Tips

Attach context to every log:
```php
AuditService::log(
    $causer, 'create', $product,
    metadata: [
        'batch_id' => 123,
        'imported_from' => 'csv',
        'discount' => 25
    ]
);
```

---

## Status Values

- `'pending'` - Waiting for approval
- `'completed'` - Done
- `'rejected'` - Approval denied

---

## Key Relationships

```php
AuditLog::causer();       // Who did it (User/Employee/System)
AuditLog::auditable();    // What was affected (any model)
AuditLog::old_values;     // Previous state
AuditLog::new_values;     // New state
AuditLog::approval_request(); // If approval required

ApprovalRequest::requestedBy();  // Who asked
ApprovalRequest::auditable();    // What's being changed
ApprovalRequest::approvedBy();   // Who approved
ApprovalRequest::auditLogs();    // All related logs
```

---

## Scopes

**AuditLog:**
```php
->pending()
->completed()
->byWebGuard()         // Only Users
->byEmployeeGuard()    // Only Employees
->byAction('delete')
```

**ApprovalRequest:**
```php
->pending()
->approved()
->rejected()
->executed()
->forAction('delete')
->forBranch($id)
```

---

## Real Code Example

```php
class LeaveApproval extends Component
{
    public function approveLeave($leaveId)
    {
        $leave = LeaveApplication::find($leaveId);
        
        // Log the approval action
        AuditService::log(
            current_actor(),
            'approve_leave',
            $leave,
            request('notes'),
            metadata: ['days' => $leave->days]
        );
        
        // Mark as approved in leave model
        $leave->update(['approved_by_id' => current_actor()->id]);
        
        $this->toast()->success('Leave approved')->send();
    }
}
```

---

## Migration Checklist

- [ ] Replace `AuditHelper::` with `AuditService::`
- [ ] Replace `ActionRequestHelper::` with `AuditService::logSensitiveAction()`
- [ ] Update `*_by_id/*_by_type` updates to use `updateActorReference()`
- [ ] Add metadata to important logs
- [ ] Test with both User and Employee guards
- [ ] Verify approval workflows
- [ ] Query audit reports
- [ ] Archive old logs if needed

---

**See:** `Auditflow.md` for complete guide  
**Models:** Check `where_by_actor_is_used.md` for full list
