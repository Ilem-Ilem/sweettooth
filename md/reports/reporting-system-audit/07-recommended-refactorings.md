# Reporting System Audit: 07 - Recommended Refactorings

## 1. Introduction

This document provides a concrete, step-by-step playbook for refactoring the SweetTooth reporting system. The recommendations are not just about fixing individual bugs but about fundamentally reshaping the architecture to be more scalable, maintainable, and robust. The end goal is to establish a single, unified reporting service that acts as the authoritative source for all report generation, eliminating fragmentation and code duplication.

---

## 2. The Target Architecture: A Unified Reporting Service

The cornerstone of this refactoring is the introduction of a centralized `ReportingService`. This service will act as a façade, providing a simple, consistent interface for the rest of the application to request reports, while encapsulating the complex underlying logic.

### 2.1. Responsibilities of the `ReportingService`:
-   **Authorization:** Check if the user has permission to generate the requested report.
-   **Validation:** Validate the parameters for the report (e.g., date ranges).
-   **Caching:** Check for a valid cached version of the report before generating a new one.
-   **Delegation:** Delegate the actual data-gathering to dedicated, report-specific service classes.
-   **Queuing:** Decide whether to generate the report synchronously or dispatch a job for asynchronous generation.
-   **Formatting:** Pass the final data to a dedicated formatter to produce the output file (PDF, CSV, etc.).

### 2.2. Target Architectural Flow

```mermaid
sequenceDiagram
    participant Livewire Component
    participant ReportingService (Facade)
    participant ReportSpecificService (e.g., SalesAnalyticsService)
    participant Laravel Cache
    participant Formatter (e.g., CsvFormatter)
    participant Laravel Queue
    participant ReportGenerationJob

    Livewire Component->>ReportingService: 1. generateReport('sales-analytics', { '2023-01-01' }, 'csv')
    ReportingService->>ReportingService: 2. Authorize user can('view_report_sales_analytics')
    ReportingService->>Laravel Cache: 3. Check for cached result
    alt Cache Miss
        ReportingService->>ReportSpecificService: 4. getData({ '2023-01-01' })
        ReportSpecificService->>ReportSpecificService: 5. Executes performant DB query
        ReportSpecificService-->>ReportingService: 6. Returns data array
        ReportingService->>Laravel Cache: 7. Store result in cache
    end
    ReportingService->>Laravel Queue: 8. Dispatch(ReportGenerationJob('sales-analytics', data, 'csv'))
    Laravel Queue-->>ReportGenerationJob: 9. Worker picks up job
    ReportGenerationJob->>Formatter: 10. CsvFormatter->format(data)
    Formatter-->>ReportGenerationJob: 11. Returns formatted file/stream
    ReportGenerationJob->>ReportGenerationJob: 12. Saves file, notifies user
```

---

## 3. Step-by-Step Refactoring Guide

### Step 1: Create a `ReportFormat` Enum
To eliminate magic strings, establish a single source of truth for report formats.

**"After" - `app/Enums/ReportFormat.php`:**
```php
<?php

namespace App\Enums;

enum ReportFormat: string
{
    case CSV = 'csv';
    case PDF = 'pdf';
    case EXCEL = 'xlsx';
}
```

### Step 2: Extract Business Logic into Dedicated Services
Move the data aggregation logic out of the Livewire components and into its own service class. This adheres to SRP.

**"Before" - Logic is trapped in the Livewire component:**
```php
// In app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php
class Index extends BaseComponent
{
    // ...
    private function profitAnalysis()
    {
        // ... complex, inefficient queries and calculations ...
    }
}
```

**"After" - Logic is in a clean, testable service:**
```php
// app/Services/Reports/SalesAnalyticsService.php
namespace App\Services\Reports;

class SalesAnalyticsService
{
    public function getProfitabilityData(string $startDate, string $endDate, int $branchId): array
    {
        // The single, efficient query from the performance audit
        return Sale::query()
            ->where('branch_id', $branchId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->selectRaw('DATE(sales.sale_date) as date, SUM(sale_items.quantity * sale_items.price) as total_revenue, SUM(sale_items.quantity * items.cost_price) as total_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
```

