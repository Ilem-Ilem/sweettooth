# Super Admin Access Fix - Complete

## Problem
Super admin users were receiving 403 "Unauthorized" errors when trying to access restricted routes like `/branch-dashboard/roles` even though they had the Super Admin role assigned.

## Root Cause
The `RolePermissionService::isSuperAdmin()` method was using `self::user()` which returned `null` when called from middleware context because it checked the employees guard first. This caused all super admin checks to fail.

## Solution - Files Fixed

### 1. **app/Services/RolePermissionService.php** (CRITICAL)
- Fixed `isSuperAdmin()` method (lines 435-455)
- Now checks web guard first, then employees guard
- Properly returns `true` for users with 'Super Admin', 'MD', or 'Managing Director' roles
- **This is the core fix** - all other middleware now use this method

```php
public static function isSuperAdmin(): bool
{
    // Check web guard first (web users are super admins)
    $webUser = Auth::guard('web')->user();
    if ($webUser && $webUser->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin'], 'web')) {
        return true;
    }
    
    // Check employees guard
    $employeeUser = Auth::guard('employees')->user();
    if ($employeeUser && $employeeUser->hasAnyRole(['Super Admin', 'MD'], 'employees')) {
        return true;
    }
    
    return false;
}
```

### 2. **app/Http/Middleware/ProtectCoreRoles.php**
- Updated to use `RolePermissionService::isSuperAdmin()`
- Now checks both guards for authentication
- Consistent role checking across the application

### 3. **app/Http/Middleware/RedirectSuperAdminToDashboard.php**
- Updated to use `RolePermissionService::isSuperAdmin()`
- Replaces direct `hasAnyRole()` calls with service method

### 4. **app/Http/Middleware/IsAdmin.php**
- Updated to use `RolePermissionService::isSuperAdmin()`
- Now checks both web and employees guards

### 5. **app/Http/Middleware/BranchMiddleware.php**
- Fixed employee guard detection: `auth('employees')->check() && !auth()->check()`
- Super admins now get default branch if none specified
- Consistent branch access logic

## Super Admin Setup

### Create Super Admin User
```bash
php artisan db:seed --class=SuperAdminUserSeeder
```

Credentials:
- Email: `admin@sweettooth.local`
- Password: `password`
- Role: Super Admin (web guard)

### Setup Web Guard Roles
```bash
php artisan db:seed --class=WebRoleSeeder
```

## Testing Access

After fix, super admin should be able to access:
- ✅ `/branch-dashboard/roles` - Role management
- ✅ `/branch-dashboard/dashboards/super-admin` - Super admin dashboard
- ✅ Any other protected admin routes

## Key Principle

**All super admin authentication checks must use `RolePermissionService::isSuperAdmin()`**

Do NOT use:
- ❌ `auth()->user()->hasAnyRole(['Super Admin'])`
- ❌ `Auth::user()->hasAnyRole(...)`

Use:
- ✅ `RolePermissionService::isSuperAdmin()`

This ensures consistent behavior across the entire application and handles both web and employees guards properly.

## Guard Definitions

- **Web Guard**: Super admins (users table) - authenticated via `auth()`
- **Employees Guard**: Regular employees (employees table) - authenticated via `auth('employees')`

A user can be in:
1. Web guard only → Super Admin
2. Employees guard only → Regular Employee
3. Both guards → Not typically used but supported

## Files Modified
1. `app/Services/RolePermissionService.php`
2. `app/Http/Middleware/ProtectCoreRoles.php`
3. `app/Http/Middleware/RedirectSuperAdminToDashboard.php`
4. `app/Http/Middleware/IsAdmin.php`
5. `app/Http/Middleware/BranchMiddleware.php`
