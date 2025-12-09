# Production Audit Service - Quick Reference Guide

## Service Location
`app/Services/ProductionAuditService.php`

## Basic Usage Pattern

### Step 1: Import the Service
```php
use App\Services\ProductionAuditService;
```

### Step 2: Get Current Actor
```php
// In Livewire components or controllers
$employee = Auth::guard('employees')->user();
$user = Auth::user(); // Super admin fallback

$actor = $employee ?? $user;
```

### Step 3: Log the Action
```php
// Simple production event
ProductionAuditService::logBatchProduced($actor, $batch, $notes);

// Quantity changes
ProductionAuditService::logVariance($actor, $dailyProduce, $expected, $actual, $reason);

// Waste tracking
ProductionAuditService::logWaste($actor, $dailyProduce, $qty, $reason);
```

## Complete Method Reference

### Production Batch Logging
```php
ProductionAuditService::logBatchProduced(
    Model $causer,                      // Employee or User
    ProductionRecord $batch,            // The batch being logged
    ?string $notes = null               // Optional notes
): AuditLog
```

### Quality Adjustments
```php
ProductionAuditService::logQualityAdjustment(
    Model $causer,
    ProductionRecord $batch,
    $previousApproved,                  // int|float
    $newApproved,                       // int|float
    $previousRejected,                  // int|float
    $newRejected,                       // int|float
    ?string $reason = null
): AuditLog
```

### Dispatch Operations
```php
ProductionAuditService::logDispatch(
    Model $causer,
    ProductionRecord $batch,
    $sentOut,                           // int|float
    $forOrder,                          // int|float
    ?string $notes = null
): AuditLog
```

### Opening Quantity
```php
ProductionAuditService::logOpeningQuantity(
    Model $causer,
    DailyProduce $dailyProduce,
    $openingQty,                        // int|float
    ?string $reason = null
): AuditLog
```

### Waste Tracking (MANDATORY REASON)
```php
ProductionAuditService::logWaste(
    Model $causer,
    DailyProduce $dailyProduce,
    $wasteQty,                          // int|float
    string $reason                      // REQUIRED - no null allowed
): AuditLog
```

### Recipe Creation
```php
ProductionAuditService::logRecipeCreated(
    Model $causer,
    Recipe $recipe,
    ?array $ingredients = null          // Recipe ingredients array
): AuditLog
```

### Recipe Updates
```php
ProductionAuditService::logRecipeUpdated(
    Model $causer,
    Recipe $recipe,
    array $changes = [],                // What changed
    ?string $reason = null
): AuditLog
```

### Recipe Deletion (MANDATORY REASON)
```php
ProductionAuditService::logRecipeDeleted(
    Model $causer,
    Recipe $recipe,
    string $reason                      // REQUIRED - explain why deleted
): AuditLog
```

### Shift Closing
```php
ProductionAuditService::logShiftClosing(
    Model $causer,
    Model $shift,                       // Shift model
    array $closingData = []             // Financial summary
): AuditLog
```

### Material Usage
```php
ProductionAuditService::logMaterialUsage(
    Model $causer,
    Model $materialRecord,
    $quantityUsed,                      // int|float
    ?string $reason = null
): AuditLog
```

### Callback Approval
```php
ProductionAuditService::logCallbackApproval(
    Model $causer,                      // The approver
    Model $callback,                    // Callback record
    string $action,                     // 'approve' or 'reject'
    ?string $notes = null
): AuditLog
```

### Production Variance
```php
ProductionAuditService::logVariance(
    Model $causer,
    DailyProduce $dailyProduce,
    $expected,                          // int|float
    $actual,                            // int|float
    ?string $reason = null
): AuditLog
```

## Implementation Checklist

For each component requiring audit:

- [ ] Add import: `use App\Services\ProductionAuditService;`
- [ ] Get current actor at top of method
- [ ] Call appropriate audit method after successful operation
- [ ] Provide meaningful descriptions/reasons
- [ ] Test by checking `audit_logs` table

## Common Implementation Patterns

