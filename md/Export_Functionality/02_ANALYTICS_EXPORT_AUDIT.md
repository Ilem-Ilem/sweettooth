# Analytics Module - Export Functionality Audit

## Current State

### 1. OverallSummaryDashboard.php Component 🔄
**File:** `app/Livewire/BranchDashboard/Analytics/OverallSummaryDashboard.php`

#### Export Status: 🔄 Stub Methods (Buttons & Logic Ready)
- **Methods:** `exportPDF()` and `exportCSV()` (Lines 457-465)
- **Status:** Toast messages "coming soon"
- **View Integration:** ✅ Buttons exist in view (Lines 24-37)
- **Data:** ✅ All preparation methods available
- **Ready to Implement:** YES - straightforward implementation

#### Current Implementation
```php
public function exportPDF()
{
    $this->toast()->info('PDF export feature coming soon')->send();
}

public function exportCSV()
{
    $this->toast()->info('CSV export feature coming soon')->send();
}
```

#### Data Available for Export

**Overall Summary (Lines 65-123):**
```php
[
    'total_stock_value' => float,
    'total_items' => int,
    'low_stock_items' => int,
    'critical_items' => int,
    'expired_items' => int,
    'total_purchases' => int,
    'total_purchase_value' => float,
    'total_movements' => int,
    'stock_in' => float,
    'stock_out' => float,
    'total_requests' => int,
    'pending_requests' => int,
    'completed_requests' => int,
    'previous_stock_value' => float,
    'stock_value_change' => float,
    'stock_value_change_percentage' => float,
]
```

**Stock Health Overview (Lines 125-138):**
- Health status distribution by count

**Recent Activity (Lines 140-151):**
- Last 10 movements with stock, item, and mover details

**Top Alerts (Lines 153-208):**
- Expired items, critical conditions, low stock warnings
- Sorted by priority, limited to 15

**Business Insights (Lines 210-288):**
- Stock value trend analysis
- Top 3 depleting items
- Purchase activity status
- Low stock alerts
- Expired items alerts
- Average daily consumption

**Stock Health Table (Lines 290-346):**
- 15 items with lowest health percentage
- Includes: name, stock level, reorder level, status, percentage, last movement, UOM

**Department/Category Breakdown (Lines 348-392):**
- Grouped by item category
- Top 10 by stock value
- Includes: stock value, stock in/out, low items, requests, item count

**Performance Metrics (Lines 394-454):**
- Average turnover rate
- Fastest moving item
- Slowest moving item
- Most requested item
- Expired vs active percentage

#### View Integration ✅
**File:** `resources/views/livewire/branch-dashboard/analytics/overall-summary-dashboard.blade.php`

**Export Buttons Location: Lines 24-37**
```blade
<button wire:click="exportPDF"
    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors flex items-center gap-2">
    <svg class="w-4 h-4"><!-- PDF icon --></svg>
    Export PDF
</button>

<button wire:click="exportCSV"
    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors flex items-center gap-2">
    <svg class="w-4 h-4"><!-- CSV icon --></svg>
    Export CSV
</button>
```

#### Recommended Implementation

```php
use Exportable;

public function exportPDF()
{
    return $this->export(
        'analytics-overall-summary-' . now()->format('Y-m-d'),
        $this->prepareExportData(),
        'exports.analytics.overall-summary-pdf',
        'pdf',
        false,
        ['orientation' => 'landscape', 'paper' => 'A4']
    );
}

public function exportCSV()
{
    return $this->export(
        'analytics-overall-summary-' . now()->format('Y-m-d'),
        $this->prepareExportData(),
        'exports.analytics.overall-summary-csv',
        'csv'
    );
}

public function exportExcel()
{
    return $this->export(
        'analytics-overall-summary-' . now()->format('Y-m-d'),
        $this->prepareExportData(),
        'exports.analytics.overall-summary-excel',
        'excel'
    );
}

private function prepareExportData()
{
    return collect([
        'period' => [
            'from' => $this->dateFrom,
            'to' => $this->dateTo,
        ],
        'summary' => $this->getOverallSummary(),
        'health_overview' => $this->getStockHealthOverview(),
        'recent_activity' => $this->getRecentActivity(),
        'top_alerts' => $this->getTopAlerts(),
        'insights' => $this->getInsights(),
        'stock_health' => $this->getStockHealthTable(),
        'department_breakdown' => $this->getDepartmentBreakdown(),
        'performance_metrics' => $this->getPerformanceMetrics(),
    ]);
}
```

