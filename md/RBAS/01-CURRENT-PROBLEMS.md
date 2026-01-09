# Current RBAC Problems

## 1. Too Many Roles (25+ for a small platform)

```
Super Admin, MD, Managing Director, Admin,
Head of Production, Sales Manager, HR Manager, Inventory Manager,
Supervisor, Till Supervisor, Chef, Head of Gelato, Confectioneries Manager,
Kitchen Staff, Gelato Production Staff, Confectioneries Production Staff,
Cashier, Corner Store Manager, Corner Store Staff, Sales Supervisor,
Sales Associate, Junior Cashier, Stock Controller, Store Keeper,
HR Officer, Employee, Viewer, Accountant...
```

**Problem:** Each department has its own role names. Adding a new department means creating new roles.

---

## 2. Role Name Inconsistencies (Causing Permission Errors)

### Case Sensitivity Mismatches:
| Location | Code Uses | Database Has |
|----------|-----------|--------------|
| SidebarVisibilityService:84 | `'admin'` | `'Admin'` |
| SidebarVisibilityService:368 | `'accountant'` | `'Accountant'` |
| Shift.php:268 | `'cashier'` | `'Cashier'` |
| AuthorizationHelper:45 | `'super-admin'` | `'Super Admin'` |

### Non-Existent Roles Being Checked:
| Location | Role Checked | Exists? |
|----------|--------------|---------|
| SidebarVisibilityService:99 | `'leave_manager'` | NO |
| SidebarVisibilityService:117 | `'auditor'` | NO |
| SidebarVisibilityService:194 | `'reporting_manager'` | NO |
| SidebarVisibilityService:84 | `'manager'` | NO |

### Spelling Errors:
- Code: `'Confectionaries Manager'` (with 'a')
- Database: `'Confectioneries Manager'` (with 'e')

---

## 3. No Department-Based Filtering

### Current Flow (BROKEN):
```
User logs in
    |
    v
Sidebar shows ALL menus based on role name
    |
    v
User can type ANY URL manually
    |
    v
NO middleware blocks them -> SECURITY HOLE
```

### What Should Happen:
```
User logs in
    |
    v
System checks user's department_id
    |
    v
Sidebar shows ONLY their department's menus
    |
    v
Middleware blocks any URL not matching their department
```

---

## 4. Permission Assignment is Complex

### Current: 50+ permissions scattered across roles
```php
$chef->givePermissionTo([
    'view-production-queue', 'start-production', 'complete-production',
    'view-recipes', 'manage-recipes', 'view-batch-history',
    'view-stock-levels', 'manage-quality-control',
]);

$headOfGelato->givePermissionTo([
    'view-production-queue', 'create-production-order', 'start-production',
    'complete-production', 'approve-production', 'manage-recipes', 'view-recipes',
    'view-production-reports', 'manage-quality-control', 'view-batch-history',
    'view-stock-levels',
]);
// ... 20 more roles with overlapping permissions
```

### Problem:
- Hard to maintain
- Easy to miss a permission
- Hard to understand who can do what

---

## 5. Dashboard Router Falls Through

When user's role doesn't match ANY check, they get:
```
abort(403, 'Your account does not have access to any dashboard. Please contact your administrator.');
```

This happens because:
1. Role name doesn't match (case sensitivity)
2. Role doesn't exist in checks
3. User has no recognized role

---

## Summary of Issues

| Issue | Impact | Frequency |
|-------|--------|-----------|
| Case sensitivity | Permission denied errors | HIGH |
| Non-existent roles | Features hidden from users | HIGH |
| No dept filtering | Security vulnerability | CRITICAL |
| Too many roles | Hard to maintain | MEDIUM |
| Complex permissions | Bugs, inconsistencies | MEDIUM |
