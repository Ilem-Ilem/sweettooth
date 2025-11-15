<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Livewire\BaseComponent;
use App\Models\{Product, Employee};
use App\Models\ProductType;
use App\Models\Department;
use Livewire\Attributes\{Layout, On, Url};

#[Layout('components.layouts.app.branch-dashboard')]
class Products extends BaseComponent
{
    #[Url(keep:true)]
    public $b_id;

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public ?int $quantity = 10;
    public ?string $search = null;
    public ?int $filterProductType = null;
    public ?int $filterDepartment = null;
    public ?string $filterStatus = null;

    // Modal states
    public bool $showModal = false;
    public ?string $productId = null;
    public bool $isEditing = false;

    // Form fields
    public string $name = '';
    public string $sku = '';
    public ?int $product_type_id = null;
    public ?int $category_id = null;
    public string $description = '';
    public $price = 0;
    public $cost = null;
    public int $shelf_life_days = 0;
    public string $uom = 'pcs';
    public $recipe_yield = 1;
    public $recipe_yield_weight = null;
    public $unit_weight = null;
    public $yield_percentage = 100;
    public bool $is_active = true;
    public bool $is_available = true;
    public string $image_url = '';
    public array $allergens = [];
    public array $tags = [];
    public $department;

    #[Url(keep: true)]
    public $dept_slug;

    public function mount($deptSlug){
        $this->dept_slug = $deptSlug;

        $this->department =  Employee::where('id', auth('employees')->id())->first();
    }

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    ];

    protected function getModelClass(): string
    {
        return Product::class;
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
        $department = Department::where('slug', $this->dept_slug)->first()->id;

        // if($department !== $department_from_route){
        //     abort(403, "Wrong Departmental Access");
        // }

        return Product::query()
            ->with(['productType.department'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterProductType, function ($query) {
                $query->where('product_type_id', $this->filterProductType);
            })
            ->when($this->filterDepartment, function ($query) {
                $query->whereHas('productType', function ($q) {
                    $q->where('department_id', $this->filterDepartment);
                });
            })
            ->when($this->filterStatus !== null, function ($query) {
                if ($this->filterStatus === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->filterStatus === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($this->filterStatus === 'available') {
                    $query->where('is_available', true);
                } elseif ($this->filterStatus === 'unavailable') {
                    $query->where('is_available', false);
                }
            })
            ->whereHas('productType', function ($q) use ($department){
                $q->where('department_id', $department);
            })
            ->where("branch_id", null)->orWhere("branch_id", $this->getBranchId(), )
            ->orderBy('created_at', 'desc');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterProductType()
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
        $this->filterProductType = null;
        $this->filterDepartment = null;
        $this->filterStatus = null;
        $this->resetPage();
    }

    // Auto-generate SKU
    public function updatedProductTypeId()
    {
        $this->generateSku();
    }

    public function updatedName()
    {
        $this->generateSku();
    }

    private function generateSku()
    {
        if (!$this->isEditing && !empty($this->product_type_id) && !empty($this->name)) {
            $productType = ProductType::find($this->product_type_id);
            if ($productType) {
                $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $this->name), 0, 3));
                $randomCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $this->sku = $productType->code . '-' . $nameCode . '-' . $randomCode;
            }
        }
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $productTypes = ProductType::with('department')->where('department_id',  Department::where('slug', $this->dept_slug)->first()->id)->active()->ordered()->get();
        $departments = Department::whereHas('category', function ($q) {
            $q->where('name', 'Production');
        })->orderBy('name')->get();

        return view('livewire.branch-dashboard.production.products', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'sku', 'label' => 'SKU'],
                ['index' => 'name', 'label' => 'Product Name'],
                ['index' => 'product_type', 'label' => 'Type'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'recipe_yield', 'label' => 'Yield/Batch'],
                ['index' => 'price', 'label' => 'Price'],
                ['index' => 'shelf_life', 'label' => 'Shelf Life'],
                ['index' => 'uom', 'label' => 'UOM'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'productTypes' => $productTypes,
            'departments' => $departments,
            'employees_department'=> Department::where('id', $this->department->department_id)->first()
        ]);
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $product = Product::findOrFail($id);

        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->product_type_id = $product->product_type_id;
        $this->category_id = $product->category_id;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->cost = $product->cost;
        $this->shelf_life_days = $product->shelf_life_days;
        $this->uom = $product->uom;
        $this->recipe_yield = $product->recipe_yield;
        $this->recipe_yield_weight = $product->recipe_yield_weight;
        $this->unit_weight = $product->unit_weight;
        $this->yield_percentage = $product->yield_percentage;
        $this->is_active = $product->is_active;
        $this->is_available = $product->is_available;
        $this->image_url = $product->image_url ?? '';
        $this->allergens = $product->allergens ?? [];
        $this->tags = $product->tags ?? [];

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'product_type_id' => 'required|exists:product_types,id',
            'category_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'shelf_life_days' => 'required|integer|min:0',
            'uom' => 'required|in:grams,kg,liters,ml,pcs,units',
            'recipe_yield' => 'required|numeric|min:0.01',
            'recipe_yield_weight' => 'nullable|numeric|min:0',
            'unit_weight' => 'nullable|numeric|min:0',
            'yield_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'image_url' => 'nullable|string',
        ];

        if ($this->isEditing) {
            $rules['sku'] = 'required|string|max:255|unique:products,sku,' . $this->productId;
        } else {
            $rules['sku'] = 'required|string|max:255|unique:products,sku';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'sku' => strtoupper($this->sku),
            'product_type_id' => $this->product_type_id,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'shelf_life_days' => $this->shelf_life_days,
            'uom' => $this->uom,
            'recipe_yield' => $this->recipe_yield,
            'recipe_yield_weight' => $this->recipe_yield_weight,
            'unit_weight' => $this->unit_weight,
            'yield_percentage' => $this->yield_percentage,
            'is_active' => $this->is_active,
            'is_available' => $this->is_available,
            'image_url' => $this->image_url,
            'allergens' => $this->allergens,
            'tags' => $this->tags,
        ];

        if ($this->isEditing && $this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);
            $message = 'Product updated successfully!';
        } else {
            Product::create($data);
            $message = 'Product created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closeModal();
    }

    public function delete($id): void
    {
        $this->productId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this product?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->delete();
            $this->dialog()->success('Success', 'Product deleted successfully!')->send();
            $this->productId = null;
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->productId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function bulkDeleteItems(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' product(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Product::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', 'Products deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->productId = null;
        $this->name = '';
        $this->sku = '';
        $this->product_type_id = null;
        $this->category_id = null;
        $this->description = '';
        $this->price = 0;
        $this->cost = null;
        $this->shelf_life_days = 0;
        $this->uom = 'pcs';
        $this->recipe_yield = 1;
        $this->recipe_yield_weight = null;
        $this->unit_weight = null;
        $this->yield_percentage = 100;
        $this->is_active = true;
        $this->is_available = true;
        $this->image_url = '';
        $this->allergens = [];
        $this->tags = [];
        $this->isEditing = false;
    }
}
