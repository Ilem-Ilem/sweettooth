# SweetTooth Restaurant Management System - Implementation Roadmap

## Current Status

### ✅ Phase 1 COMPLETE: Department & Position Structure
- ✅ 8 SweetTooth-specific departments created
- ✅ 21 positions with reporting hierarchy
- ✅ 20 branches ready
- ✅ 19 roles with permissions configured
- ✅ Database migrations run successfully
- ✅ All seeders executed

---

## 🎯 PHASE 2: Inventory Management Module (IN PROGRESS)

**Priority: HIGH - Foundation for Production and Sales**

### Why Inventory First?
- Production departments need raw materials from inventory
- Sales departments need finished products from production
- Everything flows through inventory tracking

### 2.1 Database Structure

#### Tables to Create (11 total):

1. **items** - Raw materials, packaging, consumables, equipment
   - Fields: id, branch_id, name, sku, category, uom, description, reorder_level, max_stock_level, status

2. **purchases** - Record purchases with FOB costs, landing costs, exchange rates
   - Fields: id, branch_id, recorded_by, purchase_number, purchase_date, supplier_name, supplier_contact, total_fob_fc, total_fob_ngn, other_costs, landing_cost, total_cost, currency, exchange_rate, payment_status, notes

3. **purchase_items** - Line items for purchases
   - Fields: id, purchase_id, item_id, quantity, uom, fob_fc, fob_ngn, other_costs, landing_cost, total_cost, cost_per_unit

4. **stocks** - Real-time stock levels per branch
   - Fields: id, branch_id, item_id, quantity_available, quantity_reserved, quantity_damaged, average_cost, last_stock_take_date, health_status, expiry_date

5. **stock_movements** - Track every in/out movement
   - Fields: id, stock_id, type, quantity, quantity_before, quantity_after, reference_type, reference_id, moved_by, notes, movement_date

6. **item_requests** - Production/Sales departments request items
   - Fields: id, branch_id, department_id, requested_by, request_number, request_date, shift, status, approved_by, approved_at, notes

7. **item_request_details** - Line items for requests
   - Fields: id, request_id, item_id, quantity_requested, quantity_approved, quantity_dispatched, uom, notes

8. **item_dispatches** - Fulfill requests and track delivery
   - Fields: id, request_id, item_id, dispatched_by, received_by, quantity, uom, dispatch_time, received_time, shift, notes

9. **stock_takes** - Physical inventory counts (daily/weekly/monthly)
   - Fields: id, branch_id, stock_take_number, stock_take_date, type, conducted_by, status, verified_by, verified_at, notes

10. **stock_take_details** - Line items for stock takes
    - Fields: id, stock_take_id, item_id, system_quantity, physical_quantity, variance, variance_type, notes

11. **health_checks** - Expiry tracking and quality control
    - Fields: id, stock_id, checked_by, check_date, condition, quantity_affected, observations, action_taken

### 2.2 Models to Create

- Item.php
- Purchase.php
- PurchaseItem.php
- Stock.php
- StockMovement.php
- ItemRequest.php
- ItemRequestDetail.php
- ItemDispatch.php
- StockTake.php
- StockTakeDetail.php
- HealthCheck.php

**Model Relationships:**
- Item → hasMany(Stocks, PurchaseItems, ItemRequestDetails)
- Purchase → hasMany(PurchaseItems), belongsTo(Employee as recorder)
- Stock → belongsTo(Branch, Item), hasMany(StockMovements, HealthChecks)
- ItemRequest → belongsTo(Department, Employee), hasMany(ItemRequestDetails, ItemDispatches)

### 2.3 Seeders to Create

- ItemSeeder.php (Sample raw materials: flour, sugar, milk, etc.)
- PurchaseSeeder.php (Sample purchase records)
- StockSeeder.php (Initial stock levels per branch)

### 2.4 Livewire Components to Build

**Purchase Management:**
- `app/Livewire/Inventory/Purchases/Index.php` - List all purchases
- `app/Livewire/Inventory/Purchases/Create.php` - Record new purchase
- `app/Livewire/Inventory/Purchases/View.php` - View purchase details

**Stock Management:**
- `app/Livewire/Inventory/Stock/Index.php` - Stock levels dashboard
- `app/Livewire/Inventory/Stock/StockTake.php` - Conduct stock take
- `app/Livewire/Inventory/Stock/HealthCheck.php` - Health/expiry checks

**Request & Dispatch:**
- `app/Livewire/Inventory/Requests/Pending.php` - View pending requests
- `app/Livewire/Inventory/Requests/Process.php` - Approve/reject requests
- `app/Livewire/Inventory/Dispatches/Index.php` - Dispatch items

