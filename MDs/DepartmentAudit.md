# Department Module Audit System

## Overview

The Department Module in the Branch Dashboard has a comprehensive audit system that tracks all create, update, and delete operations with different workflows for super admins and employees.

**Key Principle:** Super admins perform actions immediately. Employees must submit for approval.

## Workflows

### Super Admin Workflow

Super admins can immediately create, update, or delete departments without approval.

```
Super Admin Action → Database Updated → Audit Log (completed)
```

**Features:**
- No waiting for approval
- Actions take effect immediately
- All operations logged as "completed"
- Can select branch for new departments
- Full access across all branches

### Employee Workflow

Employees must provide a reason for all department operations, which then requires super admin approval.

```
Employee Action → Reason Modal → Submit → Approval Request → 
Pending Audit Log → Super Admin Approval → Executed
```

**Features:**
- Must provide reason (minimum 5 characters)
- Reason modal appears on save
- Creates ApprovalAuditRequest
- Super admin reviews in Audit Management
- Action only executes after approval

## Component Structure

### CreateOrUpdate Component

Location: `app/Livewire/BranchDashboard/DepartmentModule/Department/CreateOrUpdate.php`

#### Key Methods

**`initiateSave()`** - Entry point for save operation
- Validates form fields
- Routes to appropriate workflow based on user role
- For super admins: proceeds directly to save
- For employees: shows reason modal

**`proceedWithReasonSubmitted()`** - Employee workflow continuation
- Validates reason length (minimum 5 characters)
- Shows helpful error if reason too short
- Closes modal and calls `saveDepartment()`

**`saveDepartment()`** - Core save logic
- Validates all input fields
- **Super Admin Path:**
  - Creates/updates department immediately
  - Logs action as "completed"
  - Redirects to department list
- **Employee Path:**
  - Does NOT modify database
  - Creates ApprovalAuditRequest with payload
  - Logs action as "pending"
  - Shows "pending approval" message

#### Properties

```php
// Form fields
public ?string $name;              // Department name
public ?string $category_id;       // Department category
public ?string $description;       // Department description
public ?string $branch_id;         // Selected branch (super admin only)

// Workflow state
public bool $isEditing;            // Create vs Edit mode
public bool $showReasonModal;      // Reason modal visibility
public string $creationReason;     // Reason text (for approval)
```

### Index Component

Location: `app/Livewire/BranchDashboard/DepartmentModule/Index.php`

#### Deletion Workflows

**Single Delete:**

Super Admin:
```
Delete Click → Confirmation Dialog → Confirmed → 
Department Deleted → Completed Audit Log
```

Employee:
```
Delete Click → Reason Modal → Submit → 
ApprovalAuditRequest Created → Pending Audit Log
```

**Bulk Delete:**

Same workflow, but processes multiple departments at once.

#### Key Methods

**`deleteDepartment($departmentId)`** - Initiate deletion
- Shows confirmation dialog for super admins
- Shows reason modal for employees

**`confirmedDeleteDepartment(string $message)`** - Execute deletion
- **Super Admin:** Immediate deletion + completed audit log
- **Employee:** Creates approval request + pending audit log

**`bulkDeleteDepartments()`** - Initiate bulk deletion
- Processes count check
- Routes to appropriate workflow

**`confirmedBulkDelete(string $message)`** - Execute bulk deletion
- **Super Admin:** Logs each deletion, bulk delete query
- **Employee:** Creates approval request per department

## Audit Logging

### AuditService Integration

The system uses `AuditService::log()` for all operations:

```php
AuditService::log(
    $user,                          // Who performed action
    'create'|'update'|'delete',     // Action type
    $department,                    // Model affected (null for pending creates)
    'Reason/description',           // Description
    'completed'|'pending'           // Status
);
```

### Audit Log Records

**Create (Super Admin - Completed)**
```
action: 'create'
status: 'completed'
description: 'Department created by super admin'
auditable_type: App\Models\Department
auditable_id: 1
```

**Create (Employee - Pending)**
```
action: 'create'
status: 'pending'
description: 'Reason provided by employee'
auditable_type: null (no department created yet)
auditable_id: null
```

**Update (Super Admin - Completed)**
```
action: 'update'
status: 'completed'
description: 'Department updated by super admin'
auditable_type: App\Models\Department
auditable_id: 1
old_values: [...previous data...]
new_values: [...updated data...]
```

**Delete (Employee - Pending)**
```
action: 'delete'
status: 'pending'
description: 'Employee provided reason'
auditable_type: App\Models\Department
auditable_id: 1
```

## Approval Request Format

When an employee submits a department operation, an `ApprovalAuditRequest` is created:

```php
ApprovalAuditRequest::create([
    'branch_id' => $branchId,
    'requester_id' => $userId,
    'requester_type' => get_class($user),
    'action' => 'create:App\Models\Department',      // Format: action:ModelClass
    'description' => $creationReason,                 // User-provided reason
    'payload' => [                                    // Department data
        'name' => 'IT Department',
        'category_id' => 1,
        'description' => 'Technology team',
        'branch_id' => 'branch-123',
    ],
    'status' => 'pending',
])
```

## Reason Modal UI

The reason modal is shown to employees on save.

**Features:**
- Reason text area (minimum 5 characters)
- Live character counter
- Submit button enabled only when reason ≥ 5 characters
- Modal title changes based on action (Create/Update)
- Clear error messaging for validation

