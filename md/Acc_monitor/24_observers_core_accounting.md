# Observers for Core Accounting Integration

This guide explains how Laravel observers will be used to automate core accounting integrations, following the existing pattern of GL posting and status updates.

## Observer Pattern Overview

Based on existing observers (SaleObserver, PurchaseObserver, StockMovementObserver), observers will handle automatic updates when models are created/updated. They use services for complex logic and handle errors gracefully.

## Core Accounting Observers

### 1. TransactionObserver

Handles bank account balance updates and GL posting for receipts/payments.

```php
class TransactionObserver
{
    protected BankAccountService $bankService;
    protected GlPostingService $glPostingService;

    public function __construct(BankAccountService $bankService, GlPostingService $glPostingService)
    {
        $this->bankService = $bankService;
        $this->glPostingService = $glPostingService;
    }

    public function created(Transaction $transaction): void
    {
        $this->updateBankBalance($transaction);
        $this->postToGL($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        if ($transaction->wasChanged(['amount', 'type'])) {
            $this->updateBankBalance($transaction);
            $this->postToGL($transaction);
        }
    }

    private function updateBankBalance(Transaction $transaction): void
    {
        $this->bankService->updateBalance($transaction->bank_account_id);
    }

    private function postToGL(Transaction $transaction): void
    {
        try {
            if ($transaction->gl_posting_status !== 'pending') return;
            
            $this->glPostingService->postBankTransaction($transaction);
            
            $transaction->update([
                'gl_posting_status' => 'posted',
                'gl_posted_at' => now(),
            ]);
        } catch (Exception $e) {
            $transaction->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);
            \Log::error('Failed to post transaction to GL', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
```

### 2. AccountTransferObserver

Handles inter-account transfers and balance updates.

```php
class AccountTransferObserver
{
    protected BankAccountService $bankService;

    public function created(AccountTransfer $transfer): void
    {
        $this->updateBalances($transfer);
        $this->createJournalEntries($transfer);
    }

    private function updateBalances(AccountTransfer $transfer): void
    {
        $this->bankService->updateBalance($transfer->from_account_id);
        $this->bankService->updateBalance($transfer->to_account_id);
    }

    private function createJournalEntries(AccountTransfer $transfer): void
    {
        // Create automatic journal entries for transfers
    }
}
```

### 3. ExpenseClaimObserver

Automates expense claim processing and GL posting.

```php
class ExpenseClaimObserver
{
    public function updated(ExpenseClaim $claim): void
    {
        if ($claim->wasChanged('status') && $claim->status === 'approved') {
            $this->createPaymentRequest($claim);
        }
    }

    private function createPaymentRequest(ExpenseClaim $claim): void
    {
        // Create payment transaction or journal entry
    }
}
```

### 4. JournalEntryObserver

Handles automatic balancing and validation.

```php
class JournalEntryObserver
{
    public function saving(JournalEntry $entry): void
    {
        $this->validateBalance($entry);
    }

    public function created(JournalEntry $entry): void
    {
        $this->updateAccountBalances($entry);
    }

    private function validateBalance(JournalEntry $entry): void
    {
        $debitTotal = $entry->lines->sum('debit');
        $creditTotal = $entry->lines->sum('credit');
        
        if ($debitTotal !== $creditTotal) {
            throw new Exception('Journal entry is not balanced');
        }
    }

    private function updateAccountBalances(JournalEntry $entry): void
    {
        foreach ($entry->lines as $line) {
            // Update account balances
        }
    }
}
```

### 5. BankReconciliationObserver

Automates reconciliation adjustments.

```php
class BankReconciliationObserver
{
    public function created(BankReconciliation $reconciliation): void
    {
        $this->createAdjustmentEntries($reconciliation);
    }

    private function createAdjustmentEntries(BankReconciliation $reconciliation): void
    {
        $difference = $reconciliation->statement_balance - $reconciliation->book_balance;
        if ($difference !== 0) {
            // Create adjustment journal entry
        }
    }
}
```

## Registration

Register observers in `AppServiceProvider`:

```php
public function boot()
{
    Transaction::observe(TransactionObserver::class);
    AccountTransfer::observe(AccountTransferObserver::class);
    ExpenseClaim::observe(ExpenseClaimObserver::class);
    JournalEntry::observe(JournalEntryObserver::class);
    BankReconciliation::observe(BankReconciliationObserver::class);
}
```

## Benefits

- Automatic balance updates prevent manual errors
- Consistent GL posting across all transactions
- Real-time financial reporting accuracy
- Error handling without breaking user workflows

This follows the existing observer pattern for seamless integration.