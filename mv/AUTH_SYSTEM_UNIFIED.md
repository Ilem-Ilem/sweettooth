# Unified Authentication System

## Overview
All authentication and role checks now go through a single service: `AuthService`

This replaces the previous broken system that scattered `hasAnyRole()` calls everywhere.

## How to Use

### Check if user is super admin
```php
use App\Services\AuthService;

if (AuthService::isSuperAdmin()) {
    // User is authenticated via web guard (super admin)
}
```

### Check if user is employee
```php
if (AuthService::isEmployee()) {
    // User is authenticated via employees guard (regular employee)
}
```

### Check if user has specific role
```php
if (AuthService::hasRole('Admin')) {
    // User has Admin role
}

if (AuthService::hasRole(['Admin', 'Super Admin'])) {
    // User has either Admin or Super Admin role
}
```

### Throw error if not authorized
```php
// In middleware or controller
AuthService::requireSuperAdmin();  // Throws 403 if not super admin
AuthService::requireAuth();         // Throws 401 if not authenticated
AuthService::requireRole('Admin');  // Throws 403 if user doesn't have role
```

### Get current user
```php
$user = AuthService::user();  // Returns user from either guard
```

### Get current guard
```php
$guard = AuthService::guard();  // Returns 'web', 'employees', or 'none'
```

## Definition of Terms

| Term | Definition | Guard |
|------|-----------|-------|
| Super Admin | User authenticated via web guard | web |
| Employee | User authenticated via employees guard | employees |
| Super Admin + Employee | Rare case, both guards active | both |

## Files Using AuthService
- `app/Http/Middleware/ProtectCoreRoles.php`
- `app/Http/Middleware/RedirectSuperAdminToDashboard.php`
- `app/Http/Middleware/IsAdmin.php`
- `app/Http/Middleware/BranchMiddleware.php`
- `app/Livewire/BranchDashboard/Dashboards/SuperAdminDashboard.php`

## Rules to Follow

### ✅ DO:
```php
use App\Services\AuthService;

AuthService::isSuperAdmin();
AuthService::isEmployee();
AuthService::hasRole('Admin');
AuthService::requireSuperAdmin();
```

### ❌ DON'T:
```php
auth()->user()->hasAnyRole(['Super Admin']);
Auth::guard('web')->user()->hasAnyRole(...);
RolePermissionService::isSuperAdmin();
is_super_admin();  // Helper function - don't use for authorization
```

## Testing Super Admin Access
```bash
# Create super admin user
php artisan db:seed --class=SuperAdminUserSeeder

# Login with:
# Email: admin@sweettooth.local
# Password: password
```

You should now have access to:
- `/branch-dashboard/roles` - Role management
- `/branch-dashboard/dashboards/super-admin` - Super admin dashboard
- All other protected admin routes
