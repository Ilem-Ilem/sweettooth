# Middleware Specification

## Overview

This document specifies all middleware needed for department-scoped access control.

---

## 1. DepartmentScopeMiddleware (NEW - Core)

### Purpose
Enforces that users can only access URLs matching their department.

### File Location
`app/Http/Middleware/DepartmentScopeMiddleware.php`

### Logic Flow

```
Request arrives with {deptSlug} in URL
           |
           v
Is user Super Admin? ──YES──> ALLOW (bypass all checks)
           |
           NO
           v
Is user Admin? ──YES──> Check dept belongs to user's branch
           |                    |
           NO                   v
           |              Branch match? ──YES──> ALLOW
           |                    |
           |                   NO
           |                    v
           |               DENY (403)
           v
Get user's department_id
           |
           v
Does URL deptSlug match user's dept slug? ──YES──> ALLOW
           |
           NO
           v
Is user Manager AND same category? ──YES──> ALLOW (managers see category)
           |
           NO
           v
DENY (403: "You can only access your own department")
```

### Implementation

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Department;
use Symfony\Component\HttpFoundation\Response;

class DepartmentScopeMiddleware
{
    /**
     * Handle department scope validation
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get department slug from route (supports multiple param names)
        $deptSlug = $request->route('deptSlug')
                 ?? $request->route('salesDeptSlug')
                 ?? $request->route('productionDeptSlug');

        // If no department in URL, allow (non-scoped route)
        if (!$deptSlug) {
            return $next($request);
        }

        // Super Admin bypasses all checks
        if ($this->isSuperAdmin($user)) {
            $this->setDepartmentContext($request, $deptSlug);
            return $next($request);
        }

        // Admin can access any department in their branch
        if ($this->isAdmin($user)) {
            $dept = Department::where('slug', $deptSlug)
                ->where('branch_id', $user->branch_id)
                ->first();

            if (!$dept) {
                abort(403, 'This department is not in your branch.');
            }

            $this->setDepartmentContext($request, $deptSlug, $dept);
            return $next($request);
        }

        // Manager can access all departments in same category
        if ($this->isManager($user)) {
            $userDept = $user->department;
            $targetDept = Department::where('slug', $deptSlug)->first();

            if (!$targetDept) {
                abort(404, 'Department not found.');
            }

            // Same category AND same branch
            if ($targetDept->category_id === $userDept->category_id
                && $targetDept->branch_id === $user->branch_id) {
                $this->setDepartmentContext($request, $deptSlug, $targetDept);
                return $next($request);
            }

            abort(403, 'You can only access departments in your category.');
        }

        // Supervisor/Staff can only access their own department
        $userDept = $user->department;

        if (!$userDept) {
            abort(403, 'You are not assigned to any department.');
        }

        if ($userDept->slug !== $deptSlug) {
            abort(403, 'You can only access your own department.');
        }

        $this->setDepartmentContext($request, $deptSlug, $userDept);
        return $next($request);
    }

    /**
     * Check if user is Super Admin
     */
    private function isSuperAdmin($user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Check if user is Admin
     */
    private function isAdmin($user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Check if user is Manager
     */
    private function isManager($user): bool
    {
        return $user->hasRole('Manager');
    }

    /**
     * Set department context for the request
     */
    private function setDepartmentContext(Request $request, string $slug, ?Department $dept = null): void
    {
        if (!$dept) {
            $dept = Department::where('slug', $slug)->first();
        }

        if ($dept) {
            $request->attributes->set('current_department', $dept);
            $request->attributes->set('current_department_id', $dept->id);
            session(['current_department_id' => $dept->id]);
        }
    }
}
```

### Registration

```php
// bootstrap/app.php (Laravel 11)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'department.scope' => \App\Http\Middleware\DepartmentScopeMiddleware::class,
    ]);
})

// OR Kernel.php (Laravel 10)
protected $middlewareAliases = [
    'department.scope' => \App\Http\Middleware\DepartmentScopeMiddleware::class,
];
```

---

## 2. RoleLevelMiddleware (NEW)

### Purpose
Checks if user has minimum role level for an action.

### Implementation

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleLevelMiddleware
{
    private const ROLE_LEVELS = [
        'Super Admin' => 5,
        'Admin' => 4,
        'Manager' => 3,
        'Supervisor' => 2,
        'Staff' => 1,
    ];

    public function handle(Request $request, Closure $next, int $minLevel): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userLevel = $this->getUserLevel($user);

        if ($userLevel < $minLevel) {
            abort(403, 'Insufficient role level for this action.');
        }

        return $next($request);
    }

    private function getUserLevel($user): int
    {
        foreach (self::ROLE_LEVELS as $role => $level) {
            if ($user->hasRole($role)) {
                return $level;
            }
        }
        return 0;
    }
}
```

### Usage

```php
// Require Manager level (3) or higher
Route::get('/reports', ReportsController::class)
    ->middleware('role.level:3');

// Require Admin level (4) or higher
Route::get('/manage-users', UsersController::class)
    ->middleware('role.level:4');
```

---

