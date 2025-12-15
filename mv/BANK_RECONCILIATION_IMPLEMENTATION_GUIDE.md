# Bank Reconciliation Implementation Guide

**Purpose:** Fix all critical issues in the bank reconciliation system and integrate with the accounting system.

**Status:** Ready for Implementation

---

## Overview

This guide provides step-by-step instructions to:
1. Create required migrations
2. Create required models
3. Create required services
4. Update existing models
5. Rewrite the BankReconciliation component
6. Add audit logging
7. Add GL posting integration

---

## Step 1: Create Database Migrations

### Migration 1: Add Reconciliation Fields to GL Entries

**File:** `database/migrations/2025_12_15_000001_add_reconciliation_to_gl_entries_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gl_entries', function (Blueprint $table) {
            // Add reconciliation tracking
            $table->boolean('reconciled')->default(false)->after('status')->index();
            $table->timestamp('reconciled_at')->nullable()->after('reconciled');
            $table->foreignId('reconciled_by_id')->nullable()->after('reconciled_at');
            $table->string('reconciled_by_type')->default('App\Models\User')->after('reconciled_by_id');
            $table->text('reconciliation_notes')->nullable()->after('reconciled_by_type');
        });
    }

    public function down(): void
    {
        Schema::table('gl_entries', function (Blueprint $table) {
            $table->dropIndex('gl_entries_reconciled_index');
            $table->dropColumn([
                'reconciled',
                'reconciled_at',
                'reconciled_by_id',
                'reconciled_by_type',
                'reconciliation_notes',
            ]);
        });
    }
};
```

### Migration 2: Add Reconciliation Fields to Bank Transactions

**File:** `database/migrations/2025_12_15_000002_add_reconciliation_to_daily_bank_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_bank_transactions', function (Blueprint $table) {
            // Add reconciliation tracking
            $table->boolean('reconciled')->default(false)->after('status')->index();
            $table->timestamp('reconciled_at')->nullable()->after('reconciled');
            $table->foreignId('reconciled_by_id')->nullable()->after('reconciled_at');
            $table->string('reconciled_by_type')->default('App\Models\Employee')->after('reconciled_by_id');
            $table->text('reconciliation_notes')->nullable()->after('reconciled_by_type');
        });
    }

    public function down(): void
    {
        Schema::table('daily_bank_transactions', function (Blueprint $table) {
            $table->dropIndex('daily_bank_transactions_reconciled_index');
            $table->dropColumn([
                'reconciled',
                'reconciled_at',
                'reconciled_by_id',
                'reconciled_by_type',
                'reconciliation_notes',
            ]);
        });
    }
};
```

### Migration 3: Create Bank Reconciliations Table

**File:** `database/migrations/2025_12_15_000003_create_bank_reconciliations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->foreignId('accounting_period_id')->constrained('accounting_periods');
            $table->foreignId('branch_id')->constrained('branches');
            
            // Reconciliation details
            $table->date('statement_date');
            $table->decimal('statement_balance', 14, 2)->comment('Balance per bank statement');
            $table->decimal('gl_balance', 14, 2)->nullable()->comment('Balance per GL at time of reconciliation');
            $table->decimal('difference', 14, 2)->nullable()->comment('Absolute difference between GL and statement');
            
            // Status
            $table->enum('status', ['in_progress', 'completed', 'rejected', 'reversed'])->default('in_progress')->index();
            
            // Who reconciled it
            $table->foreignId('reconciled_by_id')->nullable();
            $table->string('reconciled_by_type')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            
            // Notes and audit
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['bank_account_id', 'status']);
            $table->index(['accounting_period_id']);
            $table->index('reconciled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
```

### Migration 4: Create Bank Reconciliation Details Table

