# Accounting System - Quick Reference Card

## Setup (Run Once)

```bash
# Option 1: Automated (Recommended)
chmod +x ACCOUNTING_QUICK_START.sh
./ACCOUNTING_QUICK_START.sh

# Option 2: Manual
php artisan migrate
php artisan accounting:seed-gl-accounts --force
php artisan db:seed --class=AccountingPeriodSeeder
```

---

## Service Usage

### AccountingService
```php
$service = app(App\Services\AccountingService::class);

// Post a sale
$service->postSaleTransaction($sale);

// Post a payment
$service->postPaymentTransaction($payment);

// Post COGS
$service->postCogsSale($sale, $cogsAmount);

// Get trial balance
$trialBalance = $service->getTrialBalance($period);
```

### InventoryAccountingService
```php
$service = app(App\Services\InventoryAccountingService::class);

// Record received inventory
$service->recordInventoryReceived($item, $qty, $cost, $supplier);

// Record writeoff
$service->recordInventoryWriteoff($item, $qty, $cost, $reason);

// Period-end valuation
$service->performPeriodEndValuation($period);
```

### ProductionAccountingService
```php
$service = app(App\Services\ProductionAccountingService::class);

// Record production start
$service->recordProductionStart($production);

// Record completion
$service->recordProductionCompletion($production);

// Record rejection
$service->recordProductionRejection($production);
```

### AccountingReportService
```php
$service = app(App\Services\AccountingReportService::class);

// Generate reports
$bs = $service->generateBalanceSheet($period);
$is = $service->generateIncomeStatement($period);
$tb = $service->generateTrialBalance($period);
$gl = $service->generateGeneralLedger($account, $period);
$cf = $service->generateCashFlowStatement($period);
$rec = $service->generateAccountReconciliation($account, $period);
```

### JournalEntryValidator
```php
$validator = new App\Validators\JournalEntryValidator();

// Validate single entry
if ($validator->validate($entry)) {
    $entry->post(auth()->id());
}

// Validate batch
if ($validator->validateBatch($entries)) {
    // Post all entries
}

// Get errors
$errors = $validator->getErrors();
```

---

## Common GL Accounts

| Purpose | Account | Number |
|---------|---------|--------|
| Cash Sales | Cash in Bank | 1110 |
| Petty Cash | Cash - Petty | 1120 |
| Credit Sales | Accounts Receivable | 1200 |
| Raw Materials | RM Inventory | 1310 |
| Work in Process | WIP Inventory | 1320 |
| Finished Goods | FG Inventory | 1330 |
| Accounts Payable | AP | 2101 |
| Revenue | Product Sales | 4110 |
| COGS | COGS - Production | 5110 |
| Direct Labor | Direct Labor | 6120 |
| Manufacturing | Mfg Overhead | 6130 |
| Salaries | Salaries & Wages | 6210 |
| Rent | Rent/Lease | 6220 |

---

## Key Models

### GlAccount
```php
$account = GlAccount::where('account_number', '1110')->first();

// Properties
$account->account_number;    // "1110"
$account->account_name;      // "Cash in Bank - Main"
$account->account_type;      // "asset"
$account->is_active;         // true
$account->normal_balance;    // "debit"
$account->debit_balance;     // Current debit balance
$account->credit_balance;    // Current credit balance

// Methods
$account->getBalance();              // Net balance
$account->updateBalance($db, $cr);   // Update balance
```

### GlEntry
```php
$entry = GlEntry::find(1);

// Properties
$entry->gl_account_id;       // Account ID
$entry->debit;               // Debit amount
$entry->credit;              // Credit amount
$entry->entry_date;          // Entry date
$entry->status;              // 'draft', 'posted', 'reversed'
$entry->description;         // Entry description

// Methods
$entry->post(auth()->id());  // Post the entry
$entry->reverse(auth()->id());  // Create reversing entry
```

