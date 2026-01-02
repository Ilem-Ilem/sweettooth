# Workflow Enforcement (Middleware & Guards)

## Overview

The Sales workflow is enforced through multiple layers:
1. **ValidateSalesWorkflow Middleware** - Route-level enforcement
2. **SalesWorkflowService** - State management
3. **SalesStockVerificationService** - Stock opening validation
4. **HeaderClockInOut** - Clock out redirect

## ValidateSalesWorkflow Middleware

**File**: `app/Http/Middleware/ValidateSalesWorkflow.php`

### Route Protection

Applied to sales routes in `routes/branch-route.php`:

```php
Route::prefix('sales-dashboard')->middleware([
    'validate-sales-department-context'
])->group(function () {

    // POS routes - requires stock_opening state
    Route::prefix('pos')->middleware(['validate-sales-workflow'])->group(function () {
        Route::get('/{salesDeptSlug?}', Pos\Index::class)->name('index');
    });

    // Stock Opening - requires stock_opening state
    Route::prefix('stock-opening')->middleware(['validate-sales-workflow'])->group(function () {
        Route::get('/{salesDeptSlug?}', StockOpening\Index::class)->name('index');
    });

    // Shift Closing - requires shift_closing state
    Route::prefix('shift-closing')->middleware(['validate-sales-workflow'])->group(function () {
        Route::get('/{salesDeptSlug?}', ShiftClosing\Index::class)->name('index');
    });
});
```

### Middleware Logic (Lines 26-66)

```php
public function handle(Request $request, Closure $next): Response
{
    // 1. Super admins bypass
    if (is_super_admin() || can_access_all_branches()) {
        return $next($request);
    }

    // 2. Only apply to sales employees
    if (!$this->isSalesEmployee($employee)) {
        return $next($request);
    }

    // 3. Get required state for this route
    $requiredState = $this->getRequiredWorkflowState($currentRoute);

    // 4. Get active shift
    $activeShift = $this->getActiveShift($employee);
    if (!$activeShift) {
        return $this->redirectToClockIn($request);
    }

    // 5. Get current workflow state
    $currentState = $this->workflowService->getCurrentState($employee->id, $activeShift->id);

    // 6. Check if user can access this state
    if (!$this->canAccessState($currentState, $requiredState)) {
        return $this->redirectToCorrectStep($request, $currentState, $employee);
    }

    return $next($request);
}
```

### State Hierarchy Check (Lines 121-136)

```php
protected function canAccessState(string $currentState, string $requiredState): bool
{
    $stateHierarchy = [
        'clock_in' => 1,
        'stock_opening' => 2,
        'pos' => 3,
        'clock_out' => 4,
        'shift_closing' => 5,
        'completed' => 6
    ];

    $currentLevel = $stateHierarchy[$currentState] ?? 0;
    $requiredLevel = $stateHierarchy[$requiredState] ?? 0;

    // User can only access if at or past required level
    return $currentLevel >= $requiredLevel;
}
```

### Redirect Logic (Lines 163-187)

When user is at wrong state, redirect to correct step:

```php
$redirectRoute = match($currentState) {
    'clock_in' => route('branch-dashboard.clock-in-board.today', [...]),
    'stock_opening' => route('branch-dashboard.sales-dashboard.stock-opening.index', [...]),
    'pos' => route('branch-dashboard.sales-dashboard.pos.index', [...]),
    'shift_closing' => route('branch-dashboard.sales-dashboard.shift-closing.index', [...]),
    default => route('branch-dashboard.index', [...])
};

return redirect($redirectRoute)->with('warning',
    'Please complete the required workflow steps in order.');
```

## SalesWorkflowService

**File**: `app/Services/SalesWorkflowService.php`

### Key Methods

#### getCurrentState (Lines 35-69)
Determines current workflow state:

```php
public function getCurrentState(int $employeeId, int $shiftId): string
{
    $shift = Shift::find($shiftId);

    // Completed: clocked out AND shift closing done
    if ($shift->clock_out && $this->isShiftClosingCompleted($shift)) {
        return 'completed';
    }

    // Shift Closing: clocked out but shift closing NOT done
    if ($shift->clock_out && !$this->isShiftClosingCompleted($shift)) {
        return 'shift_closing';
    }

    // Check stock verification for POS access
    // ... returns 'stock_opening' or 'pos'
}
```

#### isShiftClosingCompleted (Lines 227-232)
Checks metadata flag:

```php
protected function isShiftClosingCompleted(Shift $shift): bool
{
    $metadata = $shift->metadata ?? [];
    return isset($metadata['shift_closing_completed']) &&
           $metadata['shift_closing_completed'] === true;
}
```

#### completeStep (Lines 156-191)
Marks workflow step as complete:

```php
public function completeStep(int $employeeId, int $shiftId, string $step): bool
{
    $shift = Shift::find($shiftId);

    $metadata = $shift->metadata ?? [];
    $metadata['workflow_steps'][$step] = [
        'completed_by' => $employeeId,
        'completed_at' => now()->toIso8601String()
    ];
    $shift->metadata = $metadata;
    $shift->save();

    return true;
}
```

## RequireActiveShift Middleware

**File**: `app/Http/Middleware/RequireActiveShift.php`

Ensures all work functions require an active shift:

```php
public function handle(Request $request, Closure $next): Response
{
    $employee = auth()->user();

    $activeShift = Shift::where('employee_id', $employee->id)
        ->where('shift_date', Carbon::today())
        ->where('status', 'active')
        ->first();

    if (!$activeShift) {
        // No active shift - redirect to shift selection
        return redirect()->route('branch-dashboard.select_shift', [...])
            ->with('warning', 'Please select a shift to continue.');
    }

    return $next($request);
}
```

## Enforcement Summary

```
┌─────────────────────────────────────────────────────────────┐
│                    ROUTE REQUEST                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  RequireActiveShift Middleware                              │
│  - Is there an active shift?                                │
│  - No → Redirect to shift selection                         │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  ValidateSalesWorkflow Middleware                           │
│  - Is user a sales employee?                                │
│  - What state should they be in?                            │
│  - Are they in the correct state?                           │
│  - No → Redirect to correct workflow step                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  SalesWorkflowService                                       │
│  - Determine current state from shift data                  │
│  - Check metadata flags                                     │
│  - Provide redirect routes                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    ALLOW ACCESS                             │
└─────────────────────────────────────────────────────────────┘
```

## No Enforcement for Production

Production routes do NOT have `validate-sales-workflow` middleware:

```php
// routes/branch-route.php
Route::prefix('production')->name('production.')->group(function () {
    // Only require_active_shift from parent group
    // NO validate-sales-workflow!

    Route::prefix('shift-closing')->name('shift-closing.')->group(function () {
        Route::get('/{deptSlug}', ShiftClosing\Index::class)->name('index');
    });
});
```

This is why production employees can clock out directly without being forced to complete shift closing.
