# Audit System - Complete Implementation Guide

## Overview

The Sweet Tooth application now has a comprehensive audit system with support for:

1. **Sync Operations** - Track many-to-many relationship changes (roles, permissions)
2. **Department Management** - Create, update, delete with approval workflows
3. **Two-tier Access Control** - Super admins vs employees
4. **Approval Workflows** - Employees submit for approval, super admins auto-execute

## Components Added/Modified

### New Files

| File | Purpose |
|------|---------|
| `app/Traits/AuditableSyncTrait.php` | Trait for syncing relationships with audit logging |
| `MDs/SyncAudit.md` | Sync operations documentation |
| `MDs/SyncAudit-QuickRef.md` | Sync operations quick reference |
| `MDs/DepartmentAudit.md` | Department module audit documentation |

### Modified Files

| File | Changes |
|------|---------|
| `app/Services/AuditService.php` | Added `logSync()` method + sync actions to sensitive list |
| `app/Livewire/BranchDashboard/AuditManagement/Index.php` | Added sync action handling in `handleSyncAction()` |
| `app/Livewire/BranchDashboard/DepartmentModule/Department/CreateOrUpdate.php` | Added reason modal workflow + `initiateSave()` and `proceedWithReasonSubmitted()` methods |
| `app/Livewire/BranchDashboard/DepartmentModule/Index.php` | Already had audit integration (no changes needed) |
| `app/Livewire/BranchDashboard/EmployeeModule/Edit.php` | Added `AuditableSyncTrait` + uses `syncWithAudit()` |
| `resources/views/livewire/branch-dashboard/department-module/department/create.blade.php` | Enhanced reason modal with character counter |

## Feature Matrix

### Sync Operations (Roles, Permissions, etc.)

| Feature | Implementation | Status |
|---------|-----------------|--------|
| Log sync operations | `AuditService::logSync()` | ✅ |
| Simple sync with audit | `syncWithAudit()` trait method | ✅ |
| Sync with approval | `syncWithAuditRequiringApproval()` trait method | ✅ |
| Bulk sync multiple relationships | `syncMultipleWithAudit()` trait method | ✅ |
| Approval execution | `AuditManagement/handleSyncAction()` | ✅ |
| Track attached/detached | Stored in audit log details | ✅ |

### Department Management

| Feature | Super Admin | Employee |
|---------|------------|----------|
| Create Department | Immediate | Requires approval |
| Update Department | Immediate | Requires approval |
| Delete Department | Immediate | Requires approval |
| Reason/Description | Optional | Required (5+ chars) |
| Modal Workflow | None | Reason modal on save |
| Audit Log Status | "completed" | "pending" |

### Approval System

| Operation | Approval Request Created | Logged As |
|-----------|------------------------|----------|
| Super admin action | No | "completed" |
| Employee action | Yes | "pending" |
| Approval granted | N/A (auto-execute for new) | "completed" |
| Approval denied | N/A | "rejected" |

## Usage Examples

### Using Sync with Audit in Components

**Example 1: Simple role sync (auto-approved)**
```php
use App\Traits\AuditableSyncTrait;

class EmployeeEdit extends BaseComponent
{
    use AuditableSyncTrait;
    
    public function save()
    {
        $employee = Employee::find($id);
        
        $this->syncWithAudit(
            $employee,
            'roles',
            $this->selectedRoles,
            'Updated employee roles'
        );
    }
}
```

**Example 2: Sync requiring approval (conditional)**
```php
public function save()
{
    $employee = Employee::find($id);
    
    $result = $this->syncWithAuditRequiringApproval(
        $employee,
        'roles',
        $this->selectedRoles,
        'Assigning admin roles',
        true  // Always require approval
    );
    
    if ($result['status'] === 'pending') {
        $this->toast()->info('Pending approval')->send();
    }
}
```

### Department Creation/Update Flows

**Super Admin Flow (Automatic)**
```
Super Admin Form → Save → initiateSave() → 
saveDepartment() → Create/Update Database → 
Completed Audit Log → Redirect
```

**Employee Flow (With Approval)**
```
Employee Form → Save → initiateSave() → 
Show Reason Modal → Submit Reason → 
proceedWithReasonSubmitted() → saveDepartment() → 
Create ApprovalAuditRequest → Pending Audit Log → 
Super Admin Reviews → Approval → Execute
```

## Audit Trail Examples

### Example 1: Role Sync (Completed)
```json
{
  "action": "sync_roles",
  "status": "completed",
  "causer_type": "App\\Models\\Employee",
  "causer_id": 1,
  "auditable_type": "App\\Models\\Employee",
  "auditable_id": 5,
  "old_values": [2, 3, 4],
  "new_values": [1, 2, 3],
  "details": {
    "relationship": "roles",
    "attached": [1],
    "detached": [4],
    "updated": [2, 3]
  }
}
```

### Example 2: Department Create (Pending Approval)
```json
{
  "action": "create",
  "status": "pending",
  "causer_type": "App\\Models\\Employee",
  "causer_id": 3,
  "auditable_type": null,
  "auditable_id": null,
  "description": "We need IT department for new project"
}
```

