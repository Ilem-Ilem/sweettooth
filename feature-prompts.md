feature: Realtime Broadcasting Infrastructure
prompt
Goal
- Provide event-driven realtime updates across inventory features using broadcasting. Support WebSockets (preferred) with Livewire polling fallback.

Outcomes
- UI updates in <2s after stock/request/dispatch/shift changes
- Private channels enforce branch/department scoping and roles
- No data loss: fallback polling if websockets unavailable

Related file structure (existing and to create)
- app/Providers/AppServiceProvider.php (register observers, event listeners)
- app/Providers/FortifyServiceProvider.php (guards, if needed)
- app/Http/Middleware/BranchMiddleware.php
- app/Http/Middleware/IsAdmin.php
- routes/branch-route.php (branch-scoped routes)
- routes/web.php (general routes)
- resources/js/app.js (Echo bootstrap; optional if using websockets)
- resources/views/components/layouts/app.blade.php (load Echo, add data-branch-id)
- app/Events/Inventory/ItemMovementRecorded.php (broadcast)
- app/Events/Inventory/StockLevelsUpdated.php (broadcast)
- app/Events/Inventory/ItemRequestUpdated.php (broadcast)
- app/Events/Inventory/ItemDispatchUpdated.php (broadcast)
- app/Events/Shift/EmployeeShiftUpdated.php (broadcast)
- config/broadcasting.php (driver)
- config/queue.php (broadcast queue)

Existing database schema relevant (from migrations)
- branches (2025_10_02_160630_create_branches_table.php)
  - id uuid PK; name unique; code unique; location; phone?; email unique nullable; manager_user_id FK employees.id (set null)
  - country, state, city, postal_code, timezone; is_active bool; timestamps; softDeletes
- departments (2025_10_04_053818_create_departments_table.php)
  - id bigint PK; branch_id uuid FK branches (nullable); category_id uuid FK department_categories; name unique; description; timestamps
- employees (2025_10_02_163836_create_employees_table.php)
  - id uuid PK; branch_id uuid FK branches (nullable); department_id bigint FK departments (nullable)
  - employee_number unique; name; email unique; many HR fields; status enum; timestamps; softDeletes
- items (2025_10_09_012549_create_items_table.php)
  - id bigint PK; branch_id uuid FK branches; name; sku unique; category enum(raw_material,packaging,consumable,equipment)
  - uom enum(grams,kg,liters,ml,pcs,units,bags,cartons); description; reorder_level dec(10,2) nullable; max_stock_level dec(10,2) nullable; status enum(active,inactive); timestamps
- stocks (2025_10_09_012615_create_stocks_table.php)
  - id bigint PK; branch_id uuid FK branches; item_id bigint FK items
  - quantity_available dec(12,2) default 0; quantity_reserved dec(12,2) default 0; quantity_damaged dec(12,2) default 0; average_cost dec(12,4) default 0
  - last_stock_take_date date nullable; health_status enum(good,warning,critical,expired) default good; expiry_date date nullable; timestamps
  - unique(branch_id, item_id)
- stock_movements (2025_10_09_012617_create_stock_movements_table.php)
  - id bigint PK; stock_id bigint FK stocks; type enum(in,out,adjustment,transfer,damaged,return); quantity dec(12,2)
  - quantity_before dec(12,2); quantity_after dec(12,2); reference_type string nullable; reference_id bigint nullable
  - moved_by uuid FK employees nullable; notes text nullable; movement_date timestamp; timestamps
- item_requests (2025_10_09_012619_create_item_requests_table.php)
  - id bigint PK; branch_id uuid FK branches; department_id bigint FK departments; requested_by uuid FK employees
  - request_number unique; request_date date; shift enum(morning,afternoon) nullable; status enum(pending,approved,partially_dispatched,completed,cancelled) default pending
  - approved_by uuid FK employees nullable (set null); approved_at timestamp nullable; notes text nullable; timestamps
- item_request_details (2025_10_09_012621_create_item_request_details_table.php)
  - id bigint PK; request_id bigint FK item_requests; item_id bigint FK items
  - quantity_requested dec(12,2); quantity_approved dec(12,2) default 0; quantity_dispatched dec(12,2) default 0; uom enum(...); notes; timestamps
