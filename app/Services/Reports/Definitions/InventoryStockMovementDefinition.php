<?php

namespace App\Services\Reports\Definitions;

use App\Models\StockMovement;
use Carbon\Carbon;

class InventoryStockMovementDefinition implements ReportDefinition
{
    public function meta(): array
    {
        return [
            'name' => 'Stock Movement Report',
            'type' => 'stock_movement',
            'category' => 'inventory',
            'requires_department' => false,
            'permissions' => ['view-reports', 'generate-reports'],
        ];
    }

    public function query(array $context): array
    {
        $dateFrom = Carbon::parse($context['period_from'])->startOfDay();
        $dateTo = Carbon::parse($context['period_to'])->endOfDay();

        $movements = StockMovement::query()
            ->where(function ($q) use ($context) {
                $q->where('branch_id', $context['branch_id'])
                    ->orWhereHas('stock', function ($sq) use ($context) {
                        $sq->where('branch_id', $context['branch_id']);
                    });
            })
            ->when($context['department_id'], function ($q) use ($context) {
                $q->where('department_id', $context['department_id']);
            })
            ->whereBetween(
                \DB::raw('COALESCE(movement_date, created_at)'),
                [$dateFrom, $dateTo]
            )
            ->with(['stock.item', 'mover'])
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $movementsIn = $movements->filter(fn ($m) => $m->isInbound());
        $movementsOut = $movements->filter(fn ($m) => $m->isOutbound());

        return [
            'movement_overview' => $this->buildMovementOverview($movements, $context['period_from'], $context['period_to']),
            'all_movements' => $this->formatMovements($movements),
            'movements_in' => $this->formatMovements($movementsIn),
            'movements_out' => $this->formatMovements($movementsOut),
            'by_source' => $this->groupBySource($movements),
            'by_category' => $this->groupByCategory($movements),
            'by_item' => $this->groupByItem($movements),
            'daily_movements' => $this->buildDailyMovements($movements),
            'period_info' => [
                'from' => $context['period_from'],
                'to' => $context['period_to'],
                'total_days' => Carbon::parse($context['period_from'])->diffInDays(Carbon::parse($context['period_to'])) + 1,
            ],
        ];
    }

    public function summary(array $data, array $context): array
    {
        $overview = $data['movement_overview'] ?? [];

        return [
            'total_movements' => $overview['total_movements'] ?? 0,
            'movements_in' => $overview['movements_in_count'] ?? 0,
            'movements_out' => $overview['movements_out_count'] ?? 0,
            'net_movement_value' => $overview['net_value'] ?? 0,
            'avg_daily_movements' => $overview['avg_daily_movements'] ?? 0,
        ];
    }

