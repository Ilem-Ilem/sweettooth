# Export Functionality - Quick Reference

## Current Status at a Glance

```
✅ Complete:  20 components with full export
🟡 Partial:   3 components (CSV only, PDF/Excel stubs)
🔴 Broken:    5 components (export action defined, no implementation)
❌ Missing:   20+ components (no export at all)
```

**Total work**: ~55 components to review/implement

---

## Components with Complete Export (20) ✅

### Fully Working
| Component | Location | Formats |
|-----------|----------|---------|
| Items | Inventory | PDF, Excel, CSV |
| Stocks | Inventory | PDF, Excel, CSV |
| Purchases | Inventory | PDF, Excel, CSV |
| HealthChecks | Inventory | PDF, Excel, CSV |
| ItemDispatches | Inventory | PDF, Excel, CSV |
| ItemRequests | Inventory | PDF, Excel, CSV |
| Analytics | Inventory | PDF, Excel, CSV |
| Callbacks | Sales | PDF, Excel, CSV |
| Analytics | Sales | PDF, Excel, CSV |
| AlertsDashboard | Sales Analytics | PDF, Excel, CSV |
| StockValuation | Sales Analytics | PDF, Excel, CSV |
| StockLevelAnalytics | Sales Analytics | PDF, Excel, CSV |
| OverallSummaryDashboard | Sales Analytics | PDF, Excel, CSV |

### Partial (CSV working only, PDF/Excel stubs)
| Component | Location | Issue |
|-----------|----------|-------|
| StockTakes | Inventory | "PDF/Excel coming soon" |
| StockMovementAnalytics | Sales Analytics | "PDF/Excel coming soon" |

---

## Components with BROKEN Export (5) 🔴 URGENT

These have export action defined but NO implementation:

| # | Component | File | Status |
|---|-----------|------|--------|
| 1 | **Branches** | `Branches/Index.php` | Has `'export'` action, no method |
| 2 | **BranchModule** | `BranchModule/Index.php` | Has `'export'` action, no method |
| 3 | **DepartmentModule** | `DepartmentModule/Index.php` | Has `'export'` action, no method |
| 4 | **Roles** | `Roles/Index.php` | Has `'export'` action, no method |
| 5 | **RolePermission** | `EmployeeModule/RolePermission/Index.php` | Has `'export'` action, no method |

**Quick fix**: Add `Exportable` trait + implement `exportSelected()` method (2 min per component)

---

## High Priority Missing Exports (5) 🔴 CRITICAL

| # | Component | Location | Impact |
|---|-----------|----------|--------|
| 1 | **MySales** | `SalesDashboard/MySales/Index.php` | Core sales data |
| 2 | **Accounting** | `Accounting/Index.php` | Financial records |
| 3 | **ProductionModule** | `Production/Module.php` | Production tracking |
| 4 | **DailyProduce** | `Production/DailyProduce/Index.php` | Daily operations |
| 5 | **Pos** | `SalesDashboard/Pos/Index.php` | Transaction records |

---

## Implementation Quick Commands

### Step 1: Add Trait
```php
use App\Traits\Exportable;
```

### Step 2: Add Method
```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No items selected.');
        return;
    }

    $items = Model::whereIn('id', $this->selectedIds)->get();
    
    $this->export(
        'export_name_' . date('Y-m-d'),
        $items,
        'exports.template_name',
        'excel'
    );
    
    session()->flash('success', 'Export completed.');
    $this->resetBulkSelection();
}
```

### Step 3: Create Template
File: `resources/views/exports/template_name.blade.php`

```blade
@if($forExcel)
    <table>
        <thead><tr><th>Col1</th><th>Col2</th></tr></thead>
        <tbody>
            @foreach($data as $item)
                <tr><td>{{ $item->field1 }}</td><td>{{ $item->field2 }}</td></tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <h2>Title</h2>
    <table class="table table-bordered">
        <!-- same structure -->
    </table>
@endif
```

---

## Files to Modify (Priority Order)

### Priority 1: Fix Broken Exports (Quick Wins)
- [ ] `app/Livewire/BranchDashboard/Branches/Index.php`
- [ ] `app/Livewire/BranchDashboard/BranchModule/Index.php`
- [ ] `app/Livewire/BranchDashboard/DepartmentModule/Index.php`
- [ ] `app/Livewire/BranchDashboard/Roles/Index.php`
- [ ] `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`

**Estimated**: 30 minutes total

### Priority 2: Add Critical Missing Exports
- [ ] `app/Livewire/BranchDashboard/SalesDashboard/MySales/Index.php`
- [ ] `app/Livewire/BranchDashboard/Accounting/Index.php`
- [ ] `app/Livewire/BranchDashboard/Production/Module.php`
- [ ] `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php`
- [ ] `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php`

**Estimated**: 2-3 hours

### Priority 3: Complete Partial Implementations
- [ ] `app/Livewire/BranchDashboard/Inventory/StockTakes.php` - Replace stubs
- [ ] `app/Livewire/BranchDashboard/SalesDashboard/Analytics/StockMovementAnalytics.php` - Replace stubs
- [ ] `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/Index.php` - Add PDF/Excel