- item_dispatches (2025_10_09_012622_create_item_dispatches_table.php)
  - id bigint PK; request_id bigint FK item_requests; item_id bigint FK items; dispatched_by uuid FK employees; received_by uuid FK employees
  - quantity dec(12,2); uom enum(...); dispatch_time timestamp; received_time timestamp nullable; shift enum(morning,afternoon) nullable; notes text; timestamps

Channels and auth
- private.branch.{branchId} (employees belonging to branch)
- private.department.{departmentId} (employees in department)
- private.employee.{employeeId} (self)
- Authorize via branch membership + spatie role

Listeners and fallback
- Stock dashboards: ItemMovementRecorded, StockLevelsUpdated
- Request/Dispatch boards: ItemRequestUpdated, ItemDispatchUpdated
- Shift components: EmployeeShiftUpdated
- Fallback: wire:poll.keep-alive.2s on lists/dashboards

Tests
- Broadcast dispatched on create/update of movements/requests/dispatches/shifts
- Channel auth denies cross-branch/department

Acceptance criteria
- Components reflect updates <2s; polling fallback valid


feature: Shift Clock-In/Out
prompt
Goal
- Enforce that staff must clock in before performing inventory actions; associate actions with shift for audit and analytics.

Outcomes
- Single active shift per employee per branch/department
- All request/dispatch/movement records carry shift context once implemented

Related file structure (existing and to create)
- app/Livewire/Shift/Clock.php → resources/views/livewire/shift/clock.blade.php
- app/Livewire/Shift/MyShifts.php → resources/views/livewire/shift/my-shifts.blade.php
- app/Livewire/Shift/Manage.php → resources/views/livewire/shift/manage.blade.php
- app/Policies/EmployeeShiftPolicy.php
- app/Services/ShiftService.php
- routes/branch-route.php: /shift/clock, /shift/manage, /shift/my
- app/Events/Shift/EmployeeShiftUpdated.php

Existing database schema relevant
- employees (see above)
- branches, departments (see above)
- item_requests and item_dispatches include a shift enum field currently (morning/afternoon), but no shift table

New database objects to add (not yet present)
- employee_shifts (NEW migration)
  - id bigint PK; employee_id uuid FK employees; branch_id uuid FK branches; department_id bigint FK departments
  - shift_name string; clock_in_at datetime; clock_out_at datetime nullable; status enum(open,closed)
  - opened_by uuid; closed_by uuid nullable; opening_notes text nullable; closing_notes text nullable; timestamps
- Add nullable shift_id bigint FK employee_shifts to:
  - item_requests, item_dispatches, stock_movements

Validation and rules
- Prevent overlap per employee per branch/department (status=open)
- Require active shift to perform: request, dispatch, adjustment

Events
- EmployeeShiftUpdated broadcast on open/close

Acceptance criteria
- UI shows active shift; actions blocked without active shift


feature: Purchase Management
prompt
Goal
- Record purchases and line items; update stocks and average cost; create stock movements (type: in) and broadcast updates.

Outcomes
- Accurate stock increases and cost averaging per item/branch
- Full purchase audit and linkage to movements

Related file structure
- app/Livewire/Inventory/Purchases/Index.php → resources/views/livewire/inventory/purchases/index.blade.php
- app/Livewire/Inventory/Purchases/Create.php → resources/views/livewire/inventory/purchases/create.blade.php
- app/Livewire/Inventory/Purchases/View.php → resources/views/livewire/inventory/purchases/view.blade.php
- app/Models/Purchase.php, app/Models/PurchaseItem.php, app/Models/Stock.php, app/Models/Item.php, app/Models/StockMovement.php
- app/Policies/PurchasePolicy.php
- app/Services/InventoryService.php (upsertStock, recalcAverageCost)
- routes/branch-route.php: /inventory/purchases (index, create, view)

