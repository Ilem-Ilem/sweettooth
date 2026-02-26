# Approval Notifications - User-Friendly Redesign

## Overview
These notifications are sent when approval requests are created, approved, or rejected in the audit management system.

---

## 1. ApprovalRequestCreated

**File:** `app/Notifications/ApprovalRequestCreated.php`

**Trigger:** When a new approval request is submitted and awaits review.

**Recipients:** Approvers/Managers based on the action type.

### Current Output (Problem)
```
Type: approval_request_created
Message: New approval request awaiting review.
Action: accounting:gl_account_update
Status: pending
Branch: SweetTooth Port Harcourt
Approval request id: 9
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'approval_request_created',
    'title' => 'New Approval Request Submitted',
    'message' => 'A new approval request requires your review.',
    'summary' => 'Request to update GL Account - Petty Cash',
    'context' => [
        'request_number' => 'APR-2025-00042',
        'action_type' => 'GL Account Update',
        'requested_by' => 'John Doe',
        'department' => 'Finance',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 24, 2026 09:30 AM',
        'priority' => 'Normal',
    ],
    'action_url' => '/branch-dashboard/audit?b_id=xxx',
    'action_text' => 'Review Request',
    // Internal use only (not displayed):
    'approval_request_id' => '9',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
    'raw_action' => 'accounting:gl_account_update',
]
```

#### Email Notification (`toMail`)
```
Subject: Action Required: New Approval Request Submitted

Hi [Recipient Name],

A new approval request has been submitted and requires your review.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    APR-2025-00042
Action Type:       GL Account Update
Requested By:      John Doe (Finance)
Branch:            SweetTooth Port Harcourt
Submitted:         Feb 24, 2026 at 09:30 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Please review and take action at your earliest convenience.

[Review Request →]

Best regards,
SweetTooth System
```

---

## 2. ApprovalRequestApproved

**File:** `app/Notifications/ApprovalRequestApproved.php`

**Trigger:** When an approval request is approved by an approver.

**Recipients:** The person who submitted the request.

### Current Output (Problem)
```
Type: approval_request_approved
Message: Approval request approved.
Action: accounting:gl_account_update
Status: approved
Branch: SweetTooth Port Harcourt
Approval request id: 9
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'approval_request_approved',
    'title' => 'Your Request Has Been Approved',
    'message' => 'Your GL Account Update request has been approved.',
    'summary' => 'Approved by Sarah Johnson - Finance Manager',
    'context' => [
        'request_number' => 'APR-2025-00042',
        'action_type' => 'GL Account Update',
        'approved_by' => 'Sarah Johnson',
        'approver_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 24, 2026 09:30 AM',
        'approved_at' => 'Feb 25, 2026 12:28 PM',
    ],
    'action_url' => '/branch-dashboard/audit?b_id=xxx',
    'action_text' => 'View Request',
    // Internal use only (not displayed):
    'approval_request_id' => '9',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
    'raw_action' => 'accounting:gl_account_update',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ Approved: Your GL Account Update Request

Hi [Recipient Name],

Great news! Your approval request has been approved.

Approval Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    APR-2025-00042
Action Type:       GL Account Update
Approved By:       Sarah Johnson
Approver Role:     Finance Manager
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Timeline:
  • Submitted:  Feb 24, 2026 at 09:30 AM
  • Approved:   Feb 25, 2026 at 12:28 PM

[View Request Details →]

Best regards,
SweetTooth System
```

---

## 3. ApprovalRequestRejected

**File:** `app/Notifications/ApprovalRequestRejected.php`

**Trigger:** When an approval request is rejected by an approver.

**Recipients:** The person who submitted the request.

### Current Output (Problem)
```
Type: approval_request_rejected
Message: Approval request rejected.
Action: accounting:gl_account_update
Status: rejected
Branch: SweetTooth Port Harcourt
Approval request id: 9
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'approval_request_rejected',
    'title' => 'Your Request Needs Attention',
    'message' => 'Your GL Account Update request was not approved.',
    'summary' => 'Review feedback from Sarah Johnson',
    'context' => [
        'request_number' => 'APR-2025-00042',
        'action_type' => 'GL Account Update',
        'rejected_by' => 'Sarah Johnson',
        'rejector_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 24, 2026 09:30 AM',
        'rejected_at' => 'Feb 25, 2026 12:28 PM',
        'rejection_reason' => 'Please provide additional documentation for this account change.',
    ],
    'action_url' => '/branch-dashboard/audit?b_id=xxx',
    'action_text' => 'View Feedback',
    // Internal use only (not displayed):
    'approval_request_id' => '9',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
    'raw_action' => 'accounting:gl_account_update',
]
```

