<?php

namespace App\Services\Reports\Definitions;

use App\Models\Item;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryStockLevelsDefinition implements ReportDefinition
{
    public function meta(): array
    {
        return [
            'name' => 'Stock Levels Report',
            'type' => 'stock_levels',
            'category' => 'inventory',
            'requires_department' => false,
            'permissions' => ['view-reports', 'generate-reports'],
        ];
    }

    public function query(array $context): array
    {
        $items = Item::query()
            ->where('branch_id', $context['branch_id'])
            ->with(['stocks' => function ($q) {
                $q->where('quantity_available', '>', 0);
            }])
            ->get();

        $stockData = [];
        $lowStockItems = [];
        $outOfStockItems = [];
        $overStockItems = [];

        foreach ($items as $item) {
            $currentStock = $item->stocks->sum(function ($stock) {
                return ($stock->quantity_available ?? 0)
                    + ($stock->quantity_reserved ?? 0)
                    + ($stock->quantity_damaged ?? 0);
            });
            $minStock = $item->min_stock_level ?? 0;
            $maxStock = $item->max_stock_level ?? 0;
            $reorderPoint = $item->reorder_point ?? 0;

            $stockStatus = $this->determineStockStatus($currentStock, $minStock, $maxStock, $reorderPoint);

            $itemData = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'sku' => $item->sku,
                'category' => $item->category ?? 'Uncategorized',
                'supplier' => 'N/A',
                'current_stock' => $currentStock,
                'min_stock_level' => $minStock,
                'max_stock_level' => $maxStock,
                'reorder_point' => $reorderPoint,
                'stock_status' => $stockStatus,
                'uom' => $item->uom,
                'unit_cost' => $item->unit_cost ?? 0,
                'stock_value' => $currentStock * ($item->unit_cost ?? 0),
                'days_of_stock' => $this->calculateDaysOfStock($item->id, $currentStock),
            ];

            $stockData[] = $itemData;

            if ($stockStatus === 'out_of_stock') {
                $outOfStockItems[] = $itemData;
            } elseif ($stockStatus === 'low_stock' || $stockStatus === 'below_reorder') {
                $lowStockItems[] = $itemData;
            } elseif ($stockStatus === 'overstock') {
                $overStockItems[] = $itemData;
            }
        }

        return [
            'stock_overview' => $this->buildStockOverview($stockData),
            'all_items' => $stockData,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'overstock_items' => $overStockItems,
            'category_breakdown' => $this->buildCategoryBreakdown($stockData),
            'value_analysis' => $this->buildValueAnalysis($stockData),
            'period_info' => [
                'from' => $context['period_from'],
                'to' => $context['period_to'],
            ],
        ];
    }

    public function summary(array $data, array $context): array
    {
        $overview = $data['stock_overview'] ?? [];

        return [
            'total_items' => $overview['total_items'] ?? 0,
            'total_value' => $overview['total_stock_value'] ?? 0,
            'stock_health' => $overview['stock_health_percentage'] ?? 0,
            'items_needing_attention' => ($overview['low_stock_items'] ?? 0) + ($overview['out_of_stock_items'] ?? 0),
            'critical_items' => $overview['out_of_stock_items'] ?? 0,
            'overstock_items' => $overview['overstock_items'] ?? 0,
        ];
    }

    public function charts(array $data, array $context): array
    {
        $overview = $data['stock_overview'] ?? [];
        $categories = array_slice($data['category_breakdown'] ?? [], 0, 10);

        return [
            'stock_status_chart' => [
                'type' => 'doughnut',
                'labels' => ['Optimal', 'Low Stock', 'Out of Stock', 'Overstock'],
                'data' => [
                    $overview['optimal_stock_items'] ?? 0,
                    $overview['low_stock_items'] ?? 0,
                    $overview['out_of_stock_items'] ?? 0,
                    $overview['overstock_items'] ?? 0,
                ],
                'colors' => ['#22c55e', '#eab308', '#ef4444', '#f59e0b'],
            ],
            'category_value_chart' => [
                'type' => 'bar',
                'labels' => array_column($categories, 'category'),
                'datasets' => [
                    [
                        'label' => 'Stock Value',
                        'data' => array_column($categories, 'total_value'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'abc_analysis_chart' => [
                'type' => 'pie',
                'labels' => ['A Items (High Value)', 'B Items (Medium Value)', 'C Items (Low Value)'],
                'data' => [
                    $data['value_analysis']['a_items']['percentage'] ?? 0,
                    $data['value_analysis']['b_items']['percentage'] ?? 0,
                    $data['value_analysis']['c_items']['percentage'] ?? 0,
                ],
                'colors' => ['#ef4444', '#f59e0b', '#22c55e'],
            ],
        ];
    }

    public function tables(array $data, array $summary, array $context): array
    {
        return [
            'all_items' => [
                'headers' => ['Item', 'SKU', 'Category', 'Supplier', 'Stock', 'Min', 'Max', 'Reorder', 'Status', 'Value'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['sku'] ?? '-',
                        $row['category'] ?? '-',
                        $row['supplier'] ?? '-',
                        $row['current_stock'] ?? 0,
                        $row['min_stock_level'] ?? 0,
                        $row['max_stock_level'] ?? 0,
                        $row['reorder_point'] ?? 0,
                        $row['stock_status'] ?? '-',
                        $row['stock_value'] ?? 0,
                    ];
                }, $data['all_items'] ?? []),
            ],
            'low_stock_items' => [
                'headers' => ['Item', 'Stock', 'Reorder', 'Status'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['current_stock'] ?? 0,
                        $row['reorder_point'] ?? 0,
                        $row['stock_status'] ?? '-',
                    ];
                }, $data['low_stock_items'] ?? []),
            ],
        ];
    }

    public function narrative(array $data, array $summary, array $context): array
    {
        $highlights = [];
        $concerns = [];

        $health = $summary['stock_health'] ?? 0;
        if ($health >= 90) {
            $highlights[] = "Stock health is strong at {$health}%.";
        } elseif ($health < 70) {
            $concerns[] = "Stock health is low at {$health}%.";
        }

        if (($summary['critical_items'] ?? 0) > 0) {
            $concerns[] = 'There are out‑of‑stock items that need urgent attention.';
        }

        return [
            'overview' => "Stock levels snapshot from {$context['period_from']} to {$context['period_to']}.",
            'highlights' => $highlights,
            'concerns' => $concerns,
            'recommendations' => [
                'Review reorder points for low stock items.',
            ],
        ];
    }

    private function determineStockStatus($currentStock, $minStock, $maxStock, $reorderPoint): string
    {
        if ($currentStock <= 0) {
            return 'out_of_stock';
        }

        if ($reorderPoint > 0 && $currentStock <= $reorderPoint) {
            return 'below_reorder';
        }

        if ($minStock > 0 && $currentStock < $minStock) {
            return 'low_stock';
        }

        if ($maxStock > 0 && $currentStock > $maxStock) {
            return 'overstock';
        }

        return 'optimal';
    }

    private function calculateDaysOfStock($itemId, $currentStock): ?float
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalConsumed = StockMovement::whereHas('stock', function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            })
            ->where('type', 'out')
            ->whereBetween('movement_date', [$thirtyDaysAgo, now()])
            ->sum('quantity');

        if ($totalConsumed <= 0) {
            return null;
        }

        $avgDailyConsumption = $totalConsumed / 30;

        if ($avgDailyConsumption <= 0) {
            return null;
        }

        return round($currentStock / $avgDailyConsumption, 1);
    }

    private function buildStockOverview(array $stockData): array
    {
        $totalItems = count($stockData);
        $totalValue = array_sum(array_column($stockData, 'stock_value'));

        $statusCounts = [
            'optimal' => 0,
            'low_stock' => 0,
            'below_reorder' => 0,
            'out_of_stock' => 0,
            'overstock' => 0,
        ];

        foreach ($stockData as $item) {
            $statusCounts[$item['stock_status']]++;
        }

        return [
            'total_items' => $totalItems,
            'total_stock_value' => $totalValue,
            'optimal_stock_items' => $statusCounts['optimal'],
            'low_stock_items' => $statusCounts['low_stock'] + $statusCounts['below_reorder'],
            'out_of_stock_items' => $statusCounts['out_of_stock'],
            'overstock_items' => $statusCounts['overstock'],
            'stock_health_percentage' => $totalItems > 0
                ? round(($statusCounts['optimal'] / $totalItems) * 100, 2)
                : 0,
        ];
    }

    private function buildCategoryBreakdown(array $stockData): array
    {
        $categories = [];

        foreach ($stockData as $item) {
            $category = $item['category'];

            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'category' => $category,
                    'item_count' => 0,
                    'total_value' => 0,
                    'low_stock_count' => 0,
                    'out_of_stock_count' => 0,
                ];
            }

            $categories[$category]['item_count']++;
            $categories[$category]['total_value'] += $item['stock_value'];

            if (in_array($item['stock_status'], ['low_stock', 'below_reorder'])) {
                $categories[$category]['low_stock_count']++;
            }

            if ($item['stock_status'] === 'out_of_stock') {
                $categories[$category]['out_of_stock_count']++;
            }
        }

        usort($categories, function ($a, $b) {
            return $b['total_value'] <=> $a['total_value'];
        });

        return array_values($categories);
    }

    private function buildValueAnalysis(array $stockData): array
    {
        usort($stockData, function ($a, $b) {
            return $b['stock_value'] <=> $a['stock_value'];
        });

        $totalValue = array_sum(array_column($stockData, 'stock_value'));
        $cumulativeValue = 0;
        $aItems = [];
        $bItems = [];
        $cItems = [];

        foreach ($stockData as $item) {
            $cumulativeValue += $item['stock_value'];
            $cumulativePercentage = $totalValue > 0 ? ($cumulativeValue / $totalValue) * 100 : 0;

            if ($cumulativePercentage <= 80) {
                $aItems[] = $item;
            } elseif ($cumulativePercentage <= 95) {
                $bItems[] = $item;
            } else {
                $cItems[] = $item;
            }
        }

        return [
            'a_items' => [
                'count' => count($aItems),
                'value' => array_sum(array_column($aItems, 'stock_value')),
                'percentage' => $totalValue > 0
                    ? round((array_sum(array_column($aItems, 'stock_value')) / $totalValue) * 100, 2)
                    : 0,
            ],
            'b_items' => [
                'count' => count($bItems),
                'value' => array_sum(array_column($bItems, 'stock_value')),
                'percentage' => $totalValue > 0
                    ? round((array_sum(array_column($bItems, 'stock_value')) / $totalValue) * 100, 2)
                    : 0,
            ],
            'c_items' => [
                'count' => count($cItems),
                'value' => array_sum(array_column($cItems, 'stock_value')),
                'percentage' => $totalValue > 0
                    ? round((array_sum(array_column($cItems, 'stock_value')) / $totalValue) * 100, 2)
                    : 0,
            ],
        ];
    }
}
