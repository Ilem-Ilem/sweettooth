<?php

namespace App\Services\Reports;

use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\Stock;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockVarianceReportService extends ReportService
{
    protected string $reportCategory = 'inventory';
    protected string $reportType = 'stock_variance';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Stock Variance Report';
    }

    /**
     * Generate the stock variance report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        // Get stock takes within the period
        $stockTakes = StockTake::query()
            ->where('branch_id', $this->branchId)
            ->when($this->departmentId, function ($q) {
                $q->where('department_id', $this->departmentId);
            })
            ->whereBetween('stock_take_date', [$this->periodFrom, $this->periodTo])
            ->with(['stockTakeItems.item', 'performedBy'])
            ->get();

        $varianceData = [];
        $positiveVariances = [];
        $negativeVariances = [];
        $significantVariances = [];

        foreach ($stockTakes as $stockTake) {
            foreach ($stockTake->stockTakeItems as $stockTakeItem) {
                $systemStock = $stockTakeItem->system_quantity ?? 0;
                $actualStock = $stockTakeItem->actual_quantity ?? 0;
                $variance = $actualStock - $systemStock;
                $variancePercentage = $systemStock > 0
                    ? ($variance / $systemStock) * 100
                    : ($actualStock > 0 ? 100 : 0);

                $item = $stockTakeItem->item;

                $varianceRecord = [
                    'stock_take_id' => $stockTake->id,
                    'stock_take_date' => $stockTake->stock_take_date->format('Y-m-d'),
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'sku' => $item->sku,
                    'category' => $item->category ?? 'Uncategorized',
                    'system_quantity' => $systemStock,
                    'actual_quantity' => $actualStock,
                    'variance' => $variance,
                    'variance_percentage' => round($variancePercentage, 2),
                    'variance_value' => $variance * ($item->unit_cost ?? 0),
                    'uom' => $item->uom,
                    'unit_cost' => $item->unit_cost ?? 0,
                    'performed_by' => $stockTake->performedBy->name ?? 'Unknown',
                    'notes' => $stockTakeItem->notes ?? '',
                    'severity' => $this->determineSeverity($variancePercentage),
                ];

                $varianceData[] = $varianceRecord;

                // Categorize variances
                if ($variance > 0) {
                    $positiveVariances[] = $varianceRecord;
                } elseif ($variance < 0) {
                    $negativeVariances[] = $varianceRecord;
                }

                // Significant variances (>5% or high value)
                if (abs($variancePercentage) > 5 || abs($varianceRecord['variance_value']) > 1000) {
                    $significantVariances[] = $varianceRecord;
                }
            }
        }

        // Sort by absolute variance value descending
        usort($varianceData, function ($a, $b) {
            return abs($b['variance_value']) <=> abs($a['variance_value']);
        });

        return [
            'variance_overview' => $this->generateVarianceOverview($varianceData),
            'all_variances' => $varianceData,
            'positive_variances' => $positiveVariances,
            'negative_variances' => $negativeVariances,
            'significant_variances' => $significantVariances,
            'by_category' => $this->groupByCategory($varianceData),
            'by_severity' => $this->groupBySeverity($varianceData),
            'trend_analysis' => $this->analyzeTrends($stockTakes),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'stock_takes_count' => $stockTakes->count(),
            ],
        ];
    }

    /**
     * Determine variance severity.
     */
    private function determineSeverity($variancePercentage): string
    {
        $absPercentage = abs($variancePercentage);

        if ($absPercentage >= 20) {
            return 'critical';
        } elseif ($absPercentage >= 10) {
            return 'high';
        } elseif ($absPercentage >= 5) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Generate variance overview.
     */
    private function generateVarianceOverview(array $varianceData): array
    {
        if (empty($varianceData)) {
            return [
                'total_items_checked' => 0,
                'items_with_variance' => 0,
                'total_variance_value' => 0,
                'positive_variance_value' => 0,
                'negative_variance_value' => 0,
                'average_variance_percentage' => 0,
                'critical_variances' => 0,
                'high_variances' => 0,
            ];
        }

        $totalItems = count($varianceData);
        $itemsWithVariance = count(array_filter($varianceData, fn($item) => $item['variance'] != 0));

        $positiveValue = array_sum(array_map(
            fn($item) => $item['variance'] > 0 ? $item['variance_value'] : 0,
            $varianceData
        ));

        $negativeValue = array_sum(array_map(
            fn($item) => $item['variance'] < 0 ? $item['variance_value'] : 0,
            $varianceData
        ));

        $avgVariancePercentage = array_sum(array_column($varianceData, 'variance_percentage')) / $totalItems;

        $severityCounts = array_count_values(array_column($varianceData, 'severity'));

        return [
            'total_items_checked' => $totalItems,
            'items_with_variance' => $itemsWithVariance,
            'accuracy_rate' => $totalItems > 0
                ? round((($totalItems - $itemsWithVariance) / $totalItems) * 100, 2)
                : 0,
            'total_variance_value' => $positiveValue + $negativeValue,
            'positive_variance_value' => $positiveValue,
            'negative_variance_value' => abs($negativeValue),
            'average_variance_percentage' => round($avgVariancePercentage, 2),
            'critical_variances' => $severityCounts['critical'] ?? 0,
            'high_variances' => $severityCounts['high'] ?? 0,
            'medium_variances' => $severityCounts['medium'] ?? 0,
            'low_variances' => $severityCounts['low'] ?? 0,
        ];
    }

    /**
     * Group variances by category.
     */
    private function groupByCategory(array $varianceData): array
    {
        $categories = [];

        foreach ($varianceData as $item) {
            $category = $item['category'];

            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'category' => $category,
                    'item_count' => 0,
                    'total_variance_value' => 0,
                    'positive_variance_count' => 0,
                    'negative_variance_count' => 0,
                    'avg_variance_percentage' => 0,
                ];
            }

            $categories[$category]['item_count']++;
            $categories[$category]['total_variance_value'] += $item['variance_value'];
            $categories[$category]['avg_variance_percentage'] += $item['variance_percentage'];

            if ($item['variance'] > 0) {
                $categories[$category]['positive_variance_count']++;
            } elseif ($item['variance'] < 0) {
                $categories[$category]['negative_variance_count']++;
            }
        }

        // Calculate averages
        foreach ($categories as &$category) {
            if ($category['item_count'] > 0) {
                $category['avg_variance_percentage'] = round(
                    $category['avg_variance_percentage'] / $category['item_count'],
                    2
                );
            }
        }

        // Sort by absolute total variance value
        usort($categories, function ($a, $b) {
            return abs($b['total_variance_value']) <=> abs($a['total_variance_value']);
        });

        return array_values($categories);
    }

    /**
     * Group by severity.
     */
    private function groupBySeverity(array $varianceData): array
    {
        $severities = [
            'critical' => [],
            'high' => [],
            'medium' => [],
            'low' => [],
        ];

        foreach ($varianceData as $item) {
            $severities[$item['severity']][] = $item;
        }

        return [
            'critical' => array_slice($severities['critical'], 0, 20),
            'high' => array_slice($severities['high'], 0, 20),
            'medium' => array_slice($severities['medium'], 0, 20),
            'low' => array_slice($severities['low'], 0, 20),
        ];
    }

    /**
     * Analyze variance trends over time.
     */
    private function analyzeTrends($stockTakes): array
    {
        $trends = [];

        foreach ($stockTakes as $stockTake) {
            $date = $stockTake->stock_take_date->format('Y-m-d');

            if (!isset($trends[$date])) {
                $trends[$date] = [
                    'date' => $date,
                    'items_checked' => 0,
                    'total_variance_value' => 0,
                    'accuracy_rate' => 0,
                ];
            }

            $itemsChecked = $stockTake->stockTakeItems->count();
            $itemsWithVariance = $stockTake->stockTakeItems->filter(function ($item) {
                return ($item->actual_quantity ?? 0) != ($item->system_quantity ?? 0);
            })->count();

            $varianceValue = $stockTake->stockTakeItems->sum(function ($item) {
                $variance = ($item->actual_quantity ?? 0) - ($item->system_quantity ?? 0);
                return $variance * ($item->item->unit_cost ?? 0);
            });

            $trends[$date]['items_checked'] += $itemsChecked;
            $trends[$date]['total_variance_value'] += $varianceValue;
            $trends[$date]['accuracy_rate'] = $itemsChecked > 0
                ? round((($itemsChecked - $itemsWithVariance) / $itemsChecked) * 100, 2)
                : 0;
        }

        // Sort by date
        uksort($trends, function ($a, $b) {
            return strcmp($a, $b);
        });

        return array_values($trends);
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $overview = $reportData['variance_overview'];

        return [
            'total_items' => $overview['total_items_checked'],
            'accuracy_rate' => $overview['accuracy_rate'],
            'total_variance_value' => $overview['total_variance_value'],
            'critical_variances' => $overview['critical_variances'],
            'items_with_variance' => $overview['items_with_variance'],
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        $overview = $reportData['variance_overview'];
        $categories = array_slice($reportData['by_category'], 0, 10);
        $trends = $reportData['trend_analysis'];

        return [
            'severity_breakdown_chart' => [
                'type' => 'doughnut',
                'labels' => ['Critical', 'High', 'Medium', 'Low'],
                'data' => [
                    $overview['critical_variances'],
                    $overview['high_variances'],
                    $overview['medium_variances'],
                    $overview['low_variances'],
                ],
                'colors' => ['#ef4444', '#f59e0b', '#eab308', '#22c55e'],
            ],
            'variance_value_chart' => [
                'type' => 'bar',
                'labels' => ['Positive Variance', 'Negative Variance'],
                'datasets' => [
                    [
                        'label' => 'Variance Value',
                        'data' => [
                            $overview['positive_variance_value'],
                            $overview['negative_variance_value'],
                        ],
                        'colors' => ['#22c55e', '#ef4444'],
                    ],
                ],
            ],
            'category_variance_chart' => [
                'type' => 'horizontalBar',
                'labels' => array_column($categories, 'category'),
                'datasets' => [
                    [
                        'label' => 'Total Variance Value',
                        'data' => array_column($categories, 'total_variance_value'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'accuracy_trend_chart' => [
                'type' => 'line',
                'labels' => array_column($trends, 'date'),
                'datasets' => [
                    [
                        'label' => 'Accuracy Rate (%)',
                        'data' => array_column($trends, 'accuracy_rate'),
                        'color' => '#22c55e',
                    ],
                ],
            ],
        ];
    }
}
