<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use Livewire\Component;
use App\Models\Department;
use Livewire\WithPagination;
use App\Models\StockMovement;

class StockMovements extends Component
{
    use WithPagination;

    public $quantity = 15;
    public $search = '';
    public $filterType = '';
    public $filterShift = '';
    public $filterDepartment = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterBranch = '';

    public function render()
    {
        $query = StockMovement::with([
            'stock.item',
            'stock.branch',
            'mover'
        ])
            ->when($this->filterBranch, function ($q) {
                $q->whereHas('stock', function ($stockQuery) {
                    $stockQuery->where('branch_id', $this->filterBranch);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('stock.item', function ($subQuery) {
                        $subQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('sku', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('mover', function ($subQuery) {
                            $subQuery->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhere('notes', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterDateFrom, fn ($q) => $q->whereDate('movement_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn ($q) => $q->whereDate('movement_date', '<=', $this->filterDateTo))
            ->when($this->filterShift, function ($q) {
                // Filter by shift through ItemRequest reference
                $q->whereHasMorph('reference', ['App\Models\ItemRequest'], function ($subQuery) {
                    $subQuery->where('shift', $this->filterShift);
                });
            })
            ->when($this->filterDepartment, function ($q) {
                // Filter by department through ItemRequest reference
                $q->whereHasMorph('reference', ['App\Models\ItemRequest'], function ($subQuery) {
                    $subQuery->where('department_id', $this->filterDepartment);
                });
            })
            ->orderBy('movement_date', 'desc')
            ->orderBy('created_at', 'desc');

        $movements = $query->paginate($this->quantity ?? 15);

        // Get departments for filter
        $departments = Department::orderBy('name')->get();
        $branches = Branch::all();

        return view('livewire.super-admin.inventory.stock-movements', [
            'movements' => $movements,
            'departments' => $departments,
            'branches' => $branches,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterShift = '';
        $this->filterDepartment = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterBranch = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterShift()
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatedFilterBranch()
    {
        $this->resetPage();
    }
}