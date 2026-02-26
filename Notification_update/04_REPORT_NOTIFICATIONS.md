# Report Notifications - User-Friendly Redesign

## Overview
These notifications handle the reporting department workflow including report compilation, approval, rejection, and submission to management.

---

## 1. ReportCompiledNotification

**File:** `app/Notifications/ReportCompiledNotification.php`

**Trigger:** When a report has been compiled and is ready for review.

**Recipients:** Report reviewers, department heads, management.

### Current Output (Problem)
```
Type: report_compiled
Message: Compiled report "Monthly Sales Summary" is ready.
Compiled report id: 45
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'report_compiled',
    'title' => 'Report Ready for Review',
    'message' => 'The compiled report has been prepared and is ready for your review.',
    'summary' => 'Monthly Sales Summary - January 2026',
    'context' => [
        'report_name' => 'Monthly Sales Summary',
        'report_period' => 'January 2026',
        'report_type' => 'Sales Report',
        'compiled_by' => 'John Doe',
        'compiler_role' => 'Reporting Analyst',
        'branch' => 'SweetTooth Port Harcourt',
        'compiled_at' => 'Feb 25, 2026 09:00 AM',
    ],
    'action_url' => '/branch-dashboard/reporting/compiled/view?id=45&b_id=xxx',
    'action_text' => 'View Report',
    // Internal use only:
    'compiled_report_id' => '45',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Report Ready for Review: Monthly Sales Summary

Hi [Recipient Name],

A compiled report is ready for your review.

Report Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Report Name:       Monthly Sales Summary
Report Type:       Sales Report
Period Covered:    January 2026
Compiled By:       John Doe (Reporting Analyst)
Branch:            SweetTooth Port Harcourt
Compiled At:       Feb 25, 2026 at 09:00 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Please review the report and provide your approval or feedback.

[View Report →]

Best regards,
SweetTooth Reporting System
```

---

## 2. ReportApprovedNotification

**File:** `app/Notifications/ReportApprovedNotification.php`

**Trigger:** When a department report is approved by a reviewer.

**Recipients:** Report compiler, department head.

### Current Output (Problem)
```
Type: report_approved
Message: Report "Monthly Sales Summary" approved.
Department report id: 78
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'report_approved',
    'title' => 'Report Approved',
    'message' => 'Your report has been approved and will proceed to the next stage.',
    'summary' => 'Monthly Sales Summary approved by Sarah Johnson',
    'context' => [
        'report_name' => 'Monthly Sales Summary',
        'report_period' => 'January 2026',
        'report_type' => 'Sales Report',
        'approved_by' => 'Sarah Johnson',
        'approver_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'approved_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/reporting/report/view?id=78&b_id=xxx',
    'action_text' => 'View Report',
    // Internal use only:
    'department_report_id' => '78',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ Report Approved: Monthly Sales Summary

Hi [Recipient Name],

Great news! Your report has been approved.

Report Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Report Name:       Monthly Sales Summary
Report Type:       Sales Report
Period Covered:    January 2026
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Approval Information:
  • Approved By:   Sarah Johnson
  • Role:          Finance Manager
  • Approved At:   Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

The report will now proceed to the next stage of the workflow.

[View Report →]

Best regards,
SweetTooth Reporting System
```

---

## 3. ReportRejectedNotification

**File:** `app/Notifications/ReportRejectedNotification.php`

**Trigger:** When a department report is rejected and needs revision.

**Recipients:** Report compiler.

### Current Output (Problem)
```
Type: report_rejected
Message: Report "Monthly Sales Summary" rejected.
Department report id: 78
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'report_rejected',
    'title' => 'Report Needs Revision',
    'message' => 'Your report requires revisions before it can be approved.',
    'summary' => 'Review feedback from Sarah Johnson',
    'context' => [
        'report_name' => 'Monthly Sales Summary',
        'report_period' => 'January 2026',
        'report_type' => 'Sales Report',
        'rejected_by' => 'Sarah Johnson',
        'rejector_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'rejected_at' => 'Feb 25, 2026 02:30 PM',
        'rejection_reason' => 'Please include regional breakdown for sales data and verify Q4 figures.',
    ],
    'action_url' => '/branch-dashboard/reporting/report/view?id=78&b_id=xxx',
    'action_text' => 'View Feedback',
    // Internal use only:
    'department_report_id' => '78',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ⊘ Action Required: Report Needs Revision

Hi [Recipient Name],

Your report has been reviewed and requires revisions.

Report Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Report Name:       Monthly Sales Summary
Report Type:       Sales Report
Period Covered:    January 2026
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Review Information:
  • Reviewed By:   Sarah Johnson
  • Role:          Finance Manager
  • Reviewed At:   Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

Feedback:
┌─────────────────────────────────────────────────────────┐
│ Please include regional breakdown for sales data and    │
│ verify Q4 figures.                                      │
└─────────────────────────────────────────────────────────┘

Next Steps:
  1. Review the feedback above
  2. Make the necessary revisions
  3. Resubmit the report for approval

[View Feedback & Revise →]

Best regards,
SweetTooth Reporting System
```

---

## 4. ReportSentToMDNotification

**File:** `app/Notifications/ReportSentToMDNotification.php`

**Trigger:** When a compiled report is sent to the Managing Director.

**Recipients:** Managing Director, relevant stakeholders.

