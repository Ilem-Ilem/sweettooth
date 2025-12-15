# Role-Based Dashboard Routing Fix

## Issue
Inventory Manager was being redirected to HR Dashboard instead of Inventory Dashboard due to:
1. Inventory Manager had `view-employees` permission
2. `canSeeEmployeeManagement()` checks for this permission
3. HR route was checked before Inventory route in the router

## Solution Implemented

### 1. Reordered Route Priority
**File:** `app/Livewire/BranchDashboard/Dashboards/Router.php`

Changed routing order from:
- Production → Sales → HR → Inventory → Reporting → Organization

To:
- Production → Sales → **Inventory** → **HR** → Reporting → Organization

**Rationale:** Inventory-specific roles are now checked before general HR permissions.

### 2. Removed Unnecessary Permission
**File:** `database/seeders/RoleSeeder.php`

Removed `view-employees` permission from Inventory Manager role since:
- Inventory Dashboard doesn't require this permission
- It was causing unintended HR dashboard access
- Inventory Manager's primary focus is stock management, not employee management

**Before:**
```php
'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
'view-department-reports', 'view-analytics', 'view-employees', 'view-departments',
```

**After:**
```php
'receive-stock', 'transfer-stock', 'adjust-inventory', 'view-stock-levels',
'view-department-reports', 'view-analytics', 'view-departments',
```

### 3. Fixed Role Name Casing
**File:** `app/Services/SidebarVisibilityService.php`

Updated `canSeeEmployeeManagement()` to use Title Case role names:
```php
|| $user->hasAnyRole(['Super Admin', 'Admin']);
```

Instead of:
```php
|| $user->hasAnyRole(['Super Admin', 'employee_manager', 'admin']);
```

## Results
✅ Inventory Manager now redirected to Inventory Dashboard
✅ Removed unintended HR dashboard access
✅ Consistent role naming (Title Case) across the system
✅ Role permissions more accurately reflect job function

## Testing
```
Inventory Manager: Ngozi Mohammed
Can see Production: NO
Can see Sales: NO
Can see Inventory: YES ← Routes to INVENTORY dashboard
Can see Employee Management: NO ← No longer has this permission
```
