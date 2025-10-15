<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class SupplierPerformance extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;

    public function mount()
    {
        $this->dateFrom = now()->subDays(90)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getSupplierSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $purchases = Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_suppliers' => $purchases->pluck('supplier_name')->unique()->count(),
            'total_purchases' => $purchases->count(),
            'total_spent' => $purchases->sum('total_cost'),
            'avg_purchase_value' => $purchases->avg('total_cost'),
        ];
    }

    public function getTopSuppliers()
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

    public function getSupplierReliability()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        return Purchase::where('branch_id', $branchId)
            ->whereBetween('purchase_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('supplier_name, COUNT(*) as purchase_count, SUM(total_cost) as total_spent,
                        SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as completed_purchases')
            ->groupBy('supplier_name')
            ->orderByDesc('total_spent')
            ->limit(15)
            ->get()
            ->map(function ($supplier) {
                $supplier->reliability_score = ($supplier->completed_purchases / $supplier->purchase_count) * 100;
                return $supplier;
            });
    }

    public function render()
    {
        return view('livewire.branch-dashboard.analytics.supplier-performance', [
            'summary' => $this->getSupplierSummary(),
            'topSuppliers' => $this->getTopSuppliers(),
            'supplierReliability' => $this->getSupplierReliability(),
        ]);
    }
}
