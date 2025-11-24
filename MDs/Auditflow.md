# Auditflow: Comprehensive Audit System Documentation

**Version:** 1.0  
**Created:** November 23, 2025  
**Status:** ✅ Production Ready

---

## Overview

Auditflow is a unified, model-agnostic audit logging and approval system designed to work across your entire SweetTooth application. It replaces the fragmented `AuditHelper` and `ActionRequestHelper` with a centralized `AuditService` that provides:

- **Single source of truth** for all audit operations
- **Morphic actor support** - Track any model (User, Employee, System) as the "actor"
- **Approval workflows** - Request, approve, reject, and execute sensitive actions
- **Full audit trail** - Complete historical record of what changed, who did it, and why
- **Flexible metadata** - Attach arbitrary context to any audit entry

---

## Architecture

### Core Components

#### 1. **AuditService** (`app/Services/AuditService.php`)
Central service handling all audit operations. Replaces both old helpers with unified methods.

**Key Methods:**
- `log()` - Basic audit logging
- `logSensitiveAction()` - Sensitive actions requiring approval
- `updateActorReference()` - Update morphic *_by columns
- `bulkUpdateActorReference()` - Batch update actor references
- `executeApprovedAction()` - Execute approved requests
- `getLogsFor()` / `getLogsByActor()` - Query audit trails
- `getPendingApprovals()` - Get pending requests

#### 2. **Models**

**AuditLog** (`app/Models/AuditLog.php`)
- Records every action in the system
- Morphic relationships for flexible actor/auditable tracking
- Tracks old/new values, IP, user agent
- Links to approval requests

**ApprovalRequest** (`app/Models/ApprovalRequest.php`)
- Manages sensitive action approval workflows
- Tracks request → approval → execution
- Supports rejection with reasons
- Morphic relationships for requester/approver/executor

#### 3. **Helper Function**
`audit()` function in `BranchHelper.php` provides convenient function-based API that delegates to AuditService.

---

## Migration Path

### From Old System

| Old Approach | New Approach |
|---|---|
| `AuditHelper::auditAction()` | `AuditService::log()` |
| `ActionRequestHelper::requestSensitiveAction()` | `AuditService::logSensitiveAction()` |
| Manual `*_by_id/*_by_type` updates | `AuditService::updateActorReference()` |
| Direct AuditLog creation | Use service methods |

---

## Usage Guide

### 1. Basic Audit Logging

Log any action in your application:

```php
use App\Services\AuditService;

// Simple action logging
AuditService::log(
    causer: auth()->user(),
    action: 'create',
    auditable: $product,
    description: 'Product created by superadmin',
    status: 'completed'
);

// Using helper function (convenience)
audit(
    auth()->user(),
    'delete',
    $stock,
    'Inventory adjustment approved'
);
```

### 2. Sensitive Actions with Approval

Handle actions that require supervisor approval:

```php
$result = AuditService::logSensitiveAction(
    causer: auth('employees')->user(),
    action: 'price_drop_below_cost',
    auditable: $product,
    reason: 'Seasonal promotion - Q4 clearance',
    context: ['branch_id' => current_branch_id()],
    metadata: ['discount_percentage' => 35]
);

// Check if approval is needed
if ($result['status'] === 'pending') {
    $approvalRequest = $result['approval_request'];
    // Notify supervisors
    // Show "Awaiting Approval" UI
} else {
    // Action completed immediately
}
```

### 3. Update Actor References (Morphic Columns)

Update `*_by_id` and `*_by_type` columns safely with audit trail:

```php
use App\Services\AuditService;

// Single record
AuditService::updateActorReference(
    target: $productDispatch,
    relationshipName: 'dispatched_by',
    actor: $employee,
    description: 'Dispatch assigned to new employee'
);

// Result: Updates dispatched_by_id + dispatched_by_type + creates audit log

// Bulk update
AuditService::bulkUpdateActorReference(
    modelClass: ProductDispatch::class,
    ids: [1, 2, 3, 4, 5],
    relationshipName: 'received_by',
    actor: $newEmployee,
    description: 'All pending dispatches reassigned',
    metadata: ['reason' => 'staff_rotation']
);
```

### 4. Approval Workflows

