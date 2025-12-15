# Inventory Module - Export Functionality Audit

## Current State

### 1. Stocks.php Component
**File:** `app/Livewire/BranchDashboard/Inventory/Stocks.php`

#### Export Status: ❌ No Export Methods
- No export methods implemented
- No export buttons in view
- Should support exporting stock data with filters applied

#### Recommended Export Methods
```php
public function exportPDF()
{
    return $this->export(
        'inventory-stocks-' . now()->format('Y-m-d'),
        $this->getFilteredStocks(),
        'exports.inventory.stocks-pdf',
        'pdf',
        false,
        ['orientation' => 'landscape']
    );
}

public function exportExcel()
{
    return $this->export(
        'inventory-stocks-' . now()->format('Y-m-d'),
        $this->getFilteredStocks(),
        'exports.inventory.stocks-excel',
        'excel'
    );
}

public function exportCSV()
{
    $stocks = $this->getFilteredStocks();
    // Implement CSV export similar to StockMovements.php
}

private function getFilteredStocks()
{
    $branchId = $this->getBranchId();
    $query = Stock::with(['branch', 'item'])
        ->where('branch_id', $branchId)
        ->when($this->search, function ($q) {
            $q->whereHas('item', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        })
        // Apply other filters
        ->orderBy('updated_at', 'desc');
    
    return $query->get();
}
```

#### Data Available for Export
- Item Name, SKU, Category
- Available Quantity, Reserved, Damaged, Total
- UOM, Average Cost
- Last Stock Date
- Health Status, Expiry Date
- Reorder Level, Max Stock Level

---

### 2. StockMovements.php Component ✅
**File:** `app/Livewire/BranchDashboard/Inventory/StockMovements.php`

#### Export Status: ✅ CSV Export Implemented
- **Method:** `exportCsv()` (Lines 290-365)
- **Format:** CSV only
- **Functionality:** Complete with all filters applied
- **Data:** Movement history with full details

#### Implementation Details
```php
public function exportCsv()
{
    $branchId = $this->getBranchId();
    
    $query = StockMovement::with([
        'stock.item',
        'mover',
        'reference'
    ])
        ->whereHas('stock', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });

    $this->applyFilters($query);
    $movements = $query->orderBy('movement_date', 'desc')->get();

    // Build CSV with headers and data rows
    // Returns response()->streamDownload()
}
```

#### Export Data
- Date, Time
- Item Name, SKU
- Movement Type (in/out/transfer/damaged/adjustment)
- Quantity Change (signed)
- Quantity Before/After
- UOM
- Moved By (user/employee name)
- Department, Shift
- Reference (request number)
- Notes

#### View Integration
**File:** `resources/views/livewire/branch-dashboard/inventory/stock-movements.blade.php`

No export button found in current view. Could add:
```blade
<button wire:click="exportCsv" class="btn-export">
    <svg><!-- CSV icon --></svg>
    Export CSV
</button>
```

---

### 3. Analytics.php Component 🔄
**File:** `app/Livewire/BranchDashboard/Inventory/Analytics.php`

#### Export Status: 🔄 Stub Methods (Ready for Implementation)
- **Methods:** `exportPDF()` and `exportCSV()` (Lines 560-568)
- **Status:** Show toast messages "PDF export will be available soon"
- **Ready to Implement:** Yes - all data preparation is complete

#### Current Implementation
```php
public function exportPDF()
{
    $this->toast()->info('PDF export will be available soon')->send();
}

public function exportCSV()
{
    $this->toast()->info('CSV export will be available soon')->send();
}
```

#### Data Available for Export
The component has comprehensive analytics data:

**Summary Metrics (Lines 39-64):**
- Total Stock Value, Total Items
- Purchase Value, Total Purchases
- Movements In/Out, Total Movements
- Pending/Completed Requests
- Low Stock Items, Critical Items, Expired Items
- Previous period comparison with percentages

**Analysis Data (Lines 53-59):**
- Insights (array of business intelligence)
- Stock Health Data (status, percentage, last movement)
- Top Alerts (critical conditions, low stock, expired, expiring soon)
- Recent Activity (20 most recent movements)
- Department Breakdown (by category)
- Performance Metrics (turnover rate, fastest/slowest moving, most requested)

#### Recommended Implementation