### Pattern 1: Simple Create/Update
```php
public function saveRecipe()
{
    $employee = Auth::guard('employees')->user();
    
    // ... validation ...
    
    $recipe = Recipe::create($data);
    
    // Log it
    ProductionAuditService::logRecipeCreated(
        $employee ?? Auth::user(),
        $recipe,
        $ingredientsList
    );
}
```

### Pattern 2: With Old/New Comparison
```php
public function updateQuantities()
{
    $employee = Auth::guard('employees')->user();
    $oldValue = $model->field;
    
    // ... update ...
    
    $newValue = $model->field;
    
    if ($oldValue != $newValue) {
        ProductionAuditService::logVariance(
            $employee ?? Auth::user(),
            $model,
            $oldValue,
            $newValue,
            "Updated from {$oldValue} to {$newValue}"
        );
    }
}
```

### Pattern 3: Conditional Waste Logging
```php
public function recordWaste()
{
    $employee = Auth::guard('employees')->user();
    
    // Get waste reason from form/modal
    $reason = $this->wasteReason;
    
    if ($this->wasteQuantity > 0) {
        ProductionAuditService::logWaste(
            $employee ?? Auth::user(),
            $this->dailyProduce,
            $this->wasteQuantity,
            $reason  // MUST be non-empty
        );
    }
}
```

### Pattern 4: Batch Operations
```php
public function bulkApproveCallbacks()
{
    $employee = Auth::guard('employees')->user();
    
    foreach ($this->selectedCallbacks as $callbackId) {
        $callback = Callback::find($callbackId);
        
        // ... approval logic ...
        
        ProductionAuditService::logCallbackApproval(
            $employee ?? Auth::user(),
            $callback,
            'approve',
            $this->approvalNotes
        );
    }
}
```

## Data Stored in Audit Log

Each audit log entry captures:
- **action**: String identifier (e.g., 'record_production_batch')
- **causer_type**: 'App\Models\Employee' or 'App\Models\User'
- **causer_id**: ID of the actor
- **auditable_type**: Type of model affected
- **auditable_id**: ID of affected model
- **description**: Human-readable description
- **details**: JSON object with action-specific metadata
- **logged_at**: Timestamp
- **branch_id**: Multi-tenant context
- **ip_address**: Source IP
- **user_agent**: Browser/client info

## Querying Audit Logs

### Find all batch production logs
```php
AuditLog::where('action', 'record_production_batch')->get();
```

### Find all actions by an employee
```php
AuditLog::where('causer_type', 'App\Models\Employee')
    ->where('causer_id', $employeeId)
    ->get();
```

### Find waste logs
```php
AuditLog::where('action', 'record_production_waste')->get();
```

### Find logs for a specific recipe
```php
AuditLog::where('auditable_type', 'App\Models\Recipe')
    ->where('auditable_id', $recipeId)
    ->get();
```

## Important Notes

### Mandatory vs Optional
- **Mandatory Reason:** Waste, Recipe Deletion, Callback Approval
- **Optional Reason:** Most other operations (use empty string if none)

### Transaction Safety
- All logging happens within DB transactions
- If operation fails, audit log also rolls back
- No orphaned audit entries

### Performance
- Audit logging adds ~1-2ms per operation (negligible)
- No impact on UI responsiveness
- Safe to log in tight loops

### Employee vs Super-Admin Context
Always use pattern:
```php
$actor = Auth::guard('employees')->user() ?? Auth::user();
```
This ensures:
- Employees logged correctly
- Super-admins logged as fallback
- No null actor issues

## Related Services

- **AuditService** (`app/Services/AuditService.php`): Generic audit for all models
- **AuditLog Model** (`app/Models/AuditLog.php`): Database model
- **Audit UI** (`app/Livewire/BranchDashboard/AuditManagement/`): View audit logs

## Error Handling

Audit logging is non-blocking:
```php
try {
    ProductionAuditService::log...();
} catch (\Exception $e) {
    logger()->error('Audit log failed: ' . $e->getMessage());
    // Operation continues - audit failure doesn't block user action
}
```

---

**Last Updated:** December 8, 2025  
**Author:** Implementation Guide
