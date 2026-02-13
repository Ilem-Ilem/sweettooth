<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Livewire\BaseComponent;
use App\Models\Item;
use App\Models\RecipeIngredient;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\UnitOfMeasure;
use App\Services\AuditService;
use App\Services\InventoryApprovalService;
use App\Services\SidebarVisibilityService;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Layout('components.layouts.app.branch-dashboard')]
class Items extends BaseComponent
{
    use Exportable;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public function mount()
    {
        $this->b_id = current_branch_id();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
        $this->resetFilters();
    }

    public ?int $quantity = 10;

    public ?string $search = null;

    public ?string $advancedSearch = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    // Filters
    public ?string $filterCategory = null;

    public ?string $filterStatus = null;

    public ?string $filterStockLevel = null;

    // Form Fields
    public ?int $itemId = null;

    public string $name = '';

    public string $sku = '';

    public string $category = '';

    public ?int $uom_id = null;

    public ?float $reorder_level = null;

    public ?float $max_stock_level = null;

    public string $status = 'active';

    public bool $isEditing = false;

    // Modals
    public bool $showModal = false;

    public bool $showAuditModal = false;

    public ?string $auditAction = null;

    public string $auditReason = '';

    public ?int $pendingItemId = null;

    // Stock Modal
    public bool $showStockModal = false;

    public ?int $stockItemId = null;

    public float $stockQuantity = 0.0;

    public float $stockReserved = 0.0;

    public float $stockDamaged = 0.0;

    public string $stockNotes = '';

    // Temporary storage for pending item data (after Save, before audit)
    public array $pendingItemData = [];

    // Temporary storage for pending stock adjustment data
    public array $pendingStockData = [];