    public function charts(array $data, array $context): array
    {
        $overview = $data['movement_overview'] ?? [];
        $daily = $data['daily_movements'] ?? [];
        $categories = array_slice($data['by_category'] ?? [], 0, 10);
        $sources = array_slice($data['by_source'] ?? [], 0, 10);

        return [
            'movement_type_chart' => [
                'type' => 'doughnut',
                'labels' => ['Inbound', 'Outbound'],
                'data' => [
                    $overview['movements_in_count'] ?? 0,
                    $overview['movements_out_count'] ?? 0,
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

    public function tables(array $data, array $summary, array $context): array
    {
        return [
            'movements' => [
                'headers' => ['Date', 'Time', 'Item', 'SKU', 'Type', 'Qty', 'UoM', 'Source', 'Value', 'By'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['date'] ?? '-',
                        $row['time'] ?? '-',
                        $row['item_name'] ?? '-',
                        $row['sku'] ?? '-',
                        $row['movement_type'] ?? '-',
                        $row['quantity'] ?? 0,
                        $row['uom'] ?? '-',
                        $row['source'] ?? '-',
                        $row['total_value'] ?? 0,
                        $row['created_by'] ?? 'System',
                    ];
                }, $data['all_movements'] ?? []),
            ],
            'by_category' => [
                'headers' => ['Category', 'Movements', 'In Qty', 'Out Qty', 'Net', 'Total Value'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['category'] ?? '-',
                        $row['movements_count'] ?? 0,
                        $row['in_quantity'] ?? 0,
                        $row['out_quantity'] ?? 0,
                        $row['net_movement'] ?? 0,
                        $row['total_value'] ?? 0,
                    ];
                }, $data['by_category'] ?? []),
            ],
            'by_source' => [
                'headers' => ['Source', 'Movements', 'In', 'Out', 'Quantity', 'Value'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['source'] ?? '-',
                        $row['movements_count'] ?? 0,
                        $row['in_count'] ?? 0,
                        $row['out_count'] ?? 0,
                        $row['total_quantity'] ?? 0,
                        $row['total_value'] ?? 0,
                    ];
                }, $data['by_source'] ?? []),
            ],
        ];
    }

    public function narrative(array $data, array $summary, array $context): array
    {
        $highlights = [];
        $concerns = [];

        $netValue = $summary['net_movement_value'] ?? 0;
        if ($netValue < 0) {
            $concerns[] = 'Net movement value is negative, indicating more stock outflow than inflow.';
        } elseif ($netValue > 0) {
            $highlights[] = 'Net movement value is positive, indicating more stock inflow than outflow.';
        }

        return [
            'overview' => "Stock movement summary from {$context['period_from']} to {$context['period_to']}.",
            'highlights' => $highlights,
            'concerns' => $concerns,
            'recommendations' => [
                'Review high‑value outbound movements to confirm accuracy.',
            ],
        ];
    }

    private function formatMovements($movements): array
    {
        return $movements->map(function ($movement) {
            return [
                'id' => $movement->id,
                'date' => Carbon::parse($movement->movement_date ?? $movement->created_at)->format('Y-m-d'),
                'time' => ($movement->movement_date ?? $movement->created_at)->format('H:i'),
                'item_name' => $movement->stock?->item?->name ?? 'Unknown',
                'sku' => $movement->stock?->item?->sku ?? 'N/A',
                'category' => $movement->stock?->item?->category ?? 'Uncategorized',
                'movement_type' => $movement->type,
                'quantity' => $movement->quantity,
                'uom' => $movement->uom,
                'source' => $movement->source ?? 'N/A',
                'reference' => $movement->reference ?? 'N/A',
                'unit_cost' => $movement->stock?->item?->unit_cost ?? 0,
                'total_value' => $movement->quantity * ($movement->stock?->item?->unit_cost ?? 0),
                'created_by' => $movement->mover?->name ?? 'System',
                'notes' => $movement->notes ?? '',
            ];
        })->toArray();
    }

    private function buildMovementOverview($movements, $from, $to): array
    {
        $movementsIn = $movements->where('type', 'in');
        $movementsOut = $movements->where('type', 'out');

        $totalInQuantity = $movementsIn->sum('quantity');
        $totalOutQuantity = $movementsOut->sum('quantity');

        $totalInValue = $movementsIn->sum(function ($m) {
            return $m->quantity * ($m->stock?->item?->unit_cost ?? 0);
        });

        $totalOutValue = $movementsOut->sum(function ($m) {
            return $m->quantity * ($m->stock?->item?->unit_cost ?? 0);
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
            'avg_daily_movements' => $this->averageDailyMovements($movements, $from, $to),
        ];
    }

    private function averageDailyMovements($movements, $from, $to): float
    {
        $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
        if ($days <= 0) {
            return 0;
        }
        return round($movements->count() / $days, 2);
    }

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

            if ($movement->isInbound()) {
                $sources[$source]['in_count']++;
            } else {
                $sources[$source]['out_count']++;
            }

            $sources[$source]['total_quantity'] += $movement->quantity;
            $sources[$source]['total_value'] += $movement->quantity * ($movement->stock?->item?->unit_cost ?? 0);
        }

        usort($sources, function ($a, $b) {
            return $b['movements_count'] <=> $a['movements_count'];
        });

        return array_values($sources);
    }

    private function groupByCategory($movements): array
    {
        $categories = [];

        foreach ($movements as $movement) {
            $category = $movement->stock?->item?->category ?? 'Uncategorized';

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
            $value = $quantity * ($movement->stock?->item?->unit_cost ?? 0);

            if ($movement->isInbound()) {
                $categories[$category]['in_quantity'] += $quantity;
                $categories[$category]['total_value'] += $value;
            } else {
                $categories[$category]['out_quantity'] += $quantity;
                $categories[$category]['total_value'] -= $value;
            }

            $categories[$category]['net_movement'] =
                $categories[$category]['in_quantity'] - $categories[$category]['out_quantity'];
        }

        usort($categories, function ($a, $b) {
            return abs($b['total_value']) <=> abs($a['total_value']);
        });

        return array_values($categories);
    }

    private function groupByItem($movements): array
    {
        $items = [];

        foreach ($movements as $movement) {
            $itemId = $movement->stock?->item?->id;
            $itemName = $movement->stock?->item?->name ?? 'Unknown';

            if (!isset($items[$itemId])) {
                $items[$itemId] = [
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'sku' => $movement->stock?->item?->sku ?? 'N/A',
                    'category' => $movement->stock?->item?->category ?? 'Uncategorized',
                    'movements_count' => 0,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'net_movement' => 0,
                    'total_value' => 0,
                ];
            }

            $items[$itemId]['movements_count']++;
            $quantity = $movement->quantity;
            $value = $quantity * ($movement->stock?->item?->unit_cost ?? 0);

            if ($movement->isInbound()) {
                $items[$itemId]['in_quantity'] += $quantity;
                $items[$itemId]['total_value'] += $value;
            } else {
                $items[$itemId]['out_quantity'] += $quantity;
                $items[$itemId]['total_value'] -= $value;
            }

            $items[$itemId]['net_movement'] =
                $items[$itemId]['in_quantity'] - $items[$itemId]['out_quantity'];
        }

        usort($items, function ($a, $b) {
            return abs($b['total_value']) <=> abs($a['total_value']);
        });

        return array_values($items);
    }

    private function buildDailyMovements($movements): array
    {
        $dailyData = [];

        foreach ($movements as $movement) {
            $date = Carbon::parse($movement->movement_date)->format('Y-m-d');

            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    'date' => $date,
                    'in_count' => 0,
                    'out_count' => 0,
                    'in_quantity' => 0,
                    'out_quantity' => 0,
                    'in_value' => 0,
                    'out_value' => 0,
                ];
            }

            $quantity = $movement->quantity;
            $value = $quantity * ($movement->stock?->item?->unit_cost ?? 0);

            if ($movement->isInbound()) {
                $dailyData[$date]['in_count']++;
                $dailyData[$date]['in_quantity'] += $quantity;
                $dailyData[$date]['in_value'] += $value;
            } else {
                $dailyData[$date]['out_count']++;
                $dailyData[$date]['out_quantity'] += $quantity;
                $dailyData[$date]['out_value'] += $value;
            }
        }

        uksort($dailyData, function ($a, $b) {
            return strcmp($a, $b);
        });

        return array_values($dailyData);
    }
}