### Example 3: Department Update (Completed by Super Admin)
```json
{
  "action": "update",
  "status": "completed",
  "causer_type": "App\\Models\\User",
  "causer_id": 1,
  "auditable_type": "App\\Models\\Department",
  "auditable_id": 7,
  "description": "Department updated by super admin",
  "old_values": {"name": "IT", "description": "old desc"},
  "new_values": {"name": "IT", "description": "new desc"}
}
```

## Audit Management Viewing

### Via Audit Management Component

**Path:** Audit Management → Logs tab (for completed)
**Path:** Audit Management → Approvals tab (for pending)

**View Sync Logs:**
```
Filter by Action = "sync_roles" or "sync_permissions"
View details to see attached/detached items
```

**View Department Pending:**
```
Go to Approvals tab
Review department creation/update/delete requests
Click Approve or Reject
```

## Security Notes

### Branch Isolation

- Employees can only create departments in assigned branch
- Super admins can create in any branch
- Employees cannot change branch when updating

### Permission Checks

- `is_super_admin()` helper used throughout
- Bypasses approval for super admin actions
- Employees always require approval (except super admin)

### Reason Validation

- Minimum 5 characters required for employees
- Modal won't submit with invalid reason
- Validation happens on both client (disable button) and server

## Migration Path

### Adding Audit to Existing Components

1. Use `AuditableSyncTrait` in your component
2. Replace sync calls:
   ```php
   // Old
   $model->syncRoles($roles);
   
   // New
   $this->syncWithAudit($model, 'roles', $roles, 'Description');
   ```
3. Optionally add approval requirement:
   ```php
   $this->syncWithAuditRequiringApproval($model, 'roles', $roles, 'Desc', true);
   ```

### Adding Audit to New Components

1. Extend `BaseComponent`
2. Use `AuditableSyncTrait` if you need sync logging
3. Call `AuditService::log()` for other operations
4. Show reason modal for non-super-admins when needed

## Testing Checklist

### Sync Operations
- [ ] Super admin role sync - completes immediately
- [ ] Employee role sync - creates approval request
- [ ] Character counter on reason modal works
- [ ] Submit button disabled when reason < 5 chars
- [ ] Multiple relationship sync works
- [ ] Audit log shows attached/detached/updated

### Department Management
- [ ] Super admin create - immediate, no modal
- [ ] Employee create - shows reason modal
- [ ] Super admin update - immediate, no modal
- [ ] Employee update - shows reason modal
- [ ] Super admin delete - immediate, no reason
- [ ] Employee delete - shows reason modal
- [ ] Bulk delete works for both roles
- [ ] Approval request properly formatted
- [ ] All audit logs created correctly

## Documentation Files

1. **SyncAudit.md** - Complete sync operations guide
2. **SyncAudit-QuickRef.md** - Quick reference for sync usage
3. **DepartmentAudit.md** - Department module audit guide
4. **Auditflow.md** - Complete audit system (existing)
5. **AUDIT_SYSTEM_COMPLETE.md** - This file

## Common Issues & Solutions

### Issue: Reason modal doesn't appear for employee

**Solution:** 
- Check `is_super_admin()` returns false
- Verify `showReasonModal` property exists
- Check view includes modal HTML
- Look for JavaScript errors in browser console

### Issue: Department created without approval

**Solution:**
- Verify employee role is not accidentally super admin
- Check `saveDepartment()` is not being called directly
- Verify ApprovalAuditRequest is being created
- Check audit logs for status

### Issue: Sync not appearing in audit logs

**Solution:**
- Verify component uses `AuditableSyncTrait`
- Check sync call uses `syncWithAudit()` method
- Verify AuditLog database table has write permissions
- Check for database transaction errors

## Performance Considerations

### Caching

- Department categories cached for 4200 seconds
- Branch list cached for super admins
- Consider caching audit log filters

### Query Optimization

- Bulk delete uses single `whereIn()` query
- Use `paginate()` for large audit log lists
- Index `action`, `status` columns in audit_logs

### Transaction Handling

- All sync operations wrapped in DB::transaction()
- Approval execution wrapped in transaction
- Bulk operations use transactions

## Next Steps

1. **Test all workflows** - Follow testing checklist
2. **Add email notifications** - Notify super admins of pending approvals
3. **Add audit log retention** - Archive old logs after 1 year
4. **Add audit reports** - Generate PDF/Excel audit reports
5. **Add dashboard widgets** - Show pending approvals count
6. **Extend to other modules** - Apply pattern to other entities

## Support

For issues or questions:

1. Check the relevant MD file for your use case
2. Review the component source code
3. Check AuditLog records in database
4. Look for ApprovalAuditRequest records
5. Check application logs

## Version Info

- **System:** Sweet Tooth v1.0
- **Framework:** Laravel 12
- **Livewire:** v3.7.0
- **PHP:** 8.4+
- **Date:** December 2025
