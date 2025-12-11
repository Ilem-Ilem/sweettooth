<?php

namespace App\Livewire\BranchDashboard\Production\KitchenModule;

use App\Livewire\BaseComponent;
use App\Models\DailyProduce;
use App\Models\ProductStock;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class StockMonitor extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = 'not_sent'; // 'not_sent', 'partially_sent', 'sent', 'all'
    public $monitorDate;
    public $selectedShiftId = null;
    public $currentShift = null;

    // Send out modal
    public $showSendOutModal = false;
    public $sendingProduceId = null;
    public $sendOutQuantity = 0;
    public $sendOutNotes = '';
    public $maxSendableQuantity = 0;

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'produced', 'label' => 'Produced', 'collapsible' => false],
        ['index' => 'available', 'label' => 'Available in Production', 'collapsible' => false],
        ['index' => 'sent_out', 'label' => 'Sent to Sales', 'collapsible' => true],
        ['index' => 'orders', 'label' => 'Via Orders', 'collapsible' => true],
        ['index' => 'callbacks', 'label' => 'Callbacks', 'collapsible' => true],
        ['index' => 'production_date', 'label' => 'Production Date', 'collapsible' => true],
        ['index' => 'actions', 'label' => 'Actions'],
    ];

    protected function getModelClass(): string
    {
        return DailyProduce::class;
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
        $this->monitorDate = Carbon::today()->format('Y-m-d');
        $this->loadCurrentShift();
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = auth('employees')->user();

        // Get today's shift for the employee's department
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->where('shift_date', $this->monitorDate)
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
        } else {
            // Try to get the most recent shift
            $this->currentShift = Shift::where('branch_id', $branchId)
                ->where('department_id', $employee->department_id)
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type', 'desc')
                ->first();

            if ($this->currentShift) {
                $this->selectedShiftId = $this->currentShift->id;
                $this->monitorDate = $this->currentShift->shift_date->format('Y-m-d');
            }
        }
    }

    public function updatedMonitorDate()
    {
        $this->loadCurrentShift();
        $this->resetPage();
    }

    public function getRowsProperty()
    {
        $query = $this->getFilteredQuery();

        // Search filter
        if ($this->search) {
            $query->whereHas('recipe', function ($q) {
                $q->where('product_name', 'like', '%' . $this->search . '%');
            });
        }

        $produces = $query->paginate($this->quantity);

        // Transform for display
        return $produces->through(function ($produce) {
            $availableQty = $produce->produced_quantity - $produce->sent_out_quantity - $produce->callback_quantity;

            return (object) [
                'id' => $produce->id,
                'recipe_id' => $produce->recipe_id,
                'product_name' => $produce->recipe->product_name ?? 'N/A',
                'uom' => $produce->recipe->unitOfMeasure?->symbol ?? '',
                'produced_quantity' => (float) $produce->produced_quantity,
                'available_quantity' => $availableQty,
                'sent_out_quantity' => (float) $produce->sent_out_quantity,
                'order_quantity' => (float) $produce->order_quantity,
                'callback_quantity' => (float) $produce->callback_quantity,
                'produce_date' => $produce->produce_date,
                'shift_type' => $produce->shift_type,
                'status' => $this->getProduceStatus($produce),
            ];
        });
    }

    protected function getFilteredQuery()
    {
        if (!$this->selectedShiftId) {
            return DailyProduce::query()->whereRaw('1=0'); // Return empty
        }

        $query = DailyProduce::with(['recipe', 'shift'])
            ->where('shift_id', $this->selectedShiftId)
            ->where('produced_quantity', '>', 0); // Only show products that were produced

        // Status filter
        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $query->where(function ($q) {
                if ($this->filterStatus === 'not_sent') {
                    // Products not sent to sales yet
                    $q->whereRaw('sent_out_quantity = 0')
                      ->whereRaw('produced_quantity > callback_quantity');
                } elseif ($this->filterStatus === 'partially_sent') {
                    // Products partially sent
                    $q->whereRaw('sent_out_quantity > 0')
                      ->whereRaw('sent_out_quantity < (produced_quantity - callback_quantity)');
                } elseif ($this->filterStatus === 'sent') {
                    // Products fully sent to sales
                    $q->whereRaw('sent_out_quantity >= (produced_quantity - callback_quantity)');
                }
            });
        }

        return $query;
    }

    private function getProduceStatus($produce)
    {
        $availableQty = $produce->produced_quantity - $produce->sent_out_quantity - $produce->callback_quantity;

        if ($produce->sent_out_quantity == 0) {
            return 'not_sent';
        } elseif ($availableQty > 0) {
            return 'partially_sent';
        } else {
            return 'sent';
        }
    }

    public function openSendOutModal($produceId)
    {
        $produce = DailyProduce::find($produceId);

        if (!$produce) {
            $this->toast()->error('Production record not found.')->send();
            return;
        }

        $this->sendingProduceId = $produceId;
        $this->maxSendableQuantity = $produce->produced_quantity - $produce->sent_out_quantity - $produce->callback_quantity;
        $this->sendOutQuantity = $this->maxSendableQuantity;
        $this->sendOutNotes = '';
        $this->showSendOutModal = true;
    }

    public function closeSendOutModal()
    {
        $this->showSendOutModal = false;
        $this->sendingProduceId = null;
        $this->sendOutQuantity = 0;
        $this->sendOutNotes = '';
    }

    public function sendToSales()
    {
        $this->validate([
            'sendOutQuantity' => 'required|numeric|min:0.01|max:' . $this->maxSendableQuantity,
        ], [
            'sendOutQuantity.required' => 'Please enter a quantity to send out.',
            'sendOutQuantity.min' => 'Quantity must be greater than 0.',
            'sendOutQuantity.max' => 'Cannot send more than available quantity (' . $this->maxSendableQuantity . ').',
        ]);

        try {
            DB::transaction(function () {
                $produce = DailyProduce::find($this->sendingProduceId);

                if (!$produce) {
                    throw new \Exception('Production record not found.');
                }

                // Update sent_out_quantity
                $produce->sent_out_quantity += (float) $this->sendOutQuantity;
                $produce->updateCalculations();

                // TODO: Optionally create ProductStock entry for sales area
                // This would track the product in the sales/front area
                // For now, we're just tracking that it was sent out

                $this->toast()->success('Successfully sent ' . $this->sendOutQuantity . ' ' . $produce->recipe->unitOfMeasure?->symbol . ' to sales!')->send();
            });

            $this->closeSendOutModal();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->toast()->error('Error: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = auth('employees')->user();

        // Get available shifts for this department
        $availableShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type')
            ->limit(30)
            ->get();

        return view('livewire.branch-dashboard.production.kitchen-module.stock-monitor', [
            'rows' => $this->rows,
            'availableShifts' => $availableShifts,
        ]);
    }
}
