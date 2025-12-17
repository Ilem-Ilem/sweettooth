# Accounting System Design - Complete, Functional & Integrated

## System Overview

A complete double-entry accounting system integrated with Sales, Inventory, and Production modules. Maintains consistent COA (Chart of Accounts), automatic journal entry posting, and real-time financial reporting.

---

## 1. Chart of Accounts (COA) Structure

### Asset Accounts (1000-1999)
- **1100 Current Assets**
  - 1101: Cash on Hand
  - 1110: Cash in Bank - Main Account
  - 1120: Cash in Bank - Petty Cash
  - 1200: Accounts Receivable
  - 1210: Allowance for Bad Debts

- **1300 Inventory Assets**
  - 1310: Raw Materials
  - 1320: Work in Process
  - 1330: Finished Goods
  - 1340: Supplies

- **1500 Fixed Assets**
  - 1510: Equipment
  - 1520: Accumulated Depreciation - Equipment
  - 1530: Furniture & Fixtures
  - 1540: Accumulated Depreciation - Furniture

### Liability Accounts (2000-2999)
- **2100 Current Liabilities**
  - 2101: Accounts Payable
  - 2102: Accrued Expenses
  - 2110: Sales Tax Payable
  - 2120: VAT Payable

- **2200 Short-term Debt**
  - 2201: Short-term Loans
  - 2210: Current Portion of Long-term Debt

### Equity Accounts (3000-3999)
- **3100 Owner's Equity**
  - 3101: Capital Stock
  - 3110: Retained Earnings
  - 3120: Current Period Earnings

### Revenue Accounts (4000-4999)
- **4100 Product Sales**
  - 4110: Product Sales - Main
  - 4111: Product Sales - Secondary

- **4200 Service Revenue**
  - 4210: Service Revenue

- **4300 Other Income**
  - 4310: Discount Received
  - 4311: Interest Income

### Cost of Goods Sold (5000-5999)
- **5100 Cost of Goods Sold**
  - 5110: COGS - Production
  - 5120: COGS - Purchased

- **5200 Inventory Adjustments**
  - 5210: Inventory Writeoff
  - 5211: Obsolescence Adjustment

### Expense Accounts (6000-6999)
- **6100 Production Expenses**
  - 6110: Raw Materials Used
  - 6120: Direct Labor
  - 6130: Manufacturing Overhead

- **6200 Operating Expenses**
  - 6210: Salaries & Wages
  - 6220: Rent/Lease
  - 6230: Utilities
  - 6240: Maintenance & Repairs
  - 6250: Transportation & Delivery

- **6300 Administrative Expenses**
  - 6310: Depreciation
  - 6320: Office Supplies
  - 6330: Insurance
  - 6340: Professional Fees

- **6400 Sales & Marketing**
  - 6410: Advertising
  - 6420: Sales Commissions

### Tax Accounts (7000-7999)
- **7100 Taxes**
  - 7110: Income Tax Expense
  - 7120: Sales Tax Expense

---

## 2. Transaction Flow & Automation

### Sales Transaction Flow
```
Sale Created → Post Revenue Entry
           → Post AR/Payment Entry (if terms)
           → Post Tax Entry (if applicable)

Payment Received → Post Cash/AR Entry
               → Link to Bank Account
```

### Inventory Transaction Flow
```
Stock Movement → Update Inventory GL Account
             → Calculate COGS impact

Production Completion → Record WIP → FG transfer
                    → Calculate unit costs
                    → Allocate overhead
```

### Production Transaction Flow
```
Production Record Created → Allocate Raw Materials Cost
                        → Allocate Direct Labor
                        → Allocate Overhead

Product Moved to FG → Transfer from WIP to FG
                  → Calculate production cost
```

---

## 3. Key Services & Components

### AccountingService
- Post revenue entries (sales → accounts receivable/cash)
- Post payment entries (cash in/out)
- Generate closing entries
- Period management

### InventoryAccountingService
- Track COGS using FIFO/Weighted Average
- Manage inventory valuation
- Post stock movement entries
- Calculate adjustment entries

### ProductionAccountingService
- Allocate raw material costs
- Allocate direct labor
- Allocate manufacturing overhead
- Calculate unit costs
- Track WIP and FG

### JournalEntryValidator
- Validate debit = credit
- Check account validity
- Prevent posting to closed periods
- Ensure required fields

---

## 4. Integration Points

### Sales ↔ Accounting
- Sale created → Revenue GL entry
- Payment received → AR/Cash GL entry
- Refund issued → Reverse entries

### Inventory ↔ Accounting
- Stock in → Asset GL entry
- Stock out (sale) → COGS GL entry
- Adjustment → Variance GL entry

### Production ↔ Accounting
- Production started → WIP GL entry
- Production completed → FG GL entry
- Units sold → COGS GL entry (from FG)

---

## 5. Database Changes Required

### Additional GL Account Fields
- GL Account Group (for reporting)
- Requires Reconciliation (T/F)
- GL Account Subtype

### GL Entry Enhancements
- Multi-branch support
- Department/Cost Center allocation
- Intercompany reconciliation flag

### New Tables
- `gl_account_balances` - Period-end balances (cached)
- `journal_batches` - Group related entries
- `accounting_reconciliations` - Track reconciliation status

---

## 6. Reports Generated

1. **Balance Sheet** - Assets, Liabilities, Equity
2. **Income Statement** - Revenue, COGS, Expenses
3. **GL Report** - Detailed account transactions
4. **Trial Balance** - Debit/Credit totals
5. **Cash Flow Statement** - Operating, Investing, Financing
6. **Inventory Valuation** - Current stock at standard/actual cost
7. **Production Cost Report** - Unit costs by product
8. **Account Reconciliation** - Variance analysis

---

## 7. Implementation Phases

### Phase 1: Foundation (Current)
- ✅ COA Structure Design
- ✅ Database schema review
- Create GL Account Seeder
- Create base Service classes
- Add model relationships

### Phase 2: Automation
- Event listeners for sales
- Event listeners for inventory
- Event listeners for production
- Automatic entry posting

### Phase 3: Validation & Tools
- Journal Entry validation
- Period closing logic
- Reconciliation tools
- Error handling & logging

### Phase 4: Reporting
- GL Reports
- Financial statements
- Cost analysis reports
- Dashboard views

---

## 8. Key Business Rules

1. **All monetary transactions must have GL entries**
2. **Every GL entry must balance (debit = credit)**
3. **GL entries cannot be posted to closed periods**
4. **COGS must be calculated on product sale**
5. **Inventory must be valued at period-end**
6. **Production costs must be allocated to products**
7. **Cash entries must link to bank accounts**
8. **Monthly period close locks previous month**
