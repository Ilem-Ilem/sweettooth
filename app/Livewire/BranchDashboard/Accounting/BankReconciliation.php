<?php

namespace App\Livewire\BranchDashboard\Accounting;

use App\Models\BankAccount;
use App\Models\BankReconciliation as BankReconciliationModel;
use App\Models\GlEntry;
use App\Models\DailyBankTransaction;
use App\Services\BankReconciliationService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class BankReconciliation extends Component
{
    protected BankReconciliationService $service;

    // Tab control
    public string $activeTab = 'select'; // select, matching, results

    // Selected data
    public ?int $selectedBankAccountId = null;
    public ?int $selectedReconciliationId = null;
    public ?string $reconciliationDate = null;
    public ?string $bankBalance = null;

    // Matching data
    public array $glEntries = [];
    public array $bankTransactions = [];
    public array $matchedPairs = [];
    public array $selectedForMatching = [];

    // Statistics
    public array $stats = [];
    public bool $isBalanced = false;

    // UI state
    public bool $showAutoMatch = false;
    public int $autoMatchCount = 0;

    public function mount()
    {
        $this->service = app(BankReconciliationService::class);
    }

    public function render()
    {
        $bankAccounts = BankAccount::where('is_active', true)
            ->get();

        $reconciliation = null;
        if ($this->selectedReconciliationId) {
            $reconciliation = BankReconciliationModel::find($this->selectedReconciliationId);
        }

        return view('livewire.branch-dashboard.accounting.bank-reconciliation', [
            'bankAccounts' => $bankAccounts,
            'reconciliation' => $reconciliation,
            'glEntries' => $this->glEntries,
            'bankTransactions' => $this->bankTransactions,
            'matchedPairs' => $this->matchedPairs,
            'stats' => $this->stats,
            'isBalanced' => $this->isBalanced,
        ]);
    }

    /**
     * Start a new reconciliation
     */
    public function startReconciliation()
    {
        if (!$this->selectedBankAccountId || !$this->reconciliationDate || !$this->bankBalance) {
            $this->addError('form', 'Please fill all required fields');
            return;
        }

        try {
            $reconciliationDate = Carbon::createFromFormat('Y-m-d', $this->reconciliationDate);

            $reconciliation = $this->service->createReconciliation(
                $this->selectedBankAccountId,
                $reconciliationDate,
                (float) $this->bankBalance,
                current_branch_id()
            );

            $this->selectedReconciliationId = $reconciliation->id;
            $this->loadReconciliationData();
            $this->activeTab = 'matching';
            $this->dispatch('notify', message: 'Reconciliation started successfully');
        } catch (\Exception $e) {
            $this->addError('form', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Load reconciliation data
     */
    private function loadReconciliationData()
    {
        if (!$this->selectedReconciliationId) {
            return;
        }

        try {
            $reconciliation = BankReconciliationModel::find($this->selectedReconciliationId);

            // Load unreconciled GL entries
            $glEntries = $this->service->getUnreconciledGlEntries(
                $reconciliation->bank_account_id,
                $reconciliation->reconciliation_date
            );

            $this->glEntries = $glEntries->map(fn($entry) => [
                'id' => $entry->id,
                'account_name' => $entry->glAccount?->account_name,
                'amount' => $entry->debit + $entry->credit,
                'date' => $entry->entry_date->format('Y-m-d'),
                'reference' => $entry->reference_number,
                'description' => $entry->description,
                'type' => $entry->debit > 0 ? 'debit' : 'credit',
            ])->all();

            // Load unreconciled bank transactions
            $bankTransactions = $this->service->getUnreconciledBankTransactions(
                $reconciliation->bank_account_id,
                $reconciliation->reconciliation_date
            );

            $this->bankTransactions = $bankTransactions->map(fn($transaction) => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'date' => $transaction->transaction_date->format('Y-m-d'),
                'reference' => $transaction->reference_number,
                'description' => $transaction->description,
                'type' => $transaction->transaction_type,
            ])->all();

            // Load matched pairs
            $this->loadMatchedPairs();

            // Update statistics
            $this->updateStats();
        } catch (\Exception $e) {
            $this->addError('form', 'Error loading data: ' . $e->getMessage());
        }
    }

    /**
     * Load matched pairs
     */
    private function loadMatchedPairs()
    {
        if (!$this->selectedReconciliationId) {
            return;
        }

        $reconciliation = BankReconciliationModel::find($this->selectedReconciliationId);
        $this->matchedPairs = $reconciliation->details()
            ->with(['glEntry.glAccount', 'bankTransaction'])
            ->get()
            ->map(fn($detail) => [
                'id' => $detail->id,
                'gl_entry_id' => $detail->gl_entry_id,
                'bank_transaction_id' => $detail->daily_bank_transaction_id,
                'amount' => $detail->matched_amount,
                'gl_account' => $detail->glEntry?->glAccount?->account_name,
                'bank_reference' => $detail->bankTransaction?->reference_number,
                'match_type' => $detail->match_type,
                'matched_at' => $detail->matched_at->format('Y-m-d H:i'),
            ])
            ->all();
    }

    /**
     * Update statistics
     */
    private function updateStats()
    {
        if (!$this->selectedReconciliationId) {
            return;
        }

        $this->stats = $this->service->getReconciliationStats($this->selectedReconciliationId);
        $this->isBalanced = $this->stats['is_balanced'] ?? false;
    }

    /**
     * Match transactions
     */
    #[On('match-transactions')]
    public function matchTransactions(int $glEntryId, int $bankTransactionId)
    {
        if (!$this->selectedReconciliationId) {
            $this->addError('form', 'No active reconciliation');
            return;
        }

        try {
            $this->service->matchTransaction($glEntryId, $bankTransactionId);
            $this->loadReconciliationData();
            $this->dispatch('notify', message: 'Transaction matched successfully');
        } catch (\Exception $e) {
            $this->addError('form', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Unmatch transactions
     */
    public function unmatchTransactions(int $detailId)
    {
        try {
            $this->service->unmatchTransaction($detailId);
            $this->loadReconciliationData();
            $this->dispatch('notify', message: 'Match removed');
        } catch (\Exception $e) {
            $this->addError('form', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Auto-match transactions
     */
    public function performAutoMatch()
    {
        if (!$this->selectedReconciliationId) {
            $this->addError('form', 'No active reconciliation');
            return;
        }

        try {
            $reconciliation = BankReconciliationModel::find($this->selectedReconciliationId);
            $count = $this->service->autoMatchTransactions($reconciliation->bank_account_id);
            $this->autoMatchCount = $count;
            $this->loadReconciliationData();
            $this->dispatch('notify', message: "Auto-matched {$count} transaction(s)");
        } catch (\Exception $e) {
            $this->addError('form', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Complete reconciliation
     */
    public function completeReconciliation()
    {
        if (!$this->selectedReconciliationId) {
            $this->addError('form', 'No active reconciliation');
            return;
        }

        if (!$this->isBalanced) {
            $this->addError('form', 'Reconciliation is not balanced');
            return;
        }

        try {
            $this->service->completeReconciliation($this->selectedReconciliationId);
            $this->activeTab = 'results';
            $this->dispatch('notify', message: 'Reconciliation completed successfully');
        } catch (\Exception $e) {
            $this->addError('form', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Reset reconciliation
     */
    public function resetReconciliation()
    {
        $this->reset([
            'selectedBankAccountId',
            'selectedReconciliationId',
            'reconciliationDate',
            'bankBalance',
            'glEntries',
            'bankTransactions',
            'matchedPairs',
            'stats',
            'isBalanced',
            'activeTab',
        ]);
        $this->activeTab = 'select';
    }
}
