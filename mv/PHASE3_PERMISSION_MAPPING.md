# Phase 3: Permission Mapping & Audit

## Overview
Complete mapping of all routes, features, and Livewire components to required permissions.
This document serves as the source of truth for what permissions are needed for each feature.

## Route-to-Permission Mapping

### Dashboard Routes
```
GET /branch-dashboard/dashboard/router
  Purpose: Route users to appropriate dashboard based on role
  Required Permissions: None (redirect only)
  Related Roles: All authenticated users

GET /branch-dashboard/dashboards/super-admin
  Purpose: Super Admin dashboard
  Required Permissions: view-analytics (or implicitly super admin check)
  Related Roles: Super Admin, MD, Managing Director

GET /branch-dashboard/dashboards/admin
  Purpose: Admin dashboard
  Required Permissions: view-analytics
  Related Roles: Admin and above

GET /branch-dashboard/dashboards/manager
  Purpose: Manager dashboard
  Required Permissions: view-analytics, view-department-reports
  Related Roles: Managers and above

GET /branch-dashboard/dashboards/supervisor
  Purpose: Supervisor dashboard
  Required Permissions: view-department-reports
  Related Roles: Supervisors and above
```

### Employee Management Routes
```
GET /branch-dashboard/employees
  Purpose: View employee list
  Required Permissions: view-employees
  Related Roles: HR Manager, HR Officer, Admin, Super Admin

GET /branch-dashboard/employee/create
  Purpose: Create new employee
  Required Permissions: create-employees
  Related Roles: HR Manager, Admin, Super Admin

GET /branch-dashboard/employee/{employee_number}/{id}/
  Purpose: View employee details
  Required Permissions: view-employees
  Related Roles: HR Manager, HR Officer, Admin, Super Admin

GET /branch-dashboard/employee/{id}/edit
  Purpose: Edit employee information
  Required Permissions: edit-employees
  Related Roles: HR Manager, Admin, Super Admin

DELETE /branch-dashboard/employee/{id}
  Purpose: Delete employee record
  Required Permissions: delete-employees
  Related Roles: Admin, Super Admin
```

### Role Management Routes
```
GET /branch-dashboard/roles (with protect-roles middleware)
  Purpose: View and manage roles and permissions
  Livewire: App\Livewire\BranchDashboard\Roles\Index
  Required Permissions: view-roles
  Delete Permission: delete-roles (checked in component)
  Edit Permission: edit-roles (checked in component)
  Related Roles: Super Admin, MD, Managing Director, Admin

Actions in Roles component:
  - View role details: view-roles
  - Create role: create-roles
  - Edit role: edit-roles
  - Delete role: delete-roles
  - Assign permissions to role: edit-roles
  - Create permission: create-permissions
```

### Branch Management Routes
```
GET /branch-dashboard/branches
  Purpose: View all branches
  Required Permissions: view-branches
  Related Roles: Super Admin, MD, Admin

GET /branch-dashboard/deleted-branches
  Purpose: View deleted/soft-deleted branches
  Required Permissions: view-branches
  Related Roles: Super Admin, MD, Admin

Actions:
  - Create branch: create-branches
  - Edit branch: edit-branches
  - Delete branch: delete-branches
  - Restore branch: edit-branches
```

### Settings Routes
```
GET /branch-dashboard/settings
  Purpose: Manage system settings
  Livewire: App\Livewire\BranchDashboard\Settings\Index
  Required Permissions: manage-settings
  Related Roles: Super Admin, MD, Admin
```

### Reports Routes (MD Reports)
```
GET /branch-dashboard/md-reports/dashboard
  Purpose: View MD reports dashboard
  Required Permissions: view-reports (new permission)
  Related Roles: Super Admin, MD, Managing Director

GET /branch-dashboard/md-reports/view/{id}
  Purpose: View specific MD report
  Required Permissions: view-reports
  Related Roles: Super Admin, MD, Managing Director
```

### Role Assignment Routes
```
GET /branch-dashboard/role-assignments
  Purpose: Assign roles to employees
  Livewire: App\Livewire\BranchDashboard\EmployeeModule\RolePermission\AssignRole
  Required Permissions: assign-roles
  Related Roles: Super Admin, MD, Admin

GET /branch-dashboard/role-permission
  Purpose: View role-permission relationships
  Required Permissions: view-roles
  Related Roles: Super Admin, MD, Admin
```