    // Deletion data for audit
    public array $relatedDataToDelete = [];

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDeleteItems'],
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
        return $this->b_id ?? current_branch_id();
    }

    // ===================================================================
    // MAIN SAVE METHOD — This is the key fix
    // ===================================================================
    // Then in your save() method — this will now work perfectly
    public function save()
    {
        $this->validate($this->itemValidationRules());

        $data = [
            'branch_id' => $this->getBranchId(),
            'name' => $this->name,
            'sku' => $this->sku,
            'category' => $this->category,
            'uom_id' => $this->uom_id,
            'reorder_level' => $this->reorder_level ?? 0,
            'max_stock_level' => $this->max_stock_level ?? 0,
            'status' => $this->status,
        ];

        // Only Super Admin level users can create items directly without approval
        $user = Auth::user();
        $roleLevel = SidebarVisibilityService::getRoleLevel($user);

        if ($roleLevel >= \App\Services\SidebarVisibilityService::LEVEL_SUPER_ADMIN) {
            $this->executeImmediateSave($data);
            return;
        }

        // This now works — because $pendingItemData is public!
        $this->pendingItemData = $data;
        $this->auditAction = $this->isEditing ? 'update_item' : 'create_item';
        $this->auditReason = '';
        $this->showModal = false;
        $this->showAuditModal = true;
    }

   
    private function itemValidationRules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category' => 'required|in:raw_material,packaging,consumable,equipment',
            'uom_id' => 'required|exists:units_of_measure,id',
            'reorder_level' => 'nullable|numeric|min:0',        // ← FIXED
            'max_stock_level' => 'nullable|numeric|min:0',      // ← also make sure this one is correct
            'status' => 'required|in:active,inactive',
        ];

        if ($this->isEditing) {
            $rules['sku'] = 'required|string|max:255|unique:items,sku,'.$this->itemId;
        } else {
            $rules['sku'] = 'required|string|max:255|unique:items,sku';
        }

        return $rules;
    }

    private function executeImmediateSave(array $data)
    {
        if ($this->isEditing && $this->itemId) {
            $item = Item::where('id', $this->itemId)
                ->where('branch_id', $this->getBranchId())
                ->firstOrFail();

            // Store original values for change tracking
            $oldName = $item->name;
            $oldCategory = $item->category;
            $oldUom = $item->unitOfMeasure?->symbol;
            $oldReorderLevel = $item->reorder_level;
            $oldMaxStockLevel = $item->max_stock_level;
            $oldStatus = $item->status;

            $item->update($data);

            // Log the item update with changes
            $changes = [];
            if ($oldName !== $this->name) {
                $changes[] = "Name: {$oldName} → {$this->name}";
            }
            if ($oldCategory !== $this->category) {
                $changes[] = "Category: {$oldCategory} → {$this->category}";
            }
            $newUom = UnitOfMeasure::find($this->uom_id)?->symbol;
            if ($oldUom !== $newUom) {
                $changes[] = "UOM: {$oldUom} → {$newUom}";
            }
            if ((float) $oldReorderLevel !== (float) $this->reorder_level) {
                $changes[] = "Reorder Level: {$oldReorderLevel} → {$this->reorder_level}";
            }
            if ((float) $oldMaxStockLevel !== (float) $this->max_stock_level) {
                $changes[] = "Max Stock: {$oldMaxStockLevel} → {$this->max_stock_level}";
            }
            if ($oldStatus !== $this->status) {
                $changes[] = "Status: {$oldStatus} → {$this->status}";
            }

            AuditService::log(
                current_actor(),
                'update',
                $item,
                "Updated item '{$item->name}' (SKU: {$item->sku}). Changes: ".implode(', ', $changes),
                'completed'
            );

            $this->toast()->success('Item updated successfully!')->send();
        } else {
            $item = Item::create($data);

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

            // Log the item creation
            AuditService::log(
                current_actor(),
                'create',
                $item,
                "Created item '{$item->name}' (SKU: {$item->sku}) in category '{$item->category}'. ".
                "UOM: {$item->unitOfMeasure?->symbol}, Reorder Level: {$item->reorder_level}, Max Stock: {$item->max_stock_level}",
                'completed'
            );

            $this->toast()->success('Item created successfully!')->send();
        }

        $this->closeModal();
    }

    // ===================================================================
    // AUDIT SUBMISSION — After employee enters reason
    // ===================================================================
    public function submitAuditRequest()
    {
        $this->validate([
            'auditReason' => 'required|string|min:10|max:500',
        ]);

        try {
            $user = current_actor();
            $request = null;
            $msg = '';

            if ($this->auditAction === 'create_item') {
                // Ensure we have pending item data
                if (empty($this->pendingItemData)) {
                    throw new \Exception('No item data found. Please fill in the form and save first.');
                }
                $request = InventoryApprovalService::requestItemCreation(
                    $user,
                    $this->pendingItemData,
                    $this->auditReason
                );
                $msg = 'Item creation request submitted for approval!';
            } elseif ($this->auditAction === 'update_item') {
                // Ensure we have pending item data
                if (empty($this->pendingItemData)) {
                    throw new \Exception('No item data found. Please fill in the form and save first.');
                }
                $request = InventoryApprovalService::requestItemUpdate(
                    $user,
                    $this->itemId,
                    $this->pendingItemData,
                    $this->auditReason
                );
                $msg = 'Item update request submitted for approval!';
            } elseif ($this->auditAction === 'stock_adjustment') {
                // Handle stock adjustment audit request
                if (empty($this->pendingStockData)) {
                    throw new \Exception('No stock data found. Please save the stock adjustment first.');
                }
                $request = InventoryApprovalService::requestStockAdjustment(
                    $user,
                    Stock::where('item_id', $this->pendingItemId)
                        ->where('branch_id', $this->getBranchId())
                        ->firstOrFail()->id,
                    $this->pendingStockData,
                    $this->auditReason
                );
                $msg = 'Stock adjustment request submitted for approval!';
            } else {
                throw new \Exception('Unknown audit action: '.$this->auditAction);
            }

            // Verify request was created
            if (! $request || ! $request->id) {
                throw new \Exception('Failed to create approval request');
            }

            $this->toast()->success($msg.' (Request ID: '.$request->id.')')->send();
            $this->closeAuditModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->toast()->error('Failed: '.$e->getMessage())->send();
        }
    }

    // ===================================================================
    // MODAL OPENERS
    // ===================================================================
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $branchId = $this->getBranchId();
        $item = Item::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

        $this->itemId = $item->id;
        $this->name = $item->name;
        $this->sku = $item->sku;
        $this->category = $item->category;
        $this->uom_id = $item->uom_id;
        $this->reorder_level = $item->reorder_level;
        $this->max_stock_level = $item->max_stock_level;
        $this->status = $item->status;
        $this->isEditing = true;
        $this->showModal = true;
    }

    // ===================================================================
    // STOCK MODAL (unchanged logic, just cleaned)
    // ===================================================================
    public function openStockModal($itemId)
    {
        $branchId = $this->getBranchId();
        $stock = Stock::where('item_id', $itemId)->where('branch_id', $branchId)->first();

        $this->stockItemId = $itemId;
        $this->stockQuantity = $stock?->quantity_available ?? 0;
        $this->stockReserved = $stock?->quantity_reserved ?? 0;
        $this->stockDamaged = $stock?->quantity_damaged ?? 0;
        $this->stockNotes = '';
        $this->showStockModal = true;
    }

    public function saveStock()
    {
        $this->validate([
            'stockQuantity' => 'required|numeric|min:0',
            'stockReserved' => 'required|numeric|min:0',
            'stockDamaged' => 'required|numeric|min:0',
        ]);

        // Only Super Admin level users can update stock directly without approval
        $user = Auth::user();
        $roleLevel = SidebarVisibilityService::getRoleLevel($user);

        if ($roleLevel >= SidebarVisibilityService::LEVEL_SUPER_ADMIN) {
            $this->performStockUpdate();
        } else {
            $this->pendingItemId = $this->stockItemId;
            $this->auditAction = 'stock_adjustment';

            // Store pending stock data before closing modal
            $this->pendingStockData = [
                'quantity_available' => (float) $this->stockQuantity,
                'quantity_reserved' => (float) $this->stockReserved,
                'quantity_damaged' => (float) $this->stockDamaged,
                'notes' => $this->stockNotes,
            ];

            $this->closeStockModal();
            $this->showAuditModal = true;
        }
    }

    private function performStockUpdate()
    {
        $stock = Stock::firstOrCreate(
            ['item_id' => $this->stockItemId, 'branch_id' => $this->getBranchId()],
            ['quantity_available' => 0, 'quantity_reserved' => 0, 'quantity_damaged' => 0, 'health_status' => 'good']
        );

        $old = $stock->quantity_available;
        $stock->update([
            'quantity_available' => $this->stockQuantity,
            'quantity_reserved' => $this->stockReserved,
            'quantity_damaged' => $this->stockDamaged,
            'last_stock_take_date' => now(),
        ]);

        $actor = current_actor();

        StockMovement::create([
            'stock_id' => $stock->id,
            'type' => 'adjustment',
            'quantity' => abs($this->stockQuantity - $old),
            'quantity_before' => $old,
            'quantity_after' => $this->stockQuantity,
            'reference_type' => null,
            'reference_id' => null,
            'moved_by_id' => $actor->id,
            'moved_by_type' => get_class($actor),
            'notes' => $this->stockNotes ?: 'Manual adjustment',
            'movement_date' => now(),
        ]);

        $this->toast()->success('Stock updated!')->send();
        $this->closeStockModal();
    }

    // ===================================================================
    // DELETE (with audit)
    // ===================================================================
    public function delete($id)
    {
        $this->pendingItemId = $id;

        $branchId = $this->getBranchId();
        $item = Item::where('id', $id)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // Gather related data that will be deleted
        $this->relatedDataToDelete = $this->gatherRelatedDataForDeletion($item);

        if (is_super_admin()) {
            $this->showDeletionConfirmationDialog($item);
        } else {
            $this->auditAction = 'delete_item';
            $this->showAuditModal = true;
        }
    }

    private function gatherRelatedDataForDeletion($item): array
    {
        $relatedData = [
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
            ],
            'recipes' => [],
            'products' => [],
            'stocks' => [],
            'purchases' => [],
            'stock_movements' => [],
        ];

        // Get recipes that use this item as an ingredient
        // Use RecipeIngredient to find recipes (inverse relationship)
        $recipeIngredients = RecipeIngredient::where('item_id', $item->id)
            ->with('recipe.product')
            ->get();

        if ($recipeIngredients->count() > 0) {
            $recipes = $recipeIngredients->map(fn ($ri) => [
                'id' => $ri->recipe->id,
                'name' => $ri->recipe->product_name ?? $ri->recipe->product?->name ?? 'Unknown Recipe',
                'product_id' => $ri->recipe->product_id,
                'product_name' => $ri->recipe->product?->name ?? 'No Product',
                'quantity' => $ri->quantity,
                'uom' => $ri->uom,
            ])
                ->unique('id')
                ->values();

            $relatedData['recipes'] = [
                'count' => $recipes->count(),
                'items' => $recipes->toArray(),
                'description' => "Used in {$recipes->count()} recipe(s)",
            ];

            // Get affected products
            $affectedProducts = $recipeIngredients->map(fn ($ri) => $ri->recipe->product)
                ->filter()
                ->unique('id')
                ->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                ])
                ->values();

            if ($affectedProducts->count() > 0) {
                $relatedData['products'] = [
                    'count' => $affectedProducts->count(),
                    'items' => $affectedProducts->toArray(),
                    'description' => "Recipes for {$affectedProducts->count()} product(s) will be affected",
                ];
            }
        }

        // Get stocks for this item
        $stocks = $item->stocks()
            ->get()
            ->map(fn ($stock) => [
                'id' => $stock->id,
                'branch_name' => $stock->branch->name ?? 'Unknown',
                'quantity_available' => $stock->quantity_available,
                'quantity_reserved' => $stock->quantity_reserved,
                'quantity_damaged' => $stock->quantity_damaged,
            ]);

        if ($stocks->count() > 0) {
            $relatedData['stocks'] = [
                'count' => $stocks->count(),
                'items' => $stocks->toArray(),
                'description' => "Stock records in {$stocks->count()} branch(es)",
            ];
        }

        // Get purchases containing this item
        $purchases = $item->purchaseItems()
            ->with('purchase')
            ->get()
            ->map(fn ($pi) => [
                'purchase_id' => $pi->purchase->id,
                'purchase_number' => $pi->purchase->purchase_number ?? 'N/A',
                'quantity' => $pi->quantity,
                'status' => $pi->purchase->status,
            ]);

        if ($purchases->count() > 0) {
            $relatedData['purchases'] = [
                'count' => $purchases->count(),
                'items' => $purchases->toArray(),
                'description' => "Referenced in {$purchases->count()} purchase order(s)",
            ];
        }

        // Get stock movements for this item
        $stockMovements = $item->stocks()
            ->with(['stockMovements'])
            ->get()
            ->flatMap(fn ($stock) => $stock->stockMovements)
            ->count();

        if ($stockMovements > 0) {
            $relatedData['stock_movements'] = [
                'count' => $stockMovements,
                'description' => "{$stockMovements} stock movement record(s)",
            ];
        }

        return $relatedData;
    }

    private function showDeletionConfirmationDialog($item): void
    {
        $relatedSummary = $this->buildDeletionSummary();

        $this->dialog()
            ->confirm(
                'Delete Item: '.$item->name,
                'This will also delete the following related data:'.PHP_EOL.$relatedSummary,
                'confirmedDelete'
            )
            ->send();
    }

    private function buildDeletionSummary(): string
    {
        $summary = '';

        if (! empty($this->relatedDataToDelete['recipes'])) {
            $count = $this->relatedDataToDelete['recipes']['count'];
            $summary .= "\n• Recipes: {$count} recipe(s) using this item";
        }

        if (! empty($this->relatedDataToDelete['stocks'])) {
            $count = $this->relatedDataToDelete['stocks']['count'];
            $summary .= "\n• Stock Records: {$count} branch(es)";
        }

        if (! empty($this->relatedDataToDelete['purchases'])) {
            $count = $this->relatedDataToDelete['purchases']['count'];
            $summary .= "\n• Purchases: {$count} purchase order(s)";
        }

        if (! empty($this->relatedDataToDelete['stock_movements'])) {
            $count = $this->relatedDataToDelete['stock_movements']['count'];
            $summary .= "\n• Stock Movements: {$count} record(s)";
        }

        return $summary ?: "\nNo related data to delete.";
    }

    public function confirmedDelete()
    {
        $branchId = $this->getBranchId();
        $item = Item::where('id', $this->pendingItemId)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        $itemName = $item->name;
        $itemSku = $item->sku;

        $item->delete();

        // Log the item deletion
        AuditService::log(
            current_actor(),
            'delete',
            $item,
            "Deleted item '{$itemName}' (SKU: {$itemSku})",
            'completed'
        );

        $this->toast()->success('Item deleted!')->send();
    }

    public function submitItemDeletionRequest()
    {
        $this->validate(['auditReason' => 'required|string|min:10|max:500']);

        try {
            // Include related data in the deletion request
            $deletionData = [
                'item_id' => $this->pendingItemId,
                'related_data' => $this->relatedDataToDelete,
            ];

            $request = InventoryApprovalService::requestItemDeletion(
                current_actor(),
                $this->pendingItemId,
                $this->auditReason,
                $deletionData
            );

            // Verify request was created
            if (! $request || ! $request->id) {
                throw new \Exception('Failed to create approval request');
            }

            $this->toast()->success('Deletion request submitted! (Request ID: '.$request->id.')')->send();
            $this->closeAuditModal();
        } catch (\Exception $e) {
            $this->toast()->error('Failed: '.$e->getMessage())->send();
        }
    }

    // ===================================================================
    // UTILITIES
    // ===================================================================
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeStockModal()
    {
        $this->showStockModal = false;
        $this->reset(['stockItemId', 'stockQuantity', 'stockReserved', 'stockDamaged', 'stockNotes']);
    }

    public function closeAuditModal()
    {
        $this->showAuditModal = false;
        $this->auditReason = '';
        $this->auditAction = null;

        // If there was pending item data, reopen the item modal
        if (!empty($this->pendingItemData)) {
            // Repopulate the form with the pending data
            $this->itemId = $this->pendingItemData['item_id'] ?? null;
            $this->name = $this->pendingItemData['name'] ?? '';
            $this->sku = $this->pendingItemData['sku'] ?? '';
            $this->category = $this->pendingItemData['category'] ?? '';
            $this->uom_id = $this->pendingItemData['uom_id'] ?? null;
            $this->reorder_level = $this->pendingItemData['reorder_level'] ?? null;
            $this->max_stock_level = $this->pendingItemData['max_stock_level'] ?? null;
            $this->status = $this->pendingItemData['status'] ?? 'active';
            $this->isEditing = $this->itemId ? true : false;
            $this->showModal = true;
        }

        $this->pendingItemId = null;
        $this->pendingItemData = [];
        $this->pendingStockData = [];
        $this->relatedDataToDelete = [];
    }

    private function resetForm()
    {
        $this->reset(['itemId', 'name', 'sku', 'category', 'uom_id', 'reorder_level', 'max_stock_level', 'status', 'isEditing']);
    }

    public function updatedName()
    {
        if (! $this->isEditing) {
            $this->generateSku();
        }
    }

    public function updatedCategory()
    {
        if (! $this->isEditing) {
            $this->generateSku();
        }
    }

    private function generateSku()
    {
        if ($this->isEditing || empty($this->name) || empty($this->category)) {
            return;
        }
        $prefix = ['raw_material' => 'RM', 'packaging' => 'PK', 'consumable' => 'CN', 'equipment' => 'EQ'][$this->category] ?? 'IT';
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $this->name), 0, 3));
        $rand = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $this->sku = "$prefix-$code-$rand";
    }

    // ===================================================================
    // RENDER + FILTERS (unchanged from your original)
    // ===================================================================
    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        // Get low stock items efficiently (cached in component, with limit)
        $lowStockItems = Item::query()
            ->with(['stocks', 'unitOfMeasure'])
            ->where('branch_id', $this->getBranchId())
            ->where('status', 'active')
            ->where('reorder_level', '>', 0)
            ->whereHas('stocks', function ($q) {
                $q->where('branch_id', $this->getBranchId())
                    ->whereColumn('quantity_available', '<=', 'items.reorder_level');
            })
            ->limit(10)
            ->get();

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
            'lowStockItems' => $lowStockItems,
        ]);
    }

    protected function getFilteredQuery()
    {
        return Item::query()
            ->with(['branch', 'unitOfMeasure', 'stocks' => fn ($q) => $q->where('branch_id', $this->getBranchId())])
            ->where('branch_id', $this->getBranchId())
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('sku', 'like', "%{$this->search}%"))
            ->when($this->filterCategory, fn ($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterStockLevel, function ($q) {
                $branchId = $this->getBranchId();
                if ($this->filterStockLevel === 'low') {
                    $q->whereHas('stocks', fn ($sq) => $sq->where('branch_id', $branchId)->whereColumn('quantity_available', '<', 'items.reorder_level'));
                } elseif ($this->filterStockLevel === 'out_of_stock') {
                    $q->whereHas('stocks', fn ($sq) => $sq->where('branch_id', $branchId)->where('quantity_available', '<=', 0));
                }
            })
            ->latest();
    }

    public function applyFilters()
    {
        // Filters are applied automatically via wire:model.live, so this is a no-op
        // But we need this method to exist to prevent the MethodNotFoundException
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCategory', 'filterStatus', 'filterStockLevel']);
        $this->resetPage();
    }

    public function exportExcel()
    {
        try {
            // Export ALL items without pagination or search filters
            $items = Item::query()
                ->where('branch_id', $this->getBranchId())
                ->orderBy('sku')
                ->get();

            $data = $items->map(fn ($item) => [
                'sku' => $item->sku,
                'name' => $item->name,
                'category' => $item->category,
                'reorder_level' => $item->reorder_level,
                'status' => $item->status,
                'uom' => $item->uom,
            ])->toArray();

            if (empty($data)) {
                $this->toast()->warning('No items to export.')->send();

                return;
            }

            return $this->export(
                'inventory-items-'.now()->format('Y-m-d'),
                collect($data),
                'exports.inventory.items',
                'excel',
                true
            );
        } catch (\Exception $e) {
            $this->toast()->error('Export failed: '.$e->getMessage())->send();

            return;
        }
    }

    public function exportCSV()
    {
        try {
            // Export ALL items without pagination or search filters
            $items = Item::query()
                ->where('branch_id', $this->getBranchId())
                ->orderBy('sku')
                ->get();

            $csvData = [
                ['SKU', 'Name', 'Category', 'Reorder Level', 'Status', 'UOM'],
            ];

            foreach ($items as $item) {
                $csvData[] = [
                    $item->sku,
                    $item->name,
                    $item->category,
                    $item->reorder_level,
                    $item->status,
                    $item->uom,
                ];
            }

            $filename = 'inventory-items-'.now()->format('Y-m-d-His').'.csv';
            $handle = fopen('php://temp', 'r+');

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }

            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response()->streamDownload(function () use ($csv) {
                echo $csv;
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            $this->toast()->error('Export failed: '.$e->getMessage())->send();

            return;
        }
    }
}
