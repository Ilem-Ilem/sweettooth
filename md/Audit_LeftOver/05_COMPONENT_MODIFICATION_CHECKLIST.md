# Component Modification Checklist

**Purpose:** Detailed checklist of all component changes needed  
**Total Components to Modify:** 15+  
**Estimated Time:** 20-25 hours  

---

## Employee Module (Priority: 🔴 CRITICAL)

### ✅ 1. Create.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/Create.php`  
**Current State:** Uses direct `ApprovalAuditRequest::create()`  
**Status:** Needs modification

#### Changes Required:
- [ ] Add `use App\Services\EmployeeApprovalService;`
- [ ] Add property: `public $creationReason = '';`
- [ ] Update `submit()` method (around line 327):
  ```php
  // OLD:
  ApprovalAuditRequest::create([...]);
  
  // NEW:
  if (!is_super_admin()) {
      EmployeeApprovalService::requestCreate(
          $approvalPayload,
          $this->creationReason
      );
  } else {
      $employee = Employee::create($employeeData);
      $employee->syncRoles($this->selectedRoles);
      EmployeeAuditService::logEmployeeCreation($employee, $user);
  }
  ```
- [ ] Add form field for `creationReason` in view
- [ ] Test create flow

#### Validation:
- [ ] Non-super admin: request created
- [ ] Super admin: employee created immediately
- [ ] Audit logs created
- [ ] Toast messages show correctly

---

### ✅ 2. Edit.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/Edit.php`  
**Current State:** Uses direct `ApprovalAuditRequest::create()`  
**Status:** Needs modification

#### Changes Required:
- [ ] Add `use App\Services\EmployeeApprovalService;`
- [ ] Add `use App\Services\EmployeeAuditService;`
- [ ] Update `submit()` method (around line 314):
  ```php
  // Extract changes from form data
  $changes = array_diff_key($data, $employee->getAttributes());
  
  if (!is_super_admin()) {
      EmployeeApprovalService::requestUpdate(
          $employee,
          $changes,
          $this->updateReason,
          ['roles' => $this->selectedRoles]
      );
  } else {
      $employee->update($changes);
      $employee->syncRoles($this->selectedRoles);
      EmployeeAuditService::logEmployeeUpdate(
          $employee,
          $changes,
          $user
      );
  }
  ```
- [ ] Detect role changes separately
- [ ] Test update flow

#### Validation:
- [ ] Non-super admin: request created
- [ ] Super admin: employee updated immediately
- [ ] Role changes tracked
- [ ] Audit logs created

---

### ✅ 3. Index.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/Index.php`  
**Current State:** Uses direct `ApprovalAuditRequest::create()` (line 303)  
**Status:** Needs modification

#### Changes Required:
- [ ] Add service imports
- [ ] Find bulk operation handling
- [ ] Replace direct request creation with service calls
- [ ] Update both single and bulk operations

#### Validation:
- [ ] Bulk creates work with requests
- [ ] Individual creates work

---

### ✅ 4. RolePermission/Index.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/Index.php`  
**Current State:** Direct `ApprovalAuditRequest::create()` (line 270) + `AuditService::log()` calls scattered  
**Status:** Needs significant refactoring

#### Changes Required:
- [ ] Add imports:
  ```php
  use App\Services\EmployeeApprovalService;
  use App\Services\EmployeeAuditService;
  ```

- [ ] Find role sync operations (around line 270)
- [ ] Replace with:
  ```php
  if (!is_super_admin()) {
      EmployeeApprovalService::requestRoleSync(
          $employee,
          $newRoles,
          $reason
      );
  } else {
      $employee->syncRoles($newRoles);
      EmployeeAuditService::logRoleChange(
          $employee,
          $oldRoles,
          $newRoles,
          'Updated by super admin',
          $user
      );
  }
  ```

- [ ] Find permission grants/revokes
- [ ] Replace with `EmployeeAuditService::logPermissionChange()`

- [ ] Consolidate scattered `AuditService::log()` calls

#### Validation:
- [ ] Role sync requests created correctly
- [ ] Permission changes logged
- [ ] No direct request creation
- [ ] Super admin bypass works

---

### ✅ 5. LeaveManagement/ApplyLeave.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ApplyLeave.php`  
**Current State:** Calls `AuditService::log()` (line 206)  
**Status:** Needs integration with LeaveAuditService

#### Changes Required:
- [ ] Add `use App\Services\LeaveAuditService;`
- [ ] Replace direct `AuditService::log()` with:
  ```php
  LeaveAuditService::logLeaveApplication(
      $leaveApplication,
      $user,
      'completed'
  );
  ```
