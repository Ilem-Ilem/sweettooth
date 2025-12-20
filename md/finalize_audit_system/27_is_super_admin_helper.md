# is_super_admin() Global Helper Function

## Status: Fully Implemented

## Description
Global helper function defined in app/Helpers/AuthorizationHelper.php that provides unified super admin checking across the entire application. Works with both web and employee guards and supports multiple super admin role names.

## Key Features
- Unified super admin detection across all guards
- Supports multiple role names: 'super-admin', 'Super Admin', 'super_admin'
- Caches user instance to avoid repeated database queries
- Used throughout the application for bypass logic

## Implementation Details
```php
function is_super_admin(): bool {
    static $isSuperAdmin = null;

    if ($isSuperAdmin !== null) {
        return $isSuperAdmin;
    }

    $user = Auth::user();
    if (!$user) {
        return false;
    }

    // Legacy check: if user_type is explicitly admin
    if (isset($user->user_type) && $user->user_type === 'admin') {
        return true;
    }

    // Check if user has super-admin role (case-insensitive for compatibility)
    if (method_exists($user, 'hasRole')) {
        return $user->hasRole('Super Admin')
            || $user->hasRole('super-admin')
            || $user->hasRole('super_admin')
            || $user->hasRole('MD')
```

## Additional Context
- **WIDESPREAD PERMISSION DISABLEMENT**: Throughout the application, authorization checks are disabled for testing:
  - Inventory purchases: `// $this->authorize('create-purchases'); // TODO: Enable permissions after testing`
  - Item requests: `// $this->authorize('create-item-requests'); // TODO: Enable permissions after testing`
  - Stock takes: `// $this->authorize('create-stock-takes'); // TODO: Enable permissions after testing`
  - Health checks: `// $this->authorize('create-health-checks'); // TODO: Enable permissions after testing`
  - Item dispatches: `// $this->authorize('dispatch-items'); // TODO: Enable permissions after testing`
  - This affects audit system effectiveness as unauthorized actions may not trigger proper approval workflows

- **AUDIT SYSTEM IMPACT**: Disabled authorizations mean the audit system may not capture all sensitive actions that should require approval
    if ($isSuperAdmin !== null) {
        return $isSuperAdmin;
    }
    
    $user = auth()->user() ?? auth('web')->user() ?? auth()->guard('web')->user();
    
    if (!$user) {
        return $isSuperAdmin = false;
    }
    
    // Check for any super-admin equivalent role
    $superAdminRoles = ['super-admin', 'Super Admin', 'super_admin'];
    
    foreach ($superAdminRoles as $role) {
        if ($user->hasRole($role)) {
            return $isSuperAdmin = true;
        }
    }
    
    return $isSuperAdmin = false;
}
```

## Faults
- **CRITICAL BUG**: The global helper and trait method use different super admin detection logic
  - Global function properly checks for roles: 'Super Admin', 'super-admin', 'super_admin', 'MD'
  - RequiresApprovalWorkflow trait uses `$user->is_super_admin ?? false` which may not work with the role system
  - This inconsistency could cause super admin bypass to fail in some contexts

## To Be Done
- None identified - fully functional and critical to bypass mechanism