<?php

namespace App\Livewire\BranchDashboard\DepartmentModule;

use App\Livewire\BaseComponent;
use App\Models\DepartmentCategory;
use Livewire\Attributes\{Layout, Url};

#[Layout('components.layouts.app.branch-dashboard')]
class Category extends BaseComponent
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



    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        return view('livewire.branch-dashboard.department-module.category', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Category Name'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'created_at', 'label' => 'Created At'],
            ],
            'rows' => $rows,
        ]);
    }
}