- [ ] Add leave approval request creation (if not automated)

#### Validation:
- [ ] Leave audit logs created
- [ ] Uses dedicated service

---

## Approval Handler Integration (Priority: 🔴 CRITICAL)

### ✅ 6. AuditManagement/Index.php
**File:** `app/Livewire/BranchDashboard/AuditManagement/Index.php`  
**Current State:** Generic handlers, no employee-specific logic  
**Status:** Needs significant enhancement

#### Changes Required at line 240 (executeApprovedAction):
- [ ] Add imports:
  ```php
  use App\Services\EmployeeApprovalService;
  use App\Services\PayrollApprovalService;
  // ... etc for all new services
  ```

- [ ] Add to match statement:
  ```php
  'create:employee' => EmployeeApprovalService::executeCreate($request, $this->getApprover()),
  'update:employee' => EmployeeApprovalService::executeUpdate($request, $this->getApprover()),
  'delete:employee' => EmployeeApprovalService::executeDelete($request, $this->getApprover()),
  'sync:employee:roles' => EmployeeApprovalService::executeRoleSync($request, $this->getApprover()),
  // ... etc
  ```

- [ ] Add new method `handleEmployeeAction()` (around line 274):
  ```php
  private function handleEmployeeAction(ApprovalAuditRequest $request)
  {
      $parts = explode(':', $request->action);
      $action = $parts[0];
      
      return match ($action) {
          'create:employee' => EmployeeApprovalService::executeCreate($request, $this->getApprover()),
          'update:employee' => EmployeeApprovalService::executeUpdate($request, $this->getApprover()),
          // ... etc
      };
  }
  ```

- [ ] Add similar handlers for other modules as services are created

#### Validation:
- [ ] All employee actions dispatch correctly
- [ ] Services execute properly
- [ ] No exceptions thrown
- [ ] Audit logs created

---

## Production Module (Priority: 🟡 HIGH - Improve)

### ⚠️ 7. Products.php
**File:** `app/Livewire/BranchDashboard/Production/Products.php`  
**Current State:** Direct `ApprovalAuditRequest::create()` calls (line 419)  
**Status:** Should use service instead

#### Changes Required:
- [ ] Create `ProductApprovalService` (if not using generic handler)
- [ ] Replace direct calls with service methods
- [ ] Ensure consistency with other modules

---

### ⚠️ 8. Recipes/Add.php
**File:** `app/Livewire/BranchDashboard/Production/Recipes/Add.php`  
**Current State:** Direct request creation (line 391)  
**Status:** Should use service

#### Changes Required:
- [ ] Use `ProductionApprovalService::requestRecipeCreate()`
- [ ] Verify execution handler exists

---

### ⚠️ 9. Recipes/Edit.php
**File:** `app/Livewire/BranchDashboard/Production/Recipes/Edit.php`  
**Current State:** Direct request creation (line 264)  
**Status:** Should use service

#### Changes Required:
- [ ] Use `ProductionApprovalService::requestRecipeUpdate()`

---

## Inventory Module (Priority: 🟡 HIGH - Already Good)

### ✅ 10. Items.php
**File:** `app/Livewire/BranchDashboard/Inventory/Items.php`  
**Current State:** Uses `InventoryApprovalService` ✅  
**Status:** Already correct - no changes needed

### Note: This is the model to follow for other modules!

---

## Department Management (Priority: 🟡 HIGH)

### ✅ 11. DepartmentModule/Index.php
**File:** `app/Livewire/BranchDashboard/DepartmentModule/Index.php`  
**Current State:** Direct request creation  
**Status:** Needs service

#### Changes Required:
- [ ] Create `DepartmentApprovalService`
- [ ] Replace direct calls with service methods
- [ ] Add handling for create/update/delete

---

### ✅ 12. DepartmentModule/Category/Create.php
**File:** `app/Livewire/BranchDashboard/DepartmentModule/Cartegory/Create.php`  
**Current State:** Direct request creation  
**Status:** Needs service

#### Changes Required:
- [ ] Create `DepartmentCategoryApprovalService`
- [ ] Use service for creation requests

---

### ✅ 13. DepartmentModule/Category/Edit.php
**File:** `app/Livewire/BranchDashboard/DepartmentModule/Cartegory/Edit.php`  
**Current State:** Direct request creation  
**Status:** Needs service

