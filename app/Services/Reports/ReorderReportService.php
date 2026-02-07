<?php

namespace App\Services\Reports;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReorderReportService extends ReportService
{
    protected string $reportCategory = 'inventory';

    protected string $reportType = 'reorder';
    protected string $cacheKeyVersion = 'v2';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Reorder Report';
    }

    protected function getCacheKey(): string
    {
        return sprintf(
            'report:%s:%s:%s:%s:%s:%s:%s',
            $this->cacheKeyVersion,
            $this->reportCategory,
            $this->reportType,
            $this->branchId,
            $this->departmentId,
            $this->periodFrom,
            $this->periodTo
        );
    }

    /**
     * Generate the reorder report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        $items = Item::query()
            ->where('branch_id', $this->branchId)
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

            // Check if item needs reordering
            $needsReorder = $currentStock <= $reorderPoint || $currentStock < $minStock;

            if ($needsReorder) {
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
                    'estimated_cost' => 0, // Will be calculated below
                ];

                $itemData['estimated_cost'] = $itemData['suggested_order_qty'] * $itemData['unit_cost'];

                $reorderData[] = $itemData;

                // Categorize by priority
                if ($itemData['priority'] === 'critical') {
                    $criticalItems[] = $itemData;
                } elseif ($itemData['priority'] === 'urgent') {
                    $urgentItems[] = $itemData;
                } else {
                    $normalItems[] = $itemData;
                }
            }
        }

        // Sort by priority
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
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'report_date' => now()->toDateString(),
            ],
        ];
    }

    /**
     * Calculate suggested order quantity.
     */
    private function calculateSuggestedOrderQuantity(Item $item, $currentStock): float
    {
        $reorderQuantity = $item->reorder_quantity ?? 0;
        $maxStock = $item->max_stock_level ?? 0;
        $minStock = $item->min_stock_level ?? 0;

        // If reorder quantity is set, use it
        if ($reorderQuantity > 0) {
            return $reorderQuantity;
        }

        // Otherwise, calculate to reach max stock level
        if ($maxStock > 0) {
            $shortage = $maxStock - $currentStock;

            return max($shortage, $minStock);
        }

        // Fallback: order enough for 30 days based on avg consumption
        $avgDailyConsumption = $this->calculateAvgDailyConsumption($item->id);
        if ($avgDailyConsumption > 0) {
            return ceil($avgDailyConsumption * 30);
        }

        return $minStock > 0 ? $minStock : 10; // Default fallback
    }

    /**
     * Calculate average daily consumption.
     */
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

    /**
     * Calculate days until stockout.
     */
    private function calculateDaysUntilStockout($itemId, $currentStock): ?float
    {
        $avgDailyConsumption = $this->calculateAvgDailyConsumption($itemId);

        if ($avgDailyConsumption <= 0) {
            return null; // No consumption data
        }

        if ($currentStock <= 0) {
            return 0; // Already out of stock
        }

        return round($currentStock / $avgDailyConsumption, 1);
    }

    /**
     * Determine priority level.
     */
    private function determinePriority($currentStock, $reorderPoint, $minStock): string
    {
        if ($currentStock <= 0) {
            return 'critical'; // Out of stock
        }

        if ($currentStock < $minStock * 0.5) {
            return 'critical'; // Less than half of min stock
        }

        if ($currentStock < $minStock) {
            return 'urgent'; // Below minimum
        }

        return 'normal'; // At or below reorder point but above minimum
    }

    /**
     * Get pending purchase orders for an item.
     */
    private function getPendingOrders($itemId): array
    {
        if (!Schema::hasColumn('purchase_order_items', 'item_id')) {
            return [];
        }

        $pendingOrders = DB::table('purchase_orders')
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

        return $pendingOrders;
    }

    /**
     * Generate reorder overview.
     */
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

    /**
     * Group reorder items by supplier.
     */
    private function groupBySupplier(array $reorderData): array
    {
        $suppliers = [];

        foreach ($reorderData as $item) {
            $supplier = $item['supplier'];

            if (! isset($suppliers[$supplier])) {
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

        // Sort by total cost descending
        usort($suppliers, function ($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });

        return array_values($suppliers);
    }

    /**
     * Group reorder items by category.
     */
    private function groupByCategory(array $reorderData): array
    {
        $categories = [];

        foreach ($reorderData as $item) {
            $category = $item['category'];

            if (! isset($categories[$category])) {
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

        // Sort by critical count then total cost
        usort($categories, function ($a, $b) {
            if ($a['critical_count'] !== $b['critical_count']) {
                return $b['critical_count'] <=> $a['critical_count'];
            }

            return $b['total_cost'] <=> $a['total_cost'];
        });

        return array_values($categories);
    }

    /**
     * Generate cost analysis.
     */
    private function generateCostAnalysis(array $reorderData): array
    {
        $totalCost = array_sum(array_column($reorderData, 'estimated_cost'));

        // Sort by cost descending
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

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $overview = $reportData['reorder_overview'];

        return [
            'total_items' => $overview['total_items_to_reorder'],
            'critical_items' => $overview['critical_items'],
            'urgent_items' => $overview['urgent_items'],
            'estimated_cost' => $overview['total_estimated_cost'],
            'unique_suppliers' => $overview['unique_suppliers'],
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        $overview = $reportData['reorder_overview'];
        $suppliers = array_slice($reportData['by_supplier'], 0, 10);
        $categories = array_slice($reportData['by_category'], 0, 10);

        return [
            'priority_breakdown_chart' => [
                'type' => 'doughnut',
                'labels' => ['Critical', 'Urgent', 'Normal'],
                'data' => [
                    $overview['critical_items'],
                    $overview['urgent_items'],
                    $overview['normal_items'],
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
}
