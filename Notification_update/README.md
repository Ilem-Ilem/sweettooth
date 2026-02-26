# Notification System User-Friendly Redesign

## Overview

This documentation provides a comprehensive redesign of the SweetTooth notification system to transform technical, developer-focused notifications into user-friendly, actionable messages.

---

## Problem Statement

**Current notifications display system structure:**
```
Type: approval_request_approved
Branch: SweetTooth Port Harcourt
Approval request id: 9
Action: accounting:gl_account_update
Status: approved
Branch id: 019c850c-7294-72ea-9ec5-1b00d6b0bdd8
```

**Users need clear, actionable information:**
```
GL Account Update Approved
Your request to update the Petty Cash account has been approved by the Finance Manager.

Requested On:    Feb 24, 2026
Approved By:     Sarah Johnson (Finance Manager)
Approved At:     Feb 25, 2026 12:28 PM
Branch:          SweetTooth Port Harcourt
```

---

## Documentation Structure

| File | Content |
|------|---------|
| `01_APPROVAL_NOTIFICATIONS.md` | Approval request created, approved, rejected |
| `02_GL_POSTING_NOTIFICATIONS.md` | GL posting draft, approved, failed, journal entry |
| `03_INVENTORY_NOTIFICATIONS.md` | Item requests, low stock, expired items, health checks |
| `04_REPORT_NOTIFICATIONS.md` | Report compiled, approved, rejected, sent to MD |
| `05_EMPLOYEE_NOTIFICATIONS.md` | Employee created, updated, role changed, deleted |
| `06_LEAVE_NOTIFICATIONS.md` | Leave application submitted, approved, rejected |
| `07_SHIFT_NOTIFICATIONS.md` | Shift reminders, warnings, completion, auto clock-out |
| `08_SALES_NOTIFICATIONS.md` | Sales invoices, receipts, production requests |
| `09_SYSTEM_NOTIFICATIONS.md` | Backups, stuck callbacks, system health |
| `10_NOTIFICATION_CENTER_BLADE.md` | Updated blade template for notification display |

---

## Core Principles

### 1. Clear Titles
Every notification should have a descriptive title that summarizes the event.

| Before | After |
|--------|-------|
| `approval_request_approved` | "Your Request Has Been Approved" |
| `low_stock_alert` | "⚠️ Low Stock Alert" |
| `shift_reminder` | "Upcoming Shift Reminder" |

### 2. Context-Rich Messages
Messages should explain what happened and why it matters.

| Before | After |
|--------|-------|
| "Approval request approved." | "Your GL Account Update request has been approved by the Finance Manager." |
| "3 item(s) are expired." | "3 items have expired and require immediate attention." |

### 3. Hide Technical IDs
Internal IDs should be stored but not displayed to users.

**Hide:**
- `approval_request_id: 9`
- `branch_id: 019c850c-7294-72ea-9ec5-1b00d6b0bdd8`
- `type: approval_request_approved`

**Show:**
- Request Number: `APR-2025-00042`
- Branch: `SweetTooth Port Harcourt`
- Action: `GL Account Update`

### 4. Provide Actionable Context
Include who, what, when, where information.

```php
'context' => [
    'requested_by' => 'John Doe',
    'approved_by' => 'Sarah Johnson',
    'submitted_at' => 'Feb 24, 2026 09:30 AM',
    'approved_at' => 'Feb 25, 2026 12:28 PM',
    'branch' => 'SweetTooth Port Harcourt',
]
```

### 5. Visual Hierarchy
Structure information for easy scanning.

```
┌─────────────────────────────────────────┐
│ TITLE (Bold, Prominent)                 │
│ Message (Regular weight)                │
│ Summary (Smaller, secondary)            │
│                                         │
│ [Badge] [Badge] [Badge]                 │
│                                         │
│ 📍 Branch  •  2 hours ago               │
│                                         │
│ [View Details →]                        │
└─────────────────────────────────────────┘
```

---

## Notification Structure

### Database Notification Array

```php
return [
    // Internal type for logic (not displayed)
    'type' => 'approval_request_approved',
    
    // User-facing content
    'title' => 'Your Request Has Been Approved',
    'message' => 'Your GL Account Update request has been approved.',
    'summary' => 'Approved by Sarah Johnson - Finance Manager',
    
    // Structured context (displayed in details)
    'context' => [
        'request_number' => 'APR-2025-00042',
        'action_type' => 'GL Account Update',
        'approved_by' => 'Sarah Johnson',
        'approver_role' => 'Finance Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 24, 2026 09:30 AM',
        'approved_at' => 'Feb 25, 2026 12:28 PM',
    ],
    
    // Special content (items, lists, tables)
    'items_preview' => [...],
    'low_stock_items' => [...],
    
    // Action
    'action_url' => '/branch-dashboard/audit?b_id=xxx',
    'action_text' => 'View Request',
    
    // Internal data (not displayed)
    'approval_request_id' => '9',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
];
```

---

## Email Template Structure