**Reports:**
- `app/Livewire/Inventory/Reports/StockMovement.php` - Movement history
- `app/Livewire/Inventory/Reports/LowStock.php` - Low stock alerts
- `app/Livewire/Inventory/Reports/ExpiryWarnings.php` - Expiring items

### 2.5 Workflow: Request & Dispatch System

**Daily Flow:**
1. **Morning Shift (Production):**
   - Chef/production staff logs in
   - Creates item request for raw materials needed
   - Request goes to Inventory Manager

2. **Inventory Manager:**
   - Reviews pending requests
   - Approves quantities based on stock levels
   - Assigns to Store Keeper for dispatch

3. **Store Keeper:**
   - Picks approved items from stock
   - Dispatches to requesting department
   - Records dispatch with quantities

4. **Department Receives:**
   - Production department confirms receipt
   - Stock movement recorded automatically
   - Production can begin

### 2.6 Pages to Create (Blade Views)

```
resources/views/livewire/inventory/
├── purchases/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── view.blade.php
├── stock/
│   ├── index.blade.php
│   ├── stock-take.blade.php
│   └── health-check.blade.php
├── requests/
│   ├── pending.blade.php
│   ├── process.blade.php
│   └── history.blade.php
├── dispatches/
│   └── index.blade.php
└── reports/
    ├── stock-movement.blade.php
    ├── low-stock.blade.php
    └── expiry-warnings.blade.php
```

---

## 🎯 PHASE 3: Production Module (PENDING)

Kitchen, Gelato, Pastry departments

### 3.1 Database Structure (8 tables)

1. **recipes** - Product recipes with ingredients
2. **recipe_ingredients** - Ingredients per recipe
3. **shifts** - Production shifts tracking
4. **daily_produces** - Daily production tracking
5. **production_records** - Detailed production logs
6. **production_requests** - Link requests to recipes
7. **call_backs** - Rejected/bad items tracking
8. **raw_material_utilizations** - Track ingredient usage vs. expected

### 3.2 Daily Production Workflow

1. **Clock In** - Staff starts shift
2. **Request Items** - Request raw materials from inventory
3. **Record Production** - Log produced quantities
4. **Send Out** - Dispatch to sales departments
5. **Callbacks** - Record rejected items
6. **Closing** - Calculate variance

**Formula per product:**
```
Opening + Produced - Sent Out - Callbacks = Closing
```

### 3.3 Components to Build

- Production dashboard per department
- Shift login/logout
- Recipe management (create, edit, cost calculation)
- Daily produce tracking form
- Callback recording
- Production reports

---

## 🎯 PHASE 4: Sales Module (PENDING)

Till, Corner Store, Confectionery departments

### 4.1 Database Structure (12 tables)

1. **product_categories** - Food, beverage, dessert, gelato, pastry
2. **products** - Finished products linked to recipes
3. **tables** - Restaurant tables for Corner Store
4. **sales_shifts** - Sales shift tracking with cash management
5. **sales** - Individual sales transactions
6. **sale_items** - Line items per sale
7. **payments** - Payment tracking (cash, POS, transfer)
8. **table_orders** - Orders per table (Corner Store)
9. **table_order_items** - Items per table order
10. **product_stocks** - Product stock per shift
11. **transfers** - Product transfers between departments
12. **kitchen_orders** - Orders from sales to production

### 4.2 Daily Sales Workflow

1. **Clock In** - Cashier starts shift with opening cash
2. **Receive Stock** - Get products from production
3. **Process Sales** - Customer transactions
4. **Transfers** - Send/receive from other departments
5. **Glovo Orders** - Delivery platform sales
6. **Closing** - Count cash, calculate variance

**Formula per product:**
```
Opening + Addition - Transfer - Glovo - Sold = Closing
Total Available = Opening + Addition + Callbacks + Redress
```

### 4.3 Corner Store Features

- Table management (status tracking)
- Order taking per table
- Guest count tracking
- Split bills
- Payment processing

---

## 🎯 PHASE 5: Role-Based Dashboards (PENDING)

### 5.1 Super Admin Dashboard
- Overview of all branches
- User & branch management
- Department & position management
- System-wide reports
- Access: Super admin only

### 5.2 Managing Director (MD) Dashboard
- All branch analytics
- Financial summary reports
- Employee overview across branches
- Performance metrics
- Department comparisons
- Access: MD role

### 5.3 Branch Manager Dashboard
- Single branch operations
- Department performance within branch
- Staff management for branch
- Branch-specific reports
- Inventory levels
- Access: Branch Manager role

