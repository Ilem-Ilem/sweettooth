# Missing Audit Services - Detailed Specifications

**Total Services Needed:** 8  
**Priority Levels:** 🔴 Critical, 🟡 High, 🟢 Medium  

---

## 1. LeaveAuditService (🔴 CRITICAL)

**File:** `app/Services/LeaveAuditService.php`  
**Why:** Leave requests directly impact operations, need audit trail  

### Methods:

#### logLeaveApplication()
```php
public static function logLeaveApplication(
    EmployeeLeaveApplication $leave,
    $actor,
    $status = 'pending'
): AuditLog
{
    return AuditService::log(
        $actor,
        'leave:applied',
        $leave,
        "Leave applied: {$leave->leave_type} from {$leave->start_date} to {$leave->end_date}",
        $status
    );
}
```

#### logLeaveApproval()
```php
public static function logLeaveApproval(
    EmployeeLeaveApplication $leave,
    $actor,
    $approvalReason = null
): AuditLog
{
    return AuditService::log(
        $actor,
        'leave:approved',
        $leave,
        "Leave approved" . ($approvalReason ? ": {$approvalReason}" : ""),
        'completed'
    );
}
```

#### logLeaveRejection()
```php
public static function logLeaveRejection(
    EmployeeLeaveApplication $leave,
    $actor,
    $rejectionReason
): AuditLog
{
    return AuditService::log(
        $actor,
        'leave:rejected',
        $leave,
        "Leave rejected: {$rejectionReason}",
        'completed'
    );
}
```

#### logLeaveAllocationChange()
```php
public static function logLeaveAllocationChange(
    EmployeeLeaveApplication $leave,
    $oldBalance,
    $newBalance,
    $reason,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'leave:allocation_changed',
        $leave,
        "Leave allocation changed from {$oldBalance} to {$newBalance}: {$reason}",
        'completed'
    );
}
```

#### logLeaveTypeCreation()
```php
public static function logLeaveTypeCreation(
    $leaveType,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'leave_type:created',
        null,
        "New leave type created: {$leaveType}",
        'completed'
    );
}
```

---

## 2. PayrollApprovalService (🔴 CRITICAL)

**File:** `app/Services/PayrollApprovalService.php`  
**Why:** Salary/payment changes affect compliance, need approval workflow  

### Methods:

#### requestSalaryChange()
```php
public static function requestSalaryChange(
    Employee $employee,
    $newSalary,
    $reason,
    $effectiveDate
): ApprovalAuditRequest
{
    // Validate:
    // - New salary >= minimum wage
    // - Change percentage reasonable (e.g., not > 50%)
    // - Finance manager notified
    
    // Create request with payload
    // Log as pending
    
    return $request;
}
```

#### requestBonusPayment()
```php
public static function requestBonusPayment(
    Employee $employee,
    $amount,
    $reason
): ApprovalAuditRequest
{
    // Validate:
    // - Amount reasonable
    // - Bonus type valid
    // - Employee eligible
    
    // Create request
    // Log as pending
    
    return $request;
}
```

#### requestBenefitChange()
```php
public static function requestBenefitChange(
    Employee $employee,
    array $benefits,
    $reason
): ApprovalAuditRequest
{
    // Validate benefits
    // Create request
    // Log as pending
    
    return $request;
}
```

#### requestPayrollClosed()
```php
public static function requestPayrollClosed(
    $payrollPeriod,
    $actor
): ApprovalAuditRequest
{
    // Validate:
    // - All timesheets submitted
    // - All deductions calculated
    // - No pending adjustments
    
    // Create request
    // Log as pending
    
    return $request;
}
```

#### executePayrollChange()
```php
public static function executePayrollChange(
    ApprovalAuditRequest $request,
    $approver
): bool
{
    // Execute the payroll change
    // Update records
    // Notify payroll
    // Log completion
    
    return true;
}
```

---

## 3. PayrollAuditService (🔴 CRITICAL)

**File:** `app/Services/PayrollAuditService.php`  
**Why:** Salary/payment changes need detailed audit trail  

### Methods:

#### logSalaryChanged()
```php
public static function logSalaryChanged(
    Employee $employee,
    $oldSalary,
    $newSalary,
    $reason,
    $actor,
    $effectiveDate
): AuditLog
{
    $change = (($newSalary - $oldSalary) / $oldSalary * 100);
    
    return AuditService::log(
        $actor,
        'payroll:salary_changed',
        $employee,
        "Salary changed from {$oldSalary} to {$newSalary} ({$change}%) effective {$effectiveDate}: {$reason}",
        'completed'
    );
}
```

