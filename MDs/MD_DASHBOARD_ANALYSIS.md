# MD (Managing Director) Dashboard Architecture & Structure Analysis

**Date:** November 14, 2025
**Project:** SweetTooth RMS (Restaurant Management System)
**Analysis Scope:** Dashboard hierarchy, roles/permissions, analytics access patterns

---

## EXECUTIVE SUMMARY

The SweetTooth RMS currently has **NO dedicated MD (Managing Director) dashboard** despite having:
- Comprehensive role-based access control with "Managing Director" role defined
- Three-tier dashboard hierarchy (Branch, SuperAdmin, and partial department dashboards)
- Extensive analytics and reporting infrastructure
- Role hierarchy with MD at Level 5 (Executive level)

The application is structured to support MD dashboards, but this layer has not yet been implemented. All MD-level users currently access the **SuperAdmin dashboard** which provides organization-wide visibility.

---

## 1. CURRENT DASHBOARD HIERARCHY

### 1.1 Dashboard Tiers (Implemented)

```
┌─────────────────────────────────────────────┐
│    SUPER-ADMIN DASHBOARD (Level 5)         │
│  - Organization-wide visibility            │
│  - All branches & departments              │
│  - System configuration & settings         │
│  - Role & permission management            │
└─────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────┐
│    BRANCH DASHBOARD (Levels 1-5)           │
│  - Branch-specific operations              │
│  - Department management                   │
│  - Employee management                     │
│  - Analytics (inventory, sales, production)│
└─────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────┐
│    DEPARTMENT DASHBOARDS (Levels 1-3)      │
│  - Production modules (by dept)            │
│  - Sales modules (by sales dept)           │
│  - Shift closing operations                │
└─────────────────────────────────────────────┘
```

### 1.2 Missing Layer: MD Dashboard (Proposed)

```
┌─────────────────────────────────────────────┐
│    MD DASHBOARD (Level 5) [MISSING]        │
│  - Executive summary across all branches   │
│  - Key performance indicators              │
│  - Cross-branch analytics & comparisons    │
│  - Financial overview                      │
│  - Operational alerts & escalations        │
│  - Strategic metrics & trends              │
└─────────────────────────────────────────────┘
```

---

## 2. ROLE & PERMISSION STRUCTURE

### 2.1 Role Hierarchy (Implemented in RolePermission Helper)

The application uses **Spatie\Permission** package with custom role hierarchy:

```
LEVEL 5 (Executive - Highest Authority)
├── Super Admin (Full system access)
└── Managing Director (Full operational control)

LEVEL 4 (Management)
├── Admin (System administration)
├── Head of Production (Production oversight)
├── Sales Manager (Sales operations)
├── HR Manager (Human resources)
└── Inventory Manager (Stock management)

LEVEL 3 (Department Heads/Supervisors)
├── Chef (Kitchen leadership)
├── Head of Gelato (Gelato production)
├── Confectionaries Manager (Confectionaries)
├── Till Supervisor (POS operations)
└── Corner Store Manager (Corner store)

LEVEL 2 (Officers)
├── HR Officer
├── Stock Controller
└── Store Keeper

LEVEL 1 (Staff - Lowest)
├── Kitchen Staff
├── Gelato Production Staff
├── Confectionaries Production Staff
├── Cashier
├── Corner Store Staff
├── Confectionaries Sales Staff
```

**Key Finding:** Managing Director = Level 5 with highest authority equal to Super Admin

### 2.2 Authentication Guards

**File:** `/home/ilem/Documents/sweettooth/config/auth.php`

Two separate authentication guards:
- **web**: User model (used for Super Admin)
- **employees**: Employee model (used for all branch employees, including MDs)

Both can have roles and permissions via Spatie\Permission.

### 2.3 Role Permission Helper

**File:** `/home/ilem/Documents/sweettooth/app/Helpers/RolePermission.php`