### AccountingPeriod
```php
$period = AccountingPeriod::current()->first();

// Properties
$period->year;               // 2024
$period->month;              // 12
$period->period_start;       // Start date
$period->period_end;         // End date
$period->status;             // 'open', 'closed', 'locked'

// Methods
$period->close($userId, $notes);  // Close period
$period->lock();              // Lock period
$period->reopen();            // Reopen period
$period->getDisplayName();    // "December 2024"
```

---

## GL Entry Creation

### Manual Entry
```php
$entry = GlEntry::create([
    'gl_account_id' => 1,
    'accounting_period_id' => 1,
    'entry_type' => 'manual',
    'description' => 'Manual adjustment',
    'debit' => 100.00,
    'credit' => 0,
    'entry_date' => now(),
    'status' => 'draft',
]);

$entry->post(auth()->id());
```

### Automatic Entry (via Service)
```php
// Service creates and posts automatically
$service = app(AccountingService::class);
$service->postSaleTransaction($sale);
```

---

## Validation

### Before Posting
```php
$validator = new JournalEntryValidator();

if (!$validator->validate($entry)) {
    foreach ($validator->getErrors() as $error) {
        Log::error($error);
    }
    return false;
}

$entry->post(auth()->id());
```

### Batch Validation
```php
$validator = new JournalEntryValidator();

if ($validator->validateBatch($entries)) {
    // All entries are valid
} else {
    echo $validator->getErrorMessage();
}
```

---

## Reports

### Balance Sheet
```php
$report = $service->generateBalanceSheet($period);
// Returns: total_assets, total_liabilities_equity, breakdown by type
```

### Income Statement
```php
$report = $service->generateIncomeStatement($period);
// Returns: revenues, cogs, expenses, net_income
```

### Trial Balance
```php
$report = $service->generateTrialBalance($period);
// Returns: all accounts with debits/credits, verification of balance
```

---

## Event Dispatching

### Sales Event
```php
use App\Events\SaleCreated;

$sale = Sale::create([...]);
SaleCreated::dispatch($sale);
// Automatically posts revenue and COGS entries
```

### Payment Event
```php
use App\Events\PaymentReceived;

$payment = Payment::create([...]);
PaymentReceived::dispatch($payment);
// Automatically posts cash/AR entries
```

### Production Event
```php
use App\Events\ProductionCompleted;

$production = ProductionRecord::find(1);
ProductionCompleted::dispatch($production);
// Automatically posts labor, overhead, and FG transfer
```

---

## Livewire Components

### Accounting Dashboard
```blade
<livewire:accounting.accounting-dashboard />
```
Features: Period selection, report generation, metrics display

### GL Account Manager
```blade
<livewire:accounting.gl-account-manager />
```
Features: Account browser, search, filtering, activation toggle

---

## Troubleshooting

### Entry Not Posting?
```php
// Check 1: Period status
$period = $entry->period;
if ($period->status !== 'open') {
    // Cannot post - period is closed/locked
}

// Check 2: Account validity
$account = $entry->glAccount;
if (!$account->is_active || $account->is_header) {
    // Cannot post - account invalid
}

// Check 3: Validation
$validator = new JournalEntryValidator();
$errors = $validator->validate($entry);

// Check 4: Logs
tail -f storage/logs/laravel.log
```

### Trial Balance Not Balancing?
```php
// Get trial balance
$tb = $service->generateTrialBalance($period);

if (!$tb['is_balanced']) {
    echo "Total Debits: " . $tb['total_debits'];
    echo "Total Credits: " . $tb['total_credits'];
    // Find and fix unbalanced entries
}
```

### Missing GL Accounts?
```bash
# Check account count
php artisan tinker
  App\Models\GlAccount::count(); // Should be 84

# Reseed if needed
php artisan accounting:seed-gl-accounts --force
```

---

## Database Queries