#### Request Approval
```php
// Livewire component or controller
$result = AuditService::logSensitiveAction(
    causer: current_actor(),
    action: 'delete_department',
    auditable: $department,
    reason: 'Consolidating redundant departments',
    context: ['branch_id' => $department->branch_id]
);

if ($result['status'] === 'pending') {
    // Show modal: "Request sent for approval"
}
```

#### Approve Request
```php
// In supervisor/super admin interface
$approvalRequest->approve(
    approver: auth()->user(),
    notes: 'Approved - consolidation makes sense'
);

// Notify requester
```

#### Reject Request
```php
$approvalRequest->reject(
    rejector: auth()->user(),
    reason: 'Need more data on employee reassignment impact'
);
```

#### Execute Approved Action
```php
// After approval, execute the action
AuditService::executeApprovedAction(
    approvalRequest: $approvalRequest,
    approver: auth()->user()
);

// Now the actual delete/update happens
$department->delete();
```

### 5. Query Audit Trails

#### Get logs for a specific model
```php
$logs = AuditService::getLogsFor(
    auditable: $product,
    action: 'update',
    limit: 50
);

foreach ($logs as $log) {
    echo "{$log->causer_name} {$log->action}d at {$log->logged_at}";
    echo "Change: {$log->display_change}";
}
```

#### Get logs by actor
```php
$employeeLogs = AuditService::getLogsByActor(
    causer: $employee,
    action: 'create',
    limit: 100
);

// Shows everything this employee created
```

#### Get pending approvals
```php
$pending = AuditService::getPendingApprovals(
    actor: $employee,  // Optional: filter by requester
    limit: 50
);

foreach ($pending as $request) {
    echo "{$request->requestedBy->name} requested {$request->action}";
    echo "Reason: {$request->reason}";
}
```

### 6. In Livewire Components

#### Log an action
```php
class ProductList extends Component
{
    public function deleteProduct($productId)
    {
        $product = Product::find($productId);
        
        $result = AuditService::logSensitiveAction(
            causer: current_actor(),
            action: 'delete_product',
            auditable: $product,
            reason: request('reason'),
            context: ['branch_id' => current_branch_id()]
        );

        if ($result['status'] === 'pending') {
            $this->toast()->info('Deletion request sent for approval')->send();
        } else {
            $product->delete();
            $this->toast()->success('Product deleted')->send();
        }
    }
}
```

#### Show approval dashboard
```php
class ApprovalDashboard extends Component
{
    #[Computed]
    public function pendingApprovals()
    {
        return AuditService::getPendingApprovals(limit: 100);
    }

    public function approve($requestId)
    {
        $request = ApprovalRequest::find($requestId);
        $request->approve(current_actor());
        
        AuditService::executeApprovedAction($request, current_actor());
        
        $this->toast()->success('Action executed')->send();
    }
}
```

---

## All Models Using _by Morphic Relationships

These 25+ models use the `*_by_id/*_by_type` pattern and are fully supported:

### Employee Management
- `EmployeeLeaveAllocation` - allocated_by
- `EmployeeStepout` - approved_by, rejected_by
- `LeaveApplication` - approved_by, rejected_by, cancelled_by
- `ProbationReview` - acknowledged_by
- `SalaryHistory` - approved_by

### Inventory & Stock
- `HealthCheck` - checked_by
- `Purchase` - recorded_by
- `StockMovement` - moved_by
- `StockTake` - conducted_by, verified_by
- `ItemDispatch` - dispatched_by, received_by
- `ItemRequest` - requested_by, approved_by, cancelled_by, dispatched_by

### Production & Quality
- `DepartmentReport` - generated_by, reviewed_by
- `ProductDispatch` - dispatched_by, received_by
- `ProductionRecord` - produced_by
- `ProductionCallback` - recorded_by, approved_by
- `ProductDispatchCallback` - recorded_by, approved_by, received_by
- `ProductCallback` - recorded_by

### Reporting & Analytics
- `CompiledReport` - compiled_by, approved_by
- `ReportTemplate` - created_by

### Sales & Operations
- `Sale` - sold_by
- `SalesShift` - verified_by
- `ExpiryConfirmation` - confirmed_by
- `Recipe` - created_by
- `ApprovedItem` - approved_by
- `CallBack` - reported_by

---

## Complete Workflow Examples

### Example 1: Delete Product (Requires Approval)

