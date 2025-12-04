<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Dispatches;

use App\Livewire\BaseComponent;
use App\Models\ProductDispatch;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    #[Url(keep: true)]
    public ?string $salesDeptSlug = null;

    public ?string $branchId = null;
    public ?int $departmentId = null;
    public string $departmentName = 'Dispatch Receiving';
    public string $branchName = '';

    // Filter options
    public ?string $search = null;
    public ?string $filterStatus = 'dispatched'; // Default to pending dispatches
    public ?string $filterDate = null;

    // Receiving modal data
    public $selectedDispatch = null;
    public $receivedQuantity = null;
    public $receivingNotes = '';
    public $showReceivingModal = false;

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'dispatched_quantity', 'label' => 'Dispatched Qty'],
        ['index' => 'production_date', 'label' => 'Production Date'],
        ['index' => 'dispatched_by', 'label' => 'Dispatched By'],
        ['index' => 'dispatch_time', 'label' => 'Dispatch Time'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'variance', 'label' => 'Variance', 'collapsible' => true],
        ['index' => 'actions', 'label' => 'Actions'],
    ];

    protected function getModelClass(): string
    {
        return ProductDispatch::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->mountBase();
        $this->loadBranchAndDepartment();
        $this->filterDate = Carbon::today()->format('Y-m-d');
    }

    protected function loadBranchAndDepartment(): void
    {
        // Load branch
        $this->branchId = request('b_id');
        if ($this->branchId) {
            $branch = Branch::find($this->branchId);
            $this->branchName = $branch?->name ?? 'Unknown Branch';
        }

        // Load department from slug
        if ($this->salesDeptSlug) {
            // First try to find branch-specific department
            $department = Department::where('slug', $this->salesDeptSlug)
                ->where('branch_id', $this->branchId)
                ->first();

            // If not found, try to find global department (branch_id is null)
            if (!$department) {
                $department = Department::where('slug', $this->salesDeptSlug)
                    ->whereNull('branch_id')
                    ->first();
            }

            if ($department) {
                $this->departmentId = $department->id;
                $this->departmentName = $department->name . ' - Dispatch Receiving';
            } else {
                $this->toast()->error('Department not found.')->send();
            }
        } else {
            // If no department slug provided, use employee's department
            $employee = auth('employees')->user();
            if ($employee && $employee->department_id) {
                $this->departmentId = $employee->department_id;
                $department = Department::find($employee->department_id);
                $this->departmentName = ($department?->name ?? 'Dispatch Receiving') . ' - Dispatch Receiving';
            }
        }

        // Validate branch access
        if (!$this->branchId) {
            $this->toast()->error('Branch not specified.')->send();
        }
    }

    /**
     * Get filtered query for dispatches
     */
    protected function getFilteredQuery()
    {
        return ProductDispatch::query()
            ->where('branch_id', $this->branchId)
            ->where('sales_department_id', $this->departmentId)
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('dispatch_date', $this->filterDate);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['product', 'dispatchedBy', 'receivedBy', 'dailyProduce'])
            ->orderBy('dispatch_time', 'desc');
    }

    /**
     * Open receiving modal for a dispatch
     */
    public function openReceivingModal($dispatchId)
    {
        $this->selectedDispatch = ProductDispatch::with('product')->find($dispatchId);

        if (!$this->selectedDispatch) {
            $this->toast()->error('Dispatch not found.')->send();
            return;
        }

        if ($this->selectedDispatch->status !== 'dispatched') {
            $this->toast()->warning('This dispatch has already been processed.')->send();
            return;
        }

        // Pre-fill with dispatched quantity
        $this->receivedQuantity = $this->selectedDispatch->quantity;
        $this->receivingNotes = '';
        $this->showReceivingModal = true;
    }

    /**
     * Receive dispatch and update stock
     */
    public function receiveDispatch()
    {
        // Validate
        $this->validate([
            'receivedQuantity' => 'required|numeric|min:0',
            'receivingNotes' => 'nullable|string|max:500',
        ]);

        if (!$this->selectedDispatch) {
            $this->toast()->error('No dispatch selected.')->send();
            return;
        }

        DB::beginTransaction();
        try {
            $employee = auth('employees')->user();
            $dispatch = $this->selectedDispatch;

            // Update dispatch status
            $dispatch->update([
                'status' => 'received',
                'received_quantity' => $this->receivedQuantity,
                'received_by' => $employee->id,
                'received_at' => now(),
                'notes' => $this->receivingNotes,
            ]);

            // Get current active shift for the sales department
            $currentShift = Shift::where('employee_id', $employee->id)
                ->where('shift_date', Carbon::today())
                ->where('status', 'active')
                ->first();

            // Get production date from the daily produce or use today
            $productionDate = $dispatch->dailyProduce?->production_date ?? Carbon::today();

            // Calculate expiry date based on product shelf life
            $expiryDate = null;
            if ($dispatch->product && $dispatch->product->shelf_life_days > 0) {
                $expiryDate = Carbon::parse($productionDate)->addDays($dispatch->product->shelf_life_days);
            }

            // Update or create ProductStock record
            $stockDate = Carbon::parse($dispatch->dispatch_date);
            $shiftType = $dispatch->shift_type ?? 'morning';

            $productStock = ProductStock::where('product_id', $dispatch->product_id)
                ->where('stock_date', $stockDate->format('Y-m-d'))
                ->where('shift_type', $shiftType)
                ->first();

            if ($productStock) {
                // Update existing stock - add to addition_quantity
                $productStock->addition_quantity = ($productStock->addition_quantity ?? 0) + $this->receivedQuantity;
                $productStock->production_date = $productionDate;
                $productStock->expiry_date = $expiryDate;
                $productStock->save();
            } else {
                // Create new stock record
                ProductStock::create([
                    'sales_shift_id' => $currentShift?->id,
                    'product_id' => $dispatch->product_id,
                    'stock_date' => $stockDate->format('Y-m-d'),
                    'shift_type' => $shiftType,
                    'opening_quantity' => 0,
                    'addition_quantity' => $this->receivedQuantity,
                    'production_date' => $productionDate,
                    'expiry_date' => $expiryDate,
                    'callback_quantity' => 0,
                    'redress_quantity' => 0,
                    'total_available' => $this->receivedQuantity,
                    'transfer_quantity' => 0,
                    'glovo_quantity' => 0,
                    'quantity_sold' => 0,
                    'closing_quantity' => $this->receivedQuantity,
                    'notes' => 'Received from kitchen dispatch',
                ]);
            }

            DB::commit();

            // Check for variance
            $variance = $this->receivedQuantity - $dispatch->quantity;
            if ($variance != 0) {
                $varianceMessage = $variance > 0
                    ? "Received {$variance} more than dispatched"
                    : "Received " . abs($variance) . " less than dispatched";
                $this->toast()->warning("Dispatch received with variance: {$varianceMessage}")->send();
            } else {
                $this->toast()->success('Dispatch received successfully!')->send();
            }

            // Reset form
            $this->closeReceivingModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error receiving dispatch: ' . $e->getMessage())->send();
        }
    }

    /**
     * Reject a dispatch
     */
    public function rejectDispatch($dispatchId, $reason = '')
    {
        DB::beginTransaction();
        try {
            $employee = auth('employees')->user();
            $dispatch = ProductDispatch::find($dispatchId);

            if (!$dispatch) {
                $this->toast()->error('Dispatch not found.')->send();
                return;
            }

            if ($dispatch->status !== 'dispatched') {
                $this->toast()->warning('This dispatch has already been processed.')->send();
                return;
            }

            $dispatch->update([
                'status' => 'rejected',
                'received_quantity' => 0,
                'received_by' => $employee->id,
                'received_at' => now(),
                'notes' => 'Rejected: ' . $reason,
            ]);

            DB::commit();
            $this->toast()->success('Dispatch rejected successfully.')->send();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error rejecting dispatch: ' . $e->getMessage())->send();
        }
    }

    /**
     * Close receiving modal
     */
    public function closeReceivingModal()
    {
        $this->showReceivingModal = false;
        $this->selectedDispatch = null;
        $this->receivedQuantity = null;
        $this->receivingNotes = '';
        $this->resetValidation();
    }

    /**
     * Update search filter
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Update status filter
     */
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Update date filter
     */
    public function updatedFilterDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $dispatches = $this->getFilteredQuery()->paginate(20);

        return view('livewire.branch-dashboard.sales-dashboard.dispatches.index', [
            'dispatches' => $dispatches,
        ]);
    }
}
