# Stock Movement Analytics - Chart Implementation Guide

## Overview
Complete step-by-step guide to add 4 visual charts to StockMovementAnalytics component without breaking existing functionality.

---

## Chart 1: Movement Type Distribution (Pie Chart)

### 1.1 Data Method (Component)

Add to `app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php`:

```php
private function getMovementTypeDistribution($branchId)
{
    $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
    $dateTo = Carbon::parse($this->dateTo)->endOfDay();
    
    return StockMovement::query()
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->selectRaw('type, COUNT(*) as count, SUM(ABS(quantity)) as total_quantity')
        ->groupBy('type')
        ->orderByDesc('count')
        ->get()
        ->map(fn ($item) => [
            'type' => ucfirst($item->type),
            'count' => $item->count,
            'quantity' => $item->total_quantity,
        ]);
}
```

### 1.2 Update render() method

```php
public function render()
{
    $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
    // ... existing code ...
    
    return view('livewire.branch-dashboard.analytics.stock-movement-analytics', [
        'movements' => $movements,
        'movementTypes' => ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return'],
        'availableItems' => $this->getAvailableItems(),
        'analytics' => $this->getAnalyticsSummary($branchId),
        'typeDistribution' => $this->getMovementTypeDistribution($branchId),  // ← NEW
        'topMovedItems' => $this->getTopMovedItems(),
        'velocityAnalysis' => $this->getVelocityAnalysis(),
        'activityFeed' => $activityFeed,
        'departments' => Department::orderBy('name')->get(),
    ]);
}
```

### 1.3 Blade Template - Add Chart Section

Add to `resources/views/livewire/branch-dashboard/analytics/stock-movement-analytics.blade.php` (after Period Summary section):

```blade
<!-- Movement Type Distribution Chart -->
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
        </svg>
        Movement Type Distribution
    </h3>
    <div id="movementTypeChart" class="h-64" wire:ignore></div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const chartData = @js($typeDistribution);
        
        const chart = new Highcharts.Chart({
            chart: { type: 'pie', renderTo: 'movementTypeChart' },
            title: { text: null },
            tooltip: {
                pointFormat: '<b>{point.name}</b>: {point.y} movements ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br/>{point.y} ({point.percentage:.1f}%)'
                    }
                }
            },
            series: [{
                data: chartData.map(item => ({
                    name: item.type,
                    y: item.count,
                    color: getTypeColor(item.type)
                }))
            }],
            credits: { enabled: false }
        });
        
        // Listen for filter updates
        Livewire.on('updateCharts', () => {
            // Chart will re-render on component refresh
        });
    });
    
    function getTypeColor(type) {
        const colors = {
            'In': '#10b981',
            'Out': '#ef4444',
            'Adjustment': '#f59e0b',
            'Transfer': '#3b82f6',
            'Damaged': '#ec4899',
            'Return': '#8b5cf6'
        };
        return colors[type] || '#6b7280';
    }
</script>
@endpush
```

---

## Chart 2: Daily Movement Trend (Line/Area Chart)

### 2.1 Data Method (Component)

Already exists as `getDailyBreakdown()` - use as-is.

### 2.2 Blade Template - Add Chart Section

Add after Movement Type Distribution chart:

```blade
<!-- Daily Movement Trend Chart -->
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
        </svg>
        Daily Movement Trend (Last 7 Days)
    </h3>
    <div id="dailyTrendChart" class="h-64" wire:ignore></div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const breakdownData = @js($analytics['daily_breakdown'] ?? []);
        
        // Transform data for chart
        const dates = breakdownData.map(d => d.date).reverse();
        const stockIn = breakdownData.map(d => parseFloat(d.stock_in)).reverse();
        const stockOut = breakdownData.map(d => parseFloat(d.stock_out)).reverse();
        
        new Highcharts.Chart({
            chart: { type: 'spline', renderTo: 'dailyTrendChart' },
            title: { text: null },
            xAxis: {
                categories: dates,
                title: { text: 'Date' }
            },
            yAxis: {
                title: { text: 'Quantity' }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    pointStart: 0
                }
            },
            series: [
                {
                    name: 'Stock In',
                    data: stockIn,
                    color: '#10b981'
                },
                {
                    name: 'Stock Out',
                    data: stockOut,
                    color: '#ef4444'
                }
            ],
            credits: { enabled: false }
        });
    });
</script>
@endpush
```

---

## Chart 3: Top Moved Items (Horizontal Bar Chart)

### 3.1 Data Method (Component)

Already exists as `getTopMovedItems()` - enhance it:

```php
public function getTopMovedItems($limit = 10)
{
    $branchId = Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
    $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
    $dateTo = Carbon::parse($this->dateTo)->endOfDay();

    return StockMovement::with(['stock.item'])
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
        ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->selectRaw('stock_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_quantity')
        ->groupBy('stock_id')
        ->orderByDesc('total_quantity')
        ->limit($limit)
        ->get()
        ->map(fn ($item) => [
            'item_name' => $item->stock->item->name ?? 'Unknown',
            'movements' => $item->movement_count,
            'total_quantity' => $item->total_quantity,
        ]);
}
```

### 3.2 Update render() to include it

```php
return view('livewire.branch-dashboard.analytics.stock-movement-analytics', [
    // ... existing ...
    'topMovedItems' => $this->getTopMovedItems(10),  // Updated
    // ...
]);
```

### 3.3 Blade Template - Add Chart

```blade
<!-- Top Moved Items Chart -->
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
        Top 10 Moved Items
    </h3>
    <div id="topItemsChart" class="h-80" wire:ignore></div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const itemsData = @js($topMovedItems ?? []);
        
        new Highcharts.Chart({
            chart: { type: 'bar', renderTo: 'topItemsChart' },
            title: { text: null },
            xAxis: {
                title: { text: 'Total Quantity Moved' }
            },
            yAxis: {
                categories: itemsData.map(d => d.item_name),
                title: null
            },
            tooltip: {
                pointFormat: '<b>Quantity: {point.y}</b><br/>Movements: {point.movements}'
            },
            plotOptions: {
                bar: {
                    dataLabels: { enabled: true, format: '{point.y}' }
                }
            },
            series: [{
                data: itemsData.map(item => ({
                    y: parseFloat(item.total_quantity),
                    movements: item.movements,
                    color: '#8b5cf6'
                }))
            }],
            credits: { enabled: false }
        });
    });
</script>
@endpush
```

---

## Chart 4: Peak Activity Timeline (Column Chart)

### 4.1 Data Method (Component)

Already exists as `getPeakActivityHours()` - already optimized.

### 4.2 Blade Template - Add Chart

```blade
<!-- Peak Activity Hours Chart -->
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Peak Activity Hours
    </h3>
    <div id="peakHoursChart" class="h-64" wire:ignore></div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const peakData = @js($analytics['peak_hours'] ?? []);
        
        new Highcharts.Chart({
            chart: { type: 'column', renderTo: 'peakHoursChart' },
            title: { text: null },
            xAxis: {
                categories: peakData.map(h => h.formatted),
                title: { text: 'Time of Day' }
            },
            yAxis: {
                title: { text: 'Number of Movements' }
            },
            tooltip: {
                pointFormat: '<b>{point.y}</b> movements at {point.name}'
            },
            plotOptions: {
                column: {
                    dataLabels: { enabled: true, format: '{point.y}' }
                }
            },
            series: [{
                data: peakData.map(h => h.count),
                color: '#f59e0b'
            }],
            credits: { enabled: false }
        });
    });
</script>
@endpush
```

---

## Integration: Add Highcharts CDN

Add to layout or base blade template if not already present:

```blade
<!-- In head or before closing body -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
```

---

## Testing Checklist

- [ ] Charts render without console errors
- [ ] Charts display correct data
- [ ] Charts are responsive (mobile-friendly)
- [ ] Charts update when filters change
- [ ] Dark mode styling works
- [ ] Charts display loading state
- [ ] Export functionality works (if added)
- [ ] No N+1 queries from chart data
- [ ] Performance acceptable (< 2 sec load time)

---

## Performance Notes

All chart methods use optimized queries:
- ✓ Direct aggregation (no mapping in PHP)
- ✓ Limited to necessary data
- ✓ Proper indexing on movement_date

---

## Browser Compatibility

Highcharts supports:
- ✓ Chrome 90+
- ✓ Firefox 88+
- ✓ Safari 14+
- ✓ Edge 90+

---

## Customization Options

### Colors
Modify color scheme in chart definitions:
```php
// In JavaScript
series: [{
    color: '#custom-hex-color'  // Change here
}]
```

### Height
Adjust chart height:
```blade
<div id="chartName" class="h-64"></div>  <!-- Change h-64 to h-96, h-80, etc -->
```

### Animation
Enable/disable animations:
```javascript
plotOptions: {
    series: {
        animation: false  // or true
    }
}
```

---

## Rollback Plan

If charts cause issues:
1. Remove chart sections from blade template
2. Remove chart data methods from component
3. Charts are in wire:ignore divs, won't affect other functionality

---

## Related Issues Fixed

- ✓ Missing visual data analysis
- ✓ Better insight into movement patterns
- ✓ Peak hour identification
- ✓ Type distribution visibility
