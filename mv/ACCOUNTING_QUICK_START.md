# Accounting System - Quick Start Guide

## What's Ready

✅ **Dashboard** - View financial summaries and recent entries  
✅ **Chart of Accounts** - 62 GL accounts fully configured  
✅ **Journal Entries** - Create and manage manual journal entries  
✅ **Accounting Periods** - Manage accounting periods (36 months available)  
✅ **Reports** - Trial Balance, Balance Sheet, Income Statement, General Ledger  
✅ **Bank Reconciliation** - Reconcile bank accounts  

---

## Access Points

| Feature | URL | Permission |
|---------|-----|-----------|
| Dashboard | `/branch-dashboard/accounting/dashboard` | `access_accounting` |
| GL Accounts | `/branch-dashboard/accounting/accounts` | `manage_accounts` |
| Journal Entries | `/branch-dashboard/accounting/journal-entry` | `create_journal_entries` |
| Periods | `/branch-dashboard/accounting/periods` | `manage_periods` |
| Bank Reconciliation | `/branch-dashboard/accounting/bank-reconciliation` | `reconcile_bank_accounts` |
| Trial Balance | `/branch-dashboard/accounting/reports/trial-balance` | `view_financial_reports` |
| Balance Sheet | `/branch-dashboard/accounting/reports/balance-sheet` | `view_financial_reports` |
| Income Statement | `/branch-dashboard/accounting/reports/income-statement` | `view_financial_reports` |

---

## Create Your First Journal Entry

### Step 1: Navigate
Go to `/branch-dashboard/accounting/journal-entry`

### Step 2: Fill In Details
- **Reference:** Unique identifier (e.g., `JE-2025-001`)
- **Period:** Select "December 2025" (currently open)
- **Date:** Today's date (auto-filled)
- **Description:** What is this entry for?

### Step 3: Add Line Items
Click **+ Add Line** to add more rows:

| Account | Debit | Credit | Notes |
|---------|-------|--------|-------|
| 1101 - Cash on Hand | 1000.00 | | Payment received |
| 4110 - Product Sales | | 1000.00 | Sales revenue |

### Step 4: Verify Balance
- **Totals should show:**
  - Debits: 1000.00
  - Credits: 1000.00
  - Status: ✓ Balanced

### Step 5: Submit
Click **Create & Post** to post immediately, or **Create as Draft** to review later

---

## View Your Dashboard

### What You'll See
- **Period Info:** Current accounting period (Dec 2025 - Open)
- **Quick Stats:** 
  - Total Entries (posted)
  - Total Debits & Credits
  - Balance Status (Balanced/Not Balanced)
- **Recent Entries:** Last 10 GL entries posted
- **Quick Links:** Accounts, Periods, Journal Entries, Reports

### Period Navigation
Select a different period from the dropdown to view that month's data

---

## Manage GL Accounts

### View All Accounts
Go to `/branch-dashboard/accounting/accounts`

### Search
- By code (e.g., "1101")
- By name (e.g., "Cash")

### Filter
- **Type:** Asset, Liability, Equity, Revenue, Expense, etc.
- **Status:** Active, Inactive

### Sort
Click column headers to sort:
- Code
- Name
- Type
- Balance

### Activate/Deactivate
Click **Activate** or **Deactivate** button to toggle account status

---

## Manage Accounting Periods

### View All Periods
Go to `/branch-dashboard/accounting/periods`

### Create New Period
1. Click **+ New Period**
2. Select Year (2025, 2026, etc.)
3. Select Month (Jan-Dec)
4. Choose Status (Open or Closed)
5. Click **Create Period**

### Period Actions
- **Select:** Make it the current period for data views
- **Close:** Finish the month (allows reopening)
- **Reopen:** Reopen a closed month
- **Lock:** Permanently lock to prevent changes

---

## Chart of Accounts Reference

