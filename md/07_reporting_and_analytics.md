# 07 - Production Progress Reporting and Analytics

## Overview

This document outlines the reporting and analytics capabilities for production progress tracking. The system provides comprehensive insights into production efficiency, trends, and performance metrics across all departments.

## Report Types

### 1. Production Efficiency Reports

#### Daily Production Summary
```php
<?php

namespace App\Reports;

class DailyProductionSummaryReport extends BaseReport
{
    protected string $title = 'Daily Production Summary';
    protected array $metrics = [
        'total_produced',
        'total_planned',
        'completion_rate',
        'efficiency_rate',
        'quality_score',
        'average_production_time'
    ];

    public function generate(Carbon $date, int $departmentId = null): array
    {
        $query = DailyProduce::whereDate('produce_date', $date)
            ->with(['shift', 'recipe', 'productionRecords']);

        if ($departmentId) {
            $query->whereHas('shift', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $productions = $query->get();

        return [
            'summary' => [
                'date' => $date->format('Y-m-d'),
                'department' => $departmentId ? Department::find($departmentId)->name : 'All Departments',
                'total_productions' => $productions->count(),
                'completed_productions' => $productions->where('status', 'completed')->count(),
                'total_quantity_produced' => $productions->sum('produced_quantity'),
                'total_quantity_planned' => $productions->sum('requested_quantity'),
                'completion_rate' => $this->calculateCompletionRate($productions),
                'efficiency_metrics' => $this->calculateEfficiencyMetrics($productions),
                'quality_metrics' => $this->calculateQualityMetrics($productions),
            ],
            'department_breakdown' => $this->getDepartmentBreakdown($productions),
            'hourly_progression' => $this->getHourlyProgression($productions),
            'top_performers' => $this->getTopPerformers($productions),
            'bottlenecks' => $this->identifyBottlenecks($productions),
        ];
    }

    private function calculateCompletionRate(Collection $productions): float
    {
        if ($productions->isEmpty()) return 0;

        $completed = $productions->where('status', 'completed')->count();
        return round(($completed / $productions->count()) * 100, 2);
    }

    private function calculateEfficiencyMetrics(Collection $productions): array
    {
        $totalPlanned = $productions->sum('requested_quantity');
        $totalProduced = $productions->sum('produced_quantity');

        $efficiency = $totalPlanned > 0 ? ($totalProduced / $totalPlanned) * 100 : 0;

        return [
            'efficiency_percentage' => round($efficiency, 2),
            'overproduction' => $totalProduced > $totalPlanned ? $totalProduced - $totalPlanned : 0,
            'underproduction' => $totalProduced < $totalPlanned ? $totalPlanned - $totalProduced : 0,
            'average_production_time' => $this->calculateAverageProductionTime($productions),
        ];
    }

    private function calculateQualityMetrics(Collection $productions): array
    {
        $records = $productions->pluck('productionRecords')->flatten();

        if ($records->isEmpty()) {
            return ['average_quality_score' => 0, 'rejection_rate' => 0];
        }

        $averageQuality = $records->avg('quality_score') ?? 0;
        $totalProduced = $records->sum('quantity_produced');
        $totalRejected = $records->sum('quantity_rejected');
        $rejectionRate = $totalProduced > 0 ? ($totalRejected / $totalProduced) * 100 : 0;

        return [
            'average_quality_score' => round($averageQuality, 2),
            'rejection_rate' => round($rejectionRate, 2),
            'quality_distribution' => $this->getQualityDistribution($records),
        ];
    }

    private function getDepartmentBreakdown(Collection $productions): array
    {
        return $productions->groupBy(function($production) {
            return $production->shift->department->name;
        })->map(function($deptProductions, $deptName) {
            return [
                'department' => $deptName,
                'total_productions' => $deptProductions->count(),
                'completed' => $deptProductions->where('status', 'completed')->count(),
                'total_produced' => $deptProductions->sum('produced_quantity'),
                'completion_rate' => $this->calculateCompletionRate($deptProductions),
            ];
        })->values();
    }

    private function getHourlyProgression(Collection $productions): array
    {
        $hourly = [];

        for ($hour = 6; $hour <= 22; $hour++) {
            $hourProductions = $productions->filter(function($p) use ($hour) {
                return $p->created_at->hour === $hour;
            });

            $hourly[] = [
                'hour' => $hour,
                'productions_count' => $hourProductions->count(),
                'quantity_produced' => $hourProductions->sum('produced_quantity'),
                'completed_count' => $hourProductions->where('status', 'completed')->count(),
            ];
        }

        return $hourly;
    }

    private function getTopPerformers(Collection $productions): array
    {
        return $productions->sortByDesc(function($p) {
            return $p->produced_quantity / max($p->requested_quantity, 1);
        })->take(5)->map(function($p) {
            return [
                'recipe_name' => $p->recipe->name,
                'produced_quantity' => $p->produced_quantity,
                'requested_quantity' => $p->requested_quantity,
                'efficiency' => round(($p->produced_quantity / max($p->requested_quantity, 1)) * 100, 2),
                'production_time' => $p->productionRecords->avg('production_time_minutes'),
            ];
        })->values();
    }

    private function identifyBottlenecks(Collection $productions): array
    {
        $bottlenecks = [];

        // Check for productions with low efficiency
        $lowEfficiency = $productions->filter(function($p) {
            $efficiency = $p->produced_quantity / max($p->requested_quantity, 1);
            return $efficiency < 0.8; // Less than 80% efficiency
        });

        if ($lowEfficiency->count() > 0) {
            $bottlenecks[] = [
                'type' => 'low_efficiency',
                'description' => "{$lowEfficiency->count()} productions with low efficiency",
                'affected_productions' => $lowEfficiency->pluck('recipe.name'),
            ];
        }

        // Check for long production times
        $avgTime = $productions->avg(function($p) {
            return $p->productionRecords->avg('production_time_minutes') ?? 0;
        });

        $longProductions = $productions->filter(function($p) use ($avgTime) {
            $prodTime = $p->productionRecords->avg('production_time_minutes') ?? 0;
            return $prodTime > $avgTime * 1.5; // 50% longer than average
        });

        if ($longProductions->count() > 0) {
            $bottlenecks[] = [
                'type' => 'long_production_times',
                'description' => "{$longProductions->count()} productions taking longer than average",
                'affected_productions' => $longProductions->pluck('recipe.name'),
            ];
        }

        return $bottlenecks;
    }
}
```

