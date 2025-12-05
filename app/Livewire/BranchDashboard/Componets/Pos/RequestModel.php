<?php

namespace App\Livewire\BranchDashboard\Componets\Pos;

use App\Models\Department;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Product;
use App\Models\ProductionRequest;
use App\Models\ProductStock;
use App\Models\Recipe;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class RequestModel extends Component
{
    #[Url(keep: true)]
    public ?string $b_id = null;

    public array $requestedItems = []; // ['product_id' => quantity]
    public $currentShift = null;

    public function mount()
    {
        $this->determineCurrentShift();
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

    public function getProductsProperty(): Collection
    {
        $q = Product::query();

        // if (strlen($this->search)) {
        //     $q->where('name', 'like', '%' . $this->search . '%');
        // }
        return $q->limit(50)->get();
    }

    public function addToRequestedItems($id, $quantity = 10)
    {
        if (!isset($this->requestedItems[$id])) {
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
            $this->requestedItems[$id] = max(1, (int)$quantity); // Minimum 1 batch
        }
    }

    public function requestItems()
    {
        if (empty($this->requestedItems)) {
            session()->flash('error', 'Please select at least one product to request.');
            return;
        }

        $employee = Auth::guard('employees')->user();
        $branchId = $this->getBranchId();
        $departmentId = $employee->department_id;

        try {
            DB::transaction(function () use ($employee, $branchId, $departmentId) {
                // Get or create shift for today
                $shift = Shift::firstOrCreate([
                    'branch_id'     => $branchId,
                    'department_id' => $departmentId,
                    'shift_date'    => today(),
                    'shift_type'    => $this->currentShift,
                ], [
                    'employee_id'  => $employee->id,
                    'shift_number' => Shift::where('shift_date', today())->count() + 1,
                    'status'       => 'active',
                ]);

                // Create Item Request
                $department = Department::find($departmentId);
                $deptCode = strtoupper(substr($department->name ?? 'DEPT', 0, 4));
                $requestNumber = ItemRequest::generateRequestNumber(
                    substr($branchId, 0, 8),
                    $deptCode
                );

                $itemRequest = ItemRequest::create([
                    'branch_id'      => $branchId,
                    'department_id'  => $departmentId,
                    'requested_by'   => $employee->id,
                    'request_number' => $requestNumber,
                    'request_date'   => today(),
                    'shift'          => $this->currentShift,
                    'status'         => 'pending',
                    'notes'          => 'Request from POS - Kitchen production needed',
                ]);

                // Process each requested product with its quantity
                foreach ($this->requestedItems as $productId => $quantity) {
                    $product = Product::find($productId);

                    if (!$product) {
                        continue;
                    }

                    // Find recipe for this product
                    $recipe = Recipe::with('ingredients')->where('product_name', $product->name)
                        ->where('status', 'active')
                        ->first();

                    if (!$recipe) {
                        continue; // Skip if no recipe found
                    }

                    // Use the quantity specified by the user (batches)
                    $batchesRequested = (int)$quantity;
                    $recipeYield = (float) $recipe->yield_quantity; // Units per batch
                    $actualUnitsRequested = $batchesRequested * $recipeYield; // Total units to produce

                    // Create Production Request (store actual units, not batches)
                    ProductionRequest::create([
                        'shift_id'                    => $shift->id,
                        'item_request_id'             => $itemRequest->id,
                        'recipe_id'                   => $recipe->id,
                        'planned_production_quantity' => $actualUnitsRequested, // Actual units (batches × yield)
                    ]);

                    // Create Item Request Details for each ingredient
                    foreach ($recipe->ingredients as $ingredient) {
                        $actualQuantity = $ingredient->getActualQuantityNeeded();
                        $totalQuantity = $actualQuantity * $batchesRequested; // Ingredients based on batches

                        ItemRequestDetail::create([
                            'request_id'          => $itemRequest->id,
                            'item_id'             => $ingredient->item_id,
                            'quantity_requested'  => $totalQuantity,
                            'quantity_approved'   => 0,
                            'quantity_dispatched' => 0,
                            'uom'                 => $ingredient->uom,
                            'notes'               => "For {$recipe->product_name} production ({$batchesRequested} batches × {$recipeYield} {$recipe->uom} = {$actualUnitsRequested} {$recipe->uom}) - POS Request",
                        ]);
                    }
                }
            });

            session()->flash('success', 'Kitchen production request sent successfully!');
            $this->requestedItems = [];
            $this->dispatch('kitchen-request-sent');
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending request: ' . $e->getMessage());
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

    public function render()
    {
        return view('livewire.branch-dashboard.componets.pos.request-model');
    }
}
