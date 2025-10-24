# SALES MODULE - PHASED IMPLEMENTATION PLAN

## 📋 Overview
Sales module for Corner Store, Till, and Confectionaries departments with comprehensive stock tracking, POS system, and shelf-life management.

---
ew

  The stock opening page manages the daily opening stock for products at the start of a shift. It
  helps track inventory by comparing expected vs actual quantities.

  Key Workflow

  1. Initialization (mount() - line 73)

  - Sets stock date to today
  - Loads the current active shift for the logged-in employee
  - Loads all stock opening data

  2. Shift Management (loadCurrentShift() - line 83)

  - Checks for an active shift for today for the current employee
  - Determines shift type (morning/evening)
  - If no active shift exists, user sees a warning but can still view data

  3. Stock Data Loading (loadStockOpeningData() - line 102)

  For each product in the employee's department:

  Calculates:
  - Yesterday's Closing: Gets the closing quantity from yesterday's stock record (line 136)
  - Today's Additions: Sums quantities from product_dispatches table for today's shift (line 144)
  - Expected Opening: Yesterday's closing + Today's additions (line 164)
  - Actual Opening: Either the saved value or defaults to expected opening (line 174)
  - Variance: Actual - Expected (line 175)

  4. User Interactions

  Editable Fields:
  - Actual Opening - User physically counts and enters real quantity (line 190)
  - Production Date - When product was made; auto-calculates expiry date (line 207)
  - Notes - Additional remarks (line 229)

  Real-time Updates:
  - When actual opening changes, variance recalculates automatically (line 198)
  - When production date changes, expiry date auto-calculates based on shelf life (line 217)

  5. Saving (saveStockOpenings() - line 243)

  - Requires an active shift
  - Uses updateOrCreate to save/update records in product_stocks table
  - Saves: opening quantity, addition quantity, production date, expiry date, notes
  - Once saved, fields become read-only (is_saved flag)

  6. Verification Status

  - Shows "Verified" badge if stock records exist for current shift
  - Shows "Not Verified" if not yet saved
  - Once verified, cannot modify data for that shift

  Key Features

  Filters: Search by product name/SKU, filter by product type (line 121-127)

  Visual Indicators:
  - Variance: Green (=0), Blue (↑ positive), Red (↓ negative) - line 144-153 in view
  - Status badges for verification

  Read-only after save: All input fields become disabled once stock opening is saved for the shift
  (line 136, 161, 181 in view)

  Department-specific: Only shows products from the logged-in employee's department (line 114)

  This system ensures accountability by requiring physical counts, tracking variances, and preventing
   modifications after verification.


## 🎯 PHASE 1: Database & Core Models (HIGHEST PRIORITY)
**Goal:** Create database foundation for sales operations

### ✅ Tasks:
1. **Create Migrations**
   - [ ] `create_product_stocks_table` - Core stock tracking per shift
   - [ ] `create_sales_shifts_table` - Sales shift management
   - [ ] `create_sales_table` - Main sales records
   - [ ] `create_sale_items_table` - Individual sale items
   - [ ] `create_payments_table` - Payment records (supports split payments)
   - [ ] Add `shelf_life_days` column to `products` table
   - [ ] Add `production_date` and `expiry_date` to `product_stocks` table

2. **Create Models**
   - [ ] `ProductStock` model with relationships
   - [ ] `SalesShift` model with relationships
   - [ ] `Sale` model with relationships
   - [ ] `SaleItem` model with relationships
   - [ ] `Payment` model with relationships

3. **Model Methods & Relationships**
   - [ ] ProductStock: `isExpired()`, `calculateExpiry()`, `getShelfLifeStatus()`
   - [ ] ProductStock: `calculateTotalAvailable()`, `calculateClosing()`
   - [ ] SalesShift: `calculateCashVariance()`, `getActiveSales()`
   - [ ] Sale: `calculateTotals()`, `hasMultiplePaymentMethods()`
   - [ ] Sale: `isFullyPaid()`, `getRemainingAmount()`