### 2. Trend Analysis Reports

#### Production Trend Report
```php
<?php

class ProductionTrendReport extends BaseReport
{
    public function generate(Carbon $startDate, Carbon $endDate, int $departmentId = null): array
    {
        $dailySummaries = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dailyReport = app(DailyProductionSummaryReport::class)->generate($date, $departmentId);
            $dailySummaries->push($dailyReport['summary']);
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => $dailySummaries->count(),
            ],
            'overall_metrics' => $this->calculateOverallMetrics($dailySummaries),
            'trends' => $this->analyzeTrends($dailySummaries),
            'department_comparison' => $this->compareDepartments($startDate, $endDate),
            'peak_performance_days' => $this->identifyPeakDays($dailySummaries),
            'seasonal_patterns' => $this->analyzeSeasonalPatterns($dailySummaries),
        ];
    }

    private function calculateOverallMetrics(Collection $summaries): array
    {
        return [
            'average_completion_rate' => $summaries->avg('completion_rate'),
            'average_efficiency' => $summaries->avg('efficiency_metrics.efficiency_percentage'),
            'average_quality_score' => $summaries->avg('quality_metrics.average_quality_score'),
            'total_produced' => $summaries->sum('total_quantity_produced'),
            'total_planned' => $summaries->sum('total_quantity_planned'),
            'best_day' => $summaries->sortByDesc('completion_rate')->first(),
            'worst_day' => $summaries->sortBy('completion_rate')->first(),
        ];
    }

    private function analyzeTrends(Collection $summaries): array
    {
        $sorted = $summaries->sortBy('date');

        return [
            'completion_rate_trend' => $this->calculateTrend($sorted->pluck('completion_rate')),
            'efficiency_trend' => $this->calculateTrend($sorted->pluck('efficiency_metrics.efficiency_percentage')),
            'quality_trend' => $this->calculateTrend($sorted->pluck('quality_metrics.average_quality_score')),
            'productivity_trend' => $this->calculateTrend($sorted->pluck('total_quantity_produced')),
        ];
    }

    private function calculateTrend(Collection $values): array
    {
        if ($values->count() < 2) return ['direction' => 'stable', 'change_percentage' => 0];

        $first = $values->first();
        $last = $values->last();

        if ($first == 0) return ['direction' => 'stable', 'change_percentage' => 0];

        $change = (($last - $first) / $first) * 100;
        $direction = $change > 5 ? 'increasing' : ($change < -5 ? 'decreasing' : 'stable');

        return [
            'direction' => $direction,
            'change_percentage' => round($change, 2),
            'start_value' => $first,
            'end_value' => $last,
        ];
    }

    private function compareDepartments(Carbon $startDate, Carbon $endDate): array
    {
        $departments = Department::where('category_id', DepartmentCategory::where('name', 'Production')->first()->id)->get();

        return $departments->map(function($dept) use ($startDate, $endDate) {
            $deptReport = app(ProductionTrendReport::class)->generate($startDate, $endDate, $dept->id);
            return [
                'department' => $dept->name,
                'metrics' => $deptReport['overall_metrics'],
            ];
        });
    }
}
```

