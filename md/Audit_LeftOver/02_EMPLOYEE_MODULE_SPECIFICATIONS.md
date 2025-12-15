# Employee Module - Audit Implementation Specifications

**Priority:** 🔴 CRITICAL  
**Status:** Needs Implementation  
**Affected Files:** 5 components  

---

## Overview

The Employee module currently has **broken audit infrastructure** because:

1. ❌ No `EmployeeApprovalService` exists
2. ❌ No `EmployeeAuditService` exists
3. ⚠️ Uses direct `ApprovalAuditRequest::create()` (fragile)
4. ⚠️ No specialized validation
5. ⚠️ Generic approval handler doesn't validate employee-specific rules

---

## Current Implementation (BROKEN)

### Create.php (Line 333)
```php
ApprovalAuditRequest::create([
    'branch_id' => $this->b_id,
    'requester_id' => $user->id,
    'requester_type' => get_class($user),
    'action' => 'update:' . Employee::class . ':' . $this->employeeId,  // ❌ Wrong action
    'description' => $this->updateReason,
    'payload' => $approvalPayload,
    'status' => 'pending',
]);
```

**Problems:**
- ❌ Uses "update" action for "create" operation
- ❌ No validation of employee data
- ❌ No checks for required fields
- ❌ Doesn't validate role hierarchy
- ❌ Doesn't check salary/permission rules

### Edit.php (Line 314)
Same issue - uses generic handler that doesn't understand employee logic.

### Index.php (Line 303)
Same issue for bulk operations.

### RolePermission/Index.php (Line 270, 339)
```php
ApprovalAuditRequest::create([
    'action' => 'sync:' . Employee::class . ':roles:' . $employeeId,  // ✅ Correct
    // ...
]);

AuditService::log(
    $actor,
    'update_role_permission',
    $employee,
    'Role updated',
    'completed'
);
```

**Problems:**
- ❌ No specialized validation for role changes
- ❌ Doesn't check if roles can be removed
- ❌ Doesn't validate role hierarchy
- ❌ No permission change audit service

---

## What Needs to Be Created

### 1. EmployeeApprovalService (NEW)

**File:** `app/Services/EmployeeApprovalService.php`

**Methods:**

#### requestCreate()
```php
public static function requestCreate(array $employeeData, $reason): ApprovalAuditRequest
{
    // Validate:
    // - Required fields present
    // - Email unique
    // - Phone number valid (if required)
    // - Department exists
    // - Position exists
    
    // Create request with action 'create:employee'
    
    // Log as pending
    
    return $request;
}
```

#### executeCreate()
```php
public static function executeCreate(
    ApprovalAuditRequest $request, 
    $approver
): Employee
{
    // Execute creation from request payload
    // Sync selected roles
    // Log completion
    // Return created employee
}
```

#### requestUpdate()
```php
public static function requestUpdate(
    Employee $employee,
    array $changes,
    $reason,
    array $roleChanges = []
): ApprovalAuditRequest
{
    // Validate:
    // - Employee exists
    // - Changes are valid (not removing required fields)
    // - Salary changes have business logic check
    // - Permissions not being elevated beyond requester
    // - Role changes are valid
    
    // Create request with action 'update:employee:' . $employee->id
    
    // Log as pending
    
    return $request;
}
```

#### executeUpdate()
```php
public static function executeUpdate(
    ApprovalAuditRequest $request,
    $approver
): Employee
{
    // Get employee from payload['id']
    // Update fields
    // Sync roles if changing
    // Log completion
    // Return updated employee
}
```

#### requestDelete()
```php
public static function requestDelete(
    Employee $employee,
    $reason
): ApprovalAuditRequest
{
    // Validate:
    // - Employee not active in critical role
    // - No pending operations
    // - Manager is notified
    
    // Create request with action 'delete:employee:' . $employee->id
    
    // Log as pending
    
    return $request;
}
```

#### executeDelete()
```php
public static function executeDelete(
    ApprovalAuditRequest $request,
    $approver
): bool
{
    // Soft delete the employee
    // Revoke active permissions
    // Log completion
    // Return success
}
```

#### requestRoleSync()
```php
public static function requestRoleSync(
    Employee $employee,
    array $newRoles,
    $reason
): ApprovalAuditRequest
{
    // Validate:
    // - Roles exist
    // - Changes don't create security issue
    // - Hierarchy is respected
    
    // Create request with action 'sync:employee:roles:' . $employee->id
    
    // Log as pending
    
    return $request;
}
```

