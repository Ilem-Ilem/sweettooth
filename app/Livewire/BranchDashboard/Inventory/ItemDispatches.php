<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemDispatches extends Component
{
    use WithPagination;

    public $search = '';
    public $filterShift = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterReceived = '';

    public $showDispatchModal = false;
    public $requestId;
    public $dispatchItems = [];
    public $shift = '';

    protected $rules = [
        'shift' => 'required|in:morning,afternoon,night',
        'dispatchItems.*.quantity' => 'required|numeric|min:0.01',
    ];

    public function getBranchId()
    {
        return Auth::guard('employees')->user()->employee->branch_id;
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = ItemDispatch::with(['itemRequest.branch', 'itemRequest.department', 'item', 'dispatcher', 'receiver'])
            ->whereHas('itemRequest', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('itemRequest', function ($query) {
                    $query->where('request_number', 'like', '%' . $this->search . '%');
                })->orWhereHas('item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterShift, fn($q) => $q->where('shift', $this->filterShift))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('dispatch_time', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('dispatch_time', '<=', $this->filterDateTo))
            ->when($this->filterReceived !== '', function ($q) {
                if ($this->filterReceived === '1') {
                    $q->whereNotNull('received_time');
                } else {
                    $q->whereNull('received_time');
                }
            })
            ->orderBy('dispatch_time', 'desc');

        $dispatches = $query->paginate(15);

        $pendingRequests = ItemRequest::with(['department', 'requestDetails.item'])
            ->where('branch_id', $branchId)
            ->where('status', 'approved')
            ->get();

        return view('livewire.branch-dashboard.inventory.item-dispatches', [
            'dispatches' => $dispatches,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    public function openDispatchModal($requestId)
    {
        $this->authorize('dispatch-items');
        $this->requestId = $requestId;

        $request = ItemRequest::with('requestDetails.item')->findOrFail($requestId);
        $this->dispatchItems = [];

        foreach ($request->requestDetails as $detail) {
            $remainingQty = $detail->quantity_approved - $detail->quantity_dispatched;
            if ($remainingQty > 0) {
                $this->dispatchItems[] = [
                    'detail_id' => $detail->id,
                    'item_id' => $detail->item_id,
                    'item_name' => $detail->item->name,
                    'quantity_approved' => $detail->quantity_approved,
                    'quantity_dispatched' => $detail->quantity_dispatched,
                    'remaining' => $remainingQty,
                    'quantity' => $remainingQty,
                    'uom' => $detail->item->uom,
                ];
            }
        }

        $this->shift = '';
        $this->showDispatchModal = true;
    }

    // public function dispatch($)
    // {
    //     $this->authorize('dispatch-items');
    //     $this->validate();

    //     DB::beginTransaction();
    //     try {
    //         $branchId = $this->getBranchId();
    //         $request = ItemRequest::findOrFail($this->requestId);

    //         foreach ($this->dispatchItems as $item) {
    //             if ($item['quantity'] > 0) {
    //                 // Create dispatch record
    //                 ItemDispatch::create([
    //                     'request_id' => $this->requestId,
    //                     'item_id' => $item['item_id'],
    //                     'dispatched_by' => Auth::guard('employees')->id(),
    //                     'quantity' => $item['quantity'],
    //                     'uom' => $item['uom'],
    //                     'dispatch_time' => now(),
    //                     'shift' => $this->shift,
    //                 ]);

    //                 // Update request detail
    //                 $detail = ItemRequestDetail::find($item['detail_id']);
    //                 $detail->quantity_dispatched += $item['quantity'];
    //                 $detail->save();

    //                 // Update stock
    //                 $stock = Stock::where('branch_id', $branchId)
    //                     ->where('item_id', $item['item_id'])
    //                     ->first();

    //                 if ($stock) {
    //                     $stock->available_quantity -= $item['quantity'];
    //                     $stock->total_quantity -= $item['quantity'];
    //                     $stock->save();

    //                     // Record stock movement
    //                     StockMovement::create([
    //                         'stock_id' => $stock->id,
    //                         'item_id' => $item['item_id'],
    //                         'branch_id' => $branchId,
    //                         'movement_type' => 'out',
    //                         'quantity' => $item['quantity'],
    //                         'reference_type' => 'App\Models\ItemRequest',
    //                         'reference_id' => $this->requestId,
    //                         'recorded_by' => Auth::guard('employees')->id(),
    //                         'movement_date' => now(),
    //                         'notes' => 'Dispatch for request: ' . $request->request_number,
    //                     ]);
    //                 }
    //             }
    //         }

    //         // Check if fully dispatched
    //         if ($request->isFullyDispatched()) {
    //             $request->update(['status' => 'completed']);
    //         } else {
    //             $request->update(['status' => 'partially_dispatched']);
    //         }

    //         DB::commit();
    //         session()->flash('success', 'Items dispatched successfully.');
    //         $this->closeModal();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'Error dispatching items: ' . $e->getMessage());
    //     }
    // }

    public function closeModal()
    {
        $this->showDispatchModal = false;
        $this->requestId = null;
        $this->dispatchItems = [];
        $this->shift = '';
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterShift = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterReceived = '';
    }
}
