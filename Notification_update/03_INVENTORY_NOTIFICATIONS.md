# Inventory Notifications - User-Friendly Redesign

## Overview
These notifications handle inventory-related events including item requests, dispatches, low stock alerts, expired items, and health checks.

---

## 1. ItemRequestCreatedNotification

**File:** `app/Notifications/ItemRequestCreatedNotification.php`

**Trigger:** When a new item request is created by a department.

**Recipients:** Inventory managers, storekeepers.

### Current Output (Problem)
```
Type: item_request_created
Message: Request ITR-2025-00123 sent to inventory.
Request number: ITR-2025-00123
Items: ["Flour x 50.00 kg", "Sugar x 25.00 kg", "Butter x 10.00 kg"]
Requested by: John Doe
Department: Production
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'item_request_created',
    'title' => 'New Inventory Request Submitted',
    'message' => 'Production department has requested items from inventory.',
    'summary' => 'Flour (50 kg), Sugar (25 kg), Butter (10 kg) + 2 more',
    'context' => [
        'request_number' => 'ITR-2025-00123',
        'item_count' => '5 items',
        'requesting_department' => 'Production',
        'requested_by' => 'John Doe',
        'requester_role' => 'Production Manager',
        'priority' => 'Normal',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 25, 2026 09:15 AM',
    ],
    'items_preview' => [
        ['name' => 'Flour', 'quantity' => '50.00', 'unit' => 'kg'],
        ['name' => 'Sugar', 'quantity' => '25.00', 'unit' => 'kg'],
        ['name' => 'Butter', 'quantity' => '10.00', 'unit' => 'kg'],
    ],
    'action_url' => '/branch-dashboard/inventory/item-requests?b_id=xxx',
    'action_text' => 'Review Request',
    // Internal use only:
    'item_request_id' => '789',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: New Inventory Request: ITR-2025-00123

Hi [Recipient Name],

A new item request has been submitted and requires your attention.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    ITR-2025-00123
Department:        Production
Requested By:      John Doe (Production Manager)
Priority:          Normal
Branch:            SweetTooth Port Harcourt
Submitted:         Feb 25, 2026 at 09:15 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Items Requested:
┌─────────────────────────────────────────────┐
│ Item              Quantity        Unit      │
├─────────────────────────────────────────────┤
│ Flour             50.00           kg        │
│ Sugar             25.00           kg        │
│ Butter            10.00           kg        │
│ ... and 2 more items                        │
└─────────────────────────────────────────────┘

Please review and process this request.

[Review Request →]

Best regards,
SweetTooth Inventory System
```

---

## 2. ItemRequestApprovedNotification

**File:** `app/Notifications/ItemRequestApprovedNotification.php`

**Trigger:** When an item request is approved by inventory management.

**Recipients:** The requesting department/person.

### Current Output (Problem)
```
Type: item_request_approved
Message: Request ITR-2025-00123 approved by inventory.
Request number: ITR-2025-00123
Approved items: ["Flour x 50.00 kg", "Sugar x 25.00 kg"]
Approved by: Jane Smith
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'item_request_approved',
    'title' => 'Inventory Request Approved',
    'message' => 'Your item request has been approved and is ready for dispatch.',
    'summary' => 'All requested items approved',
    'context' => [
        'request_number' => 'ITR-2025-00123',
        'item_count' => '3 items approved',
        'requesting_department' => 'Production',
        'approved_by' => 'Jane Smith',
        'approver_role' => 'Store Manager',
        'branch' => 'SweetTooth Port Harcourt',
        'approved_at' => 'Feb 25, 2026 11:30 AM',
    ],
    'approved_items' => [
        ['name' => 'Flour', 'quantity_requested' => '50.00', 'quantity_approved' => '50.00', 'unit' => 'kg'],
        ['name' => 'Sugar', 'quantity_requested' => '25.00', 'quantity_approved' => '25.00', 'unit' => 'kg'],
        ['name' => 'Butter', 'quantity_requested' => '10.00', 'quantity_approved' => '10.00', 'unit' => 'kg'],
    ],
    'action_url' => '/branch-dashboard/inventory/item-requests?b_id=xxx',
    'action_text' => 'View Request',
    // Internal use only:
    'item_request_id' => '789',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ Inventory Request Approved: ITR-2025-00123

Hi [Recipient Name],

Great news! Your inventory request has been approved.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    ITR-2025-00123
Department:        Production
Approved By:       Jane Smith (Store Manager)
Branch:            SweetTooth Port Harcourt
Approved At:       Feb 25, 2026 at 11:30 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Approved Items:
┌─────────────────────────────────────────────────────────┐
│ Item          Requested    Approved       Unit          │
├─────────────────────────────────────────────────────────┤
│ Flour         50.00        50.00          kg            │
│ Sugar         25.00        25.00          kg            │
│ Butter        10.00        10.00          kg            │
└─────────────────────────────────────────────────────────┘

Your items will be dispatched shortly. Please coordinate with 
the inventory team for pickup.

[View Request Details →]

Best regards,
SweetTooth Inventory System
```

