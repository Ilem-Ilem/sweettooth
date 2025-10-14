<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.branch-dashboard')]
class StockMovements extends Component
{
    use WithPagination;
#[Url(keep:true)]
    public $b_id;
    public $search = '';
    public $filterType = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

        public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = StockMovement::with(['branch', 'item', 'recorder'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->whereHas('item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, fn($q) => $q->where('movement_type', $this->filterType))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('movement_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('movement_date', '<=', $this->filterDateTo))
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc');

        $movements = $query->paginate(15);

        return view('livewire.branch-dashboard.inventory.stock-movements', [
            'movements' => $movements,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
    }
}