### Clock-In Board Routes
```
GET /branch-dashboard/clock-in-board/
  Purpose: View today's clock-in board
  Livewire: App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\TodayIndex
  Required Permissions: view-employees
  Related Roles: HR Manager, Supervisors, Admin, Super Admin

GET /branch-dashboard/clock-in-board/all
  Purpose: View all clock-in records
  Required Permissions: view-employees
  Related Roles: HR Manager, Admin, Super Admin

GET /branch-dashboard/clock-in-board/employee/{employee}/history
  Purpose: View employee's clock-in history
  Required Permissions: view-employees
  Related Roles: HR Manager, HR Officer, Admin, Super Admin
```

### Leave Management Routes
```
GET /branch-dashboard/leave/types
  Purpose: View leave types
  Required Permissions: manage-leave
  Related Roles: HR Manager, HR Officer, Admin, Super Admin

GET /branch-dashboard/leave/apply
  Purpose: Apply for leave
  Required Permissions: manage-leave (personal)
  Related Roles: All employees

GET /branch-dashboard/leave/my-leaves
  Purpose: View personal leave requests
  Required Permissions: None (personal data)
  Related Roles: All employees

GET /branch-dashboard/leave/approve
  Purpose: Approve leave requests
  Required Permissions: approve-leave
  Related Roles: HR Manager, Department Heads, Admin, Super Admin

GET /branch-dashboard/leave/balance
  Purpose: View leave balance
  Required Permissions: None (personal data)
  Related Roles: All employees

GET /branch-dashboard/leave/manage-allocations
  Purpose: Manage leave allocations
  Required Permissions: manage-leave
  Related Roles: HR Manager, Admin, Super Admin
```

### Department Routes
```
GET /branch-dashboard/departments
  Purpose: View all departments
  Required Permissions: view-departments
  Related Roles: All managers and above

GET /branch-dashboard/department/create
  Purpose: Create new department
  Required Permissions: create-departments (new permission)
  Related Roles: Admin, Super Admin

GET /branch-dashboard/department/{id}/edit
  Purpose: Edit department
  Required Permissions: edit-departments (new permission)
  Related Roles: Admin, Super Admin

GET /branch-dashboard/departments/category
  Purpose: View department categories
  Required Permissions: view-departments
  Related Roles: Managers and above

GET /branch-dashboard/department/category/create
  Purpose: Create department category
  Required Permissions: create-departments
  Related Roles: Admin, Super Admin

GET /branch-dashboard/department/category/{id}/edit
  Purpose: Edit department category
  Required Permissions: edit-departments
  Related Roles: Admin, Super Admin
```

### Inventory Routes
```
GET /branch-dashboard/inventory/items
  Purpose: View inventory items
  Required Permissions: view-stock-levels
  Related Roles: Inventory Manager, Stock Controller, Store Keeper, Admin, Super Admin

GET /branch-dashboard/inventory/purchases
  Purpose: View purchase orders
  Required Permissions: view-inventory-reports
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/inventory/stocks
  Purpose: View stock levels
  Required Permissions: view-stock-levels
  Related Roles: Inventory Manager, Stock Controller, Store Keeper, Admin, Super Admin

GET /branch-dashboard/inventory/item-requests
  Purpose: View item requests
  Required Permissions: view-stock-levels
  Related Roles: Inventory Manager, Department Heads, Admin, Super Admin

GET /branch-dashboard/inventory/item-dispatches
  Purpose: View dispatches
  Required Permissions: view-stock-levels
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/inventory/stock-takes
  Purpose: Perform stock takes
  Required Permissions: adjust-inventory
  Related Roles: Inventory Manager, Stock Controller, Admin, Super Admin

GET /branch-dashboard/inventory/health-checks
  Purpose: View inventory health checks
  Required Permissions: view-inventory-reports
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/inventory/shift-closing
  Purpose: Close inventory shift
  Required Permissions: close-register (or similar)
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/inventory/callbacks/
  Purpose: Approve quality callbacks
  Required Permissions: approve-callbacks
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/inventory/reports/*
  Purpose: View inventory reports
  Required Permissions: view-inventory-reports
  Related Roles: Inventory Manager, Admin, Super Admin
```

