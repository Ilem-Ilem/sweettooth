# Accounting System Implementation Guide

## Overview

This guide covers the implementation of a complete, functional accounting system integrated with Sales, Inventory, and Production modules.

---

## Installation & Setup

### Step 1: Run Migrations

First, create the accounting period migration and add the unit_cost fields:

```bash
# Run all pending migrations
php artisan migrate

# This will create:
# - gl_accounts table (if not exists)
# - gl_entries table (if not exists)
# - accounting_periods table (if not exists)
# - bank_accounts table (if not exists)
# - bank_transactions table (if not exists)
# - Add unit_cost to production_records
```

### Step 2: Seed Chart of Accounts

```bash
# Seed the complete chart of accounts (84 accounts)
php artisan accounting:seed-gl-accounts --force

# This creates all standard GL accounts organized by type:
# - Assets (1000-1999)
# - Liabilities (2000-2999)
# - Equity (3000-3999)
# - Revenue (4000-4999)
# - COGS (5000-5999)
# - Expenses (6000-6999)
# - Taxes (7000-7999)
```

### Step 3: Seed Accounting Periods

```bash
# Create accounting periods for 24+ months
php artisan db:seed --class=AccountingPeriodSeeder

# This creates:
# - Monthly periods starting from last year through next year
# - Current and future months set to 'open' status
# - Past months set to 'locked' status
```

---

## Services Overview

### 1. AccountingService

**Location:** `app/Services/AccountingService.php`

Handles all sales and payment accounting entries.

**Key Methods:**

```php
// Post a sale transaction (revenue + AR/Cash)
$accountingService->postSaleTransaction($sale);

// Post a payment transaction (Cash/Bank + AR reduction)
$accountingService->postPaymentTransaction($payment);

// Post COGS for sold inventory
$accountingService->postCogsSale($sale, $cogsAmount);

// Get current open period
$period = $accountingService->getCurrentPeriod();

// Get trial balance
$trialBalance = $accountingService->getTrialBalance($period);
```

**Usage Example:**

```php
use App\Services\AccountingService;

$accountingService = app(AccountingService::class);

// When a sale is created
$sale = Sale::find(1);
$accountingService->postSaleTransaction($sale);

// When a payment is received
$payment = Payment::find(1);
$accountingService->postPaymentTransaction($payment);
```

---

### 2. InventoryAccountingService

**Location:** `app/Services/InventoryAccountingService.php`

Handles inventory valuation, COGS calculation, and stock adjustments.

**Key Methods:**

```php
// Record inventory received
$inventoryService->recordInventoryReceived(
    $item,
    quantity: 100,
    unitCost: 50.00,
    supplier: 'Supplier Name'
);

// Record inventory writeoff
$inventoryService->recordInventoryWriteoff(
    $item,
    quantity: 10,
    unitCost: 50.00,
    reason: 'Expired/Damaged'
);

// Perform period-end inventory valuation
$valuations = $inventoryService->performPeriodEndValuation($period);

// Record internal inventory movements (returns, transfers)
$inventoryService->recordInventoryMovement($stockMovement);
```

**Inventory Accounts:**
- `1310` - Raw Materials
- `1320` - Work in Process
- `1330` - Finished Goods
- `1340` - Supplies

---

### 3. ProductionAccountingService

**Location:** `app/Services/ProductionAccountingService.php`

Handles production costing, WIP tracking, and overhead allocation.

**Key Methods:**

```php
// Record start of production (RM to WIP transfer)
$productionService->recordProductionStart($productionRecord);

// Record production completion (WIP to FG + Labor + Overhead)
$productionService->recordProductionCompletion($productionRecord);

// Record rejected/defective production
$productionService->recordProductionRejection($productionRecord);
```

**Production Cost Flow:**
1. Raw Materials consumed → WIP
2. Direct Labor allocated → WIP
3. Manufacturing Overhead allocated → WIP
4. Completed WIP → Finished Goods
5. FG sold → COGS

---

### 4. AccountingReportService

**Location:** `app/Services/AccountingReportService.php`

Generates all financial reports and statements.

**Key Methods:**

