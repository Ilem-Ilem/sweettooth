<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Models\ProductionRequest;
use App\Models\Shift;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    public $search = '';
    public $statusFilter = 'all';
    public $shiftFilter = 'all';

    // Modal properties
    public $showViewModal = false;
    public $viewingRequest = null;
    public $requestItems = [];

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openViewModal($requestId)
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        $request = ProductionRequest::with([
            'recipe',
            'shift',
            'itemRequest.requestDetails.item',
            'itemRequest.department',
            'itemRequest.requester'
        ])
            ->whereHas('itemRequest', function ($q) use ($branchId, $employee) {
                $q->where('branch_id', $branchId)
                  ->where('department_id', $employee->department_id);
            })
            ->findOrFail($requestId);

        $this->viewingRequest = $request;
        $this->requestItems = [];

        // Prepare items data
        foreach ($request->itemRequest->requestDetails as $detail) {
            $this->requestItems[] = [
                'item_name' => $detail->item->name ?? 'N/A',
                'quantity_requested' => $detail->quantity_requested,
                'quantity_approved' => $detail->quantity_approved,
                'quantity_dispatched' => $detail->quantity_dispatched,
                'uom' => $detail->uom ?? $detail->item->uom ?? '',
                'status' => $this->getItemStatus($detail),
            ];
        }

        $this->showViewModal = true;
    }

    private function getItemStatus($detail)
    {
        $requested = (float) $detail->quantity_requested;
        $approved = (float) $detail->quantity_approved;
        $dispatched = (float) $detail->quantity_dispatched;

        // Completed: requested = approved = dispatched AND not 0
        if ($requested == $approved && $approved == $dispatched && $requested > 0) {
            return ['label' => 'Completed', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'];
        }
        // Partially Dispatched: dispatched > 0 but < approved
        elseif ($dispatched > 0 && $dispatched < $approved) {
            return ['label' => 'Partially Dispatched', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'];
        }
        // Partially Approved: approved > 0 but < requested
        elseif ($approved > 0 && $approved < $requested) {
            return ['label' => 'Partially Approved', 'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'];
        }
        // Approved: requested = approved > 0, dispatched = 0
        elseif ($approved > 0 && $requested == $approved && $dispatched == 0) {
            return ['label' => 'Approved', 'class' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200'];
        }
        // Pending: approved = 0, dispatched = 0
        else {
            return ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'];
        }
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingRequest = null;
        $this->requestItems = [];
    }

    public function cancelRequest($requestId)
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        try {
            DB::transaction(function () use ($requestId, $branchId, $employee) {
                $request = ProductionRequest::with('itemRequest')
                    ->whereHas('itemRequest', function ($q) use ($branchId, $employee) {
                        $q->where('branch_id', $branchId)
                          ->where('department_id', $employee->department_id);
                    })
                    ->findOrFail($requestId);

                if (!$request->canBeCancelled()) {
                    throw new \Exception('This request cannot be cancelled.');
                }

                // Update the item request status to cancelled
                $request->itemRequest->update(['status' => 'cancelled']);
            });

            $this->toast()->success('Request cancelled successfully.')->send();
            $this->closeViewModal();
        } catch (\Exception $e) {
            $this->toast()->error('Error cancelling request: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();
        $departmentId = $employee->department_id;

        $query = ProductionRequest::with(['recipe', 'shift', 'itemRequest.requestDetails'])
            ->whereHas('itemRequest', function ($q) use ($branchId, $departmentId) {
                $q->where('branch_id', $branchId)
                  ->where('department_id', $departmentId);
            });

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->whereHas('itemRequest', function ($q) {
                if ($this->statusFilter === 'pending') {
                    $q->where('status', 'pending');
                } elseif ($this->statusFilter === 'approved') {
                    $q->where('status', 'approved');
                } elseif ($this->statusFilter === 'completed') {
                    $q->where('status', 'completed');
                } elseif ($this->statusFilter === 'cancelled') {
                    $q->where('status', 'cancelled');
                } elseif ($this->statusFilter === 'partially_dispatched') {
                    $q->where('status', 'partially_dispatched');
                }
            });
        }

        // Apply shift filter
        if ($this->shiftFilter !== 'all') {
            $query->where('shift_id', $this->shiftFilter);
        }

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('recipe', function ($subQ) {
                    $subQ->where('product_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('itemRequest', function ($subQ) {
                    $subQ->where('request_number', 'like', '%' . $this->search . '%');
                });
            });
        }

        $requests = $query->latest()->paginate(15);

        // Get all shifts for the department
        $allShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $departmentId)
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type')
            ->limit(30) // Limit to recent shifts
            ->get();

        // Get status summary - requests that have been worked on (not pending, not cancelled)
        $statusSummary = ProductionRequest::with('itemRequest')
            ->whereHas('itemRequest', function ($q) use ($branchId, $departmentId) {
                $q->where('branch_id', $branchId)
                  ->where('department_id', $departmentId);
            })
            ->get()
            ->groupBy(function ($request) {
                return $request->getComputedStatus();
            })
            ->map(function ($group) {
                return $group->count();
            });

        return view('livewire.branch-dashboard.production.request.index', [
            'requests' => $requests,
            'allShifts' => $allShifts,
            'statusSummary' => $statusSummary,
        ]);
    }
}