```php
// User initiates delete
$result = AuditService::logSensitiveAction(
    causer: $employee,
    action: 'delete_product',
    auditable: $product,
    reason: 'Product discontinued',
    metadata: ['sku' => $product->sku]
);

// ✅ Audit log created with status='pending'
// ✅ ApprovalRequest created
// 📧 Supervisors notified

// Later: Supervisor approves
$approvalRequest->approve(auth()->user());

// Execute the action
AuditService::executeApprovedAction($approvalRequest, auth()->user());
$product->delete();

// ✅ Audit log created with status='completed'
// ✅ Historical record complete with full chain
```

### Example 2: Production Record Assignment

```php
// Production employee records output
$productionRecord = ProductionRecord::create([
    'produced_by_id' => $employee->id,
    'produced_by_type' => Employee::class,
    'quantity' => 500,
]);

AuditService::log(
    causer: $employee,
    action: 'record_production',
    auditable: $productionRecord,
    description: 'Morning shift production recorded'
);

// Later: Reassign to different employee
AuditService::updateActorReference(
    target: $productionRecord,
    relationshipName: 'produced_by',
    actor: $newEmployee,
    description: 'Corrected: actual producer was different'
);

// ✅ Old and new actors recorded with timestamps
// ✅ Complete audit trail showing who changed what when
```

### Example 3: Bulk Dispatch Assignment

```php
// Get pending dispatches
$dispatches = ProductDispatch::where('status', 'pending')
    ->where('branch_id', $branchId)
    ->limit(20)
    ->pluck('id');

// Reassign all to new distributor
$updated = AuditService::bulkUpdateActorReference(
    modelClass: ProductDispatch::class,
    ids: $dispatches->toArray(),
    relationshipName: 'dispatched_by',
    actor: $newDistributor,
    description: 'Route optimization: consolidating deliveries',
    metadata: ['reason_code' => 'optimization', 'route_id' => 42]
);

// ✅ 20 records updated
// ✅ Single bulk audit log created
// ✅ All metadata preserved
```

### Example 4: Audit Trail Report

```php
// Show full history for a product
$product = Product::find($productId);
$logs = AuditService::getLogsFor($product);

$timeline = $logs->map(function($log) {
    return [
        'timestamp' => $log->logged_at,
        'actor' => $log->causer_name,
        'action' => $log->action,
        'description' => $log->description,
        'change' => $log->display_change,
        'ip' => $log->ip_address,
        'status' => $log->status,
    ];
});

// Display in Blade or return as JSON
return view('audit.timeline', ['timeline' => $timeline]);
```

---

## Database Schema

### audit_logs table

```sql
- id (PK)
- causer_type, causer_id (MorphTo: User, Employee, etc.)
- auditable_type, auditable_id (MorphTo: any model)
- action (string, indexed)
- description (text)
- old_values (json)
- new_values (json)
- ip_address
- user_agent
- status (pending|completed)
- approval_request_id (FK)
- metadata (json)
- logged_at (datetime, indexed)
- created_at, updated_at
```

### approval_requests table

```sql
- id (PK)
- requested_by_type, requested_by_id (MorphTo)
- action (string, indexed)
- auditable_type, auditable_id (MorphTo)
- status (pending|approved|rejected, indexed)
- reason (text)
- metadata (json)
- approved_at, approved_by_type, approved_by_id
- executed_at, executed_by_type, executed_by_id
- rejection_reason (text)
- created_at, updated_at
```

---

## Key Features

### ✅ No Guard Limitations
Works seamlessly with:
- Web guard (Users/SuperAdmins)
- Employee guard (Employees)
- API tokens
- Multi-tenancy

### ✅ Flexible Actor Support
Any model can be an actor:
```php
// User created something
AuditService::log(User::find(1), 'create', $product);

// Employee created something  
AuditService::log(Employee::find(1), 'create', $product);

// System action (no actor)
AuditService::log(null, 'auto_expiry_check', $product);
```

### ✅ Metadata & Context
Attach arbitrary context to every audit entry:
```php
AuditService::log(
    $causer,
    'price_change',
    $product,
    metadata: [
        'old_price' => 100,
        'new_price' => 75,
        'discount_code' => 'SUMMER25',
        'batch_id' => 'batch_001',
        'reason_code' => 'seasonal',
    ]
);
```