```php
use App\Services\AccountingReportService;

$reportService = app(AccountingReportService::class);

// Generate Balance Sheet
$balanceSheet = $reportService->generateBalanceSheet($period);

// Generate Income Statement
$incomeStatement = $reportService->generateIncomeStatement($period);

// Generate Trial Balance
$trialBalance = $reportService->generateTrialBalance($period);

// Generate General Ledger
$ledger = $reportService->generateGeneralLedger($account, $period);

// Generate Cash Flow Statement
$cashFlow = $reportService->generateCashFlowStatement($period);

// Generate Account Reconciliation
$reconciliation = $reportService->generateAccountReconciliation($account, $period);
```

---

### 5. JournalEntryValidator

**Location:** `app/Validators/JournalEntryValidator.php`

Validates GL entries before posting.

**Key Validations:**

```php
use App\Validators\JournalEntryValidator;

$validator = new JournalEntryValidator();

// Validate single entry
if (!$validator->validate($entry)) {
    $errors = $validator->getErrors(); // Array of error messages
}

// Validate journal batch
if (!$validator->validateBatch($entries)) {
    $errorMessage = $validator->getErrorMessage(); // Single string
}

// Check period
$validator->canPostToPeriod($period);

// Check account
$validator->canPostToAccount($account);
```

**Validations Performed:**
- Account exists and is active
- Account is not a header account
- Period is open (not closed or locked)
- Entry date is within period range
- Amounts are positive
- Description is provided

---

## Event Flow & Automation

### Automatic GL Posting

The system automatically posts GL entries when transactions occur:

#### Sales Transaction Flow

```
Sale Created Event
    ↓
PostSaleToGl Listener
    ↓
1. Post Revenue Entry (Debit AR/Cash, Credit Revenue)
2. Post COGS Entry (Debit COGS, Credit FG Inventory)
    ↓
Sale marked as gl_posted
```

#### Payment Transaction Flow

```
Payment Received Event
    ↓
PostPaymentToGl Listener
    ↓
Post Payment Entry (Debit Cash/Bank, Credit AR)
    ↓
Payment marked as gl_posted
```

#### Production Completion Flow

```
Production Completed Event
    ↓
PostProductionToGl Listener
    ↓
1. Post Labor Allocation (Debit WIP, Credit Labor Expense)
2. Post Overhead Allocation (Debit WIP, Credit Overhead)
3. Post WIP to FG Transfer (Debit FG, Credit WIP)
4. If Rejections: Post Writeoff (Debit Writeoff, Credit WIP)
    ↓
Production Cost tracked
```

---

## Database Schema

### GL Accounts Table (`gl_accounts`)

```sql
- id
- account_number (UNIQUE)
- account_name
- account_type (ENUM: asset, liability, equity, revenue, expense, cogs, tax, etc.)
- account_category
- description
- debit_balance (decimal:2)
- credit_balance (decimal:2)
- normal_balance (ENUM: debit, credit)
- is_header (boolean) - for sub-account grouping
- parent_account_id (FK)
- is_active (boolean)
- allow_manual_entry (boolean)
- timestamps
- soft_deletes
```

### GL Entries Table (`gl_entries`)

```sql
- id
- gl_account_id (FK)
- accounting_period_id (FK)
- entry_type (sale, payment, adjustment, manual, reversal, etc.)
- reference_type (polymorphic)
- reference_id (polymorphic)
- reference_number
- description
- debit (decimal:2)
- credit (decimal:2)
- entry_date
- status (ENUM: draft, posted, reversed)
- entered_by_id (polymorphic user)
- entered_by_type
- posted_by_id (polymorphic user)
- posted_at
- branch_id (FK)
- cost_center
- timestamps
- soft_deletes
```

### Accounting Periods Table (`accounting_periods`)

```sql
- id
- year
- month
- period_start (date)
- period_end (date)
- status (ENUM: open, closed, locked)
- closed_by_id (nullable, polymorphic)
- closed_by_type
- closed_at (nullable)
- closing_notes
- timestamps
- soft_deletes
```

### Production Records (Additional Fields)

```sql
- unit_cost (decimal:2) - Standard cost per unit produced
- total_production_cost (decimal:2) - Total batch production cost
```

---

## Chart of Accounts

### Account Numbering Structure

