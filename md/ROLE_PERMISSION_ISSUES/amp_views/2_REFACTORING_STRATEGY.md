# Refactoring Strategy - Immediate & Long-Term Solutions

## Overview

Two approaches are available:
1. **Immediate Fix** (stabilize current system)
2. **Long-Term Refactoring** (modernize architecture)

---

## Immediate Fix (Recommended First)

### Objective
Standardize the definition of "super admin" across all helpers, services, and middleware without breaking existing functionality.

### Current Definition to Standardize
```php
// Super admin = authenticated in 'web' guard AND NOT in 'employees' guard
is_super_admin() returns: auth()->check() && !auth('employees')->check()
```

### Why This Definition?
- Already used consistently in BranchHelper.php
- Already used in AuthService.php
- Already enforced by IsAdmin middleware
- Already enforced by BranchMiddleware
- **Most restrictive** = **safest** for security

### Implementation Steps

#### Step 1: Create a Single Source of Truth
Create `app/Helpers/AuthorizationHelper.php`:

```php
<?php

use Illuminate\Support\Facades\Auth;

/**
 * Single source of truth for authentication and authorization checks
 * All other helpers and middleware should delegate to this
 */

if (!function_exists('is_super_admin')) {
    /**
     * Check if current user is a super admin.
     * 
     * Definition: Authenticated in 'web' guard AND NOT in 'employees' guard
     * This ensures super admins are never simultaneously branch employees
     * 
     * @return bool
     */
    function is_super_admin(): bool
    {
        return Auth::guard('web')->check() && !Auth::guard('employees')->check();
    }
}

if (!function_exists('get_current_user')) {
    /**
     * Get current authenticated user (from either guard)
     * 
     * @return \App\Models\User|\App\Models\Employee|null
     */
    function get_current_user()
    {
        return Auth::guard('web')->user() ?? Auth::guard('employees')->user();
    }
}

if (!function_exists('is_employee')) {
    /**
     * Check if current user is an employee
     * 
     * Definition: Authenticated in 'employees' guard AND NOT in 'web' guard
     * Mirrors is_super_admin() for symmetry
     * 
     * @return bool
     */
    function is_employee(): bool
    {
        return Auth::guard('employees')->check() && !Auth::guard('web')->check();
    }
}

if (!function_exists('get_user_branch_id')) {
    /**
     * Get the branch ID for current user
     * 
     * Returns:
     * - Session branch_id for super admins (allows branch switching)
     * - User branch_id for employees
     * - null if no context available
     * 
     * @return string|null
     */
    function get_user_branch_id(): ?string
    {
        // Super admins can switch branches via session
        if (is_super_admin() && session()->has('selected_branch_id')) {
            return session('selected_branch_id');
        }

        // Employees always use their assigned branch
        if (is_employee()) {
            return Auth::guard('employees')->user()?->branch_id;
        }

        return null;
    }
}
```

#### Step 2: Update BranchHelper.php
Replace with simpler delegation:

```php
<?php

use App\Models\Branch;

// Re-export from AuthorizationHelper for backwards compatibility
if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return \is_super_admin(); // Calls AuthorizationHelper
    }
}

// ... other functions remain but use get_user_branch_id() internally
```

#### Step 3: Update AuthService.php
Make it consistent:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * IMPORTANT: All methods delegate to AuthorizationHelper functions
     * for consistency with other parts of the application
     */

    public static function isSuperAdmin(): bool
    {
        return \is_super_admin();
    }

    public static function isEmployee(): bool
    {
        return \is_employee();
    }

    public static function user()
    {
        return \get_current_user();
    }

    // ... rest remains the same
}
```

#### Step 4: Add Documentation to All Affected Files
Add this comment block to each file:

```php
/**
 * IMPORTANT: Authorization System
 * 
 * There are two mutually exclusive user types:
 * 1. Super Admin: auth('web')->check() && !auth('employees')->check()
 * 2. Employee: auth('employees')->check() && !auth('web')->check()
 * 
 * A user CANNOT be both simultaneously.
 * 
 * For all authorization checks, use the helper functions:
 * - is_super_admin()
 * - is_employee()
 * - get_current_user()
 * - get_user_branch_id()
 * 
 * See: app/Helpers/AuthorizationHelper.php
 */