---

## 3. ItemRequestDispatchedNotification

**File:** `app/Notifications/ItemRequestDispatchedNotification.php`

**Trigger:** When items from a request are dispatched (partially or fully).

**Recipients:** The requesting department/person.

### Current Output (Problem)
```
Type: item_request_dispatched
Message: Request ITR-2025-00123 dispatched by inventory.
Dispatched by: Jane Smith
Dispatched items: ["Flour x 50.00 kg"]
Remaining items: ["Sugar x 25.00 kg"]
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'item_request_dispatched',
    'title' => $this->request->status === 'completed' 
        ? 'Inventory Request Fully Dispatched' 
        : 'Inventory Request Partially Dispatched',
    'message' => $this->request->status === 'completed'
        ? 'All items from your request have been dispatched.'
        : 'Some items from your request have been dispatched.',
    'summary' => '3 of 5 items dispatched',
    'context' => [
        'request_number' => 'ITR-2025-00123',
        'dispatch_status' => $this->request->status === 'completed' ? 'Complete' : 'Partial',
        'items_dispatched' => '3 items',
        'items_remaining' => '2 items',
        'dispatched_by' => 'Jane Smith',
        'dispatcher_role' => 'Storekeeper',
        'branch' => 'SweetTooth Port Harcourt',
        'dispatched_at' => 'Feb 25, 2026 02:00 PM',
    ],
    'dispatched_items' => [
        ['name' => 'Flour', 'quantity' => '50.00', 'unit' => 'kg'],
        ['name' => 'Yeast', 'quantity' => '5.00', 'unit' => 'kg'],
        ['name' => 'Salt', 'quantity' => '2.00', 'unit' => 'kg'],
    ],
    'remaining_items' => [
        ['name' => 'Sugar', 'quantity' => '25.00', 'unit' => 'kg'],
        ['name' => 'Butter', 'quantity' => '10.00', 'unit' => 'kg'],
    ],
    'action_url' => '/branch-dashboard/inventory/item-requests?b_id=xxx',
    'action_text' => 'View Request',
    // Internal use only:
    'item_request_id' => '789',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Inventory Request Dispatched: ITR-2025-00123

Hi [Recipient Name],

Your inventory request has been dispatched.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    ITR-2025-00123
Dispatch Status:   Partially Dispatched
Dispatched By:     Jane Smith (Storekeeper)
Branch:            SweetTooth Port Harcourt
Dispatched At:     Feb 25, 2026 at 02:00 PM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Items Dispatched:
┌─────────────────────────────────────────────┐
│ Item              Quantity        Unit      │
├─────────────────────────────────────────────┤
│ Flour             50.00           kg        │
│ Yeast             5.00            kg        │
│ Salt              2.00            kg        │
└─────────────────────────────────────────────┘

Items Remaining (will be dispatched later):
  • Sugar - 25.00 kg
  • Butter - 10.00 kg

Please coordinate with the inventory team for pickup.

[View Request Details →]

Best regards,
SweetTooth Inventory System
```

---

## 4. LowStockAlert

**File:** `app/Notifications/LowStockAlert.php`

**Trigger:** When inventory items fall below reorder level (scheduled alert).

**Recipients:** Inventory managers, procurement team.

