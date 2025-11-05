<?php

namespace App\Livewire\BranchDashboard\Production\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductDispatchCallback;
use App\Models\ProductDispatch;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class ApproveCallbacks extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Modal for viewing details
    public $selectedCallback = null;
    public $showDetailsModal = false;

    // Table headers
    public array $headers = [
        ['index' => 'callback_id', 'label' => 'Callback ID'],
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Quantity'],
        ['index' => 'reason', 'label' => 'Reason'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'sales_shift', 'label' => 'From Sales Shift'],
        ['index' => 'callback_time', 'label' => 'Callback Time'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    // Status options for filter
    public array $statusOptions = [
        'pending' => 'Pending Approval',
        'approved_by_production' => 'Approved (Awaiting Receipt)',
        'received_by_production' => 'Received (Awaiting Completion)',
        'completed' => 'Completed',
    ];

    protected function getModelClass(): string
    {
        return ProductDispatchCallback::class;
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->startDate = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
        $this->endDate = \Carbon\Carbon::today()->format('Y-m-d');
    }

    public function getRowsProperty()
    {
        $query = ProductDispatchCallback::with([
            'product',
            'salesShift',
            'productDispatch.shift',
            'recordedBy',
            'approvedBy',
            'receivedBy'
        ])
        ->whereHas('productDispatch.shift', function ($q) {
            $q->where('branch_id', $this->getBranchId());
        });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('product', function ($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('recordedBy', function ($employeeQuery) {
                    $employeeQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('callback_time', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('callback_time', '<=', $this->endDate);
        }

        return $query->orderBy('callback_time', 'desc')->paginate($this->quantity);
    }

    public function viewDetails($callbackId)
    {
        $this->selectedCallback = ProductDispatchCallback::with([
            'product',
            'productDispatch',
            'salesShift',
            'recordedBy',
            'approvedBy',
            'receivedBy'
        ])->find($callbackId);

        if (!$this->selectedCallback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedCallback = null;
    }

    public function approveCallback($callbackId)
    {
        try {
            DB::beginTransaction();

            $callback = ProductDispatchCallback::find($callbackId);

            if (!$callback) {
                $this->toast()->error('Callback not found.')->send();
                return;
            }

            if (!$callback->canBeApproved()) {
                $this->toast()->error('Callback cannot be approved. Current status: ' . $callback->formatted_status)->send();
                return;
            }

            // Get current employee ID
            $employeeId = $this->getEmployeeId();
            if (!$employeeId) {
                $this->toast()->error('Employee not found. Please ensure you are logged in.')->send();
                return;
            }

            $callback->approve($employeeId);

            DB::commit();

            $this->toast()->success('Callback approved successfully!')->send();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Failed to approve callback: ' . $e->getMessage())->send();
        }
    }

    public function receiveCallback($callbackId)
    {
        try {
            DB::beginTransaction();

            $callback = ProductDispatchCallback::find($callbackId);

            if (!$callback) {
                $this->toast()->error('Callback not found.')->send();
                return;
            }

            if (!$callback->canBeReceived()) {
                $this->toast()->error('Callback cannot be received. Current status: ' . $callback->formatted_status)->send();
                return;
            }

            // Get current employee ID
            $employeeId = $this->getEmployeeId();
            if (!$employeeId) {
                $this->toast()->error('Employee not found. Please ensure you are logged in.')->send();
                return;
            }

            $callback->markAsReceived($employeeId);

            DB::commit();

            $this->toast()->success('Callback received successfully!')->send();
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Failed to receive callback: ' . $e->getMessage())->send();
        }
    }

    public function completeCallback($callbackId)
    {
        try {
            DB::beginTransaction();

            $callback = ProductDispatchCallback::find($callbackId);

            if (!$callback) {
                $this->toast()->error('Callback not found.')->send();
                return;
            }

            if ($callback->status !== 'received_by_production') {
                $this->toast()->error('Callback must be received before completion. Current status: ' . $callback->formatted_status)->send();
                return;
            }

            $callback->complete();

            DB::commit();

            $this->toast()->success('Callback completed successfully!')->send();
            $this->dispatch('$refresh');

            // Close modal if open
            if ($this->showDetailsModal) {
                $this->closeDetailsModal();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Failed to complete callback: ' . $e->getMessage())->send();
        }
    }

    protected function getEmployeeId()
    {
        // Get the authenticated user's employee ID
        // Assuming you have a method to get current employee from session or auth
        return session('employee_id') ?? auth()->user()?->employee_id ?? null;
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved_by_production' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'received_by_production' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    public function render()
    {
        // Get stats for the branch
        $stats = [
            'total' => ProductDispatchCallback::whereHas('productDispatch.shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->count(),

            'pending' => ProductDispatchCallback::whereHas('productDispatch.shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'pending')->count(),

            'approved' => ProductDispatchCallback::whereHas('productDispatch.shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'approved_by_production')->count(),

            'received' => ProductDispatchCallback::whereHas('productDispatch.shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'received_by_production')->count(),

            'completed' => ProductDispatchCallback::whereHas('productDispatch.shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'completed')->count(),
        ];

        return view('livewire.branch-dashboard.production.callbacks.approve-callbacks', [
            'rows' => $this->rows,
            'stats' => $stats,
        ]);
    }
}
