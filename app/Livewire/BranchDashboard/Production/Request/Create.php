<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Models\Department;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Product;
use App\Models\ProductionRequest;
use App\Models\Recipe;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app.branch-dashboard')]
class Create extends Component
{
    #[Url(keep: true)]
    public $b_id;

    public $selectedProducts = [];
    public $currentShift = null;
    public $notes = '';

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

    public function addProduct()
    {
        $this->selectedProducts[] = [
            'product_id' => null,
            'quantity' => 1,
            'product_details' => null,
            'recipe_id' => null,
        ];
    }

    public function removeProduct($index)
    {
        unset($this->selectedProducts[$index]);
        $this->selectedProducts = array_values($this->selectedProducts);
    }

    /**
     * Load product and recipe details when product is selected
     */
    public function updatedSelectedProducts($value, $key)
    {
        // Extract index from key (e.g., "0.product_id" -> 0)
        if (str_contains($key, '.product_id')) {
            $index = explode('.', $key)[0];
            $productId = $this->selectedProducts[$index]['product_id'];

            if ($productId) {
                $product = Product::find($productId);

                // Find recipe for this product
                $recipe = Recipe::with('ingredients.item')
                    ->where('product_name', $product->name)
                    ->where('status', 'active')
                    ->first();

                if ($recipe) {
                    $this->selectedProducts[$index]['recipe_id'] = $recipe->id;
                    $this->selectedProducts[$index]['product_details'] = [
                        'name' => $product->name,
                        'recipe_name' => $recipe->product_name,
                        'yield_quantity' => $recipe->yield_quantity,
                        'uom' => $recipe->uom,
                        'ingredients' => $recipe->ingredients->map(function ($ing) {
                            return [
                                'item_id' => $ing->item_id,
                                'item_name' => $ing->item->name ?? 'N/A',
                                'quantity_per_batch' => $ing->quantity,
                                'uom' => $ing->uom,
                                'waste_percentage' => $ing->waste_percentage,
                            ];
                        })->toArray(),
                    ];
                } else {
                    $this->selectedProducts[$index]['recipe_id'] = null;
                    $this->selectedProducts[$index]['product_details'] = null;
                }
            }
        }
    }

    public function save()
    {
        $this->validate([
            'selectedProducts' => 'required|array|min:1',
            'selectedProducts.*.product_id' => 'required|exists:products,id',
            'selectedProducts.*.quantity' => 'required|numeric|min:1',
        ], [
            'selectedProducts.required' => 'Please add at least one product to request.',
            'selectedProducts.*.product_id.required' => 'Please select a product.',
            'selectedProducts.*.quantity.required' => 'Please enter quantity.',
        ]);

        $employee = Auth::guard('employees')->user();
        $branchId = $this->getBranchId();
        $departmentId = $employee->department_id;

        DB::transaction(function () use ($employee, $branchId, $departmentId) {
            // Get or create shift for today
            $shift = Shift::firstOrCreate([
                'branch_id' => $branchId,
                'department_id' => $departmentId,
                'shift_date' => today(),
                'shift_type' => $this->currentShift,
            ], [
                'employee_id' => $employee->id,
                'shift_number' => Shift::where('shift_date', today())->count() + 1,
                'status' => 'active',
            ]);

            // Create Item Request
            $department = Department::find($departmentId);
            $deptCode = strtoupper(substr($department->name ?? 'DEPT', 0, 4));
            $requestNumber = ItemRequest::generateRequestNumber(
                substr($branchId, 0, 8),
                $deptCode
            );

            $itemRequest = ItemRequest::create([
                'branch_id' => $branchId,
                'department_id' => $departmentId,
                'requested_by' => $employee->id,
                'request_number' => $requestNumber,
                'request_date' => today(),
                'shift' => $this->currentShift,
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            // Process each selected product
            foreach ($this->selectedProducts as $selectedProduct) {
                // Get the recipe for this product
                $recipe = Recipe::with('ingredients')->find($selectedProduct['recipe_id']);

                if (!$recipe) {
                    continue; // Skip if no recipe found
                }

                $quantity = (float) $selectedProduct['quantity'];

                // Create Production Request
                ProductionRequest::create([
                    'shift_id' => $shift->id,
                    'item_request_id' => $itemRequest->id,
                    'recipe_id' => $recipe->id,
                    'planned_production_quantity' => $quantity,
                ]);

                // Create Item Request Details for each ingredient
                foreach ($recipe->ingredients as $ingredient) {
                    $actualQuantity = $ingredient->getActualQuantityNeeded();
                    $totalQuantity = $actualQuantity * $quantity;

                    ItemRequestDetail::create([
                        'request_id' => $itemRequest->id,
                        'item_id' => $ingredient->item_id,
                        'quantity_requested' => $totalQuantity,
                        'quantity_approved' => 0,
                        'quantity_dispatched' => 0,
                        'uom' => $ingredient->uom,
                        'notes' => "For {$recipe->product_name} production (Qty: {$quantity})",
                    ]);
                }
            }
        });

        $this->toast()->success('Production request created successfully!')->send();
        return $this->redirect(branch_route('branch-dashboard.production.request.index'), navigate: true);
    }

    public function render()
    {
        $employee = Auth::guard('employees')->user();
        $branchId = $this->getBranchId();
        $departmentId = $employee->department_id;

        // Get products that have recipes in the department
        $products = Product::where('is_active', 1)
            ->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $branchId);
            })
            ->whereHas('productType', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.branch-dashboard.production.request.create', [
            'products' => $products,
        ]);
    }
}
