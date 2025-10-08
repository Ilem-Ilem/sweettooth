<?php

namespace App\Livewire\SuperAdmin\DepartmentCategories;

use App\Livewire\BaseComponent;
use App\Models\DepartmentCategory;

class Index extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;

    // Modal states
    public bool $showCategoryModal = false;
    public ?string $selectedId = null;
    public bool $isEditing = false;

    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Form fields
    public string $name = '';
    public string $description = '';

    protected function getModelClass(): string
    {
        return DepartmentCategory::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return DepartmentCategory::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->advancedSearch = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->resetPage();
    }

    public function exportExcel()
    {
        $this->toast()->success('Excel export feature coming soon!')->send();
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    public function openCategoryModal()
    {
        $this->isEditing = false;
        $this->resetForm();
        $this->showCategoryModal = true;
    }

    public function editCategory($id)
    {
        $category = DepartmentCategory::findOrFail($id);
        $this->isEditing = true;
        $this->selectedId = $id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->selectedId = null;
        $this->isEditing = false;
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:department_categories,name,' . $this->selectedId . ',id',
            'description' => 'required|string',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->isEditing && $this->selectedId) {
            DepartmentCategory::findOrFail($this->selectedId)->update($data);
            $message = 'Category updated successfully!';
        } else {
            DepartmentCategory::create($data);
            $message = 'Category created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closeCategoryModal();
    }

    public function deleteCategory($id): void
    {
        $this->selectedId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this category?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->selectedId) {
            DepartmentCategory::findOrFail($this->selectedId)->delete();
            $this->dialog()->success('Success', 'Category deleted successfully!')->send();
            $this->selectedId = null;
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->selectedId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function bulkDeleteCategories(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' category(ies)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        DepartmentCategory::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' category(ies) deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        return view('livewire.super-admin.department-categories.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Category Name'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'created_at', 'label' => 'Created At'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
        ]);
    }
}
