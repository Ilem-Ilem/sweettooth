<?php

namespace App\Services;

use App\Models\GlEntry;
use App\Models\GlAccount;
use App\Models\AccountingPeriod;
use Illuminate\Support\Collection;

class TrialBalanceService
{
    protected GeneralLedgerService $glService;

    public function __construct(GeneralLedgerService $glService)
    {
        $this->glService = $glService;
    }

    /**
     * Get trial balance for a period
     */
    public function getTrialBalance(?int $periodId = null): array
    {
        $accounts = GlAccount::where('is_active', true)
            ->where('account_type', '!=', 'header')
            ->orderBy('account_number')
            ->get();

        $balances = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $query = GlEntry::where('gl_account_id', $account->id)
                ->where('status', 'posted');

            if ($periodId) {
                $query->where('accounting_period_id', $periodId);
            }

            $debits = (float) $query->sum('debit');
            $credits = (float) $query->sum('credit');

            // Only include accounts with activity
            if ($debits > 0 || $credits > 0) {
                $balances[] = [
                    'account_number' => $account->account_number,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'account_id' => $account->id,
                    'debit' => $debits,
                    'credit' => $credits,
                ];

                $totalDebits += $debits;
                $totalCredits += $credits;
            }
        }

        return [
            'accounts' => $balances,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'balanced' => abs($totalDebits - $totalCredits) < 0.01,
            'difference' => $totalDebits - $totalCredits,
            'period_id' => $periodId,
        ];
    }

    /**
     * Check if GL is balanced
     */
    public function isBalanced(?int $periodId = null): bool
    {
        $query = GlEntry::where('status', 'posted');

        if ($periodId) {
            $query->where('accounting_period_id', $periodId);
        }

        $totalDebits = (float) $query->sum('debit');
        $totalCredits = (float) $query->sum('credit');

        return abs($totalDebits - $totalCredits) < 0.01;
    }

    /**
     * Get trial balance for comparison
     */
    public function getComparativeTrialBalance(
        ?int $periodId1 = null,
        ?int $periodId2 = null,
    ): array {
        $tb1 = $this->getTrialBalance($periodId1);
        $tb2 = $this->getTrialBalance($periodId2);

        $accounts = [];
        $allAccounts = array_merge(
            array_column($tb1['accounts'], null, 'account_id'),
            array_column($tb2['accounts'], null, 'account_id'),
        );

        foreach ($allAccounts as $accountId => $account) {
            $debit1 = 0;
            $credit1 = 0;
            $debit2 = 0;
            $credit2 = 0;

            if (isset($tb1['accounts'])) {
                $found = array_search($accountId, array_column($tb1['accounts'], 'account_id'));
                if ($found !== false) {
                    $debit1 = $tb1['accounts'][$found]['debit'];
                    $credit1 = $tb1['accounts'][$found]['credit'];
                }
            }

            if (isset($tb2['accounts'])) {
                $found = array_search($accountId, array_column($tb2['accounts'], 'account_id'));
                if ($found !== false) {
                    $debit2 = $tb2['accounts'][$found]['debit'];
                    $credit2 = $tb2['accounts'][$found]['credit'];
                }
            }

            $accounts[] = [
                'account_id' => $accountId,
                'account_number' => $account['account_number'] ?? '',
                'account_name' => $account['account_name'] ?? '',
                'debit1' => $debit1,
                'credit1' => $credit1,
                'debit2' => $debit2,
                'credit2' => $credit2,
                'debit_change' => $debit2 - $debit1,
                'credit_change' => $credit2 - $credit1,
            ];
        }

        return [
            'accounts' => $accounts,
            'period1_id' => $periodId1,
            'period2_id' => $periodId2,
            'period1_totals' => $tb1,
            'period2_totals' => $tb2,
        ];
    }

    /**
     * Get balancing report
     */
    public function getBalancingReport(?int $periodId = null): array
    {
        $query = GlEntry::where('status', 'posted');

        if ($periodId) {
            $query->where('accounting_period_id', $periodId);
        }

        $totalDebits = (float) $query->sum('debit');
        $totalCredits = (float) $query->sum('credit');
        $difference = abs($totalDebits - $totalCredits);

        return [
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $difference,
            'is_balanced' => $difference < 0.01,
            'entry_count' => $query->count(),
            'accounts_with_entries' => GlEntry::where('status', 'posted')
                ->when($periodId, fn($q) => $q->where('accounting_period_id', $periodId))
                ->distinct('gl_account_id')
                ->count(),
        ];
    }

    /**
     * Export trial balance to array
     */
    public function exportTrialBalance(?int $periodId = null): array
    {
        $tb = $this->getTrialBalance($periodId);

        $data = [];
        foreach ($tb['accounts'] as $account) {
            $data[] = [
                'Account Number' => $account['account_number'],
                'Account Name' => $account['account_name'],
                'Debit' => $account['debit'] > 0 ? number_format($account['debit'], 2) : '',
                'Credit' => $account['credit'] > 0 ? number_format($account['credit'], 2) : '',
            ];
        }

        $data[] = [
            'Account Number' => 'TOTAL',
            'Account Name' => '',
            'Debit' => number_format($tb['total_debits'], 2),
            'Credit' => number_format($tb['total_credits'], 2),
        ];

        return $data;
    }
}
