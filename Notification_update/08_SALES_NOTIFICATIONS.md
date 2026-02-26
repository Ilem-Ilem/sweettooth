# Sales & Production Notifications - User-Friendly Redesign

## Overview
These notifications handle sales-related events including invoices, receipts, and production requests from sales orders.

---

## 1. SalesInvoiceNotification

**File:** `app/Notifications/SalesInvoiceNotification.php`

**Trigger:** When a sales invoice is generated and sent to customer.

**Recipients:** Customers (via email).

### Current Output
This notification is already customer-facing and well-structured. Minor improvements suggested.

### Proposed Enhancements

#### Email Notification (`toMail`)
```
Subject: Invoice #INV-2026-00042 from SweetTooth

Dear [Customer Name],

Thank you for your order! Please find your invoice details below.

Invoice Information:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Invoice Number:    INV-2026-00042
Order Type:        Dine-In
Date:              Feb 26, 2026 at 12:30 PM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Items Ordered:
┌─────────────────────────────────────────────────────────┐
│ Item              Qty    Unit Price      Total          │
├─────────────────────────────────────────────────────────┤
│ Chocolate Cake    2      ₦2,500.00       ₦5,000.00      │
│ Vanilla Ice Cream 3      ₦1,500.00       ₦4,500.00      │
│ Fresh Juice       2      ₦1,000.00       ₦2,000.00      │
├─────────────────────────────────────────────────────────┤
│ Subtotal:                              ₦11,500.00       │
│ Discount (10%):                        -₦1,150.00       │
│ Tax (5%):                              ₦517.50          │
├─────────────────────────────────────────────────────────┤
│ TOTAL:                                 ₦10,867.50       │
└─────────────────────────────────────────────────────────┘

Payment Information:
  • Payment Status:     Paid
  • Payment Method:     Card
  • Fulfillment:        Ready for Pickup

Business Information:
  SweetTooth Confectionery
  📧 contact@sweettooth.com
  📞 +234 800 SWEET TOOTH

Thank you for your business!

Regards,
SweetTooth Team
```

---

## 2. SalesReceiptNotification

**File:** `app/Notifications/SalesReceiptNotification.php`

**Trigger:** When a sales receipt is generated after payment.

**Recipients:** Customers (via email).

### Current Output
This notification is already customer-facing and well-structured. Minor improvements suggested.

### Proposed Enhancements

#### Email Notification (`toMail`)
```
Subject: Receipt #RCP-2026-00042 - Thank You!

Dear Valued Customer,

Thank you for your purchase! Here's your receipt.

Receipt Information:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Receipt Number:    RCP-2026-00042
Date:              Feb 26, 2026 at 12:35 PM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Items Purchased:
┌─────────────────────────────────────────────────────────┐
│ Item              Qty    Unit Price      Total          │
├─────────────────────────────────────────────────────────┤
│ Chocolate Cake    2      ₦2,500.00       ₦5,000.00      │
│ Vanilla Ice Cream 3      ₦1,500.00       ₦4,500.00      │
│ Fresh Juice       2      ₦1,000.00       ₦2,000.00      │
├─────────────────────────────────────────────────────────┤
│ Subtotal:                              ₦11,500.00       │
│ Discount:                              -₦1,150.00       │
│ Tax:                                   ₦517.50          │
├─────────────────────────────────────────────────────────┤
│ TOTAL:                                 ₦10,867.50       │
└─────────────────────────────────────────────────────────┘

Payment Details:
  • Amount Paid:        ₦10,867.50
  • Payment Method:     Card (**** 1234)
  • Change Due:         ₦0.00

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Thank you for shopping with SweetTooth!
We hope to see you again soon.
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

SweetTooth Confectionery
📧 contact@sweettooth.com | 📞 +234 800 SWEET TOOTH
```

---

## 3. SalesProductionRequestCreatedNotification

**File:** `app/Notifications/SalesProductionRequestCreatedNotification.php`

**Trigger:** When a sales order is converted to production request.

**Recipients:** Production department staff and managers.

### Current Output (Problem)
```
Type: sales_production_request_created
Message: Sales request SPR-2025-00089 sent to production.
Sales request number: SPR-2025-00089
Requested by: John Doe
Sales department: Sales
Production departments: ["Production", "Packaging"]
Items: ["Cake 500g x 100 pcs (Production)", "Cake 500g Box x 100 pcs (Packaging)"]
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'sales_production_request_created',
    'title' => 'New Production Request from Sales',
    'message' => 'A sales order has been converted to a production request.',
    'summary' => 'SPR-2026-00089 • 2 departments • 5 items total',
    'context' => [
        'request_number' => 'SPR-2026-00089',
        'sales_order_number' => 'SO-2026-00150',
        'sales_department' => 'Sales',
        'production_departments' => 'Production, Packaging',
        'total_items' => '5 items',
        'requested_by' => 'John Doe',
        'requester_role' => 'Sales Representative',
        'customer_name' => 'Walk-in Customer',
        'order_type' => 'Dine-In',
        'priority' => 'Normal',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 26, 2026 10:00 AM',
        'expected_completion' => 'Feb 26, 2026 02:00 PM',
    ],
    'items_by_department' => [
        'Production' => [
            ['name' => 'Cake 500g', 'quantity' => '100', 'unit' => 'pcs', 'notes' => 'Chocolate flavor'],
            ['name' => 'Cake 1kg', 'quantity' => '50', 'unit' => 'pcs', 'notes' => 'Vanilla flavor'],
        ],
        'Packaging' => [
            ['name' => 'Cake 500g Box', 'quantity' => '100', 'unit' => 'pcs'],
            ['name' => 'Cake 1kg Box', 'quantity' => '50', 'unit' => 'pcs'],
            ['name' => 'Product Labels', 'quantity' => '150', 'unit' => 'pcs'],
        ],
    ],
    'action_url' => '/branch-dashboard/production/requests?b_id=xxx',
    'action_text' => 'View Request',
    // Internal use only:
    'sales_production_request_id' => '567',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`) - Optional
