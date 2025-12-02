# Employee Module - Audit Logging Implementation Guide

## Quick Overview
This guide provides step-by-step instructions to add missing audit logging to the Employee Module.

**Total Audit Gaps:** 9 critical + 5 important logging points
**Estimated Implementation Time:** 2-3 hours

---

## Part 1: Leave Management Audit (3 Critical Points)

### 1.1 Leave Application Creation

**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ApplyLeave.php`

**Location:** Line 215 (after successful creation)

**Current Code:**
```php
$leaveApplication = LeaveApplication::create([
    'application_number' => LeaveApplication::generateApplicationNumber(),
    'employee_id' => $employee->id,
    // ... other fields ...
]);

$this->toast()->success("Leave application {$leaveApplication->application_number} submitted successfully!")->send();
```

**Add After Creation:**
```php
// Log the leave application creation
AuditService::log(
    $employee,  // The employee applying for leave
    'create',
    $leaveApplication,
    "Submitted leave application: {$this->selected_leave_type->name} from {$this->start_date} to {$this->end_date} ({$this->total_days} days)",
    'pending'
);
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

### 1.2 Leave Allocation

**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ManageAllocations.php`

**Location:** Line 135 (inside the loop)

**Current Code:**
```php
foreach ($this->allocations as $allocation) {
    if ($allocation['allocated_days'] > 0) {
        EmployeeLeaveAllocation::allocateToEmployee(
            $this->selectedEmployeeId,
            $allocation['leave_type_id'],
            $this->selectedYear,
            $allocation['allocated_days'],
            $allocator->id,
            $allocation['notes']
        );
    }
}
```

**Modify to Log:**
```php
foreach ($this->allocations as $allocation) {
    if ($allocation['allocated_days'] > 0) {
        $allocationRecord = EmployeeLeaveAllocation::allocateToEmployee(
            $this->selectedEmployeeId,
            $allocation['leave_type_id'],
            $this->selectedYear,
            $allocation['allocated_days'],
            $allocator->id,
            $allocation['notes']
        );

        // Log the allocation
        AuditService::log(
            $allocator,
            'create',
            $allocationRecord,
            "Allocated {$allocation['allocated_days']} days of {$allocation['leave_type_name']} " .
            "to {$this->selectedEmployee->name} for {$this->selectedYear}. Notes: {$allocation['notes']}",
            'completed'
        );
    }
}
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

### 1.3 Leave Cancellation

**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/MyLeaves.php`

**Location:** Line 135 (after cancellation)

**Current Code:**
```php
$employee = auth('employees')->user();
$leave->cancel($employee->id, $this->cancellation_reason);

$this->toast()->success('Leave application cancelled successfully.')->send();
```

**Add After Cancellation:**
```php
$employee = auth('employees')->user();
$leave->cancel($employee->id, $this->cancellation_reason);

// Log the cancellation
AuditService::log(
    $employee,
    'cancel',
    $leave,
    "Cancelled leave application {$leave->application_number}. Reason: {$this->cancellation_reason}",
    'completed'
);

$this->toast()->success('Leave application cancelled successfully.')->send();
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

## Part 2: Role/Permission Audit (4 Critical Points)

### 2.1 Role Creation

**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Location:** Line 180-185 (when creating new role)

**Current Code:**
```php
$role = Role::create([
    'name' => $this->roleName,
    'guard_name' => $this->roleGuard,
]);
$message = 'Role created successfully!';
```

