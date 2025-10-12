<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\Department;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Stock;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class ItemRequests extends Component
{
    use WithPagination;
    #[Url(keep: true)]
    public $b_id;
    public $search = '';
    public $filterDepartment = '';
    public $filterStatus = '';

    protected $updatesQueryString = ['search', 'filterDepartment', 'filterStatus'];

    public $requestId;
    public $department_id = '';
    public $request_date;
    public $notes;

    public $table_quantity = 15;

    public $quantity;

    public $requestItems = [];
    public $itemIndex = 0;

    public $showModal = false;
    public $isEditing = false;
    public $showDetailModal = false;
    public $selectedRequest = null;

    protected $rules = [
        'department_id' => 'required|exists:departments,id',
        'request_date' => 'required|date|after_or_equal:today',
        'notes' => 'nullable|string',
        'requestItems.*.item_id' => 'required|exists:items,id',
        'requestItems.*.quantity_requested' => 'required|numeric|min:0.01',
    ];

    protected $messages = [
        'requestItems.*.quantity_requested.required' => 'Quantity is required for each item.',
        'requestItems.*.quantity_requested.numeric' => 'Quantity must be a number.',
        'requestItems.*.quantity_requested.min' => 'Quantity must be at least 0.01.',
    ];

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }


    public function mount()
    {
        $this->request_date = now()->addDays(1)->format('Y-m-d');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = ItemRequest::with(['branch', 'department', 'requester', 'approver', 'requestDetails'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('request_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('requester', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('department', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterDepartment, fn($q) => $q->where('department_id', $this->filterDepartment))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate($this->table_quantity);
        $departments = Department::where('branch_id', $branchId)->orWhere('branch_id', null)->orderBy('name')->get();
        $items = Item::where('branch_id', $branchId)->where('status', 'active')->orderBy('name')->get();

        return view('livewire.branch-dashboard.inventory.item-requests', [
            'requests' => $requests,
            'departments' => $departments,
            'items' => $items,
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-item-requests'); // TODO: Enable permissions after testing
        $this->resetFields();
        $this->addRequestItem();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function addRequestItem()
    {
        $this->requestItems[] = [
            'id' => $this->itemIndex++,
            'item_id' => '',
            'quantity_requested' => '',
        ];
    }

    public function removeRequestItem($index)
    {
        unset($this->requestItems[$index]);
        $this->requestItems = array_values($this->requestItems);
    }

    public function save()
    {
        // $this->authorize('create-item-requests'); // TODO: Enable permissions after testing
        $this->validate();

        // Validate stock availability
        $branchId = $this->getBranchId();
        foreach ($this->requestItems as $index => $item) {
            $stock = Stock::where('branch_id', $branchId)
                ->where('item_id', $item['item_id'])
                ->first();

            $availableQuantity = $stock ? $stock->quantity_available : 0;

            if ($item['quantity_requested'] > $availableQuantity) {
                $selectedItem = Item::find($item['item_id']);
                $this->addError("requestItems.{$index}.quantity_requested",
                    "Requested quantity for {$selectedItem->name} ({$item['quantity_requested']}) exceeds available stock ({$availableQuantity}).");
                session()->flash('error', 'Some items have insufficient stock. Please adjust quantities.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            $branch = Auth::guard('employees')->employee()->branch;

            // Verify department belongs to this branch or is a global department
            $department = Department::where('id', $this->department_id)
                ->where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereNull('branch_id');
                })
                ->firstOrFail();


            $requestNumber = ItemRequest::generateRequestNumber($branch->code, $department->name);

            $request = ItemRequest::create([
                'branch_id' => $branchId,
                'department_id' => $this->department_id,
                'request_number' => $requestNumber,
                'requested_by' => Auth::guard('employees')->id(),
                'request_date' => $this->request_date,
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            foreach ($this->requestItems as $item) {
                $selectedItem = Item::find($item['item_id']);
                ItemRequestDetail::create([
                    'request_id' => $request->id,
                    'item_id' => $item['item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'quantity_approved' => 0,
                    'quantity_dispatched' => 0,
                    'uom' => $selectedItem->uom,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Item request created successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating request: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->requestId = null;
        $this->department_id = '';
        $this->request_date = now()->addDays(1)->format('Y-m-d');
        $this->notes = '';
        $this->requestItems = [];
        $this->itemIndex = 0;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterDepartment = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function viewRequest($requestId)
    {
        $this->selectedRequest = ItemRequest::with(['branch', 'department', 'requester', 'approver', 'requestDetails.item'])
            ->findOrFail($requestId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRequest = null;
    }
}
