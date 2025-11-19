# MD Dashboard - Quick Reference Guide

## Current State Summary

### What Exists
- Managing Director role defined (Level 5, same as Super Admin)
- Role permission helper with MD checks
- Two authentication guards (web, employees)
- Three-tier dashboard hierarchy
- Comprehensive analytics infrastructure

### What's Missing
- NO dedicated MD dashboard routes
- NO MD-specific Livewire components
- NO cross-branch visibility for MD users
- NO executive-level analytics

## File Locations Reference

### Authentication & Roles
- Config: `/config/auth.php` (2 guards: web, employees)
- Helper: `/app/Helpers/RolePermission.php` (MD checks available)
- Provider: `/app/Providers/RolePermissionServiceProvider.php` (Blade directives)

### Existing Dashboards
- Super Admin: `/routes/super-admin.php` (7 analytics components)
- Branch: `/routes/branch-route.php` (152 lines, branch-scoped)
- Components: `/app/Livewire/BranchDashboard/` and `/app/Livewire/SuperAdmin/`

### Models
- Employee: `/app/Models/Employee.php` (branch_id, roles, relationships)
- Branch: `/app/Models/Branch.php`
- Sales, Production, Inventory: All branch-scoped

## Key Numbers

| Item | Count |
|------|-------|
| Livewire Components (total) | 80+ |
| Branch Analytics | 7 |
| Super Admin Analytics | 7 |
| Sales Analytics | 1 (comprehensive) |
| Role Levels | 5 |
| Employee Roles | 16 |
| Authentication Guards | 2 |
| Dashboard Tiers | 3 (should be 4) |

## Critical Gap

**MD users currently:**
1. Log in via `auth:employees` guard
2. Routed to `branch-dashboard.index`
3. Restricted to single branch
4. Have no cross-branch visibility

**MD users should:**
1. Have dedicated `/managing-director/` routes
2. Access all assigned branches
3. See executive-level KPIs
4. View cross-branch analytics
5. Access strategic reports

## Quick Implementation Checklist

Phase 1: Foundation (1-2 days)
- [ ] Create `/routes/md-dashboard.php`
- [ ] Create `/app/Livewire/ManagingDirector/Index.php`
- [ ] Create middleware: `ManagingDirectorCheck.php`
- [ ] Add route include to `web.php`

Phase 2: Executive Summary (2-3 days)
- [ ] `ExecutiveSummary.php` component
- [ ] Key metrics calculation logic
- [ ] Basic view layout
- [ ] Test authentication

Phase 3: Analytics (3-5 days)
- [ ] `CrossBranchComparison.php`
- [ ] `FinancialOverview.php`
- [ ] `OperationalMetrics.php`
- [ ] `AlertsDashboard.php`
- [ ] `TrendAnalysis.php`

Phase 4+: Management & Reports (5+ days)
- [ ] Branch management views
- [ ] Report library
- [ ] Custom reports
- [ ] Export functionality

## Existing Patterns to Follow

### Component Structure
```php
class MyComponent extends Component {
    #[Layout('components.layouts.app.branch-dashboard')]
    public function render() {
        // Component logic
    }
}
```

### Analytics Pattern
- Date filtering (today, week, month, custom)
- Computed properties for reactive updates
- Caching (5-minute cache for performance)
- Export functionality

### Authorization Pattern
```blade
@role('Managing Director')
    <!-- Content -->
@endrole

@rolelevel(5) <!-- Level 5 = Executive -->
    <!-- Content -->
@endrolelevel
```

## Data Models Available

**Multi-branch visible:**
- Sale (with branch_id)
- DailyProduce (with branch_id)
- Stock (with branch_id)
- Employee (with branch_id)
- Purchase (with branch_id)
- ProductionRecord (via shift->branch_id)
- SalesShift (with branch_id)

## Database Indexes Recommended

For MD Dashboard performance:
```sql
CREATE INDEX idx_sale_time_branch ON sales(sale_time, branch_id);
CREATE INDEX idx_stock_health_branch ON stocks(health_status, branch_id);
CREATE INDEX idx_daily_produce_branch ON daily_produces(branch_id, produce_date);
CREATE INDEX idx_production_record_branch ON production_records(branch_id);
```

## Configuration Already Done

You DON'T need to modify:
- Authentication guards (already set up)
- Role hierarchy (already defined)
- Permission system (Spatie\Permission installed)
- Database schema (branch_id present everywhere)
- Analytics components (can reuse)

## What You DO Need to Create

1. Routes file (new)
2. Middleware (new, simple)
3. Livewire components (10-15 new)
4. Blade views (10-15 new)
5. Layout template (new, based on existing)

## Code Example: MD Route Structure

```php
// routes/md-dashboard.php
Route::middleware(['auth:employees', 'managing-director'])
    ->prefix('managing-director')
    ->name('managing-director.')
    ->group(function () {
        
    Route::get('/', Index::class)->name('index');
    
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('dashboard', ExecutiveSummary::class)->name('dashboard');
        Route::get('cross-branch', CrossBranchComparison::class)->name('cross-branch');
        Route::get('financial', FinancialOverview::class)->name('financial');
    });
});
```

## Estimated Implementation Time

- **Phase 1 (Foundation):** 1-2 days
- **Phase 2 (Executive Dashboard):** 2-3 days
- **Phase 3 (Analytics):** 3-5 days
- **Phase 4 (Management):** 2-3 days
- **Phase 5 (Reports):** 2-3 days
- **Phase 6 (Testing):** 1-2 days