#### executeRoleSync()
```php
public static function executeRoleSync(
    ApprovalAuditRequest $request,
    $approver
): Employee
{
    // Get employee and new roles
    // Validate no conflicts
    // Sync roles
    // Log completion
    // Return employee
}
```

#### validateEmployeeData()
```php
private static function validateEmployeeData(array $data): bool
{
    // Required fields:
    // - name
    // - email
    // - phone
    // - department_id
    // - position
    
    // Validations:
    // - Email is unique (except self)
    // - Department exists
    // - Salary >= minimum wage
    // - Position is valid
    
    return true; // or throw exception
}
```

---

### 2. EmployeeAuditService (NEW)

**File:** `app/Services/EmployeeAuditService.php`

**Methods:**

#### logEmployeeCreation()
```php
public static function logEmployeeCreation(
    Employee $employee,
    $actor,
    $status = 'completed'
): AuditLog
{
    return AuditService::log(
        $actor,
        'employee:created',
        $employee,
        "Employee {$employee->name} created",
        $status
    );
}
```

#### logEmployeeUpdate()
```php
public static function logEmployeeUpdate(
    Employee $employee,
    array $changes,
    $actor,
    $status = 'completed'
): AuditLog
{
    $description = "Employee {$employee->name} updated: " . 
                   implode(', ', array_keys($changes));
    
    return AuditService::log(
        $actor,
        'employee:updated',
        $employee,
        $description,
        $status,
        null,
        ['changed_fields' => $changes]
    );
}
```

#### logSalaryChange()
```php
public static function logSalaryChange(
    Employee $employee,
    $oldSalary,
    $newSalary,
    $reason,
    $actor,
    $status = 'completed'
): AuditLog
{
    return AuditService::log(
        $actor,
        'employee:salary_changed',
        $employee,
        "Salary changed from {$oldSalary} to {$newSalary}: {$reason}",
        $status
    );
}
```

#### logRoleChange()
```php
public static function logRoleChange(
    Employee $employee,
    array $oldRoles,
    array $newRoles,
    $reason,
    $actor,
    $status = 'completed'
): AuditLog
{
    $added = array_diff($newRoles, $oldRoles);
    $removed = array_diff($oldRoles, $newRoles);
    
    $description = "Roles changed for {$employee->name}: " .
                   (count($added) ? "Added: " . implode(',', $added) . " " : "") .
                   (count($removed) ? "Removed: " . implode(',', $removed) : "");
    
    return AuditService::log(
        $actor,
        'employee:roles_changed',
        $employee,
        $description,
        $status
    );
}
```

#### logPermissionChange()
```php
public static function logPermissionChange(
    Employee $employee,
    $permission,
    $action, // 'granted' or 'revoked'
    $reason,
    $actor
): AuditLog
{
    return AuditService::log(
        $actor,
        'employee:permission_' . $action,
        $employee,
        "Permission '{$permission}' {$action} for {$employee->name}: {$reason}",
        'completed'
    );
}
```

#### logActivationStatusChange()
```php
public static function logActivationStatusChange(
    Employee $employee,
    $wasActive,
    $isActive,
    $reason,
    $actor
): AuditLog
{
    $action = $isActive ? 'activated' : 'deactivated';
    
    return AuditService::log(
        $actor,
        'employee:' . $action,
        $employee,
        "Employee {$employee->name} {$action}: {$reason}",
        'completed'
    );
}
```

---

## Component Updates Required

### Create.php
**Current (Line 333):**
```php
ApprovalAuditRequest::create([
    'branch_id' => $this->b_id,
    'requester_id' => $user->id,
    'requester_type' => get_class($user),
    'action' => 'update:' . Employee::class . ':' . $this->employeeId,  // ❌ WRONG
    'description' => $this->updateReason,
    'payload' => $approvalPayload,
    'status' => 'pending',
]);
```

**Should Be:**
```php
if (!is_super_admin()) {
    EmployeeApprovalService::requestCreate(
        $approvalPayload,
        $this->creationReason  // Add this property to component
    );
} else {
    // Super admin creates immediately
    $employee = Employee::create($employeeData);
    $employee->syncRoles($this->selectedRoles);
    EmployeeAuditService::logEmployeeCreation($employee, $user);
}
```

### Edit.php
**Current (Line 314):**
```php
ApprovalAuditRequest::create([
    'branch_id' => $this->b_id,
    'requester_id' => $user->id,
    'requester_type' => get_class($user),
    'action' => 'update:' . Employee::class . ':' . $this->employeeId,
    'description' => $this->updateReason,
    'payload' => $approvalPayload,
    'status' => 'pending',
]);
```