### 5.4 Department Dashboards

**Production Departments (Kitchen/Gelato/Pastry):**
- Shift login/logout
- Daily produce tracking interface
- Request items from inventory
- Record production quantities
- Callback management
- Shift closing summary
- Access: Production staff roles

**Sales Departments (Till/Corner Store/Confectionery):**
- Shift management with cash tracking
- POS/sales processing
- Product stock levels
- Transfer management
- Glovo order tracking
- Shift closing reports
- Access: Sales staff roles

**Inventory Department:**
- Stock levels overview
- Purchase management
- Request approval interface
- Dispatch processing
- Health checks & expiry alerts
- Stock take management
- Access: Inventory Manager, Store Keeper roles

---

## 🎯 PHASE 6: Reports & Analytics (PENDING)

### 6.1 Inventory Reports
- Stock movement history
- Purchase history with cost analysis
- Low stock alerts
- Expiry warnings
- Variance reports from stock takes

### 6.2 Production Reports
- Production vs. demand analysis
- Raw material utilization efficiency
- Cost analysis per recipe
- Quality metrics (callback rates)
- Shift performance comparison

### 6.3 Sales Reports
- Daily/shift sales summary
- Product performance (best sellers)
- Cash variance tracking
- Transfer history
- Payment method breakdown

### 6.4 Financial Reports
- Revenue by department
- Revenue by branch
- Cost of goods sold (COGS)
- Profit margins per product
- Branch comparison analytics

---

## Implementation Timeline

**WEEK 1-2:** ✅ Phase 1 - Department & Position Structure (COMPLETE)
**WEEK 3-4:** 🔄 Phase 2 - Inventory Module (IN PROGRESS)
**WEEK 5-7:** Phase 3 - Production Module
**WEEK 8-10:** Phase 4 - Sales Module
**WEEK 11-12:** Phase 5 - Dashboards
**WEEK 13-14:** Phase 6 - Reports & Testing

---

## Current Tasks (Priority Order)

### Immediate Next Steps:

1. ✅ Fix Department Structure - DONE
2. ✅ Create Position System - DONE
3. ✅ Update Role & Permission Seeders - DONE
4. 🔄 Create Inventory Migrations (11 tables)
5. 🔄 Create Inventory Models (11 models)
6. ⏳ Create Inventory Seeders
7. ⏳ Build Inventory UI Components
8. ⏳ Test Inventory Workflow

---

## Technical Notes

### Technology Stack
- Laravel 11
- Livewire 3
- Tailwind CSS
- Alpine.js
- Spatie Laravel Permission
- MySQL Database

### Key Design Decisions
- UUIDs for primary keys: users, branches, employees
- BigInt for: departments, positions, items, products
- Shift-based tracking for production and sales
- Double-entry stock movement tracking
- Hierarchical position/reporting structure
- Branch-isolated operations with central oversight

### Security & Access Control
- Employee guard for branch/department staff
- Web guard for super admin only
- Role-based permissions via Spatie
- Department-level access restrictions
- Branch-level data isolation

---

## Questions to Resolve

1. Should departments be duplicated per branch or remain global with branch assignment?
   - Current: Global departments, employees assigned to branch + department

2. Currency handling for multi-currency purchases?
   - Current: Support NGN and FC (Foreign Currency) with exchange rates

3. Shift overlap handling (morning/afternoon)?
   - Current: Strict shift boundaries, no overlap

4. Glovo integration - API or manual entry?
   - Current: Manual entry planned, API integration future enhancement

---

## Success Metrics

**Phase 2 Success Criteria:**
- ✅ All 11 inventory tables created
- ✅ Models with proper relationships
- ✅ Request/dispatch workflow functional
- ✅ Stock movements tracked accurately
- ✅ Purchase cost calculation working
- ✅ Stock take variance detection

**Phase 3 Success Criteria:**
- Production recipes with cost calculation
- Shift tracking with opening/closing
- Raw material usage tracking
- Callback/wastage recording
- Production-to-sales dispatch

**Phase 4 Success Criteria:**
- Sales transactions processing
- Cash variance calculation
- Table management (Corner Store)
- Product stock tracking per shift
- Transfer between departments

---

## Notes

- Following the workflows from: `inventory.md`, `products.md`, `sales.md`
- File structure based on: `flow.md`
- Using existing structure from `main.md` for migrations
- All spacing and margins reduced for Material UI feel

