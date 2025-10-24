<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
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

    public ?string $currentShiftId = null;
    public string $shiftType = 'morning';
    public $stockDate;

    // Callback form
    public $showCallbackModal = false;
    public $selectedProductStock = null;
    public $callbackQuantity = 0;
    public $callbackReason = '';
    public $callbackNotes = '';

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'current_stock', 'label' => 'Current Stock', 'collapsible' => true],
        ['index' => 'production_date', 'label' => 'Production Date', 'collapsible' => true],
        ['index' => 'expiry_date', 'label' => 'Expiry Date', 'collapsible' => true],
        ['index' => 'shelf_life', 'label' => 'Shelf Life Status'],
        ['index' => 'callback_qty', 'label' => 'Callback Qty', 'collapsible' => true],
        ['index' => 'action', 'label' => 'Action'],
    ];

    protected function getModelClass(): string
    {
        return ProductStock::class;
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
        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadCurrentShift();
    }

    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        $activeShift = Shift::where('employee_id', $employee->id)
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
            $this->shiftType = $activeShift->shift_type ?? 'morning';
        }
    }

    public function getRowsProperty()
    {
        if (!$this->currentShiftId) {
            return [];
        }

        $query = ProductStock::with(['product', 'salesShift'])
            ->where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate);

        // Search filter
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where(function ($q) {
                foreach ($q->get() as $stock) {
                    if ($stock->getShelfLifeStatus() === $this->filterStatus) {
                        $q->orWhere('id', $stock->id);
                    }
                }
            });
        }

        return $query->paginate($this->quantity);
    }

    public function openCallbackModal($stockId)
    {
        $this->selectedProductStock = ProductStock::with('product')->find($stockId);

        if (!$this->selectedProductStock) {
            $this->toast()->error('Product stock not found.')->send();
            return;
        }

        // Auto-fill callback quantity for expired products
        if ($this->selectedProductStock->shouldCallback()) {
            $this->callbackQuantity = $this->selectedProductStock->total_available;
            $this->callbackReason = 'expired';
        } else {
            $this->callbackQuantity = 0;
            $this->callbackReason = '';
        }

        $this->callbackNotes = '';
        $this->showCallbackModal = true;
    }

    public function closeCallbackModal()
    {
        $this->showCallbackModal = false;
        $this->selectedProductStock = null;
        $this->callbackQuantity = 0;
        $this->callbackReason = '';
        $this->callbackNotes = '';
    }

    public function submitCallback()
    {
        $this->validate([
            'callbackQuantity' => 'required|numeric|min:0.01',
            'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,other',
        ], [
            'callbackQuantity.required' => 'Callback quantity is required',
            'callbackQuantity.min' => 'Callback quantity must be greater than 0',
            'callbackReason.required' => 'Please select a callback reason',
        ]);

        try {
            DB::beginTransaction();

            if (!$this->selectedProductStock) {
                throw new \Exception('Product stock not found');
            }

            // Validate callback quantity doesn't exceed available
            if ($this->callbackQuantity > $this->selectedProductStock->total_available) {
                $this->toast()->error('Callback quantity cannot exceed available stock.')->send();
                return;
            }

            // Update product stock
            $this->selectedProductStock->callback_quantity += $this->callbackQuantity;
            $this->selectedProductStock->updateCalculatedFields();
            $this->selectedProductStock->save();

            // Log callback in notes
            $logEntry = sprintf(
                "[%s] Callback: %.2f %s - Reason: %s%s",
                now()->format('Y-m-d H:i'),
                $this->callbackQuantity,
                $this->selectedProductStock->product->uom ?? 'units',
                ucfirst(str_replace('_', ' ', $this->callbackReason)),
                $this->callbackNotes ? " - Notes: {$this->callbackNotes}" : ''
            );

            $this->selectedProductStock->notes = $this->selectedProductStock->notes
                ? $this->selectedProductStock->notes . "\n" . $logEntry
                : $logEntry;

            $this->selectedProductStock->save();

            DB::commit();

            $this->toast()->success('Callback recorded successfully!')->send();
            $this->closeCallbackModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error recording callback: ' . $e->getMessage())->send();
        }
    }

    protected function getFilteredQuery()
    {
        return ProductStock::query()
            ->where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.callbacks.index', [
            'rows' => $this->rows,
        ]);
    }
}