### 3. Quality Assurance Reports

#### Quality Performance Report
```php
<?php

class QualityPerformanceReport extends BaseReport
{
    public function generate(Carbon $startDate, Carbon $endDate, int $departmentId = null): array
    {
        $qualityData = $this->getQualityData($startDate, $endDate, $departmentId);

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'quality_overview' => $this->calculateQualityOverview($qualityData),
            'quality_by_department' => $this->qualityByDepartment($qualityData),
            'quality_trends' => $this->analyzeQualityTrends($qualityData),
            'rejection_analysis' => $this->analyzeRejections($qualityData),
            'quality_improvement_suggestions' => $this->generateImprovementSuggestions($qualityData),
        ];
    }

    private function getQualityData(Carbon $startDate, Carbon $endDate, ?int $departmentId): Collection
    {
        $query = ProductionRecord::whereBetween('created_at', [$startDate, $endDate])
            ->with(['dailyProduce.shift.department', 'dailyProduce.recipe']);

        if ($departmentId) {
            $query->whereHas('dailyProduce.shift', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->get();
    }

    private function calculateQualityOverview(Collection $qualityData): array
    {
        $totalRecords = $qualityData->count();
        $avgQualityScore = $qualityData->avg('quality_score') ?? 0;
        $excellentCount = $qualityData->where('quality_score', '>=', 90)->count();
        $goodCount = $qualityData->whereBetween('quality_score', [80, 89])->count();
        $acceptableCount = $qualityData->whereBetween('quality_score', [70, 79])->count();
        $poorCount = $qualityData->where('quality_score', '<', 70)->count();

        $totalProduced = $qualityData->sum('quantity_produced');
        $totalRejected = $qualityData->sum('quantity_rejected');
        $rejectionRate = $totalProduced > 0 ? ($totalRejected / $totalProduced) * 100 : 0;

        return [
            'total_records' => $totalRecords,
            'average_quality_score' => round($avgQualityScore, 2),
            'quality_distribution' => [
                'excellent' => ['count' => $excellentCount, 'percentage' => round(($excellentCount / $totalRecords) * 100, 2)],
                'good' => ['count' => $goodCount, 'percentage' => round(($goodCount / $totalRecords) * 100, 2)],
                'acceptable' => ['count' => $acceptableCount, 'percentage' => round(($acceptableCount / $totalRecords) * 100, 2)],
                'poor' => ['count' => $poorCount, 'percentage' => round(($poorCount / $totalRecords) * 100, 2)],
            ],
            'rejection_rate' => round($rejectionRate, 2),
            'quality_consistency' => $this->calculateQualityConsistency($qualityData),
        ];
    }

    private function calculateQualityConsistency(Collection $qualityData): float
    {
        if ($qualityData->isEmpty()) return 0;

        $scores = $qualityData->pluck('quality_score');
        $mean = $scores->avg();
        $variance = $scores->map(function($score) use ($mean) {
            return pow($score - $mean, 2);
        })->avg();

        $stdDev = sqrt($variance);
        $consistency = max(0, 100 - ($stdDev * 2)); // Lower std dev = higher consistency

        return round($consistency, 2);
    }

    private function analyzeRejections(Collection $qualityData): array
    {
        $rejectionsByReason = $qualityData->whereNotNull('rejection_reason')
            ->groupBy('rejection_reason')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total_quantity' => $group->sum('quantity_rejected'),
                    'percentage' => round(($group->sum('quantity_rejected') / $group->sum('quantity_produced')) * 100, 2),
                ];
            });

        $rejectionsByDepartment = $qualityData->groupBy(function($record) {
            return $record->dailyProduce->shift->department->name;
        })->map(function($deptRecords) {
            $totalProduced = $deptRecords->sum('quantity_produced');
            $totalRejected = $deptRecords->sum('quantity_rejected');

            return [
                'total_produced' => $totalProduced,
                'total_rejected' => $totalRejected,
                'rejection_rate' => $totalProduced > 0 ? round(($totalRejected / $totalProduced) * 100, 2) : 0,
            ];
        });

        return [
            'by_reason' => $rejectionsByReason,
            'by_department' => $rejectionsByDepartment,
            'trends' => $this->analyzeRejectionTrends($qualityData),
        ];
    }

    private function generateImprovementSuggestions(Collection $qualityData): array
    {
        $suggestions = [];

        $avgQuality = $qualityData->avg('quality_score') ?? 0;
        if ($avgQuality < 85) {
            $suggestions[] = [
                'type' => 'quality_improvement',
                'priority' => 'high',
                'suggestion' => 'Implement additional quality control measures to improve average quality score',
                'expected_impact' => 'Increase quality score by 5-10 points',
            ];
        }

        $rejectionRate = $this->calculateRejectionRate($qualityData);
        if ($rejectionRate > 5) {
            $suggestions[] = [
                'type' => 'reduce_waste',
                'priority' => 'high',
                'suggestion' => 'Review rejection reasons and implement preventive measures',
                'expected_impact' => 'Reduce rejection rate by 2-3%',
            ];
        }

        $consistency = $this->calculateQualityConsistency($qualityData);
        if ($consistency < 80) {
            $suggestions[] = [
                'type' => 'standardize_processes',
                'priority' => 'medium',
                'suggestion' => 'Standardize production processes to improve quality consistency',
                'expected_impact' => 'Improve consistency score by 10-15 points',
            ];
        }

        return $suggestions;
    }
}
```