**Dependencies:** None
**Estimated Time:** 2-3 days

---

## 🎯 PHASE 2: Stock Opening & Clock-In Flow (HIGH PRIORITY)
**Goal:** Implement daily opening stock recording and shift start process

### ✅ Tasks:
1. **Stock Opening Component**
   - [ ] Create `Livewire\SalesDashboard\StockOpening\Index` component
   - [ ] Display products from kitchen dispatches
   - [ ] Show yesterday's closing + today's additions
   - [ ] Calculate expected opening (auto)
   - [ ] Allow manual input of actual opening
   - [ ] Show variance between expected and actual

2. **Shelf Life Alerts on Clock-In**
   - [ ] Create `CheckExpiredProducts` service
   - [ ] On employee clock-in, check for expired products
   - [ ] Display alert modal with list of expired products
   - [ ] Options: "Mark as Callback" or "Confirm Still Good"
   - [ ] Auto-mark expired if not confirmed within X hours

3. **Pre-Sale Stock Verification**
   - [ ] Create mandatory stock verification step
   - [ ] Must be completed before POS access
   - [ ] Record verification timestamp and employee
   - [ ] Create accountability trail

4. **UI/UX**
   - [ ] Stock opening dashboard page
   - [ ] Clock-in with expiry alerts modal
   - [ ] Stock verification checklist interface
   - [ ] Mobile-responsive design

**Dependencies:** Phase 1
**Estimated Time:** 3-4 days

---


  ✅ PHASE 2.1 COMPLETE: Shelf Life Alerts on Clock-In

  🎉 What Was Built:

  1. Database Layer

  - ✅ Migration: 2025_10_23_100000_create_expiry_confirmations_table.php
    - Tracks employee confirmations for expired products
    - Fields: product_stock_id, sales_shift_id, confirmed_by, action, notes, confirmed_at
    - Actions: confirmed_good or marked_callback
  - ✅ Model: ExpiryConfirmation (app/Models/ExpiryConfirmation.php)
    - Relationships to ProductStock, SalesShift, Employee
    - Helper methods for action labels and badge colors
    - Scopes for filtering confirmations

  2. Service Layer

  - ✅ Service: CheckExpiredProducts (app/Services/CheckExpiredProducts.php)
    - getExpiredProductsForShift() - Get expired/expiring products for a shift
    - getExpiringProductsForShift() - Get products expiring in next 2 days
    - hasConfirmation() - Check if product already confirmed
    - confirmStillGood() - Mark product as still good despite expiry
    - markAsCallback() - Mark product as callback and update stock
    - autoMarkExpiredProducts() - Auto-mark unconfirmed expired products after X hours

  3. Livewire Component

  - ✅ Component: ExpiryAlerts (app/Livewire/BranchDashboard/SalesDashboard/ExpiryAlerts.php)
    - Loads expired products when employee clocks in
    - Shows modal if expired products exist
    - Allows "Confirm Still Good" or "Mark as Callback" actions
    - Tracks callback quantity and notes
    - Removes items from list after action
    - Auto-redirects to dashboard when all products addressed

  4. View Layer

  - ✅ View: expiry-alerts.blade.php
  (resources/views/livewire/branch-dashboard/sales-dashboard/expiry-alerts.blade.php)
    - Modal with red header alert
    - Lists all expired products with details
    - Shows shelf life status badges
    - Inline callback form with quantity and notes
    - Warning section for expiring soon products
    - Success state when all products confirmed

  5. Clock-In Integration

  - ✅ Updated: Shift.php (app/Livewire/Auth/Shift.php)
    - Detects if employee is in sales department
    - Creates SalesShift record for sales employees
    - Checks for expired products after clock-in
    - Auto-redirects to expiry alerts if expired products found
    - Otherwise proceeds to dashboard normally

  6. Routing

  - ✅ Route Added: branch-dashboard.sales-dashboard.expiry-alerts
    - Path: /sales-dashboard/expiry-alerts
    - Accepts salesShiftId parameter
    - Protected by branch authentication

  ---
  🔄 How It Works:

  1. Employee Clocks In
     ↓
  2. System checks if Sales Department
     ↓ (Yes)
  3. Creates SalesShift record
     ↓
  4. CheckExpiredProducts service runs
     ↓
  5. Finds expired/expiring products
     ↓ (Products found)
  6. Redirects to Expiry Alerts modal
     ↓
  7. Employee takes action:
     - "Confirm Still Good" → Creates confirmation record
     - "Mark as Callback" → Updates stock callback_quantity + creates confirmation
     ↓
  8. After all products addressed → Dashboard access

  ---
  📝 To Complete Setup:

  1. Run the migration:
  php artisan migrate
  2. Ensure sales department is properly identified:
    - The code checks if department name contains "sales" or "cashier"
    - Update isSalesDepartment() in Shift.php if needed (line 189-196)
  3. Test the flow:
    - Create test products with expiry dates
    - Create ProductStock records with expired dates
    - Clock in as sales employee
    - Verify expiry alert modal appears
    - Test both "Confirm Good" and "Callback" actions

  ---
  🚀 Next Steps (Option B - Stock Opening Verification):

  To implement mandatory stock opening before POS access, we need to:

  1. Create middleware to check if stock opening is verified
  2. Block access to POS/dashboard until verification complete
  3. Add verification timestamp to SalesShift model
  4. Create accountability trail


