# Reporting System Audit: 08 - Prioritized Fix Roadmap

## 1. Introduction

This document translates the extensive technical findings of this audit into a strategic, actionable roadmap. The purpose of this roadmap is to provide the development team with a clear, prioritized sequence of work that focuses on stabilizing the system first, then unifying the architecture, and finally enriching the feature set. Following this phased approach will ensure that the most critical risks are mitigated immediately and that subsequent work is built upon a solid foundation.

## 2. Priority Level Definitions

Each task in this roadmap is assigned a priority level to guide decision-making and resource allocation.

-   **P0 - Critical:** Must be fixed immediately. These are issues that cause system crashes, result in major data corruption or leaks, or represent severe, exploitable security vulnerabilities. The stability of the application depends on these fixes.
-   **P1 - High:** Should be fixed in the near term (next 1-2 sprints). These issues significantly degrade performance under moderate load, introduce major architectural inconsistencies that block future development, or represent a high-priority security risk.
-   **P2 - Medium:** Should be addressed in the medium term. These items represent significant technical debt, violations of best practices that make the code hard to maintain, or important missing features that are not immediately critical.
-   **P3 - Low:** Can be addressed when time permits. These are minor inconsistencies, "nice-to-have" features, or long-term strategic improvements that can be deferred without significant short-term impact.

---

## 3. The Implementation Roadmap

The refactoring effort is broken down into four distinct phases, designed to be executed sequentially.

### Phase 1: Stabilize the Foundation (Immediate Priority)
**Goal:** Address the most critical performance and security issues to prevent system failure and data leaks.

-   #### Task 1.1: Eliminate In-Memory Data Aggregation (Priority: P0)
    -   **Objective:** Refactor all reporting queries to perform aggregations at the database level.
    -   **Affected Components:**
        -   `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php` (`profitAnalysis` method)
        -   `app/Services/Reports/SalesPerformanceReportService.php` (`generateReportData` method)
    -   **Justification:** This is the most severe performance bottleneck and will cause system-wide instability as data volume grows. Fixing this is essential for application stability.
    -   **Action:** Rewrite the data-fetching logic using Eloquent's `selectRaw`, `groupBy`, `sum`, etc., as detailed in `04-performance-and-scalability-risks.md`.

-   #### Task 1.2: Implement Automatic Tenant Scoping (Priority: P0)
    -   **Objective:** Prevent accidental cross-branch data leaks by automating the `branch_id` query constraint.
    -   **Action:** Create a `BranchScope` global scope and apply it to all tenant-specific models (e.g., `Sale`, `Production`, `Item`).
    -   **Justification:** The current manual approach is a ticking time bomb. A single forgotten `where` clause can lead to a critical data breach between branches. This automated solution is non-negotiable for a multi-tenant application.

-   #### Task 1.3: Fix Broken Accounting Export (Priority: P1)
    -   **Objective:** Repair the non-functional export feature in the accounting module.
    -   **Action:** Remove the broken route from `routes/accounting.php`. Create a new `Accounting\Reports\Index` Livewire component and route to it.
    -   **Justification:** Fixes a blatant bug and restores user trust. This component can initially be a simple placeholder, but the route must be fixed.

### Phase 2: Unify the Architecture (Near-Term Priority)
**Goal:** Refactor the core architecture around a single, unified service to eliminate fragmentation and technical debt.

-   #### Task 2.1: Establish Core Reporting Service and Primitives (Priority: P1)
    -   **Objective:** Create the foundational pieces of the new architecture.
    -   **Action:**
        1.  Create the `App\Enums\ReportFormat` enum.
        2.  Create the initial `ReportingService` facade/class structure.
        3.  Create the generic `GenerateReportJob` and the required `Formatter` classes.
    -   **Justification:** This lays the groundwork for all subsequent refactoring and ensures a consistent approach.

-   #### Task 2.2: Refactor the Sales Dashboard as a Pilot Project (Priority: P1)
    -   **Objective:** Migrate the first and most complex reporting component to the new architecture.
    -   **Action:** Fully refactor the `Analytics/Index` component as detailed in `07-recommended-refactorings.md`. This includes creating the `SalesAnalyticsService` and making the component "skinny."
    -   **Justification:** Using the sales dashboard as the pilot will prove the viability of the new architecture and provide a clear template for all other components.

-   #### Task 2.3: Purge All Other Manual Export Implementations (Priority: P2)
    -   **Objective:** Remove all remaining inconsistent reporting code.
    -   **Action:** Conduct a project-wide search for any other components that implement manual CSV exports or other one-off reporting logic and refactor them to use the new `ReportingService`.
    -   **Justification:** Completes the unification process and pays down significant technical debt.

### Phase 3: Enhance and Secure (Medium-Term Priority)
**Goal:** Build upon the stable foundation to add crucial security and performance-enhancing features.

-   #### Task 3.1: Implement a Centralized Caching Strategy (Priority: P2)
    -   **Objective:** Reduce redundant report generation for non-volatile data.
    -   **Action:** Integrate `Cache::remember()` logic into the new `ReportingService` to cache the results of data-gathering methods.
    -   **Justification:** Drastically improves performance for frequently accessed reports and reduces database load.

-   #### Task 3.2: Implement a Centralized Audit Trail (Priority: P2)
    -   **Objective:** Log all report generation and access for compliance and security.
    -   **Action:** Create the `ReportViewed` event and `LogReportAccess` listener as described in the security audit. Trigger this event whenever a report is generated or viewed.
    -   **Justification:** A critical feature for security forensics and meeting compliance requirements.

-   #### Task 3.3: Introduce Granular, Report-Level Permissions (Priority: P2)
    -   **Objective:** Allow for more fine-grained access control to sensitive reports.
    -   **Action:** Implement a `view_report_{slug}` permission scheme and enforce it within the `ReportingService`.
    -   **Justification:** Provides administrators with the flexibility to control access to sensitive information on a need-to-know basis.

### Phase 4: Future-Proofing and Feature Enrichment (Long-Term)
**Goal:** Add high-value, user-facing features and consider long-term scalability.

-   #### Task 4.1: Build User-Facing Report Subscription UI (Priority: P3)
    -   **Objective:** Allow users to schedule their own recurring reports.
    -   **Action:** Implement the `report_schedules` and `report_subscriptions` tables and the corresponding UI.

-   #### Task 4.2: Investigate Read Replica / Data Warehouse (Priority: P3)
    -   **Objective:** Develop a long-term strategy to isolate analytical workloads from the production database.
    -   **Action:** Begin a research spike to evaluate the cost and complexity of setting up a read replica database as a first step.

## 4. Conclusion

This roadmap provides a logical and incremental path to transform the reporting system from a fragile and inconsistent set of components into a robust, scalable, and secure platform. By prioritizing stability (Phase 1) before unification (Phase 2), the team can ensure that the most critical risks are mitigated first. This structured approach will reduce technical debt, improve developer velocity, and ultimately deliver more value to the business.
