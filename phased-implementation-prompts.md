# SweetTooth RMS: Phased Implementation Prompts (Inventory, Production, Sales)

This document consolidates implementation guidance from the following specs and code:
- inventory.md (inventory schemas/workflows)
- products.md (production schemas/workflows)
- sales.md (sales schemas/workflows)
- products workflow.md (product types/products todo)
- 2025_10_05_create_production_tables.md (alternate production schemas)
- moels.md (Eloquent model relationships and fields)
- main.md (UUID/ID strategy)
- flow.md (application structure and Livewire components)

It is split into phases. Each phase lists features with detailed prompts including:
- Goals/outcomes
- Related file structure (paths to use/create)
- Exact database schemas (from migrations/specs)
- Pages/components/routes and permissions
- Validation/workflows/events
- Tests and acceptance criteria

---

PHASE 1: Inventory Core and Realtime Visibility

feature: Inventory Items & Purchases
prompt
Goal
- Manage items and purchases; update stocks and average cost; create stock movements (type: in).

Related file structure
- app/Livewire/Inventory/Purchases/Index.php → resources/views/livewire/inventory/purchases/index.blade.php
- app/Livewire/Inventory/Purchases/Create.php → resources/views/livewire/inventory/purchases/create.blade.php
- app/Livewire/Inventory/Purchases/View.php → resources/views/livewire/inventory/purchases/view.blade.php
- Models: app/Models/Item.php, Purchase.php, PurchaseItem.php, Stock.php, StockMovement.php
- Routes: routes/branch-route.php → /inventory/purchases (index, create, view)
- Policies: PurchasePolicy (create/view)
- Services: InventoryService (upsertStock, recalcAverageCost)

Database schemas (from database/migrations)
- items (2025_10_09_012549_create_items_table.php)
  - id bigint PK; branch_id uuid FK branches; name; sku unique; category enum(raw_material, packaging, consumable, equipment)
  - uom enum(grams, kg, liters, ml, pcs, units, bags, cartons); description nullable; reorder_level dec(10,2) nullable; max_stock_level dec(10,2) nullable; status enum(active,inactive); timestamps
- purchases (2025_10_09_012613_create_purchases_table.php)
  - id bigint PK; branch_id uuid FK branches; recorded_by uuid FK employees; purchase_number unique; purchase_date date; supplier_name; supplier_contact nullable
  - total_fob_fc dec(12,2) default 0; total_fob_ngn dec(12,2) default 0; other_costs dec(12,2) default 0; landing_cost dec(12,2) default 0; total_cost dec(12,2) default 0
  - currency char(3) default 'NGN'; exchange_rate dec(10,4) default 1; payment_status enum(paid, partial, pending) default pending; notes text nullable; timestamps
- purchase_items (2025_10_09_012614_create_purchase_items_table.php)
  - id bigint PK; purchase_id bigint FK purchases(cascade); item_id bigint FK items(restrict)
  - quantity dec(12,2); uom enum(...); fob_fc dec(12,2) default 0; fob_ngn dec(12,2) default 0; other_costs dec(12,2) default 0; landing_cost dec(12,2) default 0; total_cost dec(12,2); cost_per_unit dec(12,4); timestamps
- stocks (2025_10_09_012615_create_stocks_table.php)
  - id bigint PK; branch_id uuid FK branches; item_id bigint FK items; quantity_available dec(12,2) default 0; quantity_reserved dec(12,2) default 0; quantity_damaged dec(12,2) default 0; average_cost dec(12,4) default 0; last_stock_take_date date nullable; health_status enum(good, warning, critical, expired) default good; expiry_date date nullable; timestamps; unique(branch_id,item_id)
- stock_movements (2025_10_09_012617_create_stock_movements_table.php)
  - id; stock_id FK stocks; type enum(in,out,adjustment,transfer,damaged,return); quantity; quantity_before; quantity_after; reference_type string nullable; reference_id bigint nullable; moved_by uuid FK employees nullable; notes text; movement_date timestamp; timestamps

Pages and structure
- Purchases Index: filter by branch/date/supplier/status; columns: number, date, supplier, totals, currency, recorded_by
- Create: header (supplier, currency, dates), dynamic line items; save → upsert stock, average cost, stock movements
- View: header + line items + movement links

