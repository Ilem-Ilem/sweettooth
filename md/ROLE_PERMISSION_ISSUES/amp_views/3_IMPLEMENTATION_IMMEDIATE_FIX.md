# Immediate Fix Implementation Guide

## Overview
This guide provides step-by-step instructions to fix the inconsistent super admin definition across all files without breaking existing functionality.

---

## Step 1: Create AuthorizationHelper.php

**File:** `app/Helpers/AuthorizationHelper.php`

```php
<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

/**
 * Authorization Helper Functions
 * 
 * Single source of truth for authentication and authorization checks.
 * All authorization logic should delegate to these functions.
 * 
 * AUTHORIZATION MODEL:
 * - Super Admin: authenticated in 'web' guard AND NOT in 'employees' guard
 * - Employee: authenticated in 'employees' guard AND NOT in 'web' guard
 * 
 * These are mutually exclusive states. A user cannot be both.
 */

if (!function_exists('is_super_admin')) {
    /**
     * Check if current user is a super admin.
     *
     * A super admin is authenticated in the 'web' guard but NOT in the
     * 'employees' guard. This ensures super admins are never simultaneously
     * acting as branch employees.
     *
     * @return bool
     */
    function is_super_admin(): bool
    {
        return Auth::guard('web')->check() && !Auth::guard('employees')->check();
    }
}

if (!function_exists('is_employee')) {
    /**
     * Check if current user is an employee.
     *
     * An employee is authenticated in the 'employees' guard but NOT in the
     * 'web' guard. Mirrors is_super_admin() for semantic clarity.
     *
     * @return bool
     */
    function is_employee(): bool
    {
        return Auth::guard('employees')->check() && !Auth::guard('web')->check();
    }
}

if (!function_exists('get_current_user')) {
    /**
     * Get the currently authenticated user from either guard.
     *
     * Returns the user object from whichever guard is authenticated.
     * Prefers 'web' guard if both are somehow authenticated (defensive).
     *
     * @return \App\Models\User|\App\Models\Employee|null
     */
    function get_current_user()
    {
        return Auth::guard('web')->user() ?? Auth::guard('employees')->user();
    }
}

if (!function_exists('get_user_branch_id')) {
    /**
     * Get the branch ID for the current user.
     *
     * - Super admins: returns session-selected branch (allows switching)
     * - Employees: returns their assigned branch
     * - Unauthenticated: returns null
     *
     * @return string|null
     */
    function get_user_branch_id(): ?string
    {
        // Super admins can switch branches via session
        if (is_super_admin()) {
            if (session()->has('selected_branch_id')) {
                return session('selected_branch_id');
            }
            // Fallback to first active branch if no session
            $defaultBranch = Branch::where('is_active', 1)->first();
            return $defaultBranch?->id;
        }

        // Employees always use their assigned branch
        if (is_employee()) {
            return Auth::guard('employees')->user()?->branch_id;
        }

        return null;
    }
}

if (!function_exists('current_branch_id')) {
    /**
     * Get the current branch ID (wrapper for backwards compatibility).
     * 
     * @deprecated Use get_user_branch_id() instead
     * @return string|null
     */
    function current_branch_id(): ?string
    {
        return get_user_branch_id();
    }
}

if (!function_exists('validate_branch_access')) {
    /**
     * Validate that the current user can access the specified branch.
     *
     * - Super admins can access any active branch
     * - Employees can only access their assigned branch
     * - Unauthenticated users cannot access any branch
     *
     * @param string $branchId
     * @return bool
     */
    function validate_branch_access(string $branchId): bool
    {
        // Super admins can access all branches
        if (is_super_admin()) {
            return Branch::where('id', $branchId)->where('is_active', 1)->exists();
        }

        // Employees can only access their assigned branch
        if (is_employee()) {
            return Auth::guard('employees')->user()?->branch_id === $branchId;
        }

        return false;
    }
}

if (!function_exists('can_access_all_branches')) {
    /**
     * Check if current user can access all branches.
     *
     * Returns true for:
     * - Super admins
     * - Users with multi-branch roles (if using spatie/laravel-permission)
     *
     * @return bool
     */
    function can_access_all_branches(): bool
    {
        // Employees can never access all branches
        if (is_employee()) {
            return false;
        }

        // Super admins can always access all branches
        if (is_super_admin()) {
            return true;
        }

        // If spatie/laravel-permission is used, check roles
        $user = get_current_user();
        if ($user && method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole(['super-admin', 'md', 'director', 'admin']);
        }

        return false;
    }
}

if (!function_exists('get_accessible_branches')) {
    /**
     * Get all branches accessible to the current user.
     *
     * - Super admins: all active branches
     * - Employees: only their assigned branch
     * - Unauthenticated: empty collection
     *
     * @return \Illuminate\Support\Collection
     */
    function get_accessible_branches()
    {
        if (can_access_all_branches()) {
            return Branch::where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        if (is_employee()) {
            $branchId = Auth::guard('employees')->user()?->branch_id;
            if ($branchId) {
                return Branch::where('id', $branchId)->get();
            }
        }

        return collect([]);
    }
}

if (!function_exists('set_current_branch')) {
    /**
     * Set the current branch context in session.
     *
     * Only super admins can change their branch context.
     * Employees' branch is determined by their user record.
     *
     * @param string $branchId
     * @param bool $updateUserPreference
     * @return void
     */
    function set_current_branch(string $branchId, bool $updateUserPreference = true): void
    {
        if (!is_super_admin()) {
            return;
        }

        session(['selected_branch_id' => $branchId]);

        if ($updateUserPreference && Auth::guard('web')->check()) {
            Auth::guard('web')->user()->update(['last_accessed_branch_id' => $branchId]);
        }
    }
}

if (!function_exists('current_branch')) {
    /**
     * Get the current branch model instance.
     *
     * @return \App\Models\Branch|null
     */
    function current_branch(): ?Branch
    {
        $branchId = get_user_branch_id();

        if (!$branchId) {
            return null;
        }

        return Branch::find($branchId);
    }
}

if (!function_exists('current_actor')) {
    /**
     * Get the current acting user (super admin or employee).
     *
     * @return \App\Models\User|\App\Models\Employee|null
     */
    function current_actor()
    {
        return get_current_user();
    }
}
```

