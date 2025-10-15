<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\StockTake;
use App\Models\StockTakeDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class StockVarianceAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getVarianceSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stockTakes = StockTake::where('branch_id', $branchId)
            ->whereBetween('stock_take_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed')
            ->get();

        $details = StockTakeDetail::whereIn('stock_take_id', $stockTakes->pluck('id'))->get();

        return [
            'total_stock_takes' => $stockTakes->count(),
            'total_items_counted' => $details->count(),
            'positive_variances' => $details->where('variance', '>', 0)->count(),
            'negative_variances' => $details->where('variance', '<', 0)->count(),
            'total_variance_value' => $details->sum(fn($d) => $d->variance * ($d->stock->average_cost ?? 0)),
        ];
    }

    public function getVarianceTrend()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stockTakes = StockTake::with('details')
            ->where('branch_id', $branchId)
            ->whereBetween('stock_take_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed')
            ->orderBy('stock_take_date')
            ->get();

        $dates = [];
        $positiveVariances = [];
        $negativeVariances = [];

        foreach ($stockTakes as $take) {
            $dates[] = \Carbon\Carbon::parse($take->stock_take_date)->format('M d');
            $positiveVariances[] = $take->details->where('variance', '>', 0)->sum('variance');
            $negativeVariances[] = abs($take->details->where('variance', '<', 0)->sum('variance'));
        }

        return [
            'categories' => $dates,
            'series' => [
                ['name' => 'Positive Variance', 'data' => $positiveVariances],
                ['name' => 'Negative Variance', 'data' => $negativeVariances],
            ],
        ];
    }

    public function getTopVarianceItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stockTakes = StockTake::where('branch_id', $branchId)
            ->whereBetween('stock_take_date', [$this->dateFrom, $this->dateTo])
            ->where('status', 'completed')
            ->get();

        return StockTakeDetail::with(['stock.item'])
            ->whereIn('stock_take_id', $stockTakes->pluck('id'))
            ->get()
            ->groupBy('stock_id')
            ->map(function ($group) {
                return [
                    'item' => $group->first()->stock->item,
                    'total_variance' => $group->sum('variance'),
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc(fn($item) => abs($item['total_variance']))
            ->take(10)
            ->values();
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $stockTakes = StockTake::with(['conductor', 'stockTakeDetails'])
            ->where('branch_id', $branchId)
            ->whereBetween('stock_take_date', [$this->dateFrom, $this->dateTo])
            ->latest('stock_take_date')
            ->paginate(10);

        return view('livewire.branch-dashboard.analytics.stock-variance-analytics', [
            'stockTakes' => $stockTakes,
            'summary' => $this->getVarianceSummary(),
            'varianceTrend' => $this->getVarianceTrend(),
            'topVarianceItems' => $this->getTopVarianceItems(),
        ]);
    }
}
