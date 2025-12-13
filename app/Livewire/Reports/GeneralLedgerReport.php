<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\GlAccount;
use App\Models\AccountingPeriod;
use App\Services\Reports\GeneralLedgerService;
use Carbon\Carbon;


#[Layout('components.layouts.app.branch-dashboard')]
class GeneralLedgerReport extends Component
{
    use WithPagination;

    public $selectedAccountId = null;
    public $selectedPeriodId = null;
    public $startDate = null;
    public $endDate = null;
    public $reportData = [];
    public $showReport = false;

    protected $glService;

    public function mount()
    {
        $this->glService = new GeneralLedgerService();
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function generateReport()
    {
        try {
            $period = $this->selectedPeriodId 
                ? AccountingPeriod::find($this->selectedPeriodId)
                : null;

            $startDate = $this->startDate ? Carbon::parse($this->startDate) : null;
            $endDate = $this->endDate ? Carbon::parse($this->endDate) : null;

            $this->reportData = $this->glService->getFullLedger($period, $startDate, $endDate);
            $this->showReport = true;

            $this->dispatch('success', message: 'General Ledger report generated successfully');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to generate report: ' . $e->getMessage());
        }
    }

    public function exportToCsv()
    {
        $period = $this->selectedPeriodId 
            ? AccountingPeriod::find($this->selectedPeriodId)
            : null;

        $startDate = $this->startDate ? Carbon::parse($this->startDate) : null;
        $endDate = $this->endDate ? Carbon::parse($this->endDate) : null;

        $export = $this->glService->export($period, $startDate, $endDate);
        
        return response()->json($export);
    }

    public function resetFilters()
    {
        $this->reset(['selectedAccountId', 'selectedPeriodId', 'startDate', 'endDate', 'reportData', 'showReport']);
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.general-ledger-report', [
            'accounts' => GlAccount::where('is_active', true)->orderBy('account_number')->get(),
            'periods' => AccountingPeriod::where('status', '!=', 'locked')->orderBy('period_start', 'desc')->get(),
        ]);
    }
}
