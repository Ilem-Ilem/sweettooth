<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccountingPeriod;
use App\Services\Reports\BalanceSheetService;
use Carbon\Carbon;


#[Layout('components.layouts.app.branch-dashboard')]
class BalanceSheetReport extends Component
{
    public $selectedPeriodId = null;
    public $asOfDate = null;
    public $reportData = [];
    public $showReport = false;
    public $compareWithPrevious = false;
    public $comparisonData = [];

    protected $balanceSheetService;

    public function mount()
    {
        $this->balanceSheetService = new BalanceSheetService();
        $this->asOfDate = now()->toDateString();
    }

    public function generateReport()
    {
        try {
            $period = $this->selectedPeriodId 
                ? AccountingPeriod::find($this->selectedPeriodId)
                : null;

            $asOfDate = $this->asOfDate ? Carbon::parse($this->asOfDate) : null;

            $this->reportData = $this->balanceSheetService->generate($period, $asOfDate);

            if ($this->compareWithPrevious && $period && $period->previousPeriod) {
                $this->comparisonData = $this->balanceSheetService->compareWithPrevious($period);
            }

            $this->showReport = true;

            // Validation check
            if (!$this->reportData['balance_check']['is_balanced']) {
                $this->dispatch('warning', message: 'Balance Sheet is NOT balanced! Difference: ' . $this->reportData['balance_check']['difference']);
            } else {
                $this->dispatch('success', message: 'Balance Sheet generated and balanced successfully');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to generate report: ' . $e->getMessage());
        }
    }

    public function exportToCsv()
    {
        $period = $this->selectedPeriodId 
            ? AccountingPeriod::find($this->selectedPeriodId)
            : null;

        $asOfDate = $this->asOfDate ? Carbon::parse($this->asOfDate) : null;

        $export = $this->balanceSheetService->export($period, $asOfDate);
        
        return response()->json($export);
    }

    public function resetFilters()
    {
        $this->reset(['selectedPeriodId', 'asOfDate', 'reportData', 'showReport', 'compareWithPrevious', 'comparisonData']);
        $this->asOfDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.balance-sheet-report', [
            'periods' => AccountingPeriod::where('status', '!=', 'locked')->orderBy('period_start', 'desc')->get(),
        ]);
    }
}
