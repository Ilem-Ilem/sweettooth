# Production Workflow States

## Overview

Unlike the Sales module, **Production does NOT have a mandatory workflow** enforced by middleware. Production employees can:
- Clock in
- Perform production work (recipes, batches, daily produce)
- Clock out directly without shift closing

## Current Implementation

### Clock Out Behavior for Production

**File**: `app/Livewire/BranchDashboard/HeaderClockInOut.php`

```php
public function clockOut()
{
    $isSalesEmployee = $this->checkIfSalesEmployee();

    if ($isSalesEmployee && !is_super_admin() && !can_access_all_branches()) {
        // Sales employees → redirect to shift closing
        $this->currentShift->status = 'active';
        $this->currentShift->workflow_state = 'shift_closing';
        // ... redirect to shift closing
    } else {
        // Production employees → close immediately
        $this->currentShift->status = 'closed';
        $this->currentShift->workflow_state = 'completed';
    }
}
```

**Key Point**: Production employees (department category != 'sales') have their shift closed immediately upon clock out.

## Production Workflow (No Enforcement)

```
Clock In → Production Work → Clock Out → Completed
    ↓            ↓              ↓           ↓
 Shift      Batches/        Shift       [Optional:
 Created    Recipes         Closed       Visit Shift
            Daily Produce               Closing Page]
```

## Available Production Functions

During an active shift, production employees can access:

| Route | Purpose |
|-------|---------|
| `production.product-types` | Manage product types |
| `production.products` | Manage products |
| `production.request.index` | View/create production requests |
| `production.daily-produce.index` | Manage daily produce batches |
| `production.recipes.index` | View/manage recipes |
| `production.shift-closing.index` | **Optional** shift closing |
| `production.callbacks.index` | View callbacks |
| `production.reports.*` | View various reports |

## Route Configuration

**File**: `routes/branch-route.php` (lines 162-228)

```php
Route::prefix('production')->name('production.')->group(function () {
    // NO validate-sales-workflow middleware!
    // Only require_active_shift from parent group

    Route::prefix('shift-closing')->name('shift-closing.')->group(function () {
        Route::get('/{deptSlug}', ShiftClosing\Index::class)->name('index');
    });

    // ... other production routes
});
```

Notice: No `validate-sales-workflow` middleware is applied to production routes.

## State Comparison: Sales vs Production

| State | Sales | Production |
|-------|-------|------------|
| `clock_in` | Creates active shift | Creates active shift |
| `stock_opening` | Required before POS | N/A |
| `pos` | Production Work | Process sales | Create batches/recipes |
| `clock_out` | Triggers shift_closing | Directly closes shift |
| `shift_closing` | **Mandatory** | **Optional** |
| `completed` | After shift closing | Immediate on clock out |

## Why Production Differs

1. **No inventory tracking at POS level** - Production tracks raw materials and batch outputs, not individual sales
2. **Different reconciliation needs** - Production focuses on yield vs waste, not cash handling
3. **Batch-based workflow** - Production closes batches individually, not a daily shift summary
4. **Material efficiency** - Already tracked per-batch in `ProductionRecord`