```
Subject: [Icon] [Action]: [Summary]

Hi [Recipient Name],

[Opening message explaining the notification]

[Section: Key Details]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Label 1:           Value 1
Label 2:           Value 2
Label 3:           Value 3
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[Section: Additional Information]
  • Bullet point 1
  • Bullet point 2
  • Bullet point 3

[Special Box: Feedback/Error/Warning]
┌─────────────────────────────────────────────────────────┐
│ Content here                                            │
└─────────────────────────────────────────────────────────┘

[Call to Action]
[Button/Link →]

Best regards,
SweetTooth System
```

---

## Visual Components

### Status Badges

| Status | Color | Example |
|--------|-------|---------|
| Success | Green | ✓ Approved |
| Failed | Red | ✗ Rejected |
| Warning | Amber | ⚠️ Pending |
| Info | Blue | ℹ️ Info |

### Priority Badges

| Priority | Color |
|----------|-------|
| Urgent | Red |
| High | Orange |
| Normal | Blue |
| Low | Slate |

### Leave/Shift Type Badges

| Type | Icon | Color |
|------|------|-------|
| Annual Leave | 📅 | Blue |
| Sick Leave | 🤒 | Red |
| Morning Shift | 🌅 | Amber |
| Night Shift | 🌙 | Slate |

---

## Implementation Guide

### Step 1: Update Notification Classes

For each notification class:

1. Add `title` to `toArray()`
2. Enhance `message` with context
3. Add `summary` for preview
4. Move technical details to `context` array
5. Keep internal IDs but don't display them

### Step 2: Update Blade Template

Replace the notification center template with the updated version that:
- Displays titles prominently
- Shows context badges
- Renders special content (items, tables)
- Provides modal details view

### Step 3: Test Each Notification Type

Verify:
- Database notification displays correctly
- Email renders properly
- Mobile view is responsive
- Dark mode works
- Accessibility is maintained

---

## Migration Strategy

### Phase 1: High Priority (Week 1)
- Approval notifications (3 files)
- GL posting notifications (4 files)

### Phase 2: Medium Priority (Week 2)
- Inventory notifications (6 files)
- Report notifications (4 files)

### Phase 3: Standard Priority (Week 3)
- Employee notifications (5 files)
- Leave notifications (3 files)
- Shift notifications (4 files)

### Phase 4: Lower Priority (Week 4)
- Sales notifications (3 files)
- System notifications (3 files)
- Blade template polish

---

## Files to Modify

### Notification Classes (33 files)
```
app/Notifications/
├── ApprovalRequestApproved.php
├── ApprovalRequestCreated.php
├── ApprovalRequestRejected.php
├── GlPostingApprovedNotification.php
├── GlPostingApprovalFailedNotification.php
├── GlPostingDraftReadyNotification.php
├── JournalEntryPostedNotification.php
├── ItemRequestCreatedNotification.php
├── ItemRequestApprovedNotification.php
├── ItemRequestDispatchedNotification.php
├── LowStockAlert.php
├── ExpiredItemsAlert.php
├── HealthCheckAlert.php
├── ReportCompiledNotification.php
├── ReportApprovedNotification.php
├── ReportRejectedNotification.php
├── ReportSentToMDNotification.php
├── EmployeeCreatedNotification.php
├── EmployeeUpdatedNotification.php
├── EmployeeRoleUpdatedNotification.php
├── EmployeeDeletedNotification.php
├── LeaveApplicationSubmitted.php
├── LeaveApplicationApproved.php
├── LeaveApplicationRejected.php
├── ShiftReminder.php
├── ShiftEndingWarning.php
├── ShiftCompleted.php
├── ShiftAutoClockOutImminent.php
├── SalesInvoiceNotification.php (review only)
├── SalesReceiptNotification.php (review only)
├── SalesProductionRequestCreatedNotification.php
├── StuckCallbackNotification.php
├── BackupCompletedNotification.php
└── ...
```

### View Templates
```
resources/views/livewire/components/notification-center.blade.php
```

---

## Testing Checklist

### For Each Notification Type

- [ ] Database notification displays title
- [ ] Message is clear and actionable
- [ ] Context information is complete
- [ ] Technical IDs are hidden
- [ ] Action button works
- [ ] Details modal shows all information
- [ ] Email subject is descriptive
- [ ] Email body is well-formatted
- [ ] Mobile view is responsive
- [ ] Dark mode renders correctly
- [ ] Screen readers can access content

---

## Success Metrics

### Before
- Users confused by technical jargon
- Support tickets about notification meaning
- Low notification engagement

### After
- Users understand notifications immediately
- Reduced support tickets
- Higher notification engagement
- Faster response times to alerts

---

## Appendix: Quick Reference

### Action Type Mapping
```php
$actionLabels = [
    'accounting:gl_account_update' => 'GL Account Update',
    'accounting:journal_entry' => 'Journal Entry Posting',
    'inventory:item_request' => 'Item Request',
    'employee:role_change' => 'Role Change',
    'report:approval' => 'Report Approval',
];
```

### Date Formatting
```php
// Use consistent formatting
$date->format('M d, Y g:i A');  // Feb 25, 2026 12:28 PM
$date->diffForHumans();         // 2 hours ago
```

### Currency Formatting
```php
number_format($amount, 2);  // 1,250.00
// Or use CurrencyFormattingService
$currencyService->format($amount);  // ₦1,250.00
```

---

## Contact

For questions about this documentation, contact the development team.

**Last Updated:** February 26, 2026
**Version:** 1.0