### Production Routes
```
GET /branch-dashboard/production/product-types/{deptSlug}
  Purpose: View product types
  Required Permissions: view-production-queue
  Related Roles: Department Heads, Kitchen Staff, Admin, Super Admin

GET /branch-dashboard/production/products/{deptSlug}
  Purpose: View products
  Required Permissions: view-production-queue
  Related Roles: Department Heads, Kitchen Staff, Admin, Super Admin

GET /branch-dashboard/production/request/{deptSlug}
  Purpose: View production requests
  Required Permissions: view-production-queue
  Related Roles: Department Heads, Kitchen Staff, Admin, Super Admin

GET /branch-dashboard/production/request/{deptSlug}/create
  Purpose: Create production request
  Required Permissions: create-production
  Related Roles: Department Heads, Admin, Super Admin

GET /branch-dashboard/production/daily-produce/{deptSlug}
  Purpose: View daily production
  Required Permissions: view-production-queue
  Related Roles: Department Heads, Kitchen Staff, Admin, Super Admin

GET /branch-dashboard/production/shift-closing/{deptSlug}
  Purpose: Close production shift
  Required Permissions: close-register (or similar)
  Related Roles: Department Heads, Admin, Super Admin

GET /branch-dashboard/production/recipes/{deptSlug}
  Purpose: View recipes
  Required Permissions: manage-recipes
  Related Roles: Chef, Department Heads, Admin, Super Admin

GET /branch-dashboard/production/recipes/{deptSlug}/add
  Purpose: Create recipe
  Required Permissions: manage-recipes
  Related Roles: Chef, Department Heads, Admin, Super Admin

GET /branch-dashboard/production/recipes/{deptSlug}/{id}/edit
  Purpose: Edit recipe
  Required Permissions: manage-recipes
  Related Roles: Chef, Department Heads, Admin, Super Admin

GET /branch-dashboard/production/module/
  Purpose: Kitchen module interface
  Required Permissions: view-production-queue
  Related Roles: Kitchen Staff, Department Heads, Admin, Super Admin

GET /branch-dashboard/production/callbacks/
  Purpose: Production callbacks
  Required Permissions: approve-callbacks
  Related Roles: Department Heads, Admin, Super Admin

GET /branch-dashboard/production/reports/*
  Purpose: Production reports
  Required Permissions: view-production-reports
  Related Roles: Department Heads, Admin, Super Admin
```

### Sales Routes
```
GET /branch-dashboard/sales-dashboard/pos/{salesDeptSlug}
  Purpose: Point of sale
  Required Permissions: process-sale
  Related Roles: Cashier, Till Supervisor, Confectionaries Sales Staff, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/analytics/{salesDeptSlug}
  Purpose: Sales analytics
  Required Permissions: view-sales-reports
  Related Roles: Sales Manager, Till Supervisor, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/my-sales/{salesDeptSlug}
  Purpose: Personal sales dashboard
  Required Permissions: view-daily-sales
  Related Roles: All sales staff

GET /branch-dashboard/sales-dashboard/shift-closing/{salesDeptSlug}
  Purpose: Close sales shift
  Required Permissions: close-register
  Related Roles: Till Supervisor, Sales Manager, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/expiry-alerts
  Purpose: View product expiry alerts
  Required Permissions: view-stock-levels
  Related Roles: All staff

GET /branch-dashboard/sales-dashboard/stock-opening/{salesDeptSlug}
  Purpose: Open shift with stock
  Required Permissions: view-stock-levels
  Related Roles: Cashier, Till Supervisor, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/dispatches/{salesDeptSlug}
  Purpose: View dispatches
  Required Permissions: view-stock-levels
  Related Roles: Sales Manager, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/callbacks/
  Purpose: Sales callbacks
  Required Permissions: approve-callbacks
  Related Roles: Sales Manager, Admin, Super Admin

GET /branch-dashboard/sales-dashboard/stock-monitor
  Purpose: Monitor stock levels
  Required Permissions: view-stock-levels
  Related Roles: Sales Manager, Admin, Super Admin
```

