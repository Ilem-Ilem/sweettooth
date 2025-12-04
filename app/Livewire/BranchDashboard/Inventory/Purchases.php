<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Item;
use App\Models\Stock;
use App\Models\Branch;
use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Livewire\WithPagination;
use App\Models\StockMovement;
use App\Services\AuditService;
use App\Services\InventoryApprovalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Url, On};

#[Layout('components.layouts.app.branch-dashboard')]
class Purchases extends Component
{
    use WithPagination;
    #[Url(keep: true)]
    public ?string $b_id = null;
    public $purchaseId;
    public $purchase_date;
    public $supplier_name;
    public $supplier_contact;
    public $currency = 'NGN';
    public $exchange_rate = 1;
    public $other_costs = 0;
    public $payment_status = 'pending';
    public $notes;

    public $quantity = [];

    public $purchaseItems = [];
    public $itemIndex = 0;

    public $search = '';
    public $filterPaymentStatus = '';

    public $showModal = false;
    public $isEditing = false;

    // Audit Modal
    public $showAuditModal = false;
    public $auditReason = '';
    public $auditAction = null;
    public $pendingPurchaseData = [];
    public $pendingItemId = null;

    protected $rules = [
        'purchase_date' => 'required|date',
        'supplier_name' => 'required|string|max:255',
        'supplier_contact' => 'nullable|string|max:255',
        'currency' => 'required|in:NGN,USD,EUR,GBP',
        'exchange_rate' => 'required|numeric|min:0.01',
        'other_costs' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,partial,pending',
        'notes' => 'nullable|string',
        'purchaseItems' => 'required|array|min:1',
        'purchaseItems.*.item_id' => 'required|exists:items,id',
        'purchaseItems.*.quantity' => 'required|numeric|min:0.01',
        'purchaseItems.*.uom' => 'required|string',
        'purchaseItems.*.unit_fob_fc' => 'required|numeric|min:0',
        'auditReason' => 'required_if:showAuditModal,true|string|min:10|max:500',
    ];

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->purchase_date = now()->format('Y-m-d');
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
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
            ->when($this->filterPaymentStatus, fn ($q) => $q->where('payment_status', $this->filterPaymentStatus))
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

        // For super admins, create directly
        if (is_super_admin()) {
            $this->executeImmediatePurchaseCreation();
            return;
        }

        // For regular employees, show audit modal first
        $branchId = $this->getBranchId();
        $branch = Auth::guard('employees')->user()->branch;

        $totalFobFc = 0;
        $totalFobNgn = 0;

        foreach ($this->purchaseItems as $item) {
            $totalFobFc += $item['quantity'] * $item['unit_fob_fc'];
            $totalFobNgn += $item['quantity'] * ($item['unit_fob_fc'] * $this->exchange_rate);
        }

        $landingCost = $totalFobNgn + $this->other_costs;

        // Store purchase data for approval
        $this->pendingPurchaseData = [
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
            'items' => $this->purchaseItems,
        ];

