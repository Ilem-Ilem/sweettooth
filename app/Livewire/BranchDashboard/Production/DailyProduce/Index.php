<?php

namespace App\Livewire\BranchDashboard\Production\DailyProduce;

use App\Models\DailyProduce;
use App\Models\Shift;
use App\Models\ProductionRequest;
use App\Models\ProductDispatch;
use App\Models\Department;
use App\Services\ProductionAuditService;
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
    public ?string $b_id = null;

    #[Url(keep: true)]
    public ?string $dept_slug = null;

    public ?Department $department = null;
    public $salesDepartments = []; // Available sales departments for dispatch

    public $selectedShiftId = null;
    public $availableShifts = [];
    public $dailyProduces = [];
    public $currentShift = null;
    public $showRecordModal = false;
    public $recordingProduceId = null;
    public $showHelpModal = false;

    // For editing quantities
    public $editingQuantities = [];
    public $damagedQuantities = [];

    // For batch-level dispatch management
    public $batchQuantities = []; // Stores sent_out and order quantities for each batch
    public $batchSalesDepartments = []; // Stores sales_department_id for each batch

    // For recording production batches
    public $batchesProduced = 1; // Number of batches made
    public $batchQuantityProduced = 0; // Auto-calculated from batches × yield
    public $batchQuantityApproved = 0;
    public $batchQuantityRejected = 0;
    public $batchQualityStatus = 'good';
    public $batchRejectionReason = '';
    public $batchNotes = '';
    public $recordingProduce = null;

    public function mount($deptSlug)
    {
        $this->dept_slug = $deptSlug;
        $this->department = Department::where('slug', $deptSlug)->first();

        if (!$this->department) {
            abort(404, 'Department not found');
        }

        $this->loadAvailableShifts();
        $this->loadCurrentShift();
        $this->loadSalesDepartments();
    }

    /**
     * Load available shifts for past/future shift viewing
     */
    /**
     * Load sales departments (departments that can sell products)
     */
    public function loadSalesDepartments()
    {
        $branchId = $this->getBranchId();

        // Load departments that are sales-related (Till, Corner Store, Confectionaries Sales, etc.)
        // Exclude production departments (Kitchen, Gelato Production, etc.)
        $this->salesDepartments = Department::where(function ($query) use ($branchId) {
            $query->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
        })
        ->whereIn('slug', ['till', 'corner-store', 'confectionaries-sales']) // Add your sales dept slugs
        ->orderBy('name')
        ->get()
        ->toArray();
    }

    public function loadAvailableShifts()
    {
        $branchId = $this->getBranchId();

        // Get shifts from last 30 days for this department
        $this->availableShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $this->department->id)
            ->where('shift_date', '>=', now()->subDays(30))
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type', 'desc')
            ->get();
    }

    /**
     * When user selects a different shift to view/edit
     */
    public function updatedSelectedShiftId($shiftId = null)
    {
        // Clear editing state when switching shifts
        $this->editingQuantities = [];

        if ($shiftId) {
            $this->currentShift = Shift::find($shiftId);
        }

        $this->loadDailyProduces();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();

        // Get today's shift for this department
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('department_id', $this->department->id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
            $this->loadDailyProduces();
        } else {
            // If no shift found for today, try to get the most recent shift
            $this->currentShift = Shift::where('branch_id', $branchId)
                ->where('department_id', $this->department->id)
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
                        'uom' => $detail->uom ?? $detail->item->unitOfMeasure?->symbol ?? '',
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

            // Get production records (batches) with details
            $batchesData = $produce->productionRecords->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity_produced' => (float) $batch->quantity_produced,
                    'quantity_approved' => (float) $batch->quantity_approved,
                    'quantity_rejected' => (float) $batch->quantity_rejected,
                    'quantity_sent_out' => (float) $batch->quantity_sent_out,
                    'quantity_for_order' => (float) $batch->quantity_for_order,
                    'quantity_remaining' => (float) $batch->quantity_remaining,
                    'dispatch_status' => $batch->dispatch_status,
                    'quality_status' => $batch->quality_status,
                    'production_time' => $batch->production_time->format('M d, h:i A'),
                    'produced_by' => $batch->producedBy->name ?? 'N/A',
                ];
            })->toArray();

            return [
                'id' => $produce->id,
                'recipe_id' => $produce->recipe_id,
                'recipe_name' => $produce->recipe->product_name ?? 'N/A',
                'uom' => $produce->recipe->unitOfMeasure?->symbol ?? '',
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

                // Batch-level data
                'batches' => $batchesData,

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

                // Initialize batch quantities for batch-level management
                foreach ($produce['batches'] as $batch) {
                    $this->batchQuantities[$batch['id']] = [
                        'quantity_sent_out' => $batch['quantity_sent_out'],
                        'quantity_for_order' => $batch['quantity_for_order'],
                    ];

                    // Initialize sales department selection (default to first sales dept if available)
                    $this->batchSalesDepartments[$batch['id']] = $this->salesDepartments[0]['id'] ?? null;
                }
            }
        }
    }


    public function updateQuantity($produceId, $field)
    {
        try {
            $produce = DailyProduce::with(['recipe', 'shift'])->find($produceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            $newValue = (float) ($this->editingQuantities[$produceId][$field] ?? 0);
            $oldValue = (float) $produce->$field;

            // CRITICAL VALIDATION: Prevent sending out more than available
            if ($field === 'sent_out_quantity') {
                $netAvailable = $produce->getNetAvailable();
                $requestedQty = (float) $produce->requested_quantity;

                // Check 1: Cannot send out more than requested
                if ($newValue > $requestedQty) {
                    $this->toast()->error("Cannot send out {$newValue} - only {$requestedQty} was requested!")->send();
                    // Reset to old value
                    $this->editingQuantities[$produceId][$field] = $oldValue;
                    return;
                }

                // Check 2: Cannot send out more than net available (produced - callback)
                if ($newValue > $netAvailable) {
                    $this->toast()->error("Cannot send out {$newValue} - only {$netAvailable} available (after callbacks)!")->send();
                    // Reset to old value
                    $this->editingQuantities[$produceId][$field] = $oldValue;
                    return;
                }

                // If sent_out_quantity is being updated, create dispatch records
                if ($newValue > $oldValue) {
                    $quantityToDispatch = $newValue - $oldValue;
                    try {
                        $this->createProductDispatch($produce, $quantityToDispatch);
                    } catch (\Exception $e) {
                        // Log error but don't fail the update
                        logger()->error('Failed to create product dispatch: ' . $e->getMessage());
                    }
                }
            }

            // Update the field from editing quantities array
            $produce->$field = $newValue;

            // Auto-calculate closing quantity when any quantity field changes
            // This prevents variance issues
            $produce->closing_quantity = $produce->opening_quantity +
                                        $produce->getNetAvailable() -
                                        $produce->sent_out_quantity -
                                        $produce->order_quantity;

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
            // Get actor before transaction for audit logging
            $actor = current_actor();
            
            DB::transaction(function () use ($actor) {
                foreach ($this->editingQuantities as $produceId => $quantities) {
                    $produce = DailyProduce::find($produceId);

                    if (!$produce) {
                        continue;
                    }

                    // Store old values for audit trail
                    $oldSentOut = $produce->sent_out_quantity;
                    $oldOrder = $produce->order_quantity;
                    $oldCallback = $produce->callback_quantity;

                    // Update ONLY editable quantities (produced is auto-calculated)
                    $produce->sent_out_quantity = (float) ($quantities['sent_out_quantity'] ?? 0);
                    $produce->order_quantity = (float) ($quantities['order_quantity'] ?? 0);
                    $produce->callback_quantity = (float) ($quantities['callback_quantity'] ?? 0);

                    // Auto-update produced quantity from production records
                    $produce->produced_quantity = $produce->getTotalProducedFromRecords();

                    // Auto-update closing quantity to match expected closing (prevents variance issues)
                    // User can manually override if needed, but default to calculated value
                    $produce->closing_quantity = $produce->opening_quantity +
                                                $produce->getNetAvailable() -
                                                $produce->sent_out_quantity -
                                                $produce->order_quantity;

                    // Recalculate expected closing and variance
                    $produce->updateCalculations();

                    // Log significant quantity changes using actor pattern
                    if ($oldSentOut != $produce->sent_out_quantity || 
                        $oldOrder != $produce->order_quantity || 
                        $oldCallback != $produce->callback_quantity) {
                        ProductionAuditService::logVariance(
                            $actor,
                            $produce,
                            $oldSentOut + $oldOrder + $oldCallback,
                            $produce->sent_out_quantity + $produce->order_quantity + $produce->callback_quantity,
                            "Updated quantities - Sent out: {$oldSentOut}→{$produce->sent_out_quantity}, Order: {$oldOrder}→{$produce->order_quantity}, Callback: {$oldCallback}→{$produce->callback_quantity}"
                        );
                    }
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
        $this->batchesProduced = 1; // Default to 1 batch
        $this->batchQuantityProduced = 0;
        $this->batchQuantityApproved = 0;
        $this->batchQuantityRejected = 0;
        $this->batchQualityStatus = 'good';
        $this->batchRejectionReason = '';
        $this->batchNotes = '';

        // Calculate initial quantity based on 1 batch
        $this->calculateQuantityFromBatches();

        $this->showRecordModal = true;
    }

    public function closeRecordModal()
    {
        $this->recordingProduceId = null;
        $this->recordingProduce = null;
        $this->showRecordModal = false;
        $this->loadDailyProduces(); // Reload to show new production records
    }

    public function updatedBatchesProduced()
    {
        // Recalculate quantity when number of batches changes
        $this->calculateQuantityFromBatches();
    }

    public function calculateQuantityFromBatches()
    {
        if (!$this->recordingProduce || !$this->recordingProduce->recipe) {
            return;
        }

        $yieldPerBatch = (float) $this->recordingProduce->recipe->yield_quantity;
        $this->batchQuantityProduced = $this->batchesProduced * $yieldPerBatch;

        // Auto-set approved quantity to produced quantity by default
        if ($this->batchQuantityProduced > 0 && $this->batchQuantityApproved == 0 && $this->batchQuantityRejected == 0) {
            $this->batchQuantityApproved = $this->batchQuantityProduced;
        }
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
        // Custom validation for rejection reason
        if ($this->batchQuantityRejected > 0 && empty($this->batchRejectionReason)) {
            $this->toast()->error('Please provide a reason for rejected items')->send();
            return;
        }

        $this->validate([
            'batchesProduced' => 'required|numeric|min:1',
            'batchQuantityProduced' => 'required|numeric|min:0.01',
            'batchQuantityApproved' => 'required|numeric|min:0',
            'batchQuantityRejected' => 'required|numeric|min:0',
            'batchQualityStatus' => 'required|in:excellent,good,acceptable,rejected',
        ], [
            'batchesProduced.required' => 'Number of batches is required',
            'batchesProduced.min' => 'Number of batches must be at least 1',
            'batchQuantityProduced.required' => 'Quantity produced is required',
            'batchQuantityProduced.min' => 'Quantity produced must be greater than 0',
        ]);

        try {
            // Validate that approved + rejected = produced
            $total = (float) $this->batchQuantityApproved + (float) $this->batchQuantityRejected;
            $produced = (float) $this->batchQuantityProduced;

            if (abs($total - $produced) > 0.01) {
                $this->toast()->error("Approved ({$this->batchQuantityApproved}) + Rejected ({$this->batchQuantityRejected}) must equal Produced ({$this->batchQuantityProduced})")->send();
                return;
            }

            $produce = DailyProduce::find($this->recordingProduceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            // CRITICAL: Prevent recording batches if status is completed (unless reopened)
            if ($produce->status === 'completed') {
                $this->toast()->error('This production is marked as completed. Please reopen it first to record more batches.')->send();
                return;
            }

            // VALIDATION: Check if total production would exceed requested quantity
            $currentProduced = $produce->getTotalProducedFromRecords();
            $newTotal = $currentProduced + $this->batchQuantityApproved;
            $requested = (float) $produce->requested_quantity;

            if ($newTotal > $requested) {
                $this->toast()->error("Cannot record batch: Total produced would be {$newTotal}, but only {$requested} was requested. You cannot produce more than requested!")->send();
                return;
            }

            // Generate batch number (e.g., "Batch 1", "Batch 2")
            $batchCount = $produce->productionRecords()->count() + 1;
            $batchNumber = "Batch {$batchCount}";

            // Get current actor for polymorphic relationship
            $actor = current_actor();

            // Create production record
            $productionRecord = new \App\Models\ProductionRecord();
            $productionRecord->daily_produce_id = $produce->id;
            $productionRecord->recipe_id = $produce->recipe_id;
            $productionRecord->batch_number = $batchNumber;
            // Use polymorphic pattern for actor tracking
            $productionRecord->produced_by_id = $actor->id;
            $productionRecord->produced_by_type = get_class($actor);
            $productionRecord->quantity_produced = $this->batchQuantityProduced;
            $productionRecord->quantity_approved = $this->batchQuantityApproved;
            $productionRecord->quantity_rejected = $this->batchQuantityRejected;
            $productionRecord->quantity_sent_out = 0; // Initially nothing sent out
            $productionRecord->quantity_for_order = 0; // Initially nothing for orders
            $productionRecord->quantity_remaining = $this->batchQuantityApproved; // All approved quantity is available
            $productionRecord->production_time = now();
            $productionRecord->quality_status = $this->batchQualityStatus;
            $productionRecord->dispatch_status = 'available';
            $productionRecord->rejection_reason = $this->batchQuantityRejected > 0 ? $this->batchRejectionReason : null;
            $productionRecord->notes = $this->batchNotes;
            $productionRecord->save();

            // Log batch production to audit trail
            ProductionAuditService::logBatchProduced(
                $actor,
                $productionRecord,
                $this->batchNotes
            );

            // Track raw material utilization for this batch
            $this->trackRawMaterialUtilization($produce, $this->batchQuantityApproved);

            // Refresh the produce model to ensure productionRecords relationship is fresh
            $produce->refresh();
            $produce->load('productionRecords');

            // Auto-update produced quantity from all production records
            $totalProduced = $produce->getTotalProducedFromRecords();
            $produce->produced_quantity = $totalProduced;

            // Auto-update closing quantity to match expected closing (prevents -100% variance)
            // Closing = Opening + Produced - Sent Out - Orders - Callbacks
            $produce->closing_quantity = $produce->opening_quantity +
                                        $produce->getNetAvailable() -
                                        $produce->sent_out_quantity -
                                        $produce->order_quantity;

            $produce->updateCalculations();

            $this->toast()->success("Production batch recorded successfully! Total produced: {$totalProduced}")->send();
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

            $oldStatus = $produce->status;
            $actor = current_actor();
            
            $produce->status = 'completed';
            $produce->save();

            // Log status change to audit trail using actor pattern
            ProductionAuditService::logVariance(
                $actor,
                $produce,
                $produce->expected_closing,
                $produce->closing_quantity,
                "Production marked as completed. Status changed from {$oldStatus} to completed."
            );

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

            $oldStatus = $produce->status;
            $actor = current_actor();
            
            $produce->status = 'in_progress';
            $produce->save();

            // Log status change to audit trail using actor pattern
            ProductionAuditService::logVariance(
                $actor,
                $produce,
                $produce->expected_closing,
                $produce->closing_quantity,
                "Production reopened. Status changed from {$oldStatus} to in_progress."
            );

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

    /**
     * Update batch-level dispatch quantities
     */
    public function updateBatchQuantity($batchId, $field)
    {
        try {
            $batch = \App\Models\ProductionRecord::find($batchId);

            if (!$batch) {
                $this->toast()->error('Batch not found.')->send();
                return;
            }

            $newValue = (float) ($this->batchQuantities[$batchId][$field] ?? 0);

            // CRITICAL: Validate sales department is selected when sending out
            if ($field === 'quantity_sent_out' && $newValue > 0) {
                $salesDeptId = $this->batchSalesDepartments[$batchId] ?? null;
                if (!$salesDeptId) {
                    $this->toast()->error('Please select a sales department before dispatching!')->send();
                    // Reset to old value
                    $this->batchQuantities[$batchId][$field] = (float) $batch->$field;
                    return;
                }
            }

            // Validate: sent_out + for_order cannot exceed approved quantity
            $sentOut = $field === 'quantity_sent_out' ? $newValue : (float) $batch->quantity_sent_out;
            $forOrder = $field === 'quantity_for_order' ? $newValue : (float) $batch->quantity_for_order;

            $total = $sentOut + $forOrder;
            $approved = (float) $batch->quantity_approved;

            if ($total > $approved) {
                $this->toast()->error("Cannot allocate {$total} from batch - only {$approved} approved!")->send();
                // Reset to old value
                $this->batchQuantities[$batchId][$field] = (float) $batch->$field;
                return;
            }

            // Update the field
            $batch->$field = $newValue;
            $batch->updateQuantityRemaining();

            // Create dispatch record if sending out
            if ($field === 'quantity_sent_out' && $newValue > $batch->quantity_sent_out) {
                $quantityToDispatch = $newValue - $batch->quantity_sent_out;
                $salesDeptId = $this->batchSalesDepartments[$batchId] ?? null;

                $dailyProduce = \App\Models\DailyProduce::find($batch->daily_produce_id);
                if ($dailyProduce && $salesDeptId) {
                    $this->createProductDispatch($dailyProduce, $quantityToDispatch, $salesDeptId);
                }
            }

            $this->toast()->success("Batch updated: {$batch->quantity_remaining} remaining")->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error updating batch: ' . $e->getMessage())->send();
        }
    }

    /**
     * Save all batch quantities for a product
     */
    public function saveBatchQuantities($produceId)
    {
        try {
            $produce = DailyProduce::with('productionRecords')->find($produceId);

            if (!$produce) {
                $this->toast()->error('Daily produce record not found.')->send();
                return;
            }

            DB::transaction(function () use ($produce) {
                foreach ($produce->productionRecords as $batch) {
                    if (isset($this->batchQuantities[$batch->id])) {
                        $batch->quantity_sent_out = (float) ($this->batchQuantities[$batch->id]['quantity_sent_out'] ?? 0);
                        $batch->quantity_for_order = (float) ($this->batchQuantities[$batch->id]['quantity_for_order'] ?? 0);
                        $batch->updateQuantityRemaining();
                    }
                }

                // Update aggregate quantities in daily produce
                $totalSentOut = $produce->productionRecords()->sum('quantity_sent_out');
                $totalForOrder = $produce->productionRecords()->sum('quantity_for_order');

                $produce->sent_out_quantity = $totalSentOut;
                $produce->order_quantity = $totalForOrder;

                // Auto-calculate closing quantity
                $produce->closing_quantity = $produce->opening_quantity +
                                            $produce->getNetAvailable() -
                                            $produce->sent_out_quantity -
                                            $produce->order_quantity;

                $produce->updateCalculations();
            });

            $this->toast()->success('All batch quantities saved successfully.')->send();
            $this->loadDailyProduces();

        } catch (\Exception $e) {
            $this->toast()->error('Error saving batch quantities: ' . $e->getMessage())->send();
        }
    }

    /**
     * Create a product dispatch record when products are sent out to sales
     *
     * @param DailyProduce $produce Daily produce record
     * @param float $quantity Quantity to dispatch
     * @param int $salesDepartmentId Sales department receiving the dispatch
     */
    private function createProductDispatch($produce, $quantity, $salesDepartmentId)
    {
        if ($quantity <= 0) {
            return;
        }

        // Find product by matching SKU with recipe SKU
        $product = \App\Models\Product::where('sku', $produce->recipe->sku)->first();

        if (!$product) {
            // Log warning but don't fail - product might not be created yet
            logger()->warning("Product not found for recipe SKU: {$produce->recipe->sku}");
            return;
        }

        // Get sales department name for notes
        $salesDept = Department::find($salesDepartmentId);
        $salesDeptName = $salesDept?->name ?? 'Unknown Department';

        ProductDispatch::create([
            'branch_id' => $this->getBranchId(),
            'daily_produce_id' => $produce->id,
            'production_shift_id' => $produce->shift_id,
            'sales_department_id' => $salesDepartmentId,
            'product_id' => $product->id,
            'dispatched_by' => Auth::guard('web')->id(),
            'quantity' => $quantity,
            'uom' => $product->uom ?? $produce->recipe->unitOfMeasure?->symbol ?? 'units',
            'dispatch_time' => now(),
            'shift_type' => $produce->shift_type,
            'dispatch_date' => $produce->produce_date,
            'status' => 'dispatched',
            'notes' => "Dispatched from kitchen to {$salesDeptName} - {$produce->recipe->product_name}",
        ]);
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

        // Get available shifts for this department
        $availableShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $this->department->id)
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
