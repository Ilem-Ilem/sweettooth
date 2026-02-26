# GL Posting Notifications - User-Friendly Redesign

## Overview
These notifications are sent during the General Ledger posting workflow when drafts are created, approved, or fail approval.

---

## 1. GlPostingDraftReadyNotification

**File:** `app/Notifications/GlPostingDraftReadyNotification.php`

**Trigger:** When a GL posting draft is ready for approval.

**Recipients:** Accounting managers/approvers.

### Current Output (Problem)
```
Type: gl_posting_draft_ready
Message: GL posting draft ready for approval.
Reference: JournalEntry #123
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'gl_posting_draft_ready',
    'title' => 'GL Posting Draft Ready for Review',
    'message' => 'A new General Ledger posting draft requires your approval.',
    'summary' => 'Journal Entry JE-2025-0089 - Total: ₦1,250,000.00',
    'context' => [
        'reference_number' => 'JE-2025-0089',
        'reference_type' => 'Journal Entry',
        'total_amount' => '₦1,250,000.00',
        'entry_count' => '4 entries',
        'prepared_by' => 'John Doe',
        'branch' => 'SweetTooth Port Harcourt',
        'created_at' => 'Feb 25, 2026 10:15 AM',
    ],
    'action_url' => '/branch-dashboard/accounting/posting-approvals?b_id=xxx',
    'action_text' => 'Review Posting',
    // Internal use only:
    'reference_type_raw' => 'journal_entry',
    'reference_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Action Required: GL Posting Draft Ready for Approval

Hi [Recipient Name],

A new General Ledger posting draft is ready for your review and approval.

Posting Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Reference Number:  JE-2025-0089
Type:              Journal Entry
Total Amount:      ₦1,250,000.00
Entries:           4 line items
Prepared By:       John Doe
Branch:            SweetTooth Port Harcourt
Created:           Feb 25, 2026 at 10:15 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Please review the posting details and approve or request changes.

[Review Posting →]

Best regards,
SweetTooth Accounting System
```

---

## 2. GlPostingApprovedNotification

**File:** `app/Notifications/GlPostingApprovedNotification.php`

**Trigger:** When a GL posting draft is approved and posted to the ledger.

**Recipients:** The person who prepared the entry, relevant stakeholders.

### Current Output (Problem)
```
Type: gl_posting_approved
Message: GL posting approved.
Reference: JournalEntry #123
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'gl_posting_approved',
    'title' => 'GL Posting Successfully Posted',
    'message' => 'Your General Ledger posting has been approved and posted.',
    'summary' => 'Journal Entry JE-2025-0089 posted to ledger',
    'context' => [
        'reference_number' => 'JE-2025-0089',
        'reference_type' => 'Journal Entry',
        'total_amount' => '₦1,250,000.00',
        'posted_by' => 'Sarah Johnson',
        'approver_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'posted_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/accounting/posting-status?b_id=xxx',
    'action_text' => 'View Posting Status',
    // Internal use only:
    'reference_type_raw' => 'journal_entry',
    'reference_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ GL Posting Approved & Posted to Ledger

Hi [Recipient Name],

Your General Ledger posting has been approved and successfully posted.

Posting Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Reference Number:  JE-2025-0089
Type:              Journal Entry
Total Amount:      ₦1,250,000.00
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Approval:
  • Approved By:   Sarah Johnson
  • Role:          Finance Manager
  • Posted At:     Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

The entry is now reflected in the General Ledger.

[View Posting Status →]

Best regards,
SweetTooth Accounting System
```

---

## 3. GlPostingApprovalFailedNotification

**File:** `app/Notifications/GlPostingApprovalFailedNotification.php`

**Trigger:** When a GL posting approval fails due to an error.

**Recipients:** Accounting managers, system administrators.

### Current Output (Problem)
```
Type: gl_posting_approval_failed
Message: GL posting approval failed.
Reference: JournalEntry #123
Error: Insufficient permissions
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'gl_posting_approval_failed',
    'title' => '⚠️ GL Posting Approval Failed',
    'message' => 'The GL posting approval process encountered an error.',
    'summary' => 'Action required to resolve the issue',
    'context' => [
        'reference_number' => 'JE-2025-0089',
        'reference_type' => 'Journal Entry',
        'total_amount' => '₦1,250,000.00',
        'error_type' => 'Permission Error',
        'error_message' => 'Insufficient permissions to post to selected GL account',
        'branch' => 'SweetTooth Port Harcourt',
        'failed_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/accounting/posting-approvals?b_id=xxx',
    'action_text' => 'Review & Fix',
    // Internal use only:
    'reference_type_raw' => 'journal_entry',
    'reference_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
    'error_raw' => 'Insufficient permissions',
]
```