### Get All Entries for Account in Period
```php
$entries = GlEntry::where('gl_account_id', $accountId)
    ->where('accounting_period_id', $periodId)
    ->where('status', 'posted')
    ->orderBy('entry_date')
    ->get();
```

### Get Account Balance
```php
$account = GlAccount::find(1);
$entries = $account->entries()
    ->where('accounting_period_id', $periodId)
    ->where('status', 'posted')
    ->get();

$balance = $entries->sum('debit') - $entries->sum('credit');
```

### Find Unbalanced Entries
```php
$draftEntries = GlEntry::where('status', 'draft')
    ->where('accounting_period_id', $periodId)
    ->get();
```

### Get Period Totals
```php
$totals = GlEntry::where('accounting_period_id', $periodId)
    ->where('status', 'posted')
    ->selectRaw('SUM(debit) as total_debits, SUM(credit) as total_credits')
    ->first();
```

---

## Period Management

### Create New Period
```php
$period = AccountingPeriod::create([
    'year' => 2025,
    'month' => 1,
    'period_start' => Carbon::parse('2025-01-01'),
    'period_end' => Carbon::parse('2025-01-31'),
    'status' => 'open',
]);
```

### Close Period
```php
$period->close(auth()->id(), 'Month end close');
// Sets status to 'closed'
```

### Lock Period
```php
$period->lock();
// Sets status to 'locked' - no modifications allowed
```

### Get Current Period
```php
$period = AccountingPeriod::where('status', 'open')
    ->where('period_start', '<=', now())
    ->where('period_end', '>=', now())
    ->first();
```

---

## Field Mapping

### Sales Table Fields
```
gl_posting_status  → 'pending', 'posted', 'failed'
gl_posted_at       → Timestamp when posted
gl_posting_error   → Error message if failed
bank_account_id    → Link to bank account
```

### Payments Table Fields
```
gl_posting_status  → 'pending', 'posted', 'failed'
gl_posted_at       → Timestamp when posted
gl_posting_error   → Error message if failed
bank_account_id    → Link to bank account
```

### Production Records Fields
```
unit_cost                → Cost per unit produced
total_production_cost    → Total batch cost
```

---

## Performance Tips

1. **Use Query Caching**
   ```php
   Cache::remember('accounts', 86400, fn() => GlAccount::all());
   ```

2. **Index Frequently Searched Fields**
   ```php
   // Already indexed:
   // (gl_account_id, entry_date)
   // (accounting_period_id, status)
   // (reference_type, reference_id)
   ```

3. **Cache Reports During Period**
   ```php
   Cache::remember("bs.{$periodId}", 3600, fn() => $service->generateBalanceSheet($period));
   ```

4. **Batch Post Entries**
   ```php
   DB::transaction(fn() => $entries->each->post(auth()->id()));
   ```

---

## Common Amounts (Check)

| Description | Debit | Credit |
|-------------|-------|--------|
| Sales $100 cash | 1110: 100 | 4110: 100 |
| Sales $100 credit | 1200: 100 | 4110: 100 |
| Payment $100 AR | 1110: 100 | 1200: 100 |
| COGS $30 | 5110: 30 | 1330: 30 |
| RM to WIP $50 | 1320: 50 | 1310: 50 |
| WIP to FG $150 | 1330: 150 | 1320: 150 |

---

## Documentation Links

- **Full Design**: `ACCOUNTING_SYSTEM_DESIGN.md`
- **Implementation**: `ACCOUNTING_IMPLEMENTATION_GUIDE.md`
- **Features**: `ACCOUNTING_SYSTEM_README.md`
- **Summary**: `ACCOUNTING_IMPLEMENTATION_SUMMARY.md`

---

## Key Contacts

- **GL Account Questions**: Review Chart of Accounts structure
- **Service Integration**: Check relevant Service class
- **Report Generation**: Use AccountingReportService
- **Validation Issues**: Check JournalEntryValidator
- **Event Dispatching**: Review Events directory

---

Last Updated: December 15, 2024