**Estimated**: 1 hour

---

## Templates to Create

### Phase 1 (Broken exports)
```
resources/views/exports/
├── branches.blade.php
├── branch_modules.blade.php
├── departments.blade.php
├── roles.blade.php
└── role_permissions.blade.php
```

### Phase 2 (Critical missing)
```
resources/views/exports/
├── sales/transactions.blade.php
├── accounting/journal.blade.php
├── production/orders.blade.php
├── production/daily_produce.blade.php
└── pos/transactions.blade.php
```

---

## Trait Overview: Exportable

**Location**: `app/Traits/Exportable.php`

### Usage
```php
use App\Traits\Exportable;

// In your component method:
$this->export(
    $filename,        // string: name of exported file
    $data,           // array|Collection: data to export
    $view,           // string: blade view path (dot notation)
    $format,         // string: 'excel'|'pdf'|'both'
    $queue,          // bool: queue if true (for >500 rows)
    $options         // array: pdf options (paper, orientation, etc)
);
```

### Automatic Variables in Template
- `$data` - Your data collection/array
- `$forExcel` - True if exporting to Excel
- `$forPdf` - True if exporting to PDF

---

## Testing Checklist

For each export implementation:

- [ ] Test single item export
- [ ] Test multiple items export (5-10)
- [ ] Test large dataset (100+)
- [ ] Test with empty selection (should show error)
- [ ] Verify Excel opens correctly
- [ ] Verify PDF renders properly
- [ ] Check data accuracy
- [ ] Verify formatting (numbers, dates, currency)
- [ ] Test with special characters
- [ ] Confirm selection resets after export

---

## Common Export Issues & Solutions

### ❌ Export action doesn't show in dropdown
**Cause**: `Exportable` trait not added  
**Fix**: Add `use Exportable;` to component class

### ❌ "Method exportSelected not found"
**Cause**: Method not implemented  
**Fix**: Add the `exportSelected()` method to component

### ❌ Template not found error
**Cause**: Wrong path in `$this->export()` call  
**Fix**: Use dot notation path: `'exports.your_template'` = `resources/views/exports/your_template.blade.php`

### ❌ Variables ($forExcel, $data) not available in template
**Cause**: Template not checking conditions properly  
**Fix**: Ensure template has `@if($forExcel)` and `@elseif($forPdf)` blocks

### ❌ Memory exceeded for large exports
**Cause**: Not queuing the job  
**Fix**: Pass `true` as queue parameter for >500 rows

### ❌ Currency/Date formatting off
**Cause**: Missing formatting in template  
**Fix**: Use Laravel helpers: `number_format($value, 2)`, `$date->format('Y-m-d')`

---

## Documentation References

1. **Full Status Report**: `EXPORT_STATUS_REPORT.md`
   - Detailed breakdown of all 55 components
   - Priority classification
   - Current implementation status

2. **Implementation Guide**: `EXPORT_IMPLEMENTATION_GUIDE.md`
   - Step-by-step code examples
   - Template patterns for each phase
   - Complete examples for Phase 1 & 2

3. **Export Documentation**: `export_md/` folder
   - `00_EXPORTS_AND_BULK_ACTIONS_OVERVIEW.md` - System overview
   - `01_COMPONENTS_NEEDING_EXPORT_IMPLEMENTATION.md` - Detailed component list
   - `02_EXPORT_IMPLEMENTATION_TEMPLATES.md` - Code templates
   - `03_BULK_ACTIONS_IMPROVEMENTS.md` - Bulk actions enhancements
   - `04_IMPLEMENTATION_CHECKLIST.md` - Full checklist

---

## Time Estimate

| Phase | Components | Effort | Time |
|-------|-----------|--------|------|
| Phase 1 | 5 (broken) | Low | 30 min |
| Phase 2 | 5 (critical) | Medium | 2-3 hrs |
| Phase 3 | 3 (partial) | Low | 1 hr |
| Phase 4+ | 20+ | Medium | 2-3 weeks |
| **Total** | **55** | **Medium** | **1 month** |

---

## Recommended Execution Plan

**Day 1 (30 min)**: Implement Phase 1 - Fix broken exports
- Quick wins, establishes pattern
- Tests the Exportable trait setup

**Day 2-3 (3-4 hrs)**: Implement Phase 2 - Critical exports
- MySales, Accounting, Production
- Highest impact features

**Day 4 (1 hr)**: Complete Phase 3 - Partial implementations
- Remove "coming soon" stubs
- Full PDF/Excel support

**Week 2+**: Phase 4 - Remaining 20+ components
- Can be parallelized
- Follow established pattern

---

## Ready to Start?

1. ✅ Read `EXPORT_STATUS_REPORT.md` - understand current state
2. ✅ Read `EXPORT_IMPLEMENTATION_GUIDE.md` - see detailed examples
3. ✅ Start with Phase 1 - `Branches` component
4. ✅ Follow the 3-step pattern (Trait → Method → Template)
5. ✅ Test each implementation
6. ✅ Move to Phase 2 - `MySales` component

**Good luck!** 🚀
