# Sales Shift Closing (Stock & Cash Reconciliation)

## Overview

The Sales Shift Closing page is **mandatory** for sales employees. It combines:
1. **Stock Closing** - Update closing quantities for all products
2. **Cash Reconciliation** - Verify actual vs expected cash/POS/transfer amounts
3. **Sales Summary** - Review day's sales performance

## Key File

**File**: `app/Livewire/BranchDashboard/SalesDashboard/ShiftClosing/Index.php`
**View**: `resources/views/livewire/branch-dashboard/sales-dashboard/shift-closing/index.blade.php`
**Route**: `branch-dashboard.sales-dashboard.shift-closing.index`

## Stock Closing (Lines 117-180)

### What It Does
- Loads all `ProductStock` records for the current shift date
- Calculates sold quantities from `SaleItem` records
- Computes expected closing: `opening + additions - sold`
- Tracks variance between expected and actual closing
- Flags expired products

### Data Structure
```php
$closingStocks[] = [
    'product_id' => $stock->product_id,
    'product_name' => $stock->product->name,
    'opening_quantity' => $stock->opening_quantity,
    'addition_quantity' => $stock->addition_quantity,
    'sold_quantity' => $soldQuantity,
    'expected_closing' => $expectedClosing,
    'actual_closing' => $actualClosing,  // User can edit this
    'variance' => $variance,
    'expiry_date' => $stock->expiry_date,
    'is_expired' => $isExpired,
    'notes' => $stock->notes,
];
```

## Cash Reconciliation (Lines 277-326)

### What It Does
- Sums all completed `Payment` records by payment method
- Compares expected amounts vs actual amounts entered by user
- Calculates variance for each payment method
- Flags variances exceeding threshold (max of ₦100 or 1%)

### Data Structure
```php
$this->cashReconciliation = [
    'expected_cash' => $expectedCash,
    'expected_pos' => $expectedPos,
    'expected_transfer' => $expectedTransfer,
    'actual_cash' => $this->actualCash,
    'actual_pos' => $this->actualPos,
    'actual_transfer' => $this->actualTransfer,
    'cash_variance' => $cashVariance,
    'pos_variance' => $posVariance,
    'transfer_variance' => $transferVariance,
    'cash_requires_reason' => abs($cashVariance) > $cashThreshold,
    'pos_requires_reason' => abs($posVariance) > $posThreshold,
    'transfer_requires_reason' => abs($transferVariance) > $transferThreshold,
];
```

## Validation Checks (Lines 379-393)

Users **cannot save** shift closing without providing variance reasons when required:

```php
// Cash variance requires reason if exceeds threshold
if ($this->cashReconciliation['cash_requires_reason'] && empty($this->cashVarianceReason)) {
    $this->toast()->error('Cash variance reason required...')->send();
    return;
}

// POS variance requires reason
if ($this->cashReconciliation['pos_requires_reason'] && empty($this->posVarianceReason)) {
    $this->toast()->error('POS variance reason required...')->send();
    return;
}

// Transfer variance requires reason
if ($this->cashReconciliation['transfer_requires_reason'] && empty($this->transferVarianceReason)) {
    $this->toast()->error('Transfer variance reason required...')->send();
    return;
}
```

## Save Shift Closing (Lines 372-511)

When user clicks "Close Shift", the following happens:

### 1. Update ProductStock Records
```php
foreach ($this->closingStocks as $stockData) {
    $productStock->closing_quantity = $stockData['actual_closing'];
    $productStock->quantity_sold = $stockData['sold_quantity'];
    $productStock->save();

    // Create callback for variances
    if (abs($stockData['variance']) > 0) {
        Callback::create([
            'reason' => $stockData['variance'] < 0 ? 'shortage' : 'excess',
            // ...
        ]);
    }
}
```

### 2. Update Shift Status to 'closed'
```php
$shift->status = 'closed';
$shift->clock_out = now();
$shift->workflow_state = 'completed';

// CRITICAL: Mark shift closing as completed
$metadata['shift_closing_completed'] = true;
$shift->metadata = $metadata;
$shift->save();
```

### 3. Complete Workflow Step
```php
$workflowService = app(SalesWorkflowService::class);
$workflowService->completeStep(
    auth()->id(),
    $this->currentShiftId,
    'shift_closing'
);
```

## Why Users Can't Skip This

1. **Shift stays 'active'** until `saveShiftClosing()` is called
2. **Workflow state stays 'shift_closing'** until completion
3. **Middleware blocks access** to other sales pages while in shift_closing state
4. **Metadata flag** `shift_closing_completed` must be true for shift to be considered complete

## Flow Diagram

```
User redirected to Shift Closing
              ↓
    ┌─────────────────────────────┐
    │   SHIFT CLOSING PAGE        │
    ├─────────────────────────────┤
    │  1. Review Stock Closing    │
    │     - Update actual closing │
    │     - Add variance notes    │
    ├─────────────────────────────┤
    │  2. Cash Reconciliation     │
    │     - Enter actual cash     │
    │     - Enter actual POS      │
    │     - Enter actual transfer │
    │     - Explain variances     │
    ├─────────────────────────────┤
    │  3. Review Sales Summary    │
    └─────────────────────────────┘
              ↓
       Click "Close Shift"
              ↓
   Validation passes? ──No──→ Show error, stay on page
              │
             Yes
              ↓
   Update ProductStock records
   Create Callbacks for variances
   Update Shift: status='closed'
   Set metadata['shift_closing_completed']=true
   workflow_state='completed'
              ↓
       SHIFT COMPLETE
```