---

### 2. StockMovementAnalytics.php Component ✅
**File:** `app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php`

#### Export Status: ✅ CSV Export Implemented
- **Method:** `exportCsv()` (Lines 289-340+)
- **Format:** CSV only
- **Status:** Fully working with filters applied
- **Marked as FIXED in code:** "CSV export — safe, no more crashes"

#### Implementation Details
```php
public function exportCsv()
{
    $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
    $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
    $dateTo   = Carbon::parse($this->dateTo)->endOfDay();

    $movements = StockMovement::with(['stock.item', 'mover', 'reference'])
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->orderBy('movement_date', 'desc')
        ->get()
        ->append('department_name');

    // Apply filters manually
    $movements = $movements->filter(function ($m) {
        if ($this->selectedItem && $m->stock_id != $this->selectedItem) return false;
        if ($this->movementType && $m->type != $this->movementType) return false;
        if ($this->searchTerm) {
            $haystack = strtolower("{$m->stock->item->name} {$m->stock->item->sku} {$m->mover?->name} {$m->notes}");
            if (!str_contains($haystack, strtolower($this->searchTerm))) return false;
        }
        return true;
    });

    // Build CSV with headers
    // Returns streamed response
}
```

#### CSV Columns
- Date, Time
- Item Name, SKU
- Type (in/out/transfer/damaged/adjustment)
- Quantity Change (signed)
- Qty Before, Qty After
- UOM
- Moved By
- Department
- Shift
- Reference
- Notes

---

### 3. Other Analytics Components

No dedicated export methods found in:
- PurchaseAnalytics.php (Uses Highcharts built-in export)
- StockLevelAnalytics.php (Uses Highcharts built-in export)
- RequestDispatchAnalytics.php (Uses Highcharts built-in export)
- BranchPerformance.php
- StockValuationAnalytics.php
- StockVarianceAnalytics.php
- AlertsDashboard.php
- SupplierPerformance.php

---

## View Integration Status

### overall-summary-dashboard.blade.php ✅
**Status:** Export buttons present and functional

**Button Details:**
- Location: Header section (Lines 10-39)
- PDF Button: Line 24-30
- CSV Button: Line 31-37
- Styling: White/20 with hover effect, flex layout with icon

**Date Filters Present:**
- From Date: Line 45-46
- To Date: Line 50-51
- Live binding with `wire:model.live`

---

## Recommended Export Templates