**File:** `database/migrations/2025_12_15_000004_create_bank_reconciliation_details_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliation_details', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('bank_reconciliation_id')->constrained('bank_reconciliations')->onDelete('cascade');
            $table->foreignId('gl_entry_id')->constrained('gl_entries');
            $table->foreignId('daily_bank_transaction_id')->constrained('daily_bank_transactions');
            
            // Match details
            $table->decimal('amount', 14, 2);
            $table->enum('match_type', ['exact', 'auto', 'manual'])->default('manual');
            $table->text('match_notes')->nullable();
            
            // Audit
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique([
                'bank_reconciliation_id',
                'gl_entry_id',
                'daily_bank_transaction_id',
            ]);
            $table->index('match_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_details');
    }
};
```

---

## Step 2: Create Models

### Model 1: BankReconciliation

**File:** `app/Models/BankReconciliation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankReconciliation extends Model
{
    use SoftDeletes;

    protected $table = 'bank_reconciliations';

    protected $fillable = [
        'bank_account_id',
        'accounting_period_id',
        'branch_id',
        'statement_date',
        'statement_balance',
        'gl_balance',
        'difference',
        'status',
        'reconciled_by_id',
        'reconciled_by_type',
        'reconciled_at',
        'notes',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_balance' => 'decimal:2',
        'gl_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    // Relationships
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function accountingPeriod(): BelongsTo
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BankReconciliationDetail::class);
    }

    public function reconciledBy()
    {
        return $this->morphTo('reconciled_by', 'reconciled_by_type', 'reconciled_by_id');
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByBank($query, $bankId)
    {
        return $query->where('bank_account_id', $bankId);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('accounting_period_id', $periodId);
    }

    // Methods
    public function isBalanced(): bool
    {
        return floatval($this->difference) < 0.01;
    }

    public function finalize(): bool
    {
        if ($this->status !== 'in_progress') {
            throw new \Exception('Can only finalize in-progress reconciliations');
        }

        $this->status = 'completed';
        $this->reconciled_at = now();
        $this->reconciled_by_id = auth()->id() ?? auth('employees')->id();
        $this->reconciled_by_type = auth()->guard() === 'web' 
            ? 'App\Models\User' 
            : 'App\Models\Employee';

        return $this->save();
    }

    public function reject(string $reason = null): bool
    {
        $this->status = 'rejected';
        if ($reason) {
            $this->notes = ($this->notes ?? '') . "\n\nRejection Reason: {$reason}";
        }
        return $this->save();
    }

    public function getUnmatchedCount(): int
    {
        return $this->details()->count();
    }
}
```

### Model 2: BankReconciliationDetail

**File:** `app/Models/BankReconciliationDetail.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankReconciliationDetail extends Model
{
    use SoftDeletes;

    protected $table = 'bank_reconciliation_details';

    protected $fillable = [
        'bank_reconciliation_id',
        'gl_entry_id',
        'daily_bank_transaction_id',
        'amount',
        'match_type',
        'match_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function bankReconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class);
    }

    public function glEntry(): BelongsTo
    {
        return $this->belongsTo(GlEntry::class);
    }

    public function bankTransaction(): BelongsTo
    {
        return $this->belongsTo(DailyBankTransaction::class, 'daily_bank_transaction_id');
    }

    // Scopes
    public function scopeAuto($query)
    {
        return $query->where('match_type', 'auto');
    }

    public function scopeManual($query)
    {
        return $query->where('match_type', 'manual');
    }

    public function scopeExact($query)
    {
        return $query->where('match_type', 'exact');
    }
}
```

---

## Step 3: Create Service Class

### BankReconciliationService

**File:** `app/Services/BankReconciliationService.php`

