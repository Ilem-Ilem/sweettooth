<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\DailyBankTransaction;
use App\Models\DailyBankPosition;
use App\Models\GlEntry;
use App\Models\GlAccount;
use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BankReconciliationService
{
    /**
     * Perform bank reconciliation for a specific account and date range
     */
    public function reconcileBankAccount(
        BankAccount $account,
        Carbon $fromDate,
        Carbon $toDate
    ): array {
        $bankTransactions = $this->getBankTransactions($account, $fromDate, $toDate);
        $glTransactions = $this->getGlTransactions($account, $fromDate, $toDate);

        $matched = $this->matchTransactions($bankTransactions, $glTransactions);
        $unmatched = $this->getUnmatchedTransactions($bankTransactions, $glTransactions, $matched);

        return [
            'bank_account' => $account,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'bank_opening_balance' => $this->getOpeningBalance($account, $fromDate),
            'bank_closing_balance' => $this->getClosingBalance($account, $toDate),
            'gl_opening_balance' => $this->getGlOpeningBalance($account, $fromDate),
            'gl_closing_balance' => $this->getGlClosingBalance($account, $toDate),
            'matched_count' => $matched->count(),
            'unmatched_count' => $unmatched->count(),
            'matched_transactions' => $matched,
            'unmatched_bank_transactions' => $unmatched['bank'],
            'unmatched_gl_transactions' => $unmatched['gl'],
            'reconciliation_status' => $this->determineStatus($matched, $unmatched),
            'variance' => $this->calculateVariance($account, $toDate),
        ];
    }

    /**
     * Get bank transactions for date range
     */
    private function getBankTransactions(BankAccount $account, Carbon $fromDate, Carbon $toDate): Collection
    {
        return DailyBankTransaction::where('bank_account_id', $account->id)
            ->whereBetween('position_date', [$fromDate, $toDate])
            ->orderBy('position_date')
            ->get();
    }

    /**
     * Get GL transactions for the bank account
     */
    private function getGlTransactions(BankAccount $account, Carbon $fromDate, Carbon $toDate): Collection
    {
        return GlEntry::where('gl_account_id', $account->gl_account_id)
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->where('status', 'posted')
            ->orderBy('entry_date')
            ->get();
    }

    /**
     * Match bank and GL transactions
     */
    private function matchTransactions(Collection $bankTransactions, Collection $glTransactions): Collection
    {
        $matched = collect();

        foreach ($bankTransactions as $bankTx) {
            // Try to find matching GL transaction
            $glTx = $glTransactions->first(function ($gl) use ($bankTx) {
                return $this->transactionsMatch($bankTx, $gl);
            });

            if ($glTx) {
                $matched->push([
                    'bank_transaction' => $bankTx,
                    'gl_entry' => $glTx,
                    'bank_amount' => $bankTx->amount,
                    'gl_amount' => $bankTx->transaction_type === 'inflow' ? $glTx->debit : $glTx->credit,
                    'matched_date' => now(),
                ]);

                // Remove from GL collection to avoid double matching
                $glTransactions = $glTransactions->reject(fn($g) => $g->id === $glTx->id);
            }
        }

        return $matched;
    }

    /**
     * Check if bank and GL transactions match
     */
    private function transactionsMatch($bankTx, $glTx): bool
    {
        // Match by amount and approximate date (within 5 days)
        $amount = floatval($bankTx->amount);
        $glAmount = floatval($bankTx->transaction_type === 'inflow' ? $glTx->debit : $glTx->credit);
        $dateDiff = $glTx->entry_date->diffInDays($bankTx->position_date);

        return abs($amount - $glAmount) < 0.01 && $dateDiff <= 5;
    }

    /**
     * Get unmatched transactions
     */
    private function getUnmatchedTransactions(
        Collection $bankTransactions,
        Collection $glTransactions,
        Collection $matched
    ): array {
        $matchedBankIds = $matched->pluck('bank_transaction.id');
        $matchedGlIds = $matched->pluck('gl_entry.id');

        return [
            'bank' => $bankTransactions->reject(fn($tx) => $matchedBankIds->contains($tx->id))->values(),
            'gl' => $glTransactions->reject(fn($tx) => $matchedGlIds->contains($tx->id))->values(),
        ];
    }

    /**
     * Get opening balance
     */
    private function getOpeningBalance(BankAccount $account, Carbon $date): float
    {
        $position = DailyBankPosition::where('bank_account_id', $account->id)
            ->where('position_date', '<', $date)
            ->orderBy('position_date', 'desc')
            ->first();

        return $position ? floatval($position->available_balance) : floatval($account->opening_balance);
    }

    /**
     * Get closing balance
     */
    private function getClosingBalance(BankAccount $account, Carbon $date): float
    {
        $position = DailyBankPosition::where('bank_account_id', $account->id)
            ->where('position_date', '<=', $date)
            ->orderBy('position_date', 'desc')
            ->first();

        return $position ? floatval($position->available_balance) : floatval($account->opening_balance);
    }

    /**
     * Get GL opening balance
     */
    private function getGlOpeningBalance(BankAccount $account, Carbon $date): float
    {
        $account = $account->glAccount;
        return floatval($account->debit_balance) - floatval($account->credit_balance);
    }

    /**
     * Get GL closing balance
     */
    private function getGlClosingBalance(BankAccount $account, Carbon $date): float
    {
        $entries = GlEntry::where('gl_account_id', $account->gl_account_id)
            ->where('entry_date', '<=', $date)
            ->where('status', 'posted')
            ->get();

        $debit = floatval($entries->sum('debit'));
        $credit = floatval($entries->sum('credit'));

        return $debit - $credit;
    }

    /**
     * Calculate variance between bank and GL
     */
    private function calculateVariance(BankAccount $account, Carbon $date): float
    {
        $bankBalance = $this->getClosingBalance($account, $date);
        $glBalance = $this->getGlClosingBalance($account, $date);

        return floatval($bankBalance) - floatval($glBalance);
    }

    /**
     * Determine reconciliation status
     */
    private function determineStatus(Collection $matched, array $unmatched): string
    {
        if ($unmatched['bank']->isEmpty() && $unmatched['gl']->isEmpty()) {
            return 'reconciled';
        } elseif ($unmatched['bank']->isEmpty() || $unmatched['gl']->isEmpty()) {
            return 'partially_reconciled';
        } else {
            return 'unreconciled';
        }
    }

    /**
     * Get AR aging report
     */
    public function getArAgingReport(AccountingPeriod $period): array
    {
        $today = now();
        $arAccount = GlAccount::where('account_number', '1200')->first();

        if (!$arAccount) {
            return [];
        }

        $entries = GlEntry::where('gl_account_id', $arAccount->id)
            ->where('accounting_period_id', '<=', $period->id)
            ->where('status', 'posted')
            ->get();

        $currentBalance = floatval($entries->sum('debit')) - floatval($entries->sum('credit'));

        return [
            'period' => $period->getDisplayName(),
            'as_of_date' => $today->toDateString(),
            'total_ar' => $currentBalance,
            'aging_buckets' => [
                'current' => $this->calculateAging($entries, 0, 30),
                '31_60' => $this->calculateAging($entries, 31, 60),
                '61_90' => $this->calculateAging($entries, 61, 90),
                '91_plus' => $this->calculateAging($entries, 91, 999),
            ],
            'percentage_current' => $currentBalance > 0 
                ? ($this->calculateAging($entries, 0, 30) / $currentBalance) * 100 
                : 0,
        ];
    }

    /**
     * Calculate aging bucket
     */
    private function calculateAging(Collection $entries, int $daysMin, int $daysMax): float
    {
        $today = now();
        $total = 0;

        foreach ($entries as $entry) {
            $daysDiff = $entry->entry_date->diffInDays($today);

            if ($daysDiff >= $daysMin && $daysDiff <= $daysMax) {
                $debit = floatval($entry->debit);
                $credit = floatval($entry->credit);
                $total += $debit - $credit;
            }
        }

        return $total;
    }

    /**
     * Get inventory reconciliation report
     */
    public function getInventoryReconciliation(AccountingPeriod $period): array
    {
        $fgAccount = GlAccount::where('account_number', '1330')->first();
        
        if (!$fgAccount) {
            return [];
        }

        $entries = GlEntry::where('gl_account_id', $fgAccount->id)
            ->where('accounting_period_id', $period->id)
            ->where('status', 'posted')
            ->get();

        $glBalance = floatval($entries->sum('debit')) - floatval($entries->sum('credit'));

        // This would be compared against physical inventory count
        return [
            'period' => $period->getDisplayName(),
            'account' => '1330 - Finished Goods Inventory',
            'gl_balance' => $glBalance,
            'physical_count' => 0, // Would be populated from inventory module
            'variance' => 0, // Would be calculated
            'variance_percentage' => 0,
        ];
    }

    /**
     * Mark transactions as reconciled
     */
    public function markTransactionsReconciled(array $transactionIds): bool
    {
        try {
            DailyBankTransaction::whereIn('id', $transactionIds)
                ->update(['reconciled' => true, 'reconciled_at' => now()]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to mark transactions reconciled: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get reconciliation discrepancies
     */
    public function getDiscrepancies(BankAccount $account, Carbon $date): Collection
    {
        $discrepancies = collect();

        // Check for duplicate transactions
        $transactions = DailyBankTransaction::where('bank_account_id', $account->id)
            ->where('position_date', '<=', $date)
            ->get();

        $grouped = $transactions->groupBy(function ($item) {
            return $item->amount . '|' . $item->position_date->toDateString();
        });

        foreach ($grouped as $key => $group) {
            if ($group->count() > 1) {
                $discrepancies->push([
                    'type' => 'duplicate',
                    'count' => $group->count(),
                    'amount' => $group->first()->amount,
                    'date' => $group->first()->position_date,
                    'transactions' => $group,
                ]);
            }
        }

        return $discrepancies;
    }

    /**
     * Generate reconciliation summary
     */
    public function getReconciliationSummary(BankAccount $account, Carbon $startDate, Carbon $endDate): array
    {
        $result = $this->reconcileBankAccount($account, $startDate, $endDate);

        return [
            'bank_account_number' => $account->account_number,
            'bank_account_name' => $account->bank_name,
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'bank_opening_balance' => $result['bank_opening_balance'],
            'bank_deposits' => $this->calculateDeposits($result['matched_transactions']),
            'bank_withdrawals' => $this->calculateWithdrawals($result['matched_transactions']),
            'bank_closing_balance' => $result['bank_closing_balance'],
            'gl_opening_balance' => $result['gl_opening_balance'],
            'gl_deposits' => $this->calculateGlDeposits($result['matched_transactions']),
            'gl_withdrawals' => $this->calculateGlWithdrawals($result['matched_transactions']),
            'gl_closing_balance' => $result['gl_closing_balance'],
            'variance' => $result['variance'],
            'unmatched_items' => $result['unmatched_count'],
            'status' => $result['reconciliation_status'],
        ];
    }

    /**
     * Calculate total deposits
     */
    private function calculateDeposits(Collection $transactions): float
    {
        return floatval($transactions
            ->filter(fn($t) => $t['bank_transaction']->transaction_type === 'inflow')
            ->sum('bank_amount'));
    }

    /**
     * Calculate total withdrawals
     */
    private function calculateWithdrawals(Collection $transactions): float
    {
        return floatval($transactions
            ->filter(fn($t) => $t['bank_transaction']->transaction_type === 'outflow')
            ->sum('bank_amount'));
    }

    /**
     * Calculate GL deposits
     */
    private function calculateGlDeposits(Collection $transactions): float
    {
        return floatval($transactions
            ->filter(fn($t) => $t['bank_transaction']->transaction_type === 'inflow')
            ->sum('gl_amount'));
    }

    /**
     * Calculate GL withdrawals
     */
    private function calculateGlWithdrawals(Collection $transactions): float
    {
        return floatval($transactions
            ->filter(fn($t) => $t['bank_transaction']->transaction_type === 'outflow')
            ->sum('gl_amount'));
    }
}
