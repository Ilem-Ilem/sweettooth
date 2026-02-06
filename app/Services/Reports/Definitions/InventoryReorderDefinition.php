<?php

namespace App\Services\Reports\Definitions;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryReorderDefinition implements ReportDefinition
{
    public function meta(): array
    {
        return [
            'name' => 'Reorder Report',
            'type' => 'reorder',
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

        $reorderData = [];
        $criticalItems = [];
        $urgentItems = [];
        $normalItems = [];

        foreach ($items as $item) {
            $currentStock = $item->stocks->sum(DB::raw('quantity_available + quantity_reserved + quantity_damaged'));
            $reorderPoint = $item->reorder_point ?? 0;
            $minStock = $item->min_stock_level ?? 0;
            $reorderQuantity = $item->reorder_quantity ?? 0;

            $needsReorder = $currentStock <= $reorderPoint || $currentStock < $minStock;
            if (! $needsReorder) {
                continue;
            }

            $itemData = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'sku' => $item->sku,
                'category' => $item->category ?? 'Uncategorized',
                'supplier' => 'N/A',
                'supplier_id' => null,
                'current_stock' => $currentStock,
                'min_stock_level' => $minStock,
                'reorder_point' => $reorderPoint,
                'reorder_quantity' => $reorderQuantity,
                'suggested_order_qty' => $this->calculateSuggestedOrderQuantity($item, $currentStock),
                'uom' => $item->uom,
                'unit_cost' => $item->unit_cost ?? 0,
                'lead_time_days' => $item->lead_time_days ?? 0,
                'avg_daily_consumption' => $this->calculateAvgDailyConsumption($item->id),
                'days_until_stockout' => $this->calculateDaysUntilStockout($item->id, $currentStock),
                'priority' => $this->determinePriority($currentStock, $reorderPoint, $minStock),
                'pending_orders' => $this->getPendingOrders($item->id),
                'estimated_cost' => 0,
            ];

            $itemData['estimated_cost'] = $itemData['suggested_order_qty'] * $itemData['unit_cost'];
            $reorderData[] = $itemData;

            if ($itemData['priority'] === 'critical') {
                $criticalItems[] = $itemData;
            } elseif ($itemData['priority'] === 'urgent') {
                $urgentItems[] = $itemData;
            } else {
                $normalItems[] = $itemData;
            }
        }

        usort($reorderData, function ($a, $b) {
            $priorityOrder = ['critical' => 1, 'urgent' => 2, 'normal' => 3];
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });

        return [
            'reorder_overview' => $this->generateReorderOverview($reorderData),
            'all_reorder_items' => $reorderData,
            'critical_items' => $criticalItems,
            'urgent_items' => $urgentItems,
            'normal_items' => $normalItems,
            'by_supplier' => $this->groupBySupplier($reorderData),
            'by_category' => $this->groupByCategory($reorderData),
            'cost_analysis' => $this->generateCostAnalysis($reorderData),
            'period_info' => [
                'from' => $context['period_from'],
                'to' => $context['period_to'],
                'report_date' => now()->toDateString(),
            ],
        ];
    }

    public function summary(array $data, array $context): array
    {
        $overview = $data['reorder_overview'] ?? [];

        return [
            'total_items' => $overview['total_items_to_reorder'] ?? 0,
            'critical_items' => $overview['critical_items'] ?? 0,
            'urgent_items' => $overview['urgent_items'] ?? 0,
            'estimated_cost' => $overview['total_estimated_cost'] ?? 0,
            'unique_suppliers' => $overview['unique_suppliers'] ?? 0,
        ];
    }