Existing database schema (from migrations)
- purchases (2025_10_09_012613_create_purchases_table.php)
  - id bigint PK; branch_id uuid FK branches; recorded_by uuid FK employees; purchase_number unique; purchase_date date
  - supplier_name; supplier_contact nullable; total_fob_fc dec(12,2) default 0; total_fob_ngn dec(12,2) default 0
  - other_costs dec(12,2) default 0; landing_cost dec(12,2) default 0; total_cost dec(12,2) default 0
  - currency char(3) default 'NGN'; exchange_rate dec(10,4) default 1; payment_status enum(paid,partial,pending) default pending; notes text nullable; timestamps
- purchase_items (2025_10_09_012614_create_purchase_items_table.php)
  - id bigint PK; purchase_id bigint FK purchases (cascade); item_id bigint FK items (restrict)
  - quantity dec(12,2); uom enum(grams,kg,liters,ml,pcs,units,bags,cartons)
  - fob_fc dec(12,2) default 0; fob_ngn dec(12,2) default 0; other_costs dec(12,2) default 0; landing_cost dec(12,2) default 0
  - total_cost dec(12,2); cost_per_unit dec(12,4); timestamps
- stocks (see above) with unique(branch_id,item_id)
- stock_movements (see above)

Validation (Create)
- Header: purchase_date date; supplier_name required; currency in(NGN,FC); exchange_rate numeric if FC
- Lines: item_id exists; quantity > 0; cost fields numeric ≥ 0; uom in enum

Average cost logic
- new_avg = (old_qty*old_avg + in_qty*unit_cost) / (old_qty + in_qty); transaction + row lock on stocks

Events
- ItemMovementRecorded(type: in), StockLevelsUpdated

Acceptance criteria
- Purchase creates/updates Stocks and appends StockMovement with before/after


feature: Inventory Stock Dashboard (Stock Levels + Realtime)
prompt
Goal
- Provide stock overview with filters and realtime KPIs/feeds for a branch.

Outcomes
- Operators see low/critical/expiring items and live movement feed

Related file structure
- app/Livewire/Inventory/Stock/Index.php → resources/views/livewire/inventory/stock/index.blade.php
- app/Livewire/Inventory/Stock/RealtimeDashboard.php → resources/views/livewire/inventory/stock/realtime-dashboard.blade.php
- routes/branch-route.php: /inventory/stock, /inventory/stock/realtime
- Models: Stock, Item, StockMovement

Existing database schema
- items (see items above) → category, uom, reorder_level, max_stock_level
- stocks (see stocks above) → quantities, average_cost, last_stock_take_date, health_status, expiry_date
- stock_movements (for feed)

UI structure
- Index: filter bar (branch, category, stock state, health, date range, search) → table columns: Item, SKU, Branch, Category, Available, Reserved, Damaged, Total, Reorder, Health, Last Stock Take, Actions
- Realtime: KPI cards (low, expiring, damaged, total items) → live feed entries (type badge, qty, by, at, before/after)

Acceptance criteria
- Filtered lists paginate; feed updates without manual refresh


feature: Item Request Management (Create, Pending, Process)
prompt
Goal
- Departments request items; inventory manager approves quantities based on stock; reserved quantities updated; realtime signals emitted.

Outcomes
- Clear pipeline and reservations; no over-approval

Related file structure
- app/Livewire/Inventory/Requests/Pending.php → resources/views/livewire/inventory/requests/pending.blade.php
- app/Livewire/Inventory/Requests/Process.php → resources/views/livewire/inventory/requests/process.blade.php
- app/Livewire/Inventory/Requests/History.php → resources/views/livewire/inventory/requests/history.blade.php
- routes/branch-route.php: /inventory/requests (pending, process/{id}, history)
- Models: ItemRequest, ItemRequestDetail, Stock
- Policies: ItemRequestPolicy
- Services: InventoryService (reserveStock)

Existing database schema
- item_requests (2025_10_09_012619_create_item_requests_table.php)
  - id; branch_id uuid FK branches; department_id bigint FK departments; requested_by uuid FK employees
  - request_number unique; request_date; shift enum(morning,afternoon) nullable; status enum(pending,approved,partially_dispatched,completed,cancelled)
  - approved_by uuid FK employees nullable set null; approved_at nullable; notes; timestamps
