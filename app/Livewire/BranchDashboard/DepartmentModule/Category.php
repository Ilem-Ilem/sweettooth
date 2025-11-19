<?php

namespace App\Livewire\BranchDashboard\DepartmentModule;

use App\Livewire\BaseComponent;
use App\Models\DepartmentCategory;
use App\Livewire\Concerns\CachesDepartmentCategories;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\{Layout};

#[Layout('components.layouts.app.branch-dashboard')]
class Category extends BaseComponent
{
    use CachesDepartmentCategories;

    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Modal states
    public bool $showCategoryModal = false;
    public ?string $selectedId = null;
    public bool $isEditing = false;

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
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->advancedSearch, fn ($q) => $q->where(fn ($sq) => $sq
                ->where('name', 'like', "%{$this->advancedSearch}%")
                ->orWhere('description', 'like', "%{$this->advancedSearch}%")))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc');
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        DepartmentCategory::find($id)?->delete();
        $this->bumpCategoryCacheVersion();
        $this->toast()->success('Category deleted.')->send();
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
        $this->toast()->info('Excel export coming soon!')->send();
    }

    public function exportPdf()
    {
        $this->toast()->info('PDF export coming soon!')->send();
    }

    public function updated($property, $value)
    {
        if (Str::contains($property, ['search', 'advancedSearch', 'dateFrom', 'dateTo', 'quantity'])) {
            $this->resetPage();
        }
    }

    protected function getCacheKey(): string
    {
        $userId   = auth()->id() ?? 'guest';
        $branchId = auth()->user()?->branch_id ?? 'none';

        return 'dept_categories_v3_' . md5(serialize([
            'user_id'        => $userId,
            'branch_id'      => $branchId,
            'search'         => $this->search,
            'advancedSearch' => $this->advancedSearch,
            'dateFrom'       => $this->dateFrom,
            'dateTo'         => $this->dateTo,
            'quantity'       => $this->quantity ?? 10,
            'page'           => request()->input('page', 1),
            'cache_version'  => $this->getCategoryCacheVersion(), // This makes it auto-invalidate
        ]));
    }

    public function render()
    {
        $cacheKey = $this->getCacheKey();

        $rows = Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return $this->getFilteredQuery()
                ->paginate($this->quantity ?? 10)
                ->withQueryString();
        });

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