Replace stub methods with:
```php
use Exportable;

public function exportPDF()
{
    return $this->export(
        'inventory-analytics-' . now()->format('Y-m-d'),
        $this->prepareAnalyticsData(),
        'exports.inventory.analytics-pdf',
        'pdf',
        false,
        ['orientation' => 'landscape']
    );
}

public function exportCSV()
{
    return $this->export(
        'inventory-analytics-' . now()->format('Y-m-d'),
        $this->prepareAnalyticsData(),
        'exports.inventory.analytics-csv',
        'csv'
    );
}

private function prepareAnalyticsData()
{
    return collect([
        'date_range' => [
            'from' => $this->dateFrom,
            'to' => $this->dateTo
        ],
        'summary' => [
            'total_stock_value' => $this->totalStockValue,
            'total_items' => $this->totalItems,
            'purchase_value' => $this->purchaseValue,
            'total_purchases' => $this->totalPurchases,
            'movements_in' => $this->movementsIn,
            'movements_out' => $this->movementsOut,
            'low_stock_items' => $this->lowStockItems,
            'critical_items' => $this->criticalItems,
            'expired_items' => $this->expiredItems,
        ],
        'insights' => $this->insights,
        'stock_health' => $this->stockHealthData,
        'department_breakdown' => $this->departmentBreakdown,
        'performance_metrics' => $this->performanceMetrics,
    ]);
}
```

#### View Integration
**File:** `resources/views/livewire/branch-dashboard/inventory/analytics.blade.php`

No view file found. Component loads data but view reference is missing. Could create view with export buttons.

---

### 4. Items.php Component
**File:** `app/Livewire/BranchDashboard/Inventory/Items.php`

#### Export Status: ❌ No Export Methods
- No export functionality
- Could benefit from bulk item export
- Supports filtering and searching

#### Suggested Export Methods
```php
public function exportPDF()
{
    return $this->export(
        'inventory-items-' . now()->format('Y-m-d'),
        $this->getFilteredItems(),
        'exports.inventory.items-pdf',
        'pdf'
    );
}

private function getFilteredItems()
{
    return $this->getFilteredQuery()->get();
}
```

---

## Export Button Placement in Views

### Missing from Current Views
1. **stocks.blade.php** - No export buttons
   - Could add to header section
   - Should support PDF and Excel

2. **analytics.blade.php** - View file not found
   - Would need export buttons in header

3. **items.blade.php** - No export buttons
   - Could add to toolbar

4. **stock-movements.blade.php** - No export buttons visible
   - CSV export is implemented but button not in view
   - Should add button to call `exportCsv()`

---

## Recommended Templates

### inventory-stocks-pdf.blade.php
```blade
@if($forPdf ?? false)
<div class="header">
    <h1>Stock Report</h1>
    <p>Date Range: {{ $dateFrom }} to {{ $dateTo }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Available</th>
            <th>Reserved</th>
            <th>Total</th>
            <th>UOM</th>
            <th>Cost</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $stock)
        <tr>
            <td>{{ $stock->item->name ?? 'N/A' }}</td>
            <td>{{ $stock->item->sku ?? 'N/A' }}</td>
            <td>{{ $stock->item->category ?? 'N/A' }}</td>
            <td>{{ number_format($stock->available_quantity, 2) }}</td>
            <td>{{ number_format($stock->reserved_quantity, 2) }}</td>
            <td>{{ number_format($stock->total_quantity, 2) }}</td>
            <td>{{ $stock->item->uom ?? 'N/A' }}</td>
            <td>₦{{ number_format($stock->average_cost, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
```

### inventory-stocks-excel.blade.php
```blade
@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
            <th>Item Name</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Available</th>
            <th>Reserved</th>
            <th>Total</th>
            <th>UOM</th>
            <th>Avg Cost</th>
            <th>Total Value</th>
            <th>Health Status</th>
            <th>Last Stock Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $stock)
        <tr>
            <td>{{ $stock->item->name ?? 'N/A' }}</td>
            <td>{{ $stock->item->sku ?? 'N/A' }}</td>
            <td>{{ $stock->item->category ?? 'N/A' }}</td>
            <td>{{ number_format($stock->available_quantity, 2) }}</td>
            <td>{{ number_format($stock->reserved_quantity, 2) }}</td>
            <td>{{ number_format($stock->total_quantity, 2) }}</td>
            <td>{{ $stock->item->uom ?? 'N/A' }}</td>
            <td>{{ number_format($stock->average_cost, 2) }}</td>
            <td>{{ number_format($stock->available_quantity * $stock->average_cost, 2) }}</td>
            <td>{{ ucfirst($stock->health_status) }}</td>
            <td>{{ $stock->last_stock_date?->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
```

---

## Implementation Priority

### Phase 1 (Immediate)
1. ✅ StockMovements.php - Already working, just add button to view
2. 🔄 Analytics.php - Replace stub methods with Exportable trait

### Phase 2 (Short-term)
3. Stocks.php - Add export methods and buttons
4. Items.php - Add export methods and buttons

### Phase 3 (Polish)
5. Create all export templates
6. Add export buttons to all views
7. Test with real data
8. Add audit logging for exports

---

## Testing Checklist

- [ ] CSV export with filters applied
- [ ] PDF export generates valid files
- [ ] Excel export with formatting
- [ ] Large dataset handling (>500 rows)
- [ ] Empty result sets handling
- [ ] Special characters in data
- [ ] Column alignment in exports
- [ ] Date/number formatting consistency
- [ ] File naming with timestamps
- [ ] Download response headers correct
