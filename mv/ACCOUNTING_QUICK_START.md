# Accounting System - Quick Start Guide

## 5-Minute Setup

### Step 1: Create First Accounting Period
1. Login as Super Admin
2. Go to: `https://yourdomain/branch-dashboard/accounting/dashboard`
3. Click "Accounting Periods"
4. Click "Create Period"
5. Select month: December 2025
6. Set status: **Open**
7. Save

### Step 2: Verify Chart of Accounts
1. From Accounting Dashboard, click "Chart of Accounts"
2. You should see 40+ GL accounts
3. Key accounts to look for:
   - **1010** - Cash - Head Office
   - **1050** - Bank Account - Main
   - **1220** - Inventory - Finished Goods
   - **2010** - Accounts Payable
   - **4010** - Sales Revenue - Retail
   - **5010** - Cost of Goods Sold

### Step 3: Assign Accounting Permissions
For users to access accounting, assign role or permissions:
- **Super Admin** - Automatic access
- **MD** - Automatic access
- **Admin** - Needs `access_accounting` permission
- **Accountant** - Needs `access_accounting` permission
- **Other** - Needs explicit `access_accounting` permission

## Using Accounting Features

### Daily Operations

#### Record a Sale
1. Go to Sales Dashboard → POS
2. Create sale as normal
3. System automatically:
   - Posts revenue to GL (account 4010)
   - Posts COGS (account 5010)
   - Posts cash/bank credit

#### Record a Purchase
1. Go to Inventory → Purchases
2. Create purchase as normal
3. System automatically:
   - Posts inventory debit (account 1220)
   - Posts AP credit (account 2010)

#### Record a Payment
1. Go to appropriate module
2. Record payment
3. System automatically:
   - Reduces AP (account 2010)
   - Reduces cash/bank (account 1050)

### End-of-Day Tasks

#### Record Cash Count
1. Go to `/branch-dashboard/accounting/cash-positions`
2. Click "Record Cash Count"
3. Select cash type: Sales Cash / Petty Cash / Other
4. Enter physical count
5. Add notes if variance exists
6. Save

#### Monitor Bank Positions
1. Go to `/branch-dashboard/accounting/bank-positions`
2. Select date range
3. View opening/closing balances
4. See pending items
5. Filter by specific bank

### End-of-Month Tasks

#### Perform Bank Reconciliation
1. Get bank statement
2. Go to `/branch-dashboard/accounting/bank-reconciliation`
3. Select bank account
4. Enter statement date and closing balance
5. System shows unmatched items
6. Click "Auto-Match" to match by amount/date
7. Manually match remaining items
8. When difference = 0, reconciliation complete

#### Check Trial Balance
1. Go to Accounting Dashboard
2. Navigate to "Financial Reports" → "Trial Balance"
3. If balanced: Green checkmark ✓
4. If not balanced: Red warning ⚠️
   - Review recent GL entries
   - Check for one-sided entries
   - Create correcting entries if needed

#### Generate Month-End Reports
1. Trial Balance: `/branch-dashboard/accounting/reports/trial-balance`
2. Income Statement: `/branch-dashboard/accounting/reports/income-statement`
3. Balance Sheet: `/branch-dashboard/accounting/reports/balance-sheet`
4. General Ledger: `/branch-dashboard/accounting/reports/general-ledger`

#### Close Accounting Period
1. Go to Accounting → Periods
2. Ensure all transactions are recorded
3. Verify Trial Balance is balanced
4. Click "Close Period" (only for completed month)
5. Cannot reopen unless Super Admin

## Troubleshooting

### Problem: "No open accounting period"
**Solution:** Create an accounting period first (see Step 1 above)

### Problem: Sales not appearing in GL
**Check:**
1. Sale status is "completed" or "closed"
2. Sale is "fully paid"
3. Observers are running (check logs)
4. Period is "Open"

**Fix:**
1. Check `storage/logs/laravel.log` for GL posting errors
2. Look at sale record for `gl_posting_status` field
3. If status is "failed", check `gl_posting_error` message
4. Create manual GL entry to correct

### Problem: Trial Balance not balanced
**Check:**
1. One account might be unbalanced
2. Find missing or duplicate entries
3. Check for incomplete entries (debit only, no credit)

**Fix:**
1. Create reversing entry for incorrect transaction
2. Create correcting entry with correct amounts
3. Never delete GL entries!

### Problem: Cannot access accounting module
**Check:**
1. User has `access_accounting` permission
2. User is logged in as Super Admin or employee with role
3. Route exists (check routes with `php artisan route:list --name=accounting`)

**Fix:**
1. Go to Roles & Permissions
2. Assign `access_accounting` permission to user's role
3. Clear cache: `php artisan cache:clear`

## Dashboard Navigation

From the Accounting Dashboard, quick-access these features:

| Link | Purpose |
|------|---------|
| Chart of Accounts | Manage GL accounts |
| Accounting Periods | Create/open/close periods |
| Journal Entries | Manually create GL entries |
| Bank Reconciliation | Match GL to bank statement |
| Bank Positions | View daily bank balances |
| Cash Positions | Record cash counts |
| Trial Balance | Verify accounting balance |
| Balance Sheet | View financial position |

## Important Rules

### GL Entry Rules
- **Never delete** GL entries - always reverse them
- **Always balanced** - every entry must have debit = credit
- **Track source** - every entry must have a reference
- **Audit trail** - system tracks who entered and posted each entry

### Period Rules
- **One open at a time** - only one open accounting period per branch
- **No backdating** - entries must be in current open period
- **Closing** - once closed, no new entries can be added
- **Archive** - closed periods kept for historical records

### Account Rules
- **Don't delete** - mark inactive if no longer needed
- **Keep consistent** - use same account for same transaction type
- **Numbers matter** - account numbers determine statement placement
- **Reconcile regularly** - match GL to physical counts

## Common GL Account Numbers

| Number | Name | Used For |
|--------|------|----------|
| 1010 | Cash - Head Office | Cash payments |
| 1050 | Bank Account - Main | Bank deposits/withdrawals |
| 1220 | Inventory - Finished Goods | COGS entries |
| 2010 | Accounts Payable | Supplier invoices |
| 2020 | Sales Tax Payable | Tax collected |
| 4010 | Sales Revenue - Retail | Sale transactions |
| 5010 | Cost of Goods Sold | COGS for sales |

## Getting Help

### Check System Log
```bash
tail -f storage/logs/laravel.log
```
Look for GL posting errors or system issues.

### View GL Posting Status
In database, check these tables:
- `gl_entries` - All GL entries
- `sales` - Look for `gl_posting_status` field
- `purchases` - Look for `gl_posting_status` field

### Reset Cache If Issues
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

## Next Steps

After setting up accounting:
1. Record a test sale and verify GL posting
2. Create a test purchase and verify
3. Check Trial Balance
4. Practice bank reconciliation
5. Run month-end reports
6. Close first accounting period

---

**Ready to go!** Your accounting system is fully operational.
