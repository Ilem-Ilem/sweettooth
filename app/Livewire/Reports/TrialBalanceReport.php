<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccountingPeriod;
use App\Services\Reports\TrialBalanceService;


#[Layout('components.layouts.app.branch-dashboard')]
class TrialBalanceReport extends Component
{
    public $selectedPeriodId = null;
    public $reportData = [];
    public $showReport = false;
    public $withHierarchy = false;

    protected $trialBalanceService;

    public function mount()
    {
        $this->trialBalanceService = new TrialBalanceService();
    }

    public function generateReport()
    {
        try {
            $period = $this->selectedPeriodId 
                ? AccountingPeriod::find($this->selectedPeriodId)
                : null;

            if ($this->withHierarchy) {
                $this->reportData = $this->trialBalanceService->generateWithHierarchy($period);
            } else {
                $this->reportData = $this->trialBalanceService->generate($period);
            }

            $this->showReport = true;

            // Validation check
            if (!$this->reportData['summary']['is_balanced']) {
                $this->dispatch('warning', message: 'Trial Balance is NOT balanced! Difference: ' . $this->reportData['summary']['difference']);
            } else {
                $this->dispatch('success', message: 'Trial Balance generated and balanced successfully');
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

        $export = $this->trialBalanceService->export($period);
        
        return response()->json($export);
    }

    public function resetFilters()
    {
        $this->reset(['selectedPeriodId', 'reportData', 'showReport', 'withHierarchy']);
    }

    public function render()
    {
        return view('livewire.reports.trial-balance-report', [
            'periods' => AccountingPeriod::where('status', '!=', 'locked')->orderBy('period_start', 'desc')->get(),
        ]);
    }
}
