# Inventory Analytics - Complete Audit & Implementation Guide

## Executive Summary
Comprehensive audit of all Branch Dashboard analytics components. Identified inconsistencies, relationship issues, filter problems, chart implementation gaps, and upgrade opportunities.

---

## Part 1: Analytics Components Overview

### 1. **StockMovementAnalytics** ✓
**File:** `app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php`
**View:** `resources/views/livewire/branch-dashboard/analytics/stock-movement-analytics.blade.php`

**Status:** ✓ Fully functional (recently fixed)

**Features:**
- Summary cards (Today's in/out/transfers/latest movement)
- Period analysis (Total movements, stock in/out, net growth, adjustments, damaged)
- Top moved items
- Most active user
- Peak activity hours
- Daily breakdown (last 7 days)
- Velocity analysis
- Activity feed (latest 20 movements)
- CSV export

**Filters:**
- ✓ Date range (from/to)
- ✓ Movement type
- ✓ Item selection (searchable)
- ✓ Search term (name, SKU, person, notes)
- ✓ Shift filter (via ItemRequest reference)
- ✓ Department filter (via ItemRequest reference)

**Issues Found:** NONE - All filters working correctly

---

### 2. **PurchaseAnalytics** ✓✓
**File:** `app/Livewire/BranchDashboard/Analytics/PurchaseAnalytics.php`
**View:** `resources/views/livewire/branch-dashboard/analytics/purchase-analytics.blade.php`

**Status:** ✓ Charts implemented with proper updates

**Charts:**
- ✓ Purchase Trend (Highcharts line/spline)
- ✓ Top Suppliers by Spending (Highcharts bar)
- ✓ Cost Breakdown (Highcharts pie)
- ✓ Top 10 Purchased Items (Highcharts bar)

**Features:**
- Summary cards (Total purchases, total spent, avg value, total items)
- Purchases table (paginated)
- Charts update on filter change via `chartsUpdated` event

**Filters:**
- ✓ Supplier filter (text search)
- ✓ Payment status (paid/partial/pending)
- ✓ Date range (from/to)

**Issues Found:** 
- ⚠️ Charts use Highcharts CDN (externally hosted)
- ⚠️ No loading state handling during chart updates
- ⚠️ Chart data transformation could be optimized

**Chart Updates:** ✓ Proper event dispatch when filters change

---

### 3. **StockLevelAnalytics**
**File:** `app/Livewire/BranchDashboard/Analytics/StockLevelAnalytics.php`
**View:** `resources/views/livewire/branch-dashboard/analytics/stock-level-analytics.blade.php`

**Status:** ✓ Charts implemented

**Charts:**
- ✓ Stock Level Trend (Highcharts line)
- ✓ Health Status Distribution (Highcharts pie)
- ✓ Stock Turnover Analysis (Highcharts bar)
- ✓ Category Distribution (Highcharts pie)

**Filters:**
- ✓ Date range (from/to)
- ✓ Category selection
- ✓ Health status filter
- ✓ Item search
- ✓ Item selection

**Issues Found:** NONE - Well implemented

---

### 4. **RequestDispatchAnalytics**
**File:** `app/Livewire/BranchDashboard/Analytics/RequestDispatchAnalytics.php`
**View:** `resources/views/livewire/branch-dashboard/analytics/request-dispatch-analytics.blade.php`

**Status:** ✓ Charts implemented

**Charts:**
- ✓ Request Trend (Highcharts spline/line)
- ✓ Department Analysis (Highcharts column/bar)
- ✓ Fulfillment Rate (Highcharts pie/donut)

**Filters:**
- ✓ Date range (from/to)
- ✓ Status filter
- ✓ Department filter
- ✓ Shift filter
- ✓ Search term

**Issues Found:** NONE - Well implemented

---

### 5. **StockValuation**
**File:** `app/Livewire/BranchDashboard/Analytics/StockValuation.php`
**View:** `resources/views/livewire/branch-dashboard/analytics/stock-valuation.blade.php`

**Status:** ✓ Charts implemented

**Charts:**
- ✓ Category Valuation (Highcharts pie/column)
- ✓ Top 10 Most Valuable Items (Highcharts bar)

**Features:**
- Valuation summary cards
- Category breakdown
- Top items by value

**Issues Found:** NONE - Well implemented

---

## Part 2: Missing Charts & Implementation Gaps

### 🔴 StockMovementAnalytics - MISSING CHARTS

**Current State:** Text-based analytics only (no visualizations)

**Recommended Charts to Add:**

#### 1. **Movement Type Distribution** (Pie/Donut Chart)
- Shows breakdown of movement types: in, out, adjustment, transfer, damaged, return
- Implementation: Use typeDistribution data already being calculated
- Chart Type: Highcharts Pie/Donut
- Location: Below period summary section

```php
// Data already available in component
$typeDistribution = StockMovement::whereHas('stock', ...)
    ->selectRaw('type, COUNT(*) as count')
    ->groupBy('type')
    ->get();
```

#### 2. **Daily Movement Trend** (Line/Area Chart)
- Shows daily stock in/out trends over selected period
- Implementation: Use getDailyBreakdown() method
- Chart Type: Highcharts spline/area
- Location: First chart row

#### 3. **Top Moved Items** (Horizontal Bar Chart)
- Shows top 10 items by movement count
- Implementation: Use topMovedItems data
- Chart Type: Highcharts bar chart
- Location: Second chart row (left)

#### 4. **Peak Activity Timeline** (Column Chart)
- Shows activity by hour of day
- Implementation: Use peak_hours data
- Chart Type: Highcharts column chart
- Location: Second chart row (right)

---

## Part 3: Filter Issues & Inconsistencies

### ✓ CRITICAL: Shift & Department Filters (StockMovementAnalytics)

**Issue:** Filters only work if movement's reference is ItemRequest

**Current Code:**
```php
->when($this->filterShift, fn ($q) => 
    $q->whereHasMorph('reference', ['App\Models\ItemRequest'], 
        fn ($sq) => $sq->where('shift', $this->filterShift)
    )
)
```

**Problem:**
- Doesn't filter movements from Purchases (reference_type = 'App\Models\Purchase')
- Doesn't filter manual adjustments (reference_type = null)
- Only works for ItemRequest-related movements

**Solution Options:**

#### Option A: Add Purchase shift info
```php
// If Purchase has shift info, expand filter
->when($this->filterShift, function($q) {
    $q->where(function($sq) {
        $sq->whereHasMorph('reference', ['App\Models\ItemRequest'], 
            fn($ssq) => $ssq->where('shift', $this->filterShift)
        )
        ->orWhereHasMorph('reference', ['App\Models\Purchase'], 
            fn($ssq) => $ssq->where('shift', $this->filterShift)
        );
    });
})
```

#### Option B: Add shift to StockMovement directly
```php
// Migration: Add shift column to stock_movements
// Denormalize shift info when movement created
StockMovement::create([
    'shift' => $reference->shift ?? null,
    // ... other fields
]);
```

**Recommendation:** Use Option B (denormalization) for:
- Better query performance
- Easier filtering across all movement types
- More flexible reporting

---

## Part 4: Model & Relationship Issues

### Issue 1: StockMovement → Reference (Polymorphic)

**Current Relationships:**
```php
// Works with:
- App\Models\Purchase
- App\Models\ItemRequest (via shift/department filters)

// Missing data tracking:
- ItemDispatch
- Transfer
- StockTake
```

**Solution:** Update StockMovement creation to track:
```php
StockMovement::create([
    'stock_id' => $stock->id,
    'type' => 'out',
    'reference_type' => 'App\Models\ItemDispatch',  // Track dispatch
    'reference_id' => $dispatch->id,
    // ...
]);
```

### Issue 2: Query Efficiency (Multiple Date Parsing)

**Current Code Problem:**
```php
private function getAnalyticsSummary($branchId)
{
    $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();  // Parsed 4+ times
    $dateTo = Carbon::parse($this->dateTo)->endOfDay();
    
    // Used in: baseQuery, todayQuery, previousPeriodQuery, etc.
}
```

**Solution:** Parse once and pass as parameters:
```php
private function getAnalyticsSummary($branchId, $dateFrom = null, $dateTo = null)
{
    $dateFrom ??= Carbon::parse($this->dateFrom)->startOfDay();
    $dateTo ??= Carbon::parse($this->dateTo)->endOfDay();
    
    // Single instance used everywhere
}
```

---

## Part 5: Recommended Upgrades & Enhancements

### Priority 1 (High Impact)

#### 1. **Add Charts to StockMovementAnalytics**
- Effort: 2-3 hours
- Impact: Visual data analysis
- Implementation: See chart recommendations above

#### 2. **Fix Shift/Department Filters**
- Effort: 1-2 hours
- Impact: Accurate filtering across all movement types
- Implementation: Add shift denormalization to StockMovement

#### 3. **Real-time Chart Updates**
- Effort: 1-2 hours
- Impact: Responsive UX
- Implementation: Add Livewire event listeners to chart components

### Priority 2 (Medium Impact)

#### 4. **Comparisons Dashboard**
- Compare current period vs previous period
- Week-on-week, Month-on-month trends
- Effort: 2-3 hours

#### 5. **Export Enhancements**
- PDF export with charts
- Excel export with multiple sheets
- Effort: 2-3 hours

#### 6. **Drill-down Analysis**
- Click on chart data to see details
- Dynamic filtering from charts
- Effort: 3-4 hours

### Priority 3 (Nice to Have)

#### 7. **Alerts & Thresholds**
- Low stock alerts
- Unusual movement patterns
- Effort: 4-5 hours

#### 8. **Predictive Analytics**
- Forecast stock levels
- Predict stock-outs
- Effort: 5-6 hours

---

## Part 6: Chart Implementation Best Practices

### Standard Chart Data Structure
```php
[
    'labels' => ['Jan', 'Feb', 'Mar'],
    'series' => [
        ['name' => 'Stock In', 'data' => [100, 120, 110]],
        ['name' => 'Stock Out', 'data' => [80, 90, 85]]
    ]
]
```

### Chart Update Pattern (Using Livewire)
```php
// In Component:
public function updatedDateFrom()
{
    $this->dispatch('chartsUpdated', [
        'trend' => $this->getTrendData(),
        'distribution' => $this->getDistribution()
    ]);
}

// In Blade:
<script>
document.addEventListener('livewire:updated', () => {
    Livewire.on('chartsUpdated', (data) => {
        // Update charts with new data
        chart1.setOptions({ series: data.trend });
        chart2.setOptions({ series: data.distribution });
    });
});
</script>
```

### Loading States for Charts
```php
<!-- Blade -->
<div wire:loading.class="opacity-50">
    <div id="myChart"></div>
</div>

<!-- Alternative: Spinner -->
<div wire:loading>
    <div class="spinner">Loading...</div>
</div>
<div wire:loading.remove id="myChart"></div>
```

---

## Part 7: Filter Consistency Checklist

### All Analytics Should Have:
- [ ] Date range filter (from/to)
- [ ] Search/text filter (if applicable)
- [ ] Category/type filter (if applicable)
- [ ] Status filter (if applicable)
- [ ] Filter persist in URL (queryString)
- [ ] Reset filters button
- [ ] Filter validation (from ≤ to)
- [ ] Display applied filters

### Current Status:
- ✓ StockMovementAnalytics: 6/8 (missing reset button, validation)
- ✓ PurchaseAnalytics: 5/8 (missing reset, validation, display)
- ✓ StockLevelAnalytics: 7/8 (missing reset button)
- ✓ RequestDispatchAnalytics: 8/8 (complete)
- ✓ StockValuation: 4/8 (minimal filters)

---

## Part 8: Performance Considerations

### Query Optimization Opportunities

**1. N+1 Query Problem in Analytics Summary**
```php
// Current: Clones query 6+ times
$baseQuery = StockMovement::query()->whereHas('stock', ...);
$stock_in = $baseQuery->clone()->whereIn('type', ['in', 'return'])->sum('quantity');
$stock_out = $baseQuery->clone()->whereIn('type', ['out', ...])

// Better: Use raw SQL
->selectRaw(
    'SUM(CASE WHEN type IN ("in","return") THEN quantity ELSE 0 END) as stock_in,
     SUM(CASE WHEN type IN ("out","damaged","transfer") THEN quantity ELSE 0 END) as stock_out'
)->first();
```

**2. Missing Indexes**
- Add index on `stock_movements.movement_date`
- Add index on `stock_movements.reference_type`
- Add index on `stock_movements.moved_by_id`
- Add composite index on `(branch_id, movement_date)`

**3. Pagination Optimization**
- Use cursor pagination for large datasets
- Add query caching for analytics summaries
- Implement materialized views for complex aggregations

---

## Part 9: JavaScript/Chart Library Decisions

### Current Usage:
- **Highcharts** (Via CDN)
  - Purchase Analytics
  - Stock Level Analytics
  - Request Dispatch Analytics
  - Stock Valuation
  
- **ApexCharts** (Via CDN)
  - Stock Variance Analytics
  - Supplier Performance
  - Branch Performance

### Recommendation: Standardize on ONE library

**Option 1: Highcharts (Recommended)**
- Pros: More mature, better docs, better accessibility
- Cons: Requires license for commercial (not open-source)
- Cost: $$$ (but already in use)

**Option 2: ApexCharts (Free Alternative)**
- Pros: Modern, responsive, good UX
- Cons: Slightly less mature
- Cost: Free ✓

**Option 3: Chart.js (Minimal)**
- Pros: Lightweight, simple, free
- Cons: Limited advanced features
- Cost: Free ✓

**Decision:** Keep Highcharts for now, plan ApexCharts migration in future

---

## Part 10: Implementation Priority Matrix

| Feature | Effort | Impact | Priority | Timeline |
|---------|--------|--------|----------|----------|
| Add Charts to StockMovement | 2-3h | High | P1 | Week 1 |
| Fix Shift/Department Filters | 1-2h | High | P1 | Week 1 |
| Real-time Chart Updates | 1-2h | High | P1 | Week 1 |
| Export Enhancements | 2-3h | Medium | P2 | Week 2 |
| Comparison Dashboard | 2-3h | Medium | P2 | Week 2 |
| Drill-down Analysis | 3-4h | Medium | P2 | Week 3 |
| Performance Optimization | 4-5h | High | P1 | Ongoing |
| Alerts & Thresholds | 4-5h | Low | P3 | Month 2 |

---

## Next Steps

1. ✅ Review this audit document
2. → Implement Priority 1 items (see IMPLEMENTATION_STOCKMOVEMENT_CHARTS.md)
3. → Fix filter inconsistencies (see FIX_FILTERS.md)
4. → Add missing indexes (see DATABASE_OPTIMIZATION.md)
5. → Plan Priority 2 enhancements

---

## Related Documentation

- `IMPLEMENTATION_STOCKMOVEMENT_CHARTS.md` - Step-by-step chart implementation
- `FIX_FILTERS_CONSISTENCY.md` - Filter enhancement guide
- `DATABASE_OPTIMIZATION.md` - Query optimization opportunities
- `CHART_LIBRARY_MIGRATION.md` - Plan for standardizing chart library
