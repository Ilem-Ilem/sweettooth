<?php

namespace App\Exports;

use App\Models\DepartmentReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DepartmentReportExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected DepartmentReport $report
    ) {}

    public function view(): View
    {
        return view('exports.reports.department-report', [
            'report' => $this->report,
        ]);
    }
}
