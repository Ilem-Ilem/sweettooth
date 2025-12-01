# Sync Operations Audit Guide

## Overview

The Audit Management system now supports logging and approving **sync operations** on many-to-many relationships. This enables complete audit trails for operations like:

- Assigning roles to employees
- Syncing permissions to roles
- Attaching/detaching related models
- Any pivot table synchronization

## Architecture

### Components

1. **AuditService::logSync()** - New method for logging sync operations
2. **AuditableSyncTrait** - Convenient trait for Livewire components
3. **AuditManagement/Index.php** - Updated to handle sync action approval
4. **ApprovalAuditRequest** - Stores pending sync operations for approval

### Data Flow

```
1. Livewire Component calls syncWithAudit() or syncWithAuditRequiringApproval()
2. AuditableSyncTrait captures previous state and sync data
3. AuditService::logSync() creates an AuditLog entry
4. If requires approval, ApprovalAuditRequest is created
5. Admin approves/rejects via AuditManagement/Index
6. Approved sync is executed and logged
```

## Usage Examples

### Basic Sync with Audit (Auto-approved)

```php
use App\Traits\AuditableSyncTrait;

class Edit extends BaseComponent
{
    use AuditableSyncTrait;

    public function saveEmployee()
    {
        $employee = Employee::find($this->employeeId);

        // Sync roles and automatically log the operation
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            "Updated employee roles for {$employee->name}"
        );

        $this->toast()->success('Employee updated!')->send();
    }
}
```

### Sync Requiring Approval

```php
public function saveEmployee()
{
    $employee = Employee::find($this->employeeId);

    // Sync with approval requirement
    $result = $this->syncWithAuditRequiringApproval(
        $employee,
        'roles',
        $this->selectedRoles,
        'Assigning admin roles',
        true  // requiresApproval
    );

    if ($result['status'] === 'pending') {
        $this->toast()->info('Role assignment pending approval')->send();
    } else {
        $this->toast()->success('Roles updated')->send();
    }
}
```

### Sync with Pivot Attributes

```php
public function assignRolesWithDates()
{
    $employee = Employee::find($this->employeeId);

    // Sync roles with pivot attributes
    $this->syncWithAudit(
        $employee,
        'roles',
        [
            1 => ['assigned_at' => now()],
            2 => ['assigned_at' => now()],
        ],
        'Assigned roles with assignment dates'
    );
}
```

### Multiple Relationship Sync (Bulk)

```php
public function updateEmployeeRelationships()
{
    $employee = Employee::find($this->employeeId);

    // Sync multiple relationships in a transaction
    $results = $this->syncMultipleWithAudit(
        $employee,
        [
            'roles' => $this->selectedRoles,
            'permissions' => $this->selectedPermissions,
        ],
        'Bulk update: roles and permissions'
    );

    // $results = [
    //     'roles' => [attached => [...], detached => [...], updated => [...]],
    //     'permissions' => [...],
    // ]
}
```

### Using AuditService Directly

For non-Livewire contexts:

```php
use App\Services\AuditService;

// Log a sync operation
AuditService::logSync(
    $currentUser,                    // Causer
    $employee,                       // Model being modified
    'roles',                         // Relationship name
    [1, 2, 3],                      // New sync data (IDs)
    [2, 3, 4],                      // Previous data (IDs)
    'Admin reassigned user roles',  // Description
    'completed'                      // Status
);

// The service automatically calculates:
// - attached: [1]
// - detached: [4]
// - updated: [2, 3]
```

## AuditLog Format

When a sync operation is logged, the AuditLog record contains:

```php
[
    'action' => 'sync_roles',  // Format: sync_{relationship}
    'description' => 'Updated employee roles for John Doe',
    'old_values' => [2, 3, 4],  // Previous IDs
    'new_values' => [1, 2, 3],  // New IDs
    'details' => [
        'relationship' => 'roles',
        'attached' => [1],           // New items
        'detached' => [4],           // Removed items
        'updated' => [2, 3],         // Unchanged but still present
        'sync_data' => [1, 2, 3],
        'previous_data' => [2, 3, 4],
        'causer' => 'App\Models\Employee',
        'auditable' => 'App\Models\Employee',
    ]
]
```

## Approval Workflow

### Step 1: Request Sync with Approval

```php
$result = $this->syncWithAuditRequiringApproval(
    $employee,
    'roles',
    [1, 2, 3],
    'Assigning admin roles',
    true
);

// Returns:
// [
//     'status' => 'pending',
//     'approval_request' => ApprovalAuditRequest instance,
// ]
```

### Step 2: Approval Request Created

An `ApprovalAuditRequest` record is created with:

```php
[
    'action' => 'sync:App\Models\Employee:roles',
    'status' => 'pending',
    'description' => 'Assigning admin roles',
    'payload' => [
        'id' => 5,
        'sync_data' => [1, 2, 3],
        'previous_data' => [2, 3, 4],
        'roles' => [1, 2, 3],
    ]
]
```

### Step 3: Approve in AuditManagement

Admin navigates to **Audit Management → Approvals** tab and:

1. Reviews the pending sync request
2. Clicks "Approve" button
3. System executes `handleSyncAction()`:
   ```php
   // Parses: sync:App\Models\Employee:roles
   $action = 'sync'
   $model = 'App\Models\Employee'
   $relationship = 'roles'
   
   // Executes:
   $employee->roles()->sync([1, 2, 3]);
   ```
4. Logs the completion in AuditLog

### Step 4: Rejection Workflow

If rejected instead:

1. Status is set to 'rejected'
2. No sync operation is executed
3. Rejection logged in audit trail

## Implementation in Components

### Edit Component (Simple)

```php
<?php
namespace App\Livewire\BranchDashboard\EmployeeModule;

use App\Traits\AuditableSyncTrait;

class Edit extends BaseComponent
{
    use AuditableSyncTrait;
    
    public array $selectedRoles = [];

    public function saveEmployee()
    {
        $employee = Employee::find($this->employeeId);
        
        // 1. Update basic fields
        $employee->update([...]);
        
        // 2. Sync relationships with audit
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            "Updated roles for {$employee->name}"
        );
        
        $this->toast()->success('Saved!')->send();
    }
}
```

### Create Component (With Approval)

```php
class Create extends BaseComponent
{
    use AuditableSyncTrait;
    
    public array $selectedRoles = [];

    public function saveEmployee()
    {
        $employee = Employee::create([...]);
        
        // Create roles with approval requirement
        $result = $this->syncWithAuditRequiringApproval(
            $employee,
            'roles',
            $this->selectedRoles,
            'Creating new employee with roles',
            true  // Always require approval for new admin users
        );
        
        if ($result['status'] === 'pending') {
            $this->toast()->warning('Role assignment pending approval')->send();
        }
    }
}
```

## Audit Log Querying

### Find all role sync operations

```php
$syncLogs = AuditLog::where('action', 'sync_roles')
    ->orderBy('logged_at', 'desc')
    ->get();
```

### Find sync operations for a specific user

```php
$userLogs = AuditLog::where('auditable_type', Employee::class)
    ->where('auditable_id', $employeeId)
    ->where('action', 'like', 'sync_%')
    ->get();
```

### Get attached/detached relationships

```php
$syncLog = AuditLog::find($logId);

$attached = $syncLog->details['attached'];    // New IDs
$detached = $syncLog->details['detached'];    // Removed IDs
$updated = $syncLog->details['updated'];      // Unchanged
```

## Supported Operations

The system supports syncing any relationship that uses:
- `sync()` - Standard sync
- Pivot attributes via `[id => ['col' => 'val']]`
- `syncWithoutDetaching()` - Use with `detach` flag logic
- `attach()` / `detach()` - Can be logged separately

## Configuration

To require approval for sync operations, modify `AuditService::actionRequiresApproval()`:

```php
protected static function actionRequiresApproval(Model $causer, string $action): bool
{
    $sensitiveActions = [
        'sync_roles',           // Require approval for role syncs
        'sync_permissions',     // Require approval for permission syncs
        'sync_sensitive_group', // Custom relationship
    ];

    return in_array($action, $sensitiveActions);
}
```

## Best Practices

1. **Always provide descriptions** - Makes audit logs meaningful
   ```php
   $this->syncWithAudit($user, 'roles', $roles, 'User roles updated per HR request');
   ```

2. **Use transactions** - The trait handles transactions automatically
   ```php
   // These are all transactional:
   $this->syncWithAudit(...);
   $this->syncMultipleWithAudit(...);
   ```

3. **Capture reason for sensitive operations** - Especially if requiring approval
   ```php
   $reason = request('approval_reason') ?? 'Standard update';
   $this->syncWithAuditRequiringApproval($user, 'roles', $roles, $reason, true);
   ```

4. **Handle errors gracefully**
   ```php
   try {
       $this->syncWithAudit($user, 'roles', $roles, $reason);
   } catch (\Exception $e) {
       $this->toast()->error("Failed: {$e->getMessage()}")->send();
   }
   ```

5. **Test sync operations** - Verify attached/detached results
   ```php
   $result = $this->syncWithAudit($user, 'roles', [1, 2]);
   // $result = ['attached' => [...], 'detached' => [...], 'updated' => [...]]
   ```

## Migration Path

If you have existing Livewire components using `$model->syncRoles()` directly:

### Before
```php
$employee->syncRoles($this->selectedRoles);
```

### After
```php
use App\Traits\AuditableSyncTrait;

class Edit extends BaseComponent
{
    use AuditableSyncTrait;
    
    // ... rest of component
    
    public function save()
    {
        $employee = Employee::find($id);
        
        // Just change this one line:
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            'User roles updated'
        );
    }
}
```

## Troubleshooting

### "Failed to sync roles: [error message]"

1. Verify relationship method exists on model
2. Check if method name matches exactly
3. Ensure model uses appropriate Spatie traits

### Sync not appearing in audit logs

1. Verify `AuditableSyncTrait` is used in component
2. Check component is using `syncWithAudit()` method
3. Verify database permissions for audit_logs table

### Approval request not executing

1. Check if relationship name is correct in action string
2. Verify model class name is fully qualified
3. Check sync data format in payload

## See Also

- [Auditflow.md](./Auditflow.md) - Complete audit system documentation
- `AuditService` - Core audit service class
- `ApprovalAuditRequest` - Approval request model
- `AuditLog` - Audit log model