### overall-summary-pdf.blade.php
```blade
@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; }
            .header { margin-bottom: 30px; border-bottom: 3px solid #2C3E50; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th { background-color: #2C3E50; color: white; padding: 10px; text-align: left; }
            td { padding: 8px; border: 1px solid #ddd; }
            .metric-row { background-color: #f9f9f9; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Inventory Analytics Report</h1>
            <p>Period: {{ $data['period']['from'] }} to {{ $data['period']['to'] }}</p>
            <p>Generated: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Summary Metrics -->
        <h2>Summary Metrics</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr class="metric-row">
                <td>Total Stock Value</td>
                <td>₦{{ number_format($data['summary']['total_stock_value'], 2) }}</td>
            </tr>
            <tr>
                <td>Total Items</td>
                <td>{{ $data['summary']['total_items'] }}</td>
            </tr>
            <tr class="metric-row">
                <td>Low Stock Items</td>
                <td>{{ $data['summary']['low_stock_items'] }}</td>
            </tr>
            <tr>
                <td>Critical Items</td>
                <td>{{ $data['summary']['critical_items'] }}</td>
            </tr>
            <tr class="metric-row">
                <td>Expired Items</td>
                <td>{{ $data['summary']['expired_items'] }}</td>
            </tr>
            <tr>
                <td>Total Movements</td>
                <td>{{ $data['summary']['total_movements'] }}</td>
            </tr>
            <tr class="metric-row">
                <td>Stock In</td>
                <td>{{ number_format($data['summary']['stock_in'], 2) }} units</td>
            </tr>
            <tr>
                <td>Stock Out</td>
                <td>{{ number_format($data['summary']['stock_out'], 2) }} units</td>
            </tr>
        </table>

        <!-- Stock Health Table -->
        <h2>Stock Health Status</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>Current Level</th>
                <th>Reorder Level</th>
                <th>Health %</th>
                <th>Status</th>
            </tr>
            @foreach($data['stock_health'] as $stock)
            <tr class="metric-row">
                <td>{{ $stock['item_name'] }}</td>
                <td>{{ $stock['stock_level'] }}</td>
                <td>{{ $stock['reorder_level'] }}</td>
                <td>{{ $stock['health_percentage'] }}%</td>
                <td>{{ ucfirst($stock['status']) }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Department Breakdown -->
        <h2>Category Breakdown</h2>
        <table>
            <tr>
                <th>Category</th>
                <th>Stock Value</th>
                <th>Items</th>
                <th>Stock In</th>
                <th>Stock Out</th>
            </tr>
            @foreach($data['department_breakdown'] as $dept)
            <tr class="metric-row">
                <td>{{ $dept['category'] }}</td>
                <td>₦{{ number_format($dept['stock_value'], 2) }}</td>
                <td>{{ $dept['item_count'] }}</td>
                <td>{{ number_format($dept['stock_in'], 2) }}</td>
                <td>{{ number_format($dept['stock_out'], 2) }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Key Insights -->
        <h2>Key Insights</h2>
        <ul>
            @foreach($data['insights'] as $insight)
            <li>{{ $insight['message'] }}</li>
            @endforeach
        </ul>

        <!-- Top Alerts -->
        <h2>Top Alerts</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
            @foreach($data['top_alerts']->take(10) as $alert)
            <tr class="metric-row">
                <td>{{ $alert['type'] }}</td>
                <td>{{ $alert['message'] }}</td>
                <td>{{ $alert['action'] }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Performance Metrics -->
        <h2>Performance Metrics</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr class="metric-row">
                <td>Average Turnover Rate</td>
                <td>{{ $data['performance_metrics']['average_turnover_rate'] }} units/day</td>
            </tr>
            <tr>
                <td>Fastest Moving Item</td>
                <td>{{ $data['performance_metrics']['fastest_moving']['name'] ?? 'N/A' }}</td>
            </tr>
            <tr class="metric-row">
                <td>Slowest Moving Item</td>
                <td>{{ $data['performance_metrics']['slowest_moving']['name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Most Requested Item</td>
                <td>{{ $data['performance_metrics']['most_requested']['name'] ?? 'N/A' }}</td>
            </tr>
        </table>
    </body>
    </html>
@endif
```

### overall-summary-excel.blade.php
Similar structure with Excel-friendly formatting

---

## Implementation Priority

### Phase 1 (Immediate - High Impact)
1. **OverallSummaryDashboard.php** - Replace stub methods with Exportable trait
   - Buttons already exist in view
   - All data preparation methods ready
   - Estimated time: 30 minutes

### Phase 2 (Short-term)
2. Create export templates for OverallSummaryDashboard
3. Test exports with real data
4. Add export button to StockMovementAnalytics view

### Phase 3 (Medium-term)
5. Implement PDF/Excel for other chart-based analytics
6. Add export functionality to remaining analytics components

---

## Testing Checklist

- [ ] PDF export generates valid multi-page document
- [ ] CSV export with proper escaping and formatting
- [ ] Excel export with column width optimization
- [ ] Large datasets (>1000 rows) handle correctly
- [ ] Filters applied correctly to exports
- [ ] Date range exports accurate
- [ ] Number formatting consistent (thousands separator, decimal places)
- [ ] Null/missing values handled gracefully
- [ ] Download file names with timestamps
- [ ] Response headers correct (Content-Type, disposition)
- [ ] Performance acceptable (no timeouts)
- [ ] Special characters in item names export correctly
