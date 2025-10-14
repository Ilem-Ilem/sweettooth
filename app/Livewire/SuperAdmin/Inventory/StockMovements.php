<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovements extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBranch = '';
    public $filterType = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    public function render()
    {
        $query = StockMovement::with(['branch', 'item', 'recorder'])
            ->when($this->search, function ($q) {
                $q->whereHas('item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterType, fn($q) => $q->where('movement_type', $this->filterType))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('movement_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('movement_date', '<=', $this->filterDateTo))
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc');

        $movements = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.stock-movements', [
            'movements' => $movements,
            'branches' => $branches,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterType = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
    }
}
