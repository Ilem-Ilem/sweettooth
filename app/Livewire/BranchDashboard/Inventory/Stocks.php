<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Stock;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Stocks extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public function getBranchId()
    {
        return Auth::guard('employees')->user()->employee->branch_id;
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = Stock::with(['branch', 'item'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->whereHas('item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, function ($q) {
                $q->whereHas('item', function ($query) {
                    $query->where('category', $this->filterCategory);
                });
            })
            ->when($this->filterStatus, function ($q) {
                if ($this->filterStatus === 'low_stock') {
                    $q->whereHas('item', function ($query) {
                        $query->whereColumn('stocks.available_quantity', '<', 'items.reorder_level');
                    });
                } elseif ($this->filterStatus === 'overstock') {
                    $q->whereHas('item', function ($query) {
                        $query->whereColumn('stocks.total_quantity', '>', 'items.max_stock_level');
                    });
                }
            })
            ->orderBy('updated_at', 'desc');

        $stocks = $query->paginate(15);

        return view('livewire.branch-dashboard.inventory.stocks', [
            'stocks' => $stocks,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterCategory = '';
        $this->filterStatus = '';
    }
}
