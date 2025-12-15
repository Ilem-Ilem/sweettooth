# Sales Dashboard Module - Export Functionality Audit

## Current State

### 1. Analytics/Index.php Component 🔄
**File:** `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`

#### Export Status: 🔄 Partially Implemented
- **CSV Export:** ✅ Fully working (Lines 452-491)
- **Excel Export:** 📋 Stub (Line 496)
- **PDF Export:** 📋 Stub (Line 502)
- **Main Method:** `exportData($format)` (Lines 412-430)
- **Status:** CSV working, Excel/PDF placeholders ready

#### Current Implementation

**Main Export Dispatcher (Lines 412-430):**
```php
public function exportData($format = 'csv')
{
    try {
        $data = $this->prepareExportData();

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($data);
            case 'excel':
                return $this->exportToExcel($data);
            case 'pdf':
                return $this->exportToPDF($data);
            default:
                $this->dispatch('notify', ['message' => 'Invalid export format', 'type' => 'error']);
        }
    } catch (\Exception $e) {
        $this->dispatch('notify', ['message' => 'Export failed: ' . $e->getMessage(), 'type' => 'error']);
    }
}
```

**Data Preparation (Lines 432-450):**
```php
protected function prepareExportData()
{
    return [
        'overview' => $this->salesOverview,
        'payments' => $this->paymentBreakdown->toArray(),
        'order_types' => $this->orderTypeBreakdown->toArray(),
        'top_products' => $this->topSellingProducts->toArray(),
        'hourly_sales' => $this->hourlySalesData->toArray(),
        'daily_sales' => $this->dailySalesData->toArray(),
        'shifts' => $this->shiftPerformance->toArray(),
        'categories' => $this->categorySales->toArray(),
        'profit' => $this->profitAnalysis,
        'period' => [
            'from' => $this->dateFrom,
            'to' => $this->dateTo,
            'period' => $this->selectedPeriod
        ]
    ];
}
```

**CSV Export (Lines 452-491):**
```php
protected function exportToCSV($data)
{
    $filename = 'sales_analytics_' . date('Y-m-d_His') . '.csv';
    $handle = fopen('php://temp', 'r+');

    // Write headers and overview
    fputcsv($handle, ['Sales Analytics Report']);
    fputcsv($handle, ['Period', Carbon::parse($data['period']['from'])->format('Y-m-d H:i') . ' to ' . Carbon::parse($data['period']['to'])->format('Y-m-d H:i')]);
    fputcsv($handle, []);

    // Overview metrics
    fputcsv($handle, ['Overview Metrics']);
    fputcsv($handle, ['Metric', 'Value']);
    foreach ($data['overview'] as $key => $value) {
        fputcsv($handle, [ucwords(str_replace('_', ' ', $key)), is_numeric($value) ? number_format($value, 2) : $value]);
    }
    fputcsv($handle, []);

    // Top Products
    fputcsv($handle, ['Top Selling Products']);
    fputcsv($handle, ['Product', 'Quantity', 'Revenue', 'Orders']);
    foreach ($data['top_products'] as $product) {
        fputcsv($handle, [
            $product['product']['name'] ?? 'N/A',
            $product['total_quantity'],
            $product['total_revenue'],
            $product['order_count']
        ]);
    }

    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return Response::streamDownload(function() use ($csv) {
        echo $csv;
    }, $filename, [
        'Content-Type' => 'text/csv',
    ]);
}
```

**Excel Export (Line 496):**
```php
protected function exportToExcel($data)
{
    // Note: This requires maatwebsite/excel package
    $this->dispatch('notify', ['message' => 'Excel export will be implemented with Laravel Excel package', 'type' => 'info']);
}
```

**PDF Export (Line 502):**
```php
protected function exportToPDF($data)
{
    // Note: This requires barryvdh/laravel-dompdf package
    $this->dispatch('notify', ['message' => 'PDF export will be implemented with DomPDF package', 'type' => 'info']);
}
```

#### Data Available for Export

**Sales Overview:**
- Total Sales, Total Orders, Average Order Value
- Average Selling Price, Average Cost
- Gross Profit, Profit Margin

**Payment Breakdown:**
- By payment method with counts and totals

