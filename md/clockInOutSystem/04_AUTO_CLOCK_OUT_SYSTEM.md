# 03_ACTIVE_SHIFT_ENFORCEMENT.md

## Active Shift Enforcement System

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Overview

The Active Shift Enforcement system ensures that non-super-admin employees cannot access work functions without having an active shift. This critical security feature prevents unauthorized work sessions and ensures proper time tracking compliance.

---

## 1. RequireActiveShift Middleware

### Middleware Architecture

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Shift;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveShift
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip validation for specific routes and user types
        if ($this->shouldSkipValidation($request)) {
            return $next($request);
        }

        $employee = auth()->user();

        // Validate user exists and is not super admin
        if (!$employee || is_super_admin()) {
            return $next($request);
        }

        // Check for active shift
        $activeShift = $this->getActiveShift($employee);

        if (!$activeShift) {
            return $this->handleInactiveShift($request, $employee);
        }

        // Add shift context to request for downstream use
        $request->merge(['active_shift' => $activeShift]);

        return $next($request);
    }

    /**
     * Determine if validation should be skipped
     */
    protected function shouldSkipValidation(Request $request): bool
    {
        // Skip for unauthenticated requests
        if (!auth()->check()) {
            return true;
        }

        // Skip for super admins
        if (is_super_admin()) {
            return true;
        }

        // Skip for API routes that don't require shift validation
        $apiRoutesToSkip = [
            'api/user/profile',
            'api/notifications',
            'api/logout'
        ];

        if ($request->is($apiRoutesToSkip)) {
            return true;
        }

        // Skip for shift-related routes (to prevent infinite loops)
        $shiftRoutes = [
            'branch-dashboard.select_shift',
            'livewire/auth/shift'
        ];

        if ($request->routeIs($shiftRoutes)) {
            return true;
        }

        return false;
    }

    /**
     * Get active shift for employee
     */
    protected function getActiveShift($employee): ?Shift
    {
        return Shift::where('employee_id', $employee->id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->with('branch') // Eager load for performance
            ->first();
    }

    /**
     * Handle inactive shift scenario
     */
    protected function handleInactiveShift(Request $request, $employee): Response
    {
        // Log the access attempt
        Log::info('Unauthorized access attempt - no active shift', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'route' => $request->route()?->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String()
        ]);

        // For AJAX requests, return JSON error
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'error' => 'Active shift required',
                'message' => 'Please clock in to start your shift before accessing work functions.',
                'redirect' => route('branch-dashboard.select_shift', [
                    'b_id' => $request->query('b_id') ?? current_branch_id()
                ])
            ], 403);
        }

        // For regular requests, redirect to shift selection
        return redirect()->route('branch-dashboard.select_shift', [
            'b_id' => $request->query('b_id') ?? current_branch_id()
        ])->with('error', 'Please clock in to start your shift before accessing work functions.');
    }
}
```

---

## 2. Route Protection Strategy

### Protected Route Groups

```php
// routes/web.php
Route::middleware(['auth', 'setBranchContext', 'branch'])->group(function () {

    // Public routes (no shift required)
    Route::get('/select-shift', [ShiftController::class, 'index'])->name('select_shift');
    Route::post('/clock-in', [ShiftController::class, 'clockIn'])->name('clock_in');
    Route::post('/clock-out', [ShiftController::class, 'clockOut'])->name('clock_out');

    // Protected routes (require active shift)
    Route::middleware(['require_active_shift'])->group(function () {

        // Dashboard access
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Inventory management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/items', [InventoryController::class, 'items'])->name('items');
            Route::get('/purchases', [InventoryController::class, 'purchases'])->name('purchases');
            Route::get('/stocks', [InventoryController::class, 'stocks'])->name('stocks');
        });

        // Production management
        Route::prefix('production')->name('production.')->group(function () {
            Route::get('/recipes', [ProductionController::class, 'recipes'])->name('recipes');
            Route::get('/daily-produce', [ProductionController::class, 'dailyProduce'])->name('daily_produce');
        });

        // Sales operations (additional validation via ValidateSalesWorkflow)
        Route::prefix('sales-dashboard')->name('sales-dashboard.')->group(function () {
            Route::middleware(['validate-sales-workflow'])->group(function () {
                Route::get('/pos', [SalesController::class, 'pos'])->name('pos.index');
                Route::get('/shift-closing', [SalesController::class, 'shiftClosing'])->name('shift-closing.index');
            });
        });

        // Department-specific operations
        Route::get('/department/{deptSlug}', [DepartmentController::class, 'show'])->name('department.show');
    });
});
```

### Conditional Route Protection

```php
// For routes that need conditional protection based on department
Route::middleware(['require_active_shift'])->get('/special-route', function () {
    // This route requires active shift for all non-super-admin users
    return view('special-page');
});

