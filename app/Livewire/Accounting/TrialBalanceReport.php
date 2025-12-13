<?php

namespace App\Livewire\Accounting;

use App\Services\TrialBalanceService;
use Livewire\Attributes\Layout;
use App\Models\AccountingPeriod;
use Livewire\Component;


#[Layout('components.layouts.app.branch-dashboard')]
class TrialBalanceReport extends Component
{
    protected TrialBalanceService $tbService;

    public ?int $periodId = null;
    public ?int $comparePeriodId = null;
    public bool $isComparative = false;

    public function mount()
    {
        $this->tbService = app(TrialBalanceService::class);
    }

    public function render()
    {
        if ($this->isComparative && $this->comparePeriodId) {
            $data = $this->tbService->getComparativeTrialBalance($this->periodId, $this->comparePeriodId);
        } else {
            $data = $this->tbService->getTrialBalance($this->periodId);
        }

        $balancingReport = $this->tbService->getBalancingReport($this->periodId);

        return view('livewire.accounting.trial-balance-report', [
            'data' => $data,
            'balancingReport' => $balancingReport,
            'periods' => AccountingPeriod::orderBy('year', 'desc')->orderBy('month', 'desc')->get(),
            'isComparative' => $this->isComparative,
        ]);
    }

    public function toggleComparative()
    {
        $this->isComparative = !$this->isComparative;
    }

    public function exportToCsv()
    {
        $data = $this->tbService->exportTrialBalance($this->periodId);
        $filename = 'trial_balance_' . now()->format('Y-m-d_His') . '.csv';
        
        return response()->streamDownload(function () use ($data) {
            $f = fopen('php://output', 'w');
            fputcsv($f, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($f, $row);
            }
            fclose($f);
        }, $filename);
    }
}
