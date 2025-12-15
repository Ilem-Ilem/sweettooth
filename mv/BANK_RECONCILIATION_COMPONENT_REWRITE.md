# Bank Reconciliation Component Rewrite Guide

**File:** `app/Livewire/Accounting/BankReconciliation.php`

**Status:** Complete rewrite required

---

## Complete Rewritten Component

```php
<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BankAccount;
use App\Models\BankReconciliation as BankReconciliationModel;
use App\Models\GlEntry;
use App\Models\DailyBankTransaction;
use App\Models\AccountingPeriod;
use App\Services\BankReconciliationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Exception;

class BankReconciliation extends Component
{
    use WithPagination;

    // State Properties
    public ?int $selectedBankId = null;
    public ?int $selectedReconciliationId = null;
    public ?string $statementDate = null;
    public ?float $statementBalance = null;

    // UI State
    public string $view = 'list'; // 'list', 'detail', 'match'
    public string $activeTab = 'unmatched'; // 'unmatched', 'matched', 'summary'

    // Filters
    public ?string $glFilterReference = null;
    public ?string $glFilterDescription = null;
    public ?float $glFilterAmountMin = null;
    public ?float $glFilterAmountMax = null;
    public ?string $bankFilterReference = null;
    public ?string $bankFilterDescription = null;
    public ?float $bankFilterAmountMin = null;
    public ?float $bankFilterAmountMax = null;

    // Service
    protected BankReconciliationService $reconciliationService;

    // Cached data
    protected ?BankReconciliationModel $currentReconciliation = null;
    protected ?Collection $unmatchedGlItems = null;
    protected ?Collection $unmatchedBankItems = null;
    protected ?Collection $matchedItems = null;

    public function mount()
    {
        $this->reconciliationService = app(BankReconciliationService::class);
        $this->statementDate = Carbon::today()->format('Y-m-d');
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.accounting.bank-reconciliation', [
            'bankAccounts' => $this->getBankAccounts(),
            'reconciliations' => $this->getReconciliations(),
            'currentReconciliation' => $this->currentReconciliation,
            'unmatchedGlItems' => $this->unmatchedGlItems,
            'unmatchedBankItems' => $this->unmatchedBankItems,
            'matchedItems' => $this->matchedItems,
            'summary' => $this->getReconciliationSummary(),
        ]);
    }

    /**
     * Get bank accounts for current branch
     */
    public function getBankAccounts(): Collection
    {
        return BankAccount::where('branch_id', current_branch_id())
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->get();
    }

    /**
     * Get reconciliations for current branch
     */
    public function getReconciliations(): Collection
    {
        return BankReconciliationModel::where('branch_id', current_branch_id())
            ->with('bankAccount', 'accountingPeriod')
            ->orderBy('statement_date', 'desc')
            ->paginate(15)
            ->items();
    }

    /**
     * Create new reconciliation
     */
    public function createReconciliation()
    {
        try {
            // Validate inputs
            if (!$this->selectedBankId) {
                $this->addError('selectedBankId', 'Bank account is required');
                return;
            }

            if (!$this->statementDate) {
                $this->addError('statementDate', 'Statement date is required');
                return;
            }

            if (!$this->statementBalance) {
                $this->addError('statementBalance', 'Statement balance is required');
                return;
            }

            // Get bank account
            $bankAccount = BankAccount::findOrFail($this->selectedBankId);

            // Validate branch access
            if ($bankAccount->branch_id != current_branch_id()) {
                throw new Exception('Not authorized to reconcile this bank account');
            }

            // Create reconciliation via service
            $this->currentReconciliation = $this->reconciliationService->createReconciliation(
                $bankAccount,
                Carbon::parse($this->statementDate),
                floatval($this->statementBalance)
            );

            $this->selectedReconciliationId = $this->currentReconciliation->id;
            $this->view = 'detail';
            $this->activeTab = 'unmatched';
            $this->loadReconciliationData();

            session()->flash('success', 'Bank reconciliation created successfully');
        } catch (Exception $e) {
            $this->addError('create', $e->getMessage());
            \Log::error('Failed to create bank reconciliation', [
                'error' => $e->getMessage(),
                'bank_id' => $this->selectedBankId,
            ]);
        }
    }

    /**
     * Load unmatched GL entries and bank transactions
     */
    public function loadReconciliationData()
    {
        try {
            if (!$this->currentReconciliation) {
                return;
            }

            // Get unmatched GL entries
            $glEntries = $this->reconciliationService->getUnmatchedGlEntries($this->currentReconciliation);

            // Apply filters
            $glEntries = $this->filterGlEntries($glEntries);

            $this->unmatchedGlItems = $glEntries->map(fn($entry) => [
                'id' => $entry->id,
                'type' => 'gl',
                'date' => $entry->posted_date?->format('Y-m-d') ?? $entry->entry_date->format('Y-m-d'),
                'reference' => $entry->reference_number ?? 'N/A',
                'description' => $entry->description,
                'amount' => floatval($entry->debit ?: $entry->credit),
                'account' => $entry->glAccount?->account_number,
                'raw' => $entry,
            ])->values();

            // Get unmatched bank transactions
            $bankTransactions = $this->reconciliationService->getUnmatchedBankTransactions($this->currentReconciliation);

            // Apply filters
            $bankTransactions = $this->filterBankTransactions($bankTransactions);

            $this->unmatchedBankItems = $bankTransactions->map(fn($txn) => [
                'id' => $txn->id,
                'type' => 'bank',
                'date' => $txn->transaction_date->format('Y-m-d'),
                'reference' => $txn->reference_number ?? 'N/A',
                'description' => $txn->description,
                'amount' => floatval($txn->amount),
                'transaction_type' => $txn->transaction_type,
                'transaction_subtype' => $txn->transaction_subtype,
                'status' => $txn->status,
                'raw' => $txn,
            ])->values();

            // Get matched items
            $this->loadMatchedItems();
        } catch (Exception $e) {
            $this->addError('load', 'Failed to load reconciliation data: ' . $e->getMessage());
            \Log::error('Failed to load reconciliation data', [
                'error' => $e->getMessage(),
                'reconciliation_id' => $this->currentReconciliation->id ?? null,
            ]);
        }
    }

    /**
     * Load matched items
     */
    protected function loadMatchedItems()
    {
        if (!$this->currentReconciliation) {
            return;
        }

        $this->matchedItems = $this->currentReconciliation->details()
            ->with(['glEntry', 'bankTransaction'])
            ->get()
            ->map(fn($detail) => [
                'detail_id' => $detail->id,
                'gl_entry' => $detail->glEntry,
                'bank_transaction' => $detail->bankTransaction,
                'amount' => floatval($detail->amount),
                'match_type' => $detail->match_type,
                'match_notes' => $detail->match_notes,
            ]);
    }

    /**
     * Filter GL entries
     */
    protected function filterGlEntries(Collection $entries): Collection
    {
        return $entries->filter(function ($entry) {
            // Filter by reference
            if ($this->glFilterReference && 
                stripos($entry->reference_number, $this->glFilterReference) === false) {
                return false;
            }

            // Filter by description
            if ($this->glFilterDescription && 
                stripos($entry->description, $this->glFilterDescription) === false) {
                return false;
            }

            $amount = floatval($entry->debit ?: $entry->credit);

            // Filter by amount range
            if ($this->glFilterAmountMin && $amount < $this->glFilterAmountMin) {
                return false;
            }
            if ($this->glFilterAmountMax && $amount > $this->glFilterAmountMax) {
                return false;
            }

            return true;
        });
    }

    /**
     * Filter bank transactions
     */
    protected function filterBankTransactions(Collection $transactions): Collection
    {
        return $transactions->filter(function ($txn) {
            // Filter by reference
            if ($this->bankFilterReference && 
                stripos($txn->reference_number, $this->bankFilterReference) === false) {
                return false;
            }

            // Filter by description
            if ($this->bankFilterDescription && 
                stripos($txn->description, $this->bankFilterDescription) === false) {
                return false;
            }

            $amount = floatval($txn->amount);

            // Filter by amount range
            if ($this->bankFilterAmountMin && $amount < $this->bankFilterAmountMin) {
                return false;
            }
            if ($this->bankFilterAmountMax && $amount > $this->bankFilterAmountMax) {
                return false;
            }

            return true;
        });
    }

    /**
     * Load existing reconciliation
     */
    public function loadReconciliation(int $reconciliationId)
    {
        try {
            $this->currentReconciliation = BankReconciliationModel::findOrFail($reconciliationId);

            // Validate branch access
            if ($this->currentReconciliation->branch_id != current_branch_id()) {
                throw new Exception('Not authorized to view this reconciliation');
            }

            $this->selectedReconciliationId = $reconciliationId;
            $this->selectedBankId = $this->currentReconciliation->bank_account_id;
            $this->statementDate = $this->currentReconciliation->statement_date->format('Y-m-d');
            $this->statementBalance = floatval($this->currentReconciliation->statement_balance);
            $this->view = 'detail';
            $this->loadReconciliationData();
        } catch (Exception $e) {
            $this->addError('load', $e->getMessage());
        }
    }

    /**
     * Manually match GL entry with bank transaction
     */
    public function matchItems(int $glId, int $bankId, ?string $notes = null)
    {
        try {
            if (!$this->currentReconciliation) {
                throw new Exception('No active reconciliation');
            }

            $glEntry = GlEntry::findOrFail($glId);
            $bankTransaction = DailyBankTransaction::findOrFail($bankId);

            // Use service to match items
            $this->reconciliationService->matchItems(
                $this->currentReconciliation,
                $glEntry,
                $bankTransaction,
                $notes
            );

            // Reload data
            $this->loadReconciliationData();
            session()->flash('success', 'Items matched successfully');
        } catch (Exception $e) {
            $this->addError('match', 'Failed to match items: ' . $e->getMessage());
            \Log::error('Failed to match reconciliation items', [
                'error' => $e->getMessage(),
                'gl_id' => $glId,
                'bank_id' => $bankId,
            ]);
        }
    }

    /**
     * Remove a match
     */
    public function unmatchItems(int $detailId)
    {
        try {
            $detail = \App\Models\BankReconciliationDetail::findOrFail($detailId);

            $this->reconciliationService->removeMatch($detail);

            // Reload data
            $this->loadReconciliationData();
            session()->flash('success', 'Match removed successfully');
        } catch (Exception $e) {
            $this->addError('unmatch', 'Failed to remove match: ' . $e->getMessage());
        }
    }

    /**
     * Auto-match items
     */
    public function autoMatch()
    {
        try {
            if (!$this->currentReconciliation) {
                throw new Exception('No active reconciliation');
            }

            $results = $this->reconciliationService->autoMatch($this->currentReconciliation);

            $this->loadReconciliationData();
            session()->flash('success', 
                "Auto-matched {$results['matched_count']} items. " .
                "{$results['total_gl_entries']} GL entries and " .
                "{$results['total_bank_transactions']} bank transactions remaining."
            );
        } catch (Exception $e) {
            $this->addError('auto_match', 'Auto-match failed: ' . $e->getMessage());
        }
    }

    /**
     * Finalize reconciliation
     */
    public function finalizeReconciliation(string $approvalNotes = null)
    {
        try {
            if (!$this->currentReconciliation) {
                throw new Exception('No active reconciliation');
            }

            // Require all items to be matched
            if ($this->unmatchedGlItems?->count() || $this->unmatchedBankItems?->count()) {
                throw new Exception(
                    'Cannot finalize: ' . 
                    ($this->unmatchedGlItems?->count() ?? 0) . 
                    ' GL entries and ' . 
                    ($this->unmatchedBankItems?->count() ?? 0) . 
                    ' bank transactions remain unmatched'
                );
            }

            // Finalize
            $this->reconciliationService->finalizeReconciliation(
                $this->currentReconciliation,
                $approvalNotes
            );

            $this->currentReconciliation->refresh();
            session()->flash('success', 'Bank reconciliation completed successfully');
        } catch (Exception $e) {
            $this->addError('finalize', 'Failed to finalize: ' . $e->getMessage());
        }
    }

    /**
     * Get reconciliation summary
     */
    public function getReconciliationSummary(): array
    {
        if (!$this->currentReconciliation) {
            return [];
        }

        $glTotal = $this->unmatchedGlItems?->sum('amount') ?? 0;
        $bankTotal = $this->unmatchedBankItems?->sum('amount') ?? 0;
        $difference = abs($glTotal - $bankTotal);

        return [
            'reconciliation_id' => $this->currentReconciliation->id,
            'statement_balance' => floatval($this->currentReconciliation->statement_balance),
            'gl_balance' => floatval($this->currentReconciliation->gl_balance ?? 0),
            'reconciliation_difference' => floatval($this->currentReconciliation->difference ?? 0),
            'unmatched_gl_total' => floatval($glTotal),
            'unmatched_bank_total' => floatval($bankTotal),
            'unmatched_difference' => floatval($difference),
            'is_balanced' => $difference < 0.01,
            'gl_count' => $this->unmatchedGlItems?->count() ?? 0,
            'bank_count' => $this->unmatchedBankItems?->count() ?? 0,
            'matched_count' => $this->matchedItems?->count() ?? 0,
            'status' => $this->currentReconciliation->status,
            'can_finalize' => $this->currentReconciliation->status === 'in_progress' && 
                            $difference < 0.01,
        ];
    }

    /**
     * Cancel reconciliation
     */
    public function cancelReconciliation()
    {
        try {
            if (!$this->currentReconciliation) {
                throw new Exception('No active reconciliation');
            }

            $this->currentReconciliation->reject('User cancelled reconciliation');

            audit(
                auth('employees')->user() ?? auth()->user(),
                'bank_reconciliation_cancelled',
                $this->currentReconciliation,
                'Bank reconciliation cancelled',
                'completed'
            );

            $this->currentReconciliation = null;
            $this->selectedReconciliationId = null;
            $this->view = 'list';
            
            session()->flash('success', 'Bank reconciliation cancelled');
        } catch (Exception $e) {
            $this->addError('cancel', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    /**
     * Update GL balance calculation
     */
    public function updateGlBalance()
    {
        try {
            if (!$this->currentReconciliation) {
                return;
            }

            $this->reconciliationService->updateGlBalance($this->currentReconciliation);
            $this->currentReconciliation->refresh();

            session()->flash('success', 'GL balance updated');
        } catch (Exception $e) {
            $this->addError('update_balance', $e->getMessage());
        }
    }

    /**
     * Clear filters
     */
    public function clearFilters()
    {
        $this->glFilterReference = null;
        $this->glFilterDescription = null;
        $this->glFilterAmountMin = null;
        $this->glFilterAmountMax = null;
        $this->bankFilterReference = null;
        $this->bankFilterDescription = null;
        $this->bankFilterAmountMin = null;
        $this->bankFilterAmountMax = null;

        $this->loadReconciliationData();
    }

    /**
     * Switch tab
     */
    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Switch view
     */
    public function setView(string $view)
    {
        $this->view = $view;
    }
}
```