Validation
- Header: purchase_date date; supplier_name required; currency in(NGN,FC); exchange_rate numeric when FC
- Items[]: item_id exists; quantity > 0; uom in enum; costs ≥ 0

Events
- Emit ItemMovementRecorded(type: in) and StockLevelsUpdated

Tests
- Purchase creates/updates stocks and movements; average cost formula correct

Acceptance criteria
- Stocks reflect purchased quantities; movement trail with before/after; realtime updates visible on dashboards


feature: Inventory Stock Dashboard & Movement Feed
prompt
Goal
- Provide stock overview, KPIs, and real-time movement feed per branch.

Related file structure
- app/Livewire/Inventory/Stock/Index.php → resources/views/livewire/inventory/stock/index.blade.php
- app/Livewire/Inventory/Stock/RealtimeDashboard.php → resources/views/livewire/inventory/stock/realtime-dashboard.blade.php
- Models: Stock, Item, StockMovement
- Routes: /inventory/stock, /inventory/stock/realtime (branch scoped)

Database schemas
- stocks, items, stock_movements (see above)

UI structure
- Stock Index: filters (branch, category, stock state, health, date range, search) → table (Item, SKU, Branch, Category, Available, Reserved, Damaged, Total, Reorder, Health, Last Stock Take, Actions[History])
- Realtime: KPI cards (low, expiring, damaged, total items) + live movement feed subscribed to ItemMovementRecorded; fallback wire:poll

Acceptance criteria
- Filters paginate; live feed updates without manual refresh; badges color-coded


feature: Item Requests (Create/Pending/Process)
prompt
Goal
- Departments request items; inventory manager approves quantities; reserved quantities updated.

Related file structure
- app/Livewire/Inventory/Requests/Pending.php → resources/views/livewire/inventory/requests/pending.blade.php
- app/Livewire/Inventory/Requests/Process.php → resources/views/livewire/inventory/requests/process.blade.php
- app/Livewire/Inventory/Requests/History.php → resources/views/livewire/inventory/requests/history.blade.php
- Models: ItemRequest, ItemRequestDetail, Stock
- Services: InventoryService (reserveStock)
- Routes: /inventory/requests (pending, process/{id}, history)

Database schemas
- item_requests (2025_10_09_012619_create_item_requests_table.php)
  - id; branch_id uuid FK branches; department_id bigint FK departments; requested_by uuid FK employees; request_number unique; request_date; shift enum(morning,afternoon) nullable; status enum(pending,approved,partially_dispatched,completed,cancelled) default pending; approved_by uuid FK employees nullable set null; approved_at timestamp nullable; notes; timestamps
- item_request_details (2025_10_09_012621_create_item_request_details_table.php)
  - id; request_id FK item_requests; item_id FK items; quantity_requested; quantity_approved default 0; quantity_dispatched default 0; uom enum(...); notes; timestamps
- stocks (quantities available/reserved)

Workflow
- Create: status=pending
- Approve: per-line approve quantities limited by (available − reserved); increment stocks.quantity_reserved; status updated
- Reject: status=cancelled

Events
- ItemRequestUpdated, StockLevelsUpdated

Tests
- Cannot approve beyond limit; reservations updated; events fired

Acceptance criteria
- Reservations reflect approvals; realtime changes appear in dashboards


feature: Dispatch Management
prompt
Goal
- Fulfill approved requests; decrement reserved and available; create movement (out); receiving confirmation.

Related file structure
- app/Livewire/Inventory/Dispatches/Board.php → resources/views/livewire/inventory/dispatches/board.blade.php
- app/Livewire/Inventory/Dispatches/Index.php → resources/views/livewire/inventory/dispatches/index.blade.php
- Models: ItemDispatch, ItemRequest, ItemRequestDetail, Stock, StockMovement
- Routes: /inventory/dispatches (board, index)

Database schemas
- item_dispatches (2025_10_09_012622_create_item_dispatches_table.php)
  - id; request_id FK item_requests; item_id FK items; dispatched_by uuid FK employees; received_by uuid FK employees; quantity dec(12,2); uom enum(...); dispatch_time; received_time nullable; shift enum(morning,afternoon) nullable; notes; timestamps
- item_request_details: quantity_approved, quantity_dispatched
- stocks, stock_movements

Workflow
- Dispatch: decrement stocks.quantity_reserved and stocks.quantity_available; create StockMovement(type: out, before/after); update request detail quantity_dispatched
- Receive: set received_by/time

