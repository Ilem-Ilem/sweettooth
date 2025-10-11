# Real-Time Inventory, Usage Monitoring, Requests/Dispatch, and Clock-In Implementation Plan

This document is the blueprint for implementing a real-time system that:
- Shows how items move through inventory (live stream of movements)
- Monitors stock levels and item usage patterns
- Tracks how employees request and use stock (full approval/dispatch pipeline)
- Adds robust clock-in/out functionality that gates actions by active shift

It aligns with and extends the scope defined in:
- todo.md (Phase 2 Inventory Module and later phases)
- inventory.md (stock workflows)
- products.md (finished goods/products and later modules)
- sales.md (sales/shift workflows and integration points)
- flow.md (overall structure/flow)
- main.md (migrations/architecture notes)

---

## 0) Executive Summary

Deliver a role-aware, branch-scoped, event-driven inventory module with:
- Real-time inventory dashboard and movement timeline
- Request → Approval → Dispatch pipeline with live updates
- Usage pattern analytics by item, department, employee, and time window
- Clock-in/out with shift binding to control who can request/dispatch
- Strong audit trail: every change recorded with before/after, actor, time

Live updates use either WebSockets (recommended) or graceful fallbacks using Livewire polling.

---

## 1) System Goals and Success Criteria

Goals:
- End-to-end real-time visibility of inventory stock and movements
- Immediate feedback when requests are made/approved/dispatched
- Reliable usage pattern insights (by employee/department/shift)
- Enforce that only clocked-in employees can request/dispatch/adjust

Success Criteria:
- Stock dashboard auto-updates within < 2s of movement creation
- Request/Dispatch board reflects state changes live (without manual refresh)
- Movement timeline shows complete audit trail with quantities before/after
- Usage patterns charts filterable by item, department, employee, shift, and time
- All request/dispatch/adjust actions blocked for non-clocked-in staff

---

## 2) Current State (from codebase)

Migrations exist for the core inventory data model (todo.md):
- items, purchases, purchase_items, stocks, stock_movements,
- item_requests, item_request_details, item_dispatches,
- stock_takes, stock_take_details, health_checks

Models exist in app/Models for these entities.

Livewire modules scaffold exists (resources/views/livewire + app/Livewire), with open slots for:
- Inventory/Stock, Inventory/Requests, Inventory/Dispatches, Inventory/Reports

Root documentation outlines workflows/goals for Inventory, Production, Sales modules.

---

## 3) Architecture Overview

3.1 Realtime layer (two options)
- Preferred: Laravel WebSockets + Echo or Pusher for low-latency updates.
  - Broadcast domain events (StockLevelsUpdated, ItemMovementRecorded, ItemRequestUpdated, ItemDispatchUpdated, EmployeeShiftUpdated)
  - Subscribe in Livewire components and update state via events
- Fallback: Livewire wire:poll with short intervals (e.g., 2–5s) on list components

3.2 Event-driven domain
- On creation/update of key entities, dispatch and broadcast domain events
- Consumers update UI state or compute aggregates, keeping Stocks as source-of-truth

3.3 Data flow
- Purchases create StockMovements (type: in)
- Requests → Approvals produce reservations (quantity_reserved)
- Dispatches consume reservations and create StockMovements (type: out)
- Adjustments and Stock Takes create StockMovements (type: adjustment) and reconcile Stocks
- Health checks annotate Stocks (health_status) and may create movements for quarantined/damaged

3.4 Security & scoping
- Branch-level isolation; Department-level access; Role-based permissions via Spatie
- Private channels per branch/department/employee for broadcasting

---

## 4) Database Additions and Indexing

4.1 New Tables (to integrate clock-in with Inventory ahead of Production module)
- employee_shifts
  - id (bigint), employee_id (uuid), branch_id (uuid), department_id (bigint)
  - shift_name (string: Morning/Afternoon/Night/Custom)
  - clock_in_at (datetime), clock_out_at (nullable datetime)
  - status (enum: open, closed)
  - opened_by (uuid Employee/User), closed_by (uuid Employee/User nullable)
  - opening_notes (text nullable), closing_notes (text nullable)
  - created_at, updated_at

4.2 Columns to Add
- item_requests.shift_id (nullable, FK -> employee_shifts.id)
- item_dispatches.shift_id (nullable, FK -> employee_shifts.id)
- stock_movements.shift_id (nullable, FK -> employee_shifts.id)

4.3 Recommended Indexes
- stock_movements: (stock_id), (movement_date), (type), (shift_id)
- item_requests: (branch_id), (department_id), (status), (shift_id)
- item_request_details: (request_id), (item_id)
- item_dispatches: (request_id), (item_id), (shift_id)
- stocks: (branch_id, item_id), (health_status)
- employee_shifts: (employee_id, branch_id, department_id, status)

---