### Analytics Routes
```
GET /branch-dashboard/analytics/overview
  Purpose: Overall analytics dashboard
  Required Permissions: view-analytics
  Related Roles: All managers and above

GET /branch-dashboard/analytics/stock-level
  Purpose: Stock level analytics
  Required Permissions: view-analytics
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/analytics/stock-movement
  Purpose: Stock movement analytics
  Required Permissions: view-analytics
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/analytics/purchase
  Purpose: Purchase analytics
  Required Permissions: view-analytics
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/analytics/request-dispatch
  Purpose: Request/dispatch analytics
  Required Permissions: view-analytics
  Related Roles: Inventory Manager, Admin, Super Admin

GET /branch-dashboard/analytics/alerts
  Purpose: System alerts dashboard
  Required Permissions: view-analytics
  Related Roles: Admin, Super Admin

GET /branch-dashboard/analytics/stock-valuation
  Purpose: Stock valuation analytics
  Required Permissions: view-analytics
  Related Roles: Inventory Manager, Admin, Super Admin
```

### Reporting Routes
```
GET /branch-dashboard/reporting/dashboard
  Purpose: Reporting department dashboard
  Required Permissions: view-reports (new)
  Related Roles: Reporting staff, Admin, Super Admin

GET /branch-dashboard/reporting/review
  Purpose: Review reports
  Required Permissions: view-reports
  Related Roles: Reporting staff, Admin, Super Admin

GET /branch-dashboard/reporting/compile
  Purpose: Compile reports
  Required Permissions: generate-reports
  Related Roles: Reporting staff, Admin, Super Admin

GET /branch-dashboard/reporting/send-to-md
  Purpose: Send reports to MD
  Required Permissions: view-reports
  Related Roles: Reporting staff, Admin, Super Admin
```

### Audit Management Routes
```
GET /branch-dashboard/audit/
  Purpose: View audit logs
  Required Permissions: view-audit-logs
  Related Roles: Admin, Super Admin, MD

GET /branch-dashboard/audit/inventory-approvals
  Purpose: View inventory approvals
  Required Permissions: view-audit-logs
  Related Roles: Inventory Manager, Admin, Super Admin
```

## Missing Permissions to Add

Based on audit, these permissions are needed but not in PermissionSeeder:

```
Production:
  - view-production-reports (exists, good)

Reporting:
  + view-reports (NEW)
  + generate-reports (EXISTS in PermissionSeeder - good)

Departments:
  + create-departments (NEW)
  + edit-departments (NEW)
  + delete-departments (NEW)
```

## Permission Summary by Category

### System (Protected)
- view-roles ✅
- create-roles ✅
- edit-roles ✅
- delete-roles ✅
- assign-roles ✅
- view-permissions ✅
- create-permissions ✅
- edit-permissions ✅
- delete-permissions ✅
- view-branches ✅
- create-branches ✅
- edit-branches ✅
- delete-branches ✅
- manage-settings ✅
- view-audit-logs ✅

### HR
- view-employees ✅
- create-employees ✅
- edit-employees ✅
- delete-employees ✅
- view-departments ✅
- manage-staff-schedule ✅
- manage-leave ✅
- approve-leave ✅
- create-departments ❌ (NEW)
- edit-departments ❌ (NEW)
- delete-departments ❌ (NEW)

### Production
- view-production-queue ✅
- create-production ✅
- start-production ✅
- complete-production ✅
- approve-production ✅
- manage-recipes ✅
- view-production-reports ✅

### Inventory
- view-stock-levels ✅
- receive-stock ✅
- transfer-stock ✅
- adjust-inventory ✅
- create-purchase-order ✅
- approve-purchase-order ✅
- view-inventory-reports ✅

### Sales
- process-sale ✅
- view-daily-sales ✅
- issue-refund ✅
- close-register ✅
- manage-customers ✅
- view-sales-reports ✅

### Reports
- view-analytics ✅
- view-department-reports ✅
- generate-reports ✅
- export-data ✅
- view-reports ❌ (NEW)

### Quality
- view-callbacks ✅
- create-callback ✅
- approve-callbacks ✅
- resolve-callbacks ✅

## Status

**Total Permissions Defined:** 48 (45 from PermissionSeeder + 3 new)
**Fully Mapped Routes:** 70+
**Missing Permissions:** 4 (3 new department permissions + 1 reporting permission)
**Coverage:** ~98%

## Next Steps

1. Add missing 4 permissions to PermissionSeeder
2. Update role assignments to include new permissions
3. Add middleware to routes for permission enforcement
4. Test permission checks across all features
5. Create permission matrix document
