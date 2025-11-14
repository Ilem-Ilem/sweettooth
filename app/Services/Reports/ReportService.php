<?php

namespace App\Services\Reports;

use App\Models\DepartmentReport;
use App\Models\CompiledReport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class ReportService
{
    protected string $reportCategory;
    protected string $reportType;
    protected $branchId;
    protected $departmentId;
    protected $periodFrom;
    protected $periodTo;
    protected int $cacheMinutes = 60;

    /**
     * Set branch ID for report generation.
     */
    public function forBranch($branchId): self
    {
        $this->branchId = $branchId;
        return $this;
    }

    /**
     * Set department ID for report generation.
     */
    public function forDepartment($departmentId): self
    {
        $this->departmentId = $departmentId;
        return $this;
    }

    /**
     * Set period for report generation.
     */
    public function forPeriod($from, $to): self
    {
        $this->periodFrom = $from;
        $this->periodTo = $to;
        return $this;
    }

    /**
     * Generate and save report.
     */
    public function generate($employeeId = null): DepartmentReport
    {
        DB::beginTransaction();
        try {
            $reportData = $this->generateReportData();
            $summaryMetrics = $this->generateSummaryMetrics($reportData);
            $chartsData = $this->generateChartsData($reportData);

            $report = DepartmentReport::create([
                'branch_id' => $this->branchId,
                'department_id' => $this->departmentId,
                'generated_by' => $employeeId,
                'report_type' => $this->reportType,
                'report_category' => $this->reportCategory,
                'report_name' => $this->getReportName(),
                'report_date' => now()->toDateString(),
                'period_from' => $this->periodFrom,
                'period_to' => $this->periodTo,
                'report_data' => $reportData,
                'summary_metrics' => $summaryMetrics,
                'charts_data' => $chartsData,
                'status' => 'draft',
            ]);

            DB::commit();
            return $report;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get report data (cached).
     */
    public function getReportData(): array
    {
        $cacheKey = $this->getCacheKey();

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () {
            return $this->generateReportData();
        });
    }

    /**
     * Generate cache key for report.
     */
    protected function getCacheKey(): string
    {
        return sprintf(
            'report:%s:%s:%s:%s:%s:%s',
            $this->reportCategory,
            $this->reportType,
            $this->branchId,
            $this->departmentId,
            $this->periodFrom,
            $this->periodTo
        );
    }

    /**
     * Clear report cache.
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
    }

    /**
     * Get report name.
     */
    abstract protected function getReportName(): string;

    /**
     * Generate the actual report data.
     * This must be implemented by child classes.
     */
    abstract protected function generateReportData(): array;

    /**
     * Generate summary metrics from report data.
     */
    abstract protected function generateSummaryMetrics(array $reportData): array;

    /**
     * Generate charts data from report data.
     */
    abstract protected function generateChartsData(array $reportData): array;

    /**
     * Validate required parameters before generation.
     */
    protected function validateParameters(): void
    {
        if (!$this->branchId) {
            throw new \InvalidArgumentException('Branch ID is required');
        }

        if (!$this->periodFrom || !$this->periodTo) {
            throw new \InvalidArgumentException('Period dates are required');
        }
    }

    /**
     * Format number for display.
     */
    protected function formatNumber($number, int $decimals = 2): string
    {
        return number_format($number, $decimals);
    }

    /**
     * Calculate percentage.
     */
    protected function calculatePercentage($value, $total): float
    {
        if ($total == 0) {
            return 0;
        }
        return round(($value / $total) * 100, 2);
    }

    /**
     * Calculate growth rate.
     */
    protected function calculateGrowthRate($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get date range label.
     */
    protected function getDateRangeLabel(): string
    {
        return sprintf(
            '%s to %s',
            \Carbon\Carbon::parse($this->periodFrom)->format('M d, Y'),
            \Carbon\Carbon::parse($this->periodTo)->format('M d, Y')
        );
    }
}
