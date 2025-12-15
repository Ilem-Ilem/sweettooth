# Critical Items & Priorities

## Priority 1: MUST DO BEFORE GO-LIVE

### 1. Verify Migrations Have Run
```bash
php artisan migrate:status
```
Check that all accounting tables exist:
- `gl_accounts`
- `gl_entries`
- `accounting_periods`
- `bank_accounts`
- `daily_bank_positions`
- `daily_bank_transactions`
- `cash_positions`

### 2. Seed Chart of Accounts
```bash
php artisan db:seed --class=ChartOfAccountsSeeder
php artisan db:seed --class=AccountingAccessControlSeeder
php artisan db:seed --class=AccountantRoleSeeder
```

### 3. Create First Accounting Period
Before any GL posting can work, you MUST have an open accounting period:
- Go to `/branch-dashboard/accounting/periods`
- Create period for current month (e.g., December 2025)
- Set status to "Open"

### 4. Assign Accounting Permissions
Users need these permissions to access accounting:
- `access_accounting` - View accounting dashboard
- `view_financial_reports` - View reports
- `manage_accounts` - Manage GL accounts
- `manage_periods` - Open/close periods
- `create_journal_entries` - Create manual entries

---

## Priority 2: DATA INTEGRITY CHECKS

### Verify GL Balance Integrity
After initial setup, check:
1. **Trial Balance must balance** - Total Debits = Total Credits
2. **Balance Sheet equation** - Assets = Liabilities + Equity
3. **No orphan entries** - All GL entries have valid account IDs

### Reconcile Existing Transactions
If the system has existing sales/purchases before GL was enabled:
1. Check `gl_posting_status` field on Sales/Purchases
2. Any with status `pending` need manual review
3. Consider creating opening balance entries

---

## Priority 3: CRITICAL OBSERVERS

These observers MUST be registered in `app/Providers/AppServiceProvider.php`:

```php
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Observers\SaleObserver;
use App\Observers\PurchaseObserver;
use App\Observers\PaymentObserver;
use App\Observers\StockMovementObserver;

public function boot(): void
{
    Sale::observe(SaleObserver::class);
    Purchase::observe(PurchaseObserver::class);
    Payment::observe(PaymentObserver::class);
    StockMovement::observe(StockMovementObserver::class);
}
```

**Without these observers, NO automatic GL posting will occur!**

---

## Priority 4: KEY GL ACCOUNT NUMBERS

These account numbers are hardcoded in `GlPostingService.php`:

| Account # | Name | Type | Used For |
|-----------|------|------|----------|
| 1010 | Cash - Head Office | Asset | Cash payments |
| 1050 | Bank Account - Main | Asset | Bank/POS payments |
| 1220 | Inventory - Finished Goods | Asset | COGS entries |
| 2010 | Accounts Payable | Liability | Purchase entries |
| 2020 | Sales Tax Payable | Liability | Tax collected |
| 4010 | Sales Revenue - Retail | Revenue | Sale revenue |
| 5010 | Cost of Goods Sold | COGS | Sale COGS |

**These accounts MUST exist in `gl_accounts` table with exact account numbers!**

---

## Priority 5: COMMON FAILURE POINTS

### GL Posting Fails - "No open accounting period"
**Cause:** No accounting period exists or all periods are closed
**Fix:** Create/open an accounting period for current month

### GL Posting Fails - "Account not found"
**Cause:** Required GL account doesn't exist
**Fix:** Run `ChartOfAccountsSeeder` or manually create missing account

### Sales Not Posting to GL
**Cause:** Observer not registered OR sale not marked as "completed"
**Fix:**
1. Check observer is registered
2. Verify sale status is "completed" and `isFullyPaid()` returns true

### Trial Balance Doesn't Balance
**Cause:** Incomplete journal entries (one-sided entries)
**Fix:** Review recent GL entries, find unbalanced transactions, create correcting entries

---

## Priority 6: SECURITY CONSIDERATIONS

### Role-Based Access
Accounting routes are protected by middleware:
```php
->middleware('role_or_permission:access_accounting,view_financial_reports')
```

Ensure only authorized users have these roles:
- `super-admin`
- `md`
- `admin`
- `accountant`

### Audit Trail
All GL entries track:
- `entered_by_id` - Who created the entry
- `posted_by_id` - Who posted the entry
- `posted_at` - When it was posted
- `reversed_by_id` / `reversed_at` - If reversed

**Never delete GL entries - always reverse them!**

---

## Priority 7: PERIOD CLOSING CHECKLIST

Before closing a period:
1. [ ] All transactions for period are recorded
2. [ ] All GL postings completed (no pending)
3. [ ] Trial Balance balances
4. [ ] Bank reconciliation complete
5. [ ] Generate and save financial statements
6. [ ] Manager/MD approval obtained
7. [ ] Close period in system

**Closed periods prevent new entries - this is intentional!**

---

## Priority 8: BACKUP BEFORE CHANGES

Before any major accounting changes:
```bash
# Backup accounting tables
mysqldump -u user -p database gl_accounts gl_entries accounting_periods > accounting_backup.sql
```

---

## Critical Error Recovery

### If GL Gets Out of Sync
1. Identify the bad entries (check `gl_entries` by date range)
2. Create reversing entries (don't delete!)
3. Re-post correct entries
4. Verify trial balance

### If Period Was Closed Too Early
1. Only super-admin can reopen periods
2. Go to Period Management
3. Change status from "closed" to "open"
4. Make corrections
5. Re-close period

### If Chart of Accounts Needs Changes
1. **Never delete accounts with entries** - mark inactive instead
2. Add new accounts as needed
3. Update `GlPostingService.php` if account numbers change
4. Test posting with new accounts

---

## Emergency Contacts

For accounting system issues:
1. Check `storage/logs/laravel.log` for GL posting errors
2. Look for entries with `gl_posting_status = 'failed'`
3. Review `gl_posting_error` field for error messages
