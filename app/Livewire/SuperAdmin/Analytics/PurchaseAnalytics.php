<?php

namespace App\Livewire\SuperAdmin\Analytics;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class PurchaseAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $selectedBranch = '';
    public $supplierFilter = '';
    public $paymentStatus = '';

    protected $queryString = ['dateFrom', 'dateTo', 'supplierFilter', 'paymentStatus'];

    public function mount()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
        $this->dispatch('chartsUpdated', [
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems()->map(function($item) {
                return [
                    'name' => $item->item->name,
                    'total_cost' => $item->total_cost,
                    'total_quantity' => $item->total_quantity,
                    'uom' => $item->item->uom
                ];
            })->toArray()
        ]);
    }

    public function updatedDateTo()
    {
        $this->resetPage();
        $this->dispatch('chartsUpdated', [
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems()->map(function($item) {
                return [
                    'name' => $item->item->name,
                    'total_cost' => $item->total_cost,
                    'total_quantity' => $item->total_quantity,
                    'uom' => $item->item->uom
                ];
            })->toArray()
        ]);
    }

    public function updatedSupplierFilter()
    {
        $this->resetPage();
        $this->dispatch('chartsUpdated', [
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems()->map(function($item) {
                return [
                    'name' => $item->item->name,
                    'total_cost' => $item->total_cost,
                    'total_quantity' => $item->total_quantity,
                    'uom' => $item->item->uom
                ];
            })->toArray()
        ]);
    }

    public function updatedPaymentStatus()
    {
        $this->resetPage();
        $this->dispatch('chartsUpdated', [
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems()->map(function($item) {
                return [
                    'name' => $item->item->name,
                    'total_cost' => $item->total_cost,
                    'total_quantity' => $item->total_quantity,
                    'uom' => $item->item->uom
                ];
            })->toArray()
        ]);
    }

    public function getPurchaseTrendData()
    {
        $query = Purchase::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $purchases = $query
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
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
        $query = Purchase::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $suppliers = $query
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
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
        $query = Purchase::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $breakdown = $query
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
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
        $query = PurchaseItem::with(['item', 'purchase']);

        if ($this->selectedBranch) {
            $query->whereHas('purchase', function ($purchaseQuery) {
                $purchaseQuery->where('branch_id', $this->selectedBranch)
                    ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
                    ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
                    ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            });
        } else {
            $query->whereHas('purchase', function ($purchaseQuery) {
                $purchaseQuery->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
                    ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
                    ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            });
        }

        return $query
            ->selectRaw('item_id, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('item_id')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();
    }

    public function getSummary()
    {
        $query = Purchase::query();

        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $purchases = $query
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
        $purchasesQuery = Purchase::with('recorder');

        if ($this->selectedBranch) {
            $purchasesQuery->where('branch_id', $this->selectedBranch);
        }

        $purchases = $purchasesQuery
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->latest('purchase_date')
            ->paginate(15);

        $suppliersQuery = Purchase::query();

        if ($this->selectedBranch) {
            $suppliersQuery->where('branch_id', $this->selectedBranch);
        }

        $suppliers = $suppliersQuery->distinct()->pluck('supplier_name');
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.analytics.purchase-analytics', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'branches' => $branches,
            'summary' => $this->getSummary(),
            'trendData' => $this->getPurchaseTrendData(),
            'supplierAnalysis' => $this->getSupplierAnalysis(),
            'costBreakdown' => $this->getCostBreakdown(),
            'topItems' => $this->getTopPurchasedItems(),
        ]);
    }
}
