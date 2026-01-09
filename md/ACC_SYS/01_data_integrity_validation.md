# Data Integrity and Validation Issues in Accounting System

## 1. Journal Entry Balancing Faults

### Problem Description
The accounting system lacks proper validation to ensure that journal entries are balanced (debits equal credits). This can lead to an unbalanced general ledger and inaccurate financial reporting.

### Specific Code Evidence
- In `ManualJournalEntry.php`, the balancing validation exists but may not be comprehensive enough
- The `isBalanced` computed property uses a tolerance of 0.01 which may not be sufficient for all currencies
- No validation prevents users from saving unbalanced entries in certain scenarios

### Validation Code Example
```php
// From ManualJournalEntry.php - current balancing validation
#[Computed]
public function isBalanced()
{
    return abs($this->totalDebits - $this->totalCredits) < 0.01;
}

public function submit()
{
    // ... other validation
    
    if (! $this->isBalanced) {
        throw ValidationException::withMessages(['lines' => 'Journal entry must be balanced (Debits = Credits)']);
    }
    
    // ... rest of submission
}
```

### Impact
- Unbalanced general ledger
- Inaccurate financial statements
- Compliance violations
- Audit findings

### Solution: Enhanced Balancing Validation
```php
// Improved balancing validation with configurable tolerance
class JournalEntryValidator
{
    public static function validateBalanced(array $lines, ?string $currency = null): array
    {
        $totalDebits = array_sum(array_column($lines, 'debit'));
        $totalCredits = array_sum(array_column($lines, 'credit'));
        
        // Get currency-specific tolerance
        $tolerance = self::getCurrencyTolerance($currency);
        
        $difference = abs($totalDebits - $totalCredits);
        
        if ($difference > $tolerance) {
            return [
                'valid' => false,
                'message' => "Journal entry is unbalanced. Difference: " . 
                           self::formatCurrency($difference, $currency)
            ];
        }
        
        return ['valid' => true];
    }
    
    private static function getCurrencyTolerance(?string $currency = null): float
    {
        $currency = $currency ?? 'NGN'; // Default to Nigerian Naira
        
        // Different currencies may have different minimum units
        $tolerances = [
            'NGN' => 0.01,  // Kobo
            'USD' => 0.01,  // Cent
            'JPY' => 1.00,  // Yen (no cents)
            'EUR' => 0.01,  // Euro cent
        ];
        
        return $tolerances[$currency] ?? 0.01;
    }
    
    private static function formatCurrency(float $amount, ?string $currency): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
}

// Updated ManualJournalEntry component
class ManualJournalEntry extends Component
{
    // ... existing properties
    
    public function submit()
    {
        $this->validate([
            // ... existing validations
        ]);

        // Enhanced balancing validation
        $validationResult = JournalEntryValidator::validateBalanced($this->lines);
        if (!$validationResult['valid']) {
            throw ValidationException::withMessages([
                'lines' => $validationResult['message']
            ]);
        }

        // ... rest of submission logic
    }
}
```

## 2. Foreign Key Constraint Issues

### Problem Description
Missing foreign key constraints between related accounting entities can lead to orphaned records and data integrity issues.

### Specific Code Evidence
- From `export.sql`, some foreign key constraints exist but may not cover all relationships
- The `GlEntry` model references `GlAccount` and `AccountingPeriod` but constraints might not be properly enforced
- No cascading deletes or updates in some cases

### Impact
- Orphaned GL entries when accounts are deleted
- Referential integrity violations
- Data corruption and inconsistencies
- Difficult to maintain data quality

### Solution: Add Proper Foreign Key Constraints
```php
// Migration to add missing foreign key constraints
Schema::table('gl_entries', function (Blueprint $table) {
    $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->onDelete('cascade');
    $table->foreign('accounting_period_id')->references('id')->on('accounting_periods')->onDelete('restrict');
    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
});

Schema::table('gl_accounts', function (Blueprint $table) {
    $table->foreign('parent_account_id')->references('id')->on('gl_accounts')->onDelete('set null');
});

Schema::table('account_transfers', function (Blueprint $table) {
    $table->foreign('from_bank_account_id')->references('id')->on('bank_accounts')->onDelete('restrict');
    $table->foreign('to_bank_account_id')->references('id')->on('bank_accounts')->onDelete('restrict');
    $table->foreign('gl_entry_id')->references('id')->on('gl_entries')->onDelete('cascade');
});
```

## 3. Account Type Validation Issues

