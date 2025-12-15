# Quick Integration Guide - Implementing Exports

## Step-by-Step Implementation

### Step 1: Add Exportable Trait to Component

```php
use App\Traits\Exportable;

class YourComponent extends BaseComponent
{
    use Exportable;  // ← Add this
    
    // ... rest of component
}
```

### Step 2: Create Export Methods

#### Simple One-Liner Export
```php
public function exportPDF()
{
    return $this->export(
        'report-name',
        $this->getData(),
        'exports.your-template',
        'pdf'
    );
}
```

#### With Custom Options
```php
public function exportPDF()
{
    return $this->export(
        'report-name',
        $this->getData(),
        'exports.your-template',
        'pdf',
        false,  // queue
        ['orientation' => 'landscape', 'paper' => 'A4']
    );
}

public function exportExcel()
{
    return $this->export(
        'report-name',
        $this->getData(),
        'exports.your-template',
        'excel'
    );
}

public function exportCSV()
{
    return $this->export(
        'report-name',
        $this->getData(),
        'exports.your-template',
        'csv'
    );
}
```

### Step 3: Create Export Template

Create file: `resources/views/exports/your-template.blade.php`

```blade
@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; }
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #2C3E50; color: white; padding: 10px; }
            td { padding: 8px; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <h1>Your Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->field1 }}</td>
                    <td>{{ $item->field2 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
    </html>
@endif

@if($forExcel ?? false)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th>Column 1</th>
                <th>Column 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->field1 }}</td>
                <td>{{ $item->field2 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### Step 4: Add Export Buttons to View

```blade
<div class="flex items-center gap-2">
    <button wire:click="exportPDF" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4"><!-- PDF Icon --></svg>
        Export PDF
    </button>
    
    <button wire:click="exportExcel" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4"><!-- Excel Icon --></svg>
        Export Excel
    </button>
    
    <button wire:click="exportCSV" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4"><!-- CSV Icon --></svg>
        Export CSV
    </button>
</div>
```

### Step 5: Prepare Data Method (Optional)

```php
private function getData()
{
    return collect($this->items)->map(function ($item) {
        return [
            'field1' => $item->field1,
            'field2' => $item->field2,
            // ... more fields
        ];
    });
}
```

---

## Quick Checklist

- [ ] Import Exportable trait in component
- [ ] Create export methods (exportPDF, exportExcel, exportCSV)
- [ ] Create export template in resources/views/exports/
- [ ] Add export buttons to view
- [ ] Test each export format
- [ ] Verify data formatting in exports
- [ ] Check file naming and downloads

---

## Real Examples from Codebase

### Example 1: Simple CSV (StockMovements.php)

```php
public function exportCsv()
{
    $branchId = $this->getBranchId();
    $query = StockMovement::with(['stock.item', 'mover'])
        ->whereHas('stock', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    
    $this->applyFilters($query);
    $movements = $query->orderBy('movement_date', 'desc')->get();

    $csvData = [['Date', 'Item', 'Type', 'Quantity', 'Moved By']];
    
    foreach ($movements as $movement) {
        $csvData[] = [
            $movement->movement_date->format('Y-m-d'),
            $movement->stock->item->name ?? 'N/A',
            ucfirst($movement->type),
            number_format(abs($movement->quantity), 2),
            $movement->mover->name ?? 'N/A',
        ];
    }

    $filename = 'movements-' . now()->format('Y-m-d-His') . '.csv';
    $handle = fopen('php://temp', 'r+');
    foreach ($csvData as $row) {
        fputcsv($handle, $row);
    }
    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return response()->streamDownload(
        function () use ($csv) { echo $csv; },
        $filename,
        ['Content-Type' => 'text/csv']
    );
}
```

### Example 2: Using Exportable Trait (Recommended)

```php
use Exportable;

public function exportAnalytics()
{
    return $this->export(
        'analytics-' . now()->format('Y-m-d'),
        $this->prepareExportData(),
        'exports.analytics.summary',
        'pdf',
        false,
        ['orientation' => 'landscape']
    );
}

private function prepareExportData()
{
    return collect([
        'summary' => $this->getSummary(),
        'details' => $this->getDetails(),
        'period' => [
            'from' => $this->dateFrom,
            'to' => $this->dateTo,
        ]
    ]);
}
```

---

## Common Patterns

### Pattern: Export with Filters Applied

```php
public function exportFiltered()
{
    // Get filtered data from render() method or separate query
    $data = Stock::where('branch_id', $this->getBranchId())
        ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
        ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
        ->get();

    return $this->export(
        'stocks-filtered',
        $data,
        'exports.stocks',
        'pdf'
    );
}
```

### Pattern: Export with Date Range

```php
public function exportByDateRange()
{
    $data = SomeModel::whereBetween('created_at', [
        Carbon::parse($this->dateFrom)->startOfDay(),
        Carbon::parse($this->dateTo)->endOfDay()
    ])->get();

    return $this->export(
        'report-' . $this->dateFrom . '-to-' . $this->dateTo,
        $data,
        'exports.report-template',
        'pdf'
    );
}
```

### Pattern: Large Dataset Export (Queued)

```php
public function exportLargeDataset()
{
    $data = collect(); // Build large dataset
    
    if ($data->count() > 500) {
        // Queue for background processing
        return $this->export(
            'large-dataset',
            $data,
            'exports.template',
            'pdf',
            true  // queue=true
        );
    }
    
    // Immediate export for smaller datasets
    return $this->export(
        'large-dataset',
        $data,
        'exports.template',
        'pdf',
        false  // queue=false
    );
}
```

---

## Troubleshooting

### Issue: View not found
**Solution:** Check export template path matches exactly
```php
'exports.inventory.analytics-pdf'  // ✅ Correct
// Should map to: resources/views/exports/inventory/analytics-pdf.blade.php
```

### Issue: Data not displaying
**Solution:** Check variable names in template match passed data
```blade
<!-- In component -->
$data = collect([...]);
return $this->export(..., $data, ...);

<!-- In template -->
@foreach($data as $item)  <!-- ✅ Correct -->
```

### Issue: Formatting issues in PDF
**Solution:** Use inline styles, external stylesheets may not work
```html
<!-- ✅ Works -->
<table style="width: 100%; border-collapse: collapse;">

<!-- ❌ May not work -->
<table class="my-table">
```

### Issue: Memory issues with large exports
**Solution:** Enable queuing
```php
return $this->export(..., $data, ..., true);  // queue=true
```

---

## Export Button Styling Template

```blade
<!-- Button Group -->
<div class="flex items-center gap-2">
    <div class="inline-flex rounded-lg border border-zinc-300 dark:border-zinc-700 overflow-hidden">
        <button wire:click="exportPDF" 
            class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border-r border-zinc-300 dark:border-zinc-700 flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            PDF
        </button>
        
        <button wire:click="exportExcel" 
            class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border-r border-zinc-300 dark:border-zinc-700 flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Excel
        </button>
        
        <button wire:click="exportCSV" 
            class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            CSV
        </button>
    </div>
</div>
```

---

## See Also

- **Full Trait Details:** Read `Exportable.php` source code
- **Job Implementation:** See `ExportPDFJob.php` and `ExportExcelJob.php`
- **Working Examples:** Check `StockMovements.php` and `OverallSummaryDashboard.php`