## Analytics Dashboard

### Executive Dashboard
```php
<!-- resources/views/livewire/reports/production-analytics.blade.php -->
<div class="space-y-6">
    <!-- KPI Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card
            title="Overall Efficiency"
            :value="$overallEfficiency"
            unit="%"
            :trend="$efficiencyTrend"
            color="blue"
            icon="⚡"
        />

        <x-kpi-card
            title="Quality Score"
            :value="$avgQualityScore"
            unit="/100"
            :trend="$qualityTrend"
            color="green"
            icon="✅"
        />

        <x-kpi-card
            title="On-Time Delivery"
            :value="$onTimeDeliveryRate"
            unit="%"
            :trend="$deliveryTrend"
            color="purple"
            icon="⏰"
        />

        <x-kpi-card
            title="Resource Utilization"
            :value="$resourceUtilization"
            unit="%"
            :trend="$utilizationTrend"
            color="orange"
            icon="📊"
        />
    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Efficiency Trend -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Efficiency Trend</h3>
            <canvas id="efficiencyChart" width="400" height="200"></canvas>
        </div>

        <!-- Quality Trend -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Quality Trend</h3>
            <canvas id="qualityChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Department Performance</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                        <th class="text-left py-3 px-4 font-semibold">Department</th>
                        <th class="text-center py-3 px-4 font-semibold">Efficiency</th>
                        <th class="text-center py-3 px-4 font-semibold">Quality</th>
                        <th class="text-center py-3 px-4 font-semibold">Completion Rate</th>
                        <th class="text-center py-3 px-4 font-semibold">Alerts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departmentPerformance as $dept)
                    <tr class="border-b border-zinc-100 dark:border-zinc-700">
                        <td class="py-3 px-4 font-medium">{{ $dept['name'] }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $dept['efficiency'] >= 90 ? 'bg-green-100 text-green-800' :
                                   ($dept['efficiency'] >= 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $dept['efficiency'] }}%
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $dept['quality'] >= 85 ? 'bg-green-100 text-green-800' :
                                   ($dept['quality'] >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $dept['quality'] }}/100
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $dept['completion_rate'] >= 95 ? 'bg-green-100 text-green-800' :
                                   ($dept['completion_rate'] >= 85 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $dept['completion_rate'] }}%
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($dept['active_alerts'] > 0)
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                    {{ $dept['active_alerts'] }} active
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    No alerts
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Predictive Analytics -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Predictive Insights</h3>
        <div class="space-y-4">
            @foreach($predictions as $prediction)
            <div class="flex items-start space-x-4 p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg">
                <div class="flex-shrink-0">
                    {!! $prediction['icon'] !!}
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $prediction['title'] }}
                    </h4>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        {{ $prediction['description'] }}
                    </p>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            Confidence: {{ $prediction['confidence'] }}%
                        </span>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            Impact: {{ $prediction['impact'] }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// Initialize charts when component loads
document.addEventListener('livewire:initialized', () => {
    initializeCharts();
});

function initializeCharts() {
    // Efficiency Trend Chart
    const efficiencyCtx = document.getElementById('efficiencyChart').getContext('2d');
    new Chart(efficiencyCtx, {
        type: 'line',
        data: @json($efficiencyChartData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Quality Trend Chart
    const qualityCtx = document.getElementById('qualityChart').getContext('2d');
    new Chart(qualityCtx, {
        type: 'line',
        data: @json($qualityChartData),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}
</script>
```

