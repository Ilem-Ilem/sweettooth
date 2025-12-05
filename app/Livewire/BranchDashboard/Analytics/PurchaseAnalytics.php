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
    public $viewMode = 'overview'; // overview, table, suppliers, items
    public $sortColumn = 'total_spent';
    public $sortDirection = 'desc';

    protected $queryString = ['dateFrom', 'dateTo', 'supplierFilter', 'paymentStatus'];

    public function mount()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->validateDateRange();
        
        // Store branch_id in session for super admin access
        $branchId = $this->getBranchId();
        if ($branchId) {
            session(['branch_id_context' => $branchId]);
        }
    }

    public function updatedDateFrom()
    {
        $this->validateDateRange();
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->validateDateRange();
        $this->resetPage();
    }

    public function updatedSupplierFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function resetFilters()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->supplierFilter = '';
        $this->paymentStatus = '';
        $this->resetPage();
        
        session()->flash('success', 'Filters reset successfully.');
    }

    public function updatedPaymentStatus()
    {
        $this->resetPage();
    }

    private function validateDateRange()
    {
        $from = \Carbon\Carbon::parse($this->dateFrom);
        $to = \Carbon\Carbon::parse($this->dateTo);
        
        if ($from->greaterThan($to)) {
            // Swap dates
            $temp = $this->dateFrom;
            $this->dateFrom = $this->dateTo;
            $this->dateTo = $temp;
            
            session()->flash('warning', 'Date range was automatically corrected.');
        }
        
        // Warn if range > 1 year
        if ($from->diffInDays($to) > 365) {
            session()->flash('warning', 'Note: Large date ranges may impact performance.');
        }
    }

    private function isAnyFilterActive()
    {
        return $this->supplierFilter || $this->paymentStatus;
    }

    private function getBranchId()
    {
        // Check session first (for super admin context persistence)
        if (session()->has('branch_id_context')) {
            return session('branch_id_context');
        }
        
        // Fall back to URL param or auth user's branch
        return Auth::guard('employees')->user()?->branch_id ?? request()->get('b_id');
    }

    public function getPurchaseTrendData()
    {
        $branchId = $this->getBranchId();

        $purchases = Purchase::where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(purchase_date) as date, COUNT(*) as count, SUM(landing_cost) as total')
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
        $branchId = $this->getBranchId();

        return Purchase::where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('supplier_name, COUNT(*) as purchase_count, SUM(landing_cost) as total_spent')
            ->groupBy('supplier_name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();
    }

    public function getCostBreakdown()
    {
        $branchId = $this->getBranchId();

        $breakdown = Purchase::where('branch_id', $branchId)
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
        $branchId = $this->getBranchId();  

        $items = PurchaseItem::select('item_id', DB::raw('SUM(quantity) as total_quantity, SUM(COALESCE(total_cost, 0)) as total_cost'))
            ->whereHas('purchase', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
                    ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
                    ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo]);
            })
            ->groupBy('item_id')
            ->orderByDesc('total_cost')
            ->limit(10)
            ->get();

        // Manually load the item relationship to avoid N+1 queries
        $items->load('item');
        
        return $items;
    }

    public function getSummary()
    {
        $branchId = $this->getBranchId();

        $purchases = Purchase::where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $total = $purchases->count();
        $paid = $purchases->where('payment_status', 'paid')->count();
        $partial = $purchases->where('payment_status', 'partial')->count();
        $pending = $purchases->where('payment_status', 'pending')->count();

        return [
            'total_purchases' => $total,
            'total_spent' => $purchases->sum('landing_cost'),
            'avg_purchase_value' => $purchases->avg('landing_cost'),
            'total_items' => PurchaseItem::whereIn('purchase_id', $purchases->pluck('id'))->sum('quantity'),
            'paid_count' => $paid,
            'paid_percentage' => $total > 0 ? round(($paid / $total) * 100, 1) : 0,
            'partial_count' => $partial,
            'partial_percentage' => $total > 0 ? round(($partial / $total) * 100, 1) : 0,
            'pending_count' => $pending,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
        ];
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $purchases = Purchase::where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->latest('purchase_date')
            ->paginate(15);

        // Manually load recorder for each purchase
        $purchases->getCollection()->transform(function ($purchase) {
            if ($purchase->recorded_by_type && $purchase->recorded_by_id) {
                try {
                    $recordedByClass = $purchase->recorded_by_type;
                    $purchase->recorder = $recordedByClass::find($purchase->recorded_by_id);
                } catch (\Exception $e) {
                    $purchase->recorder = null;
                }
            } else {
                $purchase->recorder = null;
            }
            return $purchase;
        });

        $suppliers = Purchase::where('branch_id', $branchId)
            ->when($this->supplierFilter, fn($q) => $q->where('supplier_name', 'like', '%' . $this->supplierFilter . '%'))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
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
            'isAnyFilterActive' => $this->isAnyFilterActive(),
        ]);
    }
}