Events
- ItemDispatchUpdated, ItemMovementRecorded, StockLevelsUpdated

Tests
- Cannot over-dispatch; movements correct; receipts recorded

Acceptance criteria
- Accurate stock decrements; traceability to requests; realtime updates


feature: Stock Take & Health Checks
prompt
Goal
- Conduct stock takes and health checks; reconcile variances; record damaged/expired adjustments.

Related file structure
- app/Livewire/Inventory/Stock/StockTake.php → resources/views/livewire/inventory/stock/stock-take.blade.php
- app/Livewire/Inventory/Stock/HealthCheck.php → resources/views/livewire/inventory/stock/health-check.blade.php
- Models: StockTake, StockTakeDetail, HealthCheck, Stock, StockMovement
- Routes: /inventory/stock/stock-take, /inventory/stock/health-check

Database schemas
- stock_takes (2025_10_09_012623_create_stock_takes_table.php)
  - id; branch_id uuid FK branches; stock_take_number unique; stock_take_date; type enum(daily,weekly,monthly,annual,ad_hoc); conducted_by uuid FK employees; status enum(in_progress,completed,verified) default in_progress; verified_by uuid FK employees nullable set null; verified_at nullable; notes; timestamps
- stock_take_details (2025_10_09_012626_create_stock_take_details_table.php)
  - id; stock_take_id FK stock_takes; item_id FK items; system_quantity; physical_quantity; variance; variance_type enum(surplus,shortage,match) default match; notes; timestamps
- health_checks (2025_10_09_012627_create_health_checks_table.php)
  - id; stock_id FK stocks; checked_by uuid FK employees; check_date; condition enum(excellent,good,fair,poor,damaged,expired); quantity_affected dec(12,2) nullable; observations text; action_taken text; timestamps

Workflow
- Finalize stock take: create StockMovement(type: adjustment) per variance; update Stocks and last_stock_take_date
- Health check write-off: StockMovement(type: damaged) and adjust quantities

Acceptance criteria
- Movements with before/after created; statuses updated accordingly


feature: Realtime Broadcasting Layer
prompt
Goal
- Event-driven updates for inventory features with WebSockets (preferred) and Livewire polling fallback.

Related file structure
- app/Events/Inventory/{ItemMovementRecorded,StockLevelsUpdated,ItemRequestUpdated,ItemDispatchUpdated}.php
- resources/js/app.js (Echo init), resources/views/components/layouts/app.blade.php (load Echo, pass branch/department context)
- routes/channels.php (private.branch.{branchId}, private.department.{departmentId}, private.employee.{employeeId})
- Middleware: BranchMiddleware; Guards and Spatie permissions

Database schemas used
- stocks, stock_movements, item_requests, item_dispatches, employees, branches, departments (see above for fields)

Listeners and fallback
- Dashboards/boards subscribe to events; fallback to wire:poll.keep-alive.2s

Acceptance criteria
- UI reflects changes within <2s; unauthorized subscriptions denied

---

PHASE 2: Production Module (Recipes, Shifts, Produce, Quality)

feature: Recipes and Ingredients
prompt
Goal
- Define recipes, steps, and ingredients for production; compute costs using inventory average_cost.

Related file structure
- app/Livewire/Production/Kitchen/Recipes/Index.php, Create.php, Calculator.php → resources/views/livewire/production/kitchen/recipes/{index,create,calculator}.blade.php
- Models: Recipe, RecipeIngredient (see moels.md)
- Routes: /production/recipes (index, create), /production/recipes/calculator

Database schemas (from products.md and 2025_10_05_create_production_tables.md)
- recipes (products.md)
  - id; branch_id uuid FK branches; department_id bigint FK departments; product_name; sku unique; category_id bigint nullable; product_type enum(gelato_base, gelato_flavor, pastry, hot_kitchen, beverage); cost_per_unit dec(10,4) default 0; uom enum(grams,kg,liters,ml,pcs,units) default pcs; yield_quantity dec(10,2) default 1; preparation_time int nullable; instructions text nullable; status enum(active,inactive,testing) default active; created_by uuid FK employees; timestamps
- recipe_ingredients (products.md)
  - id; recipe_id FK recipes; item_id FK items; quantity dec(12,4); uom enum(grams,kg,liters,ml,pcs,units); sort_order int default 0; notes text; timestamps