#### logBonusProcessed()
```php
public static function logBonusProcessed(
    Employee $employee,
    $amount,
    $reason,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'payroll:bonus_processed',
        $employee,
        "Bonus processed: {$amount} - {$reason}",
        'completed'
    );
}
```

#### logDeductionApplied()
```php
public static function logDeductionApplied(
    Employee $employee,
    $amount,
    $deductionType,
    $reason,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'payroll:deduction_applied',
        $employee,
        "Deduction applied: {$deductionType} {$amount} - {$reason}",
        'completed'
    );
}
```

#### logPayrollClosed()
```php
public static function logPayrollClosed(
    $payrollPeriod,
    $totalAmount,
    $employeeCount,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'payroll:period_closed',
        null,
        "Payroll period {$payrollPeriod} closed. Total: {$totalAmount}, Employees: {$employeeCount}",
        'completed'
    );
}
```

---

## 4. DepartmentApprovalService (🟡 HIGH)

**File:** `app/Services/DepartmentApprovalService.php`  
**Why:** Department changes affect organizational structure, need approval  

### Methods:

#### requestCreate()
```php
public static function requestCreate(
    array $departmentData,
    $reason
): ApprovalAuditRequest
{
    // Validate:
    // - Name unique
    // - Category exists
    // - Manager exists
    
    // Create request
    // Log as pending
    
    return $request;
}
```

#### requestUpdate()
```php
public static function requestUpdate(
    Department $department,
    array $changes,
    $reason
): ApprovalAuditRequest
{
    // Validate changes
    // Create request
    // Log as pending
    
    return $request;
}
```

#### requestDelete()
```php
public static function requestDelete(
    Department $department,
    $reason
): ApprovalAuditRequest
{
    // Validate:
    // - No employees assigned
    // - No active operations
    
    // Create request
    // Log as pending
    
    return $request;
}
```

#### executeCreate() / executeUpdate() / executeDelete()
```php
// Similar execution pattern
```

---

## 5. DepartmentCategoryApprovalService (🟡 HIGH)

**File:** `app/Services/DepartmentCategoryApprovalService.php`  
**Why:** Category structure affects department organization  

### Methods:
- `requestCreate()`
- `requestUpdate()`
- `requestDelete()`
- `executeCreate()` / `executeUpdate()` / `executeDelete()`

---

## 6. CallbackApprovalService (🟡 HIGH)

**File:** `app/Services/CallbackApprovalService.php`  
**Why:** Callbacks impact inventory accuracy and production quality  

### Methods:

#### requestInventoryCallback()
```php
public static function requestInventoryCallback(
    $item,
    $quantity,
    $reason,
    $actor
): ApprovalAuditRequest
{
    // Validate:
    // - Item exists
    // - Quantity reasonable
    // - Reason provided
    
    // Create request
    // Log as pending
    
    return $request;
}
```

#### requestProductionCallback()
```php
public static function requestProductionCallback(
    $product,
    $quantity,
    $reason,
    $actor
): ApprovalAuditRequest
{
    // Similar validation
    // Create request
    // Log as pending
    
    return $request;
}
```

#### executeInventoryCallback()
```php
public static function executeInventoryCallback(
    ApprovalAuditRequest $request,
    $approver
): bool
{
    // Adjust inventory
    // Create stock movement
    // Log completion
    
    return true;
}
```

---

## 7. PurchaseAuditService (🟡 HIGH)

**File:** `app/Services/PurchaseAuditService.php`  
**Why:** Complements `PurchaseAuditApprovalService` with detailed logging  

### Methods:

#### logPurchaseCreated()
```php
public static function logPurchaseCreated(
    Purchase $purchase,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'purchase:created',
        $purchase,
        "Purchase created from {$purchase->supplier->name}",
        'completed'
    );
}
```

#### logPurchaseApproved()
```php
public static function logPurchaseApproved(
    Purchase $purchase,
    $actor,
    $approvalReason = null
): AuditLog
{
    return AuditService::log(
        $actor,
        'purchase:approved',
        $purchase,
        "Purchase approved" . ($approvalReason ? ": {$approvalReason}" : ""),
        'completed'
    );
}
```

