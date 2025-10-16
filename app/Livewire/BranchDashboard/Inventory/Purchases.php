<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.branch-dashboard')]
class Purchases extends Component
{
    use WithPagination;
#[Url(keep:true)]
    public $b_id;
    public $purchaseId;
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
    public $filterPaymentStatus = '';

    public $showModal = false;
    public $isEditing = false;

    protected $rules = [
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
        'purchaseItems.*.unit_fob_fc' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->purchase_date = now()->format('Y-m-d');
    }

        public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = Purchase::with(['branch', 'recorder', 'purchaseItems.item'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
            ->orderBy('purchase_date', 'desc');

        $purchases = $query->paginate(15);
        $items = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('livewire.branch-dashboard.inventory.purchases', [
            'purchases' => $purchases,
            'items' => $items,
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-purchases'); // TODO: Enable permissions after testing
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
            'quantity' => '',
            'uom' => '',
            'unit_fob_fc' => '',
        ];
    }

    public function removePurchaseItem($index)
    {
        unset($this->purchaseItems[$index]);
        $this->purchaseItems = array_values($this->purchaseItems);
    }

    public function save()
    {
        // TODO: Enable permissions after testing
        // if ($this->isEditing) {
        //     $this->authorize('edit-purchases');
        // } else {
        //     $this->authorize('create-purchases');
        // }

        $this->validate();

        DB::beginTransaction();
        try {
            $branchId = $this->getBranchId();
            $branch = Auth::guard('employees')->user()->employee->branch;
            $purchaseNumber = Purchase::generatePurchaseNumber($branch->code);

            $totalFobFc = 0;
            $totalFobNgn = 0;

            foreach ($this->purchaseItems as $item) {
                $totalFobFc += $item['quantity'] * $item['unit_fob_fc'];
                $totalFobNgn += $item['quantity'] * ($item['unit_fob_fc'] * $this->exchange_rate);
            }

            $landingCost = $totalFobNgn + $this->other_costs;

            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'recorded_by' => Auth::guard('employees')->id(),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $this->purchase_date,
                'supplier_name' => $this->supplier_name,
                'supplier_contact' => $this->supplier_contact,
                'total_fob_fc' => $totalFobFc,
                'total_fob_ngn' => $totalFobNgn,
                'other_costs' => $this->other_costs,
                'landing_cost' => $landingCost,
                'currency' => $this->currency,
                'exchange_rate' => $this->exchange_rate,
                'payment_status' => $this->payment_status,
                'notes' => $this->notes,
            ]);

            foreach ($this->purchaseItems as $item) {
                $quantity = $item['quantity'];
                $unitFobFc = $item['unit_fob_fc'];
                $unitFobNgn = $unitFobFc * $this->exchange_rate;
                $totalFobItemFc = $quantity * $unitFobFc;
                $totalFobItemNgn = $quantity * $unitFobNgn;

                $costProportion = $totalFobNgn > 0 ? ($totalFobItemNgn / $totalFobNgn) : 0;
                $allocatedOtherCosts = $this->other_costs * $costProportion;
                $totalCost = $totalFobItemNgn + $allocatedOtherCosts;
                $costPerUnit = $quantity > 0 ? ($totalCost / $quantity) : 0;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $quantity,
                    'uom' => $item['uom'],
                    'unit_fob_fc' => $unitFobFc,
                    'unit_fob_ngn' => $unitFobNgn,
                    'total_fob_fc' => $totalFobItemFc,
                    'total_fob_ngn' => $totalFobItemNgn,
                    'allocated_other_costs' => $allocatedOtherCosts,
                    'total_cost' => $totalCost,
                    'cost_per_unit' => $costPerUnit,
                ]);

                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $branchId,
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

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'item_id' => $item['item_id'],
                    'branch_id' => $branchId,
                    'movement_type' => 'in',
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Purchase',
                    'reference_id' => $purchase->id,
                    'recorded_by' => Auth::guard('employees')->id(),
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
        // $this->authorize('delete-purchases'); // TODO: Enable permissions after testing

        $purchase = Purchase::findOrFail($id);

        if ($purchase->branch_id !== $this->getBranchId()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

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
        $this->filterPaymentStatus = '';
    }

    public function updatedCurrency($value)
    {
        if ($value === 'NGN') {
            $this->exchange_rate = 1;
        }
    }
}
