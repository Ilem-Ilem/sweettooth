# Phase 2: Observer Visual Guide

## How Observers Work - Step by Step

### The Big Picture

```
USER ACTION
    ↓
MODEL SAVED TO DATABASE
    ↓
LARAVEL FIRES EVENT
    ↓
OBSERVER LISTENS
    ↓
OBSERVER CHECKS CONDITIONS
    ↓
CONDITIONS MET?
    ├─ YES → CALL GLPOSTINGSERVICE
    │         ↓
    │      CREATE GL ENTRIES
    │         ↓
    │      UPDATE TRANSACTION STATUS
    │
    └─ NO → SKIP (do nothing)
```

---

## Observer 1: SaleObserver

### What It Watches
```
Sale Model Events:
├── created → Ignored (sale not completed yet)
└── updated → Checks for status change
```

### When It Triggers
```
CONDITION CHECKLIST:
✓ Sale status was changed
✓ New status = 'completed'
✓ Sale is fully paid (getTotalPaid() == total)
✓ GL posting status = 'pending' (not already posted)

ALL 4 MUST BE TRUE → Observer fires
```

### What It Does
```
Calls: GlPostingService->postSaleTransaction($sale)

Creates GL Entries:
├─ Entry 1: Revenue
│  ├─ Debit: Cash/Bank (1010 or 1050)
│  └─ Credit: Sales Revenue (4010)
│
├─ Entry 2: COGS (if items have costs)
│  ├─ Debit: COGS (5010)
│  └─ Credit: Inventory (1220)
│
└─ Entry 3 (Optional): Tax (if tax > 0)
   ├─ Debit: Sales Tax Payable (2020)
   └─ Credit: Cash (for collection)

Then Updates Sale:
├─ gl_posting_status = 'posted'
├─ gl_posted_at = now()
└─ gl_posting_error = null
```

### Example Flow

```
APP ACTION:
  $sale->update(['status' => 'completed']);

LARAVEL:
  ✓ Saves to database
  ✓ Fires 'updated' event

OBSERVER CHECKS:
  ✓ status was changed? YES (pending → completed)
  ✓ new status = completed? YES
  ✓ fully paid? YES ($1000 paid, $1000 total)
  ✓ gl_posting_status = pending? YES

OBSERVER EXECUTES:
  → Calls postSaleTransaction()
  → Creates 3 GL entries
  → Sets gl_posting_status = 'posted'
  → Logs: "Sale posted to GL"

RESULT:
  $sale->gl_posting_status  // 'posted'
  $sale->gl_posted_at       // 2025-12-13 10:30:45
  GlEntry::count()          // +3 entries in GL
```

---

## Observer 2: PurchaseObserver

### What It Watches
```
Purchase Model Events:
├── created → Ignored (purchase not approved yet)
└── updated → Checks for status change
```

### When It Triggers
```
CONDITION CHECKLIST:
✓ Purchase status was changed
✓ New status = 'approved'
✓ GL posting status = 'pending' (not already posted)

ALL 3 MUST BE TRUE → Observer fires
```

### What It Does
```
Calls: GlPostingService->postPurchaseTransaction($purchase)

Creates GL Entries:
├─ Entry 1: Inventory
│  └─ Debit: Inventory (1200)
│
└─ Entry 2: Accounts Payable
   └─ Credit: Accounts Payable (2010)

Total Amount = total_fob_ngn + other_costs

Then Updates Purchase:
├─ gl_posting_status = 'posted'
├─ gl_posted_at = now()
└─ gl_posting_error = null
```

### Example Flow

```
APP ACTION:
  $purchase->update(['status' => 'approved']);

OBSERVER CHECKS:
  ✓ status was changed? YES (draft → approved)
  ✓ new status = approved? YES
  ✓ gl_posting_status = pending? YES

OBSERVER EXECUTES:
  → Calls postPurchaseTransaction()
  → Creates 2 GL entries (1200 debit, 2010 credit)
  → Sets gl_posting_status = 'posted'
  → Logs: "Purchase posted to GL"

RESULT:
  $purchase->gl_posting_status  // 'posted'
  GlEntry::count()              // +2 entries
```

---

## Observer 3: PaymentObserver

### What It Watches
```
Payment Model Events:
├── created → Ignored (payment not yet completed)
└── updated → Checks for status change
```

### When It Triggers
```
CONDITION CHECKLIST:
✓ Payment status was changed
✓ New status = 'completed'
✓ GL posting status = 'pending' (not already posted)

ALL 3 MUST BE TRUE → Observer fires
```

### What It Does
```
Calls: GlPostingService->postPaymentTransaction($payment)

Creates GL Entries:
├─ Entry 1: Accounts Payable
│  └─ Debit: Accounts Payable (2010)
│
└─ Entry 2: Cash/Bank
   └─ Credit: Cash/Bank (1010 or 1050)

Amount = payment.amount

Then Updates Payment:
├─ gl_posting_status = 'posted'
├─ gl_posted_at = now()
└─ gl_posting_error = null
```

