# Current State Analysis - Role & Permission System

## File-by-File Breakdown

### 1. BranchHelper.php (Line 93-106: is_super_admin)
**Definition Used:**
```php
function is_super_admin(): bool
{
    return auth()->check() && !auth('employees')->check();
}
```

**Issue:**
- A super admin is someone authenticated in the 'web' guard BUT NOT in the 'employees' guard
- This is too strict because it prevents users from having BOTH guards authenticated
- Contradicts multi-user scenarios where admins might occasionally act as employees

**Current Behavior:**
- Employees can never be super admins
- Prevents role-based flexibility

---

### 2. AuthService.php (Line 35-39: isSuperAdmin)
**Definition Used:**
```php
public static function isSuperAdmin(): bool
{
    // Super admin = web guard authenticated AND NOT employees guard
    return Auth::guard('web')->check() && !Auth::guard('employees')->check();
}
```

**Issue:**
- Identical to BranchHelper's definition
- However, documentation comment suggests "web guard only" which is misleading
- Doesn't mention the "NOT employees guard" requirement
- Creates subtle inconsistency with other middleware

---

### 3. IsAdmin.php Middleware
**Implementation:**
```php
public function handle(Request $request, Closure $next): Response
{
    AuthService::requireSuperAdmin();
    return $next($request);
}
```

**Issue:**
- Uses AuthService's isSuperAdmin() definition (stricter)
- Enforces dual-guard exclusivity
- Routes protected by this middleware are overly restrictive

---

### 4. SuperAdminOrPermission.php Middleware
**Implementation:**
```php
public function handle(Request $request, Closure $next, ...$permissions): Response
{
    // Allow Super Admin always
    if (AuthService::isSuperAdmin()) {
        return $next($request);
    }

    // Check permissions
    if (AuthService::user() && AuthService::user()->hasAnyPermission($permissions)) {
        return $next($request);
    }

    abort(403);
}
```

**Issue:**
- Relies on AuthService::isSuperAdmin() for the looser interpretation
- But that method enforces the strict definition (NOT employees guard)
- Mixed responsibility: checks super admin status AND permissions

---

### 5. BranchMiddleware.php
**Key Issue at Lines 29-35:**
```php
if (AuthService::isSuperAdmin() && empty($b_id)) {
    $defaultBranch = Branch::where('is_active', 1)->first();
    if ($defaultBranch) {
        $b_id = $defaultBranch->id;
        set_current_branch($b_id);
    }
}
```

**Issues:**
- Uses isSuperAdmin() to determine if user can auto-select a branch
- Employees must provide b_id, super admins don't
- Relies on the inconsistent definition of super admin
- Line 38-45: Assumes employees check is sufficient for authorization

---

## Cross-File Inconsistencies

| Location | Definition | Issues |
|----------|-----------|--------|
| `is_super_admin()` helper | auth().check() && !auth('employees').check() | Strict, exclusive |
| `AuthService::isSuperAdmin()` | Auth::guard('web')->check() && !Auth::guard('employees')->check() | Strict, exclusive |
| `IsAdmin` middleware | Uses AuthService::isSuperAdmin() | Overly restrictive |
| `SuperAdminOrPermission` middleware | Uses AuthService::isSuperAdmin() | Correct but misleading docs |
| `BranchMiddleware` | Uses AuthService::isSuperAdmin() + assumptions | Partially inconsistent |

## Root Cause

The application tries to use two guards (web for admins, employees for staff) as separate, mutually-exclusive authentication pathways instead of using:
- **Single users table** 
- **Single guard** 
- **spatie/laravel-permission roles** to differentiate user types

This creates ambiguity about what "super admin" means:
1. A user in the web guard
2. A user in the web guard BUT NOT in the employees guard
3. A user with the 'super-admin' role (via spatie)

## Security Vulnerability

If middleware definitions diverge, a user could be:
- **Granted** admin privileges on routes using looser definition
- **Denied** admin privileges on routes using stricter definition

Example:
- User authenticated in both guards
- Route A uses IsAdmin (requires strict exclusivity) → DENIED
- Route B uses a looser check → ALLOWED
- **Inconsistent authorization state**

## Current Usage

The application currently relies on:
- `is_super_admin()` and `can_access_all_branches()` for branch context logic
- `AuthService::isSuperAdmin()` for middleware
- Implicit assumption that these are consistent (they are, but fragile)

The problem is **fragility**: they're currently consistent but changes to any single location could break the system without obvious indication.
