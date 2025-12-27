<?php

namespace App\Livewire\BranchDashboard\Production\Reports\ProductionEfficiency;

use App\Models\DepartmentReport;
use App\Services\Reports\ProductionEfficiencyReportService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Title, Url};
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Production Efficiency Report')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    // Filters
    public $periodFilter = 'week';
    public $customDateFrom;
    public $customDateTo;
    public $departmentId;

    // Report data
    public $reportData = null;
    public $summaryMetrics = [];
    public $chartsData = [];
    public $isLoading = false;

    // Generated report
    public $generatedReport = null;
    public $showReportModal = false;
    public $savedReports = [];

    // Metric explanation modal
    public $showMetricModal = false;
    public $currentMetric = null;

    public function mount()
    {
        $this->b_id = $this->b_id ?? current_branch_id();
        $this->departmentId = session('selected_department_id');
        $this->setDateRange();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    /**
     * Set date range based on filter.
     */
    public function setDateRange()
    {
        switch ($this->periodFilter) {
            case 'today':
                $this->customDateFrom = Carbon::today()->toDateString();
                $this->customDateTo = Carbon::today()->toDateString();
                break;
            case 'yesterday':
                $this->customDateFrom = Carbon::yesterday()->toDateString();
                $this->customDateTo = Carbon::yesterday()->toDateString();
                break;
            case 'week':
                $this->customDateFrom = Carbon::now()->startOfWeek()->toDateString();
                $this->customDateTo = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'month':
                $this->customDateFrom = Carbon::now()->startOfMonth()->toDateString();
                $this->customDateTo = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->customDateFrom = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $this->customDateTo = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'custom':
                // Keep existing custom dates
                break;
        }
    }

    /**
     * Generate report preview.
     */
    public function generatePreview()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        $this->isLoading = true;

        try {
            $service = new ProductionEfficiencyReportService();

            $service->forBranch($this->b_id ?? current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo);

            $this->reportData = $service->getReportData();
            $this->summaryMetrics = $this->reportData['summary_metrics'] ?? $service->getSummaryMetrics($this->reportData);
            $this->chartsData = $service->getChartsData($this->reportData);

            $this->toast()->success('Report generated successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error generating report: ' . $e->getMessage())->send();
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Generate and save report.
     */
    public function generateReport()
    {
        $this->validate([
            'customDateFrom' => 'required|date',
            'customDateTo' => 'required|date|after_or_equal:customDateFrom',
        ]);

        try {
            $service = new ProductionEfficiencyReportService();

            $this->generatedReport = $service
                ->forBranch($this->b_id ?? current_branch_id())
                ->forDepartment($this->departmentId)
                ->forPeriod($this->customDateFrom, $this->customDateTo)
                ->generate(auth()->id());

            $this->showReportModal = true;
            $this->toast()->success('Report generated and saved successfully')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error generating report: ' . $e->getMessage())->send();
        }
    }

    /**
     * Submit report for review.
     */
    public function submitForReview($reportId)
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);

            if (!$report->isEditable()) {
                $this->toast()->error('Report cannot be edited in its current state')->send();
                return;
            }

            $report->update(['status' => 'pending_review']);

            $this->toast()->success('Report submitted for review')->send();
            $this->showReportModal = false;
            $this->generatedReport = null;
        } catch (\Exception $e) {
            $this->toast()->error('Error submitting report: ' . $e->getMessage())->send();
        }
    }





    /**
     * Refresh report data.
     */
    public function refresh()
    {
        $this->generatePreview();
    }

    /**
     * Update period filter.
     */
    public function updatedPeriodFilter()
    {
        $this->setDateRange();
        if ($this->periodFilter !== 'custom') {
            $this->generatePreview();
        }
    }

    /**
     * Show metric explanation modal
     */
    public function showMetricExplanation($metric)
    {
        $this->currentMetric = $metric;
        $this->showMetricModal = true;
    }

    /**
     * View a saved report
     */
    public function viewReport($reportId)
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);

            // Load the report data into the preview
            $this->reportData = $report->report_data;
            $this->summaryMetrics = $report->summary_metrics;
            $this->chartsData = $report->charts_data;
            $this->customDateFrom = Carbon::parse($report->period_from)->toDateString();
            $this->customDateTo = Carbon::parse($report->period_to)->toDateString();
            $this->periodFilter = 'custom';

            $this->toast()->success('Report loaded for preview')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error loading report: ' . $e->getMessage())->send();
        }
    }

    /**
     * Download/export a report
     */
    public function downloadReport($reportId)
    {
        try {
            $report = DepartmentReport::findOrFail($reportId);

            // For now, return JSON data - you can implement CSV/PDF export
            return response()->json($report->report_data, 200, [
                'Content-Disposition' => 'attachment; filename="production-efficiency-report-' . $report->id . '.json"'
            ]);
        } catch (\Exception $e) {
            $this->toast()->error('Error downloading report: ' . $e->getMessage())->send();
        }
    }

    /**
     * Export current preview as CSV
     */
    public function exportCsv()
    {
        if (!$this->reportData) {
            $this->toast()->error('No report data to export')->send();
            return;
        }

        // Basic CSV export of daily summary
        $csvData = "Date,Planned,Actual,Variance,Efficiency,Products Count\n";

        foreach ($this->reportData['daily_summary'] ?? [] as $day) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $day['date'],
                $day['planned'],
                $day['actual'],
                $day['variance'],
                $day['efficiency_percentage'],
                $day['products_count']
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="production-efficiency-' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Export current preview as PDF
     */
    public function exportPdf()
    {
        if (!$this->reportData) {
            $this->toast()->error('No report data to export')->send();
            return;
        }

        // For now, create a simple HTML-based PDF
        $html = $this->generatePdfHtml();

        // You would typically use a package like DomPDF or TCPDF here
        // For demonstration, we'll create a downloadable HTML file
        $filename = 'production-efficiency-' . now()->format('Y-m-d-H-i-s') . '.html';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Generate HTML content for PDF export
     */
    private function generatePdfHtml(): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Production Efficiency Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .metrics { display: flex; justify-content: space-around; margin-bottom: 30px; }
                .metric { text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #f2f2f2; }
                .chart-placeholder { text-align: center; padding: 40px; border: 1px dashed #ccc; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Production Efficiency Report</h1>
                <p>Period: ' . ($this->customDateFrom ?? 'N/A') . ' to ' . ($this->customDateTo ?? 'N/A') . '</p>
            </div>

            <div class="metrics">
                <div class="metric">
                    <h3>Total Planned</h3>
                    <p style="font-size: 24px; font-weight: bold;">' . number_format($this->summaryMetrics['total_planned'] ?? 0) . '</p>
                </div>
                <div class="metric">
                    <h3>Total Actual</h3>
                    <p style="font-size: 24px; font-weight: bold;">' . number_format($this->summaryMetrics['total_actual'] ?? 0) . '</p>
                </div>
                <div class="metric">
                    <h3>Overall Efficiency</h3>
                    <p style="font-size: 24px; font-weight: bold;">' . number_format($this->summaryMetrics['overall_efficiency'] ?? 0, 1) . '%</p>
                </div>
            </div>

            <h2>Daily Production Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Planned</th>
                        <th>Actual</th>
                        <th>Variance</th>
                        <th>Efficiency %</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($this->reportData['daily_summary'] ?? [] as $day) {
            $html .= '
                    <tr>
                        <td>' . $day['date'] . '</td>
                        <td>' . number_format($day['planned']) . '</td>
                        <td>' . number_format($day['actual']) . '</td>
                        <td>' . number_format($day['variance']) . '</td>
                        <td>' . number_format($day['efficiency_percentage'], 1) . '%</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <h2>Product Efficiency</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Planned</th>
                        <th>Actual</th>
                        <th>Variance</th>
                        <th>Efficiency %</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($this->reportData['product_efficiency'] ?? [] as $product) {
            $html .= '
                    <tr>
                        <td>' . ($product['product_name'] ?? 'Unknown') . '</td>
                        <td>' . number_format($product['planned']) . '</td>
                        <td>' . number_format($product['actual']) . '</td>
                        <td>' . number_format($product['variance']) . '</td>
                        <td>' . number_format($product['efficiency_percentage'], 1) . '%</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="chart-placeholder">
                <p><strong>Charts:</strong> Interactive charts would be displayed here in the web version</p>
                <p>Daily Production Trend, Variance Distribution, and Product Efficiency Comparison</p>
            </div>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ccc; text-align: center; font-size: 12px; color: #666;">
                <p>Report generated on ' . now()->format('Y-m-d H:i:s') . '</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    public function render()
    {
        // Load saved reports for this department and report type
        $this->savedReports = DepartmentReport::where('branch_id', $this->b_id ?? current_branch_id())
            ->where('department_id', $this->departmentId)
            ->where('report_type', 'production_efficiency')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.branch-dashboard.production.reports.production-efficiency.index', [
            'savedReports' => $this->savedReports,
        ]);
    }
}