## 3. Update Existing Middleware

### 3.1 SetBranchContext (Modify)

Add department context setting:

```php
// In SetBranchContext::handle()

// After setting branch context, also set department
if ($user->department_id) {
    $request->attributes->set('current_department', $user->department);
    session(['current_department_id' => $user->department_id]);
}
```

### 3.2 ValidateSalesDepartmentContext (Keep but Simplify)

```php
// This can now delegate to DepartmentScopeMiddleware
// Or be removed entirely if routes use department.scope

public function handle(Request $request, Closure $next)
{
    // Simply call the new middleware logic
    return app(DepartmentScopeMiddleware::class)->handle($request, $next);
}
```

---

## 4. Middleware Stack Order

```php
// For department-scoped routes:
Route::middleware([
    'auth',              // 1. Must be logged in
    'setBranchContext',  // 2. Set branch context
    'department.scope',  // 3. Validate department access
])->group(function () {
    // Routes here
});
```

---

## 5. Route Examples

### Production Routes

```php
Route::prefix('branch-dashboard/production/{deptSlug}')
    ->middleware(['auth', 'setBranchContext', 'department.scope'])
    ->group(function () {
        Route::get('/daily-produce', [DailyProduceController::class, 'index'])
            ->name('production.daily-produce');

        Route::get('/recipes', [RecipeController::class, 'index'])
            ->name('production.recipes');

        // Manager+ only
        Route::get('/reports', [ProductionReportsController::class, 'index'])
            ->name('production.reports')
            ->middleware('role.level:3');
    });
```

### Sales Routes

```php
Route::prefix('branch-dashboard/sales/{deptSlug}')
    ->middleware(['auth', 'setBranchContext', 'department.scope'])
    ->group(function () {
        Route::get('/pos', [POSController::class, 'index'])
            ->name('sales.pos');

        Route::get('/my-sales', [MySalesController::class, 'index'])
            ->name('sales.my-sales');

        // Supervisor+ only
        Route::get('/analytics', [SalesAnalyticsController::class, 'index'])
            ->name('sales.analytics')
            ->middleware('role.level:2');
    });
```

### Admin Routes (No Department Scope)

```php
Route::prefix('branch-dashboard/admin')
    ->middleware(['auth', 'setBranchContext', 'role.level:4'])
    ->group(function () {
        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/departments', [DepartmentsController::class, 'index']);
        Route::get('/settings', [SettingsController::class, 'index'])
            ->middleware('role.level:5'); // Super Admin only
    });
```

---

## 6. Error Messages

| Scenario | HTTP Code | Message |
|----------|-----------|---------|
| Not logged in | 302 | Redirect to login |
| No department assigned | 403 | "You are not assigned to any department." |
| Wrong department | 403 | "You can only access your own department." |
| Wrong category (Manager) | 403 | "You can only access departments in your category." |
| Wrong branch (Admin) | 403 | "This department is not in your branch." |
| Insufficient role level | 403 | "Insufficient role level for this action." |
| Department not found | 404 | "Department not found." |

---

## 7. Testing Scenarios

### Test Cases

```php
// Test: Staff can only access own department
public function test_staff_cannot_access_other_department()
{
    $user = User::factory()->create(['department_id' => $kitchenDept->id]);
    $user->assignRole('Staff');

    $this->actingAs($user)
        ->get('/branch-dashboard/production/gelato-production/daily-produce')
        ->assertStatus(403);
}

// Test: Manager can access all departments in category
public function test_manager_can_access_category_departments()
{
    $user = User::factory()->create(['department_id' => $kitchenDept->id]);
    $user->assignRole('Manager');

    $this->actingAs($user)
        ->get('/branch-dashboard/production/gelato-production/daily-produce')
        ->assertStatus(200);
}

// Test: Manager cannot access different category
public function test_manager_cannot_access_other_category()
{
    $user = User::factory()->create(['department_id' => $kitchenDept->id]); // Production
    $user->assignRole('Manager');

    $this->actingAs($user)
        ->get('/branch-dashboard/sales/till/pos')
        ->assertStatus(403);
}

// Test: Admin can access all departments in branch
public function test_admin_can_access_all_departments()
{
    $user = User::factory()->create(['branch_id' => $branch->id]);
    $user->assignRole('Admin');

    $this->actingAs($user)
        ->get('/branch-dashboard/production/kitchen/daily-produce')
        ->assertStatus(200);

    $this->actingAs($user)
        ->get('/branch-dashboard/sales/till/pos')
        ->assertStatus(200);
}

// Test: Super Admin can access everything
public function test_super_admin_bypasses_all()
{
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $this->actingAs($user)
        ->get('/branch-dashboard/production/kitchen/daily-produce')
        ->assertStatus(200);
}
```

---

## Summary

| Middleware | Purpose | Applied To |
|------------|---------|------------|
| `department.scope` | Validate department access | All dept-scoped routes |
| `role.level:N` | Require minimum role level | Protected actions |
| `setBranchContext` | Set branch context | All branch routes |
| `auth` | Require authentication | All protected routes |
