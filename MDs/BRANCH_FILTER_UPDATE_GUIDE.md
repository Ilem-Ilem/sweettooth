# Branch Filter Update Guide
## How to Update Components for Super Admin Branch Filtering

---

## Overview
We need to update **84 components** in the `app/Livewire/BranchDashboard` directory to use the dynamic branch context instead of hardcoded `auth('employees')->user()->branch_id`.

---

## Update Pattern

### BEFORE (Current Code):
```php
public function mount()
{
    $this->branchId = auth('employees')->user()->branch_id;
    $this->departmentId = session('selected_department_id');
}

public function someMethod()
{
    $employees = Employee::where('branch_id', auth('employees')->user()->branch_id)->get();
}
```

### AFTER (Updated Code):
```php
public function mount()
{
    // Use helper function instead of hardcoded auth
    $this->branchId = current_branch_id();
    $this->departmentId = session('selected_department_id');
}

// Add listener for branch changes
#[On('branch-changed')]
public function handleBranchChange($branchId)
{
    $this->branchId = $branchId;
    // Reload data or refresh component as needed
    $this->mount(); // or call specific refresh methods
}

public function someMethod()
{
    // Use the helper function
    $employees = Employee::where('branch_id', current_branch_id())->get();
}
```

---

## Step-by-Step Update Process

### 1. Add the branch-changed listener attribute
```php
use Livewire\Attributes\On;

#[On('branch-changed')]
public function handleBranchChange($branchId)
{
    $this->branchId = $branchId;
    $this->mount(); // Or your custom refresh logic
}
```

### 2. Replace all instances of `auth('employees')->user()->branch_id`
**Find:** `auth('employees')->user()->branch_id`
**Replace with:** `current_branch_id()`

### 3. Update mount() method
```php
public function mount()
{
    $this->branchId = current_branch_id(); // Instead of auth('employees')->user()->branch_id
    // ... rest of your code
}
```

### 4. Update database queries
```php
// BEFORE
$data = Model::where('branch_id', auth('employees')->user()->branch_id)->get();

// AFTER
$data = Model::where('branch_id', current_branch_id())->get();
```

---

## Components to Update (by Module)

### Employee Management (10 files)
- [x] EmployeeModule/Index.php ✅ COMPLETED
- [x] EmployeeModule/Create.php ✅ COMPLETED
- [ ] EmployeeModule/Shifts/Index.php
- [ ] EmployeeModule/LeaveManagement/ApproveLeave.php
- [ ] EmployeeModule/LeaveManagement/MyLeaves.php
- [ ] EmployeeModule/LeaveManagement/LeaveBalance.php
- [ ] EmployeeModule/LeaveManagement/ApplyLeave.php
- [ ] EmployeeModule/LeaveManagement/ManageAllocations.php
- [ ] EmployeeModule/LeaveManagement/LeaveTypes.php
- [ ] EmployeeModule/...

### Department Management (Estimate: 5-8 files)
- [ ] Branch/Departments/...
- [ ] Update all department-related components

### Inventory Management (Estimate: 15-20 files)
- [ ] Inventory/Items/Index.php
- [ ] Inventory/StockMovement/...
- [ ] Inventory/Reports/...
- [ ] Update all inventory components

### Sales/POS (Estimate: 15-20 files)
- [ ] SalesDashboard/Pos/Index.php
- [ ] SalesDashboard/MySales/Index.php
- [ ] SalesDashboard/Analytics/Index.php
- [ ] SalesDashboard/StockMonitor.php
- [ ] SalesDashboard/ShiftClosing/Index.php
- [ ] SalesDashboard/TableManagement/...
- [ ] SalesDashboard/Dispatches/...
- [ ] SalesDashboard/Callbacks/...
- [ ] Update all sales components

### Reports (Estimate: 10 files)
- [ ] Inventory/Reports/StockLevels/Index.php
- [ ] Inventory/Reports/Reorder/Index.php
- [ ] Inventory/Reports/StockMovement/Index.php
- [ ] Inventory/Reports/StockTurnover/Index.php
- [ ] Inventory/Reports/Variance/Index.php
- [ ] ReportingDepartment/...

### Other Modules
- [ ] ProductionModule/Index.php
- [ ] Other dashboard components

---

## Example: Complete Component Update

```php
<?php

namespace App\Livewire\BranchDashboard\EmployeeModule;

use App\Models\Employee;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $branchId;
    public $search = '';

    public function mount()
    {
        // Use helper instead of hardcoded auth
        $this->branchId = current_branch_id();
    }

    // Listen for branch changes from BranchSelector
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->branchId = $branchId;
        $this->resetPage(); // Reset pagination
    }

    public function render()
    {
        $employees = Employee::query()
            ->where('branch_id', $this->branchId) // Use property, not auth()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(20);

        return view('livewire.branch-dashboard.employee-module.index', [
            'employees' => $employees
        ]);
    }
}
```

---

## Testing Checklist

After updating each component:
- [ ] Test as regular employee (should see only their branch)
- [ ] Test as super admin (should see selected branch)
- [ ] Test branch switching (data should update)
- [ ] Test pagination/filters still work
- [ ] Test create/update/delete operations

---

## Quick Find & Replace Commands

```bash
# Find all files with auth('employees')->user()->branch_id
grep -r "auth('employees')->user()->branch_id" app/Livewire/BranchDashboard/

# Count occurrences
grep -r "auth('employees')->user()->branch_id" app/Livewire/BranchDashboard/ | wc -l
```

---

## Automation Script (Optional)

For bulk updates, you can use this pattern:

```bash
# Find and list all files that need updating
find app/Livewire/BranchDashboard -name "*.php" -exec grep -l "auth('employees')->user()->branch_id" {} \;
```

---

**Last Updated:** 2025-11-15
**Total Components:** 84 (Using Option C: Critical Components Only)
**Status:** In Progress - Focusing on Critical Components

---

## 🎯 OPTION C APPROACH - Critical Components Only

### Completed ✅
**Employee Module (5 components):**
- [x] Index.php
- [x] Create.php
- [x] Edit.php
- [x] Details.php
- [x] Shifts/Index.php

**Inventory Management (12 components):**
- [x] Items.php - HIGH PRIORITY
- [x] Stocks.php - HIGH PRIORITY
- [x] StockMovements.php - HIGH PRIORITY
- [x] ItemRequests.php - HIGH PRIORITY
- [x] ItemDispatches.php - HIGH PRIORITY
- [x] Purchases.php
- [x] StockTakes.php
- [x] Reports/StockLevels/Index.php
- [x] Reports/Reorder/Index.php
- [x] Reports/StockMovement/Index.php
- [x] Reports/StockTurnover/Index.php
- [x] Reports/Variance/Index.php

**Total Completed: 17 components**

### In Progress 🔄
None currently

### Pending ⏳
**Sales/POS (~15 components)**
**Department Management (~5 components)**
**Reporting Department (~5 components)**