## 🎯 PHASE 3: Product in Stock Management (HIGH PRIORITY)
**Goal:** Track product availability and handle callbacks

### ✅ Tasks:
1. **Product Stock Tracking**
   - [ ] Create `ProductStock` auto-calculation logic
   - [ ] Formula: `Total Available = Opening + Addition - Callback - Redress`
   - [ ] Formula: `Closing = Total Available - Transfer - Glovo - Sold`
   - [ ] Real-time stock updates on sales
   - [ ] Stock deduction on each sale

2. **Callback System**
   - [ ] Create `Livewire\SalesDashboard\Callbacks\Index` component
   - [ ] Manual callback entry form
   - [ ] Automatic callback for expired products
   - [ ] Callback reasons (expired, damaged, quality issue)
   - [ ] Update product stock quantities
   - [ ] Track callback trends per product

3. **Shelf Life Management**
   - [ ] Add `production_date` tracking from kitchen dispatch
   - [ ] Auto-calculate expiry date (`production_date + shelf_life_days`)
   - [ ] Color-coded indicators:
     - 🟢 Fresh (>50% shelf life remaining)
     - 🟡 Warning (25-50% shelf life remaining)
     - 🟠 Critical (<25% shelf life remaining)
     - 🔴 Expired (0% shelf life remaining)
   - [ ] Daily automated check for expiring products
   - [ ] Notification system for expiring products

4. **Stock Dashboard**
   - [ ] Live view of product stock levels
   - [ ] Expired/expiring products section
   - [ ] Callback history
   - [ ] Stock variance analysis

**Dependencies:** Phase 1, Phase 2
**Estimated Time:** 4-5 days

---

## 🎯 PHASE 4: POS System - Core (CRITICAL PRIORITY)
**Goal:** Build point-of-sale interface for transactions

### ✅ Tasks:
1. **POS Interface**
   - [ ] Create `Livewire\SalesDashboard\POS\Index` component
   - [ ] Product search and selection
   - [ ] Shopping cart functionality
   - [ ] Quantity adjustment
   - [ ] Real-time price calculation
   - [ ] Product availability check (in-stock validation)
   - [ ] Mobile-optimized touch interface

