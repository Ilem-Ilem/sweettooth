<?php

namespace App\Livewire\BranchDashboard\Production\KitchenModule;

use App\Models\DailyProduce;
use App\Models\ProductionRequest;
use App\Models\Shift;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public $b_id;

    public $currentShift = null;
    public $todayShifts = [];
    public $selectedShiftId = null;

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

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

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get production requests for current shift
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
                    'uom' => $request->recipe->uom ?? 'pcs',
                    'status' => $status,
                    'status_color' => $request->getStatusBadgeColor(),
                    'item_request_number' => $request->itemRequest->request_number ?? 'N/A',
                ];
            });

            // Get items to collect from inventory
            $itemRequests = ItemRequest::with(['requestDetails.item'])
                ->where('shift_id', $this->currentShift->id)
                ->whereHas('productionRequest')
                ->get();

            foreach ($itemRequests as $itemRequest) {
                foreach ($itemRequest->requestDetails as $detail) {
                    $itemsToCollect[] = [
                        'item_name' => $detail->item->name ?? 'N/A',
                        'quantity_requested' => $detail->quantity_requested,
                        'quantity_approved' => $detail->quantity_approved,
                        'quantity_dispatched' => $detail->quantity_dispatched,
                        'uom' => $detail->uom ?? $detail->item->uom ?? '',
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
                        'uom' => $produce->recipe->uom ?? 'pcs',
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

        return view('livewire.branch-dashboard.production.kitchen-module.index', [
            'productionRequests' => $productionRequests,
            'itemsToCollect' => $itemsToCollect,
            'dailyProduces' => $dailyProduces,
            'shiftSummary' => $shiftSummary,
        ]);
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
