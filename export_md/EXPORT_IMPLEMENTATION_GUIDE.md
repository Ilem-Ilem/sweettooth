# Export Implementation Guide

**Quick Reference for Implementing Missing Export Functionality**

---

## Problem Statement

1. **5 components** have export action defined in `bulkActions` but NO implementation
2. **20+ components** have zero export functionality
3. Inconsistent patterns between components using different export methods
4. Some components have partial implementations (CSV only, with PDF/Excel stubs)

---

## Standard Pattern for Implementation

### Step 1: Add Exportable Trait to Class

```php
<?php
namespace App\Livewire\BranchDashboard\YourModule;

use App\Livewire\BaseComponent;
use App\Traits\Exportable;

class Index extends BaseComponent
{
    use Exportable;  // ADD THIS
    
    // ... rest of class
}
```

### Step 2: Update bulkActions Configuration

```php
protected array $bulkActions = [
    'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],  // Ensure this
];
```

### Step 3: Implement exportSelected() Method

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No items selected for export.');
        return;
    }

    $items = YourModel::whereIn('id', $this->selectedIds)
        ->with('relationships') // Add relevant relationships
        ->get();

    $this->export(
        'your_model_' . date('Y-m-d'),      // filename
        $items,                              // data to export
        'exports.your_template',             // blade view path
        'excel',                             // format: excel|pdf|both
        false,                               // queue: true if >500 rows
        ['paper' => 'A4']                    // options
    );

    session()->flash('success', count($this->selectedIds) . ' items exported.');
    $this->resetBulkSelection();
}
```

### Step 4: Create Export Template View

**File**: `resources/views/exports/your_template.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->field1 }}</td>
                    <td>{{ $item->field2 }}</td>
                    <td>{{ $item->field3 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div class="pdf-container">
        <h1>Export Report</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                    <th>Column 3</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->field1 }}</td>
                        <td>{{ $item->field2 }}</td>
                        <td>{{ $item->field3 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
```

---

## Phase 1: Fix Defined But Unimplemented (5 Components)

### 1. Branches Export

**File**: `app/Livewire/BranchDashboard/Branches/Index.php`

```php
use App\Traits\Exportable;

protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No branches selected.');
        return;
    }

    $branches = Branch::whereIn('id', $this->selectedIds)
        ->with(['manager', 'department'])
        ->get();

    $this->export(
        'branches_' . date('Y-m-d'),
        $branches,
        'exports.branches',
        'excel'
    );

    session()->flash('success', count($this->selectedIds) . ' branches exported.');
    $this->resetBulkSelection();
}
```

**Template**: `resources/views/exports/branches.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Address</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Manager</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $branch)
                <tr>
                    <td>{{ $branch->id }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->code }}</td>
                    <td>{{ $branch->address }}</td>
                    <td>{{ $branch->email }}</td>
                    <td>{{ $branch->phone }}</td>
                    <td>{{ $branch->manager?->name ?? '-' }}</td>
                    <td>{{ $branch->status }}</td>
                    <td>{{ $branch->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Branches Export Report</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Address</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $branch)
                <tr>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->code }}</td>
                    <td>{{ $branch->address }}</td>
                    <td>{{ $branch->phone }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### 2. DepartmentModule Export

**File**: `app/Livewire/BranchDashboard/DepartmentModule/Index.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No departments selected.');
        return;
    }

    $departments = Department::whereIn('id', $this->selectedIds)
        ->with(['manager', 'branch'])
        ->get();

    $this->export(
        'departments_' . date('Y-m-d'),
        $departments,
        'exports.departments',
        'excel'
    );

    session()->flash('success', count($this->selectedIds) . ' departments exported.');
    $this->resetBulkSelection();
}
```

**Template**: `resources/views/exports/departments.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Type</th>
                <th>Manager</th>
                <th>Budget</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $dept)
                <tr>
                    <td>{{ $dept->id }}</td>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->code }}</td>
                    <td>{{ $dept->type }}</td>
                    <td>{{ $dept->manager?->name ?? '-' }}</td>
                    <td>{{ number_format($dept->budget, 2) }}</td>
                    <td>{{ $dept->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $dept->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Departments Export</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Manager</th>
                <th>Budget</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $dept)
                <tr>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->manager?->name ?? '-' }}</td>
                    <td>{{ number_format($dept->budget, 2) }}</td>
                    <td>{{ $dept->status ? 'Active' : 'Inactive' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### 3. Roles Export

**File**: `app/Livewire/BranchDashboard/Roles/Index.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No roles selected.');
        return;
    }

    $roles = Role::whereIn('id', $this->selectedIds)
        ->with('permissions')
        ->get()
        ->map(fn($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'permissions_count' => $role->permissions->count(),
            'created_at' => $role->created_at,
        ]);

    $this->export(
        'roles_' . date('Y-m-d'),
        $roles,
        'exports.roles',
        'excel'
    );

    session()->flash('success', count($this->selectedIds) . ' roles exported.');
    $this->resetBulkSelection();
}
```

**Template**: `resources/views/exports/roles.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Permissions Count</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $role)
                <tr>
                    <td>{{ $role['id'] }}</td>
                    <td>{{ $role['name'] }}</td>
                    <td>{{ $role['description'] }}</td>
                    <td>{{ $role['permissions_count'] }}</td>
                    <td>{{ $role['created_at']->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Roles Export</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Permissions</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $role)
                <tr>
                    <td>{{ $role['name'] }}</td>
                    <td>{{ $role['permissions_count'] }}</td>
                    <td>{{ $role['created_at']->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### 4. RolePermission Export

**File**: `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No role-permission mappings selected.');
        return;
    }

    $permissions = DB::table('role_has_permissions')
        ->whereIn('permission_id', $this->selectedIds)
        ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
        ->select(
            'roles.name as role',
            'permissions.name as permission',
            'permissions.guard_name',
            'permissions.category'
        )
        ->get();

    $this->export(
        'role_permissions_' . date('Y-m-d'),
        $permissions,
        'exports.role_permissions',
        'excel'
    );

    session()->flash('success', count($this->selectedIds) . ' role-permissions exported.');
    $this->resetBulkSelection();
}
```

**Template**: `resources/views/exports/role_permissions.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Permission</th>
                <th>Category</th>
                <th>Guard</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->role }}</td>
                    <td>{{ $item->permission }}</td>
                    <td>{{ $item->category ?? '-' }}</td>
                    <td>{{ $item->guard_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Role Permissions Mapping</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Role</th>
                <th>Permission</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->role }}</td>
                    <td>{{ $item->permission }}</td>
                    <td>{{ $item->category ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### 5. BranchModule Export

Similar pattern to Branches - implement same approach with BranchModule data structure.

---

## Phase 2: Critical Missing Exports

### MySales Export

**File**: `app/Livewire/BranchDashboard/SalesDashboard/MySales/Index.php`

```php
use App\Traits\Exportable;

protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No sales selected.');
        return;
    }

    $sales = Sale::whereIn('id', $this->selectedIds)
        ->with(['customer', 'saleDetails.item', 'paymentMethod'])
        ->get();

    $this->export(
        'sales_' . date('Y-m-d'),
        $sales,
        'exports.sales.transactions',
        count($sales) > 500 ? 'excel' : 'excel',  // Determine queuing
        count($sales) > 500,                        // Queue if >500
        ['orientation' => 'landscape']
    );

    session()->flash('success', count($this->selectedIds) . ' sales exported.');
    $this->resetBulkSelection();
}
```

**Template**: `resources/views/exports/sales/transactions.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $sale->customer?->name ?? '-' }}</td>
                    <td>{{ $sale->saleDetails->count() }}</td>
                    <td>{{ number_format($sale->subtotal, 2) }}</td>
                    <td>{{ number_format($sale->tax, 2) }}</td>
                    <td>{{ number_format($sale->total, 2) }}</td>
                    <td>{{ $sale->paymentMethod?->name ?? '-' }}</td>
                    <td>{{ $sale->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Sales Transactions Report</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $sale->customer?->name ?? '-' }}</td>
                    <td>{{ number_format($sale->total, 2) }}</td>
                    <td>{{ $sale->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

---

## Phase 3: Completing Partial Implementations

### StockTakes - Add Missing PDF & Excel

**File**: `app/Livewire/BranchDashboard/Inventory/StockTakes.php`

Replace the stub methods with actual implementations:

```php
public function exportPDF()
{
    try {
        $stockTakes = $this->getFilteredStockTakes();
        
        $this->export(
            'stock_takes_' . date('Y-m-d'),
            $stockTakes,
            'exports.inventory.stock_takes',
            'pdf',
            false,
            ['orientation' => 'landscape']
        );
    } catch (\Throwable $e) {
        session()->flash('error', 'Failed to export: ' . $e->getMessage());
    }
}

public function exportExcel()
{
    try {
        $stockTakes = $this->getFilteredStockTakes();
        
        $this->export(
            'stock_takes_' . date('Y-m-d'),
            $stockTakes,
            'exports.inventory.stock_takes',
            'excel'
        );
    } catch (\Throwable $e) {
        session()->flash('error', 'Failed to export: ' . $e->getMessage());
    }
}
```

---

## Testing Export Implementations

```php
// Test 1: Single item
$component->selectedIds = [1];
$component->exportSelected();

// Test 2: Multiple items
$component->selectedIds = [1, 2, 3, 4, 5];
$component->exportSelected();

// Test 3: Large dataset
$component->selectedIds = array_range(1, 1000);
$component->exportSelected();

// Test 4: Empty selection
$component->selectedIds = [];
$component->exportSelected();
// Should flash: "No items selected"
```

---

## Common Issues & Solutions

### Issue: Export not showing in dropdown
**Solution**: Verify `Exportable` trait is added AND `bulkActions` includes export action

### Issue: Template not found
**Solution**: Check path is correct (e.g., `exports.sales.transactions` = `resources/views/exports/sales/transactions.blade.php`)

### Issue: $forExcel or $forPdf not available
**Solution**: These are automatically passed by Exportable trait, ensure template checks both conditions

### Issue: Memory error on large exports
**Solution**: Pass `true` as queue parameter for >500 rows:
```php
$this->export(..., true); // Queue it
```

---

## Directory Structure for Templates

```
resources/
└── views/
    └── exports/
        ├── branches.blade.php
        ├── departments.blade.php
        ├── roles.blade.php
        ├── role_permissions.blade.php
        ├── sales/
        │   ├── transactions.blade.php
        │   └── ...
        ├── accounting/
        │   ├── journal.blade.php
        │   └── ...
        ├── production/
        │   ├── orders.blade.php
        │   ├── daily_produce.blade.php
        │   └── ...
        └── ...
```

---

## Next Steps

1. ✅ Read this guide and understand the pattern
2. ✅ Implement Phase 1 (5 components) first
3. ✅ Test each implementation with sample data
4. ✅ Then proceed to Phase 2 (5 critical components)
5. ✅ Document any special cases in comments

**Estimated Timeline**:
- Phase 1: 1-2 days
- Phase 2: 3-5 days
- Complete suite: 2-3 weeks with proper testing