### Asset Accounts (1000-1999)
- 1101: Cash on Hand
- 1110: Cash in Bank - Main
- 1200: Accounts Receivable
- 1310: Raw Materials Inventory
- 1320: Work in Process Inventory
- 1330: Finished Goods Inventory
- 1510: Equipment
- 1530: Furniture & Fixtures

### Liability Accounts (2000-2999)
- 2101: Accounts Payable
- 2102: Accrued Expenses
- 2110: Sales Tax Payable
- 2120: VAT Payable
- 2201: Short-term Loans

### Equity Accounts (3000-3999)
- 3101: Capital Stock
- 3110: Retained Earnings
- 3120: Current Period Earnings

### Revenue Accounts (4000-4999)
- 4110: Product Sales - Main
- 4111: Product Sales - Secondary
- 4200: Service Revenue
- 4310: Discount Received
- 4311: Interest Income

### Expense Accounts (6000-6999)
- 6110: Raw Materials Used
- 6120: Direct Labor
- 6210: Salaries & Wages
- 6220: Rent/Lease Expense
- 6230: Utilities Expense
- 6240: Maintenance & Repairs
- 6310: Depreciation Expense
- 6330: Insurance Expense
- 6410: Advertising Expense

---

## Tips & Tricks

### ✓ Always Balance Entries
- Debits must equal Credits
- System prevents unbalanced entries
- Real-time validation shows balance status

### ✓ Use Descriptive References
- Helps with tracking and searching
- Example: `JE-2025-001`, `PAYROLL-DEC`, `ADJ-CLOSING`

### ✓ Review Before Posting
- Create as Draft first if unsure
- Review and delete if needed
- Post when confident

### ✓ Lock Periods Regularly
- Lock each month after closing
- Prevents accidental changes
- Maintains audit trail integrity

### ✓ Monitor Trial Balance
- Should always be 0 (balanced)
- If unbalanced, find and correct errors
- Check Dashboard for balance status

---

## Common Journal Entry Examples

### Sales Transaction
```
Dr. Cash in Bank (1110)     1000.00
  Cr. Product Sales (4110)           1000.00
```

### Expense Payment
```
Dr. Utilities Expense (6230)   500.00
  Cr. Cash in Bank (1110)             500.00
```

### Inventory Purchase
```
Dr. Raw Materials (1310)    2000.00
  Cr. Accounts Payable (2101)       2000.00
```

### Payroll Entry
```
Dr. Salaries & Wages (6210)  5000.00
  Cr. Cash in Bank (1110)           5000.00
```

### Period Closing Adjustment
```
Dr. Depreciation Expense (6310)  500.00
  Cr. Accumulated Depreciation (1520)  500.00
```

---

## Keyboard Shortcuts

| Action | Shortcut |
|--------|----------|
| Search Box | `Ctrl+F` |
| Submit Form | `Ctrl+Enter` |
| Add Line | `Alt+A` |
| Save Draft | `Ctrl+S` |

---

## Troubleshooting

### "Period must be open"
- Select an "Open" period from the dropdown
- Current open period: December 2025

### "Entries must be balanced"
- Check that Debits = Credits
- Red indicator shows the difference
- Adjust amounts until balanced

### "Account not found"
- Account might be inactive
- Use dropdown to select from active accounts only
- Or activate the account in GL Accounts

### "Reference already exists"
- Use a unique reference number
- Include year: `JE-2025-XXX`
- Or include initials: `JE-ABC-001`

---

## Data Already Loaded

✅ **62 GL Accounts** seeded and ready  
✅ **36 Accounting Periods** created (2024-2026)  
✅ **Current Period:** December 2025 (OPEN)  
✅ **Test Data:** Sample entries can be created  

---

## Need Help?

1. Check the Chart of Accounts Reference above
2. Use the Dashboard to verify entries posted correctly
3. Review the IMPLEMENTATION_COMPLETE_UI_SETUP.md for detailed documentation
4. Contact your system administrator for permission issues

---

**Status:** ✅ Ready to Use

**Last Updated:** December 15, 2025