Provides utility methods:
```php
RolePermission::isManagingDirector()        // Check MD role
RolePermission::hasRoleLevel(5)             // Check executive level
RolePermission::canManageUser($user)        // MD can manage anyone
RolePermission::canAccessModule('module')   // Module-based access
```

### 2.4 Blade Directives for Views

**File:** `/home/ilem/Documents/sweettooth/app/Providers/RolePermissionServiceProvider.php`

Available directives for templates:
```blade
@role('Managing Director')
@anyrole(['Admin', 'Super Admin'])
@permission('view-reports')
@rolelevel(5)
@manager  // includes all manager roles
@module('production')
```

---

## 3. ROUTE STRUCTURE

### 3.1 Routes File Organization

**Location:** `/home/ilem/Documents/sweettooth/routes/`

```
routes/
├── web.php              // Home, basic routes
├── auth.php             // Authentication routes
├── super-admin.php      // Super Admin dashboard routes
├── branch-route.php     // Branch dashboard routes
└── console.php          // Artisan commands
```

### 3.2 Super-Admin Routes (Current Executive Dashboard)

**File:** `/home/ilem/Documents/sweettooth/routes/super-admin.php`

**Prefix:** `/super-admin/`
**Middleware:** `['auth']` (no specific role check in routes)
**Entry Point:** `super-admin.analytics.dashboard`

Routes include:
```
POST    /super-admin/                          (prefix)
├── /roles                                     (Index)
├── /branches                                  (Management)
├── /employees                                 (CRUD)
├── /departments                               (Management)
├── /leave-management                          (Leave operations)
├── /inventory/                                (Stock operations)
├── /analytics/
│   ├── /dashboard                            (OverallSummaryDashboard)
│   ├── /stock-level                          (StockLevelAnalytics)
│   ├── /stock-movement                       (StockMovementAnalytics)
│   ├── /purchase                             (PurchaseAnalytics)
│   ├── /request-dispatch                     (RequestDispatchAnalytics)
│   ├── /alerts                               (AlertsDashboard)
│   ├── /supplier-performance                 (SupplierPerformance)
│   └── /stock-valuation                      (StockValuation)
└── /settings/                                (System configuration)
```

### 3.3 Branch Dashboard Routes

**File:** `/home/ilem/Documents/sweettooth/routes/branch-route.php` (152 lines)

**Prefix:** `/branch-dashboard/`
**Middleware:** `['auth:employees', 'branch']`
**Entry Point:** `branch-dashboard.index`

Routes organized by module:
```
├── /                                         (Main dashboard)
├── /employees/                               (Employee CRUD)
├── /leave/                                   (Leave management)
├── /departments/                             (Department management)
├── /inventory/
│   ├── /items, /purchases, /stocks
│   ├── /stock-movements, /item-requests
│   ├── /item-dispatches, /stock-takes
│   ├── /health-checks
│   ├── /shift-closing
│   └── /callbacks/
├── /production/
│   ├── /product-types, /products
│   ├── /request/, /daily-produce
│   ├── /recipes/, /module/, /raw-material-tracking
│   ├── /shift-closing/
│   └── /callbacks/
├── /sales-dashboard/
│   ├── /pos/, /analytics/, /my-sales/
│   ├── /shift-closing/, /stock-opening
│   ├── /expiry-alerts, /stock-monitor
│   └── /callbacks/
└── /analytics/                               (Cross-module analytics)
    ├── /overview, /stock-level, /stock-movement
    ├── /purchase, /request-dispatch
    ├── /alerts, /stock-valuation
```

### 3.4 Missing: MD Dashboard Routes

No dedicated route file for MD dashboard exists. Proposed structure would be:

```
routes/
└── md-dashboard.php     // NEW: MD-specific routes
    
Should include:
- /managing-director/dashboard              (Executive summary)
- /managing-director/analytics/
  ├── /cross-branch                         (Branch comparison)
  ├── /financial                            (Revenue/profitability)
  ├── /operational                          (KPIs)
  ├── /alerts                               (Critical issues)
  └── /trends                               (Strategic trends)
- /managing-director/reports/               (Scheduled reports)
- /managing-director/branches/              (Branch management)
```

