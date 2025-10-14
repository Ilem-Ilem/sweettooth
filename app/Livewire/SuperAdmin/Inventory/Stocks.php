<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Livewire\BaseComponent;
use App\Models\Branch;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;

class Stocks extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Filter fields
    public ?string $filterBranch = null;
    public ?string $filterCategory = null;
    public ?string $filterStockLevel = null; // 'low', 'high', 'out_of_stock', 'critical'
    public ?string $filterHealthStatus = null; // 'good', 'warning', 'critical', 'expired'

    // Item History Modal
    public bool $showHistoryModal = false;
    public ?int $historyItemId = null;
    public ?string $historyDateFrom = null;
    public ?string $historyDateTo = null;
    public ?string $historyMovementType = null;

    // Stock Edit Modal
    public bool $showEditStockModal = false;
    public ?int $editStockId = null;
    public ?float $editQuantityAvailable = null;
    public ?float $editQuantityReserved = null;
    public ?float $editQuantityDamaged = null;
    public ?string $editHealthStatus = null;
    public ?string $editExpiryDate = null;
    public ?string $editNotes = null;

    protected array $bulkActions = [
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    protected function getModelClass(): string
    {
        return Stock::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Stock::query()
            ->with(['branch', 'item'])
            ->when($this->search, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->advancedSearch, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('sku', 'like', '%' . $this->advancedSearch . '%');
                })->orWhereHas('branch', function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->filterBranch, function ($query) {
                $query->where('branch_id', $this->filterBranch);
            })
            ->when($this->filterCategory, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('category', $this->filterCategory);
                });
            })
            ->when($this->filterStockLevel, function ($query) {
                if ($this->filterStockLevel === 'low') {
                    // Below reorder level
                    $query->whereHas('item', function ($q) {
                        $q->whereColumn('stocks.quantity_available', '<', 'items.reorder_level')
                          ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStockLevel === 'critical') {
                    // Below 25% of reorder level
                    $query->whereHas('item', function ($q) {
                        $q->whereRaw('stocks.quantity_available < (items.reorder_level * 0.25)')
                          ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStockLevel === 'high') {
                    // Above reorder level
                    $query->whereHas('item', function ($q) {
                        $q->whereColumn('stocks.quantity_available', '>=', 'items.reorder_level')
                          ->where('items.reorder_level', '>', 0);
                    });
                } elseif ($this->filterStockLevel === 'out_of_stock') {
                    $query->where('quantity_available', '<=', 0);
                } elseif ($this->filterStockLevel === 'overstock') {
                    $query->whereHas('item', function ($q) {
                        $q->whereRaw('(stocks.quantity_available + stocks.quantity_reserved + stocks.quantity_damaged) > items.max_stock_level')
                          ->where('items.max_stock_level', '>', 0);
                    });
                }
            })
            ->when($this->filterHealthStatus, function ($query) {
                $query->where('health_status', $this->filterHealthStatus);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('last_stock_take_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('last_stock_take_date', '<=', $this->dateTo);
            })
            ->orderBy('updated_at', 'desc');
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
        $this->filterBranch = null;
        $this->filterCategory = null;
        $this->filterStockLevel = null;
        $this->filterHealthStatus = null;
        $this->resetPage();
    }

    // Export methods
    public function exportExcel()
    {
        $stocks = $this->getFilteredQuery()->get();

        $csv = "Item,SKU,Branch,Category,Available,Reserved,Damaged,Total,Reorder Level,Health Status,Last Stock Take\n";
        foreach ($stocks as $stock) {
            $branchName = $stock->branch ? $stock->branch->name : 'N/A';
            $itemName = $stock->item ? $stock->item->name : 'N/A';
            $sku = $stock->item ? $stock->item->sku : 'N/A';
            $category = $stock->item ? $stock->item->category : 'N/A';
            $reorderLevel = $stock->item && $stock->item->reorder_level ? $stock->item->reorder_level : 'N/A';

            $csv .= "\"{$itemName}\",\"{$sku}\",\"{$branchName}\",\"{$category}\",\"{$stock->quantity_available}\",\"{$stock->quantity_reserved}\",\"{$stock->quantity_damaged}\",\"{$stock->total_quantity}\",\"{$reorderLevel}\",\"{$stock->health_status}\",\"{$stock->last_stock_take_date}\"\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'stocks-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    // Item History Modal
    public function openHistoryModal($itemId)
    {
        $this->historyItemId = $itemId;
        $this->historyDateFrom = null;
        $this->historyDateTo = null;
        $this->historyMovementType = null;
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->historyItemId = null;
        $this->historyDateFrom = null;
        $this->historyDateTo = null;
        $this->historyMovementType = null;
    }

    // Stock Edit Modal Methods
    public function openEditStockModal($stockId)
    {
        $stock = Stock::find($stockId);
        if (!$stock) {
            $this->toast()->error('Stock record not found')->send();
            return;
        }

        $this->editStockId = $stockId;
        $this->editQuantityAvailable = $stock->quantity_available;
        $this->editQuantityReserved = $stock->quantity_reserved;
        $this->editQuantityDamaged = $stock->quantity_damaged;
        $this->editHealthStatus = $stock->health_status;
        $this->editExpiryDate = $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : null;
        $this->editNotes = null;
        $this->showEditStockModal = true;
    }

    public function saveStockEdit()
    {
        $this->validate([
            'editQuantityAvailable' => 'required|numeric|min:0',
            'editQuantityReserved' => 'required|numeric|min:0',
            'editQuantityDamaged' => 'required|numeric|min:0',
            'editHealthStatus' => 'required|in:good,warning,critical,expired',
            'editExpiryDate' => 'nullable|date',
        ]);

        $stock = Stock::find($this->editStockId);
        if (!$stock) {
            $this->toast()->error('Stock record not found')->send();
            return;
        }

        $oldAvailable = $stock->quantity_available;
        $oldReserved = $stock->quantity_reserved;
        $oldDamaged = $stock->quantity_damaged;

        // Update stock
        $stock->update([
            'quantity_available' => $this->editQuantityAvailable,
            'quantity_reserved' => $this->editQuantityReserved,
            'quantity_damaged' => $this->editQuantityDamaged,
            'health_status' => $this->editHealthStatus,
            'expiry_date' => $this->editExpiryDate,
            'last_stock_take_date' => now(),
        ]);

        // Get employee ID if exists
        $movedBy = null;
        if (auth()->user() && auth()->user()->employee) {
            $movedBy = auth()->user()->employee->id;
        }

        // Create stock movement records for any changes
        if ($oldAvailable != $this->editQuantityAvailable) {
            $movementData = [
                'stock_id' => $stock->id,
                'type' => 'adjustment',
                'quantity' => abs($this->editQuantityAvailable - $oldAvailable),
                'quantity_before' => $oldAvailable,
                'quantity_after' => $this->editQuantityAvailable,
                'movement_date' => now(),
                'notes' => $this->editNotes ?? 'Manual stock adjustment',
            ];

            if ($movedBy) {
                $movementData['moved_by'] = $movedBy;
            }

            StockMovement::create($movementData);
        }

        if ($oldReserved != $this->editQuantityReserved) {
            $movementData = [
                'stock_id' => $stock->id,
                'type' => 'adjustment',
                'quantity' => abs($this->editQuantityReserved - $oldReserved),
                'quantity_before' => $oldReserved,
                'quantity_after' => $this->editQuantityReserved,
                'movement_date' => now(),
                'notes' => $this->editNotes ?? 'Manual reserved quantity adjustment',
            ];

            if ($movedBy) {
                $movementData['moved_by'] = $movedBy;
            }

            StockMovement::create($movementData);
        }

        if ($oldDamaged != $this->editQuantityDamaged) {
            $movementData = [
                'stock_id' => $stock->id,
                'type' => 'damaged',
                'quantity' => abs($this->editQuantityDamaged - $oldDamaged),
                'quantity_before' => $oldDamaged,
                'quantity_after' => $this->editQuantityDamaged,
                'movement_date' => now(),
                'notes' => $this->editNotes ?? 'Manual damaged quantity adjustment',
            ];

            if ($movedBy) {
                $movementData['moved_by'] = $movedBy;
            }

            StockMovement::create($movementData);
        }

        $this->toast()->success('Stock updated successfully')->send();
        $this->closeEditStockModal();
    }

    public function closeEditStockModal()
    {
        $this->showEditStockModal = false;
        $this->editStockId = null;
        $this->editQuantityAvailable = null;
        $this->editQuantityReserved = null;
        $this->editQuantityDamaged = null;
        $this->editHealthStatus = null;
        $this->editExpiryDate = null;
        $this->editNotes = null;
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.stocks', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'item', 'label' => 'Item'],
                ['index' => 'sku', 'label' => 'SKU'],
                ['index' => 'branch', 'label' => 'Branch'],
                ['index' => 'category', 'label' => 'Category'],
                ['index' => 'available', 'label' => 'Available'],
                ['index' => 'reserved', 'label' => 'Reserved'],
                ['index' => 'damaged', 'label' => 'Damaged'],
                ['index' => 'total', 'label' => 'Total'],
                ['index' => 'reorder_level', 'label' => 'Reorder Level'],
                ['index' => 'health_status', 'label' => 'Health'],
                ['index' => 'last_stock_take', 'label' => 'Last Stock Take'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'branches' => $branches,
        ]);
    }
}