// For department-specific routes
Route::get('/department/{deptSlug}/manage', [DepartmentController::class, 'manage'])
    ->middleware(['require_active_shift'])
    ->name('department.manage');
```

---

## 3. Performance Optimization

### Database Query Optimization

```php
<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ShiftValidationService
{
    /**
     * Get active shift with caching
     */
    public function getActiveShift(int $employeeId, ?int $branchId = null): ?Shift
    {
        $cacheKey = "active_shift_{$employeeId}_" . Carbon::today()->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function () use ($employeeId, $branchId) {
            $query = Shift::where('employee_id', $employeeId)
                ->where('shift_date', Carbon::today())
                ->where('status', 'active');

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            return $query->with(['branch:id,name', 'employee:id,name'])->first();
        });
    }

    /**
     * Clear shift cache for employee
     */
    public function clearShiftCache(int $employeeId): void
    {
        $cacheKey = "active_shift_{$employeeId}_" . Carbon::today()->format('Y-m-d');
        Cache::forget($cacheKey);
    }

    /**
     * Bulk cache invalidation for branch
     */
    public function clearBranchShiftCache(int $branchId): void
    {
        // This would require tracking which employees belong to which branch
        // Implementation depends on your user-branch relationship
    }
}
```

### Request Lifecycle Optimization

```php
// In Shift model - auto-clear cache on changes
class Shift extends Model
{
    protected static function booted()
    {
        static::saved(function ($shift) {
            $cacheKey = "active_shift_{$shift->employee_id}_" . $shift->shift_date->format('Y-m-d');
            Cache::forget($cacheKey);
        });

        static::deleted(function ($shift) {
            $cacheKey = "active_shift_{$shift->employee_id}_" . $shift->shift_date->format('Y-m-d');
            Cache::forget($cacheKey);
        });
    }
}
```

---

## 4. Error Handling & User Experience

### User-Friendly Error Messages

```php
// In RequireActiveShift middleware
protected function handleInactiveShift(Request $request, $employee): Response
{
    $branchId = $request->query('b_id') ?? current_branch_id();
    $currentTime = Carbon::now();

    // Get available shifts for current time
    $availableShifts = app(ShiftTimingValidator::class)->getAvailableShifts($branchId);

    $message = 'Please clock in to start your shift before accessing work functions.';

    if (!empty($availableShifts)) {
        $shiftNames = collect($availableShifts)->pluck('name')->join(', ');
        $message .= " Available shifts: {$shiftNames}";
    }

    // For AJAX requests
    if ($request->ajax() || $request->expectsJson()) {
        return response()->json([
            'error' => 'Active shift required',
            'message' => $message,
            'available_shifts' => $availableShifts,
            'redirect' => route('branch-dashboard.select_shift', ['b_id' => $branchId])
        ], 403);
    }

    return redirect()->route('branch-dashboard.select_shift', ['b_id' => $branchId])
        ->with('error', $message)
        ->with('available_shifts', $availableShifts);
}
```

### Progressive Enhancement

```php
// In dashboard layout - show shift status proactively
@if(!is_super_admin() && !$hasActiveShift)
<div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-4">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-800">
                <strong>No Active Shift:</strong> Please <a href="{{ route('branch-dashboard.select_shift', ['b_id' => $b_id ?? current_branch_id()]) }}" class="underline hover:text-yellow-900">clock in</a> to access work functions.
            </p>
        </div>
    </div>
