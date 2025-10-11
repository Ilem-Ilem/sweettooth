<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Livewire\BaseComponent;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\{Layout, Url};


#[Layout('components.layouts.app.branch-dashboard')]
class Items extends BaseComponent
{
    #[Url(keep:true)]
    public $b_id;
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Filter fields
    public ?string $filterCategory = null;
    public ?string $filterStatus = null;
    public ?string $filterStockLevel = null;

    // Modal states
    public bool $showModal = false;
    public ?int $itemId = null;
    public bool $isEditing = false;

    // Stock management
    public bool $showStockModal = false;
    public ?int $stockItemId = null;
    public $stockQuantity = 0;
    public $stockReserved = 0;
    public $stockDamaged = 0;
    public string $stockNotes = '';

    // Item form fields
    public string $name = '';
    public string $sku = '';
    public string $category = '';
    public string $uom = '';
    public $reorder_level;
    public $max_stock_level;
    public string $status = 'active';

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    ];

    protected function getModelClass(): string
    {
        return Item::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function getBranchId()
    {
        return request()->query('b_id');
    }

    protected function getFilteredQuery()
    {
        $branchId = $this->getBranchId();

        return Item::query()
            ->with(['branch', 'stocks'])
            ->where('branch_id', $this->b_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('sku', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterStockLevel, function ($query) use ($branchId) {
                if ($this->filterStockLevel === 'low') {
                    $query->whereHas('stocks', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                          ->whereColumn('quantity_available', '<', 'items.reorder_level')
                          ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStockLevel === 'high') {
                    $query->whereHas('stocks', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                          ->whereColumn('quantity_available', '>=', 'items.reorder_level')
                          ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStockLevel === 'out_of_stock') {
                    $query->whereHas('stocks', function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)
                          ->where('quantity_available', '<=', 0);
                    })->orWhereDoesntHave('stocks');
                }
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc');
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->advancedSearch = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->filterCategory = null;
        $this->filterStatus = null;
        $this->filterStockLevel = null;
        $this->resetPage();
    }

    // Generate SKU automatically
    public function updatedCategory()
    {
        $this->generateSku();
    }

    public function updatedName()
    {
        $this->generateSku();
    }

    private function generateSku()
    {
        if (!$this->isEditing && !empty($this->category) && !empty($this->name)) {
            $categoryPrefix = match($this->category) {
                'raw_material' => 'RM',
                'packaging' => 'PK',
                'consumable' => 'CN',
                'equipment' => 'EQ',
                default => 'IT'
            };

            $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $this->name), 0, 3));
            $randomCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $this->sku = $categoryPrefix . '-' . $nameCode . '-' . $randomCode;
        }
    }

    // Export methods
    public function exportExcel()
    {
        $items = $this->getFilteredQuery()->get();

        $csv = "ID,SKU,Name,Category,UOM,Stock,Reorder Level,Status,Created At\n";
        foreach ($items as $item) {
            $currentStock = $item->getCurrentStock($this->getBranchId());
            $csv .= "\"{$item->id}\",\"{$item->sku}\",\"{$item->name}\",\"{$item->category}\",\"{$item->uom}\",\"{$currentStock}\",\"{$item->reorder_level}\",\"{$item->status}\",\"{$item->created_at}\"\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'items-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        return view('livewire.branch-dashboard.inventory.items', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'sku', 'label' => 'SKU'],
                ['index' => 'name', 'label' => 'Item Name'],
                ['index' => 'category', 'label' => 'Category'],
                ['index' => 'uom', 'label' => 'UOM'],
                ['index' => 'stock', 'label' => 'Current Stock'],
                ['index' => 'reorder_level', 'label' => 'Reorder Level'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $item = Item::findOrFail($id);

        // Ensure item belongs to branch
        if ($item->branch_id !== $this->getBranchId()) {
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
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|in:raw_material,packaging,consumable,equipment',
            'uom' => 'required|in:grams,kg,liters,ml,pcs,units,bags,cartons',
            'reorder_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];

        if ($this->isEditing) {
            $rules['sku'] = 'required|string|max:255|unique:items,sku,' . $this->itemId;
        } else {
            $rules['sku'] = 'required|string|max:255|unique:items,sku';
        }

        $this->validate($rules);

        $branchId = $this->getBranchId();

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

        if ($this->isEditing && $this->itemId) {
            $item = Item::findOrFail($this->itemId);

            // Ensure item belongs to branch


            $item->update($data);
            $message = 'Item updated successfully!';
        } else {
            $item = Item::create($data);

            // Create initial stock record
            Stock::create([
                'branch_id' => $item->branch_id,
                'item_id' => $item->id,
                'quantity_available' => 0,
                'quantity_reserved' => 0,
                'quantity_damaged' => 0,
                'average_cost' => 0,
                'last_stock_take_date' => now(),
                'health_status' => 'good',
            ]);

            $message = 'Item created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closeModal();
    }

    // Delete methods
    public function delete($id): void
    {
        $this->itemId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this item?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->itemId) {
            $item = Item::findOrFail($this->itemId);

            // Ensure item belongs to branch


            $item->delete();
            $this->dialog()->success('Success', 'Item deleted successfully!')->send();
            $this->itemId = null;
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->itemId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Bulk Delete
    public function bulkDeleteItems(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' item(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Item::where('branch_id', $this->getBranchId())
            ->whereIn('id', $this->selectedIds)
            ->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' item(s) deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Update stock quantity
    public function updateStock($itemId, $quantity)
    {
        try {
            $item = Item::findOrFail($itemId);

            // Ensure item belongs to branch
            // if ($item->branch_id !== $this->getBranchId()) {
            //     abort(403);
            // }

            $stock = Stock::firstOrCreate(
                [
                    'branch_id' => $item->branch_id,
                    'item_id' => $item->id,
                ],
                [
                    'quantity_available' => 0,
                    'quantity_reserved' => 0,
                    'quantity_damaged' => 0,
                    'average_cost' => 0,
                    'last_stock_take_date' => now(),
                    'health_status' => 'good',
                ]
            );

            $oldQuantity = $stock->quantity_available;
            $stock->quantity_available = $quantity;
            $stock->last_stock_take_date = now();
            $stock->save();

            // Create stock movement record
            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'adjustment',
                'quantity' => abs($quantity - $oldQuantity),
                'quantity_before' => $oldQuantity,
                'quantity_after' => $quantity,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'moved_by' => Auth::guard('employees')->id(),
                'notes' => 'Manual stock adjustment via Items page',
                'movement_date' => now(),
            ]);

            $this->toast()->success('Stock updated successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to update stock: ' . $e->getMessage())->send();
        }
    }

    // Stock Modal Methods
    public function openStockModal($itemId)
    {
        $item = Item::with('stocks')->findOrFail($itemId);

        // Ensure item belongs to branch
        // if ($item->branch_id !== $this->getBranchId()) {
        //     abort(403);
        // }

        $stock = $item->stocks()->where('branch_id', $item->branch_id)->first();

        $this->stockItemId = $itemId;
        $this->stockQuantity = $stock->quantity_available ?? 0;
        $this->stockReserved = $stock->quantity_reserved ?? 0;
        $this->stockDamaged = $stock->quantity_damaged ?? 0;
        $this->stockNotes = '';
        $this->showStockModal = true;
    }

    public function saveStock()
    {
        $this->validate([
            'stockQuantity' => 'required|numeric|min:0',
            'stockReserved' => 'required|numeric|min:0',
            'stockDamaged' => 'required|numeric|min:0',
            'stockNotes' => 'nullable|string|max:500',
        ]);

        try {
            $item = Item::findOrFail($this->stockItemId);

            // Ensure item belongs to branch
            // if ($item->branch_id !== $this->getBranchId()) {
            //     abort(403);
            // }

            $stock = Stock::firstOrCreate(
                [
                    'branch_id' => $item->branch_id,
                    'item_id' => $item->id,
                ],
                [
                    'quantity_available' => 0,
                    'quantity_reserved' => 0,
                    'quantity_damaged' => 0,
                    'average_cost' => 0,
                    'last_stock_take_date' => now(),
                    'health_status' => 'good',
                ]
            );

            $oldQuantity = $stock->quantity_available;

            $stock->update([
                'quantity_available' => $this->stockQuantity,
                'quantity_reserved' => $this->stockReserved,
                'quantity_damaged' => $this->stockDamaged,
                'last_stock_take_date' => now(),
            ]);

            // Create stock movement record
            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => 'adjustment',
                'quantity' => abs($this->stockQuantity - $oldQuantity),
                'quantity_before' => $oldQuantity,
                'quantity_after' => $this->stockQuantity,
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'moved_by' => Auth::guard('employees')->id(),
                'notes' => $this->stockNotes ?: 'Stock adjustment via stock management panel',
                'movement_date' => now(),
            ]);

            $this->toast()->success('Stock updated successfully!')->send();
            $this->closeStockModal();
        } catch (\Exception $e) {
            $this->toast()->error('Failed to update stock: ' . $e->getMessage())->send();
        }
    }

    public function closeStockModal()
    {
        $this->showStockModal = false;
        $this->stockItemId = null;
        $this->stockQuantity = 0;
        $this->stockReserved = 0;
        $this->stockDamaged = 0;
        $this->stockNotes = '';
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
        $this->isEditing = false;
    }
}