### Step 3: Refactor the Livewire Component
The component now becomes a simple orchestrator, responsible only for UI state and calling the new service for data.

**"Before" - The "Fat Component":**
```php
// app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php
class Index extends BaseComponent
{
    use Exportable;
    // ... dozens of lines of business logic ...
    
    public function exportToCSV() { /* manual implementation */ }
}
```

**"After" - The "Skinny Component":**
```php
// app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php
use App\Enums\ReportFormat;
use App\Services\Reports\SalesAnalyticsService;

class Index extends BaseComponent
{
    public array $chartData = [];
    public string $startDate;
    public string $endDate;
    
    // Inject the service via Laravel's container
    private SalesAnalyticsService $analyticsService;

    public function boot(SalesAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function mount()
    {
        // Initial data load
        $this->loadAnalyticsData();
    }
    
    public function loadAnalyticsData()
    {
        // Delegate data fetching to the service
        $this->chartData = $this->analyticsService->getProfitabilityData($this->startDate, $this->endDate, current_branch_id());
    }

    public function onDateRangeChange()
    {
        $this->loadAnalyticsData();
    }
    
    // The ONLY method needed for exporting
    public function export(string $format)
    {
        $reportFormat = ReportFormat::tryFrom($format);
        if (!$reportFormat) {
            // handle error
            return;
        }

        // TODO: Call the new central ReportingService (once created)
        // Reporting::generate('sales-analytics', $reportFormat, ['start' => $this->startDate, 'end' => $this->endDate]);
        
        $this->notify('Your report is being generated and will be available shortly.');
    }
}
```
Notice the `Exportable` trait is gone, as its logic will be centralized. The manual `exportToCSV()` method is also gone.

### Step 4: Refactor and Unify Export Jobs
Jobs should be simple and stupid. They receive data and pass it to a formatter. They should not contain business logic.

**"Before" - Job might contain complex logic or depend on Livewire:**
```php
// app/Jobs/ExportCsvJob.php
class ExportCsvJob implements ShouldQueue
{
    // Depends on Livewire component state
    public function __construct(public int $componentId, ...) {}
    
    public function handle()
    {
        // ... complex logic to re-hydrate component and get data ...
    }
}
```

**"After" - Job is simple and decoupled:**
```php
// app/Jobs/GenerateReportJob.php - A single, generic job
use App\Enums\ReportFormat;

class GenerateReportJob implements ShouldQueue
{
    public function __construct(
        public array $data,
        public array $headings,
        public string $fileName,
        public ReportFormat $format,
        public int $userId
    ) {}

    public function handle()
    {
        $formatter = match($this->format) {
            ReportFormat::CSV => new CsvReportFormatter(),
            ReportFormat::PDF => new PdfReportFormatter(),
            // ... etc
        };
        
        $formatter->format($this->data, $this->headings, $this->fileName);
        
        // Notify user
    }
}
```

### Step 5: Fix the Broken Accounting Route
Create a new, dedicated Livewire component for accounting reports that follows the new, clean architecture.

**1. Create the component:**
```bash
php artisan make:livewire Accounting/Reports/Index
```

**2. Update the route:**
```php
// routes/accounting.php
use App\Http\Livewire\Accounting\Reports\Index as AccountingReports;

// Replace the broken route:
// Route::post('{report}/export', ...); 

// With a new, working route to the Livewire component:
Route::get('/reports', AccountingReports::class)->name('accounting.reports');
```

**3. Implement the component:** The new `Accounting\Reports\Index.php` would be built using the same "skinny component" principles as the refactored `SalesDashboard`, calling its own dedicated service (e.g., `AccountingReportService`) to fetch data. This ensures architectural consistency across the entire application.