```
Subject: New Production Request: SPR-2026-00089

Hi Production Team,

A new production request has been created from a sales order.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:      SPR-2026-00089
Sales Order:         SO-2026-00150
Sales Department:    Sales
Requested By:        John Doe (Sales Representative)
Priority:            Normal
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Production Departments:
  • Production
  • Packaging

Total Items: 5 items

Expected Completion: Feb 26, 2026 by 02:00 PM

Items by Department:

Production:
  ┌─────────────────────────────────────────────────────┐
  │ Item          Qty      Unit    Notes                │
  ├─────────────────────────────────────────────────────┤
  │ Cake 500g     100      pcs     Chocolate flavor     │
  │ Cake 1kg      50       pcs     Vanilla flavor       │
  └─────────────────────────────────────────────────────┘

Packaging:
  ┌─────────────────────────────────────────────────────┐
  │ Item              Qty      Unit                     │
  ├─────────────────────────────────────────────────────┤
  │ Cake 500g Box     100      pcs                      │
  │ Cake 1kg Box      50       pcs                      │
  │ Product Labels    150      pcs                      │
  └─────────────────────────────────────────────────────┘

Please review and schedule production accordingly.

[View Request →]

Best regards,
SweetTooth Production System
```

---

## Blade Template Integration

### Sales/Production Display Components

```blade
{{-- Priority Badge --}}
@if(!empty($notification->data['context']['priority']))
    @php
        $priorityStyles = [
            'Urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'High' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'Normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'Low' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400',
        ];
        $style = $priorityStyles[$notification->data['context']['priority']] ?? $priorityStyles['Normal'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $style }}">
        {{ $notification->data['context']['priority'] }}
    </span>
@endif

{{-- Order Type Badge --}}
@if(!empty($notification->data['context']['order_type']))
    @php
        $orderTypeIcons = [
            'Dine-In' => '🍽️',
            'Takeaway' => '🛍️',
            'Delivery' => '🚚',
            'Catering' => '🎉',
        ];
        $icon = $orderTypeIcons[$notification->data['context']['order_type']] ?? '📦';
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
        <span class="mr-1">{{ $icon }}</span>
        {{ $notification->data['context']['order_type'] }}
    </span>
@endif

{{-- Items by Department Table --}}
@if(!empty($notification->data['items_by_department']))
    <div class="mt-4 space-y-4">
        @foreach($notification->data['items_by_department'] as $deptName => $items)
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                        {{ $deptName }}
                    </span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ count($items) }} items
                    </span>
                </div>
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-full text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Item</th>
                                <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Qty</th>
                                <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Unit</th>
                                @if(isset($items[0]['notes']))
                                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Notes</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="border-t border-zinc-100 dark:border-zinc-800">
                                    <td class="px-3 py-2 text-zinc-800 dark:text-zinc-200">{{ $item['name'] }}</td>
                                    <td class="px-3 py-2 text-right font-medium text-zinc-800 dark:text-zinc-200">{{ $item['quantity'] }}</td>
                                    <td class="px-3 py-2 text-right text-zinc-500 dark:text-zinc-400">{{ $item['unit'] }}</td>
                                    @if(isset($item['notes']))
                                        <td class="px-3 py-2 text-zinc-500 dark:text-zinc-400 text-xs">{{ $item['notes'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Expected Completion Display --}}
@if(!empty($notification->data['context']['expected_completion']))
    <div class="mt-3 flex items-center gap-2 text-xs">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-zinc-600 dark:text-zinc-400">Expected Completion:</span>
        <span class="font-medium text-zinc-800 dark:text-zinc-200">
            {{ $notification->data['context']['expected_completion'] }}
        </span>
    </div>
@endif

{{-- Customer Info Display --}}
@if(!empty($notification->data['context']['customer_name']))
    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
        <span class="font-medium">Customer:</span> 
        {{ $notification->data['context']['customer_name'] }}
    </div>
@endif
```

---

## Implementation Checklist

- [ ] Review `SalesInvoiceNotification.php` (already good)
- [ ] Review `SalesReceiptNotification.php` (already good)
- [ ] Update `SalesProductionRequestCreatedNotification.php`
- [ ] Add priority badge component
- [ ] Add order type badge with icons
- [ ] Add items by department table
- [ ] Add expected completion display
- [ ] Test production request notification
- [ ] Test invoice email rendering
- [ ] Test receipt email rendering
