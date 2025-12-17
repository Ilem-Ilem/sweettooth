# Reporting System: Recommended Fixes

This document provides a detailed, step-by-step guide to refactoring the reporting system. The goal is to create a single, unified, scalable, and maintainable architecture for all data exports. The changes are designed to be incremental and production-safe.

## Guiding Principle: Unify Around a Single, Robust System

The core strategy is to refactor and enhance the `Exportable` trait to make it the single source of truth for all backend data exports (PDF, Excel, and CSV). All other implementations will be deprecated and removed.

---

## Fix 1: Enhance the `Exportable` Trait and Queued Jobs

First, we must fix the two major problems in the `Exportable` trait: the lack of CSV support and the inefficient queuing mechanism.

### Step 1.1: Add CSV Support to the Ecosystem

We need a new job to handle CSV generation, similar to the existing `ExportExcelJob`.

**1. Create `ExportCsvJob.php`:**

```bash
php artisan make:job ExportCsvJob
```

**2. Implement the `ExportCsvJob`:**

This job will be responsible for generating the CSV file. Notice it receives `$data` which we will address in the next step.

```php
// app/Jobs/ExportCsvJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
// ... other necessary imports

class ExportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public array $headings,
        public string $fileName,
        public int $userId
    ) {}

    public function handle(): void
    {
        $filePath = 'exports/' . $this->fileName;
        $file = fopen(Storage::disk('local')->path($filePath), 'w');

        fputcsv($file, $this->headings);

        foreach ($this->data as $row) {
            fputcsv($file, (array) $row);
        }

        fclose($file);

        // Notify the user (implement your notification logic here)
    }
}
```

### Step 1.2: Refactor `Exportable` Trait for Scalability and CSV

Now, modify the `Exportable` trait to support CSVs and, more importantly, to pass query parameters to jobs instead of entire data collections.

**Before: `app/Traits/Exportable.php`**

```php
// Old, inefficient queueExports method
public function queueExports(string $format, string $fileName, Collection $data, array $headings): void
{
    $job = $format === 'xlsx'
        ? new ExportExcelJob($data, $headings, $fileName, auth()->id())
        : new ExportPDFJob($data, $headings, $fileName, auth()->id());

    $job->onQueue('exports');
    dispatch($job);
}
```

**After: `app/Traits/Exportable.php`**

We will change the signature of `export` and `queueExports`. Instead of raw data, they will accept the name of a public method on the Livewire component that can be called to get the report data.

```php
// app/Traits/Exportable.php

// ... imports
use App\Jobs\ExportCsvJob; // Add this

trait Exportable
{
    // ... existing properties

    // The main public method is now more flexible
    public function export(string $format, string $fileName, string $dataMethodName, array $headings, bool $useQueue = true): mixed
    {
        if ($useQueue) {
            $this->queueExports($format, $fileName, $dataMethodName, $headings);
            // Optionally, return a success message
            return null;
        }
        
        // The immediate export also uses the method name now
        $data = $this->$dataMethodName();
        return $this->performImmediateExport($format, $fileName, $data, $headings);
    }

    // REFACTORED for scalability
    public function queueExports(string $format, string $fileName, string $dataMethodName, array $headings): void
    {
        // Instead of passing the data, we pass the component's state
        // and the name of the method to call.
        $job = match ($format) {
            'xlsx' => new ExportExcelJob($this->id, $dataMethodName, $headings, $fileName, auth()->id()),
            'pdf' => new ExportPDFJob($this->id, $dataMethodName, $headings, $fileName, auth()->id()),
            'csv' => new ExportCsvJob($this->id, $dataMethodName, $headings, $fileName, auth()->id()),
            default => throw new \Exception("Unsupported export format: $format"),
        };

        $job->onQueue('exports');
        dispatch($job);
    }
    
    // ... other methods
}
```

You would then update the Jobs (`ExportExcelJob`, `ExportPDFJob`, `ExportCsvJob`) to accept the component ID and method name, re-hydrate the component in the job's `handle` method, and then call the method to get the data. This keeps the job payload tiny.

---

## Fix 2: Optimize Data Fetching

Next, fix the inefficient data queries.

### Step 2.1: Refactor `SalesPerformanceReportService`

**Before: `app/Services/Reports/SalesPerformanceReportService.php`**

