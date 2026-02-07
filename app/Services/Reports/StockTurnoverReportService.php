<?php

namespace App\Services\Reports;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockTurnoverReportService extends ReportService
{
    protected string $reportCategory = 'inventory';

    protected string $reportType = 'stock_turnover';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Stock Turnover Report';
    }

    /**
     * Generate the stock turnover report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        $items = Item::query()
            ->where('branch_id', $this->branchId)
            ->with(['stocks'])
            ->get();

        $turnoverData = [];
        $fastMovers = [];
        $slowMovers = [];
        $nonMovers = [];

        foreach ($items as $item) {
            $turnoverMetrics = $this->calculateTurnoverMetrics($item);

            if ($turnoverMetrics) {
                $turnoverData[] = $turnoverMetrics;

                // Categorize by turnover rate
                if ($turnoverMetrics['turnover_rate'] >= 12) {
                    $fastMovers[] = $turnoverMetrics;
                } elseif ($turnoverMetrics['turnover_rate'] > 0) {
                    $slowMovers[] = $turnoverMetrics;
                } else {
                    $nonMovers[] = $turnoverMetrics;
                }
            }
        }

        // Sort by turnover rate
        usort($turnoverData, function ($a, $b) {
            return $b['turnover_rate'] <=> $a['turnover_rate'];
        });

        usort($fastMovers, function ($a, $b) {
            return $b['turnover_rate'] <=> $a['turnover_rate'];
        });

        usort($slowMovers, function ($a, $b) {
            return $b['turnover_rate'] <=> $a['turnover_rate'];
        });

        return [
            'turnover_overview' => $this->generateTurnoverOverview($turnoverData),
            'all_items' => $turnoverData,
            'fast_movers' => array_slice($fastMovers, 0, 20),
            'slow_movers' => array_slice($slowMovers, 0, 20),
            'non_movers' => $nonMovers,
            'category_turnover' => $this->generateCategoryTurnover($turnoverData),
            'turnover_trends' => $this->generateTurnoverTrends(),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'days' => Carbon::parse($this->periodFrom)->diffInDays(Carbon::parse($this->periodTo)) + 1,
            ],
        ];
    }

    /**
     * Calculate turnover metrics for an item.
     */
    private function calculateTurnoverMetrics(Item $item): ?array
    {
        $currentStock = $item->stocks->sum(function ($stock) {
            return ($stock->quantity_available ?? 0) + ($stock->quantity_reserved ?? 0) + ($stock->quantity_damaged ?? 0);
        });

        // Calculate total consumed in period
        $consumed = StockMovement::whereHas('stock', function ($q) use ($item) {
                $q->where('item_id', $item->id);
            })
            ->where('type', 'out')
            ->whereBetween('movement_date', [$this->periodFrom, $this->periodTo])
            ->sum('quantity');

        // Calculate average stock during period
        $averageStock = $this->calculateAverageStock($item->id);

        if ($averageStock <= 0) {
            return null;
        }

        // Calculate days in period
        $days = Carbon::parse($this->periodFrom)->diffInDays(Carbon::parse($this->periodTo)) + 1;

        // Turnover rate = (Consumed / Average Stock) * (365 / Days)
        $turnoverRate = ($consumed / $averageStock) * (365 / $days);

        // Days of inventory = 365 / Turnover Rate
        $daysOfInventory = $turnoverRate > 0 ? 365 / $turnoverRate : 0;

        return [
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sku' => $item->sku,
            'category' => $item->category ?? 'Uncategorized',
            'current_stock' => $currentStock,
            'average_stock' => round($averageStock, 2),
            'consumed' => $consumed,
            'turnover_rate' => round($turnoverRate, 2),
            'days_of_inventory' => round($daysOfInventory, 1),
            'uom' => $item->uom,
            'unit_cost' => $item->unit_cost ?? 0,
            'stock_value' => $currentStock * ($item->unit_cost ?? 0),
            'movement_classification' => $this->classifyMovement($turnoverRate),
        ];
    }

    /**
     * Calculate average stock for an item during the period.
     */
    private function calculateAverageStock($itemId): float
    {
        // Get stock movements during period
        $movements = StockMovement::whereHas('stock', function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            })
            ->whereBetween('movement_date', [$this->periodFrom, $this->periodTo])
            ->orderBy('movement_date')
            ->get();

        if ($movements->isEmpty()) {
            // Return current stock if no movements
            return Stock::where('item_id', $itemId)->sum(
                DB::raw('COALESCE(quantity_available,0) + COALESCE(quantity_reserved,0) + COALESCE(quantity_damaged,0)')
            );
        }

        $runningStock = Stock::where('item_id', $itemId)->sum(
            DB::raw('COALESCE(quantity_available,0) + COALESCE(quantity_reserved,0) + COALESCE(quantity_damaged,0)')
        );
        $totalStock = 0;
        $days = 0;

        // Calculate average using daily snapshots
        $currentDate = Carbon::parse($this->periodFrom);
        $endDate = Carbon::parse($this->periodTo);

        while ($currentDate <= $endDate) {
            $totalStock += $runningStock;
            $days++;

            // Apply movements for this date
            $dayMovements = $movements->where('movement_date', $currentDate->toDateString());
            foreach ($dayMovements as $movement) {
                if ($movement->type === 'in') {
                    $runningStock += $movement->quantity;
                } else {
                    $runningStock -= $movement->quantity;
                }
            }

            $currentDate->addDay();
        }

        return $days > 0 ? $totalStock / $days : 0;
    }

    /**
     * Classify movement based on turnover rate.
     */
    private function classifyMovement($turnoverRate): string
    {
        if ($turnoverRate >= 12) {
            return 'fast_mover'; // More than 12 turnovers per year
        } elseif ($turnoverRate >= 4) {
            return 'medium_mover'; // 4-12 turnovers per year
        } elseif ($turnoverRate > 0) {
            return 'slow_mover'; // Less than 4 turnovers per year
        }

        return 'non_mover'; // No movement
    }

    /**
     * Generate turnover overview.
     */
    private function generateTurnoverOverview(array $turnoverData): array
    {
        if (empty($turnoverData)) {
            return [
                'total_items' => 0,
                'average_turnover_rate' => 0,
                'average_days_of_inventory' => 0,
                'fast_movers_count' => 0,
                'slow_movers_count' => 0,
                'non_movers_count' => 0,
                'total_stock_value' => 0,
            ];
        }

        $totalItems = count($turnoverData);
        $avgTurnoverRate = array_sum(array_column($turnoverData, 'turnover_rate')) / $totalItems;
        $avgDaysOfInventory = array_sum(array_column($turnoverData, 'days_of_inventory')) / $totalItems;

        $classifications = array_count_values(array_column($turnoverData, 'movement_classification'));

        return [
            'total_items' => $totalItems,
            'average_turnover_rate' => round($avgTurnoverRate, 2),
            'average_days_of_inventory' => round($avgDaysOfInventory, 1),
            'fast_movers_count' => $classifications['fast_mover'] ?? 0,
            'medium_movers_count' => $classifications['medium_mover'] ?? 0,
            'slow_movers_count' => $classifications['slow_mover'] ?? 0,
            'non_movers_count' => $classifications['non_mover'] ?? 0,
            'total_stock_value' => array_sum(array_column($turnoverData, 'stock_value')),
        ];
    }

    /**
     * Generate category turnover.
     */
    private function generateCategoryTurnover(array $turnoverData): array
    {
        $categories = [];

        foreach ($turnoverData as $item) {
            $category = $item['category'];

            if (! isset($categories[$category])) {
                $categories[$category] = [
                    'category' => $category,
                    'item_count' => 0,
                    'total_consumed' => 0,
                    'avg_turnover_rate' => 0,
                    'total_stock_value' => 0,
                ];
            }

            $categories[$category]['item_count']++;
            $categories[$category]['total_consumed'] += $item['consumed'];
            $categories[$category]['avg_turnover_rate'] += $item['turnover_rate'];
            $categories[$category]['total_stock_value'] += $item['stock_value'];
        }

        // Calculate averages
        foreach ($categories as &$category) {
            if ($category['item_count'] > 0) {
                $category['avg_turnover_rate'] = round($category['avg_turnover_rate'] / $category['item_count'], 2);
            }
        }

        // Sort by average turnover rate descending
        usort($categories, function ($a, $b) {
            return $b['avg_turnover_rate'] <=> $a['avg_turnover_rate'];
        });

        return array_values($categories);
    }

    /**
     * Generate monthly turnover trends.
     */
    private function generateTurnoverTrends(): array
    {
        // This would ideally track turnover over time
        // For now, return empty as it requires historical data
        return [];
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $overview = $reportData['turnover_overview'];

        return [
            'total_items' => $overview['total_items'],
            'avg_turnover_rate' => $overview['average_turnover_rate'],
            'avg_days_inventory' => $overview['average_days_of_inventory'],
            'fast_movers' => $overview['fast_movers_count'],
            'slow_movers' => $overview['slow_movers_count'],
            'non_movers' => $overview['non_movers_count'],
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        $overview = $reportData['turnover_overview'];
        $categories = array_slice($reportData['category_turnover'], 0, 10);
        $topMovers = array_slice($reportData['all_items'], 0, 10);

        return [
            'movement_classification_chart' => [
                'type' => 'doughnut',
                'labels' => ['Fast Movers', 'Medium Movers', 'Slow Movers', 'Non-Movers'],
                'data' => [
                    $overview['fast_movers_count'],
                    $overview['medium_movers_count'] ?? 0,
                    $overview['slow_movers_count'],
                    $overview['non_movers_count'],
                ],
                'colors' => ['#22c55e', '#3b82f6', '#eab308', '#ef4444'],
            ],
            'category_turnover_chart' => [
                'type' => 'bar',
                'labels' => array_column($categories, 'category'),
                'datasets' => [
                    [
                        'label' => 'Average Turnover Rate',
                        'data' => array_column($categories, 'avg_turnover_rate'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'top_movers_chart' => [
                'type' => 'horizontalBar',
                'labels' => array_column($topMovers, 'item_name'),
                'datasets' => [
                    [
                        'label' => 'Turnover Rate',
                        'data' => array_column($topMovers, 'turnover_rate'),
                        'color' => '#22c55e',
                    ],
                ],
            ],
        ];
    }
}
