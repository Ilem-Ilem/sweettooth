<?php

namespace App\Livewire\BranchDashboard\Production\Recipes;

use App\Models\Department;
use App\Models\Item;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\ApprovalAuditRequest;
use App\Services\ProductionAuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;


#[Layout('components.layouts.app.branch-dashboard')]
class Add extends Component
{
    use Interactions;
    
    #[Url(keep: true)]
    public ?string $b_id = null;

    // Form fields
    public ?int $product_id = null;

    public string $productName = '';

    public string $sku = '';

    public ?int $department_id = null;

    public string $product_type = '';

    public $cost_per_unit = 0;

    public string $uom = 'grams';

    public $yield_quantity = 1;

    public ?int $preparation_time = null;

    public array $instructions = [];

    public string $status = 'active';

    // Recipe Ingredients
    public array $ingredients = [];

    // Additional fields for weight/volume sync
    public $recipe_yield_weight = null;
    public $unit_weight = null;
    public $yield_percentage = 100;

    #[Url(keep:true)]
    public ?string $dept_slug = null;

    public ?Department $department = null;

    // Audit modal for approval requests
    public bool $showAuditModal = false;
    public ?string $auditAction = null;          // create|edit|delete
    public string $auditReason = '';              // User-provided reason
    public ?int $pendingItemId = null;            // Recipe ID pending action
    public array $pendingItemData = [];           // Data to save on approval

