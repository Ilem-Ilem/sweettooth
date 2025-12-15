<?php

namespace App\Livewire\BranchDashboard\Accounting\Report;

use App\Services\IncomeStatementService;
use App\Models\AccountingPeriod;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class IncomeStatementReport extends Component
{
    protected IncomeStatementService $isService;

    public ?int $periodId = null;
    public ?int $comparePeriodId = null;
    public bool $isComparative = false;

    public function boot()
    {
        $this->isService = app(IncomeStatementService::class);
    }

    public function render()
    {
        if ($this->isComparative && $this->comparePeriodId) {
            $data = $this->isService->getComparativeIncomeStatement($this->periodId, $this->comparePeriodId);
        } else {
            $data = $this->isService->getIncomeStatement($this->periodId);
        }

        return view('livewire.branch-dashboard.accounting.report.income-statement-report', [
            'data' => $data,
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
        $data = $this->isService->exportIncomeStatement($this->periodId);
        $filename = 'income_statement_' . now()->format('Y-m-d_His') . '.csv';
        
        return response()->streamDownload(function () use ($data) {
            $f = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($f, is_array($row) ? $row : [$row]);
            }
            fclose($f);
        }, $filename);
    }
}
