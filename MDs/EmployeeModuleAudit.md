# Employee Module - Comprehensive Audit System Implementation

## Overview

The Employee Module implements a complete audit system for all employee operations: **Create**, **Update**, **Read**, **Delete**, and **Role Sync**. The system differentiates between super admin (immediate execution) and employee (approval-required) workflows.

**Principle:** Super admins can perform all actions immediately. Employees must provide reasons for sensitive operations and wait for super admin approval.

## Module Structure

```
BranchDashboard/EmployeeModule/
├── Create.php          # Create new employees
├── Edit.php            # Update employee details
├── Index.php           # List, delete, assign roles
└── Details.php         # View employee details
```

## File-by-File Implementation

### 1. Create.php - Employee Creation Workflow

**Location:** `app/Livewire/BranchDashboard/EmployeeModule/Create.php`

#### Current State
- Creates employees with roles directly
- No audit logging
- No approval workflow
- Syncs roles immediately

#### Implementation Changes Required

##### Step 1: Add Imports and Trait

```php
// Add to imports
use App\Services\AuditService;
use App\Models\ApprovalAuditRequest;
use App\Traits\AuditableSyncTrait;

// Add to class
class Create extends BaseComponent
{
    use WithFileUploads, AuditableSyncTrait;
```

##### Step 2: Add Workflow State Properties

```php
public class Create extends BaseComponent
{
    // ... existing properties ...
    
    // Reason modal state for employees
    public bool $showCreationReasonModal = false;
    public string $creationReason = '';
    public bool $creatingEmployee = false; // Flag to prevent double-click
}
```

##### Step 3: Add initiateSave() Method

```php
/**
 * Initiate employee creation workflow
 * 
 * For super admins: Proceeds directly to save
 * For employees: Shows reason modal first
 */
public function initiateSave()
{
    // Validate form first
    try {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            // ... other validations ...
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $this->dispatch('notify', message: 'Please fix validation errors', type: 'error');
        return;
    }

    if (is_super_admin()) {
        // Super admin: proceed directly
        $this->saveEmployee();
    } else {
        // Employee: show reason modal
        $this->showCreationReasonModal = true;
    }
}
```

##### Step 4: Add Reason Modal Methods

```php
/**
 * Close the reason modal
 */
public function closeCreationReasonModal()
{
    $this->showCreationReasonModal = false;
    $this->creationReason = '';
}

/**
 * Proceed with creation after providing reason
 */
public function proceedWithCreationReason()
{
    if (strlen($this->creationReason) < 5) {
        $this->toast()->error('Reason must be at least 5 characters long')->send();
        return;
    }
    
    $this->showCreationReasonModal = false;
    $this->saveEmployee();
}
```

##### Step 5: Update saveEmployee() Method

```php
public function saveEmployee()
{
    if ($this->creatingEmployee) return; // Prevent double-click
    
    $this->creatingEmployee = true;

    try {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            // ... existing validations ...
            'creationReason' => is_super_admin() ? 'nullable|string' : 'required|string|min:5',
        ]);

        $data = [
            'branch_id' => $this->b_id,
            'department_id' => $this->department_id,
            // ... other data ...
        ];

        $user = current_actor();

        if (!is_super_admin()) {
            // EMPLOYEE: Create approval request (don't create employee yet)
            $approvalRequest = ApprovalAuditRequest::create([
                'branch_id' => $this->b_id,
                'requester_id' => $user->id,
                'requester_type' => get_class($user),
                'action' => 'create:' . Employee::class,
                'description' => $this->creationReason,
                'payload' => array_merge($data, ['selectedRoles' => $this->selectedRoles]),
                'status' => 'pending',
            ]);

            // Log as pending
            AuditService::log(
                $user,
                'create',
                null,
                $this->creationReason,
                'pending'
            );

            $this->toast()->success('Employee creation request submitted for approval!')->send();
            $this->redirectRoute('branch-dashboard.employee.index', ['b_id' => $this->b_id]);
            return;
        }

        // SUPER ADMIN: Create immediately
        $employee = Employee::create($data);

        // Sync roles
        if (!empty($this->selectedRoles)) {
            $this->syncWithAudit(
                $employee,
                'roles',
                $this->selectedRoles,
                "Created employee {$employee->name} with roles"
            );
        }

        // Log as completed
        AuditService::log(
            $user,
            'create',
            $employee,
            'Employee created by super admin',
            'completed'
        );

        $this->toast()->success('Employee created successfully!')->send();
        $this->redirectRoute('branch-dashboard.employee.index', ['b_id' => $this->b_id]);

    } finally {
        $this->creatingEmployee = false;
    }
}
```

