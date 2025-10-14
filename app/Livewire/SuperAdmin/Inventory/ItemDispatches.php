<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\ItemDispatch;
use Livewire\Component;
use Livewire\WithPagination;

class ItemDispatches extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBranch = '';
    public $filterShift = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterReceived = '';

    public function render()
    {
        $query = ItemDispatch::with(['itemRequest.branch', 'itemRequest.department', 'item', 'dispatcher', 'receiver'])
            ->when($this->search, function ($q) {
                $q->whereHas('itemRequest', function ($query) {
                    $query->where('request_number', 'like', '%' . $this->search . '%');
                })->orWhereHas('item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, function ($q) {
                $q->whereHas('itemRequest', function ($query) {
                    $query->where('branch_id', $this->filterBranch);
                });
            })
            ->when($this->filterShift, fn($q) => $q->where('shift', $this->filterShift))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('dispatch_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('dispatch_time', '<=', $this->filterDateTo))
            ->when($this->filterReceived !== '', function ($q) {
                if ($this->filterReceived === '1') {
                    $q->whereNotNull('received_time');
                } else {
                    $q->whereNull('received_time');
                }
            })
            ->orderBy('dispatch_time', 'desc');

        $dispatches = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.item-dispatches', [
            'dispatches' => $dispatches,
            'branches' => $branches,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterShift = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterReceived = '';
    }
}
