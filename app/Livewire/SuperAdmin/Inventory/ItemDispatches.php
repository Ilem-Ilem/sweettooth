<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ItemDispatches extends Component
{
    use Interactions, WithPagination;

    // Pagination
    public $quantity = 15;

    public $search = '';

    public $filterBranch = '';

    public $filterShift = '';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    public $showDispatchModal = false;

    public $requestId;

    public $dispatchedItems = [];

    protected $rules = [
        'dispatchedItems.*.approve_quantity' => 'nullable|numeric|min:0',
    ];

    public function render()
    {
        // Fetch ItemRequests instead of ItemDispatches
        $query = ItemRequest::with(['branch', 'department', 'requestDetails.item', 'requester', 'requestedBy'])
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
            ->when($this->filterBranch, fn ($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterShift, fn ($q) => $q->where('shift', $this->filterShift))
            ->when($this->filterDateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->filterDateTo))
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate($this->quantity ?? 15);

        // Pending requests for the accordion
        $pendingRequests = ItemRequest::with(['branch', 'department', 'requestDetails.item'])
            ->whereIn('status', ['pending', 'approved', 'partially_dispatched'])
            ->whereHas('requestDetails', function ($q) {
                $q->whereColumn('quantity_approved', '>', 'quantity_dispatched')
                    ->orWhere(function ($sub) {
                        $sub->where('quantity_approved', '=', 0)
                            ->whereColumn('quantity_requested', '>', 'quantity_dispatched');
                    });
            })
            ->when($this->filterBranch, fn ($q) => $q->where('branch_id', $this->filterBranch))
            ->latest('created_at')
            ->get();

        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.item-dispatches', [
            'requests' => $requests,
            'pendingRequests' => $pendingRequests,
            'branches' => $branches,
        ]);
    }

    public function openDispatchModal($requestId)
    {
        $request = ItemRequest::with('requestDetails.item')
            ->where('id', $requestId)
            ->firstOrFail();

        $this->requestId = $requestId;
        $this->dispatchedItems = [];

        // Show ALL items - requested, approved, and dispatched
        foreach ($request->requestDetails as $detail) {
            $remainingToApprove = $detail->quantity_requested - $detail->quantity_approved;
            $remainingToDispatch = $detail->quantity_approved - $detail->quantity_dispatched;

            // Get current stock level
            $stock = Stock::where('item_id', $detail->item_id)->first();
            $stockAvailable = $stock ? $stock->quantity_available : 0;

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
        $this->validate();

        if (empty($this->dispatchedItems) || ! is_array($this->dispatchedItems)) {
            session()->flash('error', 'No items to approve.');
            return;
        }

        try {
            DB::transaction(function () {
                $request = ItemRequest::where('id', $this->requestId)->firstOrFail();

                $approvedCount = 0;

                foreach ($this->dispatchedItems as $item) {
                    $approveQty = (float) ($item['approve_quantity'] ?? 0);

                    if ($approveQty <= 0) {
                        continue;
                    }

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
                }

                if ($approvedCount > 0) {
                    $request->refresh();
                }
            });

            session()->flash('success', 'Items approved successfully. You can now dispatch them.');
            $this->openDispatchModal($this->requestId);

        } catch (\Exception $e) {
            session()->flash('error', 'Error approving items: '.$e->getMessage());
        }
    }

    public function dispatchItems()
    {
        if (empty($this->dispatchedItems) || ! is_array($this->dispatchedItems)) {
            $this->toast()->error("No items to dispatch")->send();
            return;
        }

        try {
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

            $lowStockWarnings = [];

            DB::transaction(function () use (&$lowStockWarnings) {
                $request = ItemRequest::where('id', $this->requestId)->firstOrFail();

                $dispatchedCount = 0;

                foreach ($this->dispatchedItems as $item) {
                    $detail = ItemRequestDetail::find($item['detail_id']);
                    if (! $detail) {
                        throw new \Exception("Request detail missing for {$item['item_name']}.");
                    }

                    $dispatchQty = $detail->quantity_approved - $detail->quantity_dispatched;

                    if ($dispatchQty <= 0) {
                        continue;
                    }

                    $stock = Stock::where('item_id', $item['item_id'])->lockForUpdate()->first();

                    if (! $stock) {
                        throw new \Exception("Stock not found for {$item['item_name']}.");
                    }

                    if ($stock->quantity_available < $dispatchQty) {
                        $lowStockWarnings[] = "{$item['item_name']}: Dispatching {$dispatchQty} {$item['uom']}, but only {$stock->quantity_available} {$item['uom']} available. Stock will go negative!";
                    }

                    $quantityBefore = $stock->quantity_available;
                    $stock->quantity_available -= $dispatchQty;
                    $stock->save();
                    $quantityAfter = $stock->quantity_available;

                    if ($stock->item && $stock->item->reorder_level && $quantityAfter <= $stock->item->reorder_level) {
                        $lowStockWarnings[] = "{$item['item_name']}: Stock level is now {$quantityAfter} {$item['uom']}, which is at or below the reorder level of {$stock->item->reorder_level} {$item['uom']}. Please restock!";
                    }

                    ItemDispatch::create([
                        'branch_id' => $request->branch_id,
                        'request_id' => $this->requestId,
                        'item_id' => $item['item_id'],
                        'dispatched_by' => auth()->user()->employee_id ?? auth()->id(),
                        'quantity' => $dispatchQty,
                        'uom' => $item['uom'],
                        'dispatch_time' => now(),
                        'shift' => $request->shift,
                    ]);

                    $detail->quantity_dispatched += $dispatchQty;
                    $detail->save();

                    StockMovement::create([
                        'stock_id' => $stock->id,
                        'type' => 'out',
                        'quantity' => -$dispatchQty,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $quantityAfter,
                        'movement_date' => now(),
                        'reference_type' => ItemRequest::class,
                        'reference_id' => $this->requestId,
                        'moved_by' => auth()->user()->employee_id ?? auth()->id(),
                        'notes' => "Dispatch for request: {$request->request_number}",
                    ]);

                    $dispatchedCount++;
                }

                $request->refresh();
                $request->update([
                    'status' => $request->isFullyDispatched()
                        ? 'completed'
                        : 'partially_dispatched',
                ]);
            });

            if (!empty($lowStockWarnings)) {
                $warningMessage = 'Items dispatched successfully, but with warnings: ' . implode(' | ', $lowStockWarnings);
                $this->toast()->warning($warningMessage)->send();
            } else {
                $this->toast()->success("All approved items dispatched successfully. Stock updated")->send();
            }

            $this->closeModal();

        } catch (\Exception $e) {
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
        $this->filterBranch = '';
        $this->filterShift = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterBranch()
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
}
