<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\Department;
use App\Models\ItemRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ItemRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBranch = '';
    public $filterDepartment = '';
    public $filterStatus = '';

    public $requestId;
    public $showApprovalModal = false;
    public $approvalNotes = '';

    public function render()
    {
        $query = ItemRequest::with(['branch', 'department', 'requester', 'approver', 'requestDetails'])
            ->when($this->search, function ($q) {
                $q->where('request_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('requester', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterDepartment, fn($q) => $q->where('department_id', $this->filterDepartment))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('livewire.super-admin.inventory.item-requests', [
            'requests' => $requests,
            'branches' => $branches,
            'departments' => $departments,
        ]);
    }

    public function openApprovalModal($id)
    {
        $this->authorize('approve-item-requests');
        $this->requestId = $id;
        $this->approvalNotes = '';
        $this->showApprovalModal = true;
    }

    public function approveRequest()
    {
        $this->authorize('approve-item-requests');

        $request = ItemRequest::findOrFail($this->requestId);

        if (!$request->canBeApproved()) {
            session()->flash('error', 'This request cannot be approved.');
            $this->closeModal();
            return;
        }

        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->guard('employees')->id(),
            'approved_at' => now(),
            'notes' => $this->approvalNotes,
        ]);

        // Auto-approve all request details with requested quantity
        foreach ($request->requestDetails as $detail) {
            $detail->update([
                'quantity_approved' => $detail->quantity_requested,
            ]);
        }

        session()->flash('success', 'Item request approved successfully.');
        $this->closeModal();
    }

    public function rejectRequest($id)
    {
        $this->authorize('approve-item-requests');

        $request = ItemRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            session()->flash('error', 'Only pending requests can be rejected.');
            return;
        }

        $request->update([
            'status' => 'rejected',
            'approved_by' => auth()->guard('employees')->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Item request rejected.');
    }

    public function closeModal()
    {
        $this->showApprovalModal = false;
        $this->requestId = null;
        $this->approvalNotes = '';
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterDepartment = '';
        $this->filterStatus = '';
    }
}
