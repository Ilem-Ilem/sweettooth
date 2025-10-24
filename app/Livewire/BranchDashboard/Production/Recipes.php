<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Livewire\BaseComponent;
use App\Models\Department;
use App\Models\Item;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Employee;

#[Layout('components.layouts.app.branch-dashboard')]
class Recipes extends BaseComponent
{
    #[Url(keep: true)]
    public $b_id;

    public ?int $quantity = 10;

    public ?string $search = null;

    public ?int $filterDepartment = null;

    public ?string $filterStatus = null;

    // Modal states
    // public bool $showModal = false;

    public ?int $recipeId = null;

    // public bool $isEditing = false;


    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    ];

    protected function getModelClass(): string
    {
        return Recipe::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    protected function getFilteredQuery()
    {
        $branchId = $this->getBranchId();
        
        $department = Employee::where('id', auth('employees')->id())->first()->department_id;
        
        return Recipe::query()
            ->where('branch_id', $branchId)
            ->with(['department', 'createdBy', 'ingredients.item'])
            ->when($this->search, function ($query) {
                $query->where('product_name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhere('product_type', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterDepartment, function ($query) {
                $query->where('department_id', $this->filterDepartment);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })->where('department_id', $department)
            ->orderBy('created_at', 'desc');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->filterDepartment = null;
        $this->filterStatus = null;
        $this->resetPage();
    }

    // Auto-generate SKU based on product name
    public function updatedProductName()
    {
        if (! $this->isEditing && ! empty($this->product_name)) {
            $this->generateSku();
        }
    }


    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $branchId = $this->getBranchId();
        $departments = Department::whereHas('category', function ($q) {
            $q->where('name', 'Production');
        })->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->pluck('id')->pluck('name')->toArray();
        $items = Item::orderBy('name')->get();

        return view('livewire.branch-dashboard.production.recipes', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'sku', 'label' => 'SKU'],
                ['index' => 'product_name', 'label' => 'Product Name'],
                ['index' => 'product_type', 'label' => 'Type'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'yield_quantity', 'label' => 'Yield'],
                ['index' => 'cost_per_unit', 'label' => 'Cost/Unit'],
                ['index' => 'preparation_time', 'label' => 'Prep Time'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'departments' => $departments,
            'products' => $products,
            'items' => $items,
        ]);
    }

    // public function openEditModal($id)
    // {
    //     $recipe = Recipe::with('ingredients')->findOrFail($id);

    //     $this->recipeId = $recipe->id;
    //     $this->product_id = $recipe->product_id ?? null;
    //     $this->product_name = $recipe->product_name;
    //     $this->sku = $recipe->sku;
    //     $this->department_id = $recipe->department_id;
    //     $this->category_id = $recipe->category_id;
    //     $this->product_type = $recipe->product_type ?? '';
    //     $this->cost_per_unit = $recipe->cost_per_unit;
    //     $this->uom = $recipe->uom;
    //     $this->yield_quantity = $recipe->yield_quantity;
    //     $this->preparation_time = $recipe->preparation_time;
    //     $this->instructions = json_decode($recipe->instructions, true) ?? [];
    //     $this->status = $recipe->status;

    //     // Load existing ingredients
    //     $this->ingredients = $recipe->ingredients->map(function ($ingredient) {
    //         return [
    //             'id' => $ingredient->id,
    //             'item_id' => $ingredient->item_id,
    //             'quantity' => $ingredient->quantity,
    //             'uom' => $ingredient->uom,
    //             'notes' => $ingredient->notes ?? '',
    //         ];
    //     })->toArray();

    //     $this->isEditing = true;
    //     $this->showModal = true;
    // }

    public function delete($id): void
    {
        $this->recipeId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this recipe?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->recipeId) {
            $recipe = Recipe::findOrFail($this->recipeId);
            $recipe->ingredients()->delete();
            $recipe->delete();
            $this->dialog()->success('Success', 'Recipe deleted successfully!')->send();
            $this->recipeId = null;
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->recipeId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function bulkDeleteItems(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete '.count($this->selectedIds).' recipe(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        foreach ($this->selectedIds as $id) {
            $recipe = Recipe::find($id);
            if ($recipe) {
                $recipe->ingredients()->delete();
                $recipe->delete();
            }
        }
        $this->dialog()->success('Success', 'Recipes deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // public function closeModal()
    // {
    //     $this->showModal = false;
    //     $this->resetFields();
    //     $this->resetValidation();
    // }

}