```php
// Inefficient: Loads everything into memory
protected function generateReportData(array $params): array
{
    $sales = Sale::whereBetween('sale_date', [$params['start_date'], $params['end_date']])->get();
    // ... then iterates in PHP
}
```

**After: `app/Services/Reports/SalesPerformanceReportService.php`**

Use database-level aggregations.

```php
// Efficient: Uses SQL aggregations
protected function generateReportData(array $params): array
{
    return Sale::query()
        ->whereBetween('sale_date', [$params['start_date'], $params['end_date']])
        ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total_sales, COUNT(*) as number_of_transactions')
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get()
        ->toArray();
}
```

### Step 2.2: Refactor `profitAnalysis` in the Dashboard Component

**Before: `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`**

```php
// Inefficient: Loads full collections
private function profitAnalysis()
{
    $sales = Sale::where(...)->get();
    $productions = Production::where(...)->get();
    // ... complex, slow PHP logic
}
```

**After: `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`**

This query is more complex, but it's vastly more performant. This is a conceptual example.

```php
// Efficient: Uses database-level joins and aggregations
private function profitAnalysis()
{
    $salesData = Sale::query()
        ->where('branch_id', current_branch_id())
        ->whereBetween('sale_date', [$this->start_date, $this->end_date])
        ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
        ->join('items', 'sale_items.item_id', '=', 'items.id')
        ->selectRaw('
            DATE(sales.sale_date) as date,
            SUM(sale_items.quantity * sale_items.price) as total_sales,
            SUM(sale_items.quantity * items.cost_price) as total_cost
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $this->profitAnalysis = [
        'salesData' => $salesData->map(function ($row) {
            return [
                'date' => $row->date,
                'total_sales' => $row->total_sales,
                'total_cost' => $row->total_cost,
                'profit' => $row->total_sales - $row->total_cost,
            ];
        }),
        // ... other calculations
    ];
}
```

---

## Fix 3: Unify the `SalesDashboard` Component

Now, remove the manual CSV export and use the refactored `Exportable` trait.

**Before: `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`**

```php
// Has exportToCSV(), exportToExcel(), exportToPDF() ...
public function exportToCSV() { /* ... manual logic ... */ }
```

**After: `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`**

```php
class Index extends BaseComponent
{
    use Exportable; // The enhanced trait

    // This public method is what the export jobs will call
    public function getProfitAnalysisReportData(): array
    {
        // Ensure data is loaded if not already
        if (empty($this->profitAnalysis)) {
            $this->profitAnalysis();
        }
        return $this->profitAnalysis['salesData']->toArray();
    }
    
    // The single entry point for all exports
    public function exportData($format)
    {
        $fileName = 'Profit-Analysis-' . now()->format('Y-m-d');
        $headings = ['Date', 'Total Sales', 'Total Cost', 'Profit'];
        
        // Call the refactored export method
        $this->export(
            $format,
            $fileName,
            'getProfitAnalysisReportData', // Pass the method name
            $headings,
            true // Always use the queue for production
        );
        
        // Give user feedback
        $this->notify('Export has been initiated. You will be notified when it is ready.');
    }
    
    // The old exportToCSV(), exportToExcel(), etc. methods are now REMOVED.
}
```

And in the Blade view, all buttons now call `exportData` with the correct format:

```html
<!-- resources/views/livewire/.../analytics/index.blade.php -->
<button wire:click="exportData('csv')">Export CSV</button>
<button wire:click="exportData('xlsx')">Export Excel</button>
<button wire:click="exportData('pdf')">Export PDF</button>
```

---

## Fix 4: Repair the Broken Accounting Route

Finally, fix the route in the accounting module. The best approach is to create a dedicated Livewire component for accounting reports that can reuse the `Exportable` trait.

**1. Create a new Livewire component:**

```bash
php artisan make:livewire Accounting/Reports/Index
```

**2. Update the Route:**

Point the route to this new component.

```php
// routes/accounting.php
use App\Livewire\Accounting\Reports\Index as AccountingReports;

// Remove the old, broken route
// Route::post('{report}/export', ...); 

// Add a route to the new component
Route::get('/reports', AccountingReports::class)->name('accounting.reports');
```

**3. Implement the Component:**

The new `Accounting/Reports/Index.php` component would contain the logic for displaying and exporting accounting reports, and it would `use Exportable` just like the sales dashboard, providing a consistent architecture across the entire application.