### 2. Edit.php - Employee Update Workflow

**Location:** `app/Livewire/BranchDashboard/EmployeeModule/Edit.php`

#### Current State
- Updates employee with role sync
- Already uses `AuditableSyncTrait` (partially implemented)
- No approval workflow
- No reason modal

#### Implementation Changes Required

##### Step 1: Add Reason Modal State

```php
public class Edit extends BaseComponent
{
    use WithFileUploads, AuditableSyncTrait;
    
    // ... existing properties ...
    
    // Reason modal state
    public bool $showUpdateReasonModal = false;
    public string $updateReason = '';
    public bool $updatingEmployee = false;
}
```

##### Step 2: Add initiateSave() Method

```php
/**
 * Initiate employee update workflow
 */
public function initiateSave()
{
    // Validate form first
    try {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employeeId,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeId,
            // ... other validations ...
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return;
    }

    if (is_super_admin()) {
        // Super admin: proceed directly
        $this->saveEmployee();
    } else {
        // Employee: show reason modal
        $this->showUpdateReasonModal = true;
    }
}
```

##### Step 3: Add Reason Modal Methods

```php
public function closeUpdateReasonModal()
{
    $this->showUpdateReasonModal = false;
    $this->updateReason = '';
}

public function proceedWithUpdateReason()
{
    if (strlen($this->updateReason) < 5) {
        $this->toast()->error('Reason must be at least 5 characters long')->send();
        return;
    }
    
    $this->showUpdateReasonModal = false;
    $this->saveEmployee();
}
```

##### Step 4: Update saveEmployee() Method

```php
public function saveEmployee()
{
    if ($this->updatingEmployee) return;
    
    $this->updatingEmployee = true;

    try {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employeeId,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeId,
            // ... existing validations ...
            'updateReason' => is_super_admin() ? 'nullable|string' : 'required|string|min:5',
        ]);

        $employee = Employee::findOrFail($this->employeeId);

        $data = [
            'branch_id' => $this->b_id,
            'department_id' => $this->department_id,
            // ... other data ...
        ];

        $user = current_actor();

        if (!is_super_admin()) {
            // EMPLOYEE: Create approval request
            $approvalRequest = ApprovalAuditRequest::create([
                'branch_id' => $this->b_id,
                'requester_id' => $user->id,
                'requester_type' => get_class($user),
                'action' => 'update:' . Employee::class . ':' . $this->employeeId,
                'description' => $this->updateReason,
                'payload' => array_merge($data, ['selectedRoles' => $this->selectedRoles]),
                'status' => 'pending',
            ]);

            // Log as pending
            AuditService::log(
                $user,
                'update',
                $employee,
                $this->updateReason,
                'pending'
            );

            $this->toast()->success('Employee update request submitted for approval!')->send();
            $this->redirectRoute('branch-dashboard.employees.index', ['b_id' => $this->b_id]);
            return;
        }

        // SUPER ADMIN: Update immediately
        $employee->update($data);

        // Sync roles with audit
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            "Updated employee {$employee->name} - roles changed"
        );

        // Log as completed
        AuditService::log(
            $user,
            'update',
            $employee,
            'Employee updated by super admin',
            'completed'
        );

        $this->toast()->success('Employee updated successfully!')->send();
        $this->redirectRoute('branch-dashboard.employees.index', ['b_id' => $this->b_id]);

    } finally {
        $this->updatingEmployee = false;
    }
}
```

### 3. Index.php - Employee List, Delete, and Role Sync

**Location:** `app/Livewire/BranchDashboard/EmployeeModule/Index.php`

#### Current State
- Lists employees with pagination and filtering
- Deletes employees (no audit)
- Assigns roles (no audit)
- No approval workflow

#### Implementation Changes Required

##### Step 1: Add Imports and Trait

```php
use App\Services\AuditService;
use App\Models\ApprovalAuditRequest;
use App\Traits\AuditableSyncTrait;

class Index extends BaseComponent
{
    use AuditableSyncTrait;
```

##### Step 2: Add Delete Workflow State

```php
public class Index extends BaseComponent
{
    // ... existing properties ...
    
    // Delete reason modal state
    public bool $showDeleteReasonModal = false;
    public string $deleteReason = '';
    
    // Role assignment with reason modal
    public bool $showRoleReasonModal = false;
    public ?string $employeeIdForRoleReason = null;
    public array $pendingRoles = [];
    public string $roleChangeReason = '';
}
```

##### Step 3: Update deleteEmployee() Method