```
1XXX - Asset Accounts
  11XX - Current Assets
    1101 - Cash on Hand
    1110 - Cash in Bank - Main
    1120 - Cash in Bank - Petty Cash
  13XX - Inventory
    1310 - Raw Materials
    1320 - Work in Process
    1330 - Finished Goods
  15XX - Fixed Assets
    1510 - Equipment
    1520 - Accumulated Depreciation - Equipment

2XXX - Liability Accounts
  21XX - Current Liabilities
    2101 - Accounts Payable
    2110 - Sales Tax Payable
  22XX - Short-term Debt
    2201 - Short-term Loans

3XXX - Equity Accounts
  3101 - Capital Stock
  3110 - Retained Earnings
  3120 - Current Period Earnings

4XXX - Revenue Accounts
  4110 - Product Sales - Main
  4111 - Product Sales - Secondary
  4200 - Service Revenue
  4310 - Discount Received

5XXX - COGS Accounts
  5110 - COGS - Production
  5120 - COGS - Purchased
  5210 - Inventory Writeoff

6XXX - Expense Accounts
  61XX - Production Expenses
    6110 - Raw Materials Used
    6120 - Direct Labor
    6130 - Manufacturing Overhead
  62XX - Operating Expenses
    6210 - Salaries & Wages
    6220 - Rent/Lease
    6230 - Utilities
    6240 - Maintenance & Repairs
    6250 - Transportation & Delivery
  63XX - Administrative Expenses
    6310 - Depreciation
    6320 - Office Supplies
  64XX - Sales & Marketing
    6410 - Advertising

7XXX - Tax Accounts
  7110 - Income Tax Expense
  7120 - Sales Tax Expense
```

---

## Reports & Dashboards

### Available Reports

1. **Balance Sheet** - Assets, Liabilities, Equity as of period end
2. **Income Statement** - Revenue, COGS, Expenses for the period
3. **Trial Balance** - List of all GL accounts with debit/credit totals
4. **General Ledger** - Detailed transaction history by account
5. **Cash Flow Statement** - Operating, Investing, Financing activities
6. **Account Reconciliation** - Variance analysis for specific accounts

### Livewire Components

#### AccountingDashboard (`app/Livewire/Accounting/AccountingDashboard.php`)

- Period selection and report generation
- Summary metrics (total entries, debits, credits)
- Multi-report support
- Real-time updates

#### GlAccountManager (`app/Livewire/Accounting/GlAccountManager.php`)

- GL account list with search and filters
- Account activation/deactivation
- Sorting and pagination
- Account type filtering

---

## Integration Points

### With Sales Module

```php
// When Sale is created:
// 1. Event 'SaleCreated' is dispatched
// 2. PostSaleToGl listener processes it
// 3. GL entries are automatically posted

// When Payment is received:
// 1. Event 'PaymentReceived' is dispatched
// 2. PostPaymentToGl listener processes it
// 3. Cash/AR GL entries are posted
```

### With Inventory Module

```php
// When Stock is received:
// 1. InventoryAccountingService.recordInventoryReceived()
// 2. GL entries: Debit Inventory, Credit AP

// When Stock is moved/adjusted:
// 1. InventoryAccountingService.recordInventoryMovement()
// 2. GL entries based on movement type

// When Stock is written off:
// 1. InventoryAccountingService.recordInventoryWriteoff()
// 2. GL entries: Debit Writeoff, Credit Inventory
```

### With Production Module

```php
// When Production starts:
// 1. ProductionAccountingService.recordProductionStart()
// 2. GL entries: Debit WIP, Credit Raw Materials

// When Production completes:
// 1. Event 'ProductionCompleted' is dispatched
// 2. PostProductionToGl listener processes it
// 3. GL entries for labor, overhead, and WIP to FG transfer

// When Production is rejected:
// 1. ProductionAccountingService.recordProductionRejection()
// 2. GL entries: Debit Writeoff, Credit WIP
```

---

## Example Usage

### Creating a Sale and Automatic GL Posting

```php
use App\Models\Sale;
use App\Events\SaleCreated;

// Create a sale
$sale = Sale::create([
    'sales_shift_id' => 1,
    'branch_id' => 1,
    'sale_number' => 'SALE-2024-001',
    'sale_time' => now(),
    'total' => 500.00,
    'status' => 'pending',
]);

// Add sale items
$sale->saleItems()->create([
    'product_id' => 1,
    'quantity' => 10,
    'unit_price' => 50.00,
    'subtotal' => 500.00,
]);

// Save and calculate totals
$sale->calculateTotals();
$sale->save();

// Dispatch event - this triggers GL posting automatically
SaleCreated::dispatch($sale);

// Or if using an observer:
// The observer will automatically dispatch the event
```