### Current Output (Problem)
```
Type: report_sent_to_md
Message: Compiled report "Monthly Sales Summary" sent to MD.
Compiled report id: 45
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'report_sent_to_md',
    'title' => 'Report Submitted to Managing Director',
    'message' => 'The compiled report has been submitted to the Managing Director for final review.',
    'summary' => 'Monthly Sales Summary - January 2026',
    'context' => [
        'report_name' => 'Monthly Sales Summary',
        'report_period' => 'January 2026',
        'report_type' => 'Sales Report',
        'compiled_by' => 'John Doe',
        'submitted_by' => 'Sarah Johnson',
        'submitter_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 25, 2026 04:00 PM',
    ],
    'action_url' => '/branch-dashboard/reporting/compiled/view?id=45&b_id=xxx',
    'action_text' => 'View Report',
    // Internal use only:
    'compiled_report_id' => '45',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Report Submitted to MD: Monthly Sales Summary

Hi [Recipient Name],

A compiled report has been submitted to the Managing Director.

Report Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Report Name:       Monthly Sales Summary
Report Type:       Sales Report
Period Covered:    January 2026
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Workflow Progress:
  • Compiled By:   John Doe (Reporting Analyst)
  • Submitted By:  Sarah Johnson (Finance Manager)
  • Submitted At:  Feb 25, 2026 at 04:00 PM

The report is now awaiting final review by the Managing Director.

[View Report →]

Best regards,
SweetTooth Reporting System
```

---

## Blade Template Integration

### Report-Specific Display Components

```blade
{{-- Report Period Badge --}}
@if(!empty($notification->data['context']['report_period']))
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ $notification->data['context']['report_period'] }}
    </span>
@endif

{{-- Report Type Badge --}}
@if(!empty($notification->data['context']['report_type']))
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
        {{ $notification->data['context']['report_type'] }}
    </span>
@endif

{{-- Rejection Reason Display --}}
@if(!empty($notification->data['context']['rejection_reason']))
    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <div class="text-xs font-semibold text-amber-700 dark:text-amber-400">
                    Reviewer Feedback
                </div>
                <div class="text-sm text-amber-600 dark:text-amber-300 mt-1">
                    {{ $notification->data['context']['rejection_reason'] }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Workflow Progress Indicator --}}
<div class="mt-4">
    <div class="flex items-center justify-between text-xs">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="text-zinc-600 dark:text-zinc-400">Compiled</span>
        </div>
        <div class="flex-1 mx-2 h-0.5 bg-green-500"></div>
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full 
                @if($notification->data['type'] === 'report_approved') bg-green-500 text-white
                @elseif($notification->data['type'] === 'report_rejected') bg-red-500 text-white
                @else bg-zinc-200 dark:bg-zinc-700 text-zinc-400
                @endif flex items-center justify-center">
                @if($notification->data['type'] === 'report_approved')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @elseif($notification->data['type'] === 'report_rejected')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <span class="text-[10px]">2</span>
                @endif
            </div>
            <span class="text-zinc-600 dark:text-zinc-400">
                @if($notification->data['type'] === 'report_approved') Approved
                @elseif($notification->data['type'] === 'report_rejected') Rejected
                @else Review
                @endif
            </span>
        </div>
        <div class="flex-1 mx-2 h-0.5 
            @if($notification->data['type'] === 'report_sent_to_md') bg-green-500
            @else bg-zinc-200 dark:bg-zinc-700
            @endif"></div>
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full 
                @if($notification->data['type'] === 'report_sent_to_md') bg-green-500 text-white
                @else bg-zinc-200 dark:bg-zinc-700 text-zinc-400
                @endif flex items-center justify-center">
                @if($notification->data['type'] === 'report_sent_to_md')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <span class="text-[10px]">3</span>
                @endif
            </div>
            <span class="text-zinc-600 dark:text-zinc-400">MD Review</span>
        </div>
    </div>
</div>
```

---

## Report Type Mapping

| Report Category | User-Friendly Labels |
|-----------------|----------------------|
| `sales` | Sales Report |
| `inventory` | Inventory Report |
| `production` | Production Report |
| `finance` | Financial Report |
| `hr` | HR Report |
| `compliance` | Compliance Report |
| `management` | Management Report |

---

## Report Period Formatting Examples

```php
// Helper function for formatting report periods
function formatReportPeriod($report): string
{
    return match ($report->period_type) {
        'daily' => $report->period_start->format('M d, Y'),
        'weekly' => $report->period_start->format('M d') . ' - ' . $report->period_end->format('M d, Y'),
        'monthly' => $report->period_start->format('F Y'),
        'quarterly' => 'Q' . ceil($report->period_start->month / 3) . ' ' . $report->period_start->format('Y'),
        'yearly' => $report->period_start->format('Y'),
        default => $report->period_start->format('M d, Y'),
    };
}
```

---

## Implementation Checklist

- [ ] Update `ReportCompiledNotification.php`
- [ ] Update `ReportApprovedNotification.php`
- [ ] Update `ReportRejectedNotification.php`
- [ ] Update `ReportSentToMDNotification.php`
- [ ] Add report period formatting helper
- [ ] Add workflow progress component to blade
- [ ] Add rejection reason styling
- [ ] Test all four notification types
- [ ] Verify email rendering
- [ ] Test with various report periods (daily, weekly, monthly, quarterly)
