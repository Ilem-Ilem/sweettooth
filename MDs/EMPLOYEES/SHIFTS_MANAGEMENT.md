# Shifts Management Module

## Overview
Manages employee shift schedules, assignments, and shift pattern configurations.

## Status: ⚠️ MINIMAL IMPLEMENTATION
**Progress:** ~20% Complete - Skeleton only, needs full development

---

## Components

### 1. **Index** (`Shifts/Index.php`)
**Status:** ⚠️ Skeleton Implementation Only

**Current Implementation:**
```php
- Minimal Livewire component
- Extends BaseComponent
- References EmployeeShift model
- render() method pointing to view
```

**What Exists:**
- ✅ Component registered at route
- ✅ View file exists
- ❌ No business logic
- ❌ No CRUD operations
- ❌ No filtering/search
- ❌ No modal interactions

**View:** `/resources/views/livewire/branch-dashboard/employee-module/shifts/index.blade.php`

---

## Database Models

### EmployeeShift
**Location:** `app/Models/EmployeeShift.php`

**Expected Structure** (needs verification):
- `id` - Primary key
- `employee_id` - UUID reference
- `shift_type` - (morning, afternoon, night, rotating, flexible)
- `start_date` - Effective date
- `end_date` - Termination date (nullable)
- `notes` - Additional info
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- `belongsTo(Employee)` - Employee assigned to shift
- `belongsTo(ShiftPattern)` - (if separate model exists)

---

## Missing Features (High Priority)

### 1. Shift CRUD Operations
- [ ] Create shift assignment
- [ ] Edit shift assignment
- [ ] Delete shift assignment
- [ ] Bulk shift assignments
- [ ] View shift assignments

### 2. Shift Management Interface
- [ ] List all shifts with pagination
- [ ] Search by employee name/number
- [ ] Filter by shift type
- [ ] Filter by date range
- [ ] Sort by employee, shift type, start date

### 3. Modal Operations
- [ ] Modal for creating new shift
- [ ] Modal for editing shift
- [ ] Modal for confirming deletions
- [ ] Modal for bulk operations

### 4. Business Logic
- [ ] Shift conflict detection
  - Prevent overlapping shifts for same employee
  - Check for consecutive shift violations
- [ ] Shift validation
  - Start date must be before end date
  - Cannot assign past shifts
  - Validate shift type against available types
- [ ] Employee shift history
  - Show all shifts assigned to employee
  - Track changes

### 5. Audit Implementation
- [ ] Log shift creation
  - Who created, when, which employee, shift type
- [ ] Log shift updates
  - Show what changed (from/to)
- [ ] Log shift deletions
  - Who deleted, when, which shift

---

## Recommended Implementation Approach

### Phase 1: Core CRUD & Display
```php
// Add to Index.php:
public bool $showShiftModal = false;
public bool $editMode = false;
public ?int $selectedShiftId = null;
public ?int $selectedEmployeeId = null;
public string $shiftType = 'morning';
public string $startDate = '';
public string $endDate = '';
public string $notes = '';

// Methods needed:
- getRowsProperty()        // Fetch shifts with pagination
- openCreateModal()        // Open modal for new shift
- openEditModal()          // Open modal for editing
- saveShift()              // Create/update shift
- deleteShift()            // Delete shift with confirmation
- closeShiftModal()        // Close modal
```

### Phase 2: Search & Filtering
```php
// Add properties:
public ?string $search = null;
public ?string $filterShiftType = null;
public ?string $filterDateFrom = null;
public ?string $filterDateTo = null;

// Add methods:
- applyFilters()           // Reset pagination on filter change
- resetFilters()           // Clear all filters
- getFilteredQuery()       // Base filtered query builder
```

### Phase 3: Validation & Business Rules
```php
// In saveShift():
- Validate dates (start <= end)
- Check for overlapping shifts
- Validate shift type against configuration
- Log changes via AuditService
```

### Phase 4: Advanced Features
- [ ] Bulk shift assignment (multiple employees)
- [ ] Shift templates (recurring patterns)
- [ ] Shift swap requests
- [ ] Export shift schedule
- [ ] Calendar view of shifts

---

## Audit Trail Implementation

### Required Logging Points
1. **Shift Creation**
   ```php
   AuditService::log(
       current_actor(),
       'create',
       $shift,
       "Assigned {$shift->shiftType} shift to {$employee->name} from {$start_date}",
       'completed'
   );
   ```

2. **Shift Update**
   ```php
   AuditService::log(
       current_actor(),
       'update',
       $shift,
       "Updated shift assignment: {changes}",
       'completed'
   );
   ```

3. **Shift Deletion**
   ```php
   AuditService::log(
       current_actor(),
       'delete',
       $shift,
       "Deleted {$shift->shiftType} shift for {$employee->name}",
       'completed'
   );
   ```

---

## Related Models & Tables

### Should Reference:
- `employees` table - Employee assignments
- `shifts` or `shift_patterns` table - Shift type definitions
- `audit_logs` table - For audit trail

### Current Issue:
⚠️ **No shift_patterns or shift_types table found** - May need to be created

---

## Routes
- `branch-dashboard.shifts.index` - View shifts
- (Need to define additional routes for CRUD)

---

## Dependencies
- BaseComponent (Livewire)
- WithPagination trait
- Interactions trait (TallStackUi)
- Employee model
- EmployeeShift model

---

## Integration Points

### With Employee Module
- Show shifts in employee detail view
- History tab could include shift changes
- Shift validation when setting employee status

### With Leave Management
- Consider leave dates when assigning shifts
- Prevent shift overlapping with approved leave

### With Role/Permission
- Only managers/admins can assign shifts
- Specific permission for shift management

---

## Next Steps (Priority Order)

1. **Define Shift Types** - Create seed data or enum
2. **Implement getRowsProperty()** - Display shifts in table
3. **Add Modal UI** - Create/Edit shift forms
4. **Implement CRUD** - Create/update/delete logic
5. **Add Validation** - Conflict detection, date validation
6. **Implement Audit Logging** - Track all changes
7. **Add Filtering** - Search and filters
8. **Create Tests** - Unit/Feature tests
9. **Documentation** - User guide

---

## Code Quality Notes

### Current Issues
- Empty implementation (skeleton only)
- No business logic
- No validation
- No audit logging
- No pagination/search

### Missing Best Practices
- No conflict detection
- No comprehensive error handling
- No detailed logging
- No permission checks
- No rate limiting

---

## Related Files to Create
- [ ] Modal component or Blade include
- [ ] Tests for shift validation
- [ ] Shift type seeder
- [ ] Migration for EmployeeShift (if not exists)