**Modify to Add Audit:**
```php
$role = Role::create([
    'name' => $this->roleName,
    'guard_name' => $this->roleGuard,
]);

// Log the role creation
$permissionNames = Permission::whereIn('id', $this->selectedPermissions)
    ->pluck('name')
    ->implode(', ');

AuditService::log(
    current_actor(),
    'create',
    $role,
    "Created role '{$this->roleName}' with guard '{$this->roleGuard}'. Permissions: {$permissionNames}",
    'completed'
);

$message = 'Role created successfully!';
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

### 2.2 Role Update

**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Location:** Line 172-178 (when editing existing role)

**Current Code:**
```php
if ($this->isEditing && $this->selectedRoleId) {
    $role = Role::findOrFail($this->selectedRoleId);
    $role->update([
        'name' => $this->roleName,
        'guard_name' => $this->roleGuard,
    ]);
    $message = 'Role updated successfully!';
}
```

**Modify to Add Audit:**
```php
if ($this->isEditing && $this->selectedRoleId) {
    $role = Role::findOrFail($this->selectedRoleId);
    $oldName = $role->name;
    $oldGuard = $role->guard_name;
    
    $role->update([
        'name' => $this->roleName,
        'guard_name' => $this->roleGuard,
    ]);

    // Log the role update
    $changes = [];
    if ($oldName !== $this->roleName) {
        $changes[] = "Name: {$oldName} → {$this->roleName}";
    }
    if ($oldGuard !== $this->roleGuard) {
        $changes[] = "Guard: {$oldGuard} → {$this->roleGuard}";
    }

    AuditService::log(
        current_actor(),
        'update',
        $role,
        "Updated role. Changes: " . implode(', ', $changes),
        'completed'
    );

    $message = 'Role updated successfully!';
}
```

---

### 2.3 Role Deletion

**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Location:** Line 209-215 (confirmedDeleteRole method)

**Current Code:**
```php
public function confirmedDeleteRole(string $message): void
{
    if ($this->selectedRoleId) {
        Role::findOrFail($this->selectedRoleId)->delete();
        $this->dialog()->success('Success', 'Role deleted successfully!')->send();
        $this->selectedRoleId = null;
    }
}
```

**Improve to Add Validation & Audit:**
```php
public function confirmedDeleteRole(string $message): void
{
    if ($this->selectedRoleId) {
        $role = Role::findOrFail($this->selectedRoleId);
        
        // Validation: Check if role is assigned to employees
        if ($role->users()->count() > 0) {
            $this->dialog()->error('Error', 'Cannot delete role assigned to ' . $role->users()->count() . ' employee(s)')->send();
            return;
        }

        // Log the deletion
        AuditService::log(
            current_actor(),
            'delete',
            $role,
            "Deleted role '{$role->name}' with guard '{$role->guard_name}'",
            'completed'
        );

        $role->delete();
        $this->dialog()->success('Success', 'Role deleted successfully!')->send();
        $this->selectedRoleId = null;
    }
}
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

### 2.4 Permission Creation

**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Locations:** 
- Line 287-290 (createPermission)
- Line 321-324 (createStandalonePermission)

**Current Code (createPermission):**
```php
$permission = Permission::create([
    'name' => $this->permissionName,
    'guard_name' => $this->permissionGuard,
]);

$this->selectedPermissions[] = $permission->id;

$this->toast()->success('Permission created and added to role!')->send();
```

**Modify to Add Audit:**
```php
$permission = Permission::create([
    'name' => $this->permissionName,
    'guard_name' => $this->permissionGuard,
]);

// Log permission creation
AuditService::log(
    current_actor(),
    'create',
    $permission,
    "Created permission '{$this->permissionName}' with guard '{$this->permissionGuard}'",
    'completed'
);

$this->selectedPermissions[] = $permission->id;

$this->toast()->success('Permission created and added to role!')->send();
```

**Current Code (createStandalonePermission):**
```php
Permission::create([
    'name' => $this->standalonePermissionName,
    'guard_name' => $this->standalonePermissionGuard,
]);

$this->toast()->success('Permission created successfully!')->send();
```

**Modify to Add Audit:**
```php
$permission = Permission::create([
    'name' => $this->standalonePermissionName,
    'guard_name' => $this->standalonePermissionGuard,
]);

// Log permission creation
AuditService::log(
    current_actor(),
    'create',
    $permission,
    "Created standalone permission '{$this->standalonePermissionName}' with guard '{$this->standalonePermissionGuard}'",
    'completed'
);

$this->toast()->success('Permission created successfully!')->send();
```

**Imports Needed:**
```php
use App\Services\AuditService;
```

---

## Part 3: Implement Validation for Role Deletion

**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Location:** Line 198 (deleteRole method)

**Current Code:**
```php
public function deleteRole($roleId)
{
    $this->selectedRoleId = $roleId;

    $this->dialog()
        ->question('Warning!', 'Are you sure you want to delete this role?')
        ->confirm('Confirm', 'confirmedDeleteRole', 'Confirmed Successfully')
        ->cancel('Cancel', 'cancelledDeleteRole', 'Cancelled Successfully')
        ->send();
}
```

