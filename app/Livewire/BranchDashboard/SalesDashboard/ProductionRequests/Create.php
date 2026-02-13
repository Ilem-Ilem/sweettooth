<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\ProductionRequests;

use App\Livewire\Concerns\SalesDepartmentContext;
use App\Models\Department;
use App\Models\Recipe;
use App\Models\SalesProductionRequest;
use App\Models\SalesProductionRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Create extends Component
{
    use Interactions;
    use SalesDepartmentContext;

    public $selectedProductionDepartmentId = null;
    public $selectedRecipeId = null;
    public $quantityRequested = 1;
    public string $priority = 'normal';
    public string $notes = '';
    public string $submitError = '';
    public string $submitSuccess = '';

    /** @var array<int, array{id:int,name:string}> */
    public array $productionDepartments = [];

    /** @var array<int, array{id:string,name:string,sku:?string}> */
    public array $availableRecipes = [];

    /** @var array<int, array{
     *     production_department_id:int,
     *     production_department_name:string,
     *     recipe_id:int,
     *     recipe_name:string,
     *     product_id:?string,
     *     product_name:?string,
     *     sku:?string,
     *     yield_quantity:float,
     *     uom:?string,
     *     quantity_requested:float
     * }>
     */
    public array $cartItems = [];

    public function mount(): void
    {
        $this->initializeDepartmentContext();
        $this->loadProductionDepartments();
    }

    public function updatedSelectedProductionDepartmentId(): void
    {
        $this->selectedRecipeId = null;
        $this->loadRecipesForDepartment();
    }

    public function loadProductionDepartments(): void
    {
        $branchId = $this->getBranchId();

        $this->productionDepartments = Department::query()
            ->where(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            })
            ->whereHas('category', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['production']);
            })
            ->orderBy('name')
            ->get()
            ->map(fn (Department $department) => [
                'id' => $department->id,
                'name' => $department->name,
            ])
            ->toArray();
    }

    public function loadRecipesForDepartment(): void
    {
        if (! $this->selectedProductionDepartmentId) {
            $this->availableRecipes = [];

            return;
        }

        $strictRecipes = Recipe::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereHas('productType', function ($subQuery) {
                    $subQuery->where('department_id', $this->selectedProductionDepartmentId);
                })->orWhereHas('product.productType', function ($subQuery) {
                    $subQuery->where('department_id', $this->selectedProductionDepartmentId);
                })->orWhereHas('product.departments', function ($subQuery) {
                    $subQuery->where('departments.id', $this->selectedProductionDepartmentId)
                        ->where('department_product.is_available', true);
                });
            })
            ->with(['product:id,name,sku', 'unitOfMeasure:id,symbol'])
            ->orderBy('product_name')
            ->get();

        // Backward compatibility for older recipe rows where only recipes.department_id was maintained.
        if ($strictRecipes->isEmpty()) {
            $strictRecipes = Recipe::query()
                ->where('department_id', $this->selectedProductionDepartmentId)
                ->where('status', 'active')
                ->with(['product:id,name,sku', 'unitOfMeasure:id,symbol'])
                ->orderBy('product_name')
                ->get();
        }

        $this->availableRecipes = $strictRecipes
            ->unique('id')
            ->values()
            ->map(fn (Recipe $recipe) => [
                'id' => $recipe->id,
                'product_id' => $recipe->product_id,
                'recipe_name' => $recipe->product_name,
                'product_name' => $recipe->product?->name,
                'sku' => $recipe->sku ?: $recipe->product?->sku,
                'yield_quantity' => (float) ($recipe->yield_quantity ?? 0),
                'uom' => $recipe->unitOfMeasure?->symbol,
            ])
            ->toArray();
    }

    public function addItem(): void
    {
        $this->submitError = '';
        $this->submitSuccess = '';

        $this->validate([
            'selectedProductionDepartmentId' => ['required', 'exists:departments,id'],
            'selectedRecipeId' => ['required', 'exists:recipes,id'],
            'quantityRequested' => ['required', 'numeric', 'min:0.01'],
        ]);

        $department = collect($this->productionDepartments)->firstWhere('id', $this->selectedProductionDepartmentId);
        $recipe = collect($this->availableRecipes)->firstWhere('id', $this->selectedRecipeId);

        if (! $department || ! $recipe) {
            $this->toast()->error('Please select a valid department and recipe.')->send();

            return;
        }

        $existingIndex = collect($this->cartItems)->search(function (array $item): bool {
            return (int) $item['production_department_id'] === (int) $this->selectedProductionDepartmentId
                && (int) $item['recipe_id'] === (int) $this->selectedRecipeId;
        });

        if ($existingIndex !== false) {
            $this->cartItems[$existingIndex]['quantity_requested'] =
                (float) $this->cartItems[$existingIndex]['quantity_requested'] + (float) $this->quantityRequested;
        } else {
            $this->cartItems[] = [
                'production_department_id' => (int) $department['id'],
                'production_department_name' => $department['name'],
                'recipe_id' => (int) $recipe['id'],
                'recipe_name' => (string) $recipe['recipe_name'],
                'product_id' => $recipe['product_id'] ? (string) $recipe['product_id'] : null,
                'product_name' => $recipe['product_name'] ?? null,
                'sku' => $recipe['sku'] ?? null,
                'yield_quantity' => (float) ($recipe['yield_quantity'] ?? 0),
                'uom' => $recipe['uom'] ?? null,
                'quantity_requested' => (float) $this->quantityRequested,
            ];
        }

        $this->selectedRecipeId = null;
        $this->quantityRequested = 1;
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->cartItems[$index])) {
            return;
        }

        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
    }

    public function submitRequest(): void
    {
        $this->submitError = '';
        $this->submitSuccess = '';

        $this->validate([
            'priority' => ['required', 'in:normal,urgent'],
            'notes' => ['nullable', 'string'],
        ]);

        if (empty($this->cartItems)) {
            $this->toast()->error('Add at least one item before submitting.')->send();

            return;
        }

        if (! $this->departmentId) {
            $this->toast()->error('Sales department context is missing.')->send();

            return;
        }

        $branchId = $this->getBranchId();
        if (! $branchId) {
            $this->toast()->error('Branch context is missing.')->send();

            return;
        }

        if (! Schema::hasTable('sales_production_requests') || ! Schema::hasTable('sales_production_request_items')) {
            $this->toast()->error('Sales production tables are not ready. Run pending migrations and try again.')->send();

            return;
        }

        $salesDeptCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->departmentName) ?: 'SALES', 0, 4));
        $branchCode = strtoupper(substr(str_replace('-', '', $branchId), 0, 8));

        try {
            DB::transaction(function () use ($branchId, $salesDeptCode, $branchCode) {
                $request = SalesProductionRequest::create([
                    'branch_id' => $branchId,
                    'sales_department_id' => $this->departmentId,
                    'request_number' => SalesProductionRequest::generateRequestNumber($branchCode, $salesDeptCode),
                    'status' => 'pending',
                    'priority' => $this->priority,
                    'requested_by_id' => auth()->id(),
                    'requested_by_type' => auth()->user() ? get_class(auth()->user()) : null,
                    'notes' => $this->notes ?: null,
                ]);

                $hasRecipeIdColumn = Schema::hasColumn('sales_production_request_items', 'recipe_id');

                foreach ($this->cartItems as $item) {
                    $payload = [
                        'sales_production_request_id' => $request->id,
                        'production_department_id' => $item['production_department_id'],
                        'product_id' => $item['product_id'],
                        'quantity_requested' => $item['quantity_requested'],
                        'quantity_produced' => 0,
                        'status' => 'pending',
                    ];

                    if ($hasRecipeIdColumn) {
                        $payload['recipe_id'] = $item['recipe_id'];
                    }

                    SalesProductionRequestItem::create($payload);
                }
            });

            $this->toast()->success('Sales production request submitted successfully.')->send();
            $this->submitSuccess = 'Sales production request submitted successfully.';

            $this->redirectRoute('branch-dashboard.sales-dashboard.production-requests.index', [
                'salesDeptSlug' => $this->salesDeptSlug,
                'sales_dept_slug' => $this->salesDeptSlug,
                'b_id' => $this->getBranchId(),
                'page' => 'Production Requests' . '_' . $this->salesDeptSlug,
            ], navigate: true);
        } catch (\Throwable $e) {
            Log::error('Sales production request submit failed', [
                'sales_department_id' => $this->departmentId,
                'branch_id' => $branchId,
                'error' => $e->getMessage(),
            ]);

            $this->submitError = 'Could not submit request: ' . $e->getMessage();
            $this->toast()->error('Could not submit request: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.production-requests.create', [
            'totalItems' => count($this->cartItems),
            'totalQuantity' => collect($this->cartItems)->sum('quantity_requested'),
        ]);
    }
}
