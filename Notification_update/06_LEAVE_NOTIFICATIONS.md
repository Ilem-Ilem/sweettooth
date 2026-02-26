# Leave Application Notifications - User-Friendly Redesign

## Overview
These notifications handle the leave management workflow including leave application submission, approval, and rejection.

---

## 1. LeaveApplicationSubmitted

**File:** `app/Notifications/LeaveApplicationSubmitted.php`

**Trigger:** When an employee submits a new leave application.

**Recipients:** HR managers, department heads, approvers.

### Current Output (Problem)
```
Type: leave_application_submitted
Message: New leave application submitted.
Employee: John Doe
Dates: 2026-03-01 to 2026-03-07
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'leave_application_submitted',
    'title' => 'New Leave Application Submitted',
    'message' => 'A new leave application requires your approval.',
    'summary' => 'John Doe - Annual Leave (Mar 01 - Mar 07, 2026)',
    'context' => [
        'application_number' => 'LEAVE-2026-0042',
        'employee_name' => 'John Doe',
        'employee_number' => 'EMP-2026-0042',
        'position' => 'Production Assistant',
        'department' => 'Production',
        'leave_type' => 'Annual Leave',
        'start_date' => 'Mar 01, 2026',
        'end_date' => 'Mar 07, 2026',
        'duration' => '7 days',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 25, 2026 09:00 AM',
        'reason' => 'Family vacation',
    ],
    'action_url' => '/branch-dashboard/leave/approve?b_id=xxx',
    'action_text' => 'Review Application',
    // Internal use only:
    'leave_application_id' => '789',
    'employee_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Leave Application for Approval: John Doe

Hi [Recipient Name],

A new leave application has been submitted and requires your approval.

Application Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Application No:    LEAVE-2026-0042
Employee:          John Doe (EMP-2026-0042)
Position:          Production Assistant
Department:        Production
Leave Type:        Annual Leave
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Leave Period:
  • Start Date:    Mar 01, 2026 (Sunday)
  • End Date:      Mar 07, 2026 (Saturday)
  • Duration:      7 days
  • Return Date:   Mar 08, 2026 (Sunday)

Reason:
  Family vacation

Branch: SweetTooth Port Harcourt
Submitted: Feb 25, 2026 at 09:00 AM

[Review Application →]

Best regards,
SweetTooth HR System
```

---

## 2. LeaveApplicationApproved

**File:** `app/Notifications/LeaveApplicationApproved.php`

**Trigger:** When a leave application is approved.

**Recipients:** The employee who applied for leave.

### Current Output (Problem)
```
Type: leave_application_approved
Message: Leave application LEAVE-2026-0042 approved.
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'leave_application_approved',
    'title' => 'Leave Application Approved',
    'message' => 'Your leave application has been approved.',
    'summary' => 'Annual Leave: Mar 01 - Mar 07, 2026 (7 days)',
    'context' => [
        'application_number' => 'LEAVE-2026-0042',
        'leave_type' => 'Annual Leave',
        'start_date' => 'Mar 01, 2026',
        'end_date' => 'Mar 07, 2026',
        'duration' => '7 days',
        'return_date' => 'Mar 08, 2026',
        'approved_by' => 'Sarah Johnson',
        'approver_role' => 'HR Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'approved_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/leave/my-leaves?b_id=xxx',
    'action_text' => 'View My Leaves',
    // Internal use only:
    'leave_application_id' => '789',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ Leave Application Approved: LEAVE-2026-0042

Hi John Doe,

Great news! Your leave application has been approved.

Approved Leave Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Application No:    LEAVE-2026-0042
Leave Type:        Annual Leave
Duration:          7 days
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Leave Period:
  • Start Date:    Mar 01, 2026 (Sunday)
  • End Date:      Mar 07, 2026 (Saturday)
  • Return to Work: Mar 08, 2026 (Sunday)

Approval Information:
  • Approved By:   Sarah Johnson (HR Manager)
  • Approved At:   Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

Before You Go:
  ✓ Complete pending tasks
  ✓ Hand over responsibilities to your cover
  ✓ Set up out-of-office email response

Enjoy your leave!

[View My Leaves →]

Best regards,
SweetTooth HR System
```

---

## 3. LeaveApplicationRejected

**File:** `app/Notifications/LeaveApplicationRejected.php`

**Trigger:** When a leave application is rejected.

**Recipients:** The employee who applied for leave.