```php
/**
 * Initiate employee deletion
 */
public function deleteEmployee($employeeId): void
{
    $this->selectedEmployeeId = $employeeId;

    if (is_super_admin()) {
        // Super admin: show confirmation dialog
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this employee?')
            ->confirm('Confirm', 'confirmedDeleteEmployee', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDeleteEmployee', 'Cancelled Successfully')
            ->send();
    } else {
        // Employee: show reason modal
        $this->showDeleteReasonModal = true;
    }
}
```

##### Step 4: Update confirmedDeleteEmployee() Method

```python
/**
 * Process confirmed employee deletion
 */
public function confirmedDeleteEmployee(string $message): void
{
    if (!$this->selectedEmployeeId) {
        return;
    }

    $employee = Employee::findOrFail($this->selectedEmployeeId);
    $user = current_actor();

    if (is_super_admin()) {
        // SUPER ADMIN: Delete immediately
        // Log as completed
        AuditService::log(
            $user,
            'delete',
            $employee,
            'Employee deleted by super admin',
            'completed'
        );

        // Delete employee
        $employee->delete();
        $this->dialog()->success('Success', 'Employee deleted successfully!')->send();
    } else {
        // EMPLOYEE: Requires approval
        // Validate reason length
        if (strlen($this->deleteReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }

        // Create approval request
        ApprovalAuditRequest::create([
            'branch_id' => $this->b_id,
            'requester_id' => $user->id,
            'requester_type' => get_class($user),
            'action' => 'delete:' . Employee::class . ':' . $this->selectedEmployeeId,
            'description' => $this->deleteReason,
            'payload' => $employee->toArray(),
            'status' => 'pending',
        ]);

        // Log as pending
        AuditService::log(
            $user,
            'delete',
            $employee,
            $this->deleteReason,
            'pending'
        );

        $this->toast()->success('Employee deletion request submitted for approval!')->send();
    }

    $this->selectedEmployeeId = null;
    $this->deleteReason = '';
    $this->showDeleteReasonModal = false;
}
```

##### Step 5: Add Bulk Delete Support

```php
/**
 * Initiate bulk employee deletion
 */
public function bulkDeleteEmployees(): void
{
    if (is_super_admin()) {
        // Super admin: confirmation dialog
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' employee(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    } else {
        // Employee: reason modal
        $this->showDeleteReasonModal = true;
    }
}

/**
 * Process confirmed bulk delete
 */
public function confirmedBulkDelete(string $message): void
{
    $user = current_actor();

    if (is_super_admin()) {
        // SUPER ADMIN: Delete immediately
        foreach ($this->selectedIds as $id) {
            $employee = Employee::find($id);
            if ($employee) {
                AuditService::log(
                    $user,
                    'delete',
                    $employee,
                    'Employee deleted by super admin (bulk)',
                    'completed'
                );
            }
        }

        Employee::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' employee(s) deleted successfully!')->send();
    } else {
        // EMPLOYEE: Requires approval
        if (strlen($this->deleteReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }

        foreach ($this->selectedIds as $id) {
            $employee = Employee::find($id);
            if ($employee && $employee->branch_id == $this->b_id) {
                ApprovalAuditRequest::create([
                    'branch_id' => $this->b_id,
                    'requester_id' => $user->id,
                    'requester_type' => get_class($user),
                    'action' => 'delete:' . Employee::class . ':' . $id,
                    'description' => $this->deleteReason,
                    'payload' => $employee->toArray(),
                    'status' => 'pending',
                ]);

                AuditService::log(
                    $user,
                    'delete',
                    $employee,
                    $this->deleteReason,
                    'pending'
                );
            }
        }

        $this->toast()->success(count($this->selectedIds) . ' employee deletion request(s) submitted for approval!')->send();
    }

    $this->selectedIds = [];
    $this->deleteReason = '';
    $this->showDeleteReasonModal = false;
}
```

##### Step 6: Update Role Assignment with Audit