### Example Flow

```
APP ACTION:
  $payment->update(['status' => 'completed']);

OBSERVER CHECKS:
  ✓ status was changed? YES (pending → completed)
  ✓ new status = completed? YES
  ✓ gl_posting_status = pending? YES

OBSERVER EXECUTES:
  → Calls postPaymentTransaction()
  → Creates 2 GL entries (2010 debit, 1050 credit)
  → Sets gl_posting_status = 'posted'

RESULT:
  $payment->gl_posting_status  // 'posted'
  GlEntry::count()             // +2 entries
```

---

## Observer 4: StockMovementObserver

### What It Watches
```
StockMovement Model Events:
├── created → Checks if damage/shrinkage
│   ├─ type = 'damage' → Observer fires
│   ├─ type = 'shrinkage' → Observer fires
│   ├─ type = 'in' → Ignored
│   ├─ type = 'out' → Ignored
│   ├─ type = 'transfer' → Ignored
│   └─ type = 'return' → Ignored
│
└── updated → Ignored (movements not adjusted)
```

### When It Triggers
```
CONDITION CHECKLIST:
✓ Movement type = 'damage' OR 'shrinkage'
✓ GL posting status = 'pending' (not already posted)

BOTH MUST BE TRUE → Observer fires
```

### What It Does
```
Calls: GlPostingService->postInventoryAdjustment($movement)

Creates GL Entries:
├─ Entry 1: Loss Account (depends on type)
│  ├─ If type='damage':
│  │  └─ Debit: Damage Loss (5020)
│  │
│  └─ If type='shrinkage':
│     └─ Debit: Shrinkage Loss (5030)
│
└─ Entry 2: Inventory
   └─ Credit: Inventory (1220)

Amount = quantity * unit_cost

Then Updates Movement:
├─ gl_posting_status = 'posted'
├─ gl_posted_at = now()
└─ gl_posting_error = null
```

### Example Flow

```
APP ACTION:
  StockMovement::create([
    'stock_id' => 1,
    'type' => 'damage',
    'quantity' => 5,
    'moved_by_type' => Employee::class,
    'moved_by_id' => 1
  ]);

OBSERVER CHECKS:
  ✓ type = 'damage'? YES
  ✓ gl_posting_status = pending? YES

OBSERVER EXECUTES:
  → Calls postInventoryAdjustment()
  → Creates 2 GL entries (5020 debit, 1220 credit)
  → Sets gl_posting_status = 'posted'
  → Logs: "Stock movement posted to GL"

RESULT:
  $movement->gl_posting_status  // 'posted'
  GlEntry::count()              // +2 entries
```

---

## Error Handling Flow

### When GL Posting Fails

```
Observer calls GlPostingService method
  ↓
Service tries to create GL entries
  ↓
ERROR occurs (e.g., accounting period closed)
  ↓
Exception caught in observer
  ↓
Observer updates transaction:
├─ gl_posting_status = 'failed'
├─ gl_posting_error = error message
└─ No gl_posted_at set

Then:
├─ Error logged to laravel.log
├─ Transaction still saved in database
└─ App continues normally (non-blocking)

USER SEES:
├─ Transaction saved successfully
├─ gl_posting_status = 'failed'
└─ gl_posting_error = "No open accounting period found"
```

### Recovery

```
AFTER ERROR FIXED:

1. Verify issue is resolved
   (e.g., open accounting period)

2. Retry posting:
   $transaction->update(['gl_posting_status' => 'pending']);
   
3. Observer fires again automatically
   (since status back to 'pending')

4. GL entries created
   gl_posting_status = 'posted'
```

---

## Visual: Observer Registration

### Where Observers Are Registered

```php
// app/Providers/AppServiceProvider.php

class AppServiceProvider extends ServiceProvider {
    
    public function boot(): void {
        
        // OLD OBSERVERS (Phase 1)
        Department::observe(DepartmentObserver::class);
        Role::observe(RoleObserver::class);
        
        // NEW OBSERVERS (Phase 2)
        Sale::observe(SaleObserver::class);           ← Watches Sales
        Purchase::observe(PurchaseObserver::class);   ← Watches Purchases
        Payment::observe(PaymentObserver::class);     ← Watches Payments
        StockMovement::observe(StockMovementObserver::class);  ← Watches Stock Movements
    }
}
```

---

## Database Changes Visual

### New Columns in Transaction Tables

```
SALES TABLE:
┌─────────────────────────────────────────┐
│ sale_number │ total │ status │ ...      │
├─────────────────────────────────────────┤
│ SAL-001     │ 1000  │ completed│ ...   │
└─────────────────────────────────────────┘
         ↓ PHASE 2 ADDS:
┌──────────────────────────────────────────────────────────┐
│ gl_entry_id │ gl_posting_status │ gl_posted_at │ error  │
├──────────────────────────────────────────────────────────┤
│ 5           │ posted            │ 2025-12-13   │ null   │
└──────────────────────────────────────────────────────────┘
```

### Relationship Added

