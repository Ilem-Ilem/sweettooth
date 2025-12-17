# Reporting System Audit: 06 - Missing Features and Gaps

## 1. Introduction

Beyond fixing existing architectural issues, a key goal of this audit is to identify gaps in the feature set of the reporting system. A mature reporting platform not only provides accurate data but also empowers users with flexibility, customization, and automation. This document outlines the major missing features that, if implemented, would significantly increase the business value and user satisfaction of the SweetTooth application.

---

## 2. Critical User-Facing Feature Gaps

These are features that end-users, such as branch managers and executives, would expect from a modern reporting suite. Their absence limits the system to a static data-provider rather than an interactive analytics tool.

### 2.1. No User-Driven Report Customization or Ad-Hoc Reporting
-   **Gap:** Users are passive consumers of pre-defined reports. There is no functionality that allows a manager to build their own simple report. For example, a user cannot answer a new, specific business question like "Show me sales for only Item X and Item Y in the last 14 days, grouped by day of the week."
-   **Business Value:** Ad-hoc reporting is one of the most powerful tools for data-driven decision-making. It allows users to explore data and find answers to questions that were not anticipated by the developers. This fosters a culture of data curiosity and can uncover valuable business insights.
-   **Implementation Concept:**
    1.  **UI:** Create a "Report Builder" interface where users can select a primary data source (e.g., "Sales," "Inventory"), choose columns to display from a pre-defined list, add filters (e.g., "Date is after X," "Item Name is Y"), and specify grouping options.
    2.  **Backend:** Create a service that can dynamically build an Eloquent query based on the JSON output from the UI builder. This service must have a strong validation layer to prevent arbitrary query execution and only allow filtering/selecting on pre-approved columns.
    3.  **Integration:** The output of this dynamic query could then be passed to the existing (and refactored) `Exportable` system to be delivered as a CSV or displayed on screen.

### 2.2. No User-Managed Report Scheduling and Subscriptions
-   **Gap:** The current system has no interface for a user to schedule a report to be run and delivered automatically. For instance, a branch manager cannot request the "Daily Sales Summary" to be emailed to them every morning at 8 AM. All job scheduling is currently configured at the system level within `app/Console/Kernel.php`.
-   **Business Value:** This automates the workflow of key stakeholders, ensuring they receive critical information consistently without having to manually log in and generate it. It makes the reporting system proactive instead of reactive.
-   **Implementation Concept:**
    1.  **Database:** Create `report_schedules` and `report_subscriptions` tables. `report_schedules` would store the report type, parameters, frequency (as a cron expression), and delivery method (e.g., email). `report_subscriptions` would link users to schedules.
    2.  **UI:** Create a "My Subscriptions" page where users can view, create, or cancel their report schedules.
    3.  **Backend:** Create a single scheduled job (e.g., `ProcessReportSchedules`) that runs every minute. This job would query the `report_schedules` table, determine which reports are due to be run based on their cron expression, and dispatch the corresponding report generation jobs.

### 2.3. Dashboards Lack Interactivity (Cross-Filtering)
-   **Gap:** The sales dashboard displays several charts and tables, but they are static. Clicking on a slice of a pie chart (e.g., "Top Selling Item") does not filter the other visuals on the page to show data related only to that item.
-   **Business Value:** Interactive dashboards allow for a "drill-down" analysis flow. A user can start with a high-level overview and progressively filter the data to investigate anomalies or trends. This is far more engaging and insightful than viewing a series of disconnected charts.
-   **Implementation Concept:**
    1.  **Livewire State:** The dashboard component would need to maintain a state property for filters (e.g., `public $filter_item_id = null;`).
    2.  **Chart Events:** The Highcharts configuration would be updated to emit a Livewire event when a data point is clicked, passing the relevant identifier (e.g., an item ID).
        ```javascript
        // In the Highcharts config
        plotOptions: {
            series: {
                point: {
                    events: {
                        click: function () {
                            // Emits event to the Livewire component
                            @this.emit('chartItemClicked', this.id);
                        }
                    }
                }
            }
        }
        ```
    3.  **Component Listeners:** The Livewire component would have a listener method that updates the filter state and re-runs the data queries, which will then cause all the charts and tables to re-render with the filtered data.

---

## 3. Backend and Administrative Gaps

These are missing features that would empower administrators to manage the reporting system effectively without requiring developer intervention.

### 3.1. No Centralized Report Management Console
-   **Gap:** There is no single place in the application for an administrator to see a list of all available reports, who has access to them, when they were last run, or to manage their settings. This "system" only exists implicitly in the form of code files.
-   **Business Value:** A central console is essential for governance, security, and maintenance. It allows an admin to easily control access, disable problematic reports, and get an overview of the system's health and usage.

### 3.2. Report Structure is Hardcoded
-   **Gap:** The columns, ordering, and formatting of each report are hardcoded in the service classes, jobs, or Blade views. If a manager wants to add a single column to a report or change the date format, it requires a code change and a new deployment.
-   **Business Value:** Decoupling the report structure from the code allows for much faster iteration and customization. Non-technical users could potentially make simple layout changes themselves.

### 3.3. No Health Monitoring for the Reporting System
-   **Gap:** While Laravel provides tools like Horizon for queue monitoring, there is no application-level dashboard dedicated to the health of the reporting system itself. An admin cannot easily answer:
    -   What is the average generation time for the `sales_performance` report?
    -   Which reports are failing most often?
    -   Which users are generating the most reports?
-   **Business Value:** This data is crucial for proactive maintenance, identifying performance bottlenecks, and understanding how the system is being used.

---

## 4. Long-Term Strategic Gap

### 4.1. Absence of a Data Warehouse / Read Replica
-   **Gap:** The system is running complex, analytical queries directly against the live, transactional (OLTP) production database.
-   **Business Value:** As the application scales, this will become a major point of contention. Heavy analytical queries can lock tables and degrade the performance of the entire application for all users, not just those running reports. Separating transactional workloads from analytical workloads is a standard practice in enterprise systems.
-   **Strategic Recommendation:** The company should plan for a future state that includes one of the following:
    1.  **Read Replica Database:** A simple, intermediate step where all reporting queries are directed to a read-only copy of the production database. This immediately isolates the main application from performance degradation caused by slow reports.
    2.  **ETL and Data Warehouse:** A more advanced, long-term solution. An Extract, Transform, Load (ETL) process would run periodically (e.g., overnight) to pull data from the production database and reshape it into a schema optimized for analytics in a separate data warehouse. All reports would then run against this warehouse. This is the most scalable and performant solution for heavy-duty business intelligence.
