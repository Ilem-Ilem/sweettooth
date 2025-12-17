# Reporting System Audit: 05 - Security and Compliance Concerns

## 1. Introduction

Reporting systems are a prime target for security threats as they aggregate and present what is often the most sensitive data within an organization: financial figures, sales performance, customer data, and operational metrics. A breach of the reporting system can lead to significant financial loss, competitive disadvantage, and reputational damage. This document assesses the security posture of the SweetTooth reporting system, identifying key vulnerabilities and compliance gaps.

---

## 2. Critical Concern: Weak Multi-Tenancy Enforcement

The application is designed to support multiple branches, making data segregation (tenancy) a critical security requirement. A failure in tenancy enforcement could allow one branch to view the sensitive operational data of another.

### 2.1. Inconsistent Use of `current_branch_id()`
-   **Location(s):**
    -   `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`
    -   `app/Services/Reports/SalesPerformanceReportService.php`
-   **Analysis:** The system relies on the `current_branch_id()` helper function to scope database queries. While this is a valid approach, its enforcement is manual and inconsistent. A developer must remember to add `->where('branch_id', current_branch_id())` to every single query related to reporting.
-   **Vulnerability:** The risk of human error is extremely high. If a developer forgets to apply this `where` clause in just one of the many reporting queries, it will lead to a **catastrophic data leak**, mixing data from all branches in the generated report. This is especially risky in complex queries with multiple joins.
-   **Impact:** **CRITICAL**. This vulnerability would break the data isolation between branches, which is a fundamental requirement of the system. It could lead to incorrect business decisions and severe trust issues with branch managers.

**Corrective Solution: Automatic Tenant Scoping**
This manual approach is fragile. The recommended solution is to use an automatic query scoping mechanism. Laravel provides "Global Scopes" for this exact purpose.

**Conceptual "After" Snippet (Global Scope):**
```php
// app/Models/Scopes/BranchScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (function_exists('current_branch_id') && current_branch_id()) {
            $builder->where($model->getTable().'.branch_id', current_branch_id());
        }
    }
}

// In a model like app/Models/Sale.php
protected static function booted()
{
    // This will automatically apply the where clause to EVERY query for this model
    static::addGlobalScope(new BranchScope);
}
```
By implementing a global scope on all branch-specific models (`Sale`, `Production`, etc.), the tenancy `where` clause is applied automatically and cannot be accidentally forgotten.

---

## 3. Authorization and Access Control

While the application uses the `spatie/laravel-permission` package, its application within the reporting system needs to be scrutinized.

### 3.1. Lack of Granular Report Permissions
-   **Analysis:** Access control appears to be coarse-grained, likely based on high-level roles (e.g., `branch-manager`, `super-admin`). The system seems to lack a mechanism for defining permissions on a per-report basis. For example, a `branch-manager` might need to see a `sales-summary-report` but should be denied access to a more sensitive `profitability-and-cost-analysis-report`.
-   **Impact:** **MEDIUM**. The inability to set fine-grained permissions forces the business to grant all-or-nothing access. This is inflexible and increases the "blast radius" if a user account is compromised, as that user will have access to all reports available to their role.

**Corrective Solution: Report-Level Permissions**
A new permission structure should be introduced, for example: `view_report_{report_slug}` (e.g., `view_report_sales_performance`).

```php
// In a Livewire component's mount() or render() method
public function mount()
{
    // Check permission at the component level
    if (!auth()->user()->can('view_report_sales_analytics')) {
        abort(403);
    }
    // ...
}
```

### 3.2. Potential for Insecure Direct Object References (IDOR)
-   **Location:** `app/Models/DepartmentReport.php`
-   **Analysis:** If a user can view a report via a URL like `/reports/view/123` (where `123` is the ID of a record in `department_reports`), the system must perform two checks:
    1.  Does the user have the permission to view reports at all?
    2.  Does this specific report (`123`) belong to the user's branch?
    The current manual tenancy enforcement makes it highly likely that the second check could be missed.
-   **Impact:** **HIGH**. An attacker could potentially iterate through numeric IDs (`/reports/view/1`, `/reports/view/2`, etc.) and gain access to reports generated by other branches or other users.

---

## 4. Compliance and Auditing Gaps

For any system handling sensitive financial data, a clear audit trail is a compliance requirement, not an optional feature.

### 4.1. No Audit Trail for Report Access
-   **Analysis:** The system does not appear to log who generates or accesses which reports and when. The `department_reports` table might store `created_by`, but this only tells us who initiated the generation, not everyone who later viewed the report.
-   **Impact:** **HIGH**. In the event of a data leak, it would be impossible to perform a forensic analysis to determine who accessed the sensitive data. This is a major gap for compliance standards like GDPR, SOX, or PCI-DSS, which require accountability for data access.

**Corrective Solution: Implement an Audit Log**
A dedicated `audit_trails` table should be created. A listener for a new `ReportViewed` event could log the necessary information.

```php
// Create a new event
class ReportViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public User $user, public DepartmentReport $report) {}
}

// Create a listener
class LogReportAccess
{
    public function handle(ReportViewed $event)
    {
        AuditTrail::create([
            'user_id' => $event->user->id,
            'action' => 'viewed_report',
            'subject_id' => $event->report->id,
            'subject_type' => DepartmentReport::class,
            'timestamp' => now(),
        ]);
    }
}

// Dispatch the event when a report is accessed
ReportViewed::dispatch(auth()->user(), $report);
```

---

## 5. Data Exposure Risks

### 5.1. Risk of Over-Exposing Data in Livewire Components
-   **Location:** All Livewire reporting components.
-   **Analysis:** Livewire component public properties are automatically serialized and sent to the frontend as JSON. If a developer accidentally makes a full Eloquent model a public property (e.g., `public User $user;`), all of that model's attributes, including potentially sensitive ones not needed by the UI, would be exposed in the browser.
-   **Impact:** **MEDIUM**. This could lead to the unintentional leakage of sensitive data to the client-side, where it could be inspected by a malicious user.

**Corrective Solution: Data Transfer Objects (DTOs)**
Instead of passing full Eloquent models as public properties, use dedicated, plain PHP objects (DTOs) or simple arrays that contain only the data necessary for the view. This ensures that only explicitly defined data ever leaves the server.
