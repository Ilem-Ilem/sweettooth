<?php

namespace App\Services\Reports;

use App\Models\StockMovement;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockMovementReportService extends ReportService
{
    protected string $reportCategory = 'inventory';
    protected string $reportType = 'stock_movement';

    /**
     * Get report name.
     */
    protected function getReportName(): string
    {
        return 'Stock Movement Report';
    }

    /**
     * Generate the stock movement report data.
     */
    protected function generateReportData(): array
    {
        $this->validateParameters();

        // Get all stock movements in the period
        $movements = StockMovement::query()
            ->where('branch_id', $this->branchId)
            ->when($this->departmentId, function ($q) {
                $q->where('department_id', $this->departmentId);
            })
            ->whereBetween('movement_date', [$this->periodFrom, $this->periodTo])
            ->with(['item.category', 'createdBy'])
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsIn = $movements->where('movement_type', 'in');
        $movementsOut = $movements->where('movement_type', 'out');

        return [
            'movement_overview' => $this->generateMovementOverview($movements),
            'all_movements' => $this->formatMovements($movements),
            'movements_in' => $this->formatMovements($movementsIn),
            'movements_out' => $this->formatMovements($movementsOut),
            'by_source' => $this->groupBySource($movements),
            'by_category' => $this->groupByCategory($movements),
            'by_item' => $this->groupByItem($movements),
            'daily_movements' => $this->generateDailyMovements($movements),
            'period_info' => [
                'from' => $this->periodFrom,
                'to' => $this->periodTo,
                'total_days' => Carbon::parse($this->periodFrom)->diffInDays(Carbon::parse($this->periodTo)) + 1,
            ],
        ];
    }

    /**
     * Format movements data.
     */
    private function formatMovements($movements): array
    {
        return $movements->map(function ($movement) {
            return [
                'id' => $movement->id,
                'date' => Carbon::parse($movement->movement_date)->format('Y-m-d'),
                'time' => $movement->created_at->format('H:i'),
                'item_name' => $movement->item->name ?? 'Unknown',
                'sku' => $movement->item->sku ?? 'N/A',
                'category' => $movement->item->category->name ?? 'Uncategorized',
                'movement_type' => $movement->movement_type,
                'quantity' => $movement->quantity,
                'uom' => $movement->uom,
                'source' => $movement->source ?? 'N/A',
                'reference' => $movement->reference ?? 'N/A',
                'unit_cost' => $movement->item->unit_cost ?? 0,
                'total_value' => $movement->quantity * ($movement->item->unit_cost ?? 0),
                'created_by' => $movement->createdBy->name ?? 'System',
                'notes' => $movement->notes ?? '',
            ];
        })->toArray();
    }

    /**
     * Generate movement overview.
     */
    private function generateMovementOverview($movements): array
    {
        $movementsIn = $movements->where('movement_type', 'in');
        $movementsOut = $movements->where('movement_type', 'out');

        $totalInQuantity = $movementsIn->sum('quantity');
        $totalOutQuantity = $movementsOut->sum('quantity');

        $totalInValue = $movementsIn->sum(function ($m) {
            return $m->quantity * ($m->item->unit_cost ?? 0);
        });

        $totalOutValue = $movementsOut->sum(function ($m) {
            return $m->quantity * ($m->item->unit_cost ?? 0);
        });

        return [
            'total_movements' => $movements->count(),
            'movements_in_count' => $movementsIn->count(),
            'movements_out_count' => $movementsOut->count(),
            'total_in_quantity' => $totalInQuantity,
            'total_out_quantity' => $totalOutQuantity,
            'net_movement' => $totalInQuantity - $totalOutQuantity,
            'total_in_value' => $totalInValue,
            'total_out_value' => $totalOutValue,
            'net_value' => $totalInValue - $totalOutValue,
            'avg_daily_movements' => $this->calculateAvgDailyMovements($movements),
        ];
    }

    /**
     * Calculate average daily movements.
     */
    private function calculateAvgDailyMovements($movements): float
    {
        $days = Carbon::parse($this->periodFrom)->diffInDays(Carbon::parse($this->periodTo)) + 1;

        if ($days <= 0) {
            return 0;
        }

        return round($movements->count() / $days, 2);
    }

    /**
     * Group movements by source.
     */
    private function groupBySource($movements): array
    {
        $sources = [];

        foreach ($movements as $movement) {
            $source = $movement->source ?? 'Unknown';

            if (!isset($sources[$source])) {
                $sources[$source] = [
                    'source' => $source,
                    'movements_count' => 0,
                    'in_count' => 0,
                    'out_count' => 0,
                    'total_quantity' => 0,
                    'total_value' => 0,
                ];
            }

            $sources[$source]['movements_count']++;

            if ($movement->movement_type === 'in') {
                $sources[$source]['in_count']++;
            } else {
                $sources[$source]['out_count']++;
            }

            $sources[$source]['total_quantity'] += $movement->quantity;
            $sources[$source]['total_value'] += $movement->quantity * ($movement->item->unit_cost ?? 0);
        }

        // Sort by movements count descending
        usort($sources, function ($a, $b) {
            return $b['movements_count'] <=> $a['movements_count'];
        });

        return array_values($sources);
    }

    /**
     * Group movements by category.
     */
    private function groupByCategory($movements): array
    {
        $categories = [];

        foreach ($movements as $movement) {
            $category = $movement->item->category->name ?? 'Uncategorized';

            if (!isset($categories[$category])) {
                $categories[$category] = [
                    'category' => $category,
                    'movements_count' => 0,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'net_movement' => 0,
                    'total_value' => 0,
                ];
            }

            $categories[$category]['movements_count']++;
            $quantity = $movement->quantity;
            $value = $quantity * ($movement->item->unit_cost ?? 0);

            if ($movement->movement_type === 'in') {
                $categories[$category]['in_quantity'] += $quantity;
                $categories[$category]['total_value'] += $value;
            } else {
                $categories[$category]['out_quantity'] += $quantity;
                $categories[$category]['total_value'] -= $value;
            }

            $categories[$category]['net_movement'] =
                $categories[$category]['in_quantity'] - $categories[$category]['out_quantity'];
        }

        // Sort by total value descending
        usort($categories, function ($a, $b) {
            return abs($b['total_value']) <=> abs($a['total_value']);
        });

        return array_values($categories);
    }

    /**
     * Group movements by item.
     */
    private function groupByItem($movements): array
    {
        $items = [];

        foreach ($movements as $movement) {
            $itemId = $movement->item_id;
            $itemName = $movement->item->name ?? 'Unknown';

            if (!isset($items[$itemId])) {
                $items[$itemId] = [
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'sku' => $movement->item->sku ?? 'N/A',
                    'category' => $movement->item->category->name ?? 'Uncategorized',
                    'movements_count' => 0,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'net_movement' => 0,
                    'total_value' => 0,
                ];
            }

            $items[$itemId]['movements_count']++;
            $quantity = $movement->quantity;
            $value = $quantity * ($movement->item->unit_cost ?? 0);

            if ($movement->movement_type === 'in') {
                $items[$itemId]['in_quantity'] += $quantity;
                $items[$itemId]['total_value'] += $value;
            } else {
                $items[$itemId]['out_quantity'] += $quantity;
                $items[$itemId]['total_value'] -= $value;
            }

            $items[$itemId]['net_movement'] =
                $items[$itemId]['in_quantity'] - $items[$itemId]['out_quantity'];
        }

        // Sort by movements count descending
        usort($items, function ($a, $b) {
            return $b['movements_count'] <=> $a['movements_count'];
        });

        return array_slice(array_values($items), 0, 50); // Top 50 items
    }

    /**
     * Generate daily movements summary.
     */
    private function generateDailyMovements($movements): array
    {
        $dailyData = [];

        foreach ($movements as $movement) {
            $date = Carbon::parse($movement->movement_date)->format('Y-m-d');

            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    'date' => $date,
                    'total_movements' => 0,
                    'in_count' => 0,
                    'out_count' => 0,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'in_value' => 0,
                    'out_value' => 0,
                ];
            }

            $dailyData[$date]['total_movements']++;
            $quantity = $movement->quantity;
            $value = $quantity * ($movement->item->unit_cost ?? 0);

            if ($movement->movement_type === 'in') {
                $dailyData[$date]['in_count']++;
                $dailyData[$date]['in_quantity'] += $quantity;
                $dailyData[$date]['in_value'] += $value;
            } else {
                $dailyData[$date]['out_count']++;
                $dailyData[$date]['out_quantity'] += $quantity;
                $dailyData[$date]['out_value'] += $value;
            }
        }

        // Sort by date
        uksort($dailyData, function ($a, $b) {
            return strcmp($a, $b);
        });

        return array_values($dailyData);
    }

    /**
     * Generate summary metrics.
     */
    protected function generateSummaryMetrics(array $reportData): array
    {
        $overview = $reportData['movement_overview'];

        return [
            'total_movements' => $overview['total_movements'],
            'movements_in' => $overview['movements_in_count'],
            'movements_out' => $overview['movements_out_count'],
            'net_movement_value' => $overview['net_value'],
            'avg_daily_movements' => $overview['avg_daily_movements'],
        ];
    }

    /**
     * Generate charts data.
     */
    protected function generateChartsData(array $reportData): array
    {
        $overview = $reportData['movement_overview'];
        $daily = $reportData['daily_movements'];
        $categories = array_slice($reportData['by_category'], 0, 10);
        $sources = array_slice($reportData['by_source'], 0, 10);

        return [
            'movement_type_chart' => [
                'type' => 'doughnut',
                'labels' => ['Inbound', 'Outbound'],
                'data' => [
                    $overview['movements_in_count'],
                    $overview['movements_out_count'],
                ],
                'colors' => ['#22c55e', '#ef4444'],
            ],
            'daily_movements_chart' => [
                'type' => 'line',
                'labels' => array_column($daily, 'date'),
                'datasets' => [
                    [
                        'label' => 'Inbound',
                        'data' => array_column($daily, 'in_count'),
                        'color' => '#22c55e',
                    ],
                    [
                        'label' => 'Outbound',
                        'data' => array_column($daily, 'out_count'),
                        'color' => '#ef4444',
                    ],
                ],
            ],
            'category_movements_chart' => [
                'type' => 'bar',
                'labels' => array_column($categories, 'category'),
                'datasets' => [
                    [
                        'label' => 'Net Movement Value',
                        'data' => array_column($categories, 'total_value'),
                        'color' => '#3b82f6',
                    ],
                ],
            ],
            'source_breakdown_chart' => [
                'type' => 'pie',
                'labels' => array_column($sources, 'source'),
                'data' => array_column($sources, 'movements_count'),
                'colors' => ['#22c55e', '#3b82f6', '#eab308', '#f59e0b', '#ef4444'],
            ],
        ];
    }
}