### Current Output (Problem)
```
Type: low_stock_alert
Message: 5 item(s) are below reorder level.
Items: [{"name": "Flour", "current": 20, "reorder": 50}, ...]
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'low_stock_alert',
    'title' => '⚠️ Low Stock Alert',
    'message' => '5 items are below reorder level and need attention.',
    'summary' => 'Flour (20/50 kg), Sugar (15/40 kg) + 3 more',
    'context' => [
        'item_count' => '5 items',
        'branch' => 'SweetTooth Port Harcourt',
        'alert_date' => 'Feb 25, 2026 06:00 AM',
        'urgency' => 'Medium',
    ],
    'low_stock_items' => [
        ['name' => 'Flour', 'current_qty' => '20', 'reorder_level' => '50', 'unit' => 'kg', 'shortage' => '30'],
        ['name' => 'Sugar', 'current_qty' => '15', 'reorder_level' => '40', 'unit' => 'kg', 'shortage' => '25'],
        ['name' => 'Butter', 'current_qty' => '8', 'reorder_level' => '20', 'unit' => 'kg', 'shortage' => '12'],
    ],
    'action_url' => '/branch-dashboard/inventory?b_id=xxx',
    'action_text' => 'View Inventory',
    // Internal use only:
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ⚠️ Low Stock Alert: 5 Items Need Reordering

Hi [Recipient Name],

The following inventory items have fallen below their reorder levels.

Alert Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Branch:            SweetTooth Port Harcourt
Items Affected:    5 items
Alert Generated:   Feb 25, 2026 at 06:00 AM
Urgency:           Medium
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Low Stock Items:
┌─────────────────────────────────────────────────────────┐
│ Item          Current    Reorder    Shortage   Unit     │
├─────────────────────────────────────────────────────────┤
│ Flour         20         50         30         kg       │
│ Sugar         15         40         25         kg       │
│ Butter        8          20         12         kg       │
│ ... and 2 more items                                    │
└─────────────────────────────────────────────────────────┘

Recommended Action:
  • Review inventory levels
  • Initiate purchase orders for shortage items
  • Update reorder levels if necessary

[View Inventory →]

Best regards,
SweetTooth Inventory System
```

---

## 5. ExpiredItemsAlert

**File:** `app/Notifications/ExpiredItemsAlert.php`

**Trigger:** When inventory items have expired (scheduled alert).

**Recipients:** Inventory managers, quality control.

### Current Output (Problem)
```
Type: expired_items_alert
Message: 3 item(s) are expired.
Items: [{"name": "Vanilla Extract", "expired_date": "2025-12-01"}, ...]
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'expired_items_alert',
    'title' => '🚨 Expired Items Alert',
    'message' => '3 items have expired and require immediate attention.',
    'summary' => 'Vanilla Extract (expired Dec 1), Baking Powder (expired Jan 15) + 1 more',
    'context' => [
        'item_count' => '3 items',
        'branch' => 'SweetTooth Port Harcourt',
        'alert_date' => 'Feb 25, 2026 06:00 AM',
        'urgency' => 'High',
    ],
    'expired_items' => [
        ['name' => 'Vanilla Extract', 'expired_date' => 'Dec 01, 2025', 'quantity' => '5', 'unit' => 'bottles', 'days_expired' => '86'],
        ['name' => 'Baking Powder', 'expired_date' => 'Jan 15, 2026', 'quantity' => '10', 'unit' => 'kg', 'days_expired' => '41'],
        ['name' => 'Food Coloring', 'expired_date' => 'Feb 20, 2026', 'quantity' => '3', 'unit' => 'boxes', 'days_expired' => '5'],
    ],
    'action_url' => '/branch-dashboard/inventory?b_id=xxx',
    'action_text' => 'View Inventory',
    // Internal use only:
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: 🚨 Urgent: 3 Expired Items Require Action

Hi [Recipient Name],

The following inventory items have expired and require immediate attention.

Alert Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Branch:            SweetTooth Port Harcourt
Items Expired:     3 items
Alert Generated:   Feb 25, 2026 at 06:00 AM
Urgency:           High
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Expired Items:
┌─────────────────────────────────────────────────────────┐
│ Item              Expired On      Qty      Days Over    │
├─────────────────────────────────────────────────────────┤
│ Vanilla Extract   Dec 01, 2025    5 bot    86 days      │
│ Baking Powder     Jan 15, 2026    10 kg    41 days      │
│ Food Coloring     Feb 20, 2026    3 box    5 days       │
└─────────────────────────────────────────────────────────┘

Recommended Actions:
  1. Remove expired items from usable inventory
  2. Document disposal according to quality procedures
  3. Initiate replacement orders if needed
  4. Review expiry tracking procedures

[View Inventory →]

Best regards,
SweetTooth Inventory System
```

---

## 6. HealthCheckAlert

**File:** `app/Notifications/HealthCheckAlert.php`

**Trigger:** When inventory health checks identify issues (scheduled alert).

**Recipients:** Inventory managers, branch managers.