**Should Be:**
```php
if (!is_super_admin()) {
    EmployeeApprovalService::requestUpdate(
        $employee,
        $changes,
        $this->updateReason,
        ['roles' => $this->selectedRoles]
    );
} else {
    // Super admin updates immediately
    $employee->update($changes);
    $employee->syncRoles($this->selectedRoles);
    EmployeeAuditService::logEmployeeUpdate($employee, $changes, $user);
}
```

### RolePermission/Index.php
**Current (Line 270):**
```php
ApprovalAuditRequest::create([
    'action' => 'sync:' . Employee::class . ':roles:' . $employeeId,
    // ...
]);
```

**Should Be:**
```php
if (!is_super_admin()) {
    EmployeeApprovalService::requestRoleSync(
        $employee,
        $newRoles,
        $this->changeReason
    );
} else {
    $employee->syncRoles($newRoles);
    EmployeeAuditService::logRoleChange(
        $employee,
        $oldRoles,
        $newRoles,
        'Updated by super admin',
        $user
    );
}
```

---

## Approval Handler Integration

### In AuditManagement/Index.php (Line 240)

**Add to match statement:**
```php
$auditable = match ($action) {
    // ... existing cases ...
    'employee' => $this->handleEmployeeAction($request),  // ✅ NEW
    'employee:roles' => EmployeeApprovalService::executoroleSync($request, $this->getApprover()),
    default => throw new \Exception("Unknown action type: {$action}"),
};
```

**Add new method:**
```php
private function handleEmployeeAction(ApprovalAuditRequest $request)
{
    $action = explode(':', $request->action)[0];
    
    return match ($action) {
        'create:employee' => EmployeeApprovalService::executeCreate($request, $this->getApprover()),
        'update:employee' => EmployeeApprovalService::executeUpdate($request, $this->getApprover()),
        'delete:employee' => EmployeeApprovalService::executeDelete($request, $this->getApprover()),
        default => throw new \Exception("Unknown employee action: {$action}"),
    };
}
```

---

## Validation Rules

### For Create:
- ✅ Name required, 3-100 characters
- ✅ Email required, unique, valid format
- ✅ Phone required, valid format
- ✅ Department exists
- ✅ Position exists
- ✅ Salary >= minimum wage (if set)

### For Update:
- ✅ Employee exists
- ✅ Email unique (except self)
- ✅ Salary changes logged separately
- ✅ Cannot remove active status if managing others
- ✅ Role changes validated against hierarchy

### For Delete:
- ✅ Employee exists
- ✅ Not currently managing others
- ✅ No pending operations
- ✅ Proper notification sent

### For Role Sync:
- ✅ All roles exist
- ✅ No circular dependencies
- ✅ Respects role hierarchy
- ✅ Cannot remove "super-admin" role if only admin

---

## Testing Scenarios

1. **Create employee (non-admin):**
   - Request created
   - Status pending
   - Admin approves
   - Employee created with roles

2. **Update employee (non-admin):**
   - Request created
   - Admin approves
   - Employee updated
   - Audit logged

3. **Change roles (non-admin):**
   - Request created
   - Admin approves
   - Roles synced
   - Audit logged

4. **Super admin operations:**
   - All immediate
   - All logged as completed

---

## Data Flow Diagram

```
Employee/Create, Edit, RolePermission Components
    ↓
EmployeeApprovalService::request*()
    ↓
ApprovalAuditRequest created (status: pending)
    ↓
EmployeeAuditService::log() [pending status]
    ↓
User sees success message
    ↓
Manager reviews in AuditManagement
    ↓
Manager approves
    ↓
AuditManagement calls EmployeeApprovalService::execute*()
    ↓
Action executed (create/update/delete/sync)
    ↓
ApprovalAuditRequest status → approved
    ↓
EmployeeAuditService::log() [completed status]
    ↓
Manager sees success message
```

---

## Implementation Checklist

- [ ] Create `EmployeeApprovalService` with all methods
- [ ] Create `EmployeeAuditService` with all methods
- [ ] Update `Create.php` to use service
- [ ] Update `Edit.php` to use service
- [ ] Update `Index.php` for bulk operations
- [ ] Update `RolePermission/Index.php` to use service
- [ ] Update `AuditManagement/Index.php` to handle employee actions
- [ ] Add employee action handler
- [ ] Test create flow
- [ ] Test update flow
- [ ] Test delete flow
- [ ] Test role sync flow
- [ ] Verify audit logs are created
- [ ] Verify approval requests work
