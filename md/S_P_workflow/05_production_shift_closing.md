# Production Shift Closing (Optional)

## Overview

Production has a shift closing page, but it is **optional**. Users can access it voluntarily to:
- Review production summary
- Track raw material usage vs planned
- Calculate material efficiency
- Finalize and lock production records

## Key File

**File**: `app/Livewire/BranchDashboard/Production/ShiftClosing/Index.php`
**View**: `resources/views/livewire/branch-dashboard/production/shift-closing/index.blade.php`
**Route**: `branch-dashboard.production.shift-closing.index`

## Production Summary (Lines 118-153)

Loads all `DailyProduce` records for the current shift:

```php
$dailyProduces = DailyProduce::where('shift_id', $this->currentShiftId)
    ->with(['recipe', 'productionRecords'])
    ->get();

$summary[] = [
    'recipe_name' => $produce->recipe->name,
    'batches_planned' => $produce->batches_to_produce,
    'batches_produced' => $produce->productionRecords->count(),
    'total_produced' => $totalProduced,
    'total_approved' => $totalApproved,
    'total_rejected' => $totalRejected,
    'total_sent' => $totalSent,
    'yield_percentage' => ($totalApproved / $totalProduced) * 100,
];
```

## Raw Material Usage (Lines 158-321)

Compares planned vs actual material usage:

### Planned Quantity Calculation
```php
foreach ($recipe->ingredients as $ingredient) {
    $plannedQuantity = $ingredient->getQuantityForBatchSize($batchesProduced);
    $materialUsageByItem[$itemId]['planned_quantity'] += $plannedQuantity;
}
```

### Actual Quantity from Item Requests
```php
$itemRequests = ItemRequest::where('department_id', $this->departmentId)
    ->where('shift', $this->shiftType)
    ->whereDate('request_date', $this->shiftDate)
    ->where('status', 'dispatched')
    ->get();

// Sum actual dispatched quantities
$actualQuantity = $detail->quantity_dispatched;
```

### Variance Tracking
```php
$usage['variance'] = $usage['actual_quantity'] - $usage['planned_quantity'];
$usage['efficiency_percentage'] = ($actual / $planned) * 100;

// Flag significant variances (>5% or >$50 cost variance)
if ($variancePercentage > 5 || abs($usage['cost_variance']) > 50) {
    $significantVariances[] = [...];
}
```

## Save Shift Closing (Lines 336-403)

When user clicks "Close Shift":

### 1. Prepare Summary Data
```php
$shiftSummary = [
    'production_summary' => $this->productionSummary,
    'raw_material_usage' => $this->rawMaterialUsage,
    'material_variances' => $this->materialVariances,
    'material_efficiency' => $this->totalMaterialEfficiency,
    'total_batches_produced' => collect($this->productionSummary)->sum('batches_produced'),
    'user_notes' => $this->notes,
    'closed_at' => now()->toDateTimeString(),
];
```

### 2. Update Shift Status
```php
$shift->status = 'closed';
$shift->clock_out = now();
$shift->notes = json_encode($shiftSummary);
$shift->save();
```

### 3. Lock Production Records
```php
ProductionRecord::whereHas('dailyProduce', function($q) {
    $q->where('shift_id', $this->currentShiftId);
})->update(['is_locked' => true]);
```

### 4. Finalize Daily Produce
```php
DailyProduce::where('shift_id', $this->currentShiftId)
    ->update(['status' => 'finalized']);
```

### 5. Complete Item Requests
```php
ItemRequest::where('department_id', $this->departmentId)
    ->where('status', 'dispatched')
    ->update(['status' => 'completed']);
```

## Key Difference from Sales

| Aspect | Sales | Production |
|--------|-------|------------|
| **Mandatory?** | Yes | No |
| **Triggered by clock out?** | Yes (redirect) | No |
| **Stock closing** | Product closing quantities | N/A |
| **Cash reconciliation** | Yes | N/A |
| **Material tracking** | N/A | Raw material usage |
| **Records locked** | ProductStock | ProductionRecord, DailyProduce |

## When to Use Production Shift Closing

Production shift closing is useful for:
1. **End-of-day reporting** - Review total production output
2. **Material efficiency analysis** - Compare planned vs actual usage
3. **Variance investigation** - Identify waste or unplanned usage
4. **Record finalization** - Lock records to prevent edits
5. **Compliance** - Audit trail for production activities

## TODOs in Code (Future Enhancements)

The code contains TODOs for future implementation:
- Update inventory stocks based on actual material usage
- Create inventory adjustment records for significant variances
- Generate PDF report
- Send notifications to production manager
- Calculate employee productivity metrics
