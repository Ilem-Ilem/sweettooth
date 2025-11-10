<?php

namespace App\Livewire\BranchDashboard\Production\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductionCallback;
use App\Models\Shift;
use App\Models\Item;
use App\Models\Product;
use App\Models\Stock;
use App\Models\DailyProduce;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class CreateInventoryCallback extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    public ?int $quantity = 20;
    public ?string $search = null;

    public ?int $currentShiftId = null;
    public ?int $selectedShiftId = null;
    public $availableShifts = [];
    public $shiftDate;

    // Callback form
    public $showCallbackModal = false;
    public $callbackType = 'raw_material'; // raw_material or finished_product
    public $selectedItemId = null;
    public $selectedProductId = null;
    public $callbackQuantity = 0;
    public $callbackUom = '';
    public $callbackReason = '';
    public $callbackNotes = '';

    // Reason options
    public array $rawMaterialReasonOptions = [
        'damaged' => 'Damaged',
        'expired' => 'Expired',
        'quality_issue' => 'Quality Issue',
        'wrong_batch' => 'Wrong Batch',
        'contamination' => 'Contamination',
        'other' => 'Other',
    ];

    public array $finishedProductReasonOptions = [
        'damaged' => 'Damaged',
        'quality_issue' => 'Quality Issue',
        'contamination' => 'Contamination',
        'other' => 'Other',
    ];

    // Table headers for raw materials
    public array $rawMaterialHeaders = [
        ['index' => 'item', 'label' => 'Raw Material'],
        ['index' => 'sku', 'label' => 'SKU'],
        ['index' => 'dispatched_qty', 'label' => 'Dispatched Qty'],
        ['index' => 'uom', 'label' => 'UOM'],
        ['index' => 'dispatch_time', 'label' => 'Dispatch Time'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    // Table headers for finished products
    public array $finishedProductHeaders = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'sku', 'label' => 'SKU'],
        ['index' => 'produced_qty', 'label' => 'Produced Qty'],
        ['index' => 'uom', 'label' => 'UOM'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    protected function getModelClass(): string
    {
        return ProductionCallback::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

        if (!$shiftId) {
            return ProductionCallback::query()->whereRaw('1=0');
        }

        return ProductionCallback::query()
            ->where('shift_id', $shiftId);
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->shiftDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadAvailableShifts();
        $this->loadCurrentShift();

        // Set selected shift to current shift if available
        if ($this->currentShiftId) {
            $this->selectedShiftId = $this->currentShiftId;
        } elseif (!empty($this->availableShifts)) {
            // If no active shift, select the most recent one
            $this->selectedShiftId = $this->availableShifts[0]->id;
        }
    }

    protected function loadAvailableShifts()
    {
        $branchId = $this->getBranchId();
        $employee = auth('employees')->user();

        // Get production shifts from last 30 days
        $this->availableShifts = Shift::where('branch_id', $branchId)
            ->where('shift_date', '>=', now()->subDays(30))
            ->whereHas('department.category', function ($q) {
                $q->where('name', 'Production');
            })
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type', 'desc')
            ->get();
    }

    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        // Find active production shift for this employee
        $activeShift = Shift::where('branch_id', $this->getBranchId())
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->where('employee_id', $employee->id)
            ->whereHas('department.category', function ($q) {
                $q->where('name', 'Production');
            })
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
        }
    }

    public function updatedSelectedShiftId($shiftId = null)
    {
        // Refresh data when shift selection changes
        $this->resetPage();
    }

    /**
     * Get available raw materials dispatched to this production shift
     */
    public function getRawMaterialsProperty()
    {
        $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

        if (!$shiftId) {
            return collect([]);
        }

        // Get ItemRequests for this production shift through ProductionRequests
        $itemRequestIds = \App\Models\ProductionRequest::where('shift_id', $shiftId)
            ->pluck('item_request_id')
            ->unique();

        // Get ItemDispatches for these requests
        $query = \App\Models\ItemDispatch::with(['item', 'itemRequest'])
            ->whereIn('request_id', $itemRequestIds)
            ->whereNotNull('received_time'); // Only show received dispatches

        // Search filter
        if ($this->search) {
            $query->whereHas('item', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('dispatch_time', 'desc')->paginate($this->quantity);
    }

    /**
     * Get finished products from today's production
     */
    public function getFinishedProductsProperty()
    {
        $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

        if (!$shiftId) {
            return collect([]);
        }

        $query = DailyProduce::with(['recipe.product'])
            ->where('shift_id', $shiftId)
            ->where('produced_quantity', '>', 0);

        // Search filter
        if ($this->search) {
            $query->whereHas('recipe.product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('updated_at', 'desc')->paginate($this->quantity);
    }

    public function openRawMaterialCallbackModal($dispatchId)
    {
        $dispatch = \App\Models\ItemDispatch::with('item')->find($dispatchId);

        if (!$dispatch) {
            $this->toast()->error('Dispatch not found.')->send();
            return;
        }

        $this->callbackType = 'raw_material';
        $this->selectedItemId = $dispatch->item_id;
        $this->selectedProductId = null;
        $this->callbackQuantity = 0;
        $this->callbackUom = $dispatch->uom ?? $dispatch->item->unit ?? '';
        $this->callbackReason = '';
        $this->callbackNotes = '';
        $this->showCallbackModal = true;
    }

    public function openFinishedProductCallbackModal($dailyProduceId)
    {
        $dailyProduce = DailyProduce::with(['recipe.product'])->find($dailyProduceId);

        if (!$dailyProduce) {
            $this->toast()->error('Daily produce record not found.')->send();
            return;
        }

        $this->callbackType = 'finished_product';
        $this->selectedItemId = null;
        $this->selectedProductId = $dailyProduce->recipe->product_id;
        $this->callbackQuantity = 0;
        $this->callbackUom = $dailyProduce->recipe->product->unit ?? '';
        $this->callbackReason = '';
        $this->callbackNotes = '';
        $this->showCallbackModal = true;
    }

    public function closeCallbackModal()
    {
        $this->showCallbackModal = false;
        $this->callbackType = 'raw_material';
        $this->selectedItemId = null;
        $this->selectedProductId = null;
        $this->callbackQuantity = 0;
        $this->callbackUom = '';
        $this->callbackReason = '';
        $this->callbackNotes = '';
    }

    public function submitCallback()
    {
        // Validate based on callback type
        $reasonOptions = $this->callbackType === 'raw_material'
            ? array_keys($this->rawMaterialReasonOptions)
            : array_keys($this->finishedProductReasonOptions);

        $this->validate([
            'callbackQuantity' => 'required|numeric|min:0.01',
            'callbackReason' => 'required|in:' . implode(',', $reasonOptions),
            'callbackUom' => 'required|string',
        ], [
            'callbackQuantity.required' => 'Callback quantity is required',
            'callbackQuantity.min' => 'Callback quantity must be greater than 0',
            'callbackReason.required' => 'Please select a callback reason',
            'callbackUom.required' => 'Unit of measure is required',
        ]);

        try {
            DB::beginTransaction();

            // Validate quantity based on type
            if ($this->callbackType === 'raw_material') {
                // Note: We're not validating against dispatched quantity because
                // production may have already used some of the materials
                // The callback is about returning damaged/unusable items
            } else {
                // For finished products, check daily produce
                $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

                $dailyProduce = DailyProduce::where('shift_id', $shiftId)
                    ->whereHas('recipe', function ($q) {
                        $q->where('product_id', $this->selectedProductId);
                    })
                    ->first();

                if (!$dailyProduce) {
                    throw new \Exception('Daily produce record not found');
                }

                if ($this->callbackQuantity > $dailyProduce->produced_quantity) {
                    $this->toast()->error("Callback quantity cannot exceed produced quantity ({$dailyProduce->produced_quantity}).")->send();
                    return;
                }
            }

            $employee = auth('employees')->user();

            // Create callback record
            $sourceType = $this->callbackType === 'raw_material'
                ? 'raw_material_from_stock'
                : 'finished_product_reject';

            $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

            ProductionCallback::create([
                'shift_id' => $shiftId,
                'source_type' => $sourceType,
                'item_id' => $this->selectedItemId,
                'product_id' => $this->selectedProductId,
                'recorded_by' => $employee->id,
                'quantity' => $this->callbackQuantity,
                'uom' => $this->callbackUom,
                'reason' => $this->callbackReason,
                'status' => 'pending',
                'notes' => $this->callbackNotes,
                'callback_time' => now(),
            ]);

            DB::commit();

            $callbackTypeName = $this->callbackType === 'raw_material' ? 'raw material' : 'finished product';
            $this->toast()->success("Production callback for {$callbackTypeName} created successfully! Awaiting inventory approval.")->send();
            $this->closeCallbackModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error creating callback: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        $shiftId = $this->selectedShiftId ?? $this->currentShiftId;

        return view('livewire.branch-dashboard.production.callbacks.create-inventory-callback', [
            'currentShift' => $this->currentShiftId ? Shift::find($this->currentShiftId) : null,
            'selectedShift' => $shiftId ? Shift::find($shiftId) : null,
            'rawMaterials' => $this->rawMaterials,
            'finishedProducts' => $this->finishedProducts,
        ]);
    }
}
