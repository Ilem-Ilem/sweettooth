<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductDispatchCallback;
use App\Models\SalesShift;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Table headers
    public array $headers = [
        ['index' => 'callback_id', 'label' => 'Callback ID'],
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Quantity'],
        ['index' => 'reason', 'label' => 'Reason'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'callback_time', 'label' => 'Callback Time'],
        ['index' => 'recorded_by', 'label' => 'Recorded By'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    // Status options for filter
    public array $statusOptions = [
        'pending' => 'Pending',
        'approved_by_production' => 'Approved by Production',
        'received_by_production' => 'Received by Production',
        'completed' => 'Completed',
    ];

    protected function getModelClass(): string
    {
        return ProductDispatchCallback::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        $query = ProductDispatchCallback::query()
            ->with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->whereHas('salesShift', function ($q) {
                $q->where('branch_id', $this->getBranchId());
            });

        return $query;
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
        $query = ProductDispatchCallback::with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->whereHas('salesShift', function ($q) {
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
        $callback = ProductDispatchCallback::with([
            'product',
            'productDispatch',
            'salesShift',
            'recordedBy',
            'approvedBy',
            'receivedBy'
        ])->find($callbackId);

        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        // Store for modal display
        $this->dispatch('show-callback-details', callback: $callback->toArray());
    }

    public function exportCallbacks()
    {
        $this->toast()->info('Export feature coming soon.')->send();
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
        return view('livewire.branch-dashboard.sales-dashboard.callbacks.index', [
            'rows' => $this->rows,
        ]);
    }
}
