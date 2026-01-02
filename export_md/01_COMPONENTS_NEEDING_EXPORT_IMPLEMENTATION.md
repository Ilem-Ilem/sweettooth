# 01 - Components Needing Export Implementation

## 5 Components with Defined But Unimplemented Export

### 1. Branches
- **File**: `app/Livewire/BranchDashboard/Branches/Index.php`
- **Status**: Has `'export' => ['label' => 'Export Selected', 'method' => 'exportSelected']` but NO method
- **Data to Export**: Branch ID, Name, Code, Address, Email, Phone, Manager
- **Recommended Format**: CSV, Excel
- **Template**: Create `resources/views/exports/branches.blade.php`
- **Implementation**:
  ```php
  protected function exportSelected(): void
  {
      if (empty($this->selectedIds)) {
          session()->flash('info', 'No branches selected.');
          return;
      }
      
      $branches = Branch::whereIn('id', $this->selectedIds)->get();
      $this->export(
          'branches_' . date('Y-m-d'),
          $branches,
          'exports.branches',
          'excel'
      );
  }
  ```

### 2. BranchModule
- **File**: `app/Livewire/BranchDashboard/BranchModule/Index.php`
- **Status**: Has `'export'` action but NO method implementation
- **Data to Export**: Similar to Branches
- **Recommended Format**: CSV, Excel
- **Template**: `resources/views/exports/branch_modules.blade.php`
- **Note**: Clarify difference from Branches (same or different model?)

### 3. DepartmentModule
- **File**: `app/Livewire/BranchDashboard/DepartmentModule/Index.php`
- **Status**: Has `'export'` action but NO method implementation
- **Data to Export**: Department ID, Name, Code, Type, Manager, Active Status, Budget
- **Recommended Format**: CSV, Excel
- **Template**: `resources/views/exports/departments.blade.php`
- **Implementation**:
  ```php
  protected function exportSelected(): void
  {
      if (empty($this->selectedIds)) {
          session()->flash('info', 'No departments selected.');
          return;
      }
      
      $departments = Department::whereIn('id', $this->selectedIds)->get();
      $this->export(
          'departments_' . date('Y-m-d'),
          $departments,
          'exports.departments',
          'excel'
      );
  }
  ```