```

---

## Long-Term Refactoring (Future Implementation)

This is a comprehensive migration to use a single users table with role-based access control via spatie/laravel-permission.

### Phase 1: Preparation (Week 1)

1. **Create new migration** to add columns to `users` table:
```sql
ALTER TABLE users ADD COLUMN branch_id UUID NULLABLE;
ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT true;
```

2. **Create migration** to migrate employee data:
```php
// Migration: migrate_employees_to_users
DB::statement("
    INSERT INTO users (
        id, name, email, password, branch_id, created_at, updated_at
    )
    SELECT 
        id, name, email, password, branch_id, created_at, updated_at
    FROM employees
");

// Assign roles based on existing employee data
// (requires analyzing current permission structure)
```

3. **Update User model**:
```php
class User extends Model {
    use HasRoles, HasPermissions; // spatie/laravel-permission
    
    protected $casts = [
        'branch_id' => 'string',
    ];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
```

### Phase 2: Guard Configuration (Week 2)

1. **Update `config/auth.php`**:
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users', // Only users table
    ],
    // Remove 'employees' guard entirely
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    // Remove 'employees' provider
],
```

2. **Create role definitions** in migration or seeder:
```php
// Roles
Role::create(['name' => 'super-admin']);
Role::create(['name' => 'admin']);
Role::create(['name' => 'branch-manager']);
Role::create(['name' => 'supervisor']);
Role::create(['name' => 'employee']);

// Permissions (example)
Permission::create(['name' => 'view.inventory']);
Permission::create(['name' => 'edit.inventory']);
// ... etc
```

### Phase 3: Code Migration (Weeks 3-4)

1. **Replace all dual-guard checks**:

Old:
```php
if (auth('employees')->check()) {
    // is employee
}

if (is_super_admin()) {
    // is super admin
}
```

New:
```php
if (auth()->user()?->hasRole('employee')) {
    // is employee
}

if (auth()->user()?->hasRole('super-admin')) {
    // is super admin
}
```

2. **Update middleware**:
```php
class IsAdmin {
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
        return $next($request);
    }
}
```

3. **Refactor BranchMiddleware**:
```php
// Uses user->branch_id instead of session
if (!auth()->user()?->branch_id) {
    abort(403, 'No branch assigned');
}

$requestedBranch = $request->query('b_id');
if ($requestedBranch && auth()->user()->branch_id !== $requestedBranch) {
    if (!auth()->user()->hasRole('super-admin')) {
        abort(403, 'Cannot access other branches');
    }
}
```

### Phase 4: Testing & Deprecation (Week 5)

1. Keep old Employee model as read-only facade (optional)
2. Run comprehensive tests
3. Deprecation warnings for old auth helpers
4. Delete old files after confidence period

---

## Comparison: Before vs After

### Current (Dual Guard)
```
users table (super admins)
├── web guard
└── Can be assigned roles via spatie

employees table (branch staff)
├── employees guard
└── Can be assigned roles via spatie

Problem: Two separate authentication pathways
```

### Proposed (Single Guard)
```
users table (everyone)
├── branch_id (nullable)
├── web guard
├── Roles: super-admin, admin, branch-manager, employee
└── Permissions: granular access control

Benefit: Single source of truth
```

---

## Implementation Timeline

| Phase | Timeline | Effort | Risk |
|-------|----------|--------|------|
| Immediate Fix | 1-2 days | Low | Very Low |
| Long-Term Phase 1 | 1 week | Low | Low |
| Long-Term Phase 2 | 1 week | Low | Low |
| Long-Term Phase 3 | 2 weeks | High | Medium |
| Long-Term Phase 4 | 1 week | Medium | Medium |
| **Total Long-Term** | **5-6 weeks** | **High** | **Medium** |

---

## Recommendation

1. **Implement Immediate Fix NOW** (1-2 days)
   - Stabilizes current system
   - Prevents security vulnerabilities
   - No breaking changes
   - No data migration

2. **Plan Long-Term Refactoring** (5-6 weeks)
   - Schedule after immediate fix is stable
   - More maintainable architecture
   - Better role-based access control
   - Aligns with Laravel best practices
