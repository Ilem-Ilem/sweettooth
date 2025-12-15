# Accounting System How-To Guide

## Getting Started

### Accessing the Accounting Module
1. Login as a user with accounting permissions
2. Navigate to `/branch-dashboard/accounting/dashboard`
3. Or use the sidebar menu: Accounting > Dashboard

### Required Permissions
| Permission | Description |
|------------|-------------|
| `access_accounting` | Access accounting dashboard |
| `view_financial_reports` | View all financial reports |
| `manage_accounts` | Add/edit GL accounts |
| `manage_periods` | Open/close accounting periods |
| `create_journal_entries` | Create manual journal entries |

---

## How to Set Up Accounting Periods

### Creating a New Period
1. Go to Accounting > Period Management (`/branch-dashboard/accounting/periods`)
2. Click "Create New Period"
3. Select Year and Month
4. System auto-calculates start/end dates
5. Set Status to "Open"
6. Save

### Closing a Period
1. Ensure all transactions are recorded
2. Generate Trial Balance to verify it balances
3. Go to Period Management
4. Select the period
5. Click "Close Period"
6. Add closing notes if needed

**Note:** Closed periods cannot accept new GL entries.

---

## How to View Financial Reports

### General Ledger
**Route:** `/branch-dashboard/accounting/reports/general-ledger`

Shows all transactions for a specific GL account:
1. Select GL Account from dropdown
2. Set date range
3. View all debits/credits with running balance

### Trial Balance
**Route:** `/branch-dashboard/accounting/reports/trial-balance`

Shows all accounts with their debit/credit balances:
1. Select accounting period
2. View summary of all accounts
3. Total Debits MUST equal Total Credits

### Income Statement (P&L)
**Route:** `/branch-dashboard/accounting/reports/income-statement`

Shows Revenue, COGS, Expenses, and Net Income:
1. Select date range
2. Optionally filter by branch
3. View:
   - Revenue (4000-4900 accounts)
   - Cost of Goods Sold (5000-5900 accounts)
   - Gross Profit
   - Operating Expenses (6000-7900 accounts)
   - Net Income

### Balance Sheet
**Route:** `/branch-dashboard/accounting/reports/balance-sheet`

Shows Assets, Liabilities, and Equity:
1. Select "As of" date
2. View:
   - Assets (1000-1900 accounts)
   - Liabilities (2000-2900 accounts)
   - Equity (3000-3900 accounts)
3. Verify: Assets = Liabilities + Equity

---

## How to Create Manual Journal Entries

### When to Use Manual Entries
- Adjusting entries (accruals, deferrals)
- Correcting errors
- Recording non-automated transactions
- Year-end closing entries

### Creating an Entry
1. Go to Accounting > Journal Entry (`/branch-dashboard/accounting/journal-entry`)
2. Set Entry Date
3. Add Description
4. Add Line Items:
   - Select Account
   - Enter Debit OR Credit amount (not both)
5. **Entry must balance** - Total Debits = Total Credits
6. Save as Draft or Post immediately

### Entry Workflow
```
Draft → Posted → (can be Reversed)
```

- **Draft:** Can be edited/deleted
- **Posted:** Updates GL balances, cannot be deleted
- **Reversed:** Creates offsetting entry

---

## How Automatic GL Posting Works

### Sale Transaction
When a sale is completed and fully paid:

```
Entry 1: Revenue Recognition
  Debit:  Cash/Bank (1010/1050)     [Amount: Sale Total]
  Credit: Sales Revenue (4010)       [Amount: Subtotal]
  Credit: Sales Tax Payable (2020)   [Amount: Tax]

Entry 2: Cost of Goods Sold
  Debit:  COGS (5010)               [Amount: Item Cost × Qty]
  Credit: Inventory (1220)           [Amount: Item Cost × Qty]
```

### Purchase Transaction
When a purchase is approved:

```
Entry: Inventory Purchase
  Debit:  Inventory (1220)          [Amount: Landing Cost]
  Credit: Accounts Payable (2010)    [Amount: Landing Cost]
```

### Payment Transaction
When payment is made:

```
Entry: Payment Made
  Debit:  Accounts Payable (2010)   [Amount: Payment]
  Credit: Cash/Bank (1010/1050)      [Amount: Payment]
```

### Inventory Adjustment
When stock is adjusted (damage, shrinkage):

```
Entry: Inventory Loss
  Debit:  Inventory Loss (5020)     [Amount: Cost × Qty]
  Credit: Inventory (1220)           [Amount: Cost × Qty]
```

---

## How to Manage Chart of Accounts

### Viewing Accounts
1. Go to Accounting > Accounts (`/branch-dashboard/accounting/accounts`)
2. Filter by Account Type (Asset, Liability, etc.)
3. Search by account number or name

### Adding a New Account
1. Click "Add Account"
2. Enter:
   - Account Number (unique, e.g., "1015")
   - Account Name (e.g., "Petty Cash - Branch A")
   - Account Type (Asset, Liability, Equity, Revenue, COGS, Expense)
   - Account Category (more specific classification)
   - Normal Balance (Debit or Credit)
3. Save

### Account Numbering Convention
```
1000-1999: Assets
  1000-1099: Cash & Bank
  1100-1199: Receivables
  1200-1299: Inventory
  1300-1499: Fixed Assets

2000-2999: Liabilities
  2000-2099: Payables
  2100-2199: Short-term Loans
  2200-2299: Long-term Loans

3000-3999: Equity
  3000-3099: Capital/Retained Earnings

4000-4999: Revenue
  4000-4099: Sales Revenue

5000-5999: Cost of Goods Sold

6000-7999: Operating Expenses

8000-8999: Finance Costs

9000-9999: Taxes
```

---

## How to Troubleshoot GL Issues

### Sale Not Posting to GL
1. Check sale status is "completed"
2. Check `isFullyPaid()` returns true
3. Check `gl_posting_status` field on sale
4. If "failed", check `gl_posting_error` for reason
5. Common issues:
   - No open accounting period
   - Missing GL account

### Finding Posting Errors
```sql
-- Find failed postings
SELECT id, reference_number, gl_posting_status, gl_posting_error
FROM sales
WHERE gl_posting_status = 'failed';
```

### Manually Reposting a Transaction
If a sale failed to post:
1. Fix the underlying issue (create period, add account)
2. Update the sale record:
```sql
UPDATE sales SET gl_posting_status = 'pending' WHERE id = X;
```
3. The observer will attempt reposting on next update

### Checking Trial Balance
If trial balance doesn't balance:
1. Look for recent entries
2. Check for one-sided entries (debit without credit)
3. Create correcting journal entry

---

## Daily Accounting Workflow

### Morning
1. Verify previous day's sales posted to GL
2. Check for any failed postings
3. Review bank position opening balances

### During Day
- All sales/purchases auto-post via observers
- Manual entries for non-automated items

### End of Day
1. Review day's GL entries
2. Check cash position
3. Record any bank transactions

### Month End
1. Complete all period transactions
2. Run Trial Balance
3. Generate Income Statement
4. Generate Balance Sheet
5. Close accounting period
6. Open next period

---

## Key URLs Reference

| Page | URL |
|------|-----|
| Accounting Dashboard | `/branch-dashboard/accounting/dashboard` |
| Chart of Accounts | `/branch-dashboard/accounting/accounts` |
| Period Management | `/branch-dashboard/accounting/periods` |
| Manual Journal Entry | `/branch-dashboard/accounting/journal-entry` |
| General Ledger | `/branch-dashboard/accounting/reports/general-ledger` |
| Trial Balance | `/branch-dashboard/accounting/reports/trial-balance` |
| Income Statement | `/branch-dashboard/accounting/reports/income-statement` |
| Balance Sheet | `/branch-dashboard/accounting/reports/balance-sheet` |

---

## Common Commands

```bash
# Run accounting migrations
php artisan migrate

# Seed chart of accounts
php artisan db:seed --class=ChartOfAccountsSeeder

# Seed accounting permissions
php artisan db:seed --class=AccountingAccessControlSeeder

# Clear cache after changes
php artisan cache:clear
php artisan config:clear

# Check logs for GL errors
tail -f storage/logs/laravel.log | grep -i "gl\|posting\|accounting"
```
