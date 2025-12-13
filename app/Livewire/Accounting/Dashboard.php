<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\AccountingPeriod;
use App\Services\Reports\TrialBalanceService;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class Dashboard extends Component
{
    public $currentPeriod = null;
    public $dashboardStats = [];
    public $recentEntries = [];
    public $unbalancedAccounts = [];
    public $periodStatus = null;

    protected $trialBalanceService;

    public function mount()
    {
        $this->trialBalanceService = new TrialBalanceService();
        $this->loadCurrentPeriod();
        $this->loadDashboardStats();
        $this->loadRecentEntries();
        $this->checkBalances();
    }

    protected function loadCurrentPeriod()
    {
        $this->currentPeriod = AccountingPeriod::current()->first();
        
        if ($this->currentPeriod) {
            $this->periodStatus = [
                'name' => $this->currentPeriod->getDisplayName(),
                'status' => ucfirst($this->currentPeriod->status),
                'start' => $this->currentPeriod->period_start->format('M d, Y'),
                'end' => $this->currentPeriod->period_end->format('M d, Y'),
            ];
        }
    }

    protected function loadDashboardStats()
    {
        if (!$this->currentPeriod) {
            $this->dashboardStats = $this->getEmptyStats();
            return;
        }

        // Get account balances
        $assets = GlEntry::where('status', 'posted')
            ->where('accounting_period_id', $this->currentPeriod->id)
            ->whereHas('glAccount', fn($q) => $q->where('account_type', 'asset'))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $liabilities = GlEntry::where('status', 'posted')
            ->where('accounting_period_id', $this->currentPeriod->id)
            ->whereHas('glAccount', fn($q) => $q->where('account_type', 'liability'))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $equity = GlEntry::where('status', 'posted')
            ->where('accounting_period_id', $this->currentPeriod->id)
            ->whereHas('glAccount', fn($q) => $q->where('account_type', 'equity'))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $revenue = GlEntry::where('status', 'posted')
            ->where('accounting_period_id', $this->currentPeriod->id)
            ->whereHas('glAccount', fn($q) => $q->where('account_type', 'revenue'))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $expenses = GlEntry::where('status', 'posted')
            ->where('accounting_period_id', $this->currentPeriod->id)
            ->whereHas('glAccount', fn($q) => $q->where('account_type', 'expense'))
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $totalAssets = abs(($assets->total_debit ?? 0) - ($assets->total_credit ?? 0));
        $totalLiabilities = abs(($liabilities->total_credit ?? 0) - ($liabilities->total_debit ?? 0));
        $totalEquity = abs(($equity->total_credit ?? 0) - ($equity->total_debit ?? 0));
        $totalRevenue = abs(($revenue->total_credit ?? 0) - ($revenue->total_debit ?? 0));
        $totalExpenses = abs(($expenses->total_debit ?? 0) - ($expenses->total_credit ?? 0));

        $this->dashboardStats = [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenue - $totalExpenses,
            'total_entries' => GlEntry::where('accounting_period_id', $this->currentPeriod->id)->where('status', 'posted')->count(),
        ];
    }

    protected function loadRecentEntries()
    {
        $this->recentEntries = GlEntry::where('status', 'posted')
            ->with('glAccount')
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'date' => $entry->entry_date->format('M d, Y'),
                    'account' => $entry->glAccount->account_number . ' - ' . $entry->glAccount->account_name,
                    'description' => substr($entry->description, 0, 50),
                    'debit' => $entry->debit,
                    'credit' => $entry->credit,
                    'reference' => $entry->reference_number,
                ];
            })
            ->toArray();
    }

    protected function checkBalances()
    {
        if (!$this->currentPeriod) {
            return;
        }

        $tb = $this->trialBalanceService->generate($this->currentPeriod);
        
        if (!$tb['summary']['is_balanced']) {
            $this->unbalancedAccounts = [
                'is_balanced' => false,
                'difference' => $tb['summary']['difference'],
                'message' => 'Trial Balance is NOT balanced',
            ];
        } else {
            $this->unbalancedAccounts = ['is_balanced' => true];
        }
    }

    protected function getEmptyStats()
    {
        return [
            'total_assets' => 0,
            'total_liabilities' => 0,
            'total_equity' => 0,
            'total_revenue' => 0,
            'total_expenses' => 0,
            'net_income' => 0,
            'total_entries' => 0,
        ];
    }

    public function render()
    {
        return view('livewire.accounting.dashboard');
    }
}
