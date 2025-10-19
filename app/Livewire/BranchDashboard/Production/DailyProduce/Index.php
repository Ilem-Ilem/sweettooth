<?php

namespace App\Livewire\BranchDashboard\Production\DailyProduce;

use App\Models\DailyProduce;
use App\Models\Shift;
use App\Models\ProductionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public $b_id;

    public $selectedShiftId = null;
    public $dailyProduces = [];
    public $currentShift = null;
    public $showRecordModal = false;
    public $recordingProduceId = null;
    public $showHelpModal = false;

    // For editing quantities
    public $editingQuantities = [];
    public $damagedQuantities = [];

    // For recording production batches
    public $batchQuantityProduced = 0;
    public $batchQuantityApproved = 0;
    public $batchQuantityRejected = 0;
    public $batchQualityStatus = 'good';
    public $batchRejectionReason = '';
    public $batchNotes = '';
    public $recordingProduce = null;

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get today's shift for the employee's department
        // This should get the production department shift (where kitchen/production happens)
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
            $this->loadDailyProduces();
        } else {
            // If no shift found for today, try to get the most recent shift
            $this->currentShift = Shift::where('branch_id', $branchId)
                ->where('department_id', $employee->department_id)
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type', 'desc')
                ->first();

            if ($this->currentShift) {
                $this->selectedShiftId = $this->currentShift->id;
                $this->loadDailyProduces();
            }
        }
    }

    public function loadDailyProduces()
    {
        if (!$this->selectedShiftId) {
            return;
        }

        $shift = Shift::find($this->selectedShiftId);

        if (!$shift) {
            return;
        }

        // Auto-create DailyProduce records from ProductionRequests if they don't exist
        DailyProduce::autoCreateFromProductionRequests($shift);

        // Load all daily produces for this shift with all related data
        $produces = DailyProduce::with([
            'recipe',
            'shift',
            'productionRecords.producedBy'
        ])
        ->where('shift_id', $this->selectedShiftId)
        ->orderBy('recipe_id')
        ->get();

        $this->dailyProduces = $produces->map(function ($produce) use ($shift) {
            // Get the production request for this recipe and shift (department-wide, not user-specific)
            $productionRequest = ProductionRequest::where('shift_id', $shift->id)
                ->where('recipe_id', $produce->recipe_id)
                ->with(['itemRequest.requestDetails.item', 'itemRequest'])
                ->first();

            // Get item request status from ItemRequest model (not ProductionRequest)
            $itemRequestStatus = 'N/A';
            $itemRequestNumber = 'N/A';

            if ($productionRequest && $productionRequest->itemRequest) {
                $itemRequestNumber = $productionRequest->itemRequest->request_number;
                // Use the ItemRequest status field directly
                $itemRequestStatus = $productionRequest->itemRequest->status ?? 'pending';
            }

            // Get dispatched items for this request
            $dispatchedItems = [];
            if ($productionRequest && $productionRequest->itemRequest) {
                foreach ($productionRequest->itemRequest->requestDetails as $detail) {
                    $dispatchedItems[] = [
                        'item_name' => $detail->item->name ?? 'N/A',
                        'requested' => (float) $detail->quantity_requested,
                        'approved' => (float) $detail->quantity_approved,
                        'dispatched' => (float) $detail->quantity_dispatched,
                        'uom' => $detail->uom ?? $detail->item->uom ?? '',
                    ];
                }
            }

            // Auto-calculate produced quantity from production records
            $actualProducedQty = $produce->getTotalProducedFromRecords();

            // Update the model if produced quantity changed
            if ($produce->produced_quantity != $actualProducedQty) {
                $produce->produced_quantity = $actualProducedQty;
                $produce->updateCalculations();
            }

            return [
                'id' => $produce->id,
                'recipe_id' => $produce->recipe_id,
                'recipe_name' => $produce->recipe->product_name ?? 'N/A',
                'uom' => $produce->recipe->uom ?? '',
                'opening_quantity' => (float) $produce->opening_quantity,
                'requested_quantity' => (float) $produce->requested_quantity,
                'produced_quantity' => (float) $actualProducedQty,
                'net_available' => (float) $produce->getNetAvailable(),
                'sent_out_quantity' => (float) $produce->sent_out_quantity,
                'order_quantity' => (float) $produce->order_quantity,
                'callback_quantity' => (float) $produce->callback_quantity,
                'closing_quantity' => (float) $produce->closing_quantity,
                'expected_closing' => (float) $produce->expected_closing,
                'variance' => (float) $produce->variance,
                'has_variance_issue' => $produce->hasVarianceIssue(),
                'variance_percentage' => $produce->getVariancePercentage(),
                'production_records_count' => $produce->productionRecords->count(),

                // Computed status based on ItemRequest and production progress
                'computed_status' => $produce->getComputedProductionStatus(),
                'status_badge_color' => $produce->getStatusBadgeColor(),
                'can_start_production' => $produce->canStartProduction(),
                'manual_status' => $produce->status ?? 'in_progress',

                // Producable quantity calculation (CRITICAL BUSINESS LOGIC)
                'producability' => $produce->calculateProducableQuantity(),

                // Linked data from production request
                'item_request_status' => $itemRequestStatus,
                'item_request_number' => $itemRequestNumber,
                'dispatched_items' => $dispatchedItems,
                'production_request_id' => $productionRequest?->id,
            ];
        })->toArray();

        // Initialize editing quantities if not set (ONLY editable fields)
        if (empty($this->editingQuantities)) {
            foreach ($this->dailyProduces as $produce) {
                $this->editingQuantities[$produce['id']] = [
                    // produced_quantity is AUTO-CALCULATED from production records
                    'sent_out_quantity' => $produce['sent_out_quantity'],
                    'order_quantity' => $produce['order_quantity'],
                    'callback_quantity' => $produce['callback_quantity'],
                    'closing_quantity' => $produce['closing_quantity'],
                ];
            }
        }
    }

    public function updatedSelectedShiftId()
    {
        $this->editingQuantities = [];
        $this->loadDailyProduces();
    }

    public function updateQuantity($produceId, $field)
    {
        try {
            $produce = DailyProduce::find($produceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            // Update the field from editing quantities array
            $produce->$field = (float) ($this->editingQuantities[$produceId][$field] ?? 0);

            // Recalculate expected closing and variance
            $produce->updateCalculations();

            $this->toast()->success('Updated successfully.')->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error updating: ' . $e->getMessage())->send();
        }
    }

    public function saveAllQuantities()
    {
        try {
            DB::transaction(function () {
                foreach ($this->editingQuantities as $produceId => $quantities) {
                    $produce = DailyProduce::find($produceId);

                    if (!$produce) {
                        continue;
                    }

                    // Update ONLY editable quantities (produced is auto-calculated)
                    $produce->sent_out_quantity = (float) ($quantities['sent_out_quantity'] ?? 0);
                    $produce->order_quantity = (float) ($quantities['order_quantity'] ?? 0);
                    $produce->callback_quantity = (float) ($quantities['callback_quantity'] ?? 0);
                    $produce->closing_quantity = (float) ($quantities['closing_quantity'] ?? 0);

                    // Auto-update produced quantity from production records
                    $produce->produced_quantity = $produce->getTotalProducedFromRecords();

                    // Recalculate expected closing and variance
                    $produce->updateCalculations();
                }
            });

            $this->toast()->success('All quantities saved successfully.')->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error saving: ' . $e->getMessage())->send();
        }
    }

    public function openRecordModal($produceId)
    {
        $this->recordingProduceId = $produceId;

        // Load the produce record with recipe details
        $this->recordingProduce = DailyProduce::with('recipe')->find($produceId);

        // Reset form fields
        $this->batchQuantityProduced = 0;
        $this->batchQuantityApproved = 0;
        $this->batchQuantityRejected = 0;
        $this->batchQualityStatus = 'good';
        $this->batchRejectionReason = '';
        $this->batchNotes = '';

        $this->showRecordModal = true;
    }

    public function closeRecordModal()
    {
        $this->recordingProduceId = null;
        $this->recordingProduce = null;
        $this->showRecordModal = false;
        $this->loadDailyProduces(); // Reload to show new production records
    }

    public function updatedBatchQuantityProduced()
    {
        // Auto-set approved quantity to produced quantity by default
        if ($this->batchQuantityProduced > 0 && $this->batchQuantityApproved == 0 && $this->batchQuantityRejected == 0) {
            $this->batchQuantityApproved = $this->batchQuantityProduced;
        }
    }

    public function updatedBatchQuantityApproved()
    {
        // Auto-calculate rejected quantity
        if ($this->batchQuantityProduced > 0) {
            $this->batchQuantityRejected = max(0, $this->batchQuantityProduced - $this->batchQuantityApproved);
        }
    }

    public function updatedBatchQuantityRejected()
    {
        // Auto-calculate approved quantity
        if ($this->batchQuantityProduced > 0) {
            $this->batchQuantityApproved = max(0, $this->batchQuantityProduced - $this->batchQuantityRejected);
        }
    }

    public function recordBatch()
    {
        $this->validate([
            'batchQuantityProduced' => 'required|numeric|min:0.01',
            'batchQuantityApproved' => 'required|numeric|min:0',
            'batchQuantityRejected' => 'required|numeric|min:0',
            'batchQualityStatus' => 'required|in:excellent,good,acceptable,rejected',
            'batchRejectionReason' => 'required_if:batchQuantityRejected,>,0',
        ], [
            'batchQuantityProduced.required' => 'Quantity produced is required',
            'batchQuantityProduced.min' => 'Quantity produced must be greater than 0',
            'batchRejectionReason.required_if' => 'Please provide a reason for rejected items',
        ]);

        try {
            // Validate that approved + rejected = produced
            $total = (float) $this->batchQuantityApproved + (float) $this->batchQuantityRejected;
            $produced = (float) $this->batchQuantityProduced;

            if (abs($total - $produced) > 0.01) {
                $this->toast()->error('Approved + Rejected must equal Produced quantity')->send();
                return;
            }

            $produce = DailyProduce::find($this->recordingProduceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            // Create production record
            $productionRecord = new \App\Models\ProductionRecord();
            $productionRecord->daily_produce_id = $produce->id;
            $productionRecord->recipe_id = $produce->recipe_id;
            $productionRecord->produced_by = Auth::guard('employees')->id();
            $productionRecord->quantity_produced = $this->batchQuantityProduced;
            $productionRecord->quantity_approved = $this->batchQuantityApproved;
            $productionRecord->quantity_rejected = $this->batchQuantityRejected;
            $productionRecord->production_time = now();
            $productionRecord->quality_status = $this->batchQualityStatus;
            $productionRecord->rejection_reason = $this->batchQuantityRejected > 0 ? $this->batchRejectionReason : null;
            $productionRecord->notes = $this->batchNotes;
            $productionRecord->save();

            // Track raw material utilization for this batch
            $this->trackRawMaterialUtilization($produce, $this->batchQuantityApproved);

            // Auto-update produced quantity from all production records
            $totalProduced = $produce->getTotalProducedFromRecords();
            $produce->produced_quantity = $totalProduced;
            $produce->updateCalculations();

            $this->toast()->success('Production batch recorded successfully!')->send();
            $this->closeRecordModal();

        } catch (\Exception $e) {
            $this->toast()->error('Error recording batch: ' . $e->getMessage())->send();
        }
    }

    public function markComplete($produceId)
    {
        try {
            $produce = DailyProduce::find($produceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            $produce->status = 'completed';
            $produce->save();

            $this->toast()->success('Marked as completed.')->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error marking as complete: ' . $e->getMessage())->send();
        }
    }

    public function markInProgress($produceId)
    {
        try {
            $produce = DailyProduce::find($produceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            $produce->status = 'in_progress';
            $produce->save();

            $this->toast()->success('Marked as in progress.')->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error: ' . $e->getMessage())->send();
        }
    }

    public function toggleHelpModal()
    {
        $this->showHelpModal = !$this->showHelpModal;
    }

    private function trackRawMaterialUtilization($produce, $unitsProduced)
    {
        // Load recipe with ingredients
        $recipe = \App\Models\Recipe::with('ingredients.item')->find($produce->recipe_id);

        if (!$recipe || !$recipe->ingredients) {
            return;
        }

        // Get the production request to find dispatched quantities
        $productionRequest = \App\Models\ProductionRequest::where('shift_id', $produce->shift_id)
            ->where('recipe_id', $produce->recipe_id)
            ->with('itemRequest.requestDetails')
            ->first();

        if (!$productionRequest || !$productionRequest->itemRequest) {
            return;
        }

        foreach ($recipe->ingredients as $ingredient) {
            // Find the matching item request detail
            $requestDetail = $productionRequest->itemRequest->requestDetails
                ->firstWhere('item_id', $ingredient->item_id);

            if (!$requestDetail) {
                continue;
            }

            // Calculate expected usage based on recipe
            $quantityRequired = (float) $ingredient->quantity * $unitsProduced;

            // Actual usage is the dispatched quantity (what was actually used)
            $quantityUsed = (float) $requestDetail->quantity_dispatched;

            // Calculate variance
            $variance = $quantityUsed - $quantityRequired;

            // Determine variance type (5% tolerance)
            $tolerance = $quantityRequired * 0.05;
            $varianceType = 'within_tolerance';

            if ($variance > $tolerance) {
                $varianceType = 'over_used';
            } elseif ($variance < -$tolerance) {
                $varianceType = 'under_used';
            }

            // Calculate cost impact (variance * item cost)
            $itemCost = $ingredient->item->cost_per_unit ?? 0;
            $costImpact = $variance * $itemCost;

            // Create or update raw material utilization record
            \App\Models\RawMaterialUtilization::updateOrCreate([
                'shift_id' => $produce->shift_id,
                'recipe_id' => $produce->recipe_id,
                'item_id' => $ingredient->item_id,
            ], [
                'quantity_required' => $quantityRequired,
                'quantity_used' => $quantityUsed,
                'units_produced' => $unitsProduced,
                'variance' => $variance,
                'variance_type' => $varianceType,
                'cost_impact' => $costImpact,
                'notes' => "Auto-tracked from production batch",
            ]);
        }
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get available shifts for this department
        $availableShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type')
            ->limit(30) // Show more shifts
            ->get();

        // Debug info: count production requests for current shift
        $productionRequestsCount = 0;
        if ($this->selectedShiftId) {
            $productionRequestsCount = ProductionRequest::where('shift_id', $this->selectedShiftId)->count();
        }

        return view('livewire.branch-dashboard.production.daily-produce.index', [
            'availableShifts' => $availableShifts,
            'productionRequestsCount' => $productionRequestsCount,
        ]);
    }
}
