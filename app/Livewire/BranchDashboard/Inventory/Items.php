<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Item;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Items extends Component
{
    use WithPagination;

    public $itemId;
    public $name;
    public $sku;
    public $category = '';
    public $uom = '';
    public $reorder_level;
    public $max_stock_level;
    public $status = 'active';

    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public $showModal = false;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:items,sku',
        'category' => 'required|in:raw_material,packaging,consumable,equipment',
        'uom' => 'required|in:grams,kg,liters,ml,pcs,units,bags,cartons',
        'reorder_level' => 'nullable|numeric|min:0',
        'max_stock_level' => 'nullable|numeric|min:0',
        'status' => 'required|in:active,inactive',
    ];

    public function render()
    {
        $branchId = Auth::user()->employee->branch_id;

        $query = Item::with(['branch', 'stocks'])
            ->forBranch($branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', 'desc');

        $items = $query->paginate(15);

        // Get items below reorder level
        $lowStockCount = Item::forBranch($branchId)
            ->get()
            ->filter(fn($item) => $item->isBelowReorderLevel())
            ->count();

        return view('livewire.branch-dashboard.inventory.items', [
            'items' => $items,
            'lowStockCount' => $lowStockCount,
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create-items');
        $this->resetFields();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->authorize('edit-items');
        $item = Item::findOrFail($id);

        // Ensure item belongs to user's branch
        if ($item->branch_id !== Auth::user()->employee->branch_id) {
            abort(403);
        }

        $this->itemId = $item->id;
        $this->name = $item->name;
        $this->sku = $item->sku;
        $this->category = $item->category;
        $this->uom = $item->uom;
        $this->reorder_level = $item->reorder_level;
        $this->max_stock_level = $item->max_stock_level;
        $this->status = $item->status;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('edit-items');
            $this->rules['sku'] = 'required|string|max:255|unique:items,sku,' . $this->itemId;
        } else {
            $this->authorize('create-items');
        }

        $this->validate();

        $branchId = Auth::user()->employee->branch_id;

        $data = [
            'branch_id' => $branchId,
            'name' => $this->name,
            'sku' => $this->sku,
            'category' => $this->category,
            'uom' => $this->uom,
            'reorder_level' => $this->reorder_level,
            'max_stock_level' => $this->max_stock_level,
            'status' => $this->status,
        ];

        if ($this->isEditing) {
            $item = Item::findOrFail($this->itemId);

            // Ensure item belongs to user's branch
            if ($item->branch_id !== $branchId) {
                abort(403);
            }

            $item->update($data);
            session()->flash('success', 'Item updated successfully.');
        } else {
            Item::create($data);
            session()->flash('success', 'Item created successfully.');
        }

        $this->closeModal();
        $this->resetFields();
    }

    public function delete($id)
    {
        $this->authorize('delete-items');

        $item = Item::findOrFail($id);

        // Ensure item belongs to user's branch
        if ($item->branch_id !== Auth::user()->employee->branch_id) {
            abort(403);
        }

        $item->delete();

        session()->flash('success', 'Item deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->itemId = null;
        $this->name = '';
        $this->sku = '';
        $this->category = '';
        $this->uom = '';
        $this->reorder_level = null;
        $this->max_stock_level = null;
        $this->status = 'active';
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterCategory = '';
        $this->filterStatus = '';
    }
}