```php
<?php

namespace App\Services;

use App\Models\BankReconciliation;
use App\Models\BankReconciliationDetail;
use App\Models\BankAccount;
use App\Models\GlEntry;
use App\Models\DailyBankTransaction;
use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Exception;

class BankReconciliationService
{
    protected GlPostingService $glPostingService;

    public function __construct(GlPostingService $glPostingService)
    {
        $this->glPostingService = $glPostingService;
    }

    /**
     * Create a new bank reconciliation
     */
    public function createReconciliation(
        BankAccount $bankAccount,
        DateTime $statementDate,
        float $statementBalance,
        string $notes = null
    ): BankReconciliation {
        // Validate bank account belongs to current branch
        if ($bankAccount->branch_id != current_branch_id()) {
            throw new Exception('Bank account does not belong to current branch');
        }

        // Get open accounting period
        $period = AccountingPeriod::where('status', 'open')
            ->where('branch_id', current_branch_id())
            ->first();

        if (!$period) {
            throw new Exception('No open accounting period found');
        }

        // Create reconciliation record
        $reconciliation = BankReconciliation::create([
            'bank_account_id' => $bankAccount->id,
            'accounting_period_id' => $period->id,
            'branch_id' => current_branch_id(),
            'statement_date' => $statementDate,
            'statement_balance' => $statementBalance,
            'status' => 'in_progress',
            'notes' => $notes,
        ]);

        // Calculate GL balance
        $this->updateGlBalance($reconciliation);

        return $reconciliation;
    }

    /**
     * Update GL balance in reconciliation
     */
    public function updateGlBalance(BankReconciliation $reconciliation): void
    {
        $glBalance = GlEntry::where('gl_account_id', $reconciliation->bankAccount->gl_account_id)
            ->where('accounting_period_id', $reconciliation->accounting_period_id)
            ->where('status', 'posted')
            ->where('posted_date', '<=', $reconciliation->statement_date)
            ->sum(\DB::raw('debit - credit'));

        $reconciliation->update([
            'gl_balance' => $glBalance,
            'difference' => abs(floatval($reconciliation->statement_balance) - $glBalance),
        ]);
    }

    /**
     * Get unmatched GL entries
     */
    public function getUnmatchedGlEntries(BankReconciliation $reconciliation): Collection
    {
        $matchedGlIds = BankReconciliationDetail::where('bank_reconciliation_id', $reconciliation->id)
            ->pluck('gl_entry_id')
            ->toArray();

        return GlEntry::where('gl_account_id', $reconciliation->bankAccount->gl_account_id)
            ->where('accounting_period_id', $reconciliation->accounting_period_id)
            ->where('status', 'posted')
            ->where('posted_date', '<=', $reconciliation->statement_date)
            ->whereNotIn('id', $matchedGlIds)
            ->where('reconciled', false)
            ->with('glAccount')
            ->orderBy('posted_date')
            ->get();
    }

    /**
     * Get unmatched bank transactions
     */
    public function getUnmatchedBankTransactions(BankReconciliation $reconciliation): Collection
    {
        $matchedBankIds = BankReconciliationDetail::where('bank_reconciliation_id', $reconciliation->id)
            ->pluck('daily_bank_transaction_id')
            ->toArray();

        return DailyBankTransaction::where('bank_account_id', $reconciliation->bank_account_id)
            ->where('transaction_date', '<=', $reconciliation->statement_date)
            ->whereNotIn('id', $matchedBankIds)
            ->where('reconciled', false)
            ->orderBy('transaction_date')
            ->get();
    }

    /**
     * Manually match GL entry with bank transaction
     */
    public function matchItems(
        BankReconciliation $reconciliation,
        GlEntry $glEntry,
        DailyBankTransaction $bankTransaction,
        string $matchNotes = null
    ): BankReconciliationDetail {
        // Validate amounts match (within 0.01)
        if (abs(floatval($glEntry->debit ?: $glEntry->credit) - floatval($bankTransaction->amount)) > 0.01) {
            throw new Exception('Amount mismatch: GL Entry must match bank transaction amount');
        }

        // Create detail record
        $detail = BankReconciliationDetail::create([
            'bank_reconciliation_id' => $reconciliation->id,
            'gl_entry_id' => $glEntry->id,
            'daily_bank_transaction_id' => $bankTransaction->id,
            'amount' => $bankTransaction->amount,
            'match_type' => 'manual',
            'match_notes' => $matchNotes,
        ]);

        // Mark items as reconciled
        $glEntry->update([
            'reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by_id' => auth()->id() ?? auth('employees')->id(),
            'reconciled_by_type' => auth()->guard() === 'web' ? 'App\Models\User' : 'App\Models\Employee',
        ]);

        $bankTransaction->update([
            'reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by_id' => auth()->id() ?? auth('employees')->id(),
            'reconciled_by_type' => auth()->guard() === 'web' ? 'App\Models\User' : 'App\Models\Employee',
        ]);

        // Log audit
        audit(
            auth('employees')->user() ?? auth()->user(),
            'bank_reconciliation_match',
            $bankTransaction,
            "Matched GL Entry #{$glEntry->id} ({$glEntry->reference_number}) with Bank Transaction #{$bankTransaction->id}",
            'completed',
            null,
            [
                'reconciliation_id' => $reconciliation->id,
                'gl_entry_id' => $glEntry->id,
                'bank_transaction_id' => $bankTransaction->id,
                'amount' => $bankTransaction->amount,
            ]
        );

        return $detail;
    }

    /**
     * Auto-match GL entries with bank transactions
     */
    public function autoMatch(BankReconciliation $reconciliation): array
    {
        $matched = 0;
        $glEntries = $this->getUnmatchedGlEntries($reconciliation);
        $bankTransactions = $this->getUnmatchedBankTransactions($reconciliation)->toArray();

        foreach ($glEntries as $glEntry) {
            $glAmount = floatval($glEntry->debit ?: $glEntry->credit);
            
            // Find matching bank transaction
            $bankTransaction = collect($bankTransactions)
                ->first(function ($bankTxn) use ($glAmount, $glEntry, $reconciliation) {
                    $bankAmount = floatval($bankTxn->amount);
                    
                    // Check amount (±0.01)
                    if (abs($bankAmount - $glAmount) > 0.01) {
                        return false;
                    }
                    
                    // Check date (±3 days)
                    $glDate = $glEntry->posted_date ?? $glEntry->entry_date;
                    $bankDate = $bankTxn->transaction_date;
                    $daysDiff = abs($glDate->diffInDays($bankDate));
                    
                    if ($daysDiff > 3) {
                        return false;
                    }
                    
                    return true;
                });

            if ($bankTransaction) {
                try {
                    $this->matchItems(
                        $reconciliation,
                        $glEntry,
                        $bankTransaction,
                        "Auto-matched: Amount={$glAmount}, Date diff={$daysDiff} days"
                    );
                    $matched++;
                    
                    // Remove from candidates
                    $bankTransactions = collect($bankTransactions)
                        ->filter(fn($b) => $b->id !== $bankTransaction->id)
                        ->toArray();
                } catch (Exception $e) {
                    \Log::warning("Auto-match failed for GL Entry {$glEntry->id}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return [
            'matched_count' => $matched,
            'total_gl_entries' => $glEntries->count(),
            'total_bank_transactions' => count($bankTransactions),
        ];
    }

    /**
     * Remove a match
     */
    public function removeMatch(BankReconciliationDetail $detail): void
    {
        $glEntry = $detail->glEntry;
        $bankTransaction = $detail->bankTransaction;

        // Mark as unreconciled
        $glEntry->update(['reconciled' => false, 'reconciled_at' => null]);
        $bankTransaction->update(['reconciled' => false, 'reconciled_at' => null]);

        // Log audit
        audit(
            auth('employees')->user() ?? auth()->user(),
            'bank_reconciliation_unmatch',
            $bankTransaction,
            "Removed match between GL Entry #{$glEntry->id} and Bank Transaction #{$bankTransaction->id}",
            'completed'
        );

        // Delete detail
        $detail->delete();
    }

    /**
     * Finalize reconciliation
     */
    public function finalizeReconciliation(BankReconciliation $reconciliation, string $approvalNotes = null): bool
    {
        if ($reconciliation->status !== 'in_progress') {
            throw new Exception('Can only finalize in-progress reconciliations');
        }

        // Create adjustment entry if there's a difference
        if ($reconciliation->difference > 0.01) {
            $this->createAdjustmentEntry($reconciliation);
        }

        // Mark reconciliation as complete
        $reconciliation->finalize();

        // Log audit
        audit(
            auth('employees')->user() ?? auth()->user(),
            'bank_reconciliation_finalized',
            $reconciliation,
            "Bank reconciliation for {$reconciliation->bankAccount->bank_name} finalized",
            'completed',
            null,
            [
                'bank_account_id' => $reconciliation->bank_account_id,
                'statement_balance' => $reconciliation->statement_balance,
                'gl_balance' => $reconciliation->gl_balance,
                'difference' => $reconciliation->difference,
                'notes' => $approvalNotes,
            ]
        );

        return true;
    }

    /**
     * Create adjustment GL entry for discrepancies
     */
    public function createAdjustmentEntry(BankReconciliation $reconciliation): void
    {
        if ($reconciliation->difference == 0) {
            return;
        }

        // Only auto-create adjustment if difference is small (< 1% of balance)
        $threshold = abs(floatval($reconciliation->statement_balance)) * 0.01;
        
        if ($reconciliation->difference <= $threshold) {
            // Create adjustment entry
            $description = "Bank Reconciliation Adjustment - {$reconciliation->statement_date}";
            
            // Debit/Credit difference account
            $glEntry = GlEntry::create([
                'gl_account_id' => $this->getAdjustmentAccountId(),
                'accounting_period_id' => $reconciliation->accounting_period_id,
                'entry_type' => 'bank_reconciliation_adjustment',
                'reference_type' => BankReconciliation::class,
                'reference_id' => $reconciliation->id,
                'reference_number' => "BRA-{$reconciliation->id}",
                'description' => $description,
                'debit' => $reconciliation->difference,
                'credit' => 0,
                'entry_date' => now(),
                'status' => 'draft',
                'branch_id' => $reconciliation->branch_id,
                'entered_by_id' => auth()->id() ?? auth('employees')->id(),
                'entered_by_type' => auth()->guard() === 'web' ? 'App\Models\User' : 'App\Models\Employee',
            ]);

            $glEntry->post(auth()->id() ?? auth('employees')->id());
        }
    }

    /**
     * Get adjustment account ID (usually Miscellaneous Expense)
     */
    protected function getAdjustmentAccountId(): int
    {
        // Assuming account 6030 is "Miscellaneous Expense"
        return \App\Models\GlAccount::where('account_number', '6030')
            ->where('is_active', true)
            ->firstOrFail()
            ->id;
    }
}
```