**Improve to Validate First:**
```php
public function deleteRole($roleId)
{
    $role = Role::find($roleId);
    
    if (!$role) {
        $this->toast()->error('Role not found.')->send();
        return;
    }
    
    // Check if role has assigned employees
    if ($role->users()->count() > 0) {
        $this->toast()
            ->error("Cannot delete role assigned to {$role->users()->count()} employee(s). " .
                    "Remove the role from all employees first.")
            ->send();
        return;
    }

    $this->selectedRoleId = $roleId;

    $this->dialog()
        ->question('Warning!', 'Are you sure you want to delete this role?')
        ->confirm('Confirm', 'confirmedDeleteRole', 'Confirmed Successfully')
        ->cancel('Cancel', 'cancelledDeleteRole', 'Cancelled Successfully')
        ->send();
}
```

---

## Part 4: Testing Your Changes

### Test Leave Application Audit
```bash
# Create a test leave application
php artisan tinker

# Check audit log was created
>>> $logs = DB::table('audit_logs')
             ->where('action', 'create')
             ->where('auditable_type', 'App\Models\LeaveApplication')
             ->latest()
             ->get();
>>> $logs->first();
```

### Test Role Creation Audit
```bash
# Navigate to Role Management
# Create a new role
# Check audit log appears in audit trail

# Or via tinker:
>>> $logs = DB::table('audit_logs')
             ->where('action', 'create')
             ->where('auditable_type', 'Spatie\Permission\Models\Role')
             ->latest()
             ->get();
```

---

## Part 5: Verification Checklist

After implementing all changes, verify:

- [ ] Leave application creation logs to audit_logs
- [ ] Leave allocation creation logs to audit_logs
- [ ] Leave cancellation logs to audit_logs
- [ ] Role creation logs to audit_logs
- [ ] Role update logs to audit_logs
- [ ] Role deletion logs to audit_logs (with validation)
- [ ] Permission creation logs to audit_logs
- [ ] All logs show correct actor (current_actor())
- [ ] All logs show correct action type
- [ ] Descriptions are clear and include relevant details
- [ ] No errors in browser console
- [ ] No errors in Laravel logs

---

## Reference: AuditService::log() Signature

```php
AuditService::log(
    $actor,           // User performing action (current_actor())
    $action,          // Action type: 'create', 'update', 'delete', 'cancel', etc.
    $model,           // Model instance being audited
    $description,     // Detailed description of what happened
    $status = 'completed'  // Status: 'completed', 'pending', 'failed', etc.
);
```

---

## Implementation Order (Recommended)

1. **Start with:** Leave application creation (simplest)
2. **Then:** Leave allocation and cancellation
3. **Then:** Role creation, update, deletion
4. **Finally:** Permission creation and validation

**Estimated Time per Section:**
- Leave audit: 20-30 minutes
- Role audit: 30-40 minutes
- Validation improvements: 10 minutes
- Testing: 15-20 minutes

**Total: ~2 hours**

---

## Troubleshooting

### Issue: Method `current_actor()` not found
**Solution:** Import or ensure it's a helper function
```php
use function app\Helpers\current_actor;
// or
current_actor() // if it's a global helper
```

### Issue: AuditService class not found
**Solution:** Verify service exists at `app/Services/AuditService.php`
```php
use App\Services\AuditService;
```

### Issue: Model not logging properly
**Solution:** Check that model has correct MorphClass defined (see Employee.php line 29-32)

---

## Related Resources

- Full module documentation: `/MDs/EMPLOYEES/EMPLOYEE_MODULE_SUMMARY.md`
- Leave management details: `/MDs/EMPLOYEES/LEAVE_MANAGEMENT.md`
- Role/Permission details: `/MDs/EMPLOYEES/ROLE_PERMISSION.md`
- Audit system overview: `/MDs/AUDIT_MANAGEMENT_DASHBOARD.md`

---

## Notes

- This guide covers critical audit logging gaps only
- Additional audit points may be added later
- All changes maintain backward compatibility
- No database changes required (schema already fixed)
- Changes follow existing audit patterns in codebase
