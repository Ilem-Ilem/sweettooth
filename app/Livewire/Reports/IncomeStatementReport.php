<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccountingPeriod;
use App\Services\Reports\IncomeStatementService;
use Carbon\Carbon;


#[Layout('components.layouts.app.branch-dashboard')]
class IncomeStatementReport extends Component
{
    public $selectedPeriodId = null;
    public $startDate = null;
    public $endDate = null;
    public $reportData = [];
    public $showReport = false;
    public $compareWithPrevious = false;
    public $comparisonData = [];

    protected $incomeStatementService;

    public function mount()
    {
        $this->incomeStatementService = new IncomeStatementService();
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

            $this->reportData = $this->incomeStatementService->generate($period, $startDate, $endDate);

            if ($this->compareWithPrevious && $period && $period->previousPeriod) {
                $this->comparisonData = $this->incomeStatementService->compareWithPrevious($period);
            }

            $this->showReport = true;
            $this->dispatch('success', message: 'Income Statement generated successfully');
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

        $export = $this->incomeStatementService->export($period, $startDate, $endDate);
        
        return response()->json($export);
    }

    public function resetFilters()
    {
        $this->reset(['selectedPeriodId', 'startDate', 'endDate', 'reportData', 'showReport', 'compareWithPrevious', 'comparisonData']);
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        return view('livewire.reports.income-statement-report', [
            'periods' => AccountingPeriod::where('status', '!=', 'locked')->orderBy('period_start', 'desc')->get(),
        ]);
    }
}