- item_request_details (2025_10_09_012621_create_item_request_details_table.php)
  - id; request_id FK item_requests; item_id FK items; quantity_requested; quantity_approved default 0; quantity_dispatched default 0; uom enum; notes; timestamps
- stocks (quantities available/reserved)

Validation
- Request header: department_id required; request_date required; lines required
- Approval: quantity_approved ≤ (quantity_available - quantity_reserved)

Workflow
- Create: status=pending
- Approve: increment stocks.quantity_reserved; status=approved or partial
- Reject: status=cancelled

Events
- ItemRequestUpdated, StockLevelsUpdated

Acceptance criteria
- Reservations reflect approvals; UI updates in realtime


feature: Dispatch Management
prompt
Goal
- Fulfill approved requests; decrement reserved and available; create movement (out); confirm receipt.

Outcomes
- Accurate consumption tracking tied to request lines; clean audit trail

Related file structure
- app/Livewire/Inventory/Dispatches/Board.php → resources/views/livewire/inventory/dispatches/board.blade.php
- app/Livewire/Inventory/Dispatches/Index.php → resources/views/livewire/inventory/dispatches/index.blade.php
- routes/branch-route.php: /inventory/dispatches (board, index)
- Models: ItemDispatch, ItemRequest, ItemRequestDetail, Stock, StockMovement
- Policies: ItemDispatchPolicy
- Services: InventoryService (dispatchStock)

Existing database schema
- item_dispatches (2025_10_09_012622_create_item_dispatches_table.php)
  - id; request_id FK item_requests; item_id FK items; dispatched_by uuid FK employees; received_by uuid FK employees
  - quantity dec(12,2); uom enum; dispatch_time; received_time nullable; shift enum(morning,afternoon) nullable; notes; timestamps
- item_request_details: quantity_approved, quantity_dispatched
- stocks: quantity_available, quantity_reserved
- stock_movements: movement trail

Validation rules
- quantity_to_dispatch ≤ (quantity_approved - quantity_dispatched)

Workflow
- Dispatch: decrement reserved and available; create StockMovement(type: out) with before/after; update request detail quantity_dispatched
- Receive: set received_by/time

Events
- ItemDispatchUpdated, ItemMovementRecorded, StockLevelsUpdated

Acceptance criteria
- Accurate stock change; request lines never over-dispatched


feature: Stock Movement Timeline
prompt
Goal
- Provide detailed, filterable movement history with audit details and references.

Outcomes
- Full traceability of stock changes

Related file structure
- app/Livewire/Inventory/Reports/StockMovement.php �� resources/views/livewire/inventory/reports/stock-movement.blade.php
- routes/branch-route.php: /inventory/reports/stock-movement
- Models: StockMovement, Stock, Item

Existing database schema
- stock_movements (2025_10_09_012617_create_stock_movements_table.php)
  - id; stock_id FK stocks; type enum(in,out,adjustment,transfer,damaged,return); quantity; quantity_before; quantity_after
  - reference_type string nullable; reference_id bigint nullable; moved_by uuid FK employees nullable; notes text nullable; movement_date timestamp; timestamps
- stocks: item_id, branch_id; items: name, sku, uom, category

Filters
- item, branch, type, date range, employee, shift (once added)

Timeline entry fields
- type badge; qty (+/-), uom; before/after; actor; timestamp; notes; reference link

Indexes to consider
- stock_movements: (stock_id), (type), (movement_date)

Acceptance criteria
- Accurate, paginated timeline; export optional


feature: Stock Take Management
prompt
Goal
- Conduct stock takes, compute variances, reconcile via adjustment movements.

Outcomes
- Books align with physical counts; audit preserved

Related file structure
- app/Livewire/Inventory/Stock/StockTake.php → resources/views/livewire/inventory/stock/stock-take.blade.php
- routes/branch-route.php: /inventory/stock/stock-take
- Models: StockTake, StockTakeDetail, Stock, StockMovement

Existing database schema
- stock_takes (2025_10_09_012623_create_stock_takes_table.php)
  - id; branch_id uuid FK branches; stock_take_number unique; stock_take_date date; type enum(daily,weekly,monthly,annual,ad_hoc)
  - conducted_by uuid FK employees; status enum(in_progress,completed,verified) default in_progress; verified_by uuid FK employees nullable set null; verified_at timestamp nullable; notes text nullable; timestamps