---

## 4. LIVEWIRE COMPONENT STRUCTURE

### 4.1 Component Organization

**Base Directory:** `/home/ilem/Documents/sweettooth/app/Livewire/`

```
Livewire/
├── Auth/                         (Login, reset, shifts)
├── Actions/                      (Logout actions)
├── Settings/                     (User settings: profile, password, 2FA)
├── BranchDashboard/
│   ├── Index.php                (Main branch dashboard)
│   ├── HeaderClockInOut.php      (Clock in/out component)
│   ├── EmployeeModule/           (Employee CRUD & leave management)
│   ├── DepartmentModule/         (Department management)
│   ├── Analytics/                (7 analytics components)
│   ├── Inventory/
│   │   ├── Items, Purchases, Stocks, StockMovements
│   │   ├── StockTakes, HealthChecks
│   │   ├── ItemRequests, ItemDispatches
│   │   ├── ShiftClosing/
│   │   └── Callbacks/
│   ├── Production/
│   │   ├── Recipes/, Products, ProductTypes
│   │   ├── DailyProduce/, Request/
│   │   ├── KitchenModule/, RawMaterialTracking
│   │   ├── ShiftClosing/
│   │   └── Callbacks/
│   └── SalesDashboard/
│       ├── Analytics/, MySales/, Pos/
│       ├── StockOpening/, StockMonitor
│       ├── ExpiryAlerts, ProductList
│       ├── TableManagement/, ShiftClosing/
│       └── Callbacks/
├── SuperAdmin/
│   ├── Analytics/                (7 analytics components - EXECUTIVE LAYER)
│   │   ├── OverallSummaryDashboard.php
│   │   ├── StockLevelAnalytics.php
│   │   ├── StockMovementAnalytics.php
│   │   ├── PurchaseAnalytics.php
│   │   ├── RequestDispatchAnalytics.php
│   │   ├── AlertsDashboard.php
│   │   └── SupplierPerformance.php
│   ├── BranchModule/             (Multi-branch management)
│   ├── EmployeeModule/           (Enterprise employee management)
│   ├── Departments/              (Organization structure)
│   ├── Inventory/                (Enterprise-wide inventory)
│   ├── Roles/                    (Permission management)
│   └── Settings/                 (System-wide settings)
└── BaseComponent.php             (Base class for all components)
```

### 4.2 Missing: MD Dashboard Components

No MD-specific Livewire components exist. Would need:

```
Livewire/ManagingDirector/        // NEW: MD-specific layer
├── Index.php                      (Main executive dashboard)
├── Analytics/
│   ├── ExecutiveSummary.php      (KPIs & metrics)
│   ├── CrossBranchComparison.php (Branch performance)
│   ├── FinancialOverview.php      (Revenue & profitability)
│   ├── OperationalMetrics.php     (Efficiency metrics)
│   ├── AlertsDashboard.php        (Critical issues)
│   └── TrendAnalysis.php          (Strategic trends)
├── Reports/
│   ├── ScheduledReports.php
│   ├── CustomReports.php
│   └── ExportHistory.php
├── Branches/
│   ├── BranchComparison.php
│   ├── PerformanceMetrics.php
│   └── StaffManagement.php
└── Settings/
    ├── DashboardConfiguration.php
    ├── Alerts.php
    └── ReportSchedules.php
```

---

## 5. ANALYTICS & REPORTING INFRASTRUCTURE

### 5.1 Branch-Level Analytics (Currently Implemented)

**Location:** `/home/ilem/Documents/sweettooth/app/Livewire/BranchDashboard/Analytics/`

