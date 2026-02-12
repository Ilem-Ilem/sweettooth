<?php

namespace App\Livewire\BranchDashboard\Componets\Pos;

use App\Events\ProductionRequest\RequestCreated;
use App\Helpers\Settings;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductionRequest;
use App\Models\Recipe;
use App\Services\CurrencyFormattingService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class RequestModel extends Component
{
    use Interactions;
    #[Url(keep: true)]
    public ?string $b_id = null;

    public bool $showModal = false;

    public array $requestedItems = []; // ['product_id' => quantity]
    public $selectedDepartment = null;
    public $priority = 'normal';
    public $notes = '';

    public $currentShift = null;

    public function mount()
    {
        \Log::info('RequestModel mounted');

        $this->determineCurrentShift();

        // Set default to first available production department
        $departments = Department::whereHas('category', function($query) {
                $query->where('name', 'Production');
            })
            ->get();

        \Log::info('Available departments', ['count' => $departments->count()]);

        if ($departments->count() > 0) {
            $this->selectedDepartment = $departments->first()->id;
            \Log::info('Selected department set', ['department_id' => $this->selectedDepartment]);
        }
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    /**
     * Determine current shift based on time
     */
    public function determineCurrentShift()
    {
        $currentHour = now()->format('H');

        // Morning shift: 6:00 AM - 2:00 PM (06:00 - 14:00)
        // Afternoon shift: 2:00 PM - 10:00 PM (14:00 - 22:00)
        if ($currentHour >= 6 && $currentHour < 14) {
            $this->currentShift = 'morning';
        } elseif ($currentHour >= 14 && $currentHour < 22) {
            $this->currentShift = 'afternoon';
        } else {
            $this->currentShift = 'morning'; // Default to morning for night hours
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    protected $listeners = ['openKitchenRequestModal' => 'openModal'];

    public function getProductsProperty(): Collection
    {
        $q = Product::query();

        if ($this->selectedDepartment) {
            // Filter products by the selected production department using the product_type relationship
            $q->whereHas('productType', function($query) {
                $query->where('department_id', $this->selectedDepartment);
            });
        }

        return $q->active()->available()->limit(50)->get();
    }

    public function getAvailableDepartmentsProperty(): Collection
    {
        return Department::whereHas('category', function($query) {
                $query->where('name', 'Production');
            })
            ->get();
    }

    public function addToRequestedItems($id, $quantity = 10)
    {
        if (! isset($this->requestedItems[$id])) {
            $this->requestedItems[$id] = $quantity;
        }
    }

    public function removeFromRequestedItems($id)
    {
        unset($this->requestedItems[$id]);
    }

    public function updateQuantity($id, $quantity)
    {
        if (isset($this->requestedItems[$id])) {
            $this->requestedItems[$id] = max(1, (int) $quantity); // Minimum 1 batch
        }
    }



    public function requestItems()
    {
        // Basic validation before processing
        if (empty($this->requestedItems)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Please select at least one product to request.'
            ]);
            return;
        }

        if (!$this->selectedDepartment) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Please select a production department.'
            ]);
            return;
        }

        $this->validate([
            'selectedDepartment' => 'required|exists:departments,id',
            'requestedItems' => 'required|array|min:1',
            'priority' => 'required|in:normal,urgent',
        ]);

        $employee = Auth::user();

        try {
            // Get current active shift for the employee (nullable for POS requests)
            $currentShift = \App\Models\Shift::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->whereDate('shift_date', today())
                ->first();

            // Determine sales department - use employee's department or default to first sales department
            $salesDepartmentId = $employee->department_id;
            if (!$salesDepartmentId) {
                $salesDepartment = Department::whereHas('category', function($query) {
                    $query->where('name', 'Sales');
                })->first();
                $salesDepartmentId = $salesDepartment?->id;
            }

            DB::transaction(function () use ($employee, $currentShift, $salesDepartmentId) {
                foreach ($this->requestedItems as $productId => $quantity) {
                    $product = Product::find($productId);
                    if (!$product) {
                        continue;
                    }

                    $recipe = Recipe::where('product_id', $productId)
                        ->where('department_id', $this->selectedDepartment)
                        ->first();

                    $noteParts = [];
                    $noteParts[] = "POS request for {$product->name} ({$product->sku})";
                    $noteParts[] = "Qty: {$quantity}";
                    if (!empty($this->notes)) {
                        $noteParts[] = $this->notes;
                    }

                    $requestedUnits = (float) $quantity;
                    $plannedQuantity = $requestedUnits;
                    $plannedBatches = null;
                    if ($recipe && (float) $recipe->yield_quantity > 0) {
                        $plannedBatches = (int) ceil($requestedUnits / (float) $recipe->yield_quantity);
                        $plannedQuantity = $plannedBatches * (float) $recipe->yield_quantity;
                        $noteParts[] = "Planned: {$plannedBatches} batch(es) = {$plannedQuantity} units";
                    }

                    $productionRequest = ProductionRequest::create([
                        'shift_id' => $currentShift?->id,
                        'sales_department_id' => $salesDepartmentId,
                        'production_department_id' => $this->selectedDepartment,
                        'recipe_id' => $recipe?->id,
                        'status' => 'pending',
                        'priority' => $this->priority,
                        'created_by_id' => $employee->id,
                        'notes' => implode(' | ', $noteParts),
                        'planned_production_quantity' => $plannedQuantity,
                        'requested_units' => $requestedUnits,
                    ]);

                    // Broadcast each request creation
                    broadcast(new RequestCreated($productionRequest))->toOthers();
                }
            });

            $this->toast()->success('Production request sent successfully!')->send();

            // Reset form
            $this->requestedItems = [];
            $this->notes = '';
            $this->priority = 'normal';
            $this->showModal = false;

            $this->dispatch('production-request-created');
        } catch (\Exception $e) {
            \Log::error('Request creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error sending request: ' . $e->getMessage()
            ]);
        }
    }

    protected function getTodayStockForProduct(string $productId, bool $forUpdate = false): ?ProductStock
    {
        $q = ProductStock::query()
            ->whereDate('stock_date', Carbon::today())
            ->where('product_id', $productId);
        if ($forUpdate) {
            $q->lockForUpdate();
        }

        return $q->first();
    }

    protected function availableQuantity(?ProductStock $stock): float
    {
        if (! $stock) {
            return 0.0;
        }
        // available is closing quantity; if not up to date, compute
        $stock->updateCalculatedFields();

        return max(0, (float) $stock->closing_quantity);
    }

    public function getCurrencySymbolProperty(): string
    {
        $currencyService = new CurrencyFormattingService();
        return $currencyService->getSymbol(Settings::currencyLocalization('primary_currency', 'NGN'));
    }

    public function render()
    {
        return view('livewire.branch-dashboard.componets.pos.request-model');
    }
}