```
Sale Model:
├─ $sale->payments() ← already existed
├─ $sale->saleItems() ← already existed
└─ $sale->glEntry() ← NEW in Phase 2
   └─ Returns the GlEntry created during posting
```

---

## Integration with GlPostingService

### Service Methods Called by Observers

```
SaleObserver
    └─→ GlPostingService::postSaleTransaction()
        └─ Creates 3-4 GL entries
        └ Returns true/throws exception

PurchaseObserver
    └─→ GlPostingService::postPurchaseTransaction()
        └─ Creates 2 GL entries
        └ Returns true/throws exception

PaymentObserver
    └─→ GlPostingService::postPaymentTransaction()
        └─ Creates 2 GL entries
        └ Returns true/throws exception

StockMovementObserver
    └─→ GlPostingService::postInventoryAdjustment()
        └─ Creates 2 GL entries
        └ Returns true/throws exception
```

### Service Validations

Before creating GL entries, service validates:
```
✓ Accounting period exists and is OPEN
✓ GL accounts exist and are ACTIVE
✓ Debit entries sum = Credit entries sum
✓ Amount > 0
```

---

## Testing the Observers

### Manual Test Example

```
TERMINAL:

$ php artisan tinker

PHP > $sale = Sale::create([
    'sales_shift_id' => 1,
    'branch_id' => 1,
    'department_id' => 1,
    'sold_by_type' => 'App\Models\Employee',
    'sold_by_id' => 1,
    'sale_number' => 'TEST-001',
    'sale_time' => now(),
    'subtotal' => 1000,
    'tax' => 0,
    'discount' => 0,
    'total' => 1000,
    'status' => 'pending'
]);

PHP > echo $sale->gl_posting_status;
pending

PHP > Payment::create([
    'sale_id' => $sale->id,
    'payment_method' => 'cash',
    'amount' => 1000,
    'status' => 'pending'
]);

PHP > $sale->update(['status' => 'completed']);

PHP > $sale->refresh();

PHP > echo $sale->gl_posting_status;
posted    ← OBSERVER FIRED!

PHP > GlEntry::where('reference_type', 'App\Models\Sale')
           ->where('reference_id', $sale->id)
           ->count();
3         ← THREE GL ENTRIES CREATED!
```

---

## Observer Flowchart

### Complete Flow Diagram

```
                    USER ACTION
                         ↓
        Model Method Called (e.g., $sale->update())
                         ↓
        Transaction Saved to Database
                         ↓
        Laravel Fires Model Event
        ├─ Event Name: 'created' or 'updated'
        └─ Model Instance: the transaction
                         ↓
        Check Registered Observers
        └─ For Sale: SaleObserver registered? YES
                         ↓
        Observer Method Called (e.g., updated())
                         ↓
        Observer Checks Trigger Conditions
        ├─ Is status changed?
        ├─ Is new status = expected value?
        └─ Is gl_posting_status = 'pending'?
                         ↓
        All Conditions Met?
        ├─ YES → Continue
        └─ NO → Return (do nothing)
                         ↓
        Call GlPostingService Method
        ├─ Validate GL accounts exist
        ├─ Open transaction
        ├─ Create GL entries
        ├─ Validate debit = credit
        ├─ Commit transaction
        └─ Return true
                         ↓
        Success?
        ├─ YES → Update transaction:
        │        ├─ gl_posting_status = 'posted'
        │        ├─ gl_posted_at = now()
        │        ├─ gl_posting_error = null
        │        └─ Log success
        │
        └─ NO → Catch exception:
                ├─ Update transaction:
                │  ├─ gl_posting_status = 'failed'
                │  └─ gl_posting_error = message
                └─ Log error
                         ↓
        Observer Complete
                         ↓
        Return to Original Request
                         ↓
        Application Continues
```

---

## Performance Considerations

### Execution Timeline

```
Typical Transaction Processing:

$sale->update(['status' => 'completed'])
│
├─ Database save: ~5ms
│
├─ Observer fires: ~0.1ms
│
└─ GL posting:
   ├─ Validate period: ~2ms
   ├─ Validate accounts: ~3ms
   ├─ Create GL entries: ~50ms
   │  ├─ Query GL accounts
   │  ├─ Insert entries
   │  └─ Commit transaction
   ├─ Update transaction: ~2ms
   └─ Total GL posting: ~57ms

TOTAL TIME: ~70ms (well under typical threshold)

If posting slow:
├─ Check database indexes
├─ Check GL account count
└─ Review logs for errors
```

---

## Summary

Observers automatically post transactions to GL when they meet trigger conditions:

| Model | Trigger | Posts |
|-------|---------|-------|
| Sale | completed + fully paid | Revenue + COGS + Tax |
| Purchase | status = approved | Inventory + AP |
| Payment | status = completed | AP + Cash |
| StockMovement | type = damage/shrinkage | Loss + Inventory |

All GL posting is non-blocking, with errors logged for manual review.

---

For detailed information, see: `ACCOUNTING_PHASE2_IMPLEMENTATION.md`