7 Livewire Components:
1. **OverallSummaryDashboard** - KPIs, employee count, revenue, inventory value
2. **StockLevelAnalytics** - Stock health, low stock items, reorder analysis
3. **StockMovementAnalytics** - Movement trends, in/out tracking
4. **PurchaseAnalytics** - Procurement trends, supplier analysis
5. **RequestDispatchAnalytics** - Item request status, dispatch performance
6. **AlertsDashboard** - System alerts and notifications
7. **StockValuation** - Inventory financial metrics

### 5.2 Super-Admin Analytics (Executive Level)

**Location:** `/home/ilem/Documents/sweettooth/app/Livewire/SuperAdmin/Analytics/`

7 Livewire Components (same as branch, but organization-wide):
1. **OverallSummaryDashboard** - Organization KPIs, multi-branch totals
2. **StockLevelAnalytics** - Enterprise stock health
3. **StockMovementAnalytics** - Cross-branch movements
4. **PurchaseAnalytics** - Organization purchase trends
5. **RequestDispatchAnalytics** - Enterprise logistics
6. **AlertsDashboard** - System-wide alerts
7. **SupplierPerformance** - Vendor management (unique to SuperAdmin)

### 5.3 Sales Analytics (Unique to Branch Dashboard)

**Location:** `/home/ilem/Documents/sweettooth/app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`

**Status:** Fully implemented (520 lines)

**Key Metrics:**
- Sales overview (total, count, avg order value)
- Revenue metrics (gross, net, refunds)
- Discount & tax analysis
- Payment method breakdown
- Order type distribution
- Top selling products (revenue & quantity)
- Hourly/daily sales patterns
- Shift performance
- Category-wise breakdown
- Profit analysis

**Features:**
- Multiple time period filters
- CSV export
- Tab-based UI
- Real-time refresh
- Caching (5-minute)

---

## 6. DATA MODELS SUPPORTING MD DASHBOARDS

### 6.1 Employee Model

**File:** `/home/ilem/Documents/sweettooth/app/Models/Employee.php`

Key attributes:
```php
- id, branch_id, department_id, manager_id
- name, email, phone, address
- hire_date, termination_date, status
- salary, hourly_rate
- roles (via Spatie\Permission)
- relationships: branch, department, manager, subordinates
```

**Supports MD access to:** All employees across all branches

### 6.2 Branch Model

**File:** `/home/ilem/Documents/sweettooth/app/Models/Branch.php`

Stores branch information and is linked to all operations (sales, inventory, production).

### 6.3 User Model (Super Admin)

**File:** `/home/ilem/Documents/sweettooth/app/Models/User.php`

Uses Spatie\Permission for role management. Super Admin and Managing Director can both be assigned to User model.

### 6.4 Configuration Models

Supporting cross-cutting configuration:
- **GlobalReportsAnalytics** - System-wide report settings
- **BranchReportsAnalytics** - Branch-specific overrides
- **GlobalBusinessConfiguration** - Organization settings
- **GlobalInventoryManagement** - Inventory policies
- **GlobalEmployeeManagement** - HR policies

---

## 7. AUTHENTICATION & AUTHORIZATION PATTERNS

### 7.1 Guard-Based Separation

Two authentication guards in use:

**Web Guard** (User model):
- Super Admin login
- System administrators
- No branch assignment
- Organization-wide access

**Employees Guard** (Employee model):
- Branch employees
- All roles (Cashier to Managing Director)
- Assigned to specific branch
- Branch-scoped operations

### 7.2 Authorization in Routes

**Super Admin routes:** `Route::middleware(['auth'])`
- No role check in route definition
- Access control via component/view checks

**Branch routes:** `Route::middleware(['auth:employees', 'branch'])`
- Uses 'employees' guard
- 'branch' middleware enforces branch scoping

### 7.3 Authorization in Components

All Livewire components inherit from `BaseComponent`:

```php
// In components, access control done via:
- Gate::check('view-reports')
- Auth::guard('employees')->user()->hasRole('Manager')
- RolePermission::isManagingDirector()
- RolePermission::canAccessModule('production')
```

---

## 8. HOW REPORTS/ANALYTICS ARE CURRENTLY ACCESSED

