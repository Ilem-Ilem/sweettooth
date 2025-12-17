# Reporting System: Current Architecture

The reporting and data export functionality is not a single, cohesive system but rather a collection of disparate components and approaches. This document outlines the three main sub-systems currently in place.

## 1. The `Exportable` Trait System (PDF & Excel)

This is the most formalized part of the reporting architecture, designed for generating PDF and Excel files from Livewire components.

*   **Core Logic:** `app/Traits/Exportable.php`
    *   This trait is intended to be used within Livewire components.
    *   It provides a public `export()` method that acts as the main entry point.
    *   It supports two modes: immediate (`performImmediateExport`) and queued (`queueExports`).

*   **Data Generation:** `app/Services/Reports/ReportService.php`
    *   This abstract class defines a contract for report data generation services.
    *   Concrete implementations like `app/Services/Reports/SalesPerformanceReportService.php` are responsible for fetching and processing the raw data.
    *   The service saves the generated report data to the `department_reports` table.

*   **Asynchronous Processing:**
    *   Jobs like `app/Jobs/ExportExcelJob.php` and `app/Jobs/ExportPDFJob.php` handle the actual file generation in the background.
    *   They rely on libraries like `maatwebsite/excel` and `barryvdh/laravel-dompdf`.

*   **Architectural Flow:**
    1.  A user clicks an "Export" button in a Livewire component (e.g., `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`).
    2.  The component's `exportData()` method calls the `export()` method from the `Exportable` trait.
    3.  The trait either generates the file directly or dispatches a job to the queue.
    4.  If queued, the job (`ExportExcelJob` or `ExportPDFJob`) takes over, generates the file, and (typically) notifies the user.

*   **Key Issue:** The `queueExports` method in `Exportable.php` serializes the entire data collection passed to it. For large reports, this creates a massive job payload that can overload the queue and database.

## 2. Manual CSV Export System

This system exists entirely within individual Livewire components and is completely separate from the `Exportable` trait.

*   **Core Logic:** Implemented directly within Livewire component methods (e.g., `exportToCSV` in `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`).

*   **Architectural Flow:**
    1.  A user clicks a "CSV Export" button in a Livewire component.
    2.  The corresponding method (e.g., `exportToCSV`) is triggered.
    3.  The method manually constructs a CSV string by iterating over a data collection.
    4.  It then returns a `response()->streamDownload()` to send the CSV file directly to the browser.

*   **Key Issues:**
    *   **No Queuing:** This process is entirely synchronous, which will lead to browser timeouts and server strain for any non-trivial amount of data.
    *   **Code Duplication:** The logic for generating CSVs is copied and pasted, or slightly modified, in every component that needs it.
    *   **Inconsistency:** It completely bypasses the `Exportable` trait and its associated services, leading to a separate, unmanaged reporting flow.

## 3. Frontend Highcharts Export System

This system is a client-side feature provided by the Highcharts JavaScript library and is independent of the Laravel backend.

*   **Core Logic:** `resources/views/livewire/branch-dashboard/sales-dashboard/analytics/index.blade.php`
    *   This view includes the Highcharts exporting module (`exporting.js`).
    *   When a chart is rendered, Highcharts automatically adds a "hamburger" menu to the top-right corner.

*   **Architectural Flow:**
    1.  The user interacts with a chart on the page.
    2.  The user clicks the chart's context menu and selects an export format (e.g., PNG, JPEG, SVG, or "View Data Table").
    3.  All processing happens entirely in the user's browser, using the data already present in the chart. The backend is not involved.

*   **Key Issues:**
    *   **Data Mismatch:** The data exported from a chart may not perfectly match the data exported from the backend systems, as it only includes the data points visible in the chart itself.
    *   **Inconsistent User Experience:** Users are presented with two different UIs for exporting data from the same screen (the Livewire buttons and the Highcharts menu), which can be confusing.
    *   **Lack of Control:** This is a third-party feature with limited server-side control or integration into the application's main reporting/auditing flow.