2. **Cart Management**
   - [ ] Add to cart
   - [ ] Update quantity
   - [ ] Remove from cart
   - [ ] Clear cart
   - [ ] Hold/Park cart (save for later)
   - [ ] Recall parked carts

3. **Price Calculation**
   - [ ] Subtotal calculation
   - [ ] Tax calculation (if applicable)
   - [ ] Discount support (optional)
   - [ ] Total calculation
   - [ ] Display breakdown

4. **Stock Validation**
   - [ ] Check product availability before adding to cart
   - [ ] Prevent overselling (cart qty <= available stock)
   - [ ] Real-time stock updates
   - [ ] Warning for low stock

**Dependencies:** Phase 1, Phase 3
**Estimated Time:** 5-6 days

---

## 🎯 PHASE 5: Payment System (CRITICAL PRIORITY)
**Goal:** Handle complete payments with multiple payment methods

### ✅ Tasks:
1. **Payment Interface**
   - [ ] Create `Livewire\SalesDashboard\POS\PaymentModal` component
   - [ ] Display total amount to pay
   - [ ] Select payment method(s)
   - [ ] Split payment support
   - [ ] Payment validation

2. **Payment Methods**
   - [ ] Cash payment
     - [ ] Amount received input
     - [ ] Change calculation
     - [ ] Cash drawer tracking
   - [ ] POS Machine
     - [ ] Transaction reference input
     - [ ] Receipt number
   - [ ] Bank Transfer
     - [ ] Account details display
     - [ ] Transfer reference input
     - [ ] Confirmation

3. **Split Payment Logic**
   - [ ] Allow 1, 2, or 3 payment methods
   - [ ] Track amount per method
   - [ ] Validate: Sum of payments = Total
   - [ ] No partial payments allowed
   - [ ] Lock sale until fully paid

4. **Payment Completion**
   - [ ] Validate full payment
   - [ ] Create Sale record
   - [ ] Create SaleItem records
   - [ ] Create Payment record(s)
   - [ ] Deduct from ProductStock
   - [ ] Generate receipt number
   - [ ] Print/Display receipt

5. **Payment Validation**
   - [ ] Ensure total payments = sale total
   - [ ] Prevent incomplete sales
   - [ ] Error handling for failed payments
   - [ ] Rollback on failure

**Dependencies:** Phase 4
**Estimated Time:** 4-5 days

---

## 🎯 PHASE 6: Sales Dashboard & Reporting (MEDIUM PRIORITY)
**Goal:** Track sales performance per employee and shift

### ✅ Tasks:
1. **Employee Sales Dashboard**
   - [ ] Create `Livewire\SalesDashboard\MySales\Index` component
   - [ ] Display employee's sales for current shift
   - [ ] Sales count
   - [ ] Total sales amount
   - [ ] Payment method breakdown
   - [ ] Best-selling products
   - [ ] Hourly sales chart

2. **Shift Summary**
   - [ ] Opening stock summary
   - [ ] Products sold
   - [ ] Stock remaining
   - [ ] Callbacks recorded
   - [ ] Revenue by payment method
   - [ ] Cash variance

3. **Sales History**
   - [ ] View past sales
   - [ ] Search/filter by date, product, payment method
   - [ ] Sale details view
   - [ ] Reprint receipt

4. **Performance Metrics**
   - [ ] Sales target vs actual
   - [ ] Average transaction value
   - [ ] Products per sale
   - [ ] Peak sales hours
   - [ ] Conversion rate (if applicable)

**Dependencies:** Phase 5
**Estimated Time:** 3-4 days

---

## 🎯 PHASE 7: Kitchen Dispatch Integration (MEDIUM PRIORITY)
**Goal:** Connect sales departments with kitchen for product requests

### ✅ Tasks:
1. **Kitchen Dispatch Receiving**
   - [ ] Create `Livewire\SalesDashboard\Dispatches\Index` component
   - [ ] View incoming dispatches from kitchen
   - [ ] Receive and confirm quantities
   - [ ] Auto-update ProductStock with additions
   - [ ] Track production_date from kitchen
   - [ ] Discrepancy reporting

