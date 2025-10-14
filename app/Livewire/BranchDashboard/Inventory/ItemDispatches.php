<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Stock;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;

#[Layout('components.layouts.app.branch-dashboard')]
class ItemDispatches extends Component
{
    use WithPagination;

    // Pagination
    public $quantity = 15;
    #[Url(keep: true)]
    public $b_id;
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
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = ItemDispatch::with(['itemRequest', 'item', 'dispatcher', 'receiver'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('itemRequest', function ($subQuery) {
                        $subQuery->where('request_number', 'like', '%' . $this->search . '%');
                    })
                        ->orWhereHas('item', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('sku', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('dispatcher', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        });
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

        $dispatches = $query->paginate($this->quantity ?? 15);

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
        // $this->authorize('dispatch-items'); // TODO: Enable permissions after testing
        $branchId = $this->getBranchId();

        $request = ItemRequest::with('requestDetails.item')
            ->where('id', $requestId)
            ->firstOrFail();

        $this->requestId = $requestId;
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

    public function dispatchItems()
    {
        // $this->authorize('dispatch-items');
        $this->validate();

        DB::beginTransaction();

        try {
            $branchId = $this->getBranchId();

            // Verify request belongs to this branch
            $request = ItemRequest::where('id', $this->requestId)
                ->where('branch_id', $branchId)
                ->firstOrFail();

            foreach ($this->dispatchItems as $item) {
                if ($item['quantity'] > 0) {
                    // Create dispatch record
                    ItemDispatch::create([
                        'request_id' => $this->requestId,
                        'item_id' => $item['item_id'],
                        'dispatched_by' => Auth::guard('employees')->id(),
                        'quantity' => $item['quantity'],
                        'uom' => $item['uom'],
                        'dispatch_time' => now(),
                        'shift' => $this->shift,
                    ]);

                    // Update request detail
                    $detail = ItemRequestDetail::find($item['detail_id']);
                    $detail->quantity_dispatched += $item['quantity'];
                    $detail->save();

                    // Update stock - use correct field name
                    $stock = Stock::where('branch_id', $branchId)
                        ->where('item_id', $item['item_id'])
                        ->first();

                    if ($stock) {
                        $stock->quantity_available -= $item['quantity'];
                        $stock->save();

                        // Record stock movement
                        StockMovement::create([
                            'stock_id' => $stock->id,
                            'item_id' => $item['item_id'],
                            'branch_id' => $branchId,
                            'movement_type' => 'out',
                            'quantity' => $item['quantity'],
                            'reference_type' => 'App\Models\ItemRequest',
                            'reference_id' => $this->requestId,
                            'recorded_by' => Auth::guard('employees')->id(),
                            'movement_date' => now(),
                            'notes' => 'Dispatch for request: ' . $request->request_number,
                        ]);
                    }
                }
            }

            // Check if fully dispatched
            if ($request->isFullyDispatched()) {
                $request->update(['status' => 'completed']);
            } else {
                $request->update(['status' => 'partially_dispatched']);
            }

            DB::commit();
            session()->flash('success', 'Items dispatched successfully.');
            $this->closeModal();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            session()->flash('error', 'Error dispatching items: ' . $e->getMessage());
        }
    }

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