### Problem Description
The system lacks validation to ensure that account types are used correctly (e.g., asset accounts shouldn't have negative balances).

### Specific Code Evidence
- In `GlAccount.php`, the `getBalance()` method calculates balances differently based on account type
- No validation prevents users from entering incorrect debit/credit amounts for specific account types
- The `updateBalance()` method doesn't validate account type restrictions

### Account Type Validation Code
```php
// From GlAccount.php - current balance calculation
public function getBalance(): float
{
    // Asset, Expense, COGS accounts: Debit is positive
    if (in_array($this->account_type, ['asset', 'cogs', 'expense'])) {
        return floatval($this->debit_balance) - floatval($this->credit_balance);
    }

    // Liability, Equity, Revenue, Tax accounts: Credit is positive
    if (in_array($this->account_type, ['liability', 'equity', 'revenue', 'tax'])) {
        return floatval($this->credit_balance) - floatval($this->debit_balance);
    }

    return 0;
}

// Enhanced account type validation
class AccountTypeValidator
{
    public static function validateEntryForAccountType(
        GlAccount $account, 
        float $debit, 
        float $credit
    ): array {
        $result = ['valid' => true, 'messages' => []];
        
        switch ($account->account_type) {
            case 'asset':
                // Assets typically have debit balances
                $newBalance = $account->debit_balance - $account->credit_balance + $debit - $credit;
                if ($newBalance < 0 && $account->account_number !== '1000') { // Allow cash to go negative temporarily
                    $result['valid'] = false;
                    $result['messages'][] = "Asset account {$account->account_number} cannot have negative balance";
                }
                break;
                
            case 'liability':
                // Liabilities typically have credit balances
                $newBalance = $account->credit_balance - $account->debit_balance - $debit + $credit;
                if ($newBalance < 0) {
                    $result['valid'] = false;
                    $result['messages'][] = "Liability account {$account->account_number} cannot have negative balance";
                }
                break;
                
            case 'equity':
                // Equity typically has credit balance
                $newBalance = $account->credit_balance - $account->debit_balance - $debit + $credit;
                if ($newBalance < 0) {
                    $result['valid'] = false;
                    $result['messages'][] = "Equity account {$account->account_number} cannot have negative balance";
                }
                break;
                
            case 'revenue':
                // Revenue increases equity (credit balance)
                break;
                
            case 'expense':
                // Expenses decrease equity (debit balance)
                break;
                
            case 'cogs': // Cost of Goods Sold
                // COGS is an expense (debit balance)
                break;
                
            case 'header':
                $result['valid'] = false;
                $result['messages'][] = "Cannot post to header account {$account->account_number}";
                break;
        }
        
        return $result;
    }
}

// Updated GlEntry model with validation
class GlEntry extends Model
{
    public function post(string|int $userId): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        // Validate account type restrictions
        $validation = AccountTypeValidator::validateEntryForAccountType(
            $this->glAccount,
            floatval($this->debit),
            floatval($this->credit)
        );
        
        if (!$validation['valid']) {
            throw new ValidationException(
                Validation::make([], [])->errors()->add('account_type', $validation['messages'])
            );
        }

        $this->status = 'posted';
        $this->posted_by_id = $userId;
        $this->posted_at = now();
        $this->save();

        // Update account balances
        $this->glAccount->updateBalance(floatval($this->debit), floatval($this->credit));

        return true;
    }
}
```

## 4. Decimal Precision Issues

### Problem Description
Inconsistent decimal precision handling across the accounting system can lead to rounding errors and calculation inaccuracies.

### Specific Code Evidence
- Various models use different decimal casting approaches
- Currency calculations may lose precision
- No standardized approach to handling decimal arithmetic

### Solution: Standardized Decimal Handling
```php
// Create a decimal utility class
class DecimalCalculator
{
    public static function add(float $a, float $b, int $precision = 2): float
    {
        return round($a + $b, $precision);
    }
    
    public static function subtract(float $a, float $b, int $precision = 2): float
    {
        return round($a - $b, $precision);
    }
    
    public static function multiply(float $a, float $b, int $precision = 2): float
    {
        return round($a * $b, $precision);
    }
    
    public static function divide(float $a, float $b, int $precision = 2): float
    {
        if ($b == 0) {
            throw new DivisionByZeroError("Cannot divide by zero");
        }
        return round($a / $b, $precision);
    }
    
    public static function compare(float $a, float $b, float $tolerance = 0.01): int
    {
        $diff = abs($a - $b);
        if ($diff <= $tolerance) {
            return 0; // Equal
        }
        return $a > $b ? 1 : -1;
    }
}

// Updated GlAccount with proper decimal handling
class GlAccount extends Model
{
    public function updateBalance(float $debit, float $credit): void
    {
        $newDebitBalance = DecimalCalculator::add(
            floatval($this->debit_balance), 
            $debit
        );
        
        $newCreditBalance = DecimalCalculator::add(
            floatval($this->credit_balance), 
            $credit
        );
        
        $this->debit_balance = $newDebitBalance;
        $this->credit_balance = $newCreditBalance;
        
        $this->save();
    }
    
    public function getBalance(): float
    {
        $calculatedBalance = match($this->account_type) {
            'asset', 'cogs', 'expense' => DecimalCalculator::subtract(
                floatval($this->debit_balance), 
                floatval($this->credit_balance)
            ),
            'liability', 'equity', 'revenue', 'tax' => DecimalCalculator::subtract(
                floatval($this->credit_balance), 
                floatval($this->debit_balance)
            ),
            default => 0
        };
        
        return $calculatedBalance;
    }
}

// Configuration for decimal precision
// config/accounting.php
return [
    'decimal_precision' => env('ACCOUNTING_DECIMAL_PRECISION', 2),
    'currency_tolerance' => env('ACCOUNTING_CURRENCY_TOLERANCE', 0.01),
    'rounding_method' => env('ACCOUNTING_ROUNDING_METHOD', 'half_up'), // half_up, half_down, up, down
];
```

## 5. Period Validation Issues

### Problem Description
The system allows journal entries to be posted to closed or inactive accounting periods, violating accounting principles.

### Specific Code Evidence
- In `AccountingService.php`, the `getCurrentPeriod()` method only looks for open periods
- No validation in `GlEntry::post()` to ensure the entry date falls within the accounting period
- Manual journal entries may bypass period validation

### Solution: Enhanced Period Validation
```php
// Enhanced AccountingPeriod model
class AccountingPeriod extends Model
{
    protected $fillable = [
        'name',
        'year',
        'month',
        'period_start',
        'period_end',
        'status',
        'closing_date',
        'closed_by_id',
        'closed_at',
    ];
    
    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'closing_date' => 'datetime',
        'closed_at' => 'datetime',
    ];
    
    public function isOpen(): bool
    {
        return $this->status === 'open' && 
               $this->period_start <= now() && 
               $this->period_end >= now();
    }
    
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
    
    public function isWithinPeriod(\DateTimeInterface $date): bool
    {
        return $date >= $this->period_start && $date <= $this->period_end;
    }
    
    public function isFuture(): bool
    {
        return $this->period_start > now();
    }
    
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
    
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
    
    public function scopeForDate($query, \DateTimeInterface $date)
    {
        return $query->where('period_start', '<=', $date)
                    ->where('period_end', '>=', $date);
    }
}

// Enhanced GlEntry with period validation
class GlEntry extends Model
{
    public function post(string|int $userId): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }
        
        // Validate accounting period
        if (!$this->period || $this->period->isClosed()) {
            throw new Exception('Cannot post to a closed accounting period');
        }
        
        if (!$this->period->isWithinPeriod($this->entry_date)) {
            throw new Exception('Entry date is outside the accounting period');
        }
        
        // Validate account type restrictions
        $validation = AccountTypeValidator::validateEntryForAccountType(
            $this->glAccount,
            floatval($this->debit),
            floatval($this->credit)
        );
        
        if (!$validation['valid']) {
            throw new ValidationException(
                Validation::make([], [])->errors()->add('account_type', $validation['messages'])
            );
        }

        $this->status = 'posted';
        $this->posted_by_id = $userId;
        $this->posted_at = now();
        $this->save();

        // Update account balances
        $this->glAccount->updateBalance(floatval($this->debit), floatval($this->credit));

        return true;
    }
}

// Enhanced validation in ManualJournalEntry
class ManualJournalEntry extends Component
{
    public function submit()
    {
        $this->validate([
            // ... existing validations
        ]);

        // Validate accounting period
        $period = AccountingPeriod::find($this->periodId);
        if (!$period || $period->isClosed()) {
            throw ValidationException::withMessages([
                'periodId' => 'Cannot create entries for a closed accounting period'
            ]);
        }

        // Validate entry date is within period
        $entryDate = Carbon::parse($this->entryDate);
        if (!$period->isWithinPeriod($entryDate)) {
            throw ValidationException::withMessages([
                'entryDate' => 'Entry date must fall within the selected accounting period'
            ]);
        }

        // Enhanced balancing validation
        $validationResult = JournalEntryValidator::validateBalanced($this->lines);
        if (!$validationResult['valid']) {
            throw ValidationException::withMessages([
                'lines' => $validationResult['message']
            ]);
        }

        // ... rest of submission logic
    }
}
```

## Implementation Checklist

- [ ] Implement enhanced journal entry balancing validation
- [ ] Add proper foreign key constraints to database
- [ ] Create account type validation system
- [ ] Standardize decimal precision handling
- [ ] Add comprehensive period validation
- [ ] Update all related models and components
- [ ] Add proper error handling for validation failures
- [ ] Create unit tests for validation logic