## 5) Domain Events & Broadcasting

Define domain events and broadcast them to keep Livewire UI in sync.

Events (implements ShouldBroadcast):
- ItemMovementRecorded(stockMovementId, stockId, itemId, branchId)
- StockLevelsUpdated(stockId, itemId, branchId)
- ItemRequestUpdated(requestId, branchId, departmentId, status)
- ItemDispatchUpdated(dispatchId, requestId, branchId)
- EmployeeShiftUpdated(shiftId, employeeId, status)

Broadcast channels (examples):
- private.branch.{branchId}
- private.department.{departmentId}
- private.employee.{employeeId}
- presence.inventory.{branchId} (optional for dashboards)

Authorization callbacks align with Spatie roles/permissions and BranchMiddleware.

Fallback: If websockets not configured, Livewire components use wire:poll to refresh state.

---

## 6) Livewire Components and Pages

Aligning with todo.md 2.4 and extending for real-time + shifts.

6.1 Purchase Management
- app/Livewire/Inventory/Purchases/Index.php → resources/views/livewire/inventory/purchases/index.blade.php
- app/Livewire/Inventory/Purchases/Create.php → resources/views/livewire/inventory/purchases/create.blade.php
- app/Livewire/Inventory/Purchases/View.php → resources/views/livewire/inventory/purchases/view.blade.php

6.2 Stock Management
- app/Livewire/Inventory/Stock/Index.php → resources/views/livewire/inventory/stock/index.blade.php
- app/Livewire/Inventory/Stock/StockTake.php → resources/views/livewire/inventory/stock/stock-take.blade.php
- app/Livewire/Inventory/Stock/HealthCheck.php → resources/views/livewire/inventory/stock/health-check.blade.php
- NEW: app/Livewire/Inventory/Stock/RealtimeDashboard.php → resources/views/livewire/inventory/stock/realtime-dashboard.blade.php
  - Live KPIs (available, reserved, damaged, low-stock, expiring)
  - Live movement feed per selected item/branch

6.3 Requests & Dispatch
- app/Livewire/Inventory/Requests/Pending.php → resources/views/livewire/inventory/requests/pending.blade.php
- app/Livewire/Inventory/Requests/Process.php → resources/views/livewire/inventory/requests/process.blade.php
- app/Livewire/Inventory/Requests/History.php → resources/views/livewire/inventory/requests/history.blade.php
- app/Livewire/Inventory/Dispatches/Index.php → resources/views/livewire/inventory/dispatches/index.blade.php
- NEW: app/Livewire/Inventory/Dispatches/Board.php → resources/views/livewire/inventory/dispatches/board.blade.php
  - Kanban-like lanes: Pending → Approved → Dispatched → Received

6.4 Reports & Monitoring
- app/Livewire/Inventory/Reports/StockMovement.php → resources/views/livewire/inventory/reports/stock-movement.blade.php
- app/Livewire/Inventory/Reports/LowStock.php → resources/views/livewire/inventory/reports/low-stock.blade.php
- app/Livewire/Inventory/Reports/ExpiryWarnings.php → resources/views/livewire/inventory/reports/expiry-warnings.blade.php
- NEW: app/Livewire/Inventory/Reports/UsagePatterns.php → resources/views/livewire/inventory/reports/usage-patterns.blade.php
  - Group by item, department, employee, shift; time-range filters; charts
- NEW: app/Livewire/Inventory/Reports/EmployeeActivity.php → resources/views/livewire/inventory/reports/employee-activity.blade.php
  - Stream of who requested/approved/dispatch/adjusted what and when

6.5 Clock-In/Shift Management
- NEW: app/Livewire/Shift/Clock.php → resources/views/livewire/shift/clock.blade.php
  - Clock-in/out; show active shift; attach shift to actions
- NEW: app/Livewire/Shift/MyShifts.php → resources/views/livewire/shift/my-shifts.blade.php
  - History; durations; export
- NEW: app/Livewire/Shift/Manage.php → resources/views/livewire/shift/manage.blade.php
  - For managers: view open shifts by branch/department; force close

---

## 7) Routes and Navigation

- Mount components under branch-aware routes respecting BranchMiddleware
- Example route prefixes:
  - /inventory/purchases, /inventory/stock, /inventory/requests, /inventory/dispatches, /inventory/reports
  - /shift/clock, /shift/manage
- Add sidebar entries under Inventory and Shift sections in your app layout

---

## 8) Permissions Matrix (Spatie)

Inventory roles (as per seeders):
- Inventory Manager: approve requests, manage stock takes/health checks, view reports
- Store Keeper: dispatch items, record receives, manage adjustments
- Department Staff (Production/Sales): create item requests, receive dispatches
- Admin roles: full access

Shift roles:
- All staff: can clock in/out for their department
- Managers: can view/manage shifts of their department; force close

