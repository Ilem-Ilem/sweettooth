<?php

namespace App\Services\Reports\Definitions;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryStockTurnoverDefinition implements ReportDefinition
{
    public function meta(): array
    {
        return [
            'name' => 'Stock Turnover Report',
            'type' => 'stock_turnover',
            'category' => 'inventory',
            'requires_department' => false,
            'permissions' => ['view-reports', 'generate-reports'],
        ];
    }

    public function query(array $context): array
    {
        $items = Item::query()
            ->where('branch_id', $context['branch_id'])
            ->with(['stocks'])
            ->get();

        $turnoverData = [];
        $fastMovers = [];
        $slowMovers = [];
        $nonMovers = [];

        foreach ($items as $item) {
            $metrics = $this->calculateTurnoverMetrics($item, $context['period_from'], $context['period_to']);
            if (!$metrics) {
                continue;
            }

            $turnoverData[] = $metrics;

            if ($metrics['turnover_rate'] >= 12) {
                $fastMovers[] = $metrics;
            } elseif ($metrics['turnover_rate'] > 0) {
                $slowMovers[] = $metrics;
            } else {
                $nonMovers[] = $metrics;
            }
        }

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
                'from' => $context['period_from'],
                'to' => $context['period_to'],
                'days' => Carbon::parse($context['period_from'])->diffInDays(Carbon::parse($context['period_to'])) + 1,
            ],
        ];
    }

    public function summary(array $data, array $context): array
    {
        $overview = $data['turnover_overview'] ?? [];

        return [
            'total_items' => $overview['total_items'] ?? 0,
            'avg_turnover_rate' => $overview['average_turnover_rate'] ?? 0,
            'avg_days_inventory' => $overview['average_days_of_inventory'] ?? 0,
            'fast_movers' => $overview['fast_movers_count'] ?? 0,
            'slow_movers' => $overview['slow_movers_count'] ?? 0,
            'non_movers' => $overview['non_movers_count'] ?? 0,
        ];
    }

    public function charts(array $data, array $context): array
    {
        $overview = $data['turnover_overview'] ?? [];
        $categories = array_slice($data['category_turnover'] ?? [], 0, 10);
        $topMovers = array_slice($data['all_items'] ?? [], 0, 10);

        return [
            'movement_classification_chart' => [
                'type' => 'doughnut',
                'labels' => ['Fast Movers', 'Medium Movers', 'Slow Movers', 'Non-Movers'],
                'data' => [
                    $overview['fast_movers_count'] ?? 0,
                    $overview['medium_movers_count'] ?? 0,
                    $overview['slow_movers_count'] ?? 0,
                    $overview['non_movers_count'] ?? 0,
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

    public function tables(array $data, array $summary, array $context): array
    {
        return [
            'all_items' => [
                'headers' => ['Item', 'SKU', 'Category', 'Current', 'Avg Stock', 'Consumed', 'Turnover', 'Days Inv'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['sku'] ?? '-',
                        $row['category'] ?? '-',
                        $row['current_stock'] ?? 0,
                        $row['average_stock'] ?? 0,
                        $row['consumed'] ?? 0,
                        $row['turnover_rate'] ?? 0,
                        $row['days_of_inventory'] ?? 0,
                    ];
                }, $data['all_items'] ?? []),
            ],
            'fast_movers' => [
                'headers' => ['Item', 'Turnover Rate', 'Days Inv'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['turnover_rate'] ?? 0,
                        $row['days_of_inventory'] ?? 0,
                    ];
                }, $data['fast_movers'] ?? []),
            ],
        ];
    }

    public function narrative(array $data, array $summary, array $context): array
    {
        $highlights = [];
        $concerns = [];

        if (($summary['fast_movers'] ?? 0) > 0) {
            $highlights[] = 'There are strong fast‑moving items driving turnover.';
        }

        if (($summary['non_movers'] ?? 0) > 0) {
            $concerns[] = 'Non‑moving items exist and may require liquidation or re‑forecasting.';
        }

        return [
            'overview' => "Stock turnover performance from {$context['period_from']} to {$context['period_to']}.",
            'highlights' => $highlights,
            'concerns' => $concerns,
            'recommendations' => [
                'Review slow and non‑moving items for discounting or purchase reduction.',
            ],
        ];
    }

    private function calculateTurnoverMetrics(Item $item, $from, $to): ?array
    {
        $currentStock = $item->stocks->sum(function ($stock) {
            return ($stock->quantity_available ?? 0)
                + ($stock->quantity_reserved ?? 0)
                + ($stock->quantity_damaged ?? 0);
        });

        $consumed = StockMovement::whereHas('stock', function ($q) use ($item) {
                $q->where('item_id', $item->id);
            })
            ->where('type', 'out')
            ->whereBetween('movement_date', [$from, $to])
            ->sum('quantity');

        $averageStock = $this->calculateAverageStock($item->id, $from, $to);

        if ($averageStock <= 0) {
            return null;
        }

        $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
        $turnoverRate = ($consumed / $averageStock) * (365 / $days);
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

    private function calculateAverageStock($itemId, $from, $to): float
    {
        $movements = StockMovement::whereHas('stock', function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            })
            ->whereBetween('movement_date', [$from, $to])
            ->orderBy('movement_date')
            ->get();

        if ($movements->isEmpty()) {
            return Stock::where('item_id', $itemId)->sum(
                DB::raw('COALESCE(quantity_available,0) + COALESCE(quantity_reserved,0) + COALESCE(quantity_damaged,0)')
            );
        }

        $runningStock = Stock::where('item_id', $itemId)->sum(
            DB::raw('COALESCE(quantity_available,0) + COALESCE(quantity_reserved,0) + COALESCE(quantity_damaged,0)')
        );
        $totalStock = 0;
        $days = 0;

        $currentDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        while ($currentDate <= $endDate) {
            $totalStock += $runningStock;
            $days++;

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

    private function classifyMovement($turnoverRate): string
    {
        if ($turnoverRate >= 12) {
            return 'fast_mover';
        } elseif ($turnoverRate >= 4) {
            return 'medium_mover';
        } elseif ($turnoverRate > 0) {
            return 'slow_mover';
        }

        return 'non_mover';
    }

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

    private function generateCategoryTurnover(array $turnoverData): array
    {
        $categories = [];

        foreach ($turnoverData as $item) {
            $category = $item['category'];

            if (!isset($categories[$category])) {
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

        foreach ($categories as &$category) {
            if ($category['item_count'] > 0) {
                $category['avg_turnover_rate'] = round($category['avg_turnover_rate'] / $category['item_count'], 2);
            }
        }

        usort($categories, function ($a, $b) {
            return $b['avg_turnover_rate'] <=> $a['avg_turnover_rate'];
        });

        return array_values($categories);
    }

    private function generateTurnoverTrends(): array
    {
        return [];
    }
}