#### logPurchaseRejected()
```php
public static function logPurchaseRejected(
    Purchase $purchase,
    $actor,
    $rejectionReason
): AuditLog
{
    return AuditService::log(
        $actor,
        'purchase:rejected',
        $purchase,
        "Purchase rejected: {$rejectionReason}",
        'completed'
    );
}
```

#### logReceived()
```php
public static function logReceived(
    Purchase $purchase,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'purchase:received',
        $purchase,
        "Purchase received and stock updated",
        'completed'
    );
}
```

---

## 8. ShiftAuditService (🟢 MEDIUM)

**File:** `app/Services/ShiftAuditService.php`  
**Why:** Shift operations affect daily operations and payroll  

### Methods:

#### logShiftCreated()
```php
public static function logShiftCreated(
    $shift,
    $actor
): AuditLog
```

#### logShiftClosed()
```php
public static function logShiftClosed(
    $shift,
    array $summary,
    $actor
): AuditLog
```

#### logShiftReopened()
```php
public static function logShiftReopened(
    $shift,
    $reason,
    $actor
): AuditLog
```

---

## Implementation Priority Order

### Phase 1 (Critical) - Week 1
1. **EmployeeApprovalService** - Employee CRUD workflows
2. **EmployeeAuditService** - Employee change logging
3. **PayrollApprovalService** - Salary change approvals
4. **PayrollAuditService** - Payroll change logging

### Phase 2 (High) - Week 2
5. **DepartmentApprovalService** - Department management
6. **DepartmentCategoryApprovalService** - Category management
7. **CallbackApprovalService** - Callback workflows

### Phase 3 (Medium) - Week 3
8. **PurchaseAuditService** - Purchase logging
9. **ShiftAuditService** - Shift operation logging
10. **LeaveAuditService** - Leave request logging

---

## Integration Points

### Component Integration
Each service integrates with components like:
- Request submission
- Approval UI in `AuditManagement/Index.php`
- Execution handlers

### Database Integration
- `ApprovalAuditRequest` table for requests
- `AuditLog` table for logging

### Service Dependencies
```
EmployeeApprovalService
├── AuditService (for logging)
├── EmployeeAuditService (for specialized logs)
└── ApprovalAuditRequest (for requests)

PayrollApprovalService
├── EmployeeApprovalService (may depend on)
├── PayrollAuditService
└── ApprovalAuditRequest

etc.
```

---

## Testing Requirements

Each service needs:
- ✅ Unit tests for request creation
- ✅ Unit tests for validation
- ✅ Integration tests for execution
- ✅ Audit log verification
- ✅ Permission-based tests (super admin vs regular)

---

## Common Patterns

All services follow this pattern:

```php
class XyzApprovalService
{
    // Request methods (create, update, delete, etc.)
    public static function request*(...)
    {
        // 1. Validate
        // 2. Create ApprovalAuditRequest
        // 3. Log as pending
        // 4. Return request
    }
    
    // Execution methods
    public static function execute*(ApprovalAuditRequest $request, $approver)
    {
        // 1. Get data from payload
        // 2. Validate again
        // 3. Execute action
        // 4. Log as completed
        // 5. Return created/updated/deleted model
    }
}

class XyzAuditService
{
    // Logging methods
    public static function log*(...)
    {
        // 1. Build description
        // 2. Call AuditService::log()
        // 3. Return AuditLog
    }
}
```

---

## Summary

| Service | Type | Status | Priority |
|---------|------|--------|----------|
| EmployeeApprovalService | Approval | ❌ Missing | 🔴 Critical |
| EmployeeAuditService | Audit | ❌ Missing | 🔴 Critical |
| PayrollApprovalService | Approval | ❌ Missing | 🔴 Critical |
| PayrollAuditService | Audit | ❌ Missing | 🔴 Critical |
| DepartmentApprovalService | Approval | ❌ Missing | 🟡 High |
| DepartmentCategoryApprovalService | Approval | ❌ Missing | 🟡 High |
| CallbackApprovalService | Approval | ❌ Missing | 🟡 High |
| PurchaseAuditService | Audit | ❌ Missing | 🟡 High |
| ShiftAuditService | Audit | ❌ Missing | 🟢 Medium |
| LeaveAuditService | Audit | ❌ Missing | 🔴 Critical |

**Total Lines to Code:** ~2,000-2,500 lines
