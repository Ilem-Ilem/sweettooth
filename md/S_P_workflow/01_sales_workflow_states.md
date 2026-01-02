# Sales Workflow States

## Workflow State Hierarchy

The Sales module enforces a strict workflow state hierarchy. Users must complete each step before proceeding to the next.

```php
// From ValidateSalesWorkflow.php:123-130
$stateHierarchy = [
    'clock_in' => 1,
    'stock_opening' => 2,
    'pos' => 3,
    'clock_out' => 4,
    'shift_closing' => 5,
    'completed' => 6
];
```

## State Descriptions

### 1. Clock In (`clock_in`)
- **Entry Point**: User selects shift and clocks in
- **File**: `app/Livewire/Auth/Shift.php`
- **Creates**: New `Shift` record with `status = 'active'`
- **Next State**: `stock_opening`

### 2. Stock Opening (`stock_opening`)
- **Purpose**: Verify opening quantities for all products
- **File**: `app/Livewire/BranchDashboard/SalesDashboard/StockOpening/Index.php`
- **Route**: `branch-dashboard.sales-dashboard.stock-opening.index`
- **Requirement**: All products must have opening quantities verified
- **Next State**: `pos`

### 3. POS (`pos`)
- **Purpose**: Process sales transactions
- **File**: `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php`
- **Route**: `branch-dashboard.sales-dashboard.pos.index`
- **Requirement**: Stock opening must be completed first
- **Next State**: `clock_out` (triggered by user action)

### 4. Clock Out (`clock_out`)
- **Purpose**: End active work period
- **File**: `app/Livewire/BranchDashboard/HeaderClockInOut.php`
- **Key Behavior**: Does NOT close shift, sets `workflow_state = 'shift_closing'`
- **Redirect**: Automatically redirects to shift closing page
- **Next State**: `shift_closing`

### 5. Shift Closing (`shift_closing`)
- **Purpose**: Complete stock closing and cash reconciliation
- **File**: `app/Livewire/BranchDashboard/SalesDashboard/ShiftClosing/Index.php`
- **Route**: `branch-dashboard.sales-dashboard.shift-closing.index`
- **Requirements**:
  - Update closing quantities for all products
  - Reconcile cash, POS, and transfer amounts
  - Provide variance reasons if thresholds exceeded
- **Next State**: `completed`

### 6. Completed (`completed`)
- **Purpose**: Shift fully closed
- **Database State**:
  - `shift.status = 'closed'`
  - `shift.workflow_state = 'completed'`
  - `shift.metadata['shift_closing_completed'] = true`

## State Determination Logic

The current state is determined by `SalesWorkflowService::getCurrentState()`:

```php
// From SalesWorkflowService.php:35-69
public function getCurrentState(int $employeeId, int $shiftId): string
{
    $shift = Shift::find($shiftId);

    // 1. Check if shift is completed (clocked out AND shift closing done)
    if ($shift->clock_out && $this->isShiftClosingCompleted($shift)) {
        return 'completed';
    }

    // 2. Check if clocked out but shift closing not done
    if ($shift->clock_out && !$this->isShiftClosingCompleted($shift)) {
        return 'shift_closing';  // FORCES shift_closing state!
    }

    // 3. Check stock verification for POS access
    // ... (determines stock_opening or pos state)
}
```

## Key Takeaway

**Users cannot skip shift closing.** Once clocked out, the system keeps them in `shift_closing` state until they complete the shift closing process.