- recipe_steps (2025_10_05_create_production_tables.md) [optional if steps needed]
  - id; recipe_id FK recipes; step_number int; description text; estimated_time int nullable; notes text; timestamps; unique(recipe_id, step_number)

Workflow
- Create recipe → ingredients; cost calculation using Stock.average_cost × ingredient quantities

Acceptance criteria
- Accurate cost computation; uom validation


feature: Production Shifts and Daily Produce
prompt
Goal
- Track production shifts, daily production, expected vs actual, and variance.

Related file structure
- app/Livewire/Production/Kitchen/ShiftLogin.php → resources/views/livewire/production/kitchen/shift-login.blade.php
- app/Livewire/Production/Kitchen/Production/DailyProduce.php → resources/views/livewire/production/kitchen/production/daily-produce.blade.php
- Models: Shift, DailyProduce, ProductionRecord (see moels.md)
- Routes: /production/shifts, /production/daily-produce

Database schemas (from products.md)
- shifts
  - id; branch_id uuid FK branches; department_id bigint FK departments; employee_id uuid FK employees; shift_number unique; shift_date date; shift_type enum(morning,afternoon,night); clock_in/out timestamps nullable; status enum(active,closed,submitted) default active; notes text nullable; timestamps; index(branch_id,department_id,shift_date)
- daily_produces
  - id; shift_id FK shifts; recipe_id FK recipes; produce_date date; shift_type enum(morning,afternoon); opening_quantity dec(12,2) default 0; requested_quantity dec(12,2) default 0; produced_quantity dec(12,2) default 0; sent_out_quantity dec(12,2) default 0; order_quantity dec(12,2) default 0; callback_quantity dec(12,2) default 0; closing_quantity dec(12,2) default 0; expected_closing dec(12,2) default 0; variance dec(12,2) default 0; notes text nullable; timestamps; unique(shift_id, recipe_id)
- production_records
  - id; daily_produce_id FK daily_produces; recipe_id FK recipes; produced_by uuid FK employees; quantity_produced; quantity_approved default 0; quantity_rejected default 0; production_time timestamp; quality_status enum(excellent,good,acceptable,rejected) default good; rejection_reason text nullable; notes text; timestamps

Formula
- Expected Closing = Opening + Produced − SentOut − Callback; Variance = Closing − ExpectedClosing

Acceptance criteria
- Variance computed; records consistent with daily_produces


feature: Production Requests and Raw Material Utilization
prompt
Goal
- Link item requests to production plans; measure raw material utilization efficiency.

Related file structure
- app/Livewire/Production/Kitchen/Production/RequestItems.php → resources/views/livewire/production/kitchen/production/request-items.blade.php
- app/Livewire/Production/Kitchen/Reports/ShiftReport.php → resources/views/livewire/production/kitchen/reports/shift-report.blade.php
- Models: ProductionRequest, RawMaterialUtilization
- Routes: /production/requests, /production/reports/shift

Database schemas (from products.md and 2025_10_05_create_production_tables.md)
- production_requests (products.md)
  - id; shift_id FK shifts; item_request_id FK item_requests; recipe_id FK recipes nullable; planned_production_quantity dec(12,2) nullable; notes text; timestamps
- raw_material_utilizations (products.md)
  - id; shift_id FK shifts; recipe_id FK recipes; item_id FK items; quantity_required dec(12,4); quantity_used dec(12,4); units_produced dec(12,2); variance dec(12,4) default 0; variance_type enum(within_tolerance, over_used, under_used) default within_tolerance; cost_impact dec(10,2) default 0; notes text; timestamps; index(shift_id, recipe_id)

Acceptance criteria
- Utilization variance captured and reported per shift/recipe


feature: Callbacks (Rejects/Wastage)
prompt
Goal
- Track rejected/bad items during production and actions taken.

Related file structure
- app/Livewire/Production/Kitchen/Production/Callbacks.php → resources/views/livewire/production/kitchen/production/callbacks.blade.php
- Model: CallBack
- Route: /production/callbacks

Database schema (products.md)
- call_backs
  - id; shift_id FK shifts; callback_type string; reference_id bigint; quantity dec(12,2); uom enum(...); reason enum(expired,damaged,quality_issue,contaminated,other); description text; reported_by uuid FK employees; callback_time timestamp; action_taken enum(disposed,returned_to_supplier,reprocessed,pending) default pending; timestamps