    public function mount($deptSlug){
        $this->dept_slug = $deptSlug;
        $this->department = Department::where('slug', $deptSlug)->first();

        if (!$this->department) {
            abort(404, 'Department not found');
        }

        // Auto-set department_id based on dept_slug
        $this->department_id = $this->department->id;
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    private function generateSku()
    {
        $branchId = $this->getBranchId();
        $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $this->productName), 0, 4));
        $randomCode = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $this->sku = 'RCP-'.$branchId.'-'.$nameCode.'-'.$randomCode;
    }

    public function addIngredient()
    {
        $this->ingredients[] = [
            'item_id' => null,
            'quantity' => 0,
            'uom' => 'grams',
            'cost_per_unit' => 0,
            'waste_percentage' => 0,
            'notes' => '',
            'preparation_notes' => '',
        ];
    }

    public function updatedproductName(){
        if (!empty($this->productName)) {
            $this->generateSku();

            // Find product within the current department only
            $product = Product::with('productType')
                ->where('name', $this->productName)
                ->whereHas('productType', function ($q) {
                    $q->where('department_id', $this->department->id);
                })
                ->first();

            if ($product && $product->productType) {
                // Store product ID for reference
                $this->product_id = $product->id;
                // Don't auto-set product_type, let user select from dropdown
            }
        }
    }

    /**
     * When product_id is updated, populate recipe fields from product
     */
    public function updatedProductId()
    {
        if ($this->product_id) {
            $product = Product::find($this->product_id);
            if ($product) {
                $this->yield_quantity = $product->recipe_yield ?? 1;
                $this->recipe_yield_weight = $product->recipe_yield_weight;
                $this->unit_weight = $product->unit_weight;
                $this->uom = $product->uom ?? 'grams';
            }
        }
    }

    /**
     * Auto-calculate yield_quantity from batch weight and unit weight
     * Formula: yield_quantity = recipe_yield_weight / unit_weight
     */
    #[Computed]
    public function calculatedYieldQuantity()
    {
        if ($this->recipe_yield_weight && $this->unit_weight && $this->unit_weight > 0) {
            return round($this->recipe_yield_weight / $this->unit_weight, 2);
        }
        return null;
    }

    /**
     * Watch for changes in recipe_yield_weight and auto-update yield_quantity
     */
    public function updatedRecipeYieldWeight()
    {
        $calculated = $this->calculatedYieldQuantity();
        if ($calculated !== null) {
            $this->yield_quantity = $calculated;
        }
    }

    /**
     * Watch for changes in unit_weight and auto-update yield_quantity
     */
    public function updatedUnitWeight()
    {
        $calculated = $this->calculatedYieldQuantity();
        if ($calculated !== null) {
            $this->yield_quantity = $calculated;
        }
    }

    public function addInstruction()
    {
        $this->instructions[] = '';
    }

    public function removeInstruction($index)
    {
        unset($this->instructions[$index]);
        $this->instructions = array_values($this->instructions);
    }

    public function removeIngredient($index)
    {
        unset($this->ingredients[$index]);
        $this->ingredients = array_values($this->ingredients);
    }

    public function save()
    {
        $rules = [
            'productName' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:recipes,sku',
            'department_id' => 'required|exists:departments,id',
            'product_type' => 'required|string|max:255',
            'uom' => 'required|in:grams,kg,liters,ml,pcs,units',
            'yield_quantity' => 'required|numeric|min:0.01',
            'recipe_yield_weight' => 'nullable|numeric|min:0',
            'yield_percentage' => 'nullable|numeric|min:0|max:100',
            'preparation_time' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,testing',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.uom' => 'required|string',
            'ingredients.*.cost_per_unit' => 'required|numeric|min:0',
            'ingredients.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
        ];

        $this->validate($rules);

        // Validate consistency between yield_quantity and weights
        $this->validateYieldConsistency();

        // Calculate cost for preview
        $totalCost = collect($this->ingredients)->sum(function ($ing) {
            $quantity = (float) $ing['quantity'];
            $costPerUnit = (float) $ing['cost_per_unit'];
            $wastePercent = (float) ($ing['waste_percentage'] ?? 0);
            $actualQuantity = $quantity * (1 + ($wastePercent / 100));
            return $actualQuantity * $costPerUnit;
        });
        $costPerUnit = $totalCost / max((float) $this->yield_quantity, 1);

        $recipeData = [
            'product_id' => $this->product_id,
            'product_name' => $this->productName,
            'sku' => strtoupper($this->sku),
            'department_id' => $this->department_id,
            'product_type' => $this->product_type,
            'cost_per_unit' => $costPerUnit,
            'uom' => $this->uom,
            'yield_quantity' => $this->yield_quantity,
            'recipe_yield_weight' => $this->recipe_yield_weight,
            'yield_percentage' => $this->yield_percentage ?? 100,
            'preparation_time' => $this->preparation_time,
            'instructions' => !empty($this->instructions) ? json_encode(array_values($this->instructions)) : null,
            'status' => $this->status,
            'ingredients' => $this->ingredients,
        ];

        // Super admin bypass - save directly without audit
        if (is_super_admin()) {
            $this->saveRecipe($recipeData);
            return;
        }

        // Non-super-admin: show audit modal
        $this->auditAction = 'create_recipe';
        $this->pendingItemId = null;
        $this->pendingItemData = $recipeData;
        $this->showAuditModal = true;
    }

    private function saveRecipe(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $ingredients = $data['ingredients'];
                unset($data['ingredients']);

                $requester = current_actor();
                $data['created_by_id'] = $requester->id;
                $data['created_by_type'] = get_class($requester);
                $data['branch_id'] = $this->getBranchId();

                $recipe = Recipe::create($data);

                // Save ingredients
                foreach ($ingredients as $ingredient) {
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'item_id' => $ingredient['item_id'],
                        'quantity' => $ingredient['quantity'],
                        'uom' => $ingredient['uom'],
                        'cost_per_unit' => $ingredient['cost_per_unit'],
                        'waste_percentage' => $ingredient['waste_percentage'] ?? 0,
                        'notes' => $ingredient['notes'] ?? null,
                        'preparation_notes' => $ingredient['preparation_notes'] ?? null,
                    ]);
                }
            });

            $this->toast()->success('Recipe created successfully')->send();
            return $this->redirect(branch_route('branch-dashboard.production.recipes.index', ['deptSlug' => $this->dept_slug]), navigate: true);
        } catch (\Exception $e) {
            $this->toast()->error('Failed to create recipe: ' . $e->getMessage())->send();
        }
    }

    /**
     * Validate yield consistency between yield_quantity, recipe_yield_weight, and unit_weight
     * If both weights are provided, yield_quantity must equal recipe_yield_weight / unit_weight
     */
    private function validateYieldConsistency()
    {
        if ($this->recipe_yield_weight && $this->unit_weight && $this->unit_weight > 0) {
            $expectedYield = round($this->recipe_yield_weight / $this->unit_weight, 2);
            $tolerance = 0.01; // Allow small rounding differences

            if (abs((float)$this->yield_quantity - $expectedYield) > $tolerance) {
                $this->addError('yield_quantity', 
                    "Yield quantity ({$this->yield_quantity}) must equal batch weight ({$this->recipe_yield_weight}) ÷ unit weight ({$this->unit_weight}) = {$expectedYield}. Auto-calculated value is used when batch and unit weights are provided."
                );
            }
        }

        if ($this->recipe_yield_weight && !$this->unit_weight) {
            $this->addError('unit_weight', 
                'Unit weight is required when batch weight is provided for auto-calculation.'
            );
        }

        if (!$this->recipe_yield_weight && $this->unit_weight) {
            $this->addError('recipe_yield_weight', 
                'Batch weight is required when unit weight is provided for auto-calculation.'
            );
        }
    }

    public function submitAuditRequest()
    {
        $this->validate([
            'auditReason' => 'required|string|min:10|max:500',
        ]);

        $requester = current_actor();
        
        ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'recipe:' . $this->auditAction,
            'description' => $this->auditReason,
            'payload' => $this->pendingItemData,
            'status' => 'pending',
            'branch_id' => $this->getBranchId(),
        ]);

        $this->closeAuditModal();
        $this->toast()->success('Approval request submitted')->send();
        return $this->redirect(branch_route('branch-dashboard.production.recipes.index', ['deptSlug' => $this->dept_slug]), navigate: true);
    }

    private function closeAuditModal()
    {
        $this->showAuditModal = false;
        $this->auditReason = '';
        $this->auditAction = null;
        $this->pendingItemId = null;
        $this->pendingItemData = [];
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        // Filter products by department
        $products = Product::where('is_active', 1)
            ->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $branchId);
            })
            ->whereHas('productType', function ($q) {
                $q->where('department_id', $this->department->id);
            })
            ->get();

        $items = Item::orderBy('name')->get();


        return view('livewire.branch-dashboard.production.recipes.add', [
            'products' => $products,
            'items' => $items,
            'department' => $this->department,
            'dept_slug'=>$this->dept_slug,
        ]);
    }
}