### Current Output (Problem)
```
Type: leave_application_rejected
Message: Leave application LEAVE-2026-0042 rejected.
Reason: Insufficient leave balance
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'leave_application_rejected',
    'title' => 'Leave Application Not Approved',
    'message' => 'Your leave application was not approved. Please review the feedback.',
    'summary' => 'Review feedback from Sarah Johnson',
    'context' => [
        'application_number' => 'LEAVE-2026-0042',
        'leave_type' => 'Annual Leave',
        'requested_dates' => 'Mar 01 - Mar 07, 2026',
        'duration' => '7 days',
        'rejected_by' => 'Sarah Johnson',
        'rejector_role' => 'HR Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'rejected_at' => 'Feb 25, 2026 02:30 PM',
        'rejection_reason' => 'Insufficient leave balance. You have 3 days remaining. Please adjust your request or consider unpaid leave.',
        'remaining_balance' => '3 days',
    ],
    'action_url' => '/branch-dashboard/leave/my-leaves?b_id=xxx',
    'action_text' => 'View Application',
    // Internal use only:
    'leave_application_id' => '789',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ⊘ Leave Application Update: LEAVE-2026-0042

Hi John Doe,

Your leave application has been reviewed. Unfortunately, it could not be 
approved at this time.

Application Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Application No:    LEAVE-2026-0042
Leave Type:        Annual Leave
Requested Period:  Mar 01 - Mar 07, 2026 (7 days)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Review Information:
  • Reviewed By:   Sarah Johnson (HR Manager)
  • Reviewed At:   Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

Feedback:
┌─────────────────────────────────────────────────────────┐
│ Insufficient leave balance. You have 3 days remaining.  │
│ Please adjust your request or consider unpaid leave.    │
└─────────────────────────────────────────────────────────┘

Your Leave Balance:
  • Annual Leave Remaining:  3 days
  • Total Entitled:          14 days
  • Already Taken:           11 days

Next Steps:
  1. Review your available leave balance
  2. Consider adjusting your requested dates
  3. Submit a new application or contact HR for assistance

[View My Leaves →]

Best regards,
SweetTooth HR System
```

---

## Blade Template Integration

### Leave-Specific Display Components

```blade
{{-- Leave Type Badge --}}
@if(!empty($notification->data['context']['leave_type']))
    @php
        $leaveTypeStyles = [
            'Annual Leave' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'Sick Leave' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'Maternity Leave' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
            'Paternity Leave' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
            'Compassionate Leave' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
            'Unpaid Leave' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        ];
        $style = $leaveTypeStyles[$notification->data['context']['leave_type']] ?? $leaveTypeStyles['Annual Leave'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $style }}">
        {{ $notification->data['context']['leave_type'] }}
    </span>
@endif

{{-- Date Range Display --}}
@if(!empty($notification->data['context']['start_date']) && !empty($notification->data['context']['end_date']))
    <div class="mt-2 flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>
            {{ $notification->data['context']['start_date'] }}
            <span class="text-zinc-400">to</span>
            {{ $notification->data['context']['end_date'] }}
        </span>
        @if(!empty($notification->data['context']['duration']))
            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-full">
                {{ $notification->data['context']['duration'] }}
            </span>
        @endif
    </div>
@endif

{{-- Duration Badge --}}
@if(!empty($notification->data['context']['duration']))
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $notification->data['context']['duration'] }}
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
                <div class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-1">
                    Reviewer Feedback
                </div>
                <div class="text-sm text-amber-600 dark:text-amber-300">
                    {{ $notification->data['context']['rejection_reason'] }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Leave Balance Display --}}
@if(!empty($notification->data['context']['remaining_balance']))
    <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-2">
            Your Leave Balance
        </div>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $notification->data['context']['remaining_balance'] }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    Days Remaining
                </div>
            </div>
            <div class="w-16 h-16 relative">
                {{-- Optional: Add a circular progress indicator --}}
            </div>
        </div>
    </div>
@endif

{{-- Timeline for Leave Application --}}
<div class="mt-4 flex items-center gap-2 text-xs">
    <div class="flex items-center gap-1">
        <div class="w-2 h-2 rounded-full bg-green-500"></div>
        <span class="text-zinc-600 dark:text-zinc-400">
            Submitted {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
        </span>
    </div>
    @if($notification->data['type'] === 'leave_application_approved')
        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <div class="flex items-center gap-1">
            <div class="w-2 h-2 rounded-full bg-green-500"></div>
            <span class="text-green-600 dark:text-green-400 font-medium">Approved</span>
        </div>
    @elseif($notification->data['type'] === 'leave_application_rejected')
        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <div class="flex items-center gap-1">
            <div class="w-2 h-2 rounded-full bg-red-500"></div>
            <span class="text-red-600 dark:text-red-400 font-medium">Not Approved</span>
        </div>
    @endif
</div>
```

---

## Leave Type Mapping

| Leave Type | Display Label | Color |
|------------|---------------|-------|
| `annual` | Annual Leave | Blue |
| `sick` | Sick Leave | Red |
| `maternity` | Maternity Leave | Pink |
| `paternity` | Paternity Leave | Purple |
| `compassionate` | Compassionate Leave | Gray |
| `unpaid` | Unpaid Leave | Amber |
| `study` | Study Leave | Teal |
| `vacation` | Vacation Leave | Green |

---

## Date Formatting Helper

```php
// Helper function for formatting leave dates
function formatLeaveDates($startDate, $endDate): array
{
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    
    return [
        'start_formatted' => $start->format('M d, Y (l)'),
        'end_formatted' => $end->format('M d, Y (l)'),
        'duration' => $start->diffInDays($end) + 1 . ' days',
        'return_date' => $end->copy()->addDay()->format('M d, Y (l)'),
    ];
}
```

---

## Implementation Checklist

- [ ] Update `LeaveApplicationSubmitted.php`
- [ ] Update `LeaveApplicationApproved.php`
- [ ] Update `LeaveApplicationRejected.php`
- [ ] Add leave type badge styling
- [ ] Add date range display component
- [ ] Add leave balance display (for rejections)
- [ ] Add timeline component
- [ ] Test all three notification types
- [ ] Verify email rendering
- [ ] Test with various leave types
- [ ] Test date formatting edge cases (weekends, holidays)
