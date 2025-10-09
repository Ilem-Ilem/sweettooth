<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\StockTake;
use Livewire\Component;
use Livewire\WithPagination;

class StockTakes extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBranch = '';
    public $filterType = '';
    public $filterStatus = '';

    public $stockTakeId;
    public $showVerificationModal = false;
    public $verificationNotes = '';

    public function render()
    {
        $query = StockTake::with(['branch', 'conductor', 'verifier', 'stockTakeDetails'])
            ->when($this->search, function ($q) {
                $q->where('stock_take_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('conductor', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('stock_take_date', 'desc');

        $stockTakes = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.stock-takes', [
            'stockTakes' => $stockTakes,
            'branches' => $branches,
        ]);
    }

    public function openVerificationModal($id)
    {
        $this->authorize('verify-stock-takes');
        $this->stockTakeId = $id;
        $this->verificationNotes = '';
        $this->showVerificationModal = true;
    }

    public function verifyStockTake()
    {
        $this->authorize('verify-stock-takes');

        $stockTake = StockTake::findOrFail($this->stockTakeId);

        if ($stockTake->status !== 'completed') {
            session()->flash('error', 'Only completed stock takes can be verified.');
            $this->closeModal();
            return;
        }

        $stockTake->verify(auth()->guard('employees')->id());

        if ($this->verificationNotes) {
            $stockTake->update([
                'notes' => $this->verificationNotes,
            ]);
        }

        session()->flash('success', 'Stock take verified successfully.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showVerificationModal = false;
        $this->stockTakeId = null;
        $this->verificationNotes = '';
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterType = '';
        $this->filterStatus = '';
    }
}