---

## Step 2: Update AuthService.php

**File:** `app/Services/AuthService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Unified Authentication Service
 * 
 * Single source of truth for all user authentication and authorization checks.
 * This service delegates to AuthorizationHelper for consistency.
 * 
 * ALWAYS use this service instead of calling guards directly.
 */
class AuthService
{
    /**
     * Get current authenticated user (from any guard)
     * 
     * @return \App\Models\User|\App\Models\Employee|null
     */
    public static function user()
    {
        return get_current_user();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function check(): bool
    {
        return Auth::guard('web')->check() || Auth::guard('employees')->check();
    }

    /**
     * Check if user is super admin.
     * 
     * Super admin definition: authenticated in 'web' guard AND NOT 'employees' guard
     * 
     * This is the ONLY definition used throughout the application.
     * If you need to change this definition, update app/Helpers/AuthorizationHelper.php
     * 
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        return is_super_admin();
    }

    /**
     * Check if user is an employee.
     * 
     * Employee definition: authenticated in 'employees' guard AND NOT 'web' guard
     * 
     * @return bool
     */
    public static function isEmployee(): bool
    {
        return is_employee();
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    public static function hasRole(string|array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Check if user has specific permission
     * 
     * @param string|array $permissions
     * @return bool
     */
    public static function hasPermission(string|array $permissions): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Get current user's roles
     * 
     * @return array
     */
    public static function getRoles(): array
    {
        $user = self::user();
        if (!$user) {
            return [];
        }

        return $user->roles()->pluck('name')->toArray();
    }

    /**
     * Get current guard name
     * 
     * @return string 'web'|'employees'|'none'
     */
    public static function guard(): string
    {
        if (Auth::guard('web')->check()) {
            return 'web';
        }
        if (Auth::guard('employees')->check()) {
            return 'employees';
        }
        return 'none';
    }

    /**
     * Ensure user is authenticated, throw 401 if not
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            abort(401, 'Unauthorized');
        }
    }

    /**
     * Ensure user is super admin, throw 403 if not
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public static function requireSuperAdmin(): void
    {
        if (!self::isSuperAdmin()) {
            abort(403, 'Only Super Admins can access this resource');
        }
    }

    /**
     * Ensure user has role, throw 403 if not
     * 
     * @param string|array $roles
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public static function requireRole(string|array $roles): void
    {
        if (!self::hasRole($roles)) {
            abort(403, 'Insufficient permissions');
        }
    }

    /**
     * Ensure user has permission, throw 403 if not
     * 
     * @param string|array $permissions
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public static function requirePermission(string|array $permissions): void
    {
        if (!self::hasPermission($permissions)) {
            abort(403, 'Insufficient permissions');
        }
    }
}
```

---

## Step 3: Update IsAdmin Middleware

**File:** `app/Http/Middleware/IsAdmin.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IsAdmin Middleware
 * 
 * Ensures that only super admins can access the protected route.
 * 
 * Super admin definition: authenticated in 'web' guard AND NOT 'employees' guard
 * 
 * Uses AuthService for consistency across the entire application.
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     * Only super admins can pass.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!is_super_admin()) {
            abort(403, 'Only super admins can access this resource');
        }

        return $next($request);
    }
}
```

---

## Step 4: Update SuperAdminOrPermission Middleware

**File:** `app/Http/Middleware/SuperAdminOrPermission.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SuperAdminOrPermission Middleware
 * 
 * Allows access if:
 * 1. User is a super admin, OR
 * 2. User has one of the specified permissions
 * 
 * Super admin definition: authenticated in 'web' guard AND NOT 'employees' guard
 */
class SuperAdminOrPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$permissions
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Super admins always have access
        if (is_super_admin()) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        $user = get_current_user();
        if ($user && $user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Insufficient permissions');
    }
}
```

---

## Step 5: Update BranchMiddleware.php