**Button States:**
- Disabled: `strlen($reason) < 5`
- Enabled: `strlen($reason) >= 5`

## Branch Security

**Super Admin:**
- Can create departments in any branch
- Branch selector dropdown visible in form

**Employee:**
- Can only create departments in assigned branch
- Branch is pre-filled from URL parameter (`b_id`)
- Cannot change branch
- Delete operations restricted to same branch

## Flow Examples

### Example 1: Super Admin Creates Department

1. Super Admin navigates to "Create Department"
2. Fills form (name, category, description, selects branch)
3. Clicks "Create Department"
4. `initiateSave()` called:
   - Validates form
   - Detects super admin role
   - Calls `saveDepartment()` directly
5. `saveDepartment()` executes:
   - Validates fields
   - Creates Department record
   - Logs as "completed"
   - Redirects to department list
6. Department appears immediately

### Example 2: Employee Creates Department

1. Employee navigates to "Create Department"
2. Fills form (name, category, description, branch is fixed)
3. Clicks "Create Department"
4. `initiateSave()` called:
   - Validates form
   - Detects employee role
   - Shows reason modal
5. Employee enters reason "We need this for new project"
6. Modal shows reason count: "28/5 minimum characters required"
7. Clicks "Submit Request"
8. `proceedWithReasonSubmitted()` called:
   - Validates reason length
   - Closes modal
   - Calls `saveDepartment()`
9. `saveDepartment()` executes:
   - Validates fields
   - Does NOT create department
   - Creates ApprovalAuditRequest with payload
   - Logs as "pending"
   - Shows "Department creation request submitted for approval!"
10. Super admin sees request in Audit Management
11. Super admin approves
12. AuditManagement creates department + logs completion

### Example 3: Employee Deletes Department

1. Employee navigates to department list
2. Clicks delete icon on department row
3. `deleteDepartment($id)` called:
   - Detects employee role
   - Shows reason modal
4. Employee enters reason "This department is redundant"
5. Clicks button
6. `confirmedDeleteDepartment()` called:
   - Validates reason length
   - Creates ApprovalAuditRequest
   - Logs as "pending"
   - Shows "Delete request submitted for approval"
7. Super admin reviews and approves in Audit Management
8. Department is deleted + completed audit log created

## Implementation Checklist

- [x] `CreateOrUpdate.php` has `initiateSave()` method
- [x] `CreateOrUpdate.php` has `proceedWithReasonSubmitted()` method
- [x] `CreateOrUpdate.php` routes super admin directly to save
- [x] `CreateOrUpdate.php` shows reason modal for employees
- [x] `CreateOrUpdate.php` creates ApprovalAuditRequest for employees
- [x] `CreateOrUpdate.php` logs all operations via AuditService
- [x] `Index.php` handles single department deletion
- [x] `Index.php` handles bulk department deletion
- [x] `Index.php` routes super admin to immediate delete
- [x] `Index.php` routes employee to approval workflow
- [x] View shows reason modal with character counter
- [x] View disables submit button until reason meets minimum
- [x] All audit logs properly created

## Testing

### Test Super Admin Create

1. Login as super admin
2. Navigate to Create Department
3. Fill form, click save
4. Verify:
   - Department created immediately
   - Audit log shows "completed"
   - No approval request created
   - Redirected to department list

### Test Employee Create

1. Login as employee
2. Navigate to Create Department
3. Fill form, click "Create Department"
4. Verify:
   - Reason modal appears
   - Character counter shows
   - Can't submit with reason < 5 chars
5. Enter reason, click "Submit Request"
6. Verify:
   - No department created
   - ApprovalAuditRequest created
   - Audit log shows "pending"
   - Message says "pending approval"
7. Login as super admin
8. Go to Audit Management → Approvals
9. See the pending request
10. Click Approve
11. Verify:
    - Department created
    - Audit log shows "completed"
    - Approval request marked "approved"

### Test Super Admin Delete

1. Login as super admin
2. Go to department list
3. Click delete on department
4. Verify:
   - Confirmation dialog appears
   - No reason modal shown
5. Click confirm
6. Verify:
   - Department deleted immediately
   - Audit log shows "completed"

### Test Employee Delete

1. Login as employee
2. Go to department list
3. Click delete on department
4. Verify:
   - Reason modal appears (not confirmation dialog)
5. Enter reason, submit
6. Verify:
   - Department still exists
   - ApprovalAuditRequest created
   - Audit log shows "pending"
7. Super admin approves
8. Verify:
   - Department deleted
   - Audit log shows "completed"

## Troubleshooting

### Reason Modal Not Appearing

- Check: User role is not super admin
- Check: Component property `showReasonModal` is true
- Check: View includes the modal HTML

### Department Created Without Approval

- Check: `is_super_admin()` is returning false for employee
- Check: Employee is getting to `saveDepartment()` directly
- Check: No ApprovalAuditRequest is being created

### Audit Log Shows Wrong Status

- Verify: `AuditService::log()` called with correct status
- Check: 'completed' for super admin, 'pending' for employee

## See Also

- [Auditflow.md](./Auditflow.md) - Complete audit system
- `AuditService` - Core audit service
- `ApprovalAuditRequest` - Approval request model
- `AuditLog` - Audit log model
- `CreateOrUpdate.php` - Department create/update component
- `Index.php` - Department list/delete component
