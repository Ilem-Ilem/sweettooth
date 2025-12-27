<?php

namespace App\Services\Reports;

use App\Models\DailyProduce;
use App\Models\ProductionRecord;
use App\Models\ItemRequestDetail;

class CostAnalysisReportService extends ReportService
{
    protected string $reportCategory = 'production';

    protected string $reportType = 'cost_analysis';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Cost Analysis Report';
    }

    /**
     * Get summary metrics for report data.
     */
    public function getSummaryMetrics(array $reportData): array
    {
        return $this->generateSummaryMetrics($reportData);
    }

    /**
     * Get charts data for report data.
     */
    public function getChartsData(array $reportData): array
    {
        return $this->generateChartsData($reportData);
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
     * Generate the cost analysis report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        // Get dispatched items with costs
        $dispatchedItems = ItemRequestDetail::query()
            ->with(['item', 'itemRequest.productionRequests.recipe'])
            ->whereHas('itemRequest', function ($q) {
                $q->where('branch_id', $this->branchId);
                if ($this->departmentId) {
                    $q->where('department_id', $this->departmentId);
                }
            })
            ->where('quantity_dispatched', '>', 0)
            ->whereBetween('updated_at', [$this->periodFrom, $this->periodTo])
            ->get();

        // Get production records for cost analysis
        $productionRecords = ProductionRecord::query()
            ->with(['recipe', 'producedBy', 'dailyProduce.shift'])
            ->whereHas('dailyProduce.shift', function ($q) {
                $q->where('branch_id', $this->branchId);
                if ($this->departmentId) {
                    $q->where('department_id', $this->departmentId);
                }
            })
            ->whereBetween('production_time', [$this->periodFrom, $this->periodTo])
            ->get();

        $reportData = [
            'cost_overview' => $this->generateCostOverview($dispatchedItems, $productionRecords),
            'ingredient_costs' => $this->generateIngredientCosts($dispatchedItems),
            'product_costs' => $this->generateProductCosts($productionRecords),
            'cost_efficiency' => $this->generateCostEfficiency($productionRecords),
            'cost_trends' => $this->generateCostTrends($dispatchedItems, $productionRecords),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'total_days' => \Carbon\Carbon::parse($this->periodFrom)
                    ->diffInDays(\Carbon\Carbon::parse($this->periodTo)) + 1,
            ],
        ];

        // Generate summary metrics
        $reportData['summary_metrics'] = $this->generateSummaryMetrics($reportData);

        return $reportData;
    }

    /**
     * Generate cost overview.
     */
    private function generateCostOverview($dispatchedItems, $productionRecords): array
    {
        $totalDispatchedCost = $dispatchedItems->sum(function ($item) {
            return $item->quantity_dispatched * ($item->item->cost_per_unit ?? 0);
        });

        $totalProductionCost = $productionRecords->sum(function ($record) {
            return $record->quantity_produced * ($record->unit_cost ?? 0);
        });

        return [
            'total_material_cost' => $totalDispatchedCost,
            'total_production_cost' => $totalProductionCost,
            'total_cost' => $totalDispatchedCost + $totalProductionCost,
            'items_dispatched' => $dispatchedItems->count(),
            'batches_produced' => $productionRecords->count(),
            'cost_per_unit' => $productionRecords->sum('quantity_produced') > 0 ?
                ($totalDispatchedCost + $totalProductionCost) / $productionRecords->sum('quantity_produced') : 0,
        ];
    }

    /**
     * Generate ingredient costs analysis.
     */
    private function generateIngredientCosts($dispatchedItems): array
    {
        return $dispatchedItems->groupBy('item_id')->map(function ($itemDispatches) {
            $item = $itemDispatches->first()->item;
            $totalDispatched = $itemDispatches->sum('quantity_dispatched');
            $totalCost = $totalDispatched * ($item->cost_per_unit ?? 0);

            return [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'unit_cost' => $item->cost_per_unit ?? 0,
                'total_dispatched' => $totalDispatched,
                'total_cost' => $totalCost,
                'dispatches_count' => $itemDispatches->count(),
            ];
        })->sortByDesc('total_cost')->values()->toArray();
    }

    /**
     * Generate product costs analysis.
     */
    private function generateProductCosts($productionRecords): array
    {
        return $productionRecords->groupBy('recipe_id')->map(function ($recipeRecords) {
            $recipe = $recipeRecords->first()->recipe;
            $totalProduced = $recipeRecords->sum('quantity_produced');
            $totalCost = $recipeRecords->sum(function ($record) {
                return $record->quantity_produced * ($record->unit_cost ?? 0);
            });

            return [
                'recipe_id' => $recipe->id ?? null,
                'product_name' => $recipe->product_name ?? 'Unknown',
                'total_produced' => $totalProduced,
                'total_cost' => $totalCost,
                'cost_per_unit' => $totalProduced > 0 ? $totalCost / $totalProduced : 0,
                'batches_count' => $recipeRecords->count(),
            ];
        })->sortByDesc('total_cost')->values()->toArray();
    }

    /**
     * Generate cost efficiency analysis.
     */
    private function generateCostEfficiency($productionRecords): array
    {
        $totalCost = $productionRecords->sum(function ($record) {
            return $record->quantity_produced * ($record->unit_cost ?? 0);
        });
        $totalProduced = $productionRecords->sum('quantity_produced');

        return [
            'total_production_cost' => $totalCost,
            'total_units_produced' => $totalProduced,
            'average_cost_per_unit' => $totalProduced > 0 ? $totalCost / $totalProduced : 0,
            'cost_efficiency_score' => $totalProduced > 0 ? min(100, 1000 / ($totalCost / $totalProduced)) : 0,
        ];
    }

    /**
     * Generate cost trends.
     */
    private function generateCostTrends($dispatchedItems, $productionRecords): array
    {
        // Group by date for cost trends
        $dailyCosts = collect();

        // Add dispatched item costs
        foreach ($dispatchedItems as $item) {
            $date = \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d');
            $cost = $item->quantity_dispatched * ($item->item->cost_per_unit ?? 0);
            $dailyCosts[$date] = ($dailyCosts[$date] ?? 0) + $cost;
        }

        // Add production costs
        foreach ($productionRecords as $record) {
            $date = \Carbon\Carbon::parse($record->production_time)->format('Y-m-d');
            $cost = $record->quantity_produced * ($record->unit_cost ?? 0);
            $dailyCosts[$date] = ($dailyCosts[$date] ?? 0) + $cost;
        }

        return [
            'daily_costs' => $dailyCosts->map(function ($cost, $date) {
                return ['date' => $date, 'cost' => $cost];
            })->values()->toArray(),
        ];
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $costOverview = $reportData['cost_overview'];

        return [
            'total_material_cost' => $costOverview['total_material_cost'],
            'total_production_cost' => $costOverview['total_production_cost'],
            'total_cost' => $costOverview['total_cost'],
            'cost_per_unit' => $costOverview['cost_per_unit'],
            'items_tracked' => $costOverview['items_dispatched'],
            'products_analyzed' => count($reportData['product_costs']),
            'cost_efficiency_score' => $reportData['cost_efficiency']['cost_efficiency_score'],
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        return [
            'cost_breakdown_chart' => [
                'type' => 'pie',
                'labels' => ['Material Costs', 'Production Costs'],
                'data' => [
                    $reportData['cost_overview']['total_material_cost'],
                    $reportData['cost_overview']['total_production_cost'],
                ],
                'colors' => ['#3b82f6', '#10b981'],
            ],
            'ingredient_cost_chart' => [
                'type' => 'bar',
                'labels' => array_column(array_slice($reportData['ingredient_costs'], 0, 10), 'item_name'),
                'datasets' => [
                    [
                        'label' => 'Total Cost',
                        'data' => array_column(array_slice($reportData['ingredient_costs'], 0, 10), 'total_cost'),
                        'color' => '#8b5cf6',
                    ],
                ],
            ],
            'cost_trends_chart' => [
                'type' => 'line',
                'labels' => array_column($reportData['cost_trends']['daily_costs'], 'date'),
                'datasets' => [
                    [
                        'label' => 'Daily Costs',
                        'data' => array_column($reportData['cost_trends']['daily_costs'], 'cost'),
                        'color' => '#ef4444',
                    ],
                ],
            ],
        ];
    }
            })
            ->whereBetween('production_time', [$this->periodFrom, $this->periodTo])
            ->get();

        $reportData = [
            'daily_summary' => $this->generateDailySummary($dailyProduces),
            'product_efficiency' => $this->generateProductEfficiency($dailyProduces),
            'shift_performance' => $this->generateShiftPerformance($dailyProduces),
            'variance_analysis' => $this->generateVarianceAnalysis($dailyProduces),
            'employee_performance' => $this->generateEmployeePerformance($productionRecords),
            'trends' => $this->generateTrends($dailyProduces),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'total_days' => \Carbon\Carbon::parse($this->periodFrom)
                    ->diffInDays(\Carbon\Carbon::parse($this->periodTo)) + 1,
            ],
        ];

        // Generate summary metrics
        $reportData['summary_metrics'] = $this->generateSummaryMetrics($reportData);

        return $reportData;
    }

    /**
     * Generate daily summary.
     */
    private function generateDailySummary($dailyProduces): array
    {
        return $dailyProduces->groupBy('produce_date')->map(function ($dayProduces) {
            $planned = $dayProduces->sum('requested_quantity');
            $actual = $dayProduces->sum('produced_quantity');
            $variance = $dayProduces->sum('variance');

            return [
                'date' => $dayProduces->first()->produce_date->format('Y-m-d'),
                'planned' => $planned,
                'actual' => $actual,
                'variance' => $variance,
                'efficiency_percentage' => $planned > 0 ? round(($actual / $planned) * 100, 2) : 0,
                'products_count' => $dayProduces->count(),
            ];
        })->values()->toArray();
    }

    /**
     * Generate product efficiency analysis.
     */
    private function generateProductEfficiency($dailyProduces): array
    {
        return $dailyProduces->groupBy('recipe_id')->map(function ($recipeProduces) {
            $recipe = $recipeProduces->first()->recipe;
            $planned = $recipeProduces->sum('requested_quantity');
            $actual = $recipeProduces->sum('produced_quantity');

            return [
                'product_id' => $recipe->id ?? null,
                'product_name' => $recipe->product_name ?? 'Unknown Recipe',
                'planned' => $planned,
                'actual' => $actual,
                'variance' => $actual - $planned,
                'efficiency_percentage' => $planned > 0 ? round(($actual / $planned) * 100, 2) : 0,
                'production_days' => $recipeProduces->count(),
            ];
        })->sortByDesc('actual')->values()->toArray();
    }

    /**
     * Generate shift performance analysis.
     */
    private function generateShiftPerformance($dailyProduces): array
    {
        return $dailyProduces->groupBy('shift_id')->map(function ($shiftProduces) {
            $shift = $shiftProduces->first()->shift;
            $planned = $shiftProduces->sum('requested_quantity');
            $actual = $shiftProduces->sum('produced_quantity');

            return [
                'shift_id' => $shift->id ?? null,
                'shift_name' => $shift->name ?? 'Unknown',
                'shift_type' => $shift->shift_type ?? 'unknown',
                'planned' => $planned,
                'actual' => $actual,
                'variance' => $actual - $planned,
                'efficiency_percentage' => $planned > 0 ? round(($actual / $planned) * 100, 2) : 0,
                'days_worked' => $shiftProduces->unique('produce_date')->count(),
            ];
        })->values()->toArray();
    }

    /**
     * Generate variance analysis.
     */
    private function generateVarianceAnalysis($dailyProduces): array
    {
        $positiveVariance = $dailyProduces->where('variance', '>', 0);
        $negativeVariance = $dailyProduces->where('variance', '<', 0);
        $zeroVariance = $dailyProduces->where('variance', '=', 0);

        return [
            'over_production' => [
                'count' => $positiveVariance->count(),
                'total_variance' => $positiveVariance->sum('variance'),
                'percentage' => $this->calculatePercentage($positiveVariance->count(), $dailyProduces->count()),
            ],
            'under_production' => [
                'count' => $negativeVariance->count(),
                'total_variance' => abs($negativeVariance->sum('variance')),
                'percentage' => $this->calculatePercentage($negativeVariance->count(), $dailyProduces->count()),
            ],
            'on_target' => [
                'count' => $zeroVariance->count(),
                'percentage' => $this->calculatePercentage($zeroVariance->count(), $dailyProduces->count()),
            ],
        ];
    }

    /**
     * Generate employee performance.
     */
    private function generateEmployeePerformance($productionRecords): array
    {
        return $productionRecords->groupBy('produced_by')->map(function ($employeeRecords) {
            $employee = $employeeRecords->first()->producedBy;

            return [
                'employee_id' => $employee->id ?? null,
                'employee_name' => $employee->name ?? 'Unknown',
                'total_batches' => $employeeRecords->count(),
                'total_produced' => $employeeRecords->sum('quantity_produced'),
                'total_approved' => $employeeRecords->sum('quantity_approved'),
                'total_rejected' => $employeeRecords->sum('quantity_rejected'),
                'approval_rate' => $this->calculateApprovalRate($employeeRecords),
            ];
        })->sortByDesc('total_produced')->values()->toArray();
    }

    /**
     * Generate trends data.
     */
    private function generateTrends($dailyProduces): array
    {
        $weeklyTrends = $dailyProduces->groupBy(function ($item) {
            return $item->produce_date->startOfWeek()->format('Y-m-d');
        })->map(function ($weekProduces, $week) {
            $planned = $weekProduces->sum('requested_quantity');
            $actual = $weekProduces->sum('produced_quantity');

            return [
                'week_start' => $week,
                'planned' => $planned,
                'actual' => $actual,
                'efficiency' => $planned > 0 ? round(($actual / $planned) * 100, 2) : 0,
            ];
        })->values()->toArray();

        return ['weekly' => $weeklyTrends];
    }

    /**
     * Calculate approval rate for employee records.
     */
    private function calculateApprovalRate($records): float
    {
        $totalProduced = $records->sum('quantity_produced');
        $totalApproved = $records->sum('quantity_approved');

        return $totalProduced > 0 ? round(($totalApproved / $totalProduced) * 100, 2) : 0;
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $dailySummary = collect($reportData['daily_summary']);

        $totalPlanned = $dailySummary->sum('planned');
        $totalActual = $dailySummary->sum('actual');
        $totalVariance = $totalActual - $totalPlanned;
        $overallEfficiency = $totalPlanned > 0 ? round(($totalActual / $totalPlanned) * 100, 2) : 0;

        return [
            'total_planned' => $totalPlanned,
            'total_actual' => $totalActual,
            'total_variance' => $totalVariance,
            'overall_efficiency' => $overallEfficiency,
            'average_daily_production' => $dailySummary->avg('actual'),
            'best_day' => $dailySummary->sortByDesc('actual')->first(),
            'worst_day' => $dailySummary->sortBy('actual')->first(),
            'products_tracked' => count($reportData['product_efficiency']),
            'shifts_analyzed' => count($reportData['shift_performance']),
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        return [
            'daily_efficiency_chart' => [
                'type' => 'line',
                'labels' => array_column($reportData['daily_summary'], 'date'),
                'datasets' => [
                    [
                        'label' => 'Planned',
                        'data' => array_column($reportData['daily_summary'], 'planned'),
                        'color' => '#3b82f6',
                    ],
                    [
                        'label' => 'Actual',
                        'data' => array_column($reportData['daily_summary'], 'actual'),
                        'color' => '#10b981',
                    ],
                ],
            ],
            'product_efficiency_chart' => [
                'type' => 'bar',
                'labels' => array_column(array_slice($reportData['product_efficiency'], 0, 10), 'product_name'),
                'datasets' => [
                    [
                        'label' => 'Efficiency %',
                        'data' => array_column(array_slice($reportData['product_efficiency'], 0, 10), 'efficiency_percentage'),
                        'color' => '#8b5cf6',
                    ],
                ],
            ],
            'variance_distribution' => [
                'type' => 'pie',
                'labels' => ['Over Production', 'Under Production', 'On Target'],
                'data' => [
                    $reportData['variance_analysis']['over_production']['percentage'],
                    $reportData['variance_analysis']['under_production']['percentage'],
                    $reportData['variance_analysis']['on_target']['percentage'],
                ],
                'colors' => ['#3b82f6', '#ef4444', '#10b981'],
            ],
        ];
    }
}
