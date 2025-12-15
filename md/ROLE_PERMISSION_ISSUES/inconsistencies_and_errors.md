# Role and Permission Issues

## Summary of Findings

The investigation uncovered critical inconsistencies and architectural flaws in the application's authentication and authorization system. The root cause is a complex dual-guard authentication setup (a 'web' guard for admins and an 'employees' guard for staff) which should be a single guard with roles.

The most severe issue is the presence of two conflicting definitions of a 'super admin':

1.  In `app/Helpers/BranchHelper.php`, a super admin is a user on the 'web' guard but NOT the 'employees' guard.
2.  In `app/Services/AuthService.php`, a super admin is ANY user on the 'web' guard.

Different middleware use different definitions, leading to a security vulnerability where a user could be granted admin privileges on one route but not another. `IsAdmin` middleware uses the looser definition, while `SuperAdminOrPermission` uses the stricter one. This unpredictable behavior is a direct result of the complex architecture.

The long-term solution is to refactor the application to use a single users table and guard, relying on the existing (but currently misused) `spatie/laravel-permission` package to differentiate between user types. The immediate fix is to standardize on a single, correct definition of 'super admin' across all helpers, services, and middleware.

## Relevant Locations

*   `app/Helpers/BranchHelper.php`
*   `app/Services/AuthService.php`
*   `app/Http/Middleware/IsAdmin.php`
*   `app/Http/Middleware/SuperAdminOrPermission.php`
*   `app/Http/Middleware/BranchMiddleware.php`
