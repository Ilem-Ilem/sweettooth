<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Livewire\BaseComponent;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ProductType;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Layout('components.layouts.app.branch-dashboard')]
class ProductTypes extends BaseComponent
{
    #[Url(keep: true)]
    public ?string $b_id = null;

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public ?int $quantity = 10;

    public ?string $search = null;

    public ?string $filterStatus = null;

    public ?int $filterDepartment = null;

    // Modal states
    public bool $showModal = false;

    public ?int $productTypeId = null;

    public bool $isEditing = false;

    // Form fields
    public ?int $department_id = null;

    public string $name = '';

    public string $code = '';

    public string $description = '';

    public string $status = 'active';

    public int $sort_order = 0;

    public User|Employee|null $employees_department = null;

    public ?Department $department = null;

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    ];

    #[Url(keep: true)]
    public ?string $dept_slug = null;

    public function mount($deptSlug)
    {
        $this->dept_slug = $deptSlug;
        $this->department = Department::where('slug', $deptSlug)->first();
        $this->employees_department = Employee::where('id', auth()->id())->first();

    }

    protected function getModelClass(): string
    {
        return ProductType::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->select('id')->pluck('id')->toArray();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    protected function getFilteredQuery()
    {
        return ProductType::query()
            ->when(!is_super_admin(), function ($query) {
                $query->where('department_id', $this->department->id);
            })
            ->with(['department:id,name,slug', 'department.category:id,name']) // Select only needed columns
            ->withCount('products') // Use withCount instead of with and counting separately
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterDepartment()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->filterStatus = null;
        $this->filterDepartment = null;
        $this->resetPage();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        return view('livewire.branch-dashboard.production.product-types', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'code', 'label' => 'Code'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'products_count', 'label' => 'Products'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'department' => $this->department,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetFields();
        // Auto-set department_id based on dept_slug
        if ($this->department) {
            $this->department_id = $this->department->id;
        }
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $productType = ProductType::findOrFail($id);

        $this->productTypeId = $productType->id;
        $this->department_id = $productType->department_id;
        $this->name = $productType->name;
        $this->code = $productType->code;
        $this->description = $productType->description ?? '';
        $this->status = $productType->status;
        $this->sort_order = $productType->sort_order;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'required|integer|min:0',
        ];

        if ($this->isEditing) {
            $rules['code'] = 'required|string|max:50|unique:product_types,code,'.$this->productTypeId;
        } else {
            $rules['code'] = 'required|string|max:50|unique:product_types,code';
        }

        $this->validate($rules);

        $data = [
            'department_id' => $this->department_id,
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ];

        if ($this->isEditing && $this->productTypeId) {
            $productType = ProductType::findOrFail($this->productTypeId);
            $productType->update($data);
            $message = 'Product type updated successfully!';
        } else {
            ProductType::create($data);
            $message = 'Product type created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closeModal();
    }

    public function delete($id): void
    {
        $this->productTypeId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this product type?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->productTypeId) {
            $productType = ProductType::withTrashed()->find($this->productTypeId);

            if ($productType) {
                // Check if it has products
                if ($productType->products()->count() > 0) {
                    $this->dialog()->error('Error', 'Cannot delete product type with associated products!')->send();

                    return;
                }

                $productType->delete();
                // Log the delete for super admin
                if (is_super_admin()) {
                    \App\Services\AuditService::log(
                        auth()->user(),
                        'delete',
                        $productType,
                        'Super admin direct delete of product type'
                    );
                }
            }
            $this->dialog()->success('Success', 'Product type deleted successfully!')->send();
            $this->productTypeId = null;

            // Clear selection after successful deletion
            $this->resetBulkSelection();
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->productTypeId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function bulkDeleteItems(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete '.count($this->selectedIds).' product type(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        $productTypes = ProductType::withTrashed()->whereIn('id', $this->selectedIds)
            ->whereDoesntHave('products')
            ->get();
        ProductType::whereIn('id', $this->selectedIds)
            ->whereDoesntHave('products')
            ->delete();

        // Log for super admin
        if (is_super_admin()) {
            foreach ($productTypes as $productType) {
                \App\Services\AuditService::log(
                    auth()->user(),
                    'delete',
                    $productType,
                    'Super admin bulk delete of product type'
                );
            }
        }

        $this->dialog()->success('Success', 'Product types deleted successfully!')->send();

        // Clear selection after successful deletion
        $this->resetBulkSelection();
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
        $this->productTypeId = null;
        $this->department_id = null;
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->status = 'active';
        $this->sort_order = 0;
        $this->isEditing = false;
    }
}
