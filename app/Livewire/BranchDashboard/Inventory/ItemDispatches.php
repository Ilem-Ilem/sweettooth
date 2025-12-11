<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\{Layout, Url, On};
use Livewire\Component;
use Livewire\WithPagination;
// use TallStackUi\
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class ItemDispatches extends Component
{
    use Interactions, WithPagination;

    // Pagination
    public $quantity = 15;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $search = '';

    public $filterShift = '';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    public $showDispatchModal = false;

    public $requestId;

    public $dispatchedItems = [];

    protected $rules = [
        'dispatchedItems.*.approve_quantity' => 'nullable|numeric|min:0',
    ];

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function mount()
    {
        $this->b_id = current_branch_id();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        // Fetch ItemRequests instead of ItemDispatches
        $query = ItemRequest::with(['department', 'requestDetails.item', 'requester', 'requestedBy'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('request_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('department', function ($subQuery) {
                            $subQuery->where('name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('requester', function ($subQuery) {
                            $subQuery->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->filterShift, fn ($q) => $q->where('shift', $this->filterShift))
            ->when($this->filterDateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->filterDateTo))
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate($this->quantity ?? 15);

        // Pending requests for the accordion
        $pendingRequests = ItemRequest::with(['department', 'requestDetails.item'])
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'approved', 'partially_dispatched'])
            ->whereHas('requestDetails', function ($q) {
                $q->whereColumn('quantity_approved', '>', 'quantity_dispatched')
                    ->orWhere(function ($sub) {
                        $sub->where('quantity_approved', '=', 0)
                            ->whereColumn('quantity_requested', '>', 'quantity_dispatched');
                    });
            })
            ->latest('created_at')
            ->get();

        return view('livewire.branch-dashboard.inventory.item-dispatches', [
            'requests' => $requests,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    public function openDispatchModal($requestId)
    {
        // $this->authorize('dispatch-items'); // TODO: Enable permissions after testing
        $branchId = $this->getBranchId();

        $request = ItemRequest::with('requestDetails.item')
            ->where('id', $requestId)
            ->firstOrFail();

        $this->requestId = $requestId;
        $this->dispatchedItems = [];

        // Show ALL items - requested, approved, and dispatched
        foreach ($request->requestDetails as $detail) {
            $remainingToApprove = (float) (($detail->quantity_requested ?? 0) - ($detail->quantity_approved ?? 0));
            $remainingToDispatch = (float) (($detail->quantity_approved ?? 0) - ($detail->quantity_dispatched ?? 0));

            // Get current stock level for this branch
            $stock = Stock::where('branch_id', $branchId)
                ->where('item_id', $detail->item_id)
                ->first();

            $stockAvailable = $stock ? (float) $stock->quantity_available : 0.0;

            $this->dispatchedItems[] = [
                'detail_id' => $detail->id,
                'item_id' => $detail->item_id,
                'item_name' => $detail->item->name,
                'quantity_requested' => $detail->quantity_requested,
                'quantity_approved' => $detail->quantity_approved,
                'quantity_dispatched' => $detail->quantity_dispatched,
                'remaining_to_approve' => $remainingToApprove,
                'remaining_to_dispatch' => $remainingToDispatch,
                'approve_quantity' => 0,
                'stock_available' => $stockAvailable,
                'uom' => $detail->item->unitOfMeasure?->symbol,
                'is_fully_approved' => $remainingToApprove <= 0,
                'is_fully_dispatched' => $remainingToDispatch <= 0,
                'is_partially_approved' => $detail->quantity_approved > 0 && $remainingToApprove > 0,
                'is_partially_dispatched' => $detail->quantity_dispatched > 0 && $remainingToDispatch > 0,
                'has_sufficient_stock' => $stockAvailable >= $remainingToDispatch,
            ];
        }

        $this->showDispatchModal = true;
    }

    public function approveItems()
    {
        // $this->authorize('approve-items');
        $this->validate();

        $branchId = $this->getBranchId();

        if (empty($this->dispatchedItems) || ! is_array($this->dispatchedItems)) {
            session()->flash('error', 'No items to approve.');
            return;
        }

        try {
            DB::transaction(function () use ($branchId) {
                // Verify request belongs to this branch
                $request = ItemRequest::where('id', $this->requestId)
                    ->where('branch_id', $branchId)
                    ->firstOrFail();

                $approvedCount = 0;

                foreach ($this->dispatchedItems as $item) {
                    $approveQty = (float) ($item['approve_quantity'] ?? 0);

                    if ($approveQty <= 0) {
                        continue; // Skip items with no approval quantity
                    }

                    // Get the detail record
                    $detail = ItemRequestDetail::find($item['detail_id']);
                    if (! $detail) {
                        throw new \Exception("Request detail missing for {$item['item_name']}.");
                    }

                    $remainingToApprove = $detail->quantity_requested - $detail->quantity_approved;

                    if ($approveQty > $remainingToApprove) {
                        throw new \Exception("Cannot approve {$approveQty} {$item['uom']} of {$item['item_name']}. Only {$remainingToApprove} {$item['uom']} remaining to approve.");
                    }

                    $detail->quantity_approved += $approveQty;
                    $detail->save();

                    $approvedCount++;

                    logger()->info('Item approved', [
                        'item_id' => $item['item_id'],
                        'item_name' => $item['item_name'],
                        'approved_quantity' => $approveQty,
                        'total_approved' => $detail->quantity_approved,
                        'request_id' => $this->requestId,
                    ]);
                }

                if ($approvedCount > 0) {
                    $request->refresh();
                    
                    // Log the approval
                    $approvedItems = [];
                    foreach ($this->dispatchedItems as $item) {
                        if ((float)($item['approve_quantity'] ?? 0) > 0) {
                            $approvedItems[] = "{$item['item_name']}: {$item['approve_quantity']} {$item['uom']}";
                        }
                    }

                    // Log the approval
                    AuditService::log(
                        Auth::guard('employees')->user(),
                        'update',
                        $request,
                        "Approved {$approvedCount} item(s) from request #{$request->request_number}. " .
                        "Items: " . implode(', ', $approvedItems),
                        'completed'
                    );
                }
            });

            session()->flash('success', 'Items approved successfully. You can now dispatch them.');

            // Refresh the modal data to show updated approval status
            $this->openDispatchModal($this->requestId);

        } catch (\Exception $e) {
            logger()->error('Approval Error: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_id' => $this->requestId ?? null,
                'branch_id' => $branchId ?? null,
                'items' => $this->dispatchedItems ?? [],
            ]);

            session()->flash('error', 'Error approving items: '.$e->getMessage());
        }
    }

    public function dispatchItems()
    {
        // $this->authorize('dispatch-items');

        $branchId = $this->getBranchId();

        if (empty($this->dispatchedItems) || ! is_array($this->dispatchedItems)) {
            // session()->flash('error', 'No items to dispatch.');
            $this->toast()->error("here")->send();

            return;
        }

        try {
            // First, check if there are any items to dispatch
            $hasItemsToDispatch = false;
            foreach ($this->dispatchedItems as $item) {
                if ($item['remaining_to_dispatch'] > 0) {
                    $hasItemsToDispatch = true;
                    break;
                }
            }

            if (!$hasItemsToDispatch) {
                $this->toast()->error('No approved items to dispatch.')->send();
                return;
            }

            // Track low stock warnings
            $lowStockWarnings = [];

            DB::transaction(function () use ($branchId, &$lowStockWarnings) {
                // Verify request belongs to this branch
                $request = ItemRequest::where('id', $this->requestId)
                    ->where('branch_id', $branchId)
                    ->firstOrFail();

                $dispatchedCount = 0;

                foreach ($this->dispatchedItems as $item) {
                    // Get fresh data from database
                    $detail = ItemRequestDetail::find($item['detail_id']);
                    if (! $detail) {
                        throw new \Exception("Request detail missing for {$item['item_name']}.");
                    }

                    $dispatchQty = $detail->quantity_approved - $detail->quantity_dispatched;

                    if ($dispatchQty <= 0) {
                        continue; // Skip items that don't need dispatching
                    }

                    // Lock stock row for concurrency safety
                    $stock = Stock::where('item_id', $item['item_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $stock) {
                        throw new \Exception("Stock not found for {$item['item_name']} in this branch.");
                    }

                    // Check if stock is insufficient - still dispatch but warn
                    if ($stock->quantity_available < $dispatchQty) {
                        $lowStockWarnings[] = "{$item['item_name']}: Dispatching {$dispatchQty} {$item['uom']}, but only {$stock->quantity_available} {$item['uom']} available. Stock will go negative!";
                    }

                    // Save before and after quantities
                    $quantityBefore = $stock->quantity_available;
                    $stock->quantity_available -= $dispatchQty;
                    $stock->save();
                    $quantityAfter = $stock->quantity_available;

                    // Check if stock is now below reorder level
                    if ($stock->item && $stock->item->reorder_level && $quantityAfter <= $stock->item->reorder_level) {
                        $lowStockWarnings[] = "{$item['item_name']}: Stock level is now {$quantityAfter} {$item['uom']}, which is at or below the reorder level of {$stock->item->reorder_level} {$item['uom']}. Please restock!";
                    }

                    // Create dispatch record
                    ItemDispatch::create([
                        'branch_id' => $branchId,
                        'request_id' => $this->requestId,
                        'item_id' => $item['item_id'],
                        'dispatched_by' => Auth::guard('employees')->id(),
                        'quantity' => $dispatchQty,
                        'uom' => $item['uom'],
                        'dispatch_time' => now(),
                        'shift' => $request->shift,
                    ]);

                    // Update request detail (track total dispatched)
                    $detail->quantity_dispatched += $dispatchQty;
                    $detail->save();

                    // Record stock movement
                    StockMovement::create([
                        'stock_id' => $stock->id,
                        'type' => 'out',
                        'quantity' => -$dispatchQty,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $quantityAfter,
                        'movement_date' => now(),
                        'reference_type' => ItemRequest::class,
                        'reference_id' => $this->requestId,
                        'moved_by_id' => Auth::guard('employees')->id(),
                        'moved_by_type' => \App\Models\Employee::class,
                        'notes' => "Dispatch for request: {$request->request_number}",
                    ]);

                    $dispatchedCount++;

                    logger()->info('Item dispatched', [
                        'item_id' => $item['item_id'],
                        'item_name' => $item['item_name'],
                        'quantity' => $dispatchQty,
                        'before' => $quantityBefore,
                        'after' => $quantityAfter,
                        'stock_id' => $stock->id,
                        'request_id' => $this->requestId,
                    ]);
                }

                // Update request status
                $request->refresh();
                $request->update([
                    'status' => $request->isFullyDispatched()
                        ? 'completed'
                        : 'partially_dispatched',
                ]);

                // Prepare audit description
                $dispatchedItems = [];
                foreach ($this->dispatchedItems as $item) {
                    $detail = ItemRequestDetail::find($item['detail_id']);
                    if ($detail) {
                        $dispatchQty = $detail->quantity_approved - $detail->quantity_dispatched;
                        if ($dispatchQty > 0) {
                            $dispatchedItems[] = "{$item['item_name']}: {$dispatchQty} {$item['uom']}";
                        }
                    }
                }

                // Log the dispatch
                if (!empty($dispatchedItems)) {
                    AuditService::log(
                        Auth::guard('employees')->user(),
                        'update',
                        $request,
                        "Dispatched items from request #{$request->request_number}. " .
                        "Items: " . implode(', ', $dispatchedItems) . 
                        ". Status: {$request->status}",
                        'completed'
                    );
                }
            });

            // Show success message with warnings if applicable
            if (!empty($lowStockWarnings)) {
                $warningMessage = 'Items dispatched successfully, but with warnings: ' . implode(' | ', $lowStockWarnings);
                $this->toast()->warning($warningMessage)->send();
            } else {
                $this->toast()->success("All approved items dispatched successfully. Stock updated")->send();
            }

            $this->closeModal();

        } catch (\Exception $e) {
            logger()->error('Dispatch Error: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_id' => $this->requestId ?? null,
                'branch_id' => $branchId ?? null,
                'dispatch_items' => $this->dispatchedItems ?? [],
            ]);
            $this->toast()->error($e->getMessage())->send();
        }
    }

    public function closeModal()
    {
        $this->showDispatchModal = false;
        $this->requestId = null;
        $this->dispatchedItems = [];
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterShift = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterShift()
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatedFilterReceived()
    {
        $this->resetPage();
    }
}