**File:** `app/Http/Middleware/BranchMiddleware.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;

/**
 * BranchMiddleware
 * 
 * Validates and manages branch context for every request.
 * 
 * Requirements:
 * 1. The b_id parameter must be a valid UUID that exists in the database
 * 2. User must be authorized to access this branch:
 *    - Super admins can access any active branch
 *    - Employees can only access their assigned branch
 */
class BranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $b_id = $request->query('b_id');

        // SUPER ADMINS: Can use first active branch if none specified
        if (is_super_admin() && empty($b_id)) {
            $defaultBranch = Branch::where('is_active', 1)->first();
            if ($defaultBranch) {
                $b_id = $defaultBranch->id;
                set_current_branch($b_id);
            }
        }

        // EMPLOYEES: MUST provide b_id parameter
        if (is_employee() && empty($b_id)) {
            Log::warning('Employee attempted to access branch route without b_id parameter', [
                'ip' => $request->ip(),
                'user_id' => get_current_user()?->id,
                'url' => $request->fullUrl(),
            ]);
            abort(403, 'Branch parameter required');
        }

        // Validate format and existence
        $validator = Validator::make(['b_id' => $b_id], [
            'b_id' => ['required', 'uuid', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            Log::warning('Blocked request with invalid or missing b_id', [
                'ip' => $request->ip(),
                'b_id' => $b_id,
                'user_type' => is_employee() ? 'employee' : 'super_admin',
                'url' => $request->fullUrl(),
            ]);
            abort(403, 'Invalid branch access');
        }

        // Validate user authorization for this branch
        if (!validate_branch_access($b_id)) {
            Log::warning('Blocked unauthorized branch access attempt', [
                'ip' => $request->ip(),
                'user_id' => get_current_user()?->id,
                'user_type' => is_employee() ? 'employee' : 'super_admin',
                'requested_branch' => $b_id,
                'url' => $request->fullUrl(),
            ]);
            abort(403, 'You are not authorized to access this branch');
        }

        // Set the branch globally for the request
        $branch = Branch::find($b_id);
        app()->instance('currentBranch', $branch);
        set_current_branch($b_id, false);

        return $next($request);
    }
}
```

---

## Step 6: Update BranchHelper.php (Optional - Keep for Backwards Compatibility)

**File:** `app/Helpers/BranchHelper.php`

Replace with simpler version that delegates to AuthorizationHelper:

```php
<?php

/**
 * Branch Helper Functions
 * 
 * DEPRECATED: Most functions have been moved to app/Helpers/AuthorizationHelper.php
 * 
 * This file is kept for backwards compatibility only.
 * New code should use AuthorizationHelper functions directly.
 */

// All functions now delegate to AuthorizationHelper
// (AuthorizationHelper.php is loaded automatically via app.php config)

// These are preserved for backwards compatibility
if (!function_exists('audit')) {
    function audit(
        $causer,
        $action,
        $auditable = null,
        $description = null,
        $status = 'completed',
        $approvalRequest = null,
        array $metadata = []
    ) {
        return \App\Services\AuditService::log(
            $causer,
            $action,
            $auditable,
            $description,
            $status,
            $approvalRequest,
            $metadata
        );
    }
}
```

---

## Step 7: Register AuthorizationHelper in config/app.php

Add the helper file to be auto-loaded:

**File:** `config/app.php`

In the `aliases` array or in `bootstrap/app.php`, ensure AuthorizationHelper is included:

```php
// In bootstrap/app.php, add after existing requires:
require base_path('app/Helpers/AuthorizationHelper.php');
```

Or in `composer.json`:

```json
{
    "autoload": {
        "files": [
            "app/Helpers/AuthorizationHelper.php"
        ]
    }
}
```

Then run: `composer dump-autoload`

---

## Step 8: Testing Checklist

Before deploying, verify:

### Authentication Tests
- [ ] Super admin can login via 'web' guard
- [ ] Employee can login via 'employees' guard
- [ ] `is_super_admin()` returns true only for web-guard users
- [ ] `is_employee()` returns true only for employees-guard users
- [ ] A user cannot be both simultaneously

### Authorization Tests
- [ ] Super admin can access all branches
- [ ] Super admin can switch branches via session
- [ ] Employee can only access their assigned branch
- [ ] Employee cannot switch branches
- [ ] `validate_branch_access()` enforces rules correctly

### Middleware Tests
- [ ] `IsAdmin` middleware rejects non-super-admins
- [ ] `SuperAdminOrPermission` allows super admin always
- [ ] `SuperAdminOrPermission` allows permission-based access
- [ ] `BranchMiddleware` validates b_id format
- [ ] `BranchMiddleware` rejects unauthorized branch access

### Backwards Compatibility
- [ ] All existing code using old helpers still works
- [ ] No breaking changes to function signatures
- [ ] Audit logging still functions

---

## Deployment Steps

1. Create new `AuthorizationHelper.php`
2. Update `AuthService.php`
3. Update middleware files (IsAdmin, SuperAdminOrPermission, BranchMiddleware)
4. Register AuthorizationHelper for auto-loading
5. Run tests
6. Deploy
7. Monitor logs for any issues
8. (Optional) Gradually deprecate BranchHelper.php functions

This implementation completes the immediate fix without breaking existing functionality.
