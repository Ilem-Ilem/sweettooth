# Sync Audit - Quick Reference

## In Your Livewire Component

### Step 1: Add Trait
```php
use App\Traits\AuditableSyncTrait;

class MyComponent extends BaseComponent
{
    use AuditableSyncTrait;
    // ...
}
```

### Step 2: Use in Save Method

**Simple (Auto-approved):**
```php
$this->syncWithAudit(
    $model,
    'roles',
    $selectedRoles,
    'Description of what was changed'
);
```

**With Approval Workflow:**
```php
$result = $this->syncWithAuditRequiringApproval(
    $model,
    'roles',
    $selectedRoles,
    'Description',
    true  // requires approval
);

if ($result['status'] === 'pending') {
    $this->toast()->info('Pending approval')->send();
}
```

**Multiple Relationships:**
```php
$this->syncMultipleWithAudit(
    $model,
    [
        'roles' => $selectedRoles,
        'permissions' => $selectedPermissions,
    ],
    'Bulk update description'
);
```

## In AuditManagement Component

The system automatically handles approval and execution:

1. User requests sync → Creates ApprovalAuditRequest
2. Admin reviews in Audit Management → Approvals tab
3. Admin clicks "Approve" → System executes sync automatically
4. Sync operation is logged in AuditLog

## What Gets Logged

```
AuditLog:
  action: 'sync_roles'
  description: 'Your description'
  old_values: [2, 3, 4]           (Previous IDs)
  new_values: [1, 2, 3]           (New IDs)
  details: {
    attached: [1],                (New IDs)
    detached: [4],                (Removed IDs)
    updated: [2, 3],              (Unchanged)
    relationship: 'roles',
    causer: 'App\Models\Employee',
    auditable: 'App\Models\Employee'
  }
```

## Supported Relationships

Any many-to-many relationship with a `sync()` method:

- Employee → Roles: `$employee->roles()`
- Employee → Permissions: `$employee->permissions()`
- Role → Permissions: `$role->permissions()`
- User → Roles: `$user->roles()`
- Custom pivot tables with `sync()`

## With Pivot Attributes

If your relationship has pivot columns:

```php
$this->syncWithAudit(
    $employee,
    'roles',
    [
        1 => ['assigned_at' => now()],
        2 => ['assigned_at' => now()],
    ],
    'Assigned roles with dates'
);
```

## Error Handling

```php
try {
    $this->syncWithAudit($model, 'roles', $roles, 'Description');
} catch (\Exception $e) {
    $this->toast()->error("Failed: {$e->getMessage()}")->send();
}
```

## Making Operations Require Approval

In `AuditService.php`, modify the `actionRequiresApproval()` method:

```php
protected static function actionRequiresApproval(Model $causer, string $action): bool
{
    $sensitiveActions = [
        'sync_roles',           // ← Add this
        'sync_permissions',     // ← Add this
        // ... other actions
    ];

    return in_array($action, $sensitiveActions);
}
```

## Viewing Audit Logs

Navigate to: **Audit Management → Logs tab**

Filter by:
- Action: `sync_roles`, `sync_permissions`, etc.
- Department
- Date range

Click on log entry to see:
- What was attached/detached
- Previous vs new values
- Who made the change
- When it happened

## Examples

### Employee Role Assignment
```php
// In EmployeeModule/Edit.php
use App\Traits\AuditableSyncTrait;

class Edit extends BaseComponent
{
    use AuditableSyncTrait;
    
    public array $selectedRoles = [];

    public function saveEmployee()
    {
        $employee = Employee::find($this->employeeId);
        $employee->update([/* fields */]);
        
        // ← This one line logs and syncs roles
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            "Updated roles for {$employee->name}"
        );
    }
}
```

### Approval Required (Admin Roles Only)
```php
// In SuperAdmin/EmployeeModule/Edit.php
$result = $this->syncWithAuditRequiringApproval(
    $employee,
    'roles',
    $this->selectedRoles,
    'Assigning admin roles',
    true  // Always require approval
);
```

### Permission Sync
```php
$this->syncWithAudit(
    $role,
    'permissions',
    $this->selectedPermissions,
    "Updated permissions for role {$role->name}"
);
```

## Relationship Method Names

The system tries both of these:
- Direct: `$model->relationship()`
- Dynamic: `$model->$relationship()`

So these all work:
```php
$this->syncWithAudit($user, 'roles', [...]);      // $user->roles()
$this->syncWithAudit($user, 'permissions', [...]);// $user->permissions()
$this->syncWithAudit($role, 'permissions', [...]);// $role->permissions()
```

## Files Modified

- `app/Services/AuditService.php` - Added `logSync()` method
- `app/Traits/AuditableSyncTrait.php` - New trait (provides methods)
- `app/Livewire/BranchDashboard/AuditManagement/Index.php` - Added sync approval handling
- `app/Livewire/BranchDashboard/EmployeeModule/Edit.php` - Example implementation

## Documentation

See `MDs/SyncAudit.md` for complete documentation with:
- Detailed usage examples
- Architecture explanation
- Approval workflow details
- Best practices
- Troubleshooting
