<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Purchases extends Component
{
    use WithPagination;

    public $purchaseId;
    public $branch_id = '';
    public $purchase_date;
    public $supplier_name;
    public $supplier_contact;
    public $currency = 'NGN';
    public $exchange_rate = 1;
    public $other_costs = 0;
    public $payment_status = 'pending';
    public $notes;

    public $purchaseItems = [];
    public $itemIndex = 0;

    public $search = '';
    public $filterBranch = '';
    public $filterPaymentStatus = '';
    public $sortColumn = 'purchase_date';
    public $sortDirection = 'desc';
    public $viewMode = 'table'; // table, cards, list, timeline, stats

    public $showModal = false;
    public $isEditing = false;

    protected $rules = [
        'branch_id' => 'required|exists:branches,id',
        'purchase_date' => 'required|date',
        'supplier_name' => 'required|string|max:255',
        'supplier_contact' => 'nullable|string|max:255',
        'currency' => 'required|in:NGN,USD,EUR,GBP',
        'exchange_rate' => 'required|numeric|min:0.01',
        'other_costs' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,partial,pending',
        'notes' => 'nullable|string',
        'purchaseItems.*.item_id' => 'required|exists:items,id',
        'purchaseItems.*.quantity' => 'required|numeric|min:0.01',
        'purchaseItems.*.uom' => 'required|string',
        'purchaseItems.*.unit_price' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->purchase_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Purchase::with(['branch', 'recorder', 'purchaseItems.item'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
            ->orderBy($this->sortColumn, $this->sortDirection);

        $purchases = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('livewire.super-admin.inventory.purchases', [
            'purchases' => $purchases,
            'branches' => $branches,
            'items' => $items,
            'summary' => $this->getPurchaseSummary(),
            'purchasesByStatus' => $this->getPurchasesByStatus(),
            'purchasesByBranch' => $this->getPurchasesByBranch(),
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create-purchases');
        $this->resetFields();
        $this->addPurchaseItem();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function addPurchaseItem()
    {
        $this->purchaseItems[] = [
            'id' => $this->itemIndex++,
            'item_id' => '',
            'quantity' => 0,
            'uom' => '',
            'unit_price' => 0,
        ];
    }

    public function removePurchaseItem($index)
    {
        unset($this->purchaseItems[$index]);
        $this->purchaseItems = array_values($this->purchaseItems);
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('edit-purchases');
        } else {
            $this->authorize('create-purchases');
        }

        $this->validate();

        DB::beginTransaction();
        try {
            $branch = Branch::findOrFail($this->branch_id);
            $purchaseNumber = Purchase::generatePurchaseNumber($branch->code);

            $totalItemsCost = 0;

            foreach ($this->purchaseItems as $item) {
                // unit_price is in the selected currency
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalItemsCost += $itemTotal;
            }

            // Convert to NGN if not NGN
            $totalFobNgn = $this->currency === 'NGN' ? $totalItemsCost : ($totalItemsCost * $this->exchange_rate);
            $landingCost = $totalFobNgn + $this->other_costs;
            $totalCost = $landingCost; // Set total_cost equal to landing_cost

            $purchase = Purchase::create([
                'branch_id' => $this->branch_id,
                'recorded_by' => Auth::id(),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $this->purchase_date,
                'supplier_name' => $this->supplier_name,
                'supplier_contact' => $this->supplier_contact,
                'total_fob_fc' => $totalFobFc,
                'total_fob_ngn' => $totalFobNgn,
                'other_costs' => $this->other_costs,
                'landing_cost' => $landingCost,
                'total_cost' => $totalCost,
                'currency' => $this->currency,
                'exchange_rate' => $this->exchange_rate,
                'payment_status' => $this->payment_status,
                'notes' => $this->notes,
            ]);

            foreach ($this->purchaseItems as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                
                // Convert unit price to NGN if needed
                $unitPriceNgn = $this->currency === 'NGN' ? $unitPrice : ($unitPrice * $this->exchange_rate);
                $totalItemCost = $quantity * $unitPriceNgn;

                // Allocate other costs proportionally
                $costProportion = $totalFobNgn > 0 ? ($totalItemCost / $totalFobNgn) : 0;
                $allocatedOtherCosts = $this->other_costs * $costProportion;
                $landingCostItem = $totalItemCost + $allocatedOtherCosts;
                $costPerUnit = $quantity > 0 ? ($landingCostItem / $quantity) : 0;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $quantity,
                    'uom' => $item['uom'],
                    'fob_fc' => 0,
                    'fob_ngn' => $unitPriceNgn,
                    'other_costs' => $allocatedOtherCosts,
                    'landing_cost' => $landingCostItem,
                    'total_cost' => $landingCostItem,
                    'cost_per_unit' => $costPerUnit,
                ]);

                // Update stock
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $this->branch_id,
                        'item_id' => $item['item_id'],
                    ],
                    [
                        'available_quantity' => 0,
                        'reserved_quantity' => 0,
                        'total_quantity' => 0,
                        'last_purchase_price' => 0,
                        'average_cost' => 0,
                    ]
                );

                $stock->updateAverageCost($quantity, $costPerUnit);
                $stock->available_quantity += $quantity;
                $stock->total_quantity += $quantity;
                $stock->last_purchase_price = $costPerUnit;
                $stock->last_stock_date = now();
                $stock->save();

                // Record stock movement
                $quantity_before = $stock->quantity_available - $quantity;
                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'in',
                    'quantity_before' => $quantity_before,
                    'quantity_after' => $stock->quantity_available,
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Purchase',
                    'reference_id' => $purchase->id,
                    'moved_by_type' => get_class(Auth::user()),
                    'moved_by_id' => Auth::id(),
                    'movement_date' => $this->purchase_date,
                    'notes' => 'Purchase: ' . $purchaseNumber,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Purchase created successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating purchase: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->authorize('delete-purchases');

        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        session()->flash('success', 'Purchase deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->purchaseId = null;
        $this->branch_id = '';
        $this->purchase_date = now()->format('Y-m-d');
        $this->supplier_name = '';
        $this->supplier_contact = '';
        $this->currency = 'NGN';
        $this->exchange_rate = 1;
        $this->other_costs = 0;
        $this->payment_status = 'pending';
        $this->notes = '';
        $this->purchaseItems = [];
        $this->itemIndex = 0;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterPaymentStatus = '';
    }

    public function updatedCurrency($value)
    {
        if ($value === 'NGN') {
            $this->exchange_rate = 1;
        }
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function getPurchaseSummary()
    {
        $query = Purchase::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus));

        $purchases = $query->get();

        return [
            'total_purchases' => $purchases->count(),
            'total_cost' => $purchases->sum('landing_cost'),
            'paid_count' => $purchases->where('payment_status', 'paid')->count(),
            'pending_count' => $purchases->where('payment_status', 'pending')->count(),
            'partial_count' => $purchases->where('payment_status', 'partial')->count(),
        ];
    }

    public function getPurchasesByStatus()
    {
        $query = Purchase::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus));

        return $query->get()->groupBy('payment_status')->map(function ($group) {
            return [
                'status' => $group->first()->payment_status,
                'count' => $group->count(),
                'total_cost' => $group->sum('landing_cost'),
                'purchases' => $group,
            ];
        })->sortByDesc('total_cost');
    }

    public function getPurchasesByBranch()
    {
        $query = Purchase::with('branch')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus));

        return $query->get()->groupBy('branch.name')->map(function ($group) {
            return [
                'branch' => $group->first()->branch->name,
                'count' => $group->count(),
                'total_cost' => $group->sum('landing_cost'),
                'avg_cost' => $group->avg('landing_cost'),
                'purchases' => $group,
            ];
        })->sortByDesc('total_cost');
    }
}
