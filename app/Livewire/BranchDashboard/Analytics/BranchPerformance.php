<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Stock;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class BranchPerformance extends Component
{
    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getPerformanceMetrics()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stocks = Stock::where('branch_id', $branchId)->get();
        $purchases = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();
        $movements = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('movement_date', [$this->dateFrom, $this->dateTo])
            ->get();
        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $stockTurnover = $movements->where('type', 'out')->sum('quantity');
        $avgStockLevel = $stocks->avg('quantity_available');
        $turnoverRatio = $avgStockLevel > 0 ? $stockTurnover / $avgStockLevel : 0;

        return [
            'inventory_turnover_ratio' => $turnoverRatio,
            'stockout_rate' => $stocks->count() > 0 ? ($stocks->where('quantity_available', '<=', 0)->count() / $stocks->count() * 100) : 0,
            'fulfillment_rate' => $requests->count() > 0 ? ($requests->where('status', 'completed')->count() / $requests->count() * 100) : 0,
            'avg_purchase_lead_time' => 0, // Would need additional data
            'stock_accuracy' => 95, // Would need stock take data
            'total_stock_value' => $stocks->sum(fn($s) => $s->quantity_available * $s->average_cost),
        ];
    }

    public function getMonthlyPerformance()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $months = collect();
        $currentMonth = \Carbon\Carbon::parse($this->dateFrom);
        $endMonth = \Carbon\Carbon::parse($this->dateTo);

        while ($currentMonth->lte($endMonth)) {
            $purchases = Purchase::where('branch_id', $branchId)
                ->whereMonth('purchase_date', $currentMonth->month)
                ->whereYear('purchase_date', $currentMonth->year)
                ->sum('total_cost');

            $movements = StockMovement::whereHas('stock', fn($q) => $q->where('branch_id', $branchId))
                ->whereMonth('movement_date', $currentMonth->month)
                ->whereYear('movement_date', $currentMonth->year)
                ->where('type', 'out')
                ->count();

            $months->push([
                'month' => $currentMonth->format('M Y'),
                'purchases' => $purchases,
                'movements' => $movements,
            ]);

            $currentMonth->addMonth();
        }

        return [
            'categories' => $months->pluck('month')->toArray(),
            'series' => [
                ['name' => 'Purchase Value', 'data' => $months->pluck('purchases')->toArray()],
                ['name' => 'Stock Out Movements', 'data' => $months->pluck('movements')->toArray()],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.branch-dashboard.analytics.branch-performance', [
            'metrics' => $this->getPerformanceMetrics(),
            'monthlyPerformance' => $this->getMonthlyPerformance(),
        ]);
    }
}