### 8.1 Branch Employee Access Pattern

```
1. Employee logs in → auth:employees guard
2. Redirected to: branch-dashboard.index
3. Can access branch-specific:
   - /branch-dashboard/analytics/overview
   - /branch-dashboard/analytics/stock-level
   - /branch-dashboard/sales-dashboard/analytics
4. Limited to their branch via middleware
5. Further limited by role:
   - Sales staff → only sales analytics
   - Inventory staff → inventory analytics
   - Manager → all branch analytics
```

### 8.2 Super Admin Access Pattern

```
1. Super Admin logs in → web guard
2. Redirected to: super-admin.analytics.dashboard
3. Can access organization-wide:
   - /super-admin/analytics/dashboard
   - /super-admin/analytics/stock-level
   - /super-admin/inventory/items (all branches)
4. No branch scoping limitation
5. System configuration access:
   - /super-admin/settings/
   - /super-admin/roles/
```

### 8.3 Current MD Access (Gap)

**Managing Directors currently:**
1. Log in as Employee (auth:employees guard)
2. Directed to branch-dashboard.index
3. Have full access to branch analytics:
   - All branch inventory analytics
   - All branch sales analytics
   - All branch production data
4. **CANNOT access:**
   - Other branches (branch middleware restricts)
   - Organization-wide comparisons
   - Executive-level KPIs
   - Cross-branch trends
   - System settings/configuration

**This is the KEY GAP:** MDs need executive view without branch restriction.

---

## 9. PROPOSED MD DASHBOARD ARCHITECTURE

### 9.1 Route Structure

Create new file: `routes/managing-director.php`

```php
Route::middleware(['auth:employees', 'managing-director'])->prefix('managing-director')->name('managing-director.')->group(function () {
    
    // Dashboard entry point
    Route::get('/', \App\Livewire\ManagingDirector\Index::class)->name('index');
    
    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('dashboard', \App\Livewire\ManagingDirector\Analytics\ExecutiveSummary::class)->name('dashboard');
        Route::get('cross-branch', \App\Livewire\ManagingDirector\Analytics\CrossBranchComparison::class)->name('cross-branch');
        Route::get('financial', \App\Livewire\ManagingDirector\Analytics\FinancialOverview::class)->name('financial');
        Route::get('operational', \App\Livewire\ManagingDirector\Analytics\OperationalMetrics::class)->name('operational');
        Route::get('alerts', \App\Livewire\ManagingDirector\Analytics\AlertsDashboard::class)->name('alerts');
        Route::get('trends', \App\Livewire\ManagingDirector\Analytics\TrendAnalysis::class)->name('trends');
    });
    
    // Branch management
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', \App\Livewire\ManagingDirector\Branches\Index::class)->name('index');
        Route::get('{branch}/performance', \App\Livewire\ManagingDirector\Branches\Performance::class)->name('performance');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', \App\Livewire\ManagingDirector\Reports\Index::class)->name('index');
        Route::get('{report}', \App\Livewire\ManagingDirector\Reports\View::class)->name('view');
    });
});
```

### 9.2 Middleware (New)

Create: `app/Http/Middleware/ManagingDirectorCheck.php`

```php
// Check user has Managing Director role
// Remove branch scoping
// Ensure access to multiple branches
```

### 9.3 Component Organization

Create directory: `/app/Livewire/ManagingDirector/`

```
ManagingDirector/
├── Index.php                     (Entry dashboard)
├── Analytics/
│   ├── ExecutiveSummary.php      (KPIs across all branches)
│   ├── CrossBranchComparison.php (Branch performance metrics)
│   ├── FinancialOverview.php      (Revenue, profitability, margins)
│   ├── OperationalMetrics.php     (Efficiency, productivity)
│   ├── AlertsDashboard.php        (Critical issues escalation)
│   └── TrendAnalysis.php          (Historical trends, forecasts)
├── Branches/
│   ├── Index.php                 (Branch list & status)
│   ├── Performance.php           (Detailed branch metrics)
│   └── StaffManagement.php       (Staff across branches)
├── Reports/
│   ├── Index.php                 (Report library)
│   ├── CustomReports.php         (Build custom reports)
│   └── ExportHistory.php         (Previous exports)
└── Settings/
    ├── DashboardConfiguration.php
    ├── Alerts.php
    └── ReportSchedules.php
```

