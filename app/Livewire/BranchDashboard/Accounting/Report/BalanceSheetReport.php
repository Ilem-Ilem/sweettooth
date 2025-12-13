<?php

namespace App\Livewire\BranchDashboard\Accounting\Report;

use App\Services\BalanceSheetService;
use App\Models\AccountingPeriod;
use Livewire\Component;

class BalanceSheetReport extends Component
{
    protected BalanceSheetService $bsService;

    public ?int $periodId = null;
    public ?int $comparePeriodId = null;
    public bool $isComparative = false;
    public bool $showRatios = false;

    public function mount()
    {
        $this->bsService = app(BalanceSheetService::class);
    }

    public function render()
    {
        if ($this->isComparative && $this->comparePeriodId) {
            $data = $this->bsService->getComparativeBalanceSheet($this->periodId, $this->comparePeriodId);
        } else {
            $data = $this->bsService->getBalanceSheet($this->periodId);
        }

        $ratios = $this->bsService->getFinancialRatios($this->periodId);

        return view('livewire.branch-dashboard.accounting.report.balance-sheet-report', [
            'data' => $data,
            'ratios' => $ratios,
            'periods' => AccountingPeriod::orderBy('year', 'desc')->orderBy('month', 'desc')->get(),
            'isComparative' => $this->isComparative,
            'showRatios' => $this->showRatios,
        ]);
    }

    public function toggleComparative()
    {
        $this->isComparative = !$this->isComparative;
    }

    public function toggleRatios()
    {
        $this->showRatios = !$this->showRatios;
    }

    public function exportToCsv()
    {
        $data = $this->bsService->exportBalanceSheet($this->periodId);
        $filename = 'balance_sheet_' . now()->format('Y-m-d_His') . '.csv';
        
        return response()->streamDownload(function () use ($data) {
            $f = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($f, is_array($row) ? $row : [$row]);
            }
            fclose($f);
        }, $filename);
    }
}