## Automated Report Distribution

### Scheduled Reports
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportDistributionService;

class SendScheduledProductionReports extends Command
{
    protected $signature = 'reports:send-production-reports {frequency=daily}';
    protected $description = 'Send scheduled production reports to stakeholders';

    public function handle(ReportDistributionService $distributionService)
    {
        $frequency = $this->argument('frequency');

        $reports = [
            'daily' => [
                'DailyProductionSummaryReport',
                'QualityPerformanceReport'
            ],
            'weekly' => [
                'ProductionTrendReport',
                'DepartmentPerformanceReport'
            ],
            'monthly' => [
                'MonthlyProductionAnalysisReport',
                'QualityImprovementReport'
            ]
        ];

        foreach ($reports[$frequency] ?? [] as $reportClass) {
            $this->info("Generating {$reportClass}...");

            $report = app("App\\Reports\\{$reportClass}");
            $data = $report->generateForPeriod($frequency);

            $distributionService->distributeReport($report, $data, $frequency);

            $this->info("{$reportClass} distributed successfully.");
        }

        $this->info('All reports sent successfully.');
    }
}
```

### Report Templates
```php
<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class ReportDistributionService
{
    public function distributeReport($report, array $data, string $frequency): void
    {
        $recipients = $this->getReportRecipients($report, $frequency);

        foreach ($recipients as $recipient) {
            $format = $recipient['preferred_format'] ?? 'email';

            switch ($format) {
                case 'pdf':
                    $this->sendPdfReport($report, $data, $recipient);
                    break;
                case 'excel':
                    $this->sendExcelReport($report, $data, $recipient);
                    break;
                default:
                    $this->sendEmailReport($report, $data, $recipient);
            }
        }
    }

    private function sendEmailReport($report, array $data, array $recipient): void
    {
        $htmlContent = $this->renderReportHtml($report, $data);

        Mail::html($htmlContent, function ($message) use ($report, $recipient) {
            $message->to($recipient['email'])
                    ->subject("{$report->title} - " . now()->format('M d, Y'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    private function sendPdfReport($report, array $data, array $recipient): void
    {
        $htmlContent = $this->renderReportHtml($report, $data);
        $pdf = Pdf::loadHTML($htmlContent);

        Mail::send([], [], function ($message) use ($pdf, $report, $recipient) {
            $message->to($recipient['email'])
                    ->subject("{$report->title} - " . now()->format('M d, Y'))
                    ->attachData($pdf->output(), "{$report->title}.pdf", [
                        'mime' => 'application/pdf',
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    private function renderReportHtml($report, array $data): string
    {
        return view("reports.{$report->getTemplateName()}", [
            'report' => $report,
            'data' => $data,
            'generated_at' => now(),
        ])->render();
    }

    private function getReportRecipients($report, string $frequency): array
    {
        // Get users who should receive this report based on frequency and permissions
        return User::whereHas('reportSubscriptions', function($q) use ($report, $frequency) {
            $q->where('report_type', get_class($report))
              ->where('frequency', $frequency)
              ->where('active', true);
        })->with('reportSubscriptions')->get()->map(function($user) {
            $subscription = $user->reportSubscriptions->first();
            return [
                'email' => $user->email,
                'preferred_format' => $subscription->preferred_format ?? 'email',
                'user' => $user,
            ];
        })->toArray();
    }
}
```

## Data Export and Integration

### Export Capabilities
- **CSV Export**: For data analysis in Excel/SPSS
- **Excel Export**: Formatted reports with charts
- **PDF Export**: Print-ready reports
- **API Endpoints**: Real-time data access for external systems

### Third-party Integrations
- **BI Tools**: Tableau, Power BI integration
- **ERP Systems**: SAP, Oracle integration
- **Communication Tools**: Slack, Microsoft Teams notifications
- **Cloud Storage**: AWS S3, Google Drive automatic backups

This completes the comprehensive reporting and analytics system for production progress tracking.