2. **Dispatch History**
   - [ ] View past dispatches
   - [ ] Filter by date, product
   - [ ] Dispatch details
   - [ ] Variance tracking

3. **Low Stock Requests**
   - [ ] Create request to kitchen for more products
   - [ ] Based on current stock levels
   - [ ] Urgent/normal priority
   - [ ] Track request status

**Dependencies:** Phase 3
**Estimated Time:** 3-4 days

---

## 🎯 PHASE 8: End-of-Shift Closing (HIGH PRIORITY)
**Goal:** Complete shift closure with stock reconciliation

### ✅ Tasks:
1. **Closing Stock Entry**
   - [ ] Create `Livewire\SalesDashboard\ShiftClosing\Index` component
   - [ ] Display expected closing (auto-calculated)
   - [ ] Manual entry of actual closing (physical count)
   - [ ] Calculate variance
   - [ ] Variance percentage
   - [ ] Highlight high variance items (>5%)

2. **Cash Reconciliation**
   - [ ] Opening cash amount
   - [ ] Expected cash (opening + cash sales - change given)
   - [ ] Actual cash count
   - [ ] Cash variance
   - [ ] Variance explanation notes

3. **Shift Closure**
   - [ ] Validate all fields completed
   - [ ] Submit for review
   - [ ] Lock shift (no more sales)
   - [ ] Generate closing report
   - [ ] Transfer closing to next shift's opening

4. **Variance Investigation**
   - [ ] Flag high variance items
   - [ ] Require manager approval if variance >X%
   - [ ] Notes and explanations
   - [ ] Corrective actions

**Dependencies:** Phase 3, Phase 5, Phase 6
**Estimated Time:** 3-4 days

---

## 🎯 PHASE 9: Advanced Features (LOW PRIORITY)
**Goal:** Additional features for enhanced functionality

### ✅ Tasks:
1. **Glovo Integration** (If needed)
   - [ ] Track Glovo orders separately
   - [ ] Deduct from product stock
   - [ ] Commission tracking
   - [ ] Glovo sales reporting

2. **Inter-Department Transfers**
   - [ ] Request transfer from another sales dept
   - [ ] Send transfer to another sales dept
   - [ ] Track transfer status
   - [ ] Update product stocks

3. **Discount Management**
   - [ ] Manual discounts (with approval)
   - [ ] Promotional discounts
   - [ ] Discount tracking
   - [ ] Loss tracking

4. **Customer Management** (Optional)
   - [ ] Loyalty programs
   - [ ] Customer accounts
   - [ ] Purchase history
   - [ ] Rewards tracking

**Dependencies:** All previous phases
**Estimated Time:** 5-7 days

---

## 🎯 PHASE 10: Analytics & Management (LOW PRIORITY)
**Goal:** Comprehensive sales analytics for management

### ✅ Tasks:
1. **Sales Analytics Dashboard**
   - [ ] Revenue by department
   - [ ] Revenue by product
   - [ ] Revenue by payment method
   - [ ] Revenue trends (daily, weekly, monthly)
   - [ ] Peak sales times
   - [ ] Employee performance

2. **Stock Analytics**
   - [ ] Stock turnover rate
   - [ ] Slow-moving products
   - [ ] Fast-moving products
   - [ ] Waste tracking (callbacks)
   - [ ] Expiry trends
   - [ ] Optimal stock levels

3. **Financial Reports**
   - [ ] Daily sales summary
   - [ ] Shift reconciliation report
   - [ ] Payment method breakdown
   - [ ] Cash variance report
   - [ ] Profit margins

4. **Alerts & Notifications**
   - [ ] Low stock alerts
   - [ ] High variance alerts
   - [ ] Expiring products alerts
   - [ ] Unusual sales patterns
   - [ ] Cash shortage/overage alerts