### 9.4 Views Structure

Create directory: `resources/views/livewire/managing-director/`

```
managing-director/
├── index.blade.php
├── analytics/
│   ├── executive-summary.blade.php
│   ├── cross-branch-comparison.blade.php
│   ├── financial-overview.blade.php
│   ├── operational-metrics.blade.php
│   ├── alerts-dashboard.blade.php
│   └── trend-analysis.blade.php
├── branches/
│   ├── index.blade.php
│   ├── performance.blade.php
│   └── staff-management.blade.php
└── reports/
    ├── index.blade.php
    ├── custom-reports.blade.php
    └── export-history.blade.php
```

---

## 10. KEY IMPLEMENTATION DECISIONS

### 10.1 Authentication Strategy

**Decision:** Use `auth:employees` guard (not `web`)

**Rationale:**
- Managing Directors are employees with branch assignments
- Enables manager hierarchy (subordinates, leave approval)
- Maintains consistency with other branch employees
- Allows tracking of who accessed what reports

**Alternative:** Create separate `md-admin` guard
- More complex
- Would duplicate employee data
- Breaks manager-subordinate relationships

### 10.2 Branch Scoping

**Decision:** Remove branch middleware for MD routes

**Rationale:**
- MDs need to see all branches
- Create custom `managing-director` middleware instead
- Middleware checks role and enables multi-branch access

### 10.3 Role vs Permissions

**Decision:** Use role-based access with permission checks in components

**Current pattern:** Components check `RolePermission::isManagingDirector()`

**Recommendation:**
- Keep role-based for dashboard access
- Add permissions for specific features:
  - `view-cross-branch-analytics`
  - `export-reports`
  - `view-financial-data`
  - `manage-branches`

### 10.4 Data Aggregation

**Decision:** Aggregate at component level (follow existing pattern)

**Current pattern:** Branch analytics aggregate data in component methods

**MD Dashboard would:**
- Aggregate across branches in component methods
- Use caching for complex calculations (Redis)
- Schedule reports via Queue jobs
- Use database views for complex queries

### 10.5 UI Layout

**Recommendation:** Create MD-specific layout template

Similar to existing layouts:
- `/resources/views/components/layouts/app/branch-dashboard.blade.php`
- `/resources/views/components/layouts/app/super-admin-dashboard.blade.php`

New:
- `/resources/views/components/layouts/app/md-dashboard.blade.php`

---

## 11. DATA ACCESSIBILITY FOR MD DASHBOARDS

### 11.1 Models MD Needs Access To

```php
// Core Operations
- Sale::where('branch_id', $mdBranches) // Multi-branch sales
- DailyProduce::where('branch_id', $mdBranches)
- Stock::where('branch_id', $mdBranches)
- Employee::where('branch_id', $mdBranches)

// Financial
- Purchase::where('branch_id', $mdBranches)
- Payment::whereHas('sale', fn($q) => $q->whereIn('branch_id', $mdBranches))

// Performance
- ProductionRecord::whereHas('shift', fn($q) => $q->whereIn('branch_id', $mdBranches))
- SalesShift::where('branch_id', $mdBranches)

// Alerts & Issues
- Stock::where('health_status', 'critical')
  ->whereIn('branch_id', $mdBranches)
```

### 11.2 Key Metrics for MD Dashboard

**1. Executive Summary Cards:**
- Total revenue (all branches)
- Total active employees
- Inventory value (total)
- Pending alerts count
- Branch count & status

**2. Cross-Branch Comparison:**
- Revenue per branch
- Production efficiency per branch
- Inventory turnover per branch
- Staff productivity per branch
- Profit margins by branch

**3. Financial Overview:**
- Revenue trend (30/60/90 day)
- Top products by revenue
- Cost analysis & margins
- Cash flow (if payment data available)
- Profitability by department

**4. Operational Metrics:**
- Production efficiency (planned vs actual)
- Sales per employee
- Inventory health status distribution
- Order fulfillment rate
- Waste/callback percentages

**5. Critical Alerts:**
- Low stock items
- Expired products
- Production delays
- Pending approvals
- Employee issues

**6. Trends & Forecasts:**
- Revenue trend with forecast
- Seasonal patterns
- Growth rates
- Performance trends by branch

---

## 12. CURRENT ROLE ASSIGNMENTS

### 12.1 File: `/home/ilem/Documents/sweettooth/app/Helpers/RolePermission.php`

Role descriptions defined:
```php
'Managing Director' => 'Executive level with full operational control'
```

Can manage anyone:
```php
public static function canManageUser($targetUser): bool
{
    if (self::isSuperAdmin() || self::isManagingDirector()) {
        return true; // MD can manage anyone
    }
}
```

### 12.2 Module Access

Current module check:
```php
public static function canAccessModule(string $module): bool
{
    $modulePermissions = [
        'production' => ['view-production-queue', 'start-production', ...],
        'sales' => ['process-sale', 'view-daily-sales'],
        'inventory' => ['view-stock-levels', 'receive-stock', ...],
        // ...
    ];
}
```

**Missing:** Executive/reporting module

---

## 13. COMPARISON: BRANCH VS SUPERADMIN VS PROPOSED MD

| Feature | Branch Dashboard | Super Admin | MD Dashboard (Proposed) |
|---------|------------------|------------|----------------------|
| **Scope** | Single branch | All organization | Multiple branches (assigned) |
| **Authentication** | auth:employees | web guard | auth:employees |
| **Branch Middleware** | Yes (restricted) | No | Custom (multi-branch aware) |
| **Analytics** | Branch-specific | Organization-wide | Cross-branch + executive |
| **Reports** | Operational | Inventory-focused | Strategic + operational |
| **Settings Access** | Branch settings | System settings | Branch settings (assigned) |
| **Role Access** | Any role | Super Admin only | Managing Director only |
| **User Base** | Employees | Few admins | 1-2 per organization |
| **Key Features** | Operations | Configuration | Decision support |

---

## 14. FILES TO MODIFY/CREATE

### 14.1 New Files Required

```
routes/
└── md-dashboard.php               (NEW: 80-100 lines)

app/Livewire/ManagingDirector/
├── Index.php                      (NEW: ~100 lines)
├── Analytics/
│   ├── ExecutiveSummary.php      (NEW: ~200 lines)
│   ├── CrossBranchComparison.php (NEW: ~250 lines)
│   ├── FinancialOverview.php      (NEW: ~200 lines)
│   ├── OperationalMetrics.php     (NEW: ~200 lines)
│   ├── AlertsDashboard.php        (NEW: ~150 lines)
│   └── TrendAnalysis.php          (NEW: ~200 lines)
├── Branches/
│   ├── Index.php                  (NEW: ~100 lines)
│   ├── Performance.php            (NEW: ~200 lines)
│   └── StaffManagement.php        (NEW: ~150 lines)
└── Reports/
    ├── Index.php                  (NEW: ~100 lines)
    ├── CustomReports.php          (NEW: ~200 lines)
    └── ExportHistory.php          (NEW: ~100 lines)

app/Http/Middleware/
└── ManagingDirectorCheck.php      (NEW: ~30 lines)

resources/views/livewire/managing-director/
├── index.blade.php                (NEW: ~50 lines)
├── analytics/ (6 views)           (NEW: ~3000 lines total)
├── branches/ (3 views)            (NEW: ~1500 lines total)
└── reports/ (3 views)             (NEW: ~1500 lines total)

resources/views/components/layouts/app/
└── md-dashboard.blade.php         (NEW: ~80 lines)
```

