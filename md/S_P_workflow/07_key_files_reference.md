# Key Files Reference

## Sales Module

### Core Workflow Files

| File | Purpose | Key Lines |
|------|---------|-----------|
| `app/Livewire/BranchDashboard/HeaderClockInOut.php` | Clock in/out functionality | 71-136: clockOut() redirect logic |
| `app/Livewire/BranchDashboard/SalesDashboard/ShiftClosing/Index.php` | Shift closing page | 372-511: saveShiftClosing() |
| `app/Livewire/BranchDashboard/SalesDashboard/StockOpening/Index.php` | Stock opening verification | Mount, verify methods |
| `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php` | Point of Sale | Sales processing |

### Middleware & Services

| File | Purpose | Key Lines |
|------|---------|-----------|
| `app/Http/Middleware/ValidateSalesWorkflow.php` | Workflow enforcement | 26-66: handle(), 121-136: canAccessState() |
| `app/Http/Middleware/RequireActiveShift.php` | Active shift requirement | 13-127: handle() |
| `app/Services/SalesWorkflowService.php` | Workflow state management | 35-69: getCurrentState() |
| `app/Services/SalesStockVerificationService.php` | Stock verification | 105-118: canAccessPos() |

### Models

| File | Purpose | Key Fields |
|------|---------|------------|
| `app/Models/Shift.php` | Shift data | status, workflow_state, metadata, clock_in, clock_out |
| `app/Models/ProductStock.php` | Stock tracking | opening_quantity, closing_quantity, stock_date |
| `app/Models/Sale.php` | Sales transactions | total, status, sale_time |
| `app/Models/Payment.php` | Payment records | payment_method, amount, status |

### Views

| File | Purpose |
|------|---------|
| `resources/views/livewire/branch-dashboard/header-clock-in-out.blade.php` | Clock in/out UI |
| `resources/views/livewire/branch-dashboard/sales-dashboard/shift-closing/index.blade.php` | Shift closing UI |
| `resources/views/livewire/branch-dashboard/sales-dashboard/stock-opening/index.blade.php` | Stock opening UI |

## Production Module

### Core Workflow Files

| File | Purpose | Key Lines |
|------|---------|-----------|
| `app/Livewire/BranchDashboard/Production/ShiftClosing/Index.php` | Optional shift closing | 336-403: saveShiftClosing() |
| `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php` | Daily production batches | Batch management |
| `app/Livewire/BranchDashboard/Production/Recipes.php` | Recipe management | Recipe CRUD |

### Models

| File | Purpose | Key Fields |
|------|---------|------------|
| `app/Models/ProductionRecord.php` | Production batch records | quantity_produced, is_locked |
| `app/Models/DailyProduce.php` | Daily production plan | shift_id, batches_to_produce, status |
| `app/Models/Recipe.php` | Recipe definitions | yield_quantity, ingredients |
| `app/Models/ItemRequest.php` | Material requests | status, quantity_dispatched |

### Views

| File | Purpose |
|------|---------|
| `resources/views/livewire/branch-dashboard/production/shift-closing/index.blade.php` | Production shift closing UI |
| `resources/views/livewire/branch-dashboard/production/daily-produce/index.blade.php` | Daily produce UI |

## Routes

**File**: `routes/branch-route.php`

| Lines | Purpose |
|-------|---------|
| 124 | `require_active_shift` middleware group start |
| 162-228 | Production routes (no workflow validation) |
| 310-359 | Sales routes (with workflow validation) |
| 317-319 | POS routes with `validate-sales-workflow` |
| 332-334 | Shift closing routes with `validate-sales-workflow` |
| 341-343 | Stock opening routes with `validate-sales-workflow` |

## Database Schema (Relevant Fields)

### shifts table
```
id, employee_id, department_id, branch_id,
shift_date, shift_type, status, workflow_state,
clock_in, clock_out, shift_closed_at,
metadata (JSON), notes (JSON)
```

### product_stocks table
```
id, product_id, stock_date, shift_type,
opening_quantity, addition_quantity, closing_quantity,
quantity_sold, expiry_date, notes
```

### production_records table
```
id, daily_produce_id, quantity_produced,
quantity_approved, quantity_rejected, quantity_sent_out,
is_locked
```

## Configuration Files

| File | Purpose |
|------|---------|
| `config/workflow.php` | Workflow configuration (if exists) |
| `app/Providers/AppServiceProvider.php` | Service bindings |

## Helper Functions

| Function | File | Purpose |
|----------|------|---------|
| `is_super_admin()` | `app/helpers.php` | Check if user is super admin |
| `can_access_all_branches()` | `app/helpers.php` | Check multi-branch access |
| `current_branch_id()` | `app/helpers.php` | Get current branch context |
