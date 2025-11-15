<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\StockTake;
use App\Models\StockTakeDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url, On};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class StockTakes extends Component
{
    use WithPagination;
#[Url(keep:true)]
    public $b_id;
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    public $showModal = false;
    public $stockTakeId;
    public $stock_take_date;
    public $type = 'full';
    public $notes;

    public $stockTakeItems = [];

    protected $rules = [
        'stock_take_date' => 'required|date',
        'type' => 'required|in:full,partial,cycle',
        'notes' => 'nullable|string',
    ];
    
    
    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->stock_take_date = now()->format('Y-m-d');
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = StockTake::with(['branch', 'conductor', 'verifier', 'stockTakeDetails'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where('stock_take_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('conductor', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('stock_take_date', 'desc');

        $stockTakes = $query->paginate(15);

        return view('livewire.branch-dashboard.inventory.stock-takes', [
            'stockTakes' => $stockTakes,
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-stock-takes'); // TODO: Enable permissions after testing
        $this->resetFields();
        $this->loadStockItems();
        $this->showModal = true;
    }

    public function loadStockItems()
    {
        $branchId = $this->getBranchId();
        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->get();

        $this->stockTakeItems = [];
        foreach ($stocks as $stock) {
            $this->stockTakeItems[] = [
                'stock_id' => $stock->id,
                'item_name' => $stock->item->name,
                'system_quantity' => $stock->total_quantity,
                'physical_quantity' => '',
                'uom' => $stock->item->uom,
            ];
        }
    }

    public function save()
    {
        // $this->authorize('create-stock-takes'); // TODO: Enable permissions after testing
        $this->validate();

        DB::beginTransaction();
        try {
            $branchId = $this->getBranchId();
            $branch = Auth::guard('employees')->user()->employee->branch;

            $stockTakeNumber = StockTake::generateStockTakeNumber($branch->code);

            $stockTake = StockTake::create([
                'branch_id' => $branchId,
                'stock_take_number' => $stockTakeNumber,
                'stock_take_date' => $this->stock_take_date,
                'type' => $this->type,
                'conducted_by' => Auth::guard('employees')->id(),
                'status' => 'in_progress',
                'notes' => $this->notes,
            ]);

            foreach ($this->stockTakeItems as $item) {
                if ($item['physical_quantity'] !== '' && $item['physical_quantity'] !== null) {
                    $variance = $item['physical_quantity'] - $item['system_quantity'];
                    $varianceType = $variance == 0 ? 'match' : ($variance > 0 ? 'surplus' : 'shortage');

                    StockTakeDetail::create([
                        'stock_take_id' => $stockTake->id,
                        'stock_id' => $item['stock_id'],
                        'system_quantity' => $item['system_quantity'],
                        'physical_quantity' => $item['physical_quantity'],
                        'variance_quantity' => $variance,
                        'variance_type' => $varianceType,
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Stock take created successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating stock take: ' . $e->getMessage());
        }
    }

    public function completeStockTake($id)
    {
        // $this->authorize('create-stock-takes'); // TODO: Enable permissions after testing

        $stockTake = StockTake::findOrFail($id);

        if ($stockTake->branch_id !== $this->getBranchId()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        if ($stockTake->status !== 'in_progress') {
            session()->flash('error', 'Only in-progress stock takes can be completed.');
            return;
        }

        $stockTake->markAsCompleted();
        session()->flash('success', 'Stock take marked as completed.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->stockTakeId = null;
        $this->stock_take_date = now()->format('Y-m-d');
        $this->type = 'full';
        $this->notes = '';
        $this->stockTakeItems = [];
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterStatus = '';
    }
}
