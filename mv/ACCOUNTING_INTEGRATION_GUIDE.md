# Accounting System Integration Guide

## Overview
The accounting system is now linked to your operational data (sales, purchases, inventory). GL entries are automatically created when transactions complete.

## How It Works

### Automatic GL Posting (Observer Pattern)
When operational events occur, GL entries are automatically created:

1. **Sales** → GL entries created when:
   - Sale status = `completed`
   - Payment received fully (all invoices paid)
   - Triggers: Revenue (Credit) + Cash (Debit) + COGS (Debit) + Inventory (Credit)

2. **Purchases** → GL entries created when:
   - Purchase status = `approved`
   - Triggers: Inventory (Debit) + Accounts Payable (Credit)

3. **Payments** → GL entries created when:
   - Payment status = `completed`
   - Triggers: Accounts Payable (Debit) + Cash (Credit)

4. **Inventory Adjustments** (Damage/Shrinkage) → GL entries created when:
   - StockMovement type = `damage` or `shrinkage`
   - Triggers: Loss Expense (Debit) + Inventory (Credit)

### GL Account Mapping

```
ASSET ACCOUNTS
├─ 1010: Cash
├─ 1020: Bank Account
└─ 1220: Inventory

LIABILITY ACCOUNTS
├─ 2010: Accounts Payable
└─ 2020: Sales Tax Payable

REVENUE ACCOUNTS
├─ 4010: Sales Revenue
└─ 4020: Other Income

EXPENSE ACCOUNTS
├─ 5010: Cost of Goods Sold
├─ 6010: Salaries & Wages
├─ 6020: Rent Expense
├─ 6030: Utilities
└─ 6040: Inventory Loss
```

## Integration Steps

### 1. Verify GL Accounts Exist
```bash
php artisan accounting:verify-accounts
```
This command:
- Checks if all required GL accounts exist
- Creates missing accounts if you approve
- Shows a summary of account status

### 2. Set Up Accounting Period
```bash
php artisan accounting:create-period
```
Or use the UI: Dashboard → Accounting → Periods

Current open period is required for GL posting.

### 3. Backfill Historical Data
If you have existing sales/purchases that were completed before the accounting module was integrated:

```bash
# Backfill all transaction types
php artisan accounting:backfill-gl

# Or backfill specific types
php artisan accounting:backfill-gl --type=sales
php artisan accounting:backfill-gl --type=purchases
php artisan accounting:backfill-gl --type=payments
php artisan accounting:backfill-gl --type=adjustments
```

This command:
- Posts all completed sales to GL
- Posts all approved purchases to GL
- Posts all completed payments to GL
- Posts all inventory adjustments to GL
- Shows success/failure count

### 4. Verify GL Balancing
Go to Dashboard → Accounting → Dashboard to check:
- Total Debits vs Total Credits (should be balanced)
- Posted entries count
- Failed postings (if any)

## Troubleshooting

### No GL Entries Created
**Check:**
1. Is the accounting period open?
   ```bash
   php artisan accounting:create-period
   ```

2. Do all required GL accounts exist?
   ```bash
   php artisan accounting:verify-accounts
   ```

3. Check error logs:
   ```bash
   tail -f storage/logs/laravel.log | grep -i "gl\|posting"
   ```

### Failed Postings
View failed postings:
- Dashboard → Accounting → GL Posting Status Monitor
- Select failed transaction type
- Click "Retry" to attempt reposting

Or check the logs:
```bash
tail -f storage/logs/laravel.log | grep "Failed to post"
```

### GL Not Balanced
Check these:
1. All sales have matching COGS entries
2. All purchases have matching AP entries
3. All payments have matching cash entries
4. Run trial balance: Dashboard → Accounting → Reports → Trial Balance

## Data Flow Diagram

```
Sales Transaction
    ↓
Sale Status = 'completed'
    ↓
[SaleObserver triggered]
    ↓
GlPostingService::postSaleTransaction()
    ↓
Creates GL Entries:
├─ Debit: Cash Account
├─ Credit: Sales Revenue
├─ Debit: COGS
└─ Credit: Inventory
    ↓
gl_posting_status = 'posted'
    ↓
GL Entry visible in:
├─ GL Account Ledger
├─ Trial Balance
└─ Financial Reports
```

## Important Notes

1. **GL Period**: All GL entries must belong to an open accounting period
2. **Account Codes**: Account numbers (1010, 4010, etc.) must match your chart of accounts
3. **Audit Trail**: All GL postings are logged and auditable
4. **Double Entry**: Every GL entry maintains double-entry bookkeeping (Debits = Credits)
5. **Reversals**: Use "Reverse" function in GL to reverse incorrect entries (not delete)

## Next Steps

1. Run `php artisan accounting:verify-accounts`
2. Create an accounting period for current month
3. Verify your stock and sales values are reflected in GL
4. Run `php artisan accounting:backfill-gl` if needed for historical data
5. Check trial balance to ensure GL is balanced
6. Generate financial reports: Balance Sheet, Income Statement, etc.
