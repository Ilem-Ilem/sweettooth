<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductDispatch;
use App\Models\ProductDispatchCallback;
use App\Models\ProductStock;
use App\Models\SalesShift;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;
use function is_super_admin;

#[Layout('components.layouts.app.branch-dashboard')]
class CreateDispatchCallback extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    #[Url(keep: true)]
    public ?string $salesDeptSlug = null;

    public ?string $branchId = null;
    public ?int $departmentId = null;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public string $sourceMode = 'dispatch'; // dispatch|stock
    public bool $lockSource = false;
    public string $pageTitle = 'Dispatch Callbacks';
    public string $pageSubtitle = 'Return products back to production from dispatches';

    public ?string $currentSalesShiftId = null;
    public ?string $selectedSalesShiftId = null;
    public $availableShifts = [];
    public $stockDate;
    public bool $isSuperAdmin = false;

    // Callback form
    public $showCallbackModal = false;
    public $selectedDispatch = null;
    public $selectedStock = null;
    public string $callbackSource = 'dispatch';
    public $callbackQuantity = 0;
    public $callbackReason = '';
    public $callbackNotes = '';

    // Reason options
    public array $reasonOptions = [
        'expired' => 'Expired',
        'damaged' => 'Damaged',
        'quality_issue' => 'Quality Issue',
        'customer_return' => 'Customer Return',
        'over_received' => 'Over Received',
        'wrong_item' => 'Wrong Item',
        'other' => 'Other',
    ];

    // Table headers (computed in render)
    public array $headers = [];

    protected function getModelClass(): string
    {
        return ProductDispatch::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        if ($this->sourceMode === 'stock') {
            $query = ProductStock::query()
                ->with('product')
                ->whereDate('stock_date', '>=', now()->subDays(30))
                ->orderByDesc('stock_date')
                ->orderByDesc('shift_type');

            if (Schema::hasColumn('product_stocks', 'department_id')) {
                $query->when($this->departmentId, function ($q) {
                    $q->where(function ($nested) {
                        $nested->where('department_id', $this->departmentId)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('department_id')
                                    ->whereHas('product', fn ($p) => $p->where('sales_department_id', $this->departmentId));
                            });
                    });
                });
            }

            return $query;
        }

        return ProductDispatch::query()
            ->where('branch_id', $this->getBranchId())
            ->when($this->departmentId, fn ($q) => $q->where('sales_department_id', $this->departmentId))
            ->where('status', 'received');
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $mode = request()->query('source');
        if (in_array($mode, ['dispatch', 'stock'], true)) {
            $this->sourceMode = $mode;
        }

        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->isSuperAdmin = is_super_admin();
        $this->loadBranchAndDepartment();
        $this->loadAvailableShifts();
        $this->loadCurrentSalesShift();

        // Default to "All Shifts"
        $this->selectedSalesShiftId = null;
        $this->pageTitle = $this->pageTitle ?: 'Dispatch Callbacks';
        $this->pageSubtitle = $this->pageSubtitle ?: 'Return products back to production from dispatches';
    }

    protected function loadAvailableShifts()
    {
        $branchId = $this->getBranchId();

        // Get sales shifts from last 30 days
        $this->availableShifts = SalesShift::where('branch_id', $branchId)
            ->where('shift_date', '>=', now()->subDays(30))
            ->when($this->departmentId, fn ($q) => $q->where('department_id', $this->departmentId))
            ->with('department')
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type', 'desc')
            ->get();
    }

    protected function loadBranchAndDepartment(): void
    {
        $this->branchId = $this->getBranchId();
        if ($this->salesDeptSlug) {
            $department = Department::where('slug', $this->salesDeptSlug)
                ->where('branch_id', $this->branchId)
                ->first();

            if (! $department) {
                $department = Department::where('slug', $this->salesDeptSlug)
                    ->whereNull('branch_id')
                    ->first();
            }

            if ($department) {
                $this->departmentId = $department->id;
            }
        }
    }

    protected function loadCurrentSalesShift()
    {
        if ($this->isSuperAdmin) {
            return;
        }

        $employee = auth()->user();

        // First try to find active sales shift for this employee
        $activeShift = SalesShift::where('branch_id', $this->getBranchId())
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->where('employee_id', $employee->id)
            ->first();

        // If not found, try to find any active sales shift in the employee's department
        if (!$activeShift && $employee->department_id) {
            $activeShift = SalesShift::where('branch_id', $this->getBranchId())
                ->where('shift_date', \Carbon\Carbon::today())
                ->where('status', 'active')
                ->where('department_id', $employee->department_id)
                ->first();
        }

        // If still not found, try to find any active sales shift in the branch
        if (!$activeShift) {
            $activeShift = SalesShift::where('branch_id', $this->getBranchId())
                ->where('shift_date', \Carbon\Carbon::today())
                ->where('status', 'active')
                ->whereHas('department.category', function ($q) {
                    $q->where('name', 'Sales');
                })
                ->first();
        }

        if ($activeShift) {
            $this->currentSalesShiftId = $activeShift->id;
        }
    }

    public function updatedSelectedSalesShiftId($shiftId = null)
    {
        // Refresh data when shift selection changes
        $this->resetPage();
    }

    public function getRowsProperty()
    {
        if ($this->sourceMode === 'stock') {
            $query = ProductStock::with('product')
                ->whereDate('stock_date', '>=', now()->subDays(30))
                ->orderByDesc('stock_date')
                ->orderByDesc('shift_type');

            if (Schema::hasColumn('product_stocks', 'department_id')) {
                $query->when($this->departmentId, function ($q) {
                    $q->where(function ($nested) {
                        $nested->where('department_id', $this->departmentId)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('department_id')
                                    ->whereHas('product', fn ($p) => $p->where('sales_department_id', $this->departmentId));
                            });
                    });
                });
            }

            if ($this->search) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            }

            return $query->paginate($this->quantity);
        }

        $shiftId = $this->selectedSalesShiftId;

        $query = ProductDispatch::with([
            'product',
            'shift',
            'productDispatchCallbacks',
            'salesProductionRequestItem.product',
            'salesProductionRequestItem.recipe',
            'dailyProduce.recipe',
        ])
            ->where('branch_id', $this->getBranchId())
            ->when($this->departmentId, fn ($q) => $q->where('sales_department_id', $this->departmentId))
            ->when($shiftId, fn ($q) => $q->where('sales_shift_id', $shiftId))
            ->where('status', 'received');

        // Search filter
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('dispatch_date', 'desc')->paginate($this->quantity);
    }

    public function openCallbackModal($dispatchId)
    {
        if ($this->sourceMode === 'stock') {
            $this->selectedStock = ProductStock::with('product')->find($dispatchId);
            if (! $this->selectedStock) {
                $this->toast()->error('Stock record not found.')->send();
                return;
            }
            $this->callbackSource = 'stock';
        } else {
            $this->selectedDispatch = ProductDispatch::with(['product', 'productDispatchCallbacks'])->find($dispatchId);
            if (! $this->selectedDispatch) {
                $this->toast()->error('Dispatch not found.')->send();
                return;
            }
            $this->callbackSource = 'dispatch';
        }

        $this->callbackQuantity = 0;
        $this->callbackReason = '';
        $this->callbackNotes = '';
        $this->showCallbackModal = true;
    }

    public function closeCallbackModal()
    {
        $this->showCallbackModal = false;
        $this->selectedDispatch = null;
        $this->selectedStock = null;
        $this->callbackSource = 'dispatch';
        $this->callbackQuantity = 0;
        $this->callbackReason = '';
        $this->callbackNotes = '';
    }

    public function getAvailableQuantity($dispatch)
    {
        $totalCallbacks = $dispatch->productDispatchCallbacks()
            ->whereIn('status', ['pending', 'approved_by_production', 'received_by_production', 'completed'])
            ->sum('quantity');

        return $dispatch->received_quantity - $totalCallbacks;
    }

    public function getAvailableStockQuantity(ProductStock $stock): float
    {
        $base = (float) ($stock->closing_quantity ?? 0);
        $query = ProductDispatchCallback::where('product_id', $stock->product_id)
            ->whereIn('status', ['pending', 'approved_by_production', 'received_by_production', 'completed']);

        if (! empty($stock->sales_shift_id)) {
            $query->where('sales_shift_id', $stock->sales_shift_id);
        } else {
            $query->whereNull('sales_shift_id');
        }

        $totalCallbacks = (float) $query->sum('quantity');
        return max(0, $base - $totalCallbacks);
    }

    public function submitCallback()
    {
        $this->validate([
            'callbackQuantity' => 'required|numeric|min:0.01',
            'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,over_received,wrong_item,other',
        ], [
            'callbackQuantity.required' => 'Callback quantity is required',
            'callbackQuantity.min' => 'Callback quantity must be greater than 0',
            'callbackReason.required' => 'Please select a callback reason',
        ]);

        try {
            DB::beginTransaction();

            if ($this->callbackSource === 'stock') {
                if (! $this->selectedStock) {
                    throw new \Exception('Stock record not found');
                }
            } elseif (! $this->selectedDispatch) {
                throw new \Exception('Dispatch not found');
            }

            // Validate callback quantity doesn't exceed available
            $availableQty = $this->callbackSource === 'stock'
                ? $this->getAvailableStockQuantity($this->selectedStock)
                : $this->getAvailableQuantity($this->selectedDispatch);
            if ($this->callbackQuantity > $availableQty) {
                $this->toast()->error("Callback quantity cannot exceed available quantity ({$availableQty}).")->send();
                return;
            }

            $employee = auth()->user();

            $shiftId = null;
            if ($this->callbackSource === 'stock') {
                $shiftId = $this->selectedStock->sales_shift_id
                    ?? $this->selectedSalesShiftId
                    ?? $this->currentSalesShiftId;
            } else {
                $shiftId = $this->selectedDispatch->sales_shift_id
                    ?? $this->selectedSalesShiftId
                    ?? $this->currentSalesShiftId;
            }

            // Create callback record
            $callback = ProductDispatchCallback::create([
                'product_dispatch_id' => $this->callbackSource === 'dispatch' ? $this->selectedDispatch->id : null,
                'sales_shift_id' => $shiftId,
                'product_id' => $this->callbackSource === 'dispatch'
                    ? $this->selectedDispatch->product_id
                    : $this->selectedStock->product_id,
                'recorded_by_id' => $employee->id,
                'recorded_by_type' => get_class($employee),
                'quantity' => $this->callbackQuantity,
                'uom' => $this->callbackSource === 'dispatch'
                    ? $this->selectedDispatch->uom
                    : ($this->selectedStock->product?->unitOfMeasure?->symbol ?? $this->selectedStock->product?->uomSymbol ?? 'unit'),
                'reason' => $this->callbackReason,
                'status' => 'pending',
                'notes' => $this->callbackNotes,
                'callback_time' => now(),
            ]);

            // Immediately reflect callback in sales stock
            $callback->syncProductStock();

            DB::commit();

            $this->toast()->success('Dispatch callback created successfully! Awaiting production approval.')->send();
            $this->closeCallbackModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error creating callback: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        $shiftId = $this->selectedSalesShiftId;

        return view('livewire.branch-dashboard.sales-dashboard.callbacks.create-dispatch-callback', [
            'rows' => $this->rows,
            'currentSalesShift' => $this->currentSalesShiftId ? SalesShift::find($this->currentSalesShiftId) : null,
            'selectedSalesShift' => $shiftId ? SalesShift::find($shiftId) : null,
            'headers' => $this->sourceMode === 'stock'
                ? [
                    ['index' => 'product', 'label' => 'Product'],
                    ['index' => 'dispatch_date', 'label' => 'Stock Date'],
                    ['index' => 'quantity', 'label' => 'Quantity'],
                    ['index' => 'received_qty', 'label' => 'Opening Qty'],
                    ['index' => 'returned_qty', 'label' => 'Callbacks'],
                    ['index' => 'available_to_return', 'label' => 'Available to Return'],
                    ['index' => 'status', 'label' => 'Status'],
                    ['index' => 'action', 'label' => 'Action'],
                ]
                : [
                    ['index' => 'product', 'label' => 'Product'],
                    ['index' => 'dispatch_date', 'label' => 'Dispatch Date'],
                    ['index' => 'received_qty', 'label' => 'Received Qty'],
                    ['index' => 'returned_qty', 'label' => 'Returned Qty'],
                    ['index' => 'available_to_return', 'label' => 'Available to Return'],
                    ['index' => 'status', 'label' => 'Status'],
                    ['index' => 'action', 'label' => 'Action'],
                ],
        ]);
    }
}