</div>
@endif
```

---

## 5. Integration with Existing Systems

### Compatibility with ValidateSalesWorkflow

```php
// The new middleware works alongside existing sales validation
Route::middleware(['require_active_shift', 'validate-sales-workflow'])->group(function () {
    // Routes requiring both general active shift AND sales workflow state
    Route::get('/sales/pos', [SalesController::class, 'pos'])->name('sales.pos');
    Route::get('/sales/closing', [SalesController::class, 'closing'])->name('sales.closing');
});
```

### Exception Handling for Special Cases

```php
// Allow access to certain routes even without active shift
protected function shouldSkipValidation(Request $request): bool
{
    // Skip for emergency routes
    if ($request->routeIs(['emergency.*', 'help.*'])) {
        return true;
    }

    // Skip for read-only operations that don't affect work
    if ($request->routeIs(['reports.view', 'analytics.read'])) {
        return true;
    }

    // Skip for super admin override
    if (is_super_admin()) {
        return true;
    }

    // Skip for API health checks
    if ($request->is('api/health')) {
        return true;
    }

    return false;
}
```

---

## 6. Testing Strategy

### Unit Tests for Middleware

```php
<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RequireActiveShift;
use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class RequireActiveShiftTest extends TestCase
{
    public function test_super_admin_bypasses_validation()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user);

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_employee_without_shift_is_redirected()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $this->actingAs($user);

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return new Response('Should not reach here');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('select_shift', $response->getTargetUrl());
    }

    public function test_employee_with_active_shift_passes_validation()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $branch = Branch::factory()->create();

        // Create active shift
        Shift::create([
            'employee_id' => $user->id,
            'branch_id' => $branch->id,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::now(),
            'status' => 'active'
        ]);

        $this->actingAs($user);

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertTrue($request->has('active_shift'));
    }
}
```

### Integration Tests

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequireActiveShiftIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_access_requires_active_shift()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $branch = Branch::factory()->create();

        // Without active shift - should redirect
        $response = $this->actingAs($user)
            ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

        $response->assertRedirect(route('branch-dashboard.select_shift', ['b_id' => $branch->id]));

        // With active shift - should allow access
        Shift::create([
            'employee_id' => $user->id,
            'branch_id' => $branch->id,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::now(),
            'status' => 'active'
        ]);

        $response = $this->actingAs($user)
            ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

        $response->assertSuccessful();
    }

    public function test_api_requests_return_json_errors()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->actingAs($user)
            ->getJson('/api/dashboard');

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Active shift required',
                'redirect' => true
            ]);
    }
}
```

---

## 7. Monitoring & Alerting

### Security Monitoring

```php
// Log all unauthorized access attempts
Log::warning('Active shift enforcement triggered', [
    'employee_id' => $employee->id,
    'employee_name' => $employee->name,
    'attempted_route' => $request->route()?->getName(),
    'ip_address' => $request->ip(),
    'timestamp' => now()->toIso8601String()
]);
```

### Performance Monitoring

```php
// Track middleware performance
$startTime = microtime(true);
$response = $next($request);
$endTime = microtime(true);

Log::info('RequireActiveShift middleware performance', [
    'duration_ms' => round(($endTime - $startTime) * 1000, 2),
    'employee_id' => $employee->id,
    'route' => $request->route()?->getName(),
    'has_active_shift' => $activeShift !== null
]);
```

### Alert Thresholds

- More than 10 unauthorized access attempts per hour per employee
- Middleware execution time > 100ms
- Cache miss rate > 20%

---

## 8. Rollback Strategy

### Feature Flag Implementation

```php
// config/clock-in-out.php
return [
    'require_active_shift' => env('REQUIRE_ACTIVE_SHIFT', false),
    'skip_routes' => [
        'branch-dashboard.select_shift',
        'livewire/auth/shift',
        'api/health'
    ]
];
```

### Gradual Rollout

```php
// Phase 1: Monitoring only (log violations without blocking)
'mode' => env('SHIFT_ENFORCEMENT_MODE', 'log'), // 'log', 'warn', 'block'

// Phase 2: Warning mode (show messages but allow access)
// Phase 3: Full enforcement (block access)
```

### Emergency Disable

```php
// Console command to temporarily disable enforcement
php artisan shift:enforcement:disable --reason="System maintenance"
php artisan shift:enforcement:enable
```

---

## 9. Security Considerations

### Authorization Bypass Prevention

- Middleware runs before controllers
- No exceptions for specific user roles except super admin
- All routes protected unless explicitly exempted
- Audit trail for all enforcement actions

### Data Integrity

- Active shift validation prevents orphaned records
- Foreign key constraints maintain referential integrity
- Transaction boundaries prevent partial updates
- Rollback capabilities for emergency situations

---

## 10. Migration Path

### Phase 1: Infrastructure Setup
1. Deploy middleware in monitoring mode
2. Set up logging and alerting
3. Train staff on new requirements

### Phase 2: Gradual Enforcement
1. Enable warnings for users without active shifts
2. Monitor impact and user feedback
3. Adjust exempted routes as needed

### Phase 3: Full Enforcement
1. Enable blocking for all non-exempt routes
2. Monitor for any issues
3. Provide support for affected users

---

**Document Information**
- **Prepared By**: Security & Compliance Team
- **Reviewed By**: Development & QA Teams
- **Approved By**: Project Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/03_ACTIVE_SHIFT_ENFORCEMENT.md