Route middlewares and Livewire components should check:
- Employee guard (for staff) or Web guard (admins)
- Branch and department scope
- Active shift requirement before actions (request/dispatch/adjust)

---

## 9) Detailed Implementation Steps

Step 0 – Preflight
- Ensure all existing inventory migrations are present and migrated
- Seeders executed (roles/permissions, inventory items, departments)

Step 1 – Shifts (Clock-In)
- Create migration for employee_shifts
- Create model EmployeeShift, relationships: employee, branch, department
- Policies: allow own shift management; managers can manage within branch/department
- Add repository/service to open/close shift and validate no overlapping opens
- Add FK columns shift_id to item_requests, item_dispatches, stock_movements (nullable)
- Add gate in services: require active shift for request/dispatch/adjust actions

Step 2 – Domain Events & Broadcasting
- Create events: EmployeeShiftUpdated, ItemRequestUpdated, ItemDispatchUpdated, ItemMovementRecorded, StockLevelsUpdated
- Broadcast on private channels by branch/department
- Wire listeners to update Livewire component state (or wire:poll fallback)

Step 3 – Request → Approval → Dispatch workflow
- Implement Requests/Pending and Requests/Process
  - Pending lists new requests; Process handles approve/reject and qty approvals
- When approved, reserve stock (increment quantity_reserved) and emit ItemRequestUpdated + StockLevelsUpdated
- Dispatch: fulfill approved qty; decrement quantity_reserved and quantity_available; create StockMovement(type: out); emit ItemDispatchUpdated + ItemMovementRecorded + StockLevelsUpdated
- Receiving department confirms; update dispatch record

Step 4 – Purchase ingestion
- Create Purchase Create/View; on storing purchase, upsert Stocks and create StockMovement(type: in)
- Recompute stock average_cost from purchase_items; emit events

Step 5 – Stock dashboard and movement timeline
- Stock/Index: searchable, filterable (branch, category, health, low/over/critical)
- RealtimeDashboard: KPIs and live movement feed (subscribe to ItemMovementRecorded)
- Reports/StockMovement: timeline with before/after, actor, type, shift, notes

Step 6 – Health checks and stock takes
- Implement HealthCheck and StockTake components per todo.md
- Stock take reconciliation creates adjustment movements

Step 7 – Usage patterns and employee activity
- Reports/UsagePatterns: aggregate stock_movements by filters (time, item, department, employee, type)
- Reports/EmployeeActivity: stream of who did what (requests/approvals/dispatch/adjustments) with shift context

Step 8 – Clock-in UI
- Shift/Clock: show current shift, clock-in/out, capture shift_name, notes
- Shift/MyShifts: history and metrics (total hours, per department)
- Shift/Manage: list open shifts in branch; allow manager force-close

Step 9 – Tests
- Feature tests: request approval, dispatch, stock updates, broadcasting payloads
- Policy tests: permissions by role/department/branch
- Shift tests: cannot act without open shift; overlapping shifts prevention

Step 10 – Deployment/Perf
- Configure Laravel WebSockets if opting for realtime
- Add DB indexes listed above
- Queue broadcasting with Redis/queue workers

---

## 10) Data Contracts and Movement Types

StockMovement.type values:
- in (Purchase/Receive)
- out (Dispatch/Consumption)
- adjustment (Manual/StockTake variance)
- damaged (Quarantine/Write-off)
- transfer (Inter-branch; if implemented later)
- return (Returned from department/sales)

Every StockMovement must include:
- stock_id, quantity, quantity_before, quantity_after
- reference_type, reference_id (Purchase, Request, Dispatch, StockTake, HealthCheck, Adjustment)
- moved_by (employee_id/user_id), movement_date, notes, shift_id (nullable)

---

## 11) Page Inventory (Blade Views)

resources/views/livewire/inventory/
- purchases/
  - index.blade.php, create.blade.php, view.blade.php
- stock/
  - index.blade.php, stock-take.blade.php, health-check.blade.php, realtime-dashboard.blade.php
- requests/
  - pending.blade.php, process.blade.php, history.blade.php
- dispatches/
  - index.blade.php, board.blade.php
- reports/
  - stock-movement.blade.php, low-stock.blade.php, expiry-warnings.blade.php, usage-patterns.blade.php, employee-activity.blade.php

resources/views/livewire/shift/
- clock.blade.php, my-shifts.blade.php, manage.blade.php

---

## 12) Navigation and UX Notes

- Sidebar: Inventory (Purchases, Stock, Requests, Dispatches, Reports) and Shift (Clock In/Out)
- Badge colors and status indicators for low stock, expiring, pending approvals
- Slide-over or modal for Item History timeline with filters (type, date range)
- Kanban board for request/dispatch states
- Charts for usage patterns (could start with simple tables, add charts later)

---