**Total: 11-18 days (2-3 weeks)**

## Contact Points in Code

### For Authentication Logic
- File: `/config/auth.php`
- File: `/app/Http/Middleware/Authenticate.php` (if exists)

### For Role Checks
- File: `/app/Helpers/RolePermission.php`
- Method: `isManagingDirector()`
- Method: `hasRoleLevel(5)`

### For Analytics Patterns
- File: `/app/Livewire/BranchDashboard/Analytics/OverallSummaryDashboard.php`
- File: `/app/Livewire/SuperAdmin/Analytics/OverallSummaryDashboard.php`

### For Layout Templates
- File: `/resources/views/components/layouts/app/branch-dashboard.blade.php`
- File: `/resources/views/components/layouts/app/super-admin-dashboard.blade.php`

---

**Document Date:** November 14, 2025
**Full Analysis:** See `MD_DASHBOARD_ANALYSIS.md`
**Project Status:** Ready for MD Dashboard implementation

  system:

  📊 COMPREHENSIVE REPORTING SYSTEM ARCHITECTURE

  System Overview

  Authentication Structure:
  - SuperAdmin/MD Section: auth() guard → users table → routes/super-admin.php
  - Branch Dashboard: auth('employees') guard → employees table → routes/branch-route.php

  Reporting Flow:
  Department Reports (Branch Dashboard - Employees)
      ↓
  Reporting Department Compilation (Branch Dashboard - Reporting role)
      ↓
  MD Dashboard (SuperAdmin - Users table)

  Phase 1: Database Infrastructure (First Priority)

  I'll create tables for:
  1. department_reports - Store individual department reports
  2. compiled_reports - Store compiled reports for MD
  3. report_schedules - Automated report generation schedules
  4. report_distributions - Who receives which reports
  5. report_templates - Customizable report templates

  Phase 2: Department-Based Reporting (Branch Dashboard)

  A. Production Department Reports (9 Critical Reports - 0% Complete)

  1. Production Efficiency Report (Planned vs Actual)
  2. Quality Metrics Report (Rejection rates, reasons)
  3. Waste Analysis Report (Raw material waste, costs)
  4. Production Cost Analysis (Cost per unit, variances)
  5. Recipe Performance Report (Most produced, most rejected)
  6. Shift Summary Report (Production per shift/employee)
  7. Ingredient Utilization Report (Usage trends, costs)
  8. Production-to-Sales Pipeline (Dispatch status)
  9. Capacity Planning Report (Utilization rates)

  B. Sales Department Reports (7 Missing Reports)

  1. Employee Performance Report (Sales per employee)
  2. Product Profitability Report (Margin by product)
  3. Customer Analysis Report (Repeat customers, LTV)
  4. Table Analytics Report (Occupancy, sitting time)
  5. Discount Analysis Report (Discount frequency, impact)
  6. Payment Method Performance (Trends by method)
  7. Day-part Analysis (Breakfast, lunch, dinner performance)

  C. Inventory Department Reports (7 Missing Reports)

  1. Supplier Performance Report (On-time delivery, quality)
  2. Stock Aging Report (Dormant items, age analysis)
  3. Reorder Point Analysis (Items at reorder level)
  4. Stock Variance Report (Physical vs system count)
  5. Cost Analysis Report (Cost trends, damaged goods impact)
  6. FIFO/Expiry Report (Expired stock value, trends)
  7. Category Performance Report (Performance by category)

  Phase 3: Reporting Department Section (Branch Dashboard)

  Location: app/Livewire/BranchDashboard/ReportingDepartment/

  Components:
  - CompileReports/Index.php - Compile reports from all departments
  - ReviewReports/Index.php - Review individual department reports
  - SendToMD/Index.php - Send compiled reports to MD dashboard
  - ReportScheduler/Index.php - Schedule automated reports
  - ReportTemplates/Index.php - Manage report templates

  Phase 4: MD Dashboard (SuperAdmin)

  Location: app/Livewire/SuperAdmin/MDReports/

  Components:
  - Dashboard.php - Main MD reports dashboard
  - ProductionReports.php - All production reports across branches
  - SalesReports.php - All sales reports across branches
  - InventoryReports.php - All inventory reports across branches
  - ComparativeAnalysis.php - Compare branches performance
  - ExecutiveSummary.php - High-level KPIs and insights

  Phase 5: Supporting Infrastructure

  1. Report Services:
    - app/Services/ReportService.php (Abstract base)
    - app/Services/ProductionReportService.php
    - app/Services/SalesReportService.php
    - app/Services/InventoryReportService.php
    - app/Services/ReportCompilationService.php
  2. Export Functionality:
    - PDF Export (using barryvdh/laravel-dompdf)
    - Excel Export (using maatwebsite/excel)
    - CSV Export (built-in)
  3. Automated Scheduling:
    - Daily reports (auto-generated at end of day)
    - Weekly summaries (Sundays)
    - Monthly comprehensive reports

  ---
  🚀 Implementation Plan

  Would you like me to start implementing this comprehensive reporting system? I'll begin with:

  1. Database migrations for the reporting infrastructure
  2. Base service classes for report generation
  3. Production department reports (highest priority - 0% complete)
  4. Reporting Department compilation section
  5. MD Dashboard for viewing all reports

  This will create a complete end-to-end reporting system where:
  - Departments generate their reports
  - Reporting department compiles and reviews them
  - MD/SuperAdmin views comprehensive insights from all branches

  Shall I proceed with the implementation?
