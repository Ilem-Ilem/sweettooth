# Incomplete and Inconsistent Features

This document outlines features that are either incomplete, inconsistent with the documentation, or implemented in a confusing manner.

## 1. Cash Flow Statement
- **Status:** Partially Implemented
- **Files:**
    - `app/Livewire/BranchDashboard/Accounting/Report/CashFlowStatementReport.php`
    - `app/Services/CashFlowStatementService.php`
- **Issue:** While both the Livewire component and the service for the Cash Flow Statement exist, it's not clear if the feature is fully functional and integrated. The documentation is inconsistent about its completion status.
- **Recommendation:** The functionality of the Cash Flow Statement report needs to be verified. If it is complete, the documentation should be updated to reflect this. If not, the feature should be completed.

## 2. Placeholder Production Reports
- **Status:** Not Implemented
- **Files:**
    - `app/Livewire/BranchDashboard/Production/Reports/CapacityPlanning/Index.php`
    - `app/Livewire/BranchDashboard/Production/Reports/CostAnalysis/Index.php`
    - `app/Livewire/BranchDashboard/Production/Reports/IngredientUtilization/Index.php`
    - `app/Livewire/BranchDashboard/Production/Reports/PipelineStatus/Index.php`
    - `app/Livewire/BranchDashboard/Production/Reports/RecipePerformance/Index.php`
    - `app/Livewire/BranchDashboard/Production/Reports/ShiftSummary/Index.php`
- **Issue:** The production reports module contains several components that are placeholders. They display a "Coming Soon" message and render a generic placeholder view.
- **Recommendation:** These reports need to be implemented as per the project requirements.

## 3. Confusing Dashboard Components
- **Status:** Implemented, but confusing
- **Files:**
    - `app/Livewire/BranchDashboard/Accounting/Index.php`
    - `app/Livewire/BranchDashboard/Accounting/Overview.php`
- **Issue:** There are two dashboard-like components in the `Accounting` module: `Index.php` and `Overview.php`.
    - `Index.php` provides a financial summary (Trial Balance, Income Statement, Balance Sheet).
    - `Overview.php` provides a system health/status summary (GL entry status, posting status).
    - The documentation is not clear on the purpose of each, or how they should be used together. The naming is also not descriptive.
- **Recommendation:**
    - Rename `Index.php` to `FinancialDashboard.php`.
    - Rename `Overview.php` to `AccountingStatusDashboard.php`.
    - Create a main `Dashboard.php` that either combines the functionality of both or provides links to these two specialized dashboards.
    - Update the documentation to clarify the purpose and usage of each dashboard.

## 4. Undocumented `PostingStatusMonitor.php`
- **Status:** Implemented, but undocumented
- **File:** `app/Livewire/BranchDashboard/Accounting/PostingStatusMonitor.php`
- **Issue:** This component provides a dedicated interface for monitoring the status of transactions (sales, purchases, payments, adjustments) and their GL posting status. This is a useful feature, but it is not mentioned in the high-level documentation.
- **Recommendation:** The documentation should be updated to include this feature, explaining its purpose and how to use it.