---

## Step 4: Update Existing Models

### Update GlEntry Model

Add these methods to `app/Models/GlEntry.php`:

```php
// Add to fillable array
'reconciled',
'reconciled_at',
'reconciled_by_id',
'reconciled_by_type',
'reconciliation_notes',

// Add to casts
'reconciled' => 'boolean',
'reconciled_at' => 'datetime',

// Add scope
public function scopeUnreconciled($query)
{
    return $query->where('reconciled', false);
}

public function scopeReconciled($query)
{
    return $query->where('reconciled', true);
}

// Add relationship
public function reconciliationDetails()
{
    return $this->hasMany(BankReconciliationDetail::class);
}
```

### Update DailyBankTransaction Model

Add these methods to `app/Models/DailyBankTransaction.php`:

```php
// Add to fillable array
'reconciled',
'reconciled_at',
'reconciled_by_id',
'reconciled_by_type',
'reconciliation_notes',

// Add to casts
'reconciled' => 'boolean',
'reconciled_at' => 'datetime',

// Add scope
public function scopeUnreconciled($query)
{
    return $query->where('reconciled', false);
}

public function scopeReconciled($query)
{
    return $query->where('reconciled', true);
}

// Add relationship
public function reconciliationDetails()
{
    return $this->hasMany(BankReconciliationDetail::class);
}
```