### Manual GL Entry

```php
use App\Models\GlEntry;
use App\Models\GlAccount;
use App\Models\AccountingPeriod;
use App\Validators\JournalEntryValidator;

$period = AccountingPeriod::where('status', 'open')->first();

$entry = GlEntry::create([
    'gl_account_id' => GlAccount::where('account_number', '1110')->first()->id,
    'accounting_period_id' => $period->id,
    'entry_type' => 'manual',
    'description' => 'Manual bank deposit',
    'debit' => 1000.00,
    'credit' => 0,
    'entry_date' => now(),
    'status' => 'draft',
    'entered_by_id' => auth()->id(),
    'entered_by_type' => get_class(auth()->user()),
]);

// Validate before posting
$validator = new JournalEntryValidator();
if ($validator->validate($entry)) {
    $entry->post(auth()->id());
} else {
    Log::error($validator->getErrorMessage());
}
```

### Generating Reports

```php
use App\Services\AccountingReportService;
use App\Models\AccountingPeriod;

$reportService = app(AccountingReportService::class);
$period = AccountingPeriod::where('status', 'open')->first();

// Get Balance Sheet
$balanceSheet = $reportService->generateBalanceSheet($period);
echo "Total Assets: " . $balanceSheet['total_assets'];
echo "Total Liabilities & Equity: " . $balanceSheet['total_liabilities_equity'];

// Get Income Statement
$incomeStatement = $reportService->generateIncomeStatement($period);
echo "Net Income: " . $incomeStatement['net_income'];

// Get Trial Balance
$trialBalance = $reportService->generateTrialBalance($period);
if ($trialBalance['is_balanced']) {
    echo "Trial balance is in balance!";
}
```

---

## Troubleshooting

### GL Entry Not Posting

1. Check if accounting period is open:
```php
$period = AccountingPeriod::find($entry->accounting_period_id);
if ($period->status !== 'open') {
    // Cannot post to closed/locked period
}
```

2. Verify GL account is active and not a header:
```php
$account = $entry->glAccount;
if (!$account->is_active || $account->is_header) {
    // Account cannot receive entries
}
```

3. Check validation errors:
```php
$validator = new JournalEntryValidator();
if (!$validator->validate($entry)) {
    foreach ($validator->getErrors() as $error) {
        Log::error($error);
    }
}
```

### Balances Not Matching

1. Run trial balance to check if GL is in balance:
```php
$trialBalance = $reportService->generateTrialBalance($period);
if (!$trialBalance['is_balanced']) {
    // Find and fix unbalanced entries
}
```

2. Check for pending (draft) entries:
```php
$draftEntries = GlEntry::where('status', 'draft')->get();
// Post or delete drafts
```

---

## Performance Considerations

1. **Index on GL Entries**: Queries are indexed on:
   - `(gl_account_id, entry_date)`
   - `(accounting_period_id, status)`
   - `(reference_type, reference_id)`
   - `entry_date`

2. **Batch Posting**: For large imports, use batch transactions:
```php
DB::transaction(function () {
    foreach ($entries as $entry) {
        $entry->post(auth()->id());
    }
});
```

3. **Cache Reports**: Cache financial reports during period:
```php
Cache::remember(
    "accounting.balance_sheet.{$period->id}",
    3600, // 1 hour
    fn() => $reportService->generateBalanceSheet($period)
);
```

---

## Security Considerations

1. **Posted Entries are Immutable**: Once posted, GL entries cannot be edited
2. **Reversals Only**: To correct a posted entry, create a reversal entry
3. **Period Locking**: Past periods are automatically locked to prevent tampering
4. **Audit Trail**: All GL entries track who entered and who posted them
5. **Manual Entry Permission**: Restrict manual GL entries to authorized users

---

## Next Steps

1. ✅ Run migrations and seeders
2. ✅ Test GL posting with sample sales
3. ✅ Verify period-end reports
4. ✅ Set up event listeners
5. ✅ Create accounting users and permissions
6. ✅ Train team on system usage
7. Integrate with existing dashboards
8. Set up automated backups
9. Create accounting reconciliation procedures