- stock_take_details (2025_10_09_012626_create_stock_take_details_table.php)
  - id; stock_take_id FK stock_takes; item_id FK items; system_quantity; physical_quantity; variance; variance_type enum(surplus,shortage,match) default match; notes; timestamps

Workflow
- Create → add details → compute variance → finalize → create StockMovement(type: adjustment), update Stocks and last_stock_take_date

Acceptance criteria
- Variances reconciled; movements with before/after created


feature: Health Check Management
prompt
Goal
- Record expiry/condition checks; optionally adjust damaged quantities.

Outcomes
- Visibility on expiring/poor-condition items with audit

Related file structure
- app/Livewire/Inventory/Stock/HealthCheck.php → resources/views/livewire/inventory/stock/health-check.blade.php
- routes/branch-route.php: /inventory/stock/health-check
- Models: HealthCheck, Stock, StockMovement

Existing database schema
- health_checks (2025_10_09_012627_create_health_checks_table.php)
  - id; stock_id FK stocks; checked_by uuid FK employees; check_date date; condition enum(excellent,good,fair,poor,damaged,expired)
  - quantity_affected dec(12,2) nullable; observations text nullable; action_taken text nullable; timestamps
- stocks: quantity_damaged, health_status, expiry_date

Workflow
- Log check; if write-off/mark damaged, create StockMovement(type: damaged) and adjust stock quantities

Acceptance criteria
- Health status and warnings visible; movements created when applicable


feature: Low Stock Report
prompt
Goal
- Identify items at/below thresholds with export and branch/category filters.

Outcomes
- Clear replenishment list for procurement/inventory

Related file structure
- app/Livewire/Inventory/Reports/LowStock.php → resources/views/livewire/inventory/reports/low-stock.blade.php
- routes/branch-route.php: /inventory/reports/low-stock
- Models: Stock, Item

Existing database schema
- items: reorder_level dec(10,2), max_stock_level dec(10,2), category enum, uom enum
- stocks: quantity_available dec(12,2), branch_id

Logic
- Critical: available < 0.25 * reorder_level; Low: available < reorder_level; Overstock: available > max_stock_level

Acceptance criteria
- Correct classification; export works


feature: Expiry Warnings Report
prompt
Goal
- Show items nearing/past expiry with filters and urgency level.

Outcomes
- Proactive actions on expiring inventory

Related file structure
- app/Livewire/Inventory/Reports/ExpiryWarnings.php → resources/views/livewire/inventory/reports/expiry-warnings.blade.php
- routes/branch-route.php: /inventory/reports/expiry-warnings
- Models: Stock, Item

Existing database schema
- stocks: expiry_date date nullable; health_status enum(good,warning,critical,expired)
- items: name, category, uom

UI structure
- Filters: branch, category, daysToExpiry (default 30)
- Table: item, sku, branch, expiry_date, days_left, health badge

Acceptance criteria
- Sorted by ascending days_left; correctly marks expired


feature: Usage Patterns Analytics
prompt
Goal
- Analyze consumption by item, department, employee, and time window; provide chat-style usage feed and aggregations.

Outcomes
- Insight into usage trends to inform purchasing and production

Related file structure
- app/Livewire/Inventory/Reports/UsagePatterns.php → resources/views/livewire/inventory/reports/usage-patterns.blade.php
- routes/branch-route.php: /inventory/reports/usage-patterns
- Models: StockMovement, Item, Department, Employee, ItemDispatch, ItemRequestDetail

Existing database schema
- stock_movements: type enum(in,out,adjustment,transfer,damaged,return); quantity; movement_date; moved_by uuid FK employees
- item_dispatches: request_id FK item_requests; item_id FK items; dispatched_by/received_by uuid FK employees; quantity; times
- item_request_details: request_id FK item_requests; item_id FK items; quantities

Analytics logic
- Consumption = sum of movements where type in (out, damaged, negative adjustments) grouped by time bucket, item, department (via request), employee

Acceptance criteria
- Accurate totals and deltas; feed entries link to source movements