**Dependencies:** All core phases
**Estimated Time:** 4-5 days

---

## 📊 PRIORITY MATRIX

### CRITICAL PATH (Must have for basic operations):
1. ✅ Phase 1: Database & Core Models
2. ✅ Phase 2: Stock Opening & Clock-In Flow
3. ✅ Phase 3: Product in Stock Management
4. ✅ Phase 4: POS System - Core
5. ✅ Phase 5: Payment System
6. ✅ Phase 8: End-of-Shift Closing

### IMPORTANT (Essential for smooth operations):
7. ⚡ Phase 6: Sales Dashboard & Reporting
8. ⚡ Phase 7: Kitchen Dispatch Integration

### ENHANCEMENT (Nice to have):
9. 💡 Phase 9: Advanced Features
10. 💡 Phase 10: Analytics & Management

---

## 🔄 RECOMMENDED IMPLEMENTATION ORDER

**Week 1-2:** Phase 1 + Phase 2
- Set up database structure
- Implement clock-in flow with expiry alerts
- Basic stock opening

**Week 3-4:** Phase 3 + Phase 4
- Complete product stock tracking
- Build POS interface
- Implement shelf life management

**Week 5-6:** Phase 5 + Phase 8
- Payment system with split payments
- Shift closing procedures
- Stock reconciliation

**Week 7-8:** Phase 6 + Phase 7
- Sales dashboards
- Kitchen dispatch integration
- Reporting

**Week 9+:** Phase 9 + Phase 10
- Advanced features
- Analytics
- Optimization

---

## 🎯 SUCCESS METRICS

### Phase Completion Criteria:
- ✅ All database tables created and migrated
- ✅ All models created with relationships
- ✅ Clock-in triggers expiry alerts
- ✅ Stock verification mandatory before sales
- ✅ POS system can complete sales
- ✅ Payment validation prevents partial payments
- ✅ Split payments work correctly
- ✅ Stock deducts on each sale
- ✅ Employee can see their shift sales
- ✅ Shift closing calculates variance
- ✅ Expired products auto-flagged

### User Acceptance Criteria:
- Sales staff can clock in and see expiry alerts
- Sales staff can record opening stock
- Sales staff can make sales through POS
- Multiple payment methods work
- No partial payments possible
- Stock updates in real-time
- Shift closing shows variance
- Reports are accurate

---

## 📝 NOTES

### Key Business Rules:
1. **No Partial Payments** - Sale cannot complete unless fully paid
2. **Stock Verification Mandatory** - Must verify stock before accessing POS
3. **Expiry Alerts on Clock-In** - Cannot bypass expired product check
4. **Transparency with Accountability** - Show expected closing but allow actual input
5. **Variance Threshold** - Flag variance >5% for investigation
6. **Shelf Life Tracking** - Auto-calculate from production date
7. **Multi-Payment Support** - Can split between Cash + POS + Transfer

### Technical Considerations:
- Use Livewire for real-time updates
- TallStackUI for consistent UI
- Database transactions for sale completion
- Stock locking to prevent overselling
- Audit trails for all stock changes
- Mobile-first design for POS

### Security Considerations:
- Employee can only see their own sales
- Manager approval for high variance
- Cash reconciliation required
- Payment rollback on failures
- Stock movement audit log

---

## 📅 ESTIMATED TOTAL TIME
- **Critical Path:** 18-22 days
- **Full Implementation:** 30-40 days
- **MVP (Phases 1-6):** 20-25 days

---

**Last Updated:** 2025-10-20
**Status:** Planning Phase
**Next Step:** Begin Phase 1 - Database & Core Models



## starting point
📊 Implementation Status Report
✅ PHASE 1: Database & Core Models - 90% COMPLETE
What's Done:

✅ All migrations created (product_stocks, sales_shifts, sales, sale_items, payments, product_callbacks)
✅ All models created with complete relationships
✅ ProductStock has auto-calculation methods (calculateTotalAvailable, calculateClosing)
✅ ProductStock has shelf-life methods (isExpired, calculateExpiry, getShelfLifeStatus)
✅ SalesShift has helper methods (calculateCashVariance, getTotalSales)
✅ ProductCallback model with reason tracking
✅ Products table has shelf_life_days column

What's Missing:

⚠ Need to verify Sale model has all required methods
⚠ Need to check if SaleItem model exists and is complete
⚠ Need ProductCallback table to have proper foreign keys to ProductStock


🟡 PHASE 2: Stock Opening & Clock-In Flow - 50% COMPLETE
What's Done:

✅ StockOpening/Index component created
✅ Shows yesterday's closing + today's additions
✅ Calculates expected opening automatically
✅ Allows manual actual opening input
✅ Shows variance calculation
✅ Production date and expiry tracking

What's Missing (HIGH PRIORITY):

❌ CheckExpiredProducts service - needs to be created
❌ Shelf life alerts on clock-in - modal with expired products
❌ Pre-sale stock verification - mandatory before POS access
❌ Stock verification accountability trail - who verified when
❌ View templates for StockOpening (need to check if exists)


🟡 PHASE 3: Product in Stock Management - 60% COMPLETE
What's Done:

✅ ProductStock auto-calculation logic implemented
✅ Callbacks/Index component created
✅ Manual callback entry form
✅ Shelf life status color coding (fresh, warning, critical, expired)
✅ Callback reasons (expired, damaged, quality_issue, etc.)

What's Missing (HIGH PRIORITY):

❌ Stock Monitor Dashboard - needs completion/view
❌ Automated daily expired products check (scheduled job)
❌ Notification system for expiring products
❌ View templates for Callbacks (need to check)


🎯 Recommended Next Steps (In Order)
STEP 1: Complete Missing Database Pieces (1-2 hours)
// 1. Create stock_verifications table for accountability
Schema::create('stock_verifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sales_shift_id')->constrained()->onDelete('cascade');
    $table->foreignId('verified_by')->constrained('employees');
    $table->timestamp('verified_at');
    $table->integer('products_count');
    $table->decimal('total_variance', 12, 2)->default(0);
    $table->text('notes')->nullable();
    $table->timestamps();
});

