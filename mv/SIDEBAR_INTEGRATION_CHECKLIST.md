# Sidebar Integration Checklist

## Status
🔄 **IN PROGRESS** - SidebarVisibilityService created, blade template partially updated

## Completed
✅ SidebarVisibilityService created with 20+ visibility methods
✅ Started updating branch-dashboard.blade.php
✅ Updated Administration section visibility
✅ Updated Organization section visibility
✅ Updated Employee Management visibility
✅ Updated Department visibility
✅ Updated Leave Management visibility

## Remaining Sidebar Updates

### Audit Management Section (Line 150)
```blade
@if ($isSuperAdmin || $currentUser->hasRole('auditor'))
```
**Change to:**
```blade
@if ($sidebarService::canSeeAuditManagement($currentUser))
```

### Inventory Section (Line 161)
```blade
@if ($isSuperAdmin || $currentUser->hasAnyRole(['inventory_manager']))
```
**Change to:**
```blade
@if ($sidebarService::canSeeInventory($currentUser))
```

### Analytics Section (Line 207)
```blade
@if ($isSuperAdmin || $currentUser->hasAnyRole(['reporting_manager']))
```
**Change to:**
```blade
@if ($sidebarService::canSeeAnalytics($currentUser))
```

### Production Section (Line 280)
Multiple checks need updating:
- Line 283: `$isSuperAdmin || $currentUser->hasAnyRole(['employee_manager'])`
  - Change to: `$sidebarService::canSeeProduction($currentUser)`

### Production Callbacks (Line 359)
```blade
@if($isSuperAdmin || $currentUser->hasAnyRole(array_merge($adminProductionRoles, $departmentRestrictedRoles)))
```
**Change to:**
```blade
@if($sidebarService::canSeeProductionCallbacks($currentUser))
```

### Sales Management Section (Line 378)
```blade
@if($isSuperAdmin || $currentUser->hasAnyRole(array_merge($adminSalesRoles, $departmentRestrictedSalesRoles)))
```
**Change to:**
```blade
@if($sidebarService::canSeeSalesManagement($currentUser))
```

### Sales Manager Items (Line 381)
```blade
@if($isSuperAdmin || $currentUser->hasAnyRole($adminSalesRoles))
```
**Change to:**
```blade
@if($sidebarService::canSeeSalesManagerItems($currentUser))
```

### Reporting Section (Line 503)
```blade
@if ($isSuperAdmin || $currentUser->hasAnyRole(['reporting_manager']))
```
**Change to:**
```blade
@if ($sidebarService::canSeeReporting($currentUser))
```

### Role/Branches/Settings in Administration (Lines 33-57)
Add individual visibility checks for:
- Roles & Permissions: `$sidebarService::canSeeRolesPermissions($currentUser)`
- Branch Management: `$sidebarService::canSeeBranchManagement($currentUser)`
- MD Reports: `$sidebarService::canSeeMDReports($currentUser)`
- System Settings: `$sidebarService::canSeeSettings($currentUser)`

### Department-Specific Roles Detection (Lines 257-278)
Replace role name checks with service methods:
- Line 257: Use `$sidebarService::isProductionAdminRole($currentUser)`
- Line 260: Use `$sidebarService::isDepartmentRestrictedProductionRole($currentUser)`
- Line 268: Use `$sidebarService::isSalesAdminRole($currentUser)`
- Line 271: Use `$sidebarService::isDepartmentRestrictedSalesRole($currentUser)`

## Implementation Steps

1. **Continue editing blade template** (16 more changes needed)
2. **Test each section visibility** after changes
3. **Verify sidebar appears correctly** for different user roles
4. **Check console for blade errors**
5. **Test with different employee types**:
   - Admin
   - HR Manager
   - Inventory Manager
   - Sales Manager
   - Head of Production
   - Regular staff

## Quick Reference for All Changes

| Line | Current Check | New Service Method |
|------|--------------|-------------------|
| 32 | `$isSuperAdmin` | `canSeeAdministration()` |
| 63 | `$isSuperAdmin OR hasAnyRole()` | `canSeeOrganization()` |
| 65 | `$isSuperAdmin OR hasAnyRole()` | `canSeeDepartments()` |
| 79 | `$isSuperAdmin OR hasAnyRole(['employee_manager'])` | `canSeeEmployeeManagement()` |
| 118 | `!$isSuperAdmin` | `!canSeeAdministration()` |
| 133 | `$isSuperAdmin OR hasAnyRole(['leave_manager'])` | `canSeeLeaveManagement()` |
| 150 | `$isSuperAdmin OR hasRole('auditor')` | `canSeeAuditManagement()` |
| 161 | `$isSuperAdmin OR hasAnyRole(['inventory_manager'])` | `canSeeInventory()` |
| 207 | `$isSuperAdmin OR hasAnyRole(['reporting_manager'])` | `canSeeAnalytics()` |
| 280 | Various checks | `canSeeProduction()` |
| 359 | Complex role checks | `canSeeProductionCallbacks()` |
| 378 | Complex role checks | `canSeeSalesManagement()` |
| 381 | Complex role checks | `canSeeSalesManagerItems()` |
| 503 | `$isSuperAdmin OR hasAnyRole()` | `canSeeReporting()` |

## Testing Commands

```bash
# Run migration
php artisan migrate

# Seed all data
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=EmployeeSeeder
php artisan db:seed --class=MDSeeder

# Or all at once
php artisan db:seed

# Clear cache (if blade caching issues)
php artisan cache:clear
php artisan view:clear

# Test as different users (login with email/password)
# Email format: firstname.lastname.number@sweettooth.com
# Password: password
```

## Expected Behavior After Implementation

- **Super Admin**: Sees all menu items
- **Admin**: Sees administration + organization sections
- **HR Manager**: Sees organization section (except admin items)
- **Inventory Manager**: Sees inventory section
- **Sales Manager**: Sees sales management section
- **Head of Production**: Sees production section
- **Department Staff**: Sees only personal sections (leave, my sales, etc.)

## Notes

- All role checks are now permission-based through the service
- Sidebar visibility matches route-level permissions
- Service methods use both role and permission checks
- Menu structure preserved, just visibility logic improved
- No functional changes to sidebar, only visibility conditions

## Estimated Time to Complete

- Continue blade updates: 30-45 minutes
- Test all menu sections: 30-45 minutes
- Fix any issues: 15-30 minutes
- **Total**: 1-2 hours

---

**Next Steps**: Continue with remaining blade template updates