feature: Employee Activity Feed
prompt
Goal
- Unified stream of inventory-related actions with shift context; chat-style visual.

Outcomes
- Accountability and transparency of actions

Related file structure
- app/Livewire/Inventory/Reports/EmployeeActivity.php → resources/views/livewire/inventory/reports/employee-activity.blade.php
- routes/branch-route.php: /inventory/reports/employee-activity
- Models: ItemRequest, ItemRequestDetail, ItemDispatch, StockMovement, Employee

Existing database schema
- item_requests: requested_by, approved_by, approved_at, status
- item_dispatches: dispatched_by, received_by, dispatch_time, received_time
- stock_movements: moved_by, movement_date, type

UI structure
- Filters: employee, department, action type, date range, shift
- Entry format: avatar, name, action verb, entity ref, qty, timestamp, shift name

Acceptance criteria
- Includes all relevant actions with links; respects branch scoping


feature: Navigation, Routing, and Permissions
prompt
Goal
- Expose inventory and shift features via branch-scoped routes; protect with roles/permissions and guards.

Outcomes
- Correctly visible/accessible pages per role and branch

Related file structure
- routes/branch-route.php, routes/web.php
- app/Http/Middleware/BranchMiddleware.php, app/Http/Middleware/IsAdmin.php
- config/permission.php
- database/migrations/2025_10_07_030703_create_permission_tables.php
- database/seeders/InventoryPermissionSeeder.php, PermissionSeeder.php, RoleSeeder.php
- resources/views/components/layouts/app.blade.php (sidebar: Inventory + Shift)

Existing database schema (Spatie core)
- permissions: id, name, guard_name, unique(name,guard_name)
- roles: id, name, guard_name (+ optional team FK)
- model_has_permissions: permission_id, model_type, model_id
- model_has_roles: role_id, model_type, model_id
- role_has_permissions: permission_id, role_id

Permissions map (ability names)
- purchases.view, purchases.create
- stock.view, stock.realtime
- requests.create, requests.process
- dispatch.view, dispatch.process
- reports.view, reports.lowstock, reports.expiry, reports.movements, reports.usage, reports.activity
- shift.clock, shift.manage

Acceptance criteria
- Unauthorized users 403; menus hide links when lacking permission


feature: Data Model Integrity and Indexing
prompt
Goal
- Ensure data correctness and performance through constraints and indexes; enforce referential integrity.

Outcomes
- Fast queries and safe concurrent updates

Related file structure
- database/migrations/* (add indexes where missing)
- app/Models/* (relationships, guarded/fillable)

Existing indexes/constraints (from migrations)
- stocks: unique(branch_id, item_id)
- purchases: purchase_number unique
- stock_takes: stock_take_number unique
- departments: name unique
- FKs across items/stocks/movements/requests/dispatches to branches, departments, employees

Recommended additional indexes
- stock_movements: index(stock_id), index(type), index(movement_date)
- item_requests: index(branch_id), index(department_id), index(status)
- item_request_details: index(request_id), index(item_id)
- item_dispatches: index(request_id), index(item_id)

Transactions/locking
- Use transactions and row-level locks on stocks when adjusting quantities and calculating before/after

Acceptance criteria
- Correctness under concurrent approvals/dispatch


feature: Test Strategy and Seed Data
prompt
Goal
- Provide repeatable tests and realistic seed data to validate inventory workflows end-to-end.

Outcomes
- High confidence in request/approval/dispatch/purchase flows and realtime updates

Related file structure
- tests/Feature/* (per feature)
- tests/Unit/* (services and policies)
- database/seeders/InventorySeeder.php (baseline items/stocks)
- database/seeders/InventoryPermissionSeeder.php (abilities)

Test cases overview
- Shift: open/close, overlap prevention, gating actions
- Purchase: create, average cost calc, stock/movement creation
- Request: approval limits, reservations updated, events emitted
- Dispatch: decrement stock, movement recorded, receipt confirm
- Reports: stock movement filters, low stock, expiry warnings
- Realtime: broadcasting payloads and channel auth

Acceptance criteria
- Green test suite; coverage on critical paths