### Current Output (Problem)
```
Type: health_check_alert
Message: 4 health check(s) require action.
Checks: [{"type": "negative_stock", "item": "Flour"}, ...]
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'health_check_alert',
    'title' => '⚠️ Inventory Health Issues Detected',
    'message' => '4 inventory health issues require your attention.',
    'summary' => 'Negative stock (2), Near expiry (1), Overstock (1)',
    'context' => [
        'issue_count' => '4 issues',
        'critical_issues' => '2',
        'branch' => 'SweetTooth Port Harcourt',
        'alert_date' => 'Feb 25, 2026 06:00 AM',
    ],
    'health_issues' => [
        ['type' => 'negative_stock', 'severity' => 'critical', 'item' => 'Flour', 'detail' => 'Stock: -5 kg'],
        ['type' => 'negative_stock', 'severity' => 'critical', 'item' => 'Sugar', 'detail' => 'Stock: -2 kg'],
        ['type' => 'near_expiry', 'severity' => 'warning', 'item' => 'Yeast', 'detail' => 'Expires in 7 days'],
        ['type' => 'overstock', 'severity' => 'info', 'item' => 'Packaging Boxes', 'detail' => '150% of max level'],
    ],
    'action_url' => '/branch-dashboard/inventory?b_id=xxx',
    'action_text' => 'View Health Report',
    // Internal use only:
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Inventory Health Check: 4 Issues Require Attention

Hi [Recipient Name],

The inventory health check has identified issues that need attention.

Health Check Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Branch:            SweetTooth Port Harcourt
Issues Found:      4 issues
Critical:          2 | Warnings: 1 | Info: 1
Check Date:        Feb 25, 2026
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Issues by Severity:

🔴 CRITICAL:
  • Negative Stock: Flour (Current: -5 kg)
  • Negative Stock: Sugar (Current: -2 kg)

🟡 WARNING:
  • Near Expiry: Yeast (Expires in 7 days)

ℹ️ INFO:
  • Overstock: Packaging Boxes (150% of max level)

Recommended Actions:
  1. Investigate and correct negative stock immediately
  2. Use or dispose of near-expiry items
  3. Review overstocked items for redistribution

[View Health Report →]

Best regards,
SweetTooth Inventory System
```

---

## Blade Template Integration

### Items List Display

```blade
{{-- Items Preview in Card --}}
@if(!empty($notification->data['items_preview']))
    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
        <span class="font-medium">Items:</span>
        {{ collect($notification->data['items_preview'])->take(3)->map(fn($item) => "{$item['name']} ({$item['quantity']} {$item['unit']})")->implode(', ') }}
        @if(count($notification->data['items_preview']) > 3)
            <span class="text-zinc-400">+ {{ count($notification->data['items_preview']) - 3 }} more</span>
        @endif
    </div>
@endif

{{-- Detailed Items Table in Modal --}}
@if(!empty($notification->data['low_stock_items']))
    <div class="mt-4 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full text-xs">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Item</th>
                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Current</th>
                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Reorder</th>
                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Shortage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notification->data['low_stock_items'] as $item)
                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                        <td class="px-3 py-2 text-zinc-800 dark:text-zinc-200">{{ $item['name'] }}</td>
                        <td class="px-3 py-2 text-right text-zinc-800 dark:text-zinc-200">{{ $item['current_qty'] }} {{ $item['unit'] }}</td>
                        <td class="px-3 py-2 text-right text-zinc-500">{{ $item['reorder_level'] }} {{ $item['unit'] }}</td>
                        <td class="px-3 py-2 text-right text-red-600 dark:text-red-400 font-medium">{{ $item['shortage'] }} {{ $item['unit'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Urgency Badge --}}
@if(!empty($notification->data['context']['urgency']))
    @php
        $urgencyColors = [
            'high' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'low' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        ];
        $urgencyClass = $urgencyColors[strtolower($notification->data['context']['urgency'])] ?? $urgencyColors['low'];
    @endphp
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $urgencyClass }}">
        {{ ucfirst($notification->data['context']['urgency']) }}
    </span>
@endif
```

---

## Implementation Checklist

- [ ] Update `ItemRequestCreatedNotification.php`
- [ ] Update `ItemRequestApprovedNotification.php`
- [ ] Update `ItemRequestDispatchedNotification.php`
- [ ] Update `LowStockAlert.php`
- [ ] Update `ExpiredItemsAlert.php`
- [ ] Update `HealthCheckAlert.php`
- [ ] Add item list blade component
- [ ] Add urgency badge component
- [ ] Test all six notification types
- [ ] Verify email table rendering
- [ ] Test with various item quantities