---

## Key Improvements Over Original

1. **Proper Database Integration**
   - Uses BankReconciliationModel to persist data
   - Uses service layer for business logic
   - Proper error handling and validation

2. **Complete Audit Trail**
   - Service calls create audit logs
   - All actions tracked
   - User attribution

3. **Branch Context Validation**
   - Validates bank belongs to current branch
   - Validates reconciliation belongs to current branch
   - Prevents cross-branch access

4. **Proper Field Names**
   - Uses correct database fields (status, cleared, etc.)
   - Doesn't reference non-existent fields
   - Proper casting

5. **Service-Driven Architecture**
   - Business logic in service
   - Component handles UI only
   - Testable and reusable

6. **Enhanced Features**
   - Filters for GL entries and bank transactions
   - Better auto-match logic
   - Reconciliation state management
   - Approval workflow ready

7. **Better Error Handling**
   - Try-catch blocks with logging
   - User-friendly error messages
   - Proper exception handling

8. **State Management**
   - Tracks current reconciliation
   - Persists matched items
   - Maintains filter state

---

## Blade Template Structure

The component expects a view at `resources/views/livewire/accounting/bank-reconciliation.blade.php`

Key sections needed:

1. **List View** - Show all reconciliations with ability to create new
2. **Detail View** - Show current reconciliation with matching interface
3. **Unmatched Tab** - Side-by-side GL and bank items
4. **Matched Tab** - Show matched pairs
5. **Summary Tab** - Show totals and balance status

---

## Testing the Component

```php
// Test creating reconciliation
$this->actingAs($employee)
    ->livewire(BankReconciliation::class)
    ->set('selectedBankId', $bankAccount->id)
    ->set('statementDate', '2025-12-15')
    ->set('statementBalance', 50000)
    ->call('createReconciliation')
    ->assertHasNoErrors();

// Test matching items
$this->livewire(BankReconciliation::class)
    ->call('loadReconciliation', $reconciliation->id)
    ->call('matchItems', $glEntry->id, $bankTransaction->id)
    ->assertHasNoErrors();

// Test finalization
$this->livewire(BankReconciliation::class)
    ->call('loadReconciliation', $reconciliation->id)
    ->call('finalizeReconciliation', 'All items matched')
    ->assertHasNoErrors();
```

---

## Notes

- Component now uses proper service layer
- All business logic in service is testable
- Component focuses on UI state management
- Proper error handling throughout
- Audit logging integrated
- Branch context validation enforced