    public function charts(array $data, array $context): array
    {
        $overview = $data['reorder_overview'] ?? [];
        $suppliers = array_slice($data['by_supplier'] ?? [], 0, 10);
        $categories = array_slice($data['by_category'] ?? [], 0, 10);

        return [
            'priority_breakdown_chart' => [
                'type' => 'doughnut',
                'labels' => ['Critical', 'Urgent', 'Normal'],
                'data' => [
                    $overview['critical_items'] ?? 0,
                    $overview['urgent_items'] ?? 0,
                    $overview['normal_items'] ?? 0,
                ],
                'colors' => ['#ef4444', '#f59e0b', '#3b82f6'],
            ],
            'supplier_cost_chart' => [
                'type' => 'bar',
                'labels' => array_column($suppliers, 'supplier'),
                'datasets' => [
                    [
                        'label' => 'Estimated Cost',
                        'data' => array_column($suppliers, 'total_cost'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'category_breakdown_chart' => [
                'type' => 'horizontalBar',
                'labels' => array_column($categories, 'category'),
                'datasets' => [
                    [
                        'label' => 'Items to Reorder',
                        'data' => array_column($categories, 'item_count'),
                        'color' => '#22c55e',
                    ],
                ],
            ],
        ];
    }

    public function tables(array $data, array $summary, array $context): array
    {
        return [
            'reorder_items' => [
                'headers' => ['Item', 'SKU', 'Category', 'Stock', 'Reorder', 'Suggested', 'Priority', 'Cost'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['sku'] ?? '-',
                        $row['category'] ?? '-',
                        $row['current_stock'] ?? 0,
                        $row['reorder_point'] ?? 0,
                        $row['suggested_order_qty'] ?? 0,
                        $row['priority'] ?? '-',
                        $row['estimated_cost'] ?? 0,
                    ];
                }, $data['all_reorder_items'] ?? []),
            ],
            'critical_items' => [
                'headers' => ['Item', 'Stock', 'Suggested', 'Days Until Stockout'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['item_name'] ?? '-',
                        $row['current_stock'] ?? 0,
                        $row['suggested_order_qty'] ?? 0,
                        $row['days_until_stockout'] ?? null,
                    ];
                }, $data['critical_items'] ?? []),
            ],
        ];
    }

    public function narrative(array $data, array $summary, array $context): array
    {
        $highlights = [];
        $concerns = [];

        if (($summary['critical_items'] ?? 0) > 0) {
            $concerns[] = 'Critical items require immediate reorder to avoid stockouts.';
        }

        if (($summary['total_items'] ?? 0) === 0) {
            $highlights[] = 'No items currently require reorder.';
        }

        return [
            'overview' => "Reorder report generated for {$context['period_from']} to {$context['period_to']}.",
            'highlights' => $highlights,
            'concerns' => $concerns,
            'recommendations' => [
                'Place orders for critical and urgent items first.',
            ],
        ];
    }

    private function calculateSuggestedOrderQuantity(Item $item, $currentStock): float
    {
        $reorderQuantity = $item->reorder_quantity ?? 0;
        $maxStock = $item->max_stock_level ?? 0;
        $minStock = $item->min_stock_level ?? 0;

        if ($reorderQuantity > 0) {
            return $reorderQuantity;
        }

        if ($maxStock > 0) {
            $shortage = $maxStock - $currentStock;
            return max($shortage, $minStock);
        }

        $avgDailyConsumption = $this->calculateAvgDailyConsumption($item->id);
        if ($avgDailyConsumption > 0) {
            return ceil($avgDailyConsumption * 30);
        }

        return $minStock > 0 ? $minStock : 10;
    }

    private function calculateAvgDailyConsumption($itemId): float
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalConsumed = StockMovement::whereHas('stock', function ($q) use ($itemId) {
                $q->where('item_id', $itemId);
            })
            ->where('type', 'out')
            ->whereBetween('movement_date', [$thirtyDaysAgo, now()])
            ->sum('quantity');

        return round($totalConsumed / 30, 2);
    }

    private function calculateDaysUntilStockout($itemId, $currentStock): ?float
    {
        $avgDailyConsumption = $this->calculateAvgDailyConsumption($itemId);

        if ($avgDailyConsumption <= 0) {
            return null;
        }

        if ($currentStock <= 0) {
            return 0;
        }

        return round($currentStock / $avgDailyConsumption, 1);
    }

    private function determinePriority($currentStock, $reorderPoint, $minStock): string
    {
        if ($currentStock <= 0) {
            return 'critical';
        }

        if ($currentStock < $minStock * 0.5) {
            return 'critical';
        }

        if ($currentStock < $minStock) {
            return 'urgent';
        }

        return 'normal';
    }

    private function getPendingOrders($itemId): array
    {
        if (!Schema::hasColumn('purchase_order_items', 'item_id')) {
            return [];
        }

        return DB::table('purchase_orders')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->where('purchase_order_items.item_id', $itemId)
            ->whereIn('purchase_orders.status', ['pending', 'approved', 'ordered'])
            ->select(
                'purchase_orders.order_number',
                'purchase_order_items.quantity_ordered',
                'purchase_orders.expected_delivery_date'
            )
            ->get()
            ->toArray();
    }

    private function generateReorderOverview(array $reorderData): array
    {
        if (empty($reorderData)) {
            return [
                'total_items_to_reorder' => 0,
                'critical_items' => 0,
                'urgent_items' => 0,
                'normal_items' => 0,
                'total_estimated_cost' => 0,
                'unique_suppliers' => 0,
            ];
        }

        $priorities = array_count_values(array_column($reorderData, 'priority'));
        $uniqueSuppliers = count(array_unique(array_filter(array_column($reorderData, 'supplier_id'))));

        return [
            'total_items_to_reorder' => count($reorderData),
            'critical_items' => $priorities['critical'] ?? 0,
            'urgent_items' => $priorities['urgent'] ?? 0,
            'normal_items' => $priorities['normal'] ?? 0,
            'total_estimated_cost' => array_sum(array_column($reorderData, 'estimated_cost')),
            'unique_suppliers' => $uniqueSuppliers,
        ];
    }

    private function groupBySupplier(array $reorderData): array
    {
        $suppliers = [];

        foreach ($reorderData as $item) {
            $supplier = $item['supplier'];

            if (!isset($suppliers[$supplier])) {
                $suppliers[$supplier] = [
                    'supplier' => $supplier,
                    'supplier_id' => $item['supplier_id'],
                    'item_count' => 0,
                    'total_cost' => 0,
                    'items' => [],
                ];
            }

            $suppliers[$supplier]['item_count']++;
            $suppliers[$supplier]['total_cost'] += $item['estimated_cost'];
            $suppliers[$supplier]['items'][] = [
                'item_name' => $item['item_name'],
                'sku' => $item['sku'],
                'quantity' => $item['suggested_order_qty'],
                'cost' => $item['estimated_cost'],
            ];
        }

        usort($suppliers, function ($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });

        return array_values($suppliers);
    }

    private function groupByCategory(array $reorderData): array
    {
        $categories = [];

        foreach ($reorderData as $item) {
            $category = $item['category'];

            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'category' => $category,
                    'item_count' => 0,
                    'total_cost' => 0,
                    'critical_count' => 0,
                ];
            }

            $categories[$category]['item_count']++;
            $categories[$category]['total_cost'] += $item['estimated_cost'];

            if ($item['priority'] === 'critical') {
                $categories[$category]['critical_count']++;
            }
        }

        usort($categories, function ($a, $b) {
            if ($a['critical_count'] !== $b['critical_count']) {
                return $b['critical_count'] <=> $a['critical_count'];
            }

            return $b['total_cost'] <=> $a['total_cost'];
        });

        return array_values($categories);
    }

    private function generateCostAnalysis(array $reorderData): array
    {
        $totalCost = array_sum(array_column($reorderData, 'estimated_cost'));

        usort($reorderData, function ($a, $b) {
            return $b['estimated_cost'] <=> $a['estimated_cost'];
        });

        $topCostItems = array_slice($reorderData, 0, 10);

        return [
            'total_estimated_cost' => $totalCost,
            'average_item_cost' => count($reorderData) > 0 ? $totalCost / count($reorderData) : 0,
            'highest_cost_items' => $topCostItems,
        ];
    }
}