● Perfect! Now let me create a comprehensive documentation explaining the
  Item Monitoring System:

  📊 COMPREHENSIVE STOCK LEVELS PAGE & ITEM MONITORING SYSTEM

  ✅ What I've Built:

  1. Modernized Stock Levels Page (stocks.blade.php)

  Features:
  - Modern UI with dark mode support
  - Export functionality (Excel & PDF)
  - Advanced filtering system:
    - Branch filter
    - Category filter (Raw Material, Packaging, Consumable, Equipment)
    - Stock Level filters:
        - Critical (< 25% of reorder level)
      - Low (below reorder level)
      - High (above reorder level)
      - Out of Stock
      - Overstock (above max level)
    - Health Status filter (Good, Warning, Critical, Expired)
    - Date range filter (Last Stock Take)
    - Advanced search

  Table Columns:
  - Item Name
  - SKU
  - Branch
  - Category
  - Available Stock (color-coded: red if low, green if good)
  - Reserved Stock
  - Damaged Stock
  - Total Stock
  - Reorder Level
  - Health Status (color-coded badges)
  - Last Stock Take Date
  - Actions (View History button)

  ---
  🔍 2. Item History & Monitoring System

  How It Works:

  1. Click "View History" button (clock icon) on any stock item
  2. Slide-in panel opens showing:

  A. Item Overview Section:

  - Item name, SKU, branch, category
  - Current stock summary cards (Available, Reserved, Damaged, Total)

  B. History Filtering:

  - From Date - Filter movements from specific date
  - To Date - Filter movements to specific date
  - Movement Type - Filter by:
    - In (Stock received)
    - Out (Stock dispatched)
    - Adjustment (Manual corrections)
    - Transfer (Between branches)
    - Damaged (Marked as damaged)
    - Return (Returns from customers/branches)

  C. Movement History Timeline:

  Each movement record shows:
  - Type badge (color-coded by movement type)
  - Quantity changed with UOM
  - Date and time of movement
  - Before/After quantities
  - Who performed it (Employee name)
  - Notes (reason for adjustment)

  ---
  📈 How to Use Item Monitoring:

  Scenario 1: Check When Item Was Last Restocked

  1. Go to Stock Levels page
  2. Find the item in the table
  3. Click "View History" button
  4. Look at the timeline - "In" movements show restocking
  5. See date, quantity, and who restocked it

  Scenario 2: Track Item from First Purchase

  1. Open item history
  2. Scroll to the bottom of timeline (oldest first)
  3. First "In" movement = Initial purchase
  4. See all subsequent purchases, adjustments, dispatches

  Scenario 3: Filter Movements by Time Period

  1. Open item history
  2. Set "From Date" = Start of period
  3. Set "To Date" = End of period
  4. View only movements in that timeframe

  Scenario 4: Find Who Made Stock Adjustments

  1. Open item history
  2. Filter by "Movement Type" = Adjustment
  3. Each record shows employee who made the adjustment
  4. See their notes explaining why

  Scenario 5: Track Damaged Stock

  1. Open item history
  2. Filter by "Movement Type" = Damaged
  3. See when stock was marked damaged
  4. Check who marked it and notes

  ---
  🎯 Advanced Monitoring Capabilities:

  What You Can Monitor:

  1. Purchase History - All stock received
  2. Dispatch History - All stock sent out
  3. Adjustment History - Manual stock corrections
  4. Transfer History - Stock movements between branches
  5. Damage Records - Stock marked as damaged
  6. Return Records - Items returned

  For Each Movement You See:

  - Exact quantity changed
  - Stock level before the movement
  - Stock level after the movement
  - Date and time (down to the minute)
  - Employee who performed it
  - Notes/reasons for the movement
  - Movement type (color-coded for easy identification)

  ---
  📊 Reporting & Analysis:

  You can answer questions like:
  - When was this item first purchased?
  - Who last restocked this item?
  - How much stock was damaged this month?
  - What adjustments were made and by whom?
  - When did we last transfer this item?
  - What's the complete audit trail for this item?

  ---
  🚀 Future Enhancements You Could Add:

  1. Charts/Graphs - Visualize stock levels over time
  2. Export History - Download movement history as PDF/Excel
  3. Alerts - Notify when item hits reorder level
  4. Predictions - Forecast when to reorder based on usage patterns
  5. Supplier Info - Track which supplier each purchase came from
  6. Cost Tracking - See cost changes over time

  ---
  💡 Key Benefits:

  ✅ Full Audit Trail - Every stock change is recorded
  ✅ Accountability - Know who made each change
  ✅ Transparency - See complete item history
  ✅ Compliance - Meet regulatory requirements
  ✅ Analysis - Understand stock patterns
  ✅ Problem Solving - Identify discrepancies quickly

  The system is now fully functional and ready to use!