Acceptance criteria
- Callback entries complete and reportable; optional movement adjustments if affects inventory

---

PHASE 3: Sales Module (POS, Tables, Payments, Product Stocks)

feature: Product Catalog (Categories/Products)
prompt
Goal
- Manage products and categories for sales; support links to recipes and departments.

Related file structure
- app/Livewire/Sales/Catalog/Products/Index.php, Create.php, Edit.php → resources/views/livewire/sales/catalog/products/{index,create,edit}.blade.php
- app/Livewire/Sales/Catalog/Categories/Index.php → resources/views/livewire/sales/catalog/categories/index.blade.php
- Models: ProductCategory, Product (see moels.md)
- Routes: /sales/catalog/products, /sales/catalog/categories

Database schemas (from sales.md)
- product_categories
  - id; name; slug unique; description nullable; type enum(food, beverage, dessert, gelato, pastry, other); timestamps
- products
  - id; branch_id uuid FK branches; department_id bigint FK departments; category_id bigint nullable FK product_categories; recipe_id bigint nullable FK recipes; name; sku unique; description nullable; unit_price dec(10,2); source enum(kitchen, gelato, pastry, hot_kitchen, in_house); available_for_glovo bool default false; available_for_transfer bool default true; status enum(active,inactive,out_of_stock) default active; image nullable; timestamps

Acceptance criteria
- Products and categories CRUD operational; relationships valid


feature: Sales Shifts and POS Sales
prompt
Goal
- Manage sales shifts, record sales and line items with payment tracking.

Related file structure
- app/Livewire/Sales/Till/ShiftLogin.php → resources/views/livewire/sales/till/shift-login.blade.php
- app/Livewire/Sales/Till/Sales.php → resources/views/livewire/sales/till/sales.blade.php
- app/Livewire/Sales/Till/Reports.php → resources/views/livewire/sales/till/reports.blade.php
- Models: SalesShift, Sale, SaleItem, Payment
- Routes: /sales/till/shift, /sales/till/new, /sales/till/reports

Database schemas (from sales.md)
- sales_shifts
  - id; branch_id uuid FK branches; department_id bigint FK departments; employee_id uuid FK employees; shift_number unique; shift_date date; shift_type enum(morning,afternoon,night); clock_in/out timestamps nullable; opening_cash dec(10,2) default 0; closing_cash dec(10,2) default 0; expected_cash dec(10,2) default 0; cash_variance dec(10,2) default 0; status enum(active,closed,submitted,verified) default active; verified_by uuid FK employees nullable set null; notes text; timestamps
- sales
  - id; sales_shift_id bigint FK sales_shifts; branch_id uuid FK branches; department_id bigint FK departments; sold_by uuid FK employees; sale_number unique; table_id bigint nullable FK tables set null; table_number nullable; sale_time timestamp; subtotal dec(10,2) default 0; tax dec(10,2) default 0; discount dec(10,2) default 0; total dec(10,2) default 0; status enum(pending,completed,cancelled,refunded) default completed; order_type enum(dine_in,takeaway,glovo,transfer) default dine_in; notes text; timestamps; index(branch_id,department_id,sale_time)
- sale_items
  - id; sale_id FK sales; product_id FK products; quantity dec(10,2); unit_price dec(10,2); subtotal dec(10,2); discount dec(10,2) default 0; total dec(10,2); notes; timestamps
- payments
  - id; sale_id FK sales; payment_method enum(cash,pos,transfer,card,mobile) default cash; amount dec(10,2); reference_number nullable; payment_time timestamp; status enum(pending,completed,failed,refunded) default completed; notes; timestamps

Acceptance criteria
- Sales captured; payments recorded; shift cash variance computed


feature: Tables and Table Orders (Corner Store)
prompt
Goal
- Manage tables and orders with guest count, status, and clearing flow.

Related file structure
- app/Livewire/Sales/CornerStore/Tables/Manage.php, Monitor.php → resources/views/livewire/sales/corner-store/tables/{manage,monitor}.blade.php
- app/Livewire/Sales/CornerStore/Orders/TableOrders.php → resources/views/livewire/sales/corner-store/orders/table-orders.blade.php
- Models: Table, TableOrder, TableOrderItem
- Routes: /sales/corner-store/tables, /sales/corner-store/orders