**Order Type Breakdown:**
- Dine-in, Takeout, Delivery counts and values

**Top Selling Products:**
- Product name, total quantity, total revenue, order count

**Hourly Sales Data:**
- By hour with quantity and value

**Daily Sales Data:**
- By date with quantity and value

**Shift Performance:**
- By shift with metrics

**Category Sales:**
- By category with metrics

**Profit Analysis:**
- Detailed profit breakdown

#### Recommended Implementation

Replace stub methods with Exportable trait:

```php
use Exportable;

public function exportData($format = 'csv')
{
    try {
        $data = $this->prepareExportData();

        switch ($format) {
            case 'csv':
                return $this->exportToCSV($data);
            case 'excel':
                return $this->export(
                    'sales-analytics-' . now()->format('Y-m-d'),
                    collect($this->transformForExport($data)),
                    'exports.sales.analytics-excel',
                    'excel'
                );
            case 'pdf':
                return $this->export(
                    'sales-analytics-' . now()->format('Y-m-d'),
                    collect($this->transformForExport($data)),
                    'exports.sales.analytics-pdf',
                    'pdf',
                    false,
                    ['orientation' => 'landscape']
                );
            default:
                $this->dispatch('notify', ['message' => 'Invalid export format', 'type' => 'error']);
        }
    } catch (\Exception $e) {
        $this->dispatch('notify', ['message' => 'Export failed: ' . $e->getMessage(), 'type' => 'error']);
    }
}

private function transformForExport($data)
{
    return [
        'period' => $data['period'],
        'overview' => $data['overview'],
        'top_products' => $data['top_products'],
        'payment_breakdown' => $data['payments'],
        'shifts' => $data['shifts'],
        'categories' => $data['categories'],
    ];
}
```

#### View Integration
**File:** `resources/views/livewire/branch-dashboard/sales-dashboard/analytics/index.blade.php`

No export buttons found in the view. Should add export button group to header:

```blade
<div class="flex items-center gap-2">
    <button wire:click="exportData('csv')" class="btn-export">
        <svg><!-- CSV icon --></svg>
        Export CSV
    </button>
    <button wire:click="exportData('excel')" class="btn-export">
        <svg><!-- Excel icon --></svg>
        Export Excel
    </button>
    <button wire:click="exportData('pdf')" class="btn-export">
        <svg><!-- PDF icon --></svg>
        Export PDF
    </button>
</div>
```

---

### 2. MySales/Index.php Component
**File:** `app/Livewire/BranchDashboard/SalesDashboard/MySales/Index.php`

#### Export Status: ❌ No Export Methods
- No export functionality
- Could export personal sales history

#### Suggested Implementation
```php
public function exportSales()
{
    $userId = auth('employees')->id();
    
    return $this->export(
        'my-sales-' . now()->format('Y-m-d'),
        $this->getFilteredSales(),
        'exports.sales.my-sales',
        'csv'
    );
}

private function getFilteredSales()
{
    // Apply current filters and return sales data
}
```

---

### 3. Callbacks/Index.php Component
**File:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/Index.php`

#### Export Status: 🔄 Stub Method
- **Method:** `exportCallbacks()` (Lines 266-268)
- **Status:** Toast message "coming soon"
- **Ready to Implement:** Yes

#### Current Implementation
```php
public function exportCallbacks()
{
    $this->toast()->info('Export feature coming soon.')->send();
}
```

#### Recommended Implementation
```php
use Exportable;