// 2. Create expired_product_alerts table
Schema::create('expired_product_alerts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_stock_id')->constrained()->onDelete('cascade');
    $table->foreignId('sales_shift_id')->constrained()->onDelete('cascade');
    $table->foreignId('employee_id')->constrained();
    $table->enum('action', ['callback_marked', 'confirmed_good', 'pending'])->default('pending');
    $table->timestamp('alerted_at');
    $table->timestamp('actioned_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

STEP 2: Create CheckExpiredProducts Service (2-3 hours)
// app/Services/CheckExpiredProducts.php
namespace App\Services;

class CheckExpiredProducts
{
    public function checkForExpiredProducts($salesShiftId)
    {
        // Get all product stocks for the shift that are expired or critical
        // Create alerts for each
        // Return list of expired products
    }

    public function markAsCallback($alertId, $employeeId)
    {
        // Mark alert as actioned
        // Create ProductCallback entry
        // Update ProductStock callback_quantity
    }

    public function confirmStillGood($alertId, $employeeId, $notes)
    {
        // Mark alert as actioned with notes
        // Log who confirmed and why
    }
}

STEP 3: Create Expiry Alert Modal Component (3-4 hours)
// app/Livewire/BranchDashboard/SalesDashboard/ExpiryAlertModal.php
// Triggered on clock-in
// Shows list of expired/expiring products
// Options: Mark as Callback OR Confirm Still Good

STEP 4: Create Stock Verification Flow (2-3 hours)
// Modify StockOpening/Index to add verification step
public function verifyAndLockStock()
{
    // Create StockVerification record
    // Mark shift as "stock_verified"
    // Unlock POS access
}

STEP 5: Create View Templates (4-6 hours)
Check if these exist, if not create:

resources/views/livewire/branch-dashboard/sales-dashboard/stock-opening/index.blade.php
resources/views/livewire/branch-dashboard/sales-dashboard/callbacks/index.blade.php
resources/views/livewire/branch-dashboard/sales-dashboard/expiry-alert-modal.blade.php


📋 Database Structure - What You Need
New Migrations Needed:
# 1. Stock Verifications
php artisan make:migration create_stock_verifications_table

# 2. Expired Product Alerts
php artisan make:migration create_expired_product_alerts_table

# 3. Add stock_verified_at to sales_shifts
php artisan make:migration add_stock_verified_fields_to_sales_shifts_table

Schema Details:
-- Stock Verifications (tracks who verified opening stock)
CREATE TABLE stock_verifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sales_shift_id BIGINT NOT NULL,
    verified_by BIGINT NOT NULL,
    verified_at TIMESTAMP NOT NULL,
    products_count INT,
    total_variance DECIMAL(12,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (sales_shift_id) REFERENCES sales_shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES employees(id)
);

-- Expired Product Alerts (tracks shelf-life alerts)
CREATE TABLE expired_product_alerts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_stock_id BIGINT NOT NULL,
    sales_shift_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    action ENUM('callback_marked', 'confirmed_good', 'pending') DEFAULT 'pending',
    alerted_at TIMESTAMP NOT NULL,
    actioned_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (product_stock_id) REFERENCES product_stocks(id) ON DELETE CASCADE,
    FOREIGN KEY (sales_shift_id) REFERENCES sales_shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- Add to sales_shifts table
ALTER TABLE sales_shifts ADD COLUMN stock_verified_at TIMESTAMP NULL;
ALTER TABLE sales_shifts ADD COLUMN stock_verified_by BIGINT NULL;
ALTER TABLE sales_shifts ADD CONSTRAINT fk_stock_verified_by
    FOREIGN KEY (stock_verified_by) REFERENCES employees(id);


🚀 Step-by-Step Implementation Plan
Week 1: Complete Phase 2
Day 1-2:

Create new migrations (stock_verifications, expired_product_alerts)
Create models (StockVerification, ExpiredProductAlert)
Run migrations

Day 3-4:4. Create CheckExpiredProducts service5. Create ExpiryAlertModal Livewire component6. Integrate with clock-in flow
Day 5:7. Add stock verification step to StockOpening8. Create verification accountability trail9. Testing
Week 2: Complete Phase 3
Day 1-2:

Create StockMonitor view template
Add real-time stock updates
Create expired products section

Day 3:4. Create scheduled job for daily expiry checks5. Create notification system
Day 4-5:6. Complete Callbacks view template7. Add callback history and trends8. Testing and refinement

📁 Files You Need to Create
app/
├── Services/
│   └── CheckExpiredProducts.php [NEW]
├── Models/
│   ├── StockVerification.php [NEW]
│   └── ExpiredProductAlert.php [NEW]
├── Livewire/
│   └── BranchDashboard/
│       └── SalesDashboard/
│           └── ExpiryAlertModal.php [NEW]
└── Console/
    └── Commands/
        └── CheckExpiredProductsDaily.php [NEW]

database/migrations/
├── xxxx_create_stock_verifications_table.php [NEW]
├── xxxx_create_expired_product_alerts_table.php [NEW]
└── xxxx_add_stock_verified_fields_to_sales_shifts_table.php [NEW]

resources/views/livewire/branch-dashboard/sales-dashboard/
├── stock-opening/
│   └── index.blade.php [VERIFY EXISTS]
├── callbacks/
│   └── index.blade.php [VERIFY EXISTS]
└── expiry-alert-modal.blade.php [NEW]
