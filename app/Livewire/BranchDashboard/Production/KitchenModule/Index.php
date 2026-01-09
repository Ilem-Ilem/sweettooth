<?php

namespace App\Livewire\BranchDashboard\Production\KitchenModule;

use App\Models\DailyProduce;
use App\Models\ProductStock;
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
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    // Tab functionality
    public $activeTab = 'dashboard'; // 'dashboard', 'stock-monitor'

    // Dashboard properties
    public $currentShift = null;
    public $todayShifts = [];
    public $selectedShiftId = null;

    // Stock Monitor properties
    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = 'not_sent'; // 'not_sent', 'partially_sent', 'sent', 'all'
    public $monitorDate;
    public $monitorSelectedShiftId = null;
    public $monitorCurrentShift = null;

    // Send out modal for stock monitor
    public $showSendOutModal = false;
    public $sendingProduceId = null;
    public $sendOutQuantity = 0;
    public $sendOutNotes = '';
    public $maxSendableQuantity = 0;

    public function mount()
    {
        $this->loadCurrentShift();
        $this->monitorDate = Carbon::today()->format('Y-m-d');
        $this->loadMonitorCurrentShift();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('web')->user();

        // Get today's shift for the currently logged-in employee
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('employee_id', $employee->id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
        }

        // Get all today's shifts for this employee
        $this->todayShifts = Shift::where('branch_id', $branchId)
            ->where('employee_id', $employee->id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->get();
    }

    public function updatedSelectedShiftId()
    {
        $this->currentShift = Shift::find($this->selectedShiftId);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'stock-monitor') {
            $this->loadMonitorCurrentShift();
        }
        $this->resetPage();
    }

    // Stock Monitor Methods
    public function loadMonitorCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('web')->user();

        // Get today's shift for the employee's department
        $this->monitorCurrentShift = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->where('shift_date', $this->monitorDate)
            ->orderBy('shift_type')
            ->first();

        if ($this->monitorCurrentShift) {
            $this->monitorSelectedShiftId = $this->monitorCurrentShift->id;
        } else {
            // Try to get the most recent shift
            $this->monitorCurrentShift = Shift::where('branch_id', $branchId)
                ->where('department_id', $employee->department_id)
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type', 'desc')
                ->first();

            if ($this->monitorCurrentShift) {
                $this->monitorSelectedShiftId = $this->monitorCurrentShift->id;
                $this->monitorDate = $this->monitorCurrentShift->shift_date->format('Y-m-d');
            }
        }
    }

    public function updatedMonitorDate()
    {
        $this->loadMonitorCurrentShift();
        $this->resetPage();
    }

    public function getStockMonitorRowsProperty()
    {
        $query = $this->getStockMonitorFilteredQuery();

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

    protected function getStockMonitorFilteredQuery()
    {
        if (!$this->monitorSelectedShiftId) {
            return DailyProduce::query()->whereRaw('1=0'); // Return empty
        }

        $query = DailyProduce::with(['recipe', 'shift'])
            ->where('shift_id', $this->monitorSelectedShiftId)
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
        $employee = Auth::guard('web')->user();
        $deptSlug = $employee->department?->slug ?? 'kitchen';

        $data = ['deptSlug' => $deptSlug];

        if ($this->activeTab === 'dashboard') {
            // Dashboard data
            $productionRequests = [];
            $itemsToCollect = [];
            $dailyProduces = [];
            $shiftSummary = [
                'total_recipes' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'pending' => 0,
                'total_produced' => 0,
                'total_requested' => 0,
            ];

            if ($this->currentShift) {
                // Get production requests for this shift
                $productionRequests = ProductionRequest::with([
                    'recipe',
                    'shift',
                    'itemRequest.requestDetails.item'
                ])
                ->where('shift_id', $this->currentShift->id)
                ->get()
                ->map(function ($request) {
                    $status = $request->getComputedStatus();
                    return [
                        'id' => $request->id,
                        'recipe_name' => $request->recipe->product_name ?? 'N/A',
                        'planned_quantity' => $request->planned_production_quantity,
                        'uom' => $request->recipe->unitOfMeasure?->symbol ?? 'pcs',
                        'status' => $status,
                        'status_color' => $request->getStatusBadgeColor(),
                        'item_request_number' => $request->itemRequest->request_number ?? 'N/A',
                    ];
                });

                // Get items to collect from inventory
                $itemRequests = ItemRequest::with(['requestDetails.item'])
                    ->whereHas('productionRequests', function ($q) {
                        $q->where('shift_id', $this->currentShift->id);
                    })
                    ->get();

                foreach ($itemRequests as $itemRequest) {
                    foreach ($itemRequest->requestDetails as $detail) {
                        $itemsToCollect[] = [
                            'item_name' => $detail->item->name ?? 'N/A',
                            'quantity_requested' => $detail->quantity_requested,
                            'quantity_approved' => $detail->quantity_approved,
                            'quantity_dispatched' => $detail->quantity_dispatched,
                            'uom' => $detail->uom ?? $detail->item->unitOfMeasure?->symbol ?? '',
                            'status' => $this->getItemCollectionStatus($detail),
                        ];
                    }
                }

                // Get daily produces for current shift
                $dailyProduces = DailyProduce::with(['recipe', 'productionRecords'])
                    ->where('shift_id', $this->currentShift->id)
                    ->get()
                    ->map(function ($produce) {
                        $producability = $produce->calculateProducableQuantity();
                        return [
                            'id' => $produce->id,
                            'recipe_name' => $produce->recipe->product_name ?? 'N/A',
                            'requested_quantity' => $produce->requested_quantity,
                            'produced_quantity' => $produce->getTotalProducedFromRecords(),
                            'producable_quantity' => $producability['producable_quantity'],
                            'can_produce' => $producability['can_produce_full_batch'],
                            'uom' => $produce->recipe->unitOfMeasure?->symbol ?? 'pcs',
                            'status' => $produce->status ?? 'in_progress',
                            'batches_count' => $produce->productionRecords->count(),
                            'progress_percentage' => $produce->requested_quantity > 0
                                ? min(100, ($produce->getTotalProducedFromRecords() / $produce->requested_quantity) * 100)
                                : 0,
                        ];
                    });

                // Calculate shift summary
                $shiftSummary = [
                    'total_recipes' => $dailyProduces->count(),
                    'completed' => $dailyProduces->where('status', 'completed')->count(),
                    'in_progress' => $dailyProduces->where('status', 'in_progress')->count(),
                    'pending' => $dailyProduces->where('status', 'pending')->count(),
                    'total_produced' => $dailyProduces->sum('produced_quantity'),
                    'total_requested' => $dailyProduces->sum('requested_quantity'),
                ];
            }

            $data += [
                'productionRequests' => $productionRequests,
                'itemsToCollect' => $itemsToCollect,
                'dailyProduces' => $dailyProduces,
                'shiftSummary' => $shiftSummary,
            ];
        } elseif ($this->activeTab === 'stock-monitor') {
            // Stock monitor data
            $availableShifts = Shift::where('branch_id', $branchId)
                ->where('department_id', $employee->department_id)
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type')
                ->limit(30)
                ->get();

            $data += [
                'rows' => $this->stockMonitorRows,
                'availableShifts' => $availableShifts,
            ];
        }

        return view('livewire.branch-dashboard.production.kitchen-module.index', $data);
    }

    private function getItemCollectionStatus($detail)
    {
        $requested = (float) $detail->quantity_requested;
        $approved = (float) $detail->quantity_approved;
        $dispatched = (float) $detail->quantity_dispatched;

        if ($dispatched >= $approved && $approved > 0) {
            return [
                'label' => 'Collected',
                'color' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            ];
        } elseif ($dispatched > 0) {
            return [
                'label' => 'Partially Collected',
                'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
            ];
        } elseif ($approved > 0) {
            return [
                'label' => 'Ready to Collect',
                'color' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200'
            ];
        } elseif ($requested > 0) {
            return [
                'label' => 'Pending Approval',
                'color' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            ];
        }

        return [
            'label' => 'Unknown',
            'color' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200'
        ];
    }
}
