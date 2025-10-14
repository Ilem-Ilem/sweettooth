<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class Stocks extends Component
{
    use WithPagination;

    // Pagination
    public $quantity = 15;
    #[Url(keep: true)]
    public $b_id;
    // Filters
    public $search = '';

    public $filterCategory = '';
    public $filterStatus = '';
    public $filterHealthStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Edit Modal
    public $showEditModal = false;
    public $editingStockId = null;
    public $quantity_available = 0;
    public $quantity_reserved = 0;
    public $quantity_damaged = 0;
    public $average_cost = 0;
    public $health_status = 'good';
    public $expiry_date = '';
    public $notes = '';

    protected $rules = [
        'quantity_available' => 'required|numeric|min:0',
        'quantity_reserved' => 'required|numeric|min:0',
        'quantity_damaged' => 'required|numeric|min:0',
        'average_cost' => 'required|numeric|min:0',
        'health_status' => 'required|in:good,warning,critical,expired',
        'expiry_date' => 'nullable|date',
        'notes' => 'nullable|string|max:500',
    ];

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
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
                        $query->whereColumn('stocks.quantity_available', '<', 'items.reorder_level')
                            ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStatus === 'overstock') {
                    $q->whereHas('item', function ($query) {
                        $query->whereRaw('(stocks.quantity_available + stocks.quantity_reserved + stocks.quantity_damaged) > items.max_stock_level')
                            ->where('items.max_stock_level', '>', 0);
                    });
                }
            })
            ->when($this->filterHealthStatus, fn($q) => $q->where('health_status', $this->filterHealthStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('last_stock_take_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('last_stock_take_date', '<=', $this->filterDateTo))
            ->orderBy('updated_at', 'desc');

        $stocks = $query->paginate($this->quantity ?? 15);

        return view('livewire.branch-dashboard.inventory.stocks', [
            'stocks' => $stocks,
        ]);
    }

    public function openEditModal($stockId)
    {
        $branchId = $this->getBranchId();

        $stock = Stock::with('item')
            ->where('id', $stockId)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        $this->editingStockId = $stock->id;
        $this->quantity_available = $stock->quantity_available;
        $this->quantity_reserved = $stock->quantity_reserved;
        $this->quantity_damaged = $stock->quantity_damaged;
        $this->average_cost = $stock->average_cost;
        $this->health_status = $stock->health_status;
        $this->expiry_date = $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : '';
        $this->notes = '';
        $this->showEditModal = true;
    }

    public function updateStock()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $branchId = $this->getBranchId();

            $stock = Stock::where('id', $this->editingStockId)
                ->where('branch_id', $branchId)
                ->firstOrFail();

            $oldQuantityAvailable = $stock->quantity_available;

            $stock->update([
                'quantity_available' => $this->quantity_available,
                'quantity_reserved' => $this->quantity_reserved,
                'quantity_damaged' => $this->quantity_damaged,
                'average_cost' => $this->average_cost,
                'health_status' => $this->health_status,
                'expiry_date' => $this->expiry_date ?: null,
                'last_stock_take_date' => now(),
            ]);

            // Record stock movement if quantity changed
            if ($oldQuantityAvailable != $this->quantity_available) {
                $quantityDiff = $this->quantity_available - $oldQuantityAvailable;

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => $quantityDiff > 0 ? 'in' : 'out',
                    'quantity' => abs($quantityDiff),
                    'quantity_before' => $oldQuantityAvailable,
                    'quantity_after' => $this->quantity_available,
                    'reference_type' => 'manual_adjustment',
                    'reference_id' => null,
                    'moved_by' => Auth::guard('employees')->id(),
                    'movement_date' => now(),
                    'notes' => $this->notes ?: 'Manual stock adjustment from Stocks page',
                ]);
            }

            DB::commit();
            session()->flash('success', 'Stock updated successfully.');
            $this->closeEditModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating stock: ' . $e->getMessage());
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingStockId = null;
        $this->resetValidation();
        $this->resetEditFields();
    }

    public function resetEditFields()
    {
        $this->quantity_available = 0;
        $this->quantity_reserved = 0;
        $this->quantity_damaged = 0;
        $this->average_cost = 0;
        $this->health_status = 'good';
        $this->expiry_date = '';
        $this->notes = '';
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterCategory = '';
        $this->filterStatus = '';
        $this->filterHealthStatus = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterHealthStatus()
    {
        $this->resetPage();
    }
}
