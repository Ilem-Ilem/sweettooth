<?php

namespace App\Services\Reports;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockLevelReportService extends ReportService
{
    protected string $reportCategory = 'inventory';
    protected string $reportType = 'stock_levels';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Stock Level Report';
    }

    /**
     * Generate the stock level report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        $stocks = Stock::query()
            ->with(['item', 'branch'])
            ->where('branch_id', $this->branchId)
            ->get();

        // Get stock movements within period
        $movements = StockMovement::query()
            ->with(['stock.item'])
            ->whereHas('stock', function ($q) {
                $q->where('branch_id', $this->branchId);
            })
            ->whereBetween('movement_date', [$this->periodFrom, $this->periodTo])
            ->get();

        return [
            'stock_overview' => $this->generateStockOverview($stocks),
            'stock_by_item' => $this->generateStockByItem($stocks),
            'low_stock_alerts' => $this->generateLowStockAlerts($stocks),
            'expiry_alerts' => $this->generateExpiryAlerts($stocks),
            'damaged_stock' => $this->generateDamagedStock($stocks),
            'stock_health_analysis' => $this->generateStockHealthAnalysis($stocks),
            'stock_movements_summary' => $this->generateStockMovementsSummary($movements),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'total_days' => \Carbon\Carbon::parse($this->periodFrom)
                    ->diffInDays(\Carbon\Carbon::parse($this->periodTo)) + 1,
            ],
        ];
    }

    /**
     * Generate stock overview.
     */
    private function generateStockOverview($stocks): array
    {
        $totalItems = $stocks->count();
        $totalQuantity = $stocks->sum('quantity_available');
        $totalValue = $stocks->sum(function ($stock) {
            return $stock->quantity_available * $stock->average_cost;
        });
        $totalReserved = $stocks->sum('quantity_reserved');
        $totalDamaged = $stocks->sum('quantity_damaged');

        return [
            'total_items' => $totalItems,
            'total_quantity_available' => $totalQuantity,
            'total_quantity_reserved' => $totalReserved,
            'total_quantity_damaged' => $totalDamaged,
            'total_stock_value' => $totalValue,
            'average_stock_value_per_item' => $totalItems > 0 ? $totalValue / $totalItems : 0,
            'healthy_items' => $stocks->where('health_status', 'healthy')->count(),
            'low_stock_items' => $stocks->where('health_status', 'low')->count(),
            'critical_stock_items' => $stocks->where('health_status', 'critical')->count(),
            'out_of_stock_items' => $stocks->where('quantity_available', '<=', 0)->count(),
        ];
    }

    /**
     * Generate stock by item.
     */
    private function generateStockByItem($stocks): array
    {
        return $stocks->map(function ($stock) {
            $item = $stock->item;
            $stockValue = $stock->quantity_available * $stock->average_cost;

            return [
                'item_id' => $item->id ?? null,
                'item_name' => $item->name ?? 'Unknown Item',
                'item_code' => $item->item_code ?? 'N/A',
                'category' => $item->category ?? 'Uncategorized',
                'quantity_available' => $stock->quantity_available,
                'quantity_reserved' => $stock->quantity_reserved,
                'quantity_damaged' => $stock->quantity_damaged,
                'uom' => $item->uom ?? 'unit',
                'average_cost' => $stock->average_cost,
                'stock_value' => $stockValue,
                'health_status' => $stock->health_status,
                'last_stock_take' => $stock->last_stock_take_date?->format('Y-m-d'),
                'expiry_date' => $stock->expiry_date?->format('Y-m-d'),
                'days_until_expiry' => $stock->expiry_date
                    ? \Carbon\Carbon::now()->diffInDays($stock->expiry_date, false)
                    : null,
            ];
        })->sortByDesc('stock_value')->values()->toArray();
    }

    /**
     * Generate low stock alerts.
     */
    private function generateLowStockAlerts($stocks): array
    {
        $lowStock = $stocks->filter(function ($stock) {
            return in_array($stock->health_status, ['low', 'critical']) ||
                   $stock->quantity_available <= 0;
        });

        return $lowStock->map(function ($stock) {
            $item = $stock->item;

            return [
                'item_name' => $item->name ?? 'Unknown',
                'item_code' => $item->item_code ?? 'N/A',
                'current_quantity' => $stock->quantity_available,
                'min_quantity' => $item->min_quantity ?? 0,
                'max_quantity' => $item->max_quantity ?? 0,
                'health_status' => $stock->health_status,
                'severity' => $stock->quantity_available <= 0 ? 'critical' :
                             ($stock->health_status === 'critical' ? 'high' : 'medium'),
            ];
        })->sortBy('current_quantity')->values()->toArray();
    }

    /**
     * Generate expiry alerts.
     */
    private function generateExpiryAlerts($stocks): array
    {
        $now = \Carbon\Carbon::now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);

        $expiringStock = $stocks->filter(function ($stock) use ($now, $thirtyDaysFromNow) {
            return $stock->expiry_date &&
                   $stock->expiry_date->between($now, $thirtyDaysFromNow);
        });

        return $expiringStock->map(function ($stock) use ($now) {
            $item = $stock->item;
            $daysUntilExpiry = $now->diffInDays($stock->expiry_date, false);

            return [
                'item_name' => $item->name ?? 'Unknown',
                'item_code' => $item->item_code ?? 'N/A',
                'quantity' => $stock->quantity_available,
                'expiry_date' => $stock->expiry_date->format('Y-m-d'),
                'days_until_expiry' => $daysUntilExpiry,
                'severity' => $daysUntilExpiry <= 7 ? 'critical' :
                             ($daysUntilExpiry <= 14 ? 'high' : 'medium'),
                'stock_value' => $stock->quantity_available * $stock->average_cost,
            ];
        })->sortBy('days_until_expiry')->values()->toArray();
    }

    /**
     * Generate damaged stock report.
     */
    private function generateDamagedStock($stocks): array
    {
        $damagedStock = $stocks->where('quantity_damaged', '>', 0);

        $totalDamagedQuantity = $damagedStock->sum('quantity_damaged');
        $totalDamagedValue = $damagedStock->sum(function ($stock) {
            return $stock->quantity_damaged * $stock->average_cost;
        });

        return [
            'total_damaged_items' => $damagedStock->count(),
            'total_damaged_quantity' => $totalDamagedQuantity,
            'total_damaged_value' => $totalDamagedValue,
            'items' => $damagedStock->map(function ($stock) {
                $item = $stock->item;

                return [
                    'item_name' => $item->name ?? 'Unknown',
                    'item_code' => $item->item_code ?? 'N/A',
                    'damaged_quantity' => $stock->quantity_damaged,
                    'average_cost' => $stock->average_cost,
                    'damaged_value' => $stock->quantity_damaged * $stock->average_cost,
                ];
            })->sortByDesc('damaged_value')->values()->toArray(),
        ];
    }

    /**
     * Generate stock health analysis.
     */
    private function generateStockHealthAnalysis($stocks): array
    {
        $healthCategories = $stocks->groupBy('health_status');

        return $healthCategories->map(function ($items, $status) use ($stocks) {
            $count = $items->count();
            $totalItems = $stocks->count();

            return [
                'status' => $status ?: 'unknown',
                'count' => $count,
                'percentage' => $totalItems > 0 ? ($count / $totalItems) * 100 : 0,
                'total_quantity' => $items->sum('quantity_available'),
                'total_value' => $items->sum(function ($stock) {
                    return $stock->quantity_available * $stock->average_cost;
                }),
            ];
        })->values()->toArray();
    }

    /**
     * Generate stock movements summary.
     */
    private function generateStockMovementsSummary($movements): array
    {
        $stockIn = $movements->whereIn('type', ['purchase', 'return', 'adjustment_increase']);
        $stockOut = $movements->whereIn('type', ['sale', 'wastage', 'adjustment_decrease']);

        return [
            'total_movements' => $movements->count(),
            'stock_in_count' => $stockIn->count(),
            'stock_out_count' => $stockOut->count(),
            'stock_in_quantity' => $stockIn->sum('quantity'),
            'stock_out_quantity' => $stockOut->sum('quantity'),
            'net_movement' => $stockIn->sum('quantity') - $stockOut->sum('quantity'),
        ];
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $overview = $reportData['stock_overview'];
        $lowStockAlerts = count($reportData['low_stock_alerts']);
        $expiryAlerts = count($reportData['expiry_alerts']);
        $damagedStock = $reportData['damaged_stock'];

        return [
            'total_items' => $overview['total_items'],
            'total_stock_value' => $overview['total_stock_value'],
            'total_available_quantity' => $overview['total_quantity_available'],
            'low_stock_alerts' => $lowStockAlerts,
            'expiry_alerts' => $expiryAlerts,
            'out_of_stock_items' => $overview['out_of_stock_items'],
            'total_damaged_value' => $damagedStock['total_damaged_value'],
            'healthy_stock_percentage' => $overview['total_items'] > 0
                ? ($overview['healthy_items'] / $overview['total_items']) * 100
                : 0,
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        $healthAnalysis = $reportData['stock_health_analysis'];
        $topItems = array_slice($reportData['stock_by_item'], 0, 10);

        return [
            'stock_health_distribution' => [
                'type' => 'pie',
                'labels' => array_column($healthAnalysis, 'status'),
                'data' => array_column($healthAnalysis, 'count'),
                'colors' => ['#10b981', '#f59e0b', '#ef4444', '#6b7280'],
            ],
            'top_stock_values' => [
                'type' => 'bar',
                'labels' => array_column($topItems, 'item_name'),
                'datasets' => [
                    [
                        'label' => 'Stock Value',
                        'data' => array_column($topItems, 'stock_value'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'stock_status_overview' => [
                'type' => 'doughnut',
                'labels' => ['Available', 'Reserved', 'Damaged'],
                'data' => [
                    $reportData['stock_overview']['total_quantity_available'],
                    $reportData['stock_overview']['total_quantity_reserved'],
                    $reportData['stock_overview']['total_quantity_damaged'],
                ],
                'colors' => ['#10b981', '#f59e0b', '#ef4444'],
            ],
        ];
    }
}