```php
/**
 * Open role assignment modal with reason (for employees)
 */
public function openRoleModal($employeeId): void
{
    $this->employeeIdForRole = $employeeId;
    $employee = Employee::find($employeeId);
    $this->selectedRoles = $employee ? $employee->roles->pluck('name')->toArray() : [];
    
    if (is_super_admin()) {
        // Super admin: show modal without reason
        $this->showRoleModal = true;
    } else {
        // Employee: show reason modal first
        $this->employeeIdForRoleReason = $employeeId;
        $this->pendingRoles = $this->selectedRoles;
        $this->showRoleReasonModal = true;
    }
}

/**
 * Close role reason modal
 */
public function closeRoleReasonModal(): void
{
    $this->showRoleReasonModal = false;
    $this->employeeIdForRoleReason = null;
    $this->roleChangeReason = '';
    $this->pendingRoles = [];
}

/**
 * Proceed with role assignment after reason submission (employee)
 */
public function proceedWithRoleReason(): void
{
    if (strlen($this->roleChangeReason) < 5) {
        $this->toast()->error('Reason must be at least 5 characters long')->send();
        return;
    }

    if (!$this->employeeIdForRoleReason) {
        return;
    }

    $employee = Employee::findOrFail($this->employeeIdForRoleReason);
    $user = current_actor();

    // Create approval request for role change
    ApprovalAuditRequest::create([
        'branch_id' => $this->b_id,
        'requester_id' => $user->id,
        'requester_type' => get_class($user),
        'action' => 'sync:' . Employee::class . ':roles',
        'description' => $this->roleChangeReason,
        'payload' => [
            'id' => $employee->id,
            'sync_data' => $this->pendingRoles,
            'roles' => $this->pendingRoles,
        ],
        'status' => 'pending',
    ]);

    // Log as pending
    AuditService::logSync(
        $user,
        $employee,
        'roles',
        $this->pendingRoles,
        $employee->roles->pluck('id')->toArray(),
        $this->roleChangeReason,
        'pending'
    );

    $this->toast()->success('Role change request submitted for approval!')->send();
    $this->closeRoleReasonModal();
}

/**
 * Save roles (super admin only)
 */
public function saveRoles(): void
{
    if (!$this->employeeIdForRole) {
        return;
    }

    $employee = Employee::findOrFail($this->employeeIdForRole);

    if (is_super_admin()) {
        // SUPER ADMIN: Sync immediately
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            "Updated roles for {$employee->name}"
        );

        $this->toast()->success('Roles updated successfully!')->send();
    } else {
        // EMPLOYEE: Already handled in proceedWithRoleReason()
        $this->toast()->info('Please use the reason modal to request role changes')->send();
    }

    $this->closeRoleModal();
}
```

### 4. Handle Approval in AuditManagement

The `AuditManagement/Index.php` already handles `sync:` operations. For employee creation/update/delete, update the `handleCreateAction`, `handleUpdateAction`, and `handleDeleteAction` methods:

```php
/**
 * Handle update actions for employees
 */
private function handleUpdateAction(string $modelName, ?int $modelId, array $payload)
{
    if ($modelName !== Employee::class || !$modelId) {
        return null;
    }

    $employee = Employee::findOrFail($modelId);
    
    // Get role data from payload
    $roles = $payload['selectedRoles'] ?? [];
    
    // Update basic fields
    $updateData = array_intersect_key($payload, array_flip($employee->getFillable()));
    $employee->update($updateData);

    // Sync roles if provided
    if (!empty($roles)) {
        $employee->syncRoles($roles);
    }

    // Log as completed
    AuditService::log(
        current_actor(),
        'update',
        $employee,
        'Employee update approved and executed',
        'completed'
    );

    return $employee;
}

/**
 * Handle delete actions for employees
 */
private function handleDeleteAction(string $modelName, ?int $modelId)
{
    if ($modelName !== Employee::class || !$modelId) {
        return null;
    }

    $employee = Employee::find($modelId);
    if (!$employee) {
        return null;
    }

    // Log before deletion
    AuditService::log(
        current_actor(),
        'delete',
        $employee,
        'Employee deletion approved and executed',
        'completed'
    );

    // Delete the employee
    $employee->delete();

    return $employee;
}
```

## View Components Required

### Create.blade.php Reason Modal

```blade
<!-- Reason Modal for Non-Super Admins -->
@if(!is_super_admin())
<div class="fixed inset-0 z-50 flex items-center justify-center {{ $showCreationReasonModal ? 'pointer-events-auto' : 'pointer-events-none' }}">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-bold">Create Employee - Reason Required</h3>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4">
            <p class="text-sm text-zinc-600 mb-4">
                Please provide a reason for creating this employee.
            </p>
            <textarea wire:model.live="creationReason" rows="4"
                class="w-full px-4 py-2 border border-zinc-300 rounded-lg"
                placeholder="Explain your reason (minimum 5 characters)..."></textarea>
            <p class="text-xs text-zinc-500 mt-2">
                {{ strlen($creationReason) }}/5 minimum characters required
            </p>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-zinc-200 flex gap-3 justify-end">
            <button type="button" wire:click="closeCreationReasonModal"
                class="px-4 py-2 text-sm bg-zinc-100 hover:bg-zinc-200 rounded-lg">
                Cancel
            </button>
            <button type="button" wire:click="proceedWithCreationReason"
                {{ strlen($creationReason) < 5 ? 'disabled' : '' }}
                class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded-lg">
                Submit Request
            </button>
        </div>
    </div>
</div>
@endif
```