#### Email Notification (`toMail`)
```
Subject: ⊘ Action Required: GL Posting Approval Failed

Hi [Recipient Name],

The General Ledger posting approval process has failed and requires your attention.

Posting Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Reference Number:  JE-2025-0089
Type:              Journal Entry
Total Amount:      ₦1,250,000.00
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Error Information:
┌─────────────────────────────────────────────────────────┐
│ Error Type:    Permission Error                         │
│ Message:       Insufficient permissions to post to      │
│                selected GL account                      │
│ Failed At:     Feb 25, 2026 at 02:30 PM                │
└─────────────────────────────────────────────────────────┘

Recommended Actions:
  1. Verify the GL account permissions
  2. Contact your system administrator if needed
  3. Resubmit the posting after resolving the issue

[Review & Fix →]

Best regards,
SweetTooth Accounting System
```

---

## 4. JournalEntryPostedNotification

**File:** `app/Notifications/JournalEntryPostedNotification.php`

**Trigger:** When a journal entry is posted to the General Ledger.

**Recipients:** Accounting team, relevant stakeholders.

### Current Output (Problem)
```
Type: journal_entry_posted
Message: Journal entry posted.
Reference Number: JE-2025-0089
Amount: 1,250,000.00
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'journal_entry_posted',
    'title' => 'Journal Entry Posted to Ledger',
    'message' => 'A new journal entry has been posted to the General Ledger.',
    'summary' => 'JE-2025-0089 - ₦1,250,000.00',
    'context' => [
        'reference_number' => 'JE-2025-0089',
        'total_amount' => '₦1,250,000.00',
        'debit_total' => '₦1,250,000.00',
        'credit_total' => '₦1,250,000.00',
        'entry_count' => '4 entries',
        'posted_by' => 'System',
        'branch' => 'SweetTooth Port Harcourt',
        'posted_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/accounting/journal-entry?b_id=xxx',
    'action_text' => 'View Journal',
    // Internal use only:
    'gl_entry_id' => '456',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Journal Entry Posted to General Ledger

Hi [Recipient Name],

A journal entry has been successfully posted to the General Ledger.

Entry Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Reference Number:  JE-2025-0089
Total Amount:      ₦1,250,000.00
Debit Total:       ₦1,250,000.00
Credit Total:      ₦1,250,000.00
Line Items:        4 entries
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Posting Information:
  • Posted By:     System
  • Posted At:     Feb 25, 2026 at 02:30 PM
  • Branch:        SweetTooth Port Harcourt

[View Journal Entry →]

Best regards,
SweetTooth Accounting System
```

---

## Blade Template Integration

### Context Display for GL Notifications

```blade
{{-- In the details modal --}}
@if(!empty($notification->data['context']))
    <div class="mt-4 space-y-2 text-sm">
        {{-- Reference Number --}}
        @if(!empty($notification->data['context']['reference_number']))
            <div class="flex justify-between gap-4">
                <span class="text-zinc-500 dark:text-zinc-400">Reference:</span>
                <span class="text-zinc-800 dark:text-zinc-200 font-medium">
                    {{ $notification->data['context']['reference_number'] }}
                </span>
            </div>
        @endif
        
        {{-- Amount with special styling --}}
        @if(!empty($notification->data['context']['total_amount']))
            <div class="flex justify-between gap-4">
                <span class="text-zinc-500 dark:text-zinc-400">Amount:</span>
                <span class="text-zinc-800 dark:text-zinc-200 font-bold">
                    {{ $notification->data['context']['total_amount'] }}
                </span>
            </div>
        @endif
        
        {{-- Other context fields --}}
        @foreach($notification->data['context'] as $key => $value)
            @if(!in_array($key, ['reference_number', 'total_amount']))
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

{{-- Error Display for Failed Approvals --}}
@if($notification->data['type'] === 'gl_posting_approval_failed')
    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <div class="text-xs font-semibold text-red-700 dark:text-red-400">
                    {{ $notification->data['context']['error_type'] ?? 'Error' }}
                </div>
                <div class="text-sm text-red-600 dark:text-red-300 mt-1">
                    {{ $notification->data['context']['error_message'] ?? $notification->data['error'] ?? 'An error occurred' }}
                </div>
            </div>
        </div>
    </div>
@endif
```

---

## Reference Type Mapping

| Raw Reference Type | User-Friendly Label |
|-------------------|---------------------|
| `journal_entry` | Journal Entry |
| `sales_receipt` | Sales Receipt |
| `payment` | Payment |
| `purchase` | Purchase |
| `expense` | Expense |
| `adjustment` | Stock Adjustment |
| `transfer` | Inventory Transfer |

---

## Implementation Checklist

- [ ] Update `GlPostingDraftReadyNotification.php`
- [ ] Update `GlPostingApprovedNotification.php`
- [ ] Update `GlPostingApprovalFailedNotification.php`
- [ ] Update `JournalEntryPostedNotification.php`
- [ ] Add reference type mapping helper
- [ ] Add currency formatting helper
- [ ] Update notification-center.blade.php for error display
- [ ] Test all four notification types
- [ ] Verify email error rendering
- [ ] Test with various reference types