Database schemas (from sales.md)
- tables
  - id; branch_id uuid FK branches; table_number unique; capacity int default 4; status enum(available,occupied,reserved,needs_cleaning) default available; location enum(indoor,outdoor,vip,regular) default regular; timestamps
- table_orders
  - id; table_id FK tables; sales_shift_id FK sales_shifts; served_by uuid FK employees; order_number unique; order_time timestamp; guest_count int default 1; total_amount dec(10,2) default 0; order_status enum(pending,in_progress,ready,served,completed) default pending; payment_status enum(unpaid,partially_paid,paid) default unpaid; is_cleared bool default false; cleared_at timestamp nullable; notes; timestamps
- table_order_items
  - id; table_order_id FK table_orders; product_id FK products; quantity dec(10,2); unit_price dec(10,2); total dec(10,2); status enum(pending,preparing,ready,served) default pending; special_instructions text nullable; timestamps

Acceptance criteria
- Orders flow through statuses; clearing marks table available


feature: Product Stocks per Sales Shift
prompt
Goal
- Track product stock per shift to drive POS availability and reports.

Related file structure
- app/Livewire/Sales/Reports/ShiftStock.php → resources/views/livewire/sales/reports/shift-stock.blade.php
- Model: ProductStock
- Route: /sales/reports/shift-stock

Database schema (from sales.md)
- product_stocks
  - id; sales_shift_id FK sales_shifts; product_id FK products; stock_date date; shift_type enum(morning,afternoon)
  - opening_quantity dec(12,2) default 0; addition_quantity dec(12,2) default 0; callback_quantity dec(12,2) default 0; redress_quantity dec(12,2) default 0; total_available dec(12,2) default 0; transfer_quantity dec(12,2) default 0; glovo_quantity dec(12,2) default 0; quantity_sold dec(12,2) default 0; closing_quantity dec(12,2) default 0; amount dec(12,2) default 0; notes text; timestamps; unique(sales_shift_id, product_id, stock_date, shift_type)

Acceptance criteria
- Closing = Opening + Addition − Transfer − Glovo − Sold + Redress − Callback


feature: Transfers and Kitchen Orders
prompt
Goal
- Transfer finished goods between departments; place kitchen orders from sales to production.

Related file structure
- app/Livewire/Sales/Transfers/Index.php → resources/views/livewire/sales/transfers/index.blade.php
- app/Livewire/Sales/KitchenOrders/Index.php → resources/views/livewire/sales/kitchen-orders/index.blade.php
- Models: Transfer, KitchenOrder
- Routes: /sales/transfers, /sales/kitchen-orders

Database schemas (from sales.md)
- transfers
  - id; branch_id uuid FK branches; from_department_id bigint FK departments; to_department_id bigint FK departments; product_id bigint FK products; transfer_number unique; transferred_by uuid FK employees; received_by uuid FK employees nullable set null; quantity dec(12,2); transfer_time timestamp; received_time timestamp nullable; status enum(pending,in_transit,received,rejected) default pending; rejection_reason text nullable; notes text; timestamps
- kitchen_orders
  - id; sales_shift_id FK sales_shifts; production_department_id bigint FK departments; product_id FK products; order_number unique; ordered_by uuid FK employees; quantity_ordered dec(12,2); quantity_received dec(12,2) default 0; order_time timestamp; expected_ready_time timestamp nullable; actual_ready_time timestamp nullable; received_time timestamp nullable; status enum(pending,in_progress,ready,completed,cancelled) default pending; priority enum(normal,high,urgent) default normal; notes text; timestamps

Acceptance criteria
- Transfers tracked end-to-end; kitchen orders tracked with ready/received lifecycle

---

PHASE 4: Cross-Cutting: Navigation, Permissions, Guards, and Realtime

feature: Navigation, Routing, and Permissions
prompt
Goal
- Wire branch-scoped routes, guards, and sidebar navigation per role.

Related file structure
- routes/{web.php, branch-route.php, sales.php, production.php, inventory.php} (per flow.md)
- app/Http/Middleware/{BranchMiddleware, CheckBranchAccess, CheckRole, CheckShiftActive}
- resources/views/components/layouts/app.blade.php (sidebar entries for Inventory, Production, Sales)
- database/seeders/{PermissionSeeder.php, RoleSeeder.php, InventoryPermissionSeeder.php}
- config/permission.php, database/migrations/2025_10_07_030703_create_permission_tables.php