### Update BankAccount Model

Add relationship to `app/Models/BankAccount.php`:

```php
public function reconciliations(): HasMany
{
    return $this->hasMany(BankReconciliation::class);
}
```

---

## Step 5: Rewrite BankReconciliation Component

See separate file: **BANK_RECONCILIATION_COMPONENT_REWRITE.md**

---

## Step 6: Register Service in AppServiceProvider

Add to `app/Providers/AppServiceProvider.php`:

```php
public function register(): void
{
    // Register BankReconciliationService
    $this->app->singleton(BankReconciliationService::class, function ($app) {
        return new BankReconciliationService(
            $app->make(GlPostingService::class)
        );
    });
}
```

---

## Step 7: Create Audit Observer (Optional but Recommended)

**File:** `app/Observers/BankReconciliationObserver.php`

```php
<?php

namespace App\Observers;

use App\Models\BankReconciliation;

class BankReconciliationObserver
{
    public function created(BankReconciliation $reconciliation): void
    {
        audit(
            auth('employees')->user() ?? auth()->user(),
            'bank_reconciliation_created',
            $reconciliation,
            "New bank reconciliation created for {$reconciliation->bankAccount->bank_name}",
            'completed',
            null,
            [
                'bank_account_id' => $reconciliation->bank_account_id,
                'statement_date' => $reconciliation->statement_date,
                'statement_balance' => $reconciliation->statement_balance,
            ]
        );
    }

    public function updated(BankReconciliation $reconciliation): void
    {
        if ($reconciliation->wasChanged('status')) {
            audit(
                auth('employees')->user() ?? auth()->user(),
                'bank_reconciliation_status_changed',
                $reconciliation,
                "Bank reconciliation status changed to {$reconciliation->status}",
                'completed',
                null,
                [
                    'old_status' => $reconciliation->getOriginal('status'),
                    'new_status' => $reconciliation->status,
                ]
            );
        }
    }
}
```