### ✅ Request Metadata
Approval requests store context for decision-making:
```php
$result = AuditService::logSensitiveAction(
    $causer,
    'negative_stock_adjustment',
    $stock,
    reason: 'Inventory discrepancy correction',
    context: [
        'branch_id' => 1,
        'discrepancy_amount' => -50,
        'initial_count' => 200,
        'recount_value' => 150,
    ]
);

// Later, approver can see why
$approvalRequest = $result['approval_request'];
echo $approvalRequest->metadata['discrepancy_amount']; // -50
```

### ✅ Query Helpers
Built-in scopes for common queries:
```php
AuditLog::pending()->get();
AuditLog::completed()->get();
AuditLog::byAction('delete')->get();
AuditLog::byWebGuard()->get();
AuditLog::byEmployeeGuard()->get();

ApprovalRequest::pending()->get();
ApprovalRequest::approved()->get();
ApprovalRequest::rejected()->get();
ApprovalRequest::executed()->get();
ApprovalRequest::forAction('delete_product')->get();
ApprovalRequest::forBranch($branchId)->get();
```

---

## Migration to New System

### Step 1: Update Imports
Replace old helper imports:
```php
// ❌ Old
use App\Helpers\AuditHelper;
use App\Helpers\ActionRequestHelper;

// ✅ New
use App\Services\AuditService;
```

### Step 2: Replace Function Calls

#### Audit logging
```php
// ❌ Old
AuditHelper::auditAction('update', $product, ['reason' => '...']);

// ✅ New
AuditService::log(current_actor(), 'update', $product, '...');
// or use helper
audit(current_actor(), 'update', $product, '...');
```

#### Sensitive actions
```php
// ❌ Old
try {
    $request = ActionRequestHelper::requestSensitiveAction('delete_product', [...], $reason);
} catch (ApprovalRequiredException $e) {
    // Handle pending
}

// ✅ New
$result = AuditService::logSensitiveAction($causer, 'delete_product', $product, $reason);
if ($result['status'] === 'pending') {
    // Handle pending
}
```

### Step 3: Update Actor References

Wherever you're updating `*_by_id` and `*_by_type`:
```php
// ❌ Old
$dispatch->update([
    'dispatched_by_id' => $employee->id,
    'dispatched_by_type' => Employee::class,
]);

// ✅ New
AuditService::updateActorReference(
    $dispatch,
    'dispatched_by',
    $employee,
    'Reassigned to different employee'
);
```

---

## Troubleshooting

### Issue: Approval always skipped
**Solution:** Ensure your model has `canBypassApproval()` method or is super admin:
```php
// In Employee or User model
public function canBypassApproval(string $action): bool
{
    return $this->hasRole('supervisor');
}
```

### Issue: Morphic relationship not loading
**Cause:** Type mismatch between stored type and actual class  
**Solution:** Always use `get_class($model)` when storing
```php
// ✅ Correct
'requested_by_type' => get_class($employee),

// ❌ Wrong - may not match actual class
'requested_by_type' => 'Employee',
```

### Issue: Audit logs bloating database
**Solution:** Archive old logs regularly:
```php
// Keep last 90 days
AuditLog::where('logged_at', '<', now()->subDays(90))->delete();
```

---

## Performance Optimization

### 1. Index Queries
Add database indexes:
```php
// In migration
$table->index(['causer_type', 'causer_id']);
$table->index(['auditable_type', 'auditable_id']);
$table->index(['action']);
$table->index('logged_at');
```

### 2. Query Efficiently
Use eager loading to avoid N+1:
```php
$logs = AuditLog::with('causer', 'auditable')
    ->where('auditable_type', Product::class)
    ->orderBy('logged_at', 'desc')
    ->paginate();
```

### 3. Archive Strategy
Move old logs to separate table:
```php
// Keep hot data (30 days) in main table
// Archive older logs monthly
AuditLog::where('logged_at', '<', now()->subDays(30))
    ->moveToArchiveTable();
```

---

## Related Documentation

- **where_by_actor_is_used.md** - Complete map of all 25+ models using *_by relationships
- **_by.md** - Migration details from foreign keys to morphic relationships
- **CODEBASE_SUMMARY.md** - BranchDashboard and DepartmentModule overview

---

## Support & Questions

For implementation questions:
1. Review examples in this document
2. Check AuditService docblock comments
3. Look at how models implement morphic relationships
4. Refer to where_by_actor_is_used.md for pattern consistency

---

**Last Updated:** November 23, 2025  
**Next Review:** Q1 2026