### 4. Roles
- **File**: `app/Livewire/BranchDashboard/Roles/Index.php`
- **Status**: Has `'export'` action but NO method implementation
- **Data to Export**: Role ID, Name, Description, Permissions Count, Created Date
- **Recommended Format**: CSV, Excel
- **Template**: `resources/views/exports/roles.blade.php`
- **Implementation**:
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
              'created_at' => $role->created_at->format('Y-m-d H:i'),
          ]);
      
      $this->export(
          'roles_' . date('Y-m-d'),
          $roles,
          'exports.roles',
          'excel'
      );
  }
  ```

### 5. RolePermission
- **File**: `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`
- **Status**: Has `'export'` action but NO method implementation
- **Data to Export**: Role Name, Permission Name, Guard Name, Category
- **Recommended Format**: CSV, Excel
- **Template**: `resources/views/exports/role_permissions.blade.php`
- **Implementation**:
  ```php
  protected function exportSelected(): void
  {
      if (empty($this->selectedIds)) {
          session()->flash('info', 'No role-permission pairs selected.');
          return;
      }
      
      $rolePermissions = DB::table('role_has_permissions')
          ->whereIn('permission_id', $this->selectedIds)
          ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
          ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
          ->select('roles.name as role', 'permissions.name as permission', 'permissions.guard_name', 'permissions.category')
          ->get();
      
      $this->export(
          'role_permissions_' . date('Y-m-d'),
          $rolePermissions,
          'exports.role_permissions',
          'excel'
      );
  }
  ```

## Critical Components Needing NEW Export Support (26 Components)

### Priority 1: Financial & Sales
1. **MySales** (`app/Livewire/BranchDashboard/Sales/MySales.php`)
   - Export: Sales transactions with date range, customer, amount, status
   - Formats: CSV (immediate), Excel (queued for large datasets)
   - Template needed: `exports.sales.sales_transactions`

2. **Accounting** (`app/Livewire/BranchDashboard/Accounting/Index.php`)
   - Export: Journal entries, invoices, payments
   - Formats: Excel (with formatting), PDF (for printing)
   - Templates: `exports.accounting.*`

3. **Analytics** (`app/Livewire/BranchDashboard/Analytics/Index.php`)
   - Export: Dashboard metrics, trend data
   - Formats: Excel (multiple sheets), PDF (charts)
   - Templates: `exports.analytics.*`

### Priority 2: Production
4. **ProductionModule** (`app/Livewire/BranchDashboard/Production/Module.php`)
   - Export: Production orders, status, timelines
   - Formats: CSV, Excel
   - Template: `exports.production.orders`

5. **DailyProduce** (`app/Livewire/BranchDashboard/Production/DailyProduce.php`)
   - Export: Daily production records
   - Formats: CSV, Excel
   - Template: `exports.production.daily_produce`

6. **KitchenModule** (`app/Livewire/BranchDashboard/Kitchen/Index.php`)
   - Export: Kitchen orders, production schedule
   - Formats: CSV (for kitchen staff), PDF (for printing)
   - Template: `exports.kitchen.orders`

### Priority 3: Operations
7. **Dispatches** (`app/Livewire/BranchDashboard/Dispatches/Index.php`)
   - Export: Shipment records, delivery tracking
   - Formats: CSV, Excel
   - Template: `exports.dispatches.shipments`

8. **EmployeeModule** (`app/Livewire/BranchDashboard/EmployeeModule/Index.php`)
   - Export: Employee list with all details
   - Formats: Excel (with formatting), PDF
   - Template: `exports.employees.roster`

9. **Requests** (`app/Livewire/BranchDashboard/Requests/Index.php`)
   - Export: Item requests, approval status, timeline
   - Formats: CSV, Excel
   - Template: `exports.requests.item_requests`

10. **AuditManagement** (`app/Livewire/BranchDashboard/AuditManagement/Index.php`)
    - Export: Audit logs with filters
    - Formats: CSV, Excel
    - Template: `exports.audit.logs`

### Priority 4: Inventory & POS
11. **ProductList** (`app/Livewire/BranchDashboard/Inventory/ProductList.php`)
    - Export: Product inventory with quantities, costs
    - Formats: CSV, Excel
    - Template: `exports.inventory.products`

12. **Recipes** (`app/Livewire/BranchDashboard/Production/Recipes.php`)
    - Export: Recipe details with ingredients, instructions
    - Formats: PDF (for kitchen), Excel (for management)
    - Template: `exports.recipes.detailed`

13. **Pos** (`app/Livewire/BranchDashboard/Pos/Index.php`)
    - Export: POS transactions, daily sales
    - Formats: CSV, Excel
    - Template: `exports.pos.transactions`

14. **StockOpening** (`app/Livewire/BranchDashboard/Inventory/StockOpening.php`)
    - Export: Opening stock records
    - Formats: CSV, Excel
    - Template: `exports.inventory.stock_opening`

### Priority 5: Reporting
15. **Report** (`app/Livewire/BranchDashboard/Report/Index.php`)
    - Export: Generated reports in multiple formats
    - Formats: PDF, Excel, CSV
    - Template: Varies by report type

16. **CompileReports** (`app/Livewire/BranchDashboard/CompileReports/Index.php`)
    - Export: Compiled report data
    - Formats: PDF, Excel
    - Template: `exports.reports.compiled`

17. **ReviewReports** (`app/Livewire/BranchDashboard/ReviewReports/Index.php`)
    - Export: Report review records
    - Formats: Excel, PDF
    - Template: `exports.reports.review`

18. **ViewReport** (`app/Livewire/BranchDashboard/ViewReport/Index.php`)
    - Export: Report viewing/data export
    - Formats: Multiple based on report type
    - Template: Dynamic

### Priority 6: HR & Operations
19. **Shifts** (`app/Livewire/BranchDashboard/Shifts/Index.php`)
    - Export: Shift schedules, assignments
    - Formats: CSV, Excel
    - Template: `exports.hr.shifts`

20. **ShiftClosing** (multiple variations)
    - Export: Shift closing records, cash reconciliation
    - Formats: Excel (with calculations), PDF
    - Template: `exports.shifts.closing_report`

21. **Callbacks** (`app/Livewire/BranchDashboard/Callbacks/Index.php`)
    - Export: Callback events, logging
    - Formats: CSV, Excel
    - Template: `exports.callbacks.events`

### Priority 7: Supporting Modules
22. **Dashboard** (various)
    - Export: Dashboard data snapshots
    - Formats: PDF (summary), Excel (detailed)
    - Template: `exports.dashboard.snapshot`

23. **Settings** (`app/Livewire/BranchDashboard/Settings/Index.php`)
    - Export: Configuration backup
    - Formats: JSON, Excel
    - Template: `exports.settings.configuration`

24. **TableManagement** (`app/Livewire/BranchDashboard/TableManagement/Index.php`)
    - Export: Table setup/assignments
    - Formats: CSV, PDF (for printing)
    - Template: `exports.pos.tables`

25. **SendToMD** (`app/Livewire/BranchDashboard/SendToMD/Index.php`)
    - Export: Markdown export feature (likely incomplete)
    - Formats: Markdown files
    - Status: Review existing implementation

26. **ViewCompiled** (`app/Livewire/BranchDashboard/ViewCompiled/Index.php`)
    - Export: Compiled view data
    - Formats: Excel, PDF
    - Template: `exports.views.compiled`

## Implementation Strategy

### Phase 1: Fix Defined But Unimplemented (5 components)
1. Add `exportSelected()` method to each component
2. Create corresponding Blade templates
3. Test with sample data

### Phase 2: Add Export to High Priority (5 components)
1. MySales, Accounting, Analytics, ProductionModule, DailyProduce
2. Follow same pattern as Phase 1

### Phase 3: Add Export to Medium Priority (10 components)
1. Remaining operational modules
2. Create batch templates where applicable

### Phase 4: Add Export to Lower Priority (11 components)
1. Supporting and specialized modules
2. Focus on most-used features

## Common Implementation Pattern

```php
use App\Traits\Exportable;

class YourComponent extends BaseComponent
{
    use Exportable;
    
    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];
    
    protected function exportSelected(): void
    {
        if (empty($this->selectedIds)) {
            session()->flash('info', 'No items selected for export.');
            return;
        }
        
        $items = YourModel::whereIn('id', $this->selectedIds)->get();
        
        $this->export(
            'your_export_name_' . date('Y-m-d'),
            $items,
            'exports.your_template',
            'excel', // or 'pdf' or 'both'
            false,   // queue (true if > 500 rows expected)
            [
                'paper' => 'A4',
                'orientation' => 'portrait',
                // other options...
            ]
        );
    }
}
```

## Export View Template Pattern

```blade
{{-- resources/views/exports/your_template.blade.php --}}
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->field1 }}</td>
                    <td>{{ $item->field2 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div class="pdf-container">
        <h1>Export Title</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->field1 }}</td>
                        <td>{{ $item->field2 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
```