## Complete Workflow Summary

### Super Admin Creating Employee

```
Super Admin Form → initiateSave() → saveEmployee() → 
Create Employee Record → Sync Roles Immediately → 
Completed Audit Log → Redirect
```

### Employee Creating Employee

```
Employee Form → initiateSave() → 
Show Reason Modal → Enter Reason (5+ chars) → 
proceedWithCreationReason() → saveEmployee() → 
Create ApprovalAuditRequest → Pending Audit Log → 
Super Admin Reviews → Approves → 
Employee Created + Roles Synced → Completed Audit Log
```

### Super Admin Deleting Employee

```
Super Admin List → deleteEmployee() → 
Confirmation Dialog → confirmedDeleteEmployee() → 
Delete Employee → Completed Audit Log
```

### Employee Deleting Employee

```
Employee List → deleteEmployee() → 
Reason Modal → Enter Reason → confirmedDeleteEmployee() → 
Create ApprovalAuditRequest → Pending Audit Log → 
Super Admin Reviews → Approves → 
Employee Deleted → Completed Audit Log
```

### Super Admin Changing Roles

```
Super Admin List → openRoleModal() → 
Select Roles → saveRoles() → 
Sync Roles with Audit → Completed Audit Log
```

### Employee Requesting Role Change

```
Employee List → openRoleModal() → 
Show Reason Modal → Enter Reason → proceedWithRoleReason() → 
Create ApprovalAuditRequest (sync action) → Pending Audit Log → 
Super Admin Reviews → Approves → 
Roles Synced → Completed Audit Log
```

## Audit Log Examples

### Employee Creation - Pending
```json
{
  "action": "create",
  "status": "pending",
  "causer_type": "App\\Models\\Employee",
  "causer_id": 3,
  "auditable_type": null,
  "auditable_id": null,
  "description": "Need additional staff for new project"
}
```

### Employee Update - Completed (Super Admin)
```json
{
  "action": "update",
  "status": "completed",
  "causer_type": "App\\Models\\User",
  "causer_id": 1,
  "auditable_type": "App\\Models\\Employee",
  "auditable_id": 15,
  "description": "Employee updated by super admin",
  "old_values": {"salary": 50000},
  "new_values": {"salary": 55000}
}
```

### Role Sync - Pending (Employee)
```json
{
  "action": "sync_roles",
  "status": "pending",
  "causer_type": "App\\Models\\Employee",
  "causer_id": 3,
  "auditable_type": "App\\Models\\Employee",
  "auditable_id": 15,
  "description": "Promote employee to manager",
  "details": {
    "relationship": "roles",
    "attached": ["manager"],
    "detached": [],
    "updated": ["employee"]
  }
}
```

## Security Considerations

1. **Branch Isolation** - Employees can only manage within their branch
2. **Permission Checks** - `is_super_admin()` determines workflow
3. **Reason Validation** - Client and server-side validation
4. **Double-Click Prevention** - `$creatingEmployee` and `$updatingEmployee` flags
5. **Transaction Safety** - All operations wrapped in transactions

## Testing Checklist

- [ ] Super admin create employee - immediate, no modal
- [ ] Employee create employee - shows reason modal
- [ ] Character counter works on all modals
- [ ] Submit button disabled when reason < 5 chars
- [ ] Create request creates ApprovalAuditRequest
- [ ] Create request logged as "pending"
- [ ] Super admin can approve/reject in Audit Management
- [ ] Super admin update - immediate, no modal
- [ ] Employee update - shows reason modal
- [ ] Role sync works with audit (both roles)
- [ ] Bulk delete works for both roles
- [ ] All audit logs created correctly
- [ ] Status field shows completed/pending appropriately

## Implementation Priority

1. **Phase 1:** Add reason modals and basic approval workflow (Create, Update, Delete)
2. **Phase 2:** Implement role sync with audit in Index component
3. **Phase 3:** Update AuditManagement to handle all employee operations
4. **Phase 4:** Test all workflows thoroughly

## Related Files

- `AuditService.php` - Core audit logging
- `ApprovalAuditRequest.php` - Approval request model
- `AuditableSyncTrait.php` - Sync operations trait
- `AuditManagement/Index.php` - Approval handling
- `DepartmentAudit.md` - Similar pattern implementation

