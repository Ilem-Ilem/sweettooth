# Callback System Analysis

**Date:** December 5, 2025  
**Scope:** Inventory & Production Callback Systems  
**Status:** Reviewed with Inconsistencies & Improvements Identified

---

## Executive Summary

The callback system exists in three main implementations:
1. **Inventory Callbacks** (`BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php`)
2. **Production Callbacks** (`BranchDashboard/Production/Callbacks/ApproveCallbacks.php`)
3. **Sales Dispatch Callbacks** (`BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php`)

Each handles different types of callbacks (raw material returns, finished product rejects, dispatch issues) but with inconsistent patterns and code structure.

---

## Critical Issues Found

### 1. **Inconsistent Model Relationships & Naming** 🔴
- **Inventory Callbacks** use `recorded_by` (integer) but load via `recordedBy()` (Employee relation)
- **ProductionCallback Model** has `recordedBy()` and `createdBy()` doing the same thing (line 67-78)
- Some models use `recorded_by_id` pattern, others use just `recorded_by`
- **Impact:** Confusing for developers, potential runtime errors

### 2. **Duplicate Query Logic** 🔴
- Both `getFilteredQuery()` and `getRowsProperty()` in `ApproveCallbacks.php` (Inventory) have nearly identical code
- Date range filtering logic is duplicated across implementations
- **Impact:** Maintenance nightmare, inconsistent behavior

### 3. **Inconsistent Status Lifecycle** 🟡
- **Inventory callbacks:** pending → approved_by_inventory → completed OR rejected
- **Production callbacks:** pending → approved_by_production → received_by_production → completed OR rejected
- **Sales callbacks:** Multiple workflows with different transitions
- **Impact:** Different UX, hard to understand approval workflows

### 4. **Missing Authorization Checks** 🔴
- No `authorize()` calls in callback actions
- `getEmployeeId()` method doesn't verify if user has permission to approve
- No role-based access control (branch manager only, etc.)
- **Impact:** Security vulnerability - any user can approve callbacks

### 5. **Poor Error Handling in Handler Methods** 🟡
- `handleRawMaterialCallback()` and `handleFinishedProductCallback()` throw generic exceptions
- No validation that stock records exist before updating
- If stock update fails, callback approval still succeeds
- **Impact:** Data integrity issues, silent failures

### 6. **Incomplete Implementation** 🟠
- **Production callbacks** missing rejection modal and rejection workflow
- No audit logging for who approved/rejected and when
- **Impact:** No compliance trail

### 7. **Inheritance Not Utilized** 🟡
- All three callback components inherit from `BaseComponent` but don't use shared functionality
- Each reimplements: pagination, table headers, filtering, modal management
- **Impact:** Code duplication, inconsistent behavior

### 8. **UOM (Unit of Measure) Not Validated** 🟡
- Callbacks store quantity in different UOMs without validation
- Stock updates don't account for UOM conversion
- **Impact:** Incorrect inventory calculations

### 9. **Brain Dead Filter Properties** 🟡
- `filterSourceType` in Inventory callbacks never gets used in queries (missing filter logic)
- `startDate` and `endDate` exist but filters written inline
- **Impact:** Confusing code organization

### 10. **Missing TypeScript/Type Hints** 🟡
- No return type hints on methods
- No nullable type hints on properties
- **Impact:** Harder to debug, less IDE support

---

## Inconsistencies by Component

### Inventory Callbacks (ApproveCallbacks.php)
| Issue | Severity | Details |
|-------|----------|---------|
| Duplicate `getFilteredQuery()` and `getRowsProperty()` | High | Nearly identical code in two methods |
| `handleFinishedProductCallback()` missing `save()` call | Critical | DailyProduce changes not persisted |
| No soft deletes support | Medium | Callbacks can't be recoverable |
| StockMovement reference_type as string | Low | Should use model class constant |

### Production Callbacks (ApproveCallbacks.php)
| Issue | Severity | Details |
|-------|----------|---------|
| Missing rejection modal completely | High | Users can't reject callbacks |
| Incomplete `received_by` workflow | High | Callbacks stuck in "received" state |
| No `reject()` method in ProductDispatchCallback | Critical | Rejection not implemented |
| Different approval flow than Inventory | Medium | Inconsistent UX |