## 13) Permissions and Guards

- Employee guard for branch/department staff components
- Web guard for super-admin and managers (as configured)
- Spatie roles: ensure Inventory Manager, Store Keeper, Department Staff are seeded (as in seeders)
- Middleware: BranchMiddleware + IsAdmin appropriately; ensure branch-route.php wires routes

---

## 14) Analytics and Alerts

- Low stock alerts: threshold at reorder_level and critical at 25% of reorder_level
- Expiry warnings: within X days (configurable) from expiry_date
- Optional email/notification channels for managers

---

## 15) Risks and Mitigations

- Real-time complexity → Provide wire:poll fallback; feature flag websockets
- Concurrency on stock updates → Use DB transactions and row-level locking when computing before/after
- Data correctness → All mutations create StockMovement records
- Permissions leakage → Strict channel authorization and branch/department scoping

---

## 16) Definition of Done

- All components/pages listed exist with basic UX implemented
- Websocket or polling updates visible on dashboard and request/dispatch boards
- All CRUD flows covered for purchases, requests, dispatches
- Movement timeline shows accurate before/after and actors
- Clock-in gating enforced for requests/dispatches
- Tests cover happy paths and key edge cases

---

## 17) Developer Prompts (copy/paste tasks)

Prompt A – Create Shifts (Clock-in) Backbone
- Create employee_shifts migration and model with relationships to Employee, Branch, Department
- Add shift_id columns to item_requests, item_dispatches, stock_movements (nullable FKs)
- Implement ShiftService: openShift(employee, shift_name, notes), closeShift(employee, notes)
- Enforce active shift requirement in Request/Dispatch/Adjustment actions; throw domain exception otherwise
- Emit/broadcast EmployeeShiftUpdated on open/close

Prompt B – Implement Request → Approval → Dispatch with reservations
- Build Livewire components: Requests/Pending, Requests/Process, Dispatches/Index, Dispatches/Board
- On approve: set quantity_approved; update stocks.quantity_reserved; emit ItemRequestUpdated + StockLevelsUpdated
- On dispatch: decrement quantity_available, quantity_reserved; create StockMovement(type: out) with before/after; emit ItemDispatchUpdated + ItemMovementRecorded + StockLevelsUpdated
- Add policies for roles/permissions

Prompt C – Real-time Stock Dashboard and Movement Timeline
- Build Stock/Index and Stock/RealtimeDashboard with filters (branch, category, health, stock levels)
- Subscribe to ItemMovementRecorded events (or wire:poll); update KPIs and tables
- Build Reports/StockMovement: timeline showing type badge, qty, before/after, by, date, notes, shift

Prompt D – Purchases ingestion and average cost
- Create Purchases/Create & View forms; on save, create purchase_items and upsert stock
- Compute average_cost per item; create StockMovement(type: in) with before/after
- Emit ItemMovementRecorded + StockLevelsUpdated

Prompt E – Usage Patterns and Employee Activity
- Build Reports/UsagePatterns: group stock_movements by filters and render charts/tables
- Build Reports/EmployeeActivity: list of actions by employee with shift context

Prompt F – Health Checks and Stock Takes
- Implement health-check and stock-take flows as in todo.md, producing movements on variance/adjustments

---

## 18) Integration with Future Phases

- Production module will build on employee_shifts and item_requests
- Sales module will reuse shifts and dispatch/receive flows (for finished products)
- Analytics in Phase 6 will leverage movement and shift data for KPIs

---

## 19) File/Task Checklist

- [ ] Migration: employee_shifts
- [ ] Add shift_id FKs to item_requests, item_dispatches, stock_movements
- [ ] Model + Policy: EmployeeShift
- [ ] Services: ShiftService, InventoryService (reserve, dispatch, adjust)
- [ ] Events: EmployeeShiftUpdated, ItemRequestUpdated, ItemDispatchUpdated, ItemMovementRecorded, StockLevelsUpdated
- [ ] Channels: private.branch.{branchId}, private.department.{departmentId}
- [ ] Livewire: Stock (Index, RealtimeDashboard); Purchases (Index/Create/View)
- [ ] Livewire: Requests (Pending/Process/History); Dispatches (Index/Board)
- [ ] Livewire: Reports (StockMovement, LowStock, ExpiryWarnings, UsagePatterns, EmployeeActivity)
- [ ] Livewire: Shift (Clock, MyShifts, Manage)
- [ ] Views: Create all blade files listed
- [ ] Routing + sidebar nav entries
- [ ] Tests: features, policies, services
- [ ] Perf: DB indexes, queue/broadcast config

---

This plan is designed to be executed incrementally. Start with shifts and core request/dispatch flows, then layer on real-time broadcasting and reports. It aligns with todo.md Phase 2 and prepares the foundation for Phase 3 (Production) and Phase 4 (Sales).