public function exportCallbacks()
{
    $branchId = current_branch_id();
    
    $callbacks = SalesDispatchCallback::with(['dispatch.sale', 'approvedBy'])
        ->where('branch_id', $branchId)
        ->when($this->searchTerm, function ($q) {
            $q->where('reason', 'like', '%' . $this->searchTerm . '%')
              ->orWhereHas('dispatch.sale', function ($sq) {
                  $sq->where('reference_number', 'like', '%' . $this->searchTerm . '%');
              });
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return $this->export(
        'sales-callbacks-' . now()->format('Y-m-d'),
        $callbacks,
        'exports.sales.callbacks-csv',
        'csv'
    );
}
```

---

## Export Button Placement

### Missing from Views
1. **sales-dashboard/analytics/index.blade.php** - No export buttons
   - Should add to header with date range
   - Support CSV, Excel, PDF

2. **sales-dashboard/my-sales/index.blade.php** - No export buttons
   - Could add personal sales export

3. **sales-dashboard/callbacks/index.blade.php** - No export buttons
   - Callbacks export button

---

## Recommended Templates

### sales-analytics-pdf.blade.php
```blade
@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; }
            .header { margin-bottom: 30px; border-bottom: 3px solid #E74C3C; }
            .metric { display: inline-block; width: 45%; margin: 10px 2%; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th { background-color: #E74C3C; color: white; padding: 10px; }
            td { padding: 8px; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Sales Analytics Report</h1>
            <p>Period: {{ $data['period']['from'] }} to {{ $data['period']['to'] }}</p>
        </div>

        <!-- Overview Metrics -->
        <h2>Sales Overview</h2>
        <div>
            <div class="metric">
                <strong>Total Sales:</strong>
                ₦{{ number_format($data['overview']['total_sales'] ?? 0, 2) }}
            </div>
            <div class="metric">
                <strong>Total Orders:</strong>
                {{ $data['overview']['total_orders'] ?? 0 }}
            </div>
            <div class="metric">
                <strong>Average Order Value:</strong>
                ₦{{ number_format($data['overview']['average_order_value'] ?? 0, 2) }}
            </div>
            <div class="metric">
                <strong>Gross Profit:</strong>
                ₦{{ number_format($data['overview']['gross_profit'] ?? 0, 2) }}
            </div>
        </div>

        <!-- Top Products -->
        <h2>Top Selling Products</h2>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Revenue</th>
                <th>Orders</th>
            </tr>
            @foreach($data['top_products'] ?? [] as $product)
            <tr>
                <td>{{ $product['product']['name'] ?? 'N/A' }}</td>
                <td>{{ $product['total_quantity'] ?? 0 }}</td>
                <td>₦{{ number_format($product['total_revenue'] ?? 0, 2) }}</td>
                <td>{{ $product['order_count'] ?? 0 }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Payment Methods -->
        <h2>Payment Methods</h2>
        <table>
            <tr>
                <th>Method</th>
                <th>Count</th>
                <th>Total</th>
            </tr>
            @foreach($data['payment_breakdown'] ?? [] as $payment)
            <tr>
                <td>{{ $payment['method'] ?? 'N/A' }}</td>
                <td>{{ $payment['count'] ?? 0 }}</td>
                <td>₦{{ number_format($payment['total'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Shift Performance -->
        <h2>Shift Performance</h2>
        <table>
            <tr>
                <th>Shift</th>
                <th>Orders</th>
                <th>Total Sales</th>
                <th>Average</th>
            </tr>
            @foreach($data['shifts'] ?? [] as $shift)
            <tr>
                <td>{{ $shift['shift'] ?? 'N/A' }}</td>
                <td>{{ $shift['orders'] ?? 0 }}</td>
                <td>₦{{ number_format($shift['total'] ?? 0, 2) }}</td>
                <td>₦{{ number_format($shift['average'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </body>
    </html>
@endif
```

### sales-analytics-excel.blade.php
Similar structure with Excel-friendly styling

---

## Implementation Priority

### Phase 1 (Immediate)
1. **SalesDashboard/Analytics** - Complete Excel and PDF exports
   - CSV already working
   - Add export buttons to view
   - Implement Excel/PDF methods

### Phase 2 (Short-term)
2. **SalesDashboard/Callbacks** - Implement export
   - Add export button to view
   - Implement callback export logic

### Phase 3 (Medium-term)
3. **MySales** - Add personal sales export
4. Create sales export templates
5. Test with real sales data

---

## Testing Checklist

- [ ] CSV exports all data sections properly
- [ ] CSV handles currency formatting
- [ ] CSV escapes special characters in product names
- [ ] Excel export formats dates and numbers correctly
- [ ] Excel export applies column widths
- [ ] PDF generates multi-page documents when needed
- [ ] PDF preserves table formatting
- [ ] Large sales datasets export without timeout
- [ ] Filters applied correctly to exports
- [ ] Download filenames include timestamps
- [ ] All data types (currency, percentages, counts) formatted correctly