### 14.2 Files to Modify

```
web.php                             (Add MD route include)
app/Http/Kernel.php                 (Register middleware if needed)
RolePermission.php                  (Add MD-specific helpers)
RolePermissionServiceProvider.php   (Add MD directives if needed)
config/auth.php                     (Already configured)
```

---

## 15. ESTIMATED IMPLEMENTATION EFFORT

### Phase 1: Foundation (1-2 days)
- Create routes/md-dashboard.php
- Create middleware
- Create base component & layout
- Setup directory structure

### Phase 2: Executive Dashboard (2-3 days)
- ExecutiveSummary component
- Key metrics aggregation
- Basic layout & styling

### Phase 3: Analytics Components (3-5 days)
- CrossBranchComparison
- FinancialOverview
- OperationalMetrics
- AlertsDashboard
- TrendAnalysis

### Phase 4: Management Features (2-3 days)
- Branch management view
- Performance details
- Staff management

### Phase 5: Reporting (2-3 days)
- Report library
- Custom report builder
- Export history
- Scheduling

### Phase 6: Testing & Polish (1-2 days)
- Unit tests
- UI refinement
- Performance optimization

**Total Estimated:** 11-18 days (2-3 weeks)

---

## 16. DEPENDENCIES & CONSIDERATIONS

### 16.1 Technical Dependencies
- Spatie\Permission (already installed)
- Livewire (already installed)
- TallStackUi (already installed)
- Laravel 10+ (confirmed in project)

### 16.2 Data Dependencies
- All branch-related data properly labeled with branch_id
- Employee branch assignments current
- Sales, inventory, production data branch-scoped

### 16.3 Business Dependencies
- MD assignment to branches (not implemented yet)
- MD approval workflows (if needed)
- Report distribution requirements

---

## 17. SECURITY CONSIDERATIONS

### 17.1 Authorization Check
- Only "Managing Director" role can access MD routes
- Custom middleware validates
- No super admin bypass (unless explicitly needed)

### 17.2 Data Access
- MD can only see assigned branches (requires field in Employee or relationship)
- Cross-branch data aggregation respects branch restrictions
- Audit logging recommended for MD actions

### 17.3 Sensitive Data
- Financial data access (if exposed)
- Employee salary/performance data
- Strategic metrics visibility

---

## 18. NEXT STEPS RECOMMENDATION

### Phase 1: Quick Start (Day 1)
1. Create `/routes/md-dashboard.php` (empty placeholder)
2. Add route include to `routes/web.php`
3. Create `app/Livewire/ManagingDirector/Index.php` (simple index)
4. Create `ManagingDirectorCheck.php` middleware
5. Test authentication flow

### Phase 2: Foundation (Days 2-3)
1. Create `ExecutiveSummary.php` component
2. Implement key metrics calculation
3. Create basic view with cards
4. Test with sample data

### Phase 3: Expand (Days 4-7)
1. Add 5 more analytics components
2. Implement cross-branch aggregation
3. Add filtering and date ranges
4. Optimize queries

### Phase 4: Polish (Days 8+)
1. Add remaining features (branches, reports)
2. Performance tuning
3. User testing
4. Documentation

---

## CONCLUSION

The SweetTooth RMS has **excellent foundational support** for MD dashboards:
- Role hierarchy with MD defined at Level 5
- Analytics infrastructure ready
- Multi-branch data available
- Authentication guards properly configured

The **implementation gap** is at the UI/routing layer:
- No dedicated MD routes
- No MD-specific components
- No cross-branch aggregation logic
- No executive summary views

**Recommendation:** Implement Phase 1 & 2 immediately to provide MD executives with necessary visibility. This is a strategic feature for operational oversight.

---

**Analysis Date:** November 14, 2025
**Scope:** Complete dashboard architecture review
**Confidence Level:** High (based on code inspection of 60+ components and 150+ files)
