<?php

namespace App\Livewire\BranchDashboard\Inventory\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductionCallback;
use App\Models\Shift;
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
    public ?string $b_id = null;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $filterSourceType = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Modal for viewing details
    public $selectedCallback = null;
    public $showDetailsModal = false;

    // Rejection modal
    public $showRejectModal = false;
    public $rejectReason = '';
    public $callbackToReject = null;

    // Table headers
    public array $headers = [
        ['index' => 'callback_id', 'label' => 'ID'],
        ['index' => 'source_type', 'label' => 'Type'],
        ['index' => 'item_product', 'label' => 'Item/Product'],
        ['index' => 'quantity', 'label' => 'Quantity'],
        ['index' => 'reason', 'label' => 'Reason'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'shift', 'label' => 'Production Shift'],
        ['index' => 'callback_time', 'label' => 'Callback Time'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    // Status options for filter
    public array $statusOptions = [
        'pending' => 'Pending Approval',
        'approved_by_inventory' => 'Approved',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ];

    // Source type options for filter
    public array $sourceTypeOptions = [
        'raw_material_from_stock' => 'Raw Material Return',
        'finished_product_reject' => 'Finished Product Reject',
    ];

    protected function getModelClass(): string
    {
        return ProductionCallback::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        $query = ProductionCallback::query()
            ->with(['shift', 'item', 'product', 'recordedBy', 'approvedBy'])
            ->whereHas('shift', function ($q) {
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
        $query = ProductionCallback::with([
            'shift',
            'item',
            'product',
            'recordedBy',
            'approvedBy'
        ])
        ->whereHas('shift', function ($q) {
            $q->where('branch_id', $this->getBranchId());
        });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('item', function ($itemQuery) {
                    $itemQuery->where('name', 'like', '%' . $this->search . '%')
                              ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('product', function ($productQuery) {
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

        // Source type filter
        if ($this->filterSourceType) {
            $query->where('source_type', $this->filterSourceType);
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
        $this->selectedCallback = ProductionCallback::with([
            'shift',
            'item',
            'product',
            'recordedBy',
            'approvedBy'
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

            $callback = ProductionCallback::find($callbackId);

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

            // Update stock based on source_type
            if ($callback->isRawMaterial()) {
                // Raw material return: Update Stock
                $this->handleRawMaterialCallback($callback);
            } elseif ($callback->isFinishedProduct()) {
                // Finished product reject: Update DailyProduce
                $this->handleFinishedProductCallback($callback);
            }

            DB::commit();

            $this->toast()->success('Callback approved successfully!')->send();
            $this->dispatch('$refresh');

            // Close modal if open
            if ($this->showDetailsModal) {
                $this->closeDetailsModal();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Failed to approve callback: ' . $e->getMessage())->send();
        }
    }

    public function openRejectModal($callbackId)
    {
        $this->callbackToReject = $callbackId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->callbackToReject = null;
        $this->rejectReason = '';
    }

    public function rejectCallback()
    {
        if (empty($this->rejectReason)) {
            $this->toast()->error('Please provide a reason for rejection.')->send();
            return;
        }

        try {
            DB::beginTransaction();

            $callback = ProductionCallback::find($this->callbackToReject);

            if (!$callback) {
                $this->toast()->error('Callback not found.')->send();
                return;
            }

            if (!$callback->canBeApproved()) {
                $this->toast()->error('Callback cannot be rejected. Current status: ' . $callback->formatted_status)->send();
                return;
            }

            // Get current employee ID
            $employeeId = $this->getEmployeeId();
            if (!$employeeId) {
                $this->toast()->error('Employee not found. Please ensure you are logged in.')->send();
                return;
            }

            $callback->reject($employeeId, $this->rejectReason);

            DB::commit();

            $this->toast()->success('Callback rejected successfully.')->send();
            $this->closeRejectModal();
            $this->dispatch('$refresh');

            // Close details modal if open
            if ($this->showDetailsModal) {
                $this->closeDetailsModal();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Failed to reject callback: ' . $e->getMessage())->send();
        }
    }

    public function completeCallback($callbackId)
    {
        try {
            DB::beginTransaction();

            $callback = ProductionCallback::find($callbackId);

            if (!$callback) {
                $this->toast()->error('Callback not found.')->send();
                return;
            }

            if ($callback->status !== 'approved_by_inventory') {
                $this->toast()->error('Callback must be approved before completion. Current status: ' . $callback->formatted_status)->send();
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
        return session('employee_id') ?? auth()->user()?->employee_id ?? null;
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved_by_inventory' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    /**
     * Handle raw material callback stock updates
     */
    protected function handleRawMaterialCallback(ProductionCallback $callback): void
    {
        if (!$callback->item_id) {
            throw new \Exception('Raw material callback missing item_id');
        }

        // Get the branch ID from the shift
        $branchId = $callback->shift->branch_id ?? $this->getBranchId();

        // Find the stock record for this item and branch
        $stock = \App\Models\Stock::where('item_id', $callback->item_id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$stock) {
            throw new \Exception('Stock record not found for item ID: ' . $callback->item_id);
        }

        // Decrease available quantity and increase damaged quantity
        $stock->quantity_available = max(0, $stock->quantity_available - $callback->quantity);
        $stock->quantity_damaged = $stock->quantity_damaged + $callback->quantity;
        $stock->save();

        // Log the stock movement (optional but recommended)
        \App\Models\StockMovement::create([
            'stock_id' => $stock->id,
            'type' => 'callback',
            'quantity' => -$callback->quantity,
            'reference_type' => 'production_callback',
            'reference_id' => $callback->id,
            'notes' => 'Production callback: ' . $callback->reason . ' (Callback #' . $callback->id . ')',
            'movement_date' => now(),
        ]);
    }

    /**
     * Handle finished product callback updates
     */
    protected function handleFinishedProductCallback(ProductionCallback $callback): void
    {
        if (!$callback->product_id) {
            throw new \Exception('Finished product callback missing product_id');
        }

        // Find the DailyProduce record for this shift and recipe/product
        // First, get the recipe for this product
        $recipe = \App\Models\Recipe::where('product_id', $callback->product_id)->first();

        if (!$recipe) {
            throw new \Exception('Recipe not found for product ID: ' . $callback->product_id);
        }

        $dailyProduce = \App\Models\DailyProduce::where('shift_id', $callback->shift_id)
            ->where('recipe_id', $recipe->id)
            ->first();

        if (!$dailyProduce) {
            throw new \Exception('DailyProduce record not found for shift ID: ' . $callback->shift_id . ' and recipe ID: ' . $recipe->id);
        }

        // Increase callback quantity
        $dailyProduce->callback_quantity = $dailyProduce->callback_quantity + $callback->quantity;

        // Recalculate expected closing and variance
        $dailyProduce->updateCalculations();
    }

    public function render()
    {
        // Get stats for the branch
        $stats = [
            'total' => ProductionCallback::whereHas('shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->count(),

            'pending' => ProductionCallback::whereHas('shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'pending')->count(),

            'approved' => ProductionCallback::whereHas('shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'approved_by_inventory')->count(),

            'completed' => ProductionCallback::whereHas('shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'completed')->count(),

            'rejected' => ProductionCallback::whereHas('shift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            })->where('status', 'rejected')->count(),
        ];

        return view('livewire.branch-dashboard.inventory.callbacks.approve-callbacks', [
            'rows' => $this->rows,
            'stats' => $stats,
        ]);
    }
}
