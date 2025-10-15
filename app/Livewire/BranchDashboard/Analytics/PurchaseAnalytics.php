<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class PurchaseAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $supplierFilter = '';
    public $paymentStatus = '';

    protected $queryString = ['dateFrom', 'dateTo', 'supplierFilter', 'paymentStatus'];

    public function mount()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedSupplierFilter() { $this->resetPage(); }
    public function updatedPaymentStatus() { $this->resetPage(); }

    public function getPurchaseTrendData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $purchases = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(purchase_date) as date, COUNT(*) as count, SUM(total_cost) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'categories' => $purchases->map(fn($p) => \Carbon\Carbon::parse($p->date)->format('M d'))->toArray(),
            'series' => [
                ['name' => 'Total Cost (₦)', 'data' => $purchases->pluck('total')->toArray()],
                ['name' => 'Purchase Count', 'data' => $purchases->pluck('count')->toArray(), 'yaxis' => 1],
            ],
        ];
    }

    public function getSupplierAnalysis()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $suppliers = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('supplier_name, COUNT(*) as purchase_count, SUM(total_cost) as total_spent')
            ->groupBy('supplier_name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return [
            'labels' => $suppliers->pluck('supplier_name')->toArray(),
            'series' => [
                ['name' => 'Total Spent', 'data' => $suppliers->pluck('total_spent')->toArray()],
            ],
        ];
    }

    public function getCostBreakdown()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $breakdown = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('
                SUM(total_fob_ngn) as fob_total,
                SUM(other_costs) as other_costs_total,
                SUM(landing_cost) as landing_total
            ')
            ->first();

        return [
            'labels' => ['FOB Cost', 'Other Costs', 'Landing Cost'],
            'series' => [
                (float) $breakdown->fob_total,
                (float) $breakdown->other_costs_total,
                (float) $breakdown->landing_total,
            ],
        ];
    }

    public function getTopPurchasedItems()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return PurchaseItem::with(['item', 'purchase'])
            ->whereHas('purchase', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            })
            ->selectRaw('item_id, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('item_id')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();
    }

    public function getSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $purchases = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_purchases' => $purchases->count(),
            'total_spent' => $purchases->sum('total_cost'),
            'avg_purchase_value' => $purchases->avg('total_cost'),
            'total_items' => PurchaseItem::whereIn('purchase_id', $purchases->pluck('id'))->sum('quantity'),
            'paid_count' => $purchases->where('payment_status', 'paid')->count(),
            'pending_count' => $purchases->where('payment_status', 'pending')->count(),
        ];
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $purchases = Purchase::with('recorder')
            ->where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->latest('purchase_date')
            ->paginate(15);

        $suppliers = Purchase::where('branch_id', $branchId)
            ->distinct()
            ->pluck('supplier_name');

        return view('livewire.branch-dashboard.analytics.purchase-analytics', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'summary' => $this->getSummary(),
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems(),
        ]);
    }
}