Register in AppServiceProvider:

```php
protected $observers = [
    BankReconciliation::class => [BankReconciliationObserver::class],
];
```

---

## Deployment Steps

1. Run migrations: `php artisan migrate`
2. Create models and services (copy code above)
3. Update existing models
4. Rewrite component
5. Register service in AppServiceProvider
6. Run tests: `php artisan test`
7. Manual testing (see checklist below)

---

## Testing Checklist

- [ ] Create new bank reconciliation
- [ ] Load GL entries for bank account
- [ ] Load bank transactions
- [ ] Manually match items
- [ ] Verify audit logs created
- [ ] Auto-match items
- [ ] Remove match
- [ ] Finalize reconciliation
- [ ] Verify GL posting for adjustments
- [ ] Check all GL entries marked as reconciled
- [ ] Check all bank transactions marked as reconciled
- [ ] View reconciliation history
- [ ] Test branch context validation
- [ ] Test permission validation

---

## Performance Considerations

1. **Pagination**: Implement pagination for large datasets
2. **Indexes**: Ensure all foreign keys and status fields are indexed
3. **Caching**: Cache unmatched items while reconciliation is in progress
4. **Batch Operations**: Allow batch matching for multiple items
5. **Background Jobs**: Consider queueing auto-match for large datasets

---

## Security Considerations

1. **Authorization**: Only allow users with 'reconcile_bank_accounts' permission
2. **Branch Context**: Always validate bank belongs to current branch
3. **Audit Trail**: Log all reconciliation actions
4. **Approval Workflow**: Consider requiring approval for finalized reconciliations
5. **Read-Only**: Make completed reconciliations read-only (only allow reversal)

---

## Next Steps

1. Implement all migrations
2. Create all models and services
3. Rewrite component
4. Test thoroughly
5. Deploy to staging
6. UAT with accountants
7. Deploy to production

---

**Prepared by:** Amp Code Auditor  
**Date:** December 15, 2025