        $this->auditReason = '';
        $this->closeModal();
        $this->showAuditModal = true;
    }

    private function executeImmediatePurchaseCreation()
    {
        DB::beginTransaction();
        try {
            $actor = current_actor();

            if (!$actor) {
                abort(403, "No Authenticated User Found");
            }

            $branchId = $this->getBranchId();
            $branch = is_super_admin() ? Branch::where('id', $branchId)->First() :
                Auth::guard('employees')->user()->branch;

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
                'recorded_by_id' => $actor->id,
                'recorded_by_type' => get_class($actor),
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
                    'fob_fc' => $unitFobFc,
                    'fob_ngn' => $unitFobNgn,
                    'other_costs' => $allocatedOtherCosts,
                    'total_cost' => $totalCost,
                    'cost_per_unit' => $costPerUnit,
                ]);

                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $branchId,
                        'item_id' => $item['item_id'],
                    ],
                    [
                        'quantity_available' => 0,
                        'quantity_reserved' => 0,
                        'average_cost' => 0,
                    ]
                );

                $stock->updateAverageCost($quantity, $costPerUnit);
                $quantity_before = $stock->quantity_available;
                $stock->quantity_available += $quantity;
                $stock->last_stock_take_date = now();
                $stock->save();

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'in',
                    'quantity_before'=>$quantity_before,
                    'quantity_after'=>$stock->quantity_available,
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Purchase',
                    'reference_id' => $purchase->id,
                    'moved_by_type'   => get_class($actor),
                    'moved_by_id'     => $actor->id,
                    'movement_date' => $this->purchase_date,
                    'notes' => 'Purchase: ' . $purchaseNumber,
                ]);
            }

            // Log the purchase creation
            AuditService::log(
                $actor,
                'create',
                $purchase,
                "Created purchase #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
                "Total FOB FC: {$purchase->total_fob_fc}, Total FOB NGN: {$purchase->total_fob_ngn}, " .
                "Landing Cost: {$purchase->landing_cost}, Payment Status: {$purchase->payment_status}. " .
                "Items: " . count($this->purchaseItems),
                'completed'
            );

            DB::commit();
            session()->flash('success', 'Purchase created successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
            session()->flash('error', 'Error creating purchase: ' . $e->getMessage());
        }
    }

    public function submitPurchaseApprovalRequest()
    {
        $this->validate([
            'auditReason' => 'required|string|min:10|max:500',
        ], [
            'auditReason.required' => 'Please provide a reason for the purchase.',
            'auditReason.min' => 'Reason must be at least 10 characters.',
        ]);

        try {
            $actor = Auth::guard('employees')->user();

            $request = InventoryApprovalService::requestPurchaseCreation(
                $actor,
                $this->pendingPurchaseData,
                $this->auditReason
            );

            // Verify request was created
            if (!$request || !$request->id) {
                throw new \Exception('Failed to create approval request');
            }

            $this->toast()->success('Purchase approval request submitted! (Request ID: ' . $request->id . ')')->send();
            $this->closeAuditModal();
            $this->resetFields();
        } catch (\Exception $e) {
            $this->toast()->error('Failed: ' . $e->getMessage())->send();
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

        // For super admins, delete directly
        if (is_super_admin()) {
            $purchaseNumber = $purchase->purchase_number;
            $supplierName = $purchase->supplier_name;
            $itemCount = $purchase->purchaseItems()->count();
            $landingCost = $purchase->landing_cost;

            $purchase->delete();

            // Log the purchase deletion
            AuditService::log(
                current_actor(),
                'delete',
                $purchase,
                "Deleted purchase #{$purchaseNumber} from {$supplierName}. Items: {$itemCount}, Landing Cost: {$landingCost}",
                'completed'
            );

            session()->flash('success', 'Purchase deleted successfully.');
            return;
        }

        // For regular employees, request approval
        $this->pendingItemId = $id;
        $this->auditReason = '';
        $this->showAuditModal = true;
    }

    public function submitPurchaseDeletionRequest()
    {
        $this->validate([
            'auditReason' => 'required|string|min:10|max:500',
        ], [
            'auditReason.required' => 'Please provide a reason for deletion.',
            'auditReason.min' => 'Reason must be at least 10 characters.',
        ]);

        try {
            $actor = Auth::guard('employees')->user();

            $request = InventoryApprovalService::requestPurchaseDeletion(
                $actor,
                $this->pendingItemId,
                $this->auditReason
            );

            // Verify request was created
            if (!$request || !$request->id) {
                throw new \Exception('Failed to create approval request');
            }

            $this->toast()->success('Purchase deletion request submitted! (Request ID: ' . $request->id . ')')->send();
            $this->closeAuditModal();
        } catch (\Exception $e) {
            $this->toast()->error('Failed: ' . $e->getMessage())->send();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function closeAuditModal()
    {
        $this->showAuditModal = false;
        $this->auditReason = '';
        $this->pendingPurchaseData = [];
        $this->pendingItemId = null;
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
