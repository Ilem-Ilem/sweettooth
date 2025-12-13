<?php

namespace App\Services;

use App\Models\GlEntry;
use App\Models\GlAccount;
use App\Models\DailyBankPosition;
use App\Models\CashPosition;
use Carbon\Carbon;

class CashFlowStatementService
{
    /**
     * Get cash flow statement
     */
    public function getCashFlowStatement(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        // Operating Activities
        $operatingCash = $this->getOperatingActivities($startDate, $endDate);

        // Investing Activities
        $investingCash = $this->getInvestingActivities($startDate, $endDate);

        // Financing Activities
        $financingCash = $this->getFinancingActivities($startDate, $endDate);

        // Net Change in Cash
        $netChange = $operatingCash['total'] + $investingCash['total'] + $financingCash['total'];

        // Opening and Closing Cash
        $openingCash = $this->getOpeningCash($startDate);
        $closingCash = $openingCash + $netChange;

        return [
            'operating' => $operatingCash,
            'investing' => $investingCash,
            'financing' => $financingCash,
            'net_change' => $netChange,
            'opening_cash' => $openingCash,
            'closing_cash' => $closingCash,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    /**
     * Get operating activities cash flow
     */
    protected function getOperatingActivities(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        // Net Income (from Income Statement)
        $incomeService = new IncomeStatementService();
        $is = $incomeService->getIncomeStatement();
        $netIncome = $is['net_income'];

        // Changes in Working Capital
        // Accounts Receivable (1100)
        $arChange = $this->getAccountChangeInPeriod(1100, $startDate, $endDate);

        // Inventory (1200-1220)
        $invChange = $this->getAccountChangeInPeriod(1200, $startDate, $endDate)
                  + $this->getAccountChangeInPeriod(1210, $startDate, $endDate)
                  + $this->getAccountChangeInPeriod(1220, $startDate, $endDate);

        // Accounts Payable (2010)
        $apChange = $this->getAccountChangeInPeriod(2010, $startDate, $endDate);

        // Operating Cash Flow
        $operatingCash = $netIncome - $arChange - $invChange + $apChange;

        return [
            'net_income' => $netIncome,
            'ar_change' => -$arChange,
            'inventory_change' => -$invChange,
            'ap_change' => $apChange,
            'other_adjustments' => 0,
            'total' => $operatingCash,
        ];
    }

    /**
     * Get investing activities cash flow
     */
    protected function getInvestingActivities(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        // Fixed Assets changes (1300-1399)
        $fixedAssetChange = 0;
        for ($i = 1300; $i <= 1399; $i++) {
            $fixedAssetChange += $this->getAccountChangeInPeriod($i, $startDate, $endDate);
        }

        // Typically negative (cash outflow for purchases)
        $investingCash = -abs($fixedAssetChange);

        return [
            'fixed_assets_purchases' => $investingCash,
            'asset_sales' => 0,
            'other_investing' => 0,
            'total' => $investingCash,
        ];
    }

    /**
     * Get financing activities cash flow
     */
    protected function getFinancingActivities(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        // Debt changes (2100-2200)
        $debtChange = 0;
        for ($i = 2100; $i <= 2200; $i++) {
            $debtChange += $this->getAccountChangeInPeriod($i, $startDate, $endDate);
        }

        // Equity changes (3000-3030)
        $equityChange = 0;
        for ($i = 3000; $i <= 3030; $i++) {
            $equityChange += $this->getAccountChangeInPeriod($i, $startDate, $endDate);
        }

        $financingCash = $debtChange + $equityChange;

        return [
            'debt_issuance' => $debtChange > 0 ? $debtChange : 0,
            'debt_repayment' => $debtChange < 0 ? abs($debtChange) : 0,
            'equity_issuance' => $equityChange > 0 ? $equityChange : 0,
            'dividends' => $equityChange < 0 ? abs($equityChange) : 0,
            'total' => $financingCash,
        ];
    }

    /**
     * Get account change in period
     */
    protected function getAccountChangeInPeriod(
        int $accountNumber,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): float {
        $account = GlAccount::where('account_number', (string)$accountNumber)->first();
        if (!$account) {
            return 0;
        }

        $query = GlEntry::where('gl_account_id', $account->id)
            ->where('status', 'posted');

        if ($startDate) {
            $query->where('entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('entry_date', '<=', $endDate);
        }

        $debits = (float) $query->sum('debit');
        $credits = (float) $query->sum('credit');

        return $account->normal_balance === 'debit' ? $debits - $credits : $credits - $debits;
    }

    /**
     * Get opening cash balance
     */
    protected function getOpeningCash(?Carbon $date = null): float
    {
        if (!$date) {
            $date = now()->startOfMonth();
        }

        // Sum of cash accounts (1010, 1020, 1030, 1040, 1050-1070)
        $cashAccounts = [1010, 1020, 1030, 1040, 1050, 1060, 1070];
        $totalCash = 0;

        foreach ($cashAccounts as $accountNum) {
            $account = GlAccount::where('account_number', (string)$accountNum)->first();
            if ($account) {
                $balance = GlEntry::where('gl_account_id', $account->id)
                    ->where('status', 'posted')
                    ->where('entry_date', '<', $date)
                    ->sum('debit') - GlEntry::where('gl_account_id', $account->id)
                    ->where('status', 'posted')
                    ->where('entry_date', '<', $date)
                    ->sum('credit');
                $totalCash += $balance;
            }
        }

        return $totalCash;
    }

    /**
     * Get daily bank positions summary
     */
    public function getBankPositionsSummary(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        $query = DailyBankPosition::query();

        if ($startDate) {
            $query->where('position_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('position_date', '<=', $endDate);
        }

        $positions = $query->get();

        return [
            'count' => $positions->count(),
            'total_inflows' => $positions->sum('inflows_total'),
            'total_outflows' => $positions->sum('outflows_total'),
            'average_balance' => $positions->avg('available_balance'),
            'total_variance' => $positions->sum('variance_amount'),
            'reconciled_count' => $positions->where('reconciled', true)->count(),
        ];
    }

    /**
     * Get daily cash positions summary
     */
    public function getCashPositionsSummary(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        $query = CashPosition::query();

        if ($startDate) {
            $query->where('position_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('position_date', '<=', $endDate);
        }

        $positions = $query->get();

        return [
            'count' => $positions->count(),
            'total_receipts' => $positions->sum('sales_receipts'),
            'total_withdrawals' => $positions->sum('withdrawals'),
            'total_variance' => $positions->sum('variance_amount'),
            'average_balance' => $positions->avg('closing_balance'),
        ];
    }

    /**
     * Export cash flow statement
     */
    public function exportCashFlowStatement(
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
    ): array {
        $cfs = $this->getCashFlowStatement($startDate, $endDate);

        return [
            ['CASH FLOW STATEMENT', '', ''],
            ['', '', ''],
            ['OPERATING ACTIVITIES', '', ''],
            ['  Net Income', '', number_format($cfs['operating']['net_income'], 2)],
            ['  Accounts Receivable Change', '', number_format($cfs['operating']['ar_change'], 2)],
            ['  Inventory Change', '', number_format($cfs['operating']['inventory_change'], 2)],
            ['  Accounts Payable Change', '', number_format($cfs['operating']['ap_change'], 2)],
            ['Net Cash from Operating Activities', '', number_format($cfs['operating']['total'], 2)],
            ['', '', ''],
            ['INVESTING ACTIVITIES', '', ''],
            ['  Fixed Assets Purchases', '', number_format($cfs['investing']['fixed_assets_purchases'], 2)],
            ['Net Cash from Investing Activities', '', number_format($cfs['investing']['total'], 2)],
            ['', '', ''],
            ['FINANCING ACTIVITIES', '', ''],
            ['  Debt Issuance', '', number_format($cfs['financing']['debt_issuance'], 2)],
            ['  Debt Repayment', '', number_format($cfs['financing']['debt_repayment'], 2)],
            ['  Equity Issuance', '', number_format($cfs['financing']['equity_issuance'], 2)],
            ['  Dividends', '', number_format($cfs['financing']['dividends'], 2)],
            ['Net Cash from Financing Activities', '', number_format($cfs['financing']['total'], 2)],
            ['', '', ''],
            ['Net Change in Cash', '', number_format($cfs['net_change'], 2)],
            ['Cash at Beginning of Period', '', number_format($cfs['opening_cash'], 2)],
            ['Cash at End of Period', '', number_format($cfs['closing_cash'], 2)],
        ];
    }
}