### Sales Dispatch Callbacks (CreateDispatchCallback.php)
| Issue | Severity | Details |
|-------|----------|---------|
| Shift loading logic extremely complex | Medium | Multiple similar methods for same thing |
| `callbackReason` validation missing | Medium | Can submit without reason |
| No confirmation before creating callback | Low | User could accidentally create multiple |
| Poor separation of concerns | Medium | Creating callbacks mixed with listing |

---

## Code Quality Issues

### Missing Validations
```php
// Current: Line 239-241
if (empty($this->rejectReason)) {
    // Only checks for empty string
}

// Should validate:
- Reason is not just whitespace
- Reason length (min/max)
- Callback exists and belongs to user's branch
- User has permission to reject
```

### Missing Audit Trail
```php
// Current: No audit logging
$callback->approve($employeeId); // Only sets approved_by, approved_at

// Should:
- Log who approved, when, from where (IP)
- Log what changed in callback
- Log stock updates with reason
- Compliance trail for auditors
```

### Inconsistent Transaction Handling
```php
// Current: Sometimes nested, sometimes not
DB::beginTransaction();
try {
    // Some methods handle stock updates
    // Some don't
}

// Should: Consistent pattern with savepoints for stock updates
```

---

## Database Schema Issues

### Callback Tables Missing Columns
- **No `branch_id`** - Callbacks filtered via shift → branch relationship (slow)
- **No `approved_reason`** - Rejection reason stored in notes (mixing data types)
- **No `audit_user`** - Audit information missing
- **No `audit_timestamp`** - When approval happened

### Stock Movement Issues
- **reference_type as string** - Should be polymorphic model reference
- **No uom_id** - Quantity without UOM context is invalid

---

## Performance Issues

### N+1 Query Problems
```php
// Line 99-107: Loads shift, item, product separately
ProductionCallback::with([
    'shift',
    'item', 
    'product',
    'recordedBy',
    'approvedBy'
])
// But then references recipe separately (line 386)
$recipe = \App\Models\Recipe::where('product_id', $callback->product_id)->first();
```

### Inefficient Filtering
```php
// Line 75-83: Has to load shift just to filter by branch_id
->whereHas('shift', function ($q) {
    $q->where('branch_id', $this->getBranchId());
});
```

---

## Recommendations Priority

| Priority | Item | Effort | Impact |
|----------|------|--------|--------|
| 🔴 Critical | Extract base CallbackComponent class | Medium | High - Reduces duplication |
| 🔴 Critical | Add authorization checks | Low | High - Security |
| 🔴 Critical | Implement rejection in ProductionCallbacks | High | High - Feature completeness |
| 🟡 High | Add audit logging | Medium | High - Compliance |
| 🟡 High | Fix DailyProduce save() bug | Low | High - Data integrity |
| 🟡 High | Add `branch_id` to callback tables | High | Medium - Performance |
| 🟡 High | Standardize status workflows | High | Medium - UX consistency |
| 🟠 Medium | Add UOM validation | Medium | Medium - Accuracy |
| 🟠 Medium | Extract filter logic to methods | Low | Medium - Maintainability |
| 🟠 Medium | Add type hints everywhere | Low | Low - Developer experience |

---

## Files Requiring Changes

1. **Models:**
   - `app/Models/ProductionCallback.php` - Duplicate methods, missing validations
   - `app/Models/ProductDispatchCallback.php` - Missing reject() method
   - `app/Models/Stock.php` - Add UOM validation

2. **Components:**
   - `app/Livewire/BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php` - Duplicate logic
   - `app/Livewire/BranchDashboard/Production/Callbacks/ApproveCallbacks.php` - Missing rejection
   - `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php` - Complex shift logic

3. **Views:**
   - `resources/views/livewire/branch-dashboard/inventory/callbacks/approve-callbacks.blade.php`
   - `resources/views/livewire/branch-dashboard/production/callbacks/approve-callbacks.blade.php` - Missing rejection modal

4. **Migrations:**
   - Update callback tables with `branch_id`, `audit_columns`

5. **Services:**
   - Create `CallbackService` for shared logic
   - Create `AuditService` integration