#### Changes Required:
- [ ] Use `DepartmentCategoryApprovalService::requestUpdate()`

---

## Leave Management (Priority: 🟢 MEDIUM)

### ⚠️ 14. LeaveManagement/ApproveLeave.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ApproveLeave.php`  
**Current State:** Handles leave approvals  
**Status:** Needs leave audit service integration

#### Changes Required:
- [ ] Add `LeaveAuditService::logLeaveApproval()`
- [ ] Add `LeaveAuditService::logLeaveRejection()`

---

### ⚠️ 15. LeaveManagement/ManageAllocations.php
**File:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ManageAllocations.php`  
**Current State:** Manages leave allocations (line 139)  
**Status:** Needs service integration

#### Changes Required:
- [ ] Add `LeaveAuditService::logLeaveAllocationChange()`

---

## Callback Modules (Priority: 🟡 HIGH)

### ✅ 16. Inventory/Callbacks/ApproveCallbacks.php
**File:** `app/Livewire/BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php`  
**Current State:** Approves callbacks  
**Status:** Needs service

#### Changes Required:
- [ ] Create `CallbackApprovalService`
- [ ] Add approval request submission
- [ ] Integrate audit logging

---

### ✅ 17. Production/Callbacks/Index.php
**File:** `app/Livewire/BranchDashboard/Production/Callbacks/Index.php`  
**Current State:** Manages production callbacks  
**Status:** Needs service

#### Changes Required:
- [ ] Use `CallbackApprovalService`
- [ ] Add request submission for approvals

---

## Summary Table

| Component | File | Status | Changes | Time |
|-----------|------|--------|---------|------|
| **Employee Create** | Create.php | ❌ Broken | High | 2h |
| **Employee Edit** | Edit.php | ❌ Broken | High | 2h |
| **Employee Index** | Index.php | ❌ Broken | High | 1h |
| **Role Permission** | RolePermission/Index.php | ❌ Broken | High | 2h |
| **Approval Handler** | AuditManagement/Index.php | ⚠️ Incomplete | High | 3h |
| **Leave Apply** | ApplyLeave.php | ⚠️ Partial | Medium | 1h |
| **Leave Approve** | ApproveLeave.php | ⚠️ Partial | Medium | 1h |
| **Leave Allocations** | ManageAllocations.php | ⚠️ Partial | Medium | 1h |
| **Products** | Products.php | ⚠️ Needs Refactor | Low | 1h |
| **Recipes Add** | Recipes/Add.php | ⚠️ Needs Refactor | Low | 1h |
| **Recipes Edit** | Recipes/Edit.php | ⚠️ Needs Refactor | Low | 1h |
| **Department Index** | DepartmentModule/Index.php | ⚠️ Needs Service | Medium | 2h |
| **Department Cat Create** | Cartegory/Create.php | ⚠️ Needs Service | Medium | 1h |
| **Department Cat Edit** | Cartegory/Edit.php | ⚠️ Needs Service | Medium | 1h |
| **Inventory Callbacks** | Callbacks/ApproveCallbacks.php | ❌ Missing | High | 2h |
| **Production Callbacks** | Callbacks/Index.php | ❌ Missing | High | 2h |

**Total Estimated Time: 24-28 hours**

---

## Implementation Order

### Day 1-2: Critical Employee Module
1. Create both employee services
2. Modify Create.php, Edit.php
3. Modify RolePermission/Index.php
4. Test end-to-end

### Day 3: Approval Handler
1. Update AuditManagement/Index.php
2. Add employee action handler
3. Test approval flow

### Day 4-5: Other Components
1. Update leave components
2. Update department components
3. Create missing approval services

---

## Testing Checklist

For Each Component:
- [ ] Non-super admin: request created ✅
- [ ] Super admin: immediate execution ✅
- [ ] Audit logs created ✅
- [ ] Approval handler works ✅
- [ ] Rejection works ✅
- [ ] Permissions validated ✅
- [ ] No errors in logs ✅

---

## Code Review Checklist

Before merging each component:
- [ ] Follows service pattern
- [ ] No direct `ApprovalAuditRequest::create()` calls
- [ ] Uses correct service methods
- [ ] Proper error handling
- [ ] Audit logging included
- [ ] Tests passing
- [ ] Documentation updated

---

## Final Verification

After all components modified:
- [ ] Zero direct request creation (except in services)
- [ ] All approval handlers registered
- [ ] All services functional
- [ ] All tests passing
- [ ] No lint errors
- [ ] Performance acceptable
- [ ] Documentation complete