#### Email Notification (`toMail`)
```
Subject: ⊘ Action Required: Your Approval Request Needs Revision

Hi [Recipient Name],

Your approval request has been reviewed and requires attention before it can be approved.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    APR-2025-00042
Action Type:       GL Account Update
Reviewed By:       Sarah Johnson
Reviewer Role:     Finance Manager
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Feedback:
┌─────────────────────────────────────────────────────────┐
│ Please provide additional documentation for this        │
│ account change.                                         │
└─────────────────────────────────────────────────────────┘

Timeline:
  • Submitted:  Feb 24, 2026 at 09:30 AM
  • Reviewed:   Feb 25, 2026 at 12:28 PM

[View Feedback & Resubmit →]

Best regards,
SweetTooth System
```

---

## Blade Template Integration

### Updated notification-center.blade.php Section

```blade
{{-- Notification Card --}}
<div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            {{-- Title (if available) --}}
            @if(!empty($notification->data['title']))
                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $notification->data['title'] }}
                </div>
            @endif
            
            {{-- Main Message --}}
            <div class="text-sm text-zinc-700 dark:text-zinc-200 mt-0.5">
                {{ $notification->data['message'] ?? 'You have a new notification.' }}
            </div>
            
            {{-- Summary (if available) --}}
            @if(!empty($notification->data['summary']))
                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2">
                    {{ $notification->data['summary'] }}
                </div>
            @endif
            
            {{-- Branch Info --}}
            @if(!empty($notification->data['context']['branch']))
                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $notification->data['context']['branch'] }}
                </div>
            @endif
            
            {{-- Timestamp --}}
            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>
        
        {{-- Mark as Read Button --}}
        @if ($notification->read_at === null)
            <button
                type="button"
                wire:click="markAsRead('{{ $notification->id }}')"
                class="text-[11px] text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
            >
                Mark read
            </button>
        @endif
    </div>
    
    {{-- Action Button --}}
    @if(!empty($notification->data['action_url']))
        <a
            href="{{ $notification->data['action_url'] }}"
            class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mt-2"
        >
            {{ $notification->data['action_text'] ?? 'View Details' }}
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif
</div>

{{-- Details Modal --}}
<div class="px-5 py-4 text-sm text-zinc-700 dark:text-zinc-300">
    <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
        {{ $notification->data['title'] ?? $notification->data['message'] ?? 'Notification Details' }}
    </div>
    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
        {{ $notification->created_at->format('M d, Y H:i') }}
    </div>
    
    @if(!empty($notification->data['context']))
        <div class="mt-4 space-y-2 text-sm">
            @foreach($notification->data['context'] as $key => $value)
                @if($key !== 'rejection_reason')
                    <div class="flex justify-between gap-4">
                        <span class="text-zinc-500 dark:text-zinc-400">
                            {{ str_replace('_', ' ', ucfirst($key)) }}:
                        </span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-medium text-right">
                            {{ $value }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
    
    @if(!empty($notification->data['context']['rejection_reason']))
        <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="text-xs font-semibold text-red-700 dark:text-red-400 mb-1">Feedback:</div>
            <div class="text-sm text-red-600 dark:text-red-300">
                {{ $notification->data['context']['rejection_reason'] }}
            </div>
        </div>
    @endif
</div>
```

---

## Action Type Mapping Reference

| Raw Action Key | User-Friendly Label |
|----------------|---------------------|
| `accounting:gl_account_update` | GL Account Update |
| `accounting:journal_entry` | Journal Entry Posting |
| `accounting:payment_approval` | Payment Approval |
| `inventory:item_request` | Item Request |
| `inventory:stock_adjustment` | Stock Adjustment |
| `employee:role_change` | Role Change |
| `employee:permission_change` | Permission Change |
| `report:approval` | Report Approval |
| `sales:discount_override` | Discount Override |
| `production:request` | Production Request |

---

## Implementation Checklist

- [ ] Update `ApprovalRequestCreated.php` with new structure
- [ ] Update `ApprovalRequestApproved.php` with new structure
- [ ] Update `ApprovalRequestRejected.php` with new structure
- [ ] Add action type mapping helper method
- [ ] Update notification-center.blade.php template
- [ ] Test all three notification types
- [ ] Verify email rendering
- [ ] Verify database notification display
- [ ] Update existing notifications in database (optional migration)
