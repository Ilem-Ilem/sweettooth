# Phase 2: Automatic GL Posting - Quick Start Guide

## What Was Built

Phase 2 automatically posts transactions to the General Ledger using Laravel model observers. When sales, purchases, payments, or inventory adjustments occur, they're automatically recorded in GL.

## File Structure

```
app/
├── Observers/
│   ├── SaleObserver.php           ← Watches Sales model
│   ├── PurchaseObserver.php       ← Watches Purchases model
│   ├── PaymentObserver.php        ← Watches Payments model
│   └── StockMovementObserver.php  ← Watches Stock Movements
├── Providers/
│   └── AppServiceProvider.php     ← Registers all observers
└── Models/
    ├── Sale.php                   ← Added GL fields & relationship
    ├── Purchase.php               ← Added GL fields & relationship
    ├── Payment.php                ← Added GL fields & relationship
    └── StockMovement.php          ← Added GL fields & relationship

database/migrations/
├── 2025_12_13_000007_add_gl_references_to_sales_table.php
├── 2025_12_13_000008_add_gl_references_to_purchases_table.php
├── 2025_12_13_000009_add_gl_references_to_payments_table.php
└── 2025_12_13_000010_add_gl_references_to_stock_movements_table.php
```

## How to Deploy

```bash
# 1. Pull latest code
git pull

# 2. Run migrations
php artisan migrate

# 3. Verify observers are registered
php artisan tinker
> GlEntry::count()  # Should work without errors

# 4. Done! Observers now active
```

## Testing (No Code Needed)

Just use the app normally:

1. **Create a Sale** → Complete it → See GL entries auto-created
2. **Create a Purchase** → Approve it → See GL entries auto-created
3. **Record a Payment** → Complete it → See GL entries auto-created
4. **Record Damage** → See GL entries auto-created

Check in `gl_entries` table or use accounting dashboard to verify.

## What Happens Automatically

| Action | Observer Fires When | GL Entries Created |
|--------|---------------------|-------------------|
| Sale | Status = 'completed' AND Fully Paid | Revenue + COGS + Tax |
| Purchase | Status = 'approved' | Inventory + AP |
| Payment | Status = 'completed' | AP + Cash |
| Damage | Type = 'damage' created | Loss + Inventory |
| Shrinkage | Type = 'shrinkage' created | Loss + Inventory |

## Check GL Posting Status

Each transaction now has these fields:

```php
// All transaction models have:
$model->gl_posting_status    // 'pending' | 'posted' | 'failed'
$model->gl_posted_at         // Timestamp when posted
$model->gl_posting_error     // Error message if failed
$model->gl_entry_id          // Reference to GL entry
```

Example:

```php
php artisan tinker

$sale = Sale::find(1);
$sale->gl_posting_status;   // 'posted'
$sale->gl_posted_at;        // 2025-12-13 10:30:45
$sale->gl_entry_id;         // 1
```

## Troubleshooting

### GL not posting?
1. Check accounting period is OPEN
2. Check transaction status meets observer trigger
3. Check `gl_posting_error` field for error message
4. Check logs: `tail storage/logs/laravel.log`

### Why is status 'failed'?
- Accounting period is closed
- GL account doesn't exist or is inactive
- Missing observer trigger condition

### How to retry failed postings?
```php
php artisan tinker

// Find failed posting
$sale = Sale::where('gl_posting_status', 'failed')->first();

// Check error
echo $sale->gl_posting_error;

// Fix the issue (e.g., open accounting period)

// Retry manually
$sale->update(['gl_posting_status' => 'pending']);
// Observer won't fire, but you can manually call:
$glService = app(GlPostingService::class);
$glService->postSaleTransaction($sale);
```

## Common Questions

**Q: Does posting block the transaction?**
No. If GL posting fails, the transaction is saved but marked as failed in `gl_posting_status`.

**Q: Can I post manually?**
Yes. Use `GlPostingService` directly if needed. But normally observers handle it.

**Q: What if posting happens twice?**
Can't happen. Observers check `gl_posting_status` before posting.

**Q: How do I know if posting worked?**
Check `gl_posting_status` field:
- `pending` = Hasn't been posted yet
- `posted` = Successfully posted
- `failed` = Posting failed (check `gl_posting_error`)

## Key Accounts Used

| Transaction | Debit Account | Credit Account |
|-------------|---------------|-----------------|
| Sales Revenue | 1010/1050 (Cash) | 4010 (Revenue) |
| COGS | 5010 (COGS) | 1220 (Inventory) |
| Purchases | 1200 (Inventory) | 2010 (AP) |
| Payments | 2010 (AP) | 1010/1050 (Cash) |
| Damage Loss | 5020 (Damage) | 1220 (Inventory) |
| Shrinkage Loss | 5030 (Shrinkage) | 1220 (Inventory) |

See `ACCOUNTING_PHASE1_COMPLETE.md` for full chart of accounts.

## Database Fields Added

Each transaction table now has:

```sql
gl_entry_id          BIGINT UNSIGNED NULL
gl_posting_status    VARCHAR(20) DEFAULT 'pending'
gl_posting_error     TEXT NULL
gl_posted_at         TIMESTAMP NULL
```

Indexed for fast lookups:
- `INDEX gl_entry_id`
- `INDEX gl_posting_status`

## Validation Checks

Before posting, GlPostingService verifies:

- ✓ Accounting period is open
- ✓ GL accounts exist and are active
- ✓ Journal entries are balanced (debit = credit)
- ✓ Transaction meets observer trigger conditions

If any check fails → status set to 'failed' + error logged.

## Next Steps (Phase 3)

Once Phase 2 is stable, we'll build:
- Financial report services (GL, TB, P&L, B/S)
- Report UI components
- Export to PDF/Excel

---

**For detailed info:** See `ACCOUNTING_PHASE2_IMPLEMENTATION.md`
**For testing:** Run `php artisan test tests/Feature/AccountingPhase2ObserversTest.php`