Database schemas (spatie core)
- permissions, roles, model_has_permissions, model_has_roles, role_has_permissions (see migration for columns and keys)

Acceptance criteria
- Unauthorized users receive 403; menu hides inaccessible links; branch context enforced throughout


feature: Realtime Broadcasting Infrastructure
prompt
Goal
- Private channels and domain events for inventory/production/sales updates with Echo fallback to polling.

Related file structure
- app/Events/*, routes/channels.php, resources/js/app.js, config/broadcasting.php

Database schemas used
- stocks, stock_movements, item_requests, item_dispatches, shifts (production/sales), sales, table_orders

Acceptance criteria
- Live updates reflect within <2s; channel auth prevents cross-branch access

---

PHASE 5: Analytics and Reports

feature: Inventory Reports (Movement, Low Stock, Expiry, Usage Patterns)
prompt
Goal
- Provide historical/audit and alerting reports for inventory.

Related file structure
- app/Livewire/Inventory/Reports/{StockMovement, LowStock, ExpiryWarnings, UsagePatterns}.php → resources/views/livewire/inventory/reports/*.blade.php
- Routes: /inventory/reports/*

Database schemas
- stock_movements (movement timeline); stocks/items (low/expiry); item_dispatches/request_details (usage by dept/employee)

Acceptance criteria
- Correct filters; exports optional


feature: Production Reports
prompt
Goal
- Shift summaries and production analysis per department.

Related file structure
- app/Livewire/Production/*/Reports/{ShiftReport, ProductionAnalysis}.php

Database schemas
- shifts, daily_produces, production_records, raw_material_utilizations

Acceptance criteria
- Accurate KPIs (variance, utilization)


feature: Sales Reports
prompt
Goal
- Daily/shift sales summaries, product performance, revenue breakdown.

Related file structure
- app/Livewire/Sales/Reports/{DailySales, ProductAnalysis}.php

Database schemas
- sales, sale_items, payments, product_stocks

Acceptance criteria
- Filters by date/branch/department; cash variance reconciliation

---

PHASE 6: Integration Workflows and Data Integrity

feature: Inventory ↔ Production Integration
prompt
Goal
- Ensure item requests feed production plans; stock consumption aligns with produced outputs.

Schemas
- item_requests/details, production_requests, raw_material_utilizations, stock_movements

Acceptance criteria
- Traceability from request → production → movement; no negative inventory


feature: Production ↔ Sales Integration
prompt
Goal
- Produced goods dispatch to sales; product stocks reflect additions; kitchen orders back to production.

Schemas
- production_records/daily_produces → product_stocks additions; kitchen_orders lifecycle; transfers between departments

Acceptance criteria
- Product availability matches recorded sends; sales cannot oversell beyond product stock per shift

---

Appendix A: ID Strategy and Entities

- UUID PKs for users/branches/employees per main.md. Current migrations already reflect: branches.id (uuid), employees.id (uuid). Department is bigint.
- Inventory tables use bigint PKs with FKs to these entities; ensure guards and middleware apply branch/department scoping consistently.

Appendix B: Eloquent Relationships (from moels.md)
- Inventory: Item hasMany Stock, PurchaseItem, ItemRequestDetail; Stock hasMany StockMovement, HealthCheck; ItemRequest hasMany ItemRequestDetail and ItemDispatch
- Production: Recipe hasMany RecipeIngredient/DailyProduce/ProductionRecord; Shift hasMany DailyProduce, ProductionRequest, CallBack, RawMaterialUtilization
- Sales: ProductCategory hasMany Product; Product hasMany SaleItem, ProductStock, Transfer, KitchenOrder; SalesShift hasMany Sale, ProductStock, TableOrder, KitchenOrder

Appendix C: Indexing and Concurrency
- Unique and indexes per migrations: stocks unique(branch_id,item_id); stock_takes and purchases unique numbers; product_stocks unique(sales_shift_id,product_id,stock_date,shift_type)
- Add indexes: stock_movements(stock_id,type,movement_date); item_requests(branch_id,department_id,status); item_dispatches(request_id,item_id)
- Use transactions and row-level locking on stocks/product_stocks during adjustments and dispatch to maintain before/after correctness.
