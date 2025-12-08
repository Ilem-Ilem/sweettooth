<?php

namespace App\Livewire\BranchDashboard\Production\Recipes;

use App\Models\Department;
use App\Models\Item;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Services\ProductionAuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
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

    #[Url(keep:true)]
    public ?string $dept_slug = null;

    public ?Department $department = null;

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
                // Department is already set from mount, no need to override
                // Don't auto-set product_type, let user select from dropdown
            }
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

        $message = '';
        // Get actor before transaction for audit logging
        $actor = current_actor();
        
        DB::transaction(function () use (&$message, $actor) {
            $branchId = $this->getBranchId();

            // Calculate total cost from ingredients
            $totalCost = collect($this->ingredients)->sum(function ($ing) {
                $quantity = (float) $ing['quantity'];
                $costPerUnit = (float) $ing['cost_per_unit'];
                $wastePercent = (float) ($ing['waste_percentage'] ?? 0);

                $actualQuantity = $quantity * (1 + ($wastePercent / 100));
                return $actualQuantity * $costPerUnit;
            });

            $costPerUnit = $totalCost / max((float) $this->yield_quantity, 1);

            $data = [
                'branch_id' => $branchId,
                'product_id' => $this->product_id,
                'product_name' => $this->productName,
                'sku' => strtoupper($this->sku),
                'department_id' => $this->department_id,
                'product_type' => $this->product_type,
                'cost_per_unit' => $costPerUnit,
                'uom' => $this->uom,
                'yield_quantity' => $this->yield_quantity,
                'preparation_time' => $this->preparation_time,
                'instructions' => !empty($this->instructions) ? json_encode(array_values($this->instructions)) : null,
                'status' => $this->status,
                // Store actor info using polymorphic pattern
                'created_by_id' => $actor->id,
                'created_by_type' => get_class($actor),
            ];

            $recipe = Recipe::create($data);
            $message = 'Recipe created successfully!';

            // Save ingredients
            foreach ($this->ingredients as $index => $ingredient) {
                if (!empty($ingredient['item_id'])) {
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'item_id' => $ingredient['item_id'],
                        'quantity' => $ingredient['quantity'],
                        'uom' => $ingredient['uom'],
                        'cost_per_unit' => $ingredient['cost_per_unit'] ?? 0,
                        'waste_percentage' => $ingredient['waste_percentage'] ?? 0,
                        'sort_order' => $index + 1,
                        'notes' => $ingredient['notes'] ?? null,
                        'preparation_notes' => $ingredient['preparation_notes'] ?? null,
                    ]);
                }
            }

            // Log recipe creation to audit trail using actor pattern
            ProductionAuditService::logRecipeCreated(
                $actor,
                $recipe,
                $this->ingredients
            );
        });

        $this->toast()->success($message ?? 'Recipe saved successfully!')->send();
        return $this->redirect(branch_route('branch-dashboard.production.recipes.index', ['deptSlug' => $this->dept_slug]), navigate: true);
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
