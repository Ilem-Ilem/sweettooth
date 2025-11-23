<?php

namespace App\Livewire\BranchDashboard\DepartmentModule\Cartegory;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\DepartmentCategory;
use App\Livewire\Concerns\CachesDepartmentCategories;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Create New Category')]
class Create extends Component
{
    use CachesDepartmentCategories;
    use Interactions;

    public string $name = '';
    public string $description = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:department_categories,name',
        'description' => 'required|string|max:1000',
    ];

    public function saveCategory()
    {
        $this->validate();

        DepartmentCategory::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->bumpCategoryCacheVersion(); // This instantly invalidates all list caches

        $this->toast()->success('Category created successfully!')->send();

        $this->redirect(route('branch-dashboard.branch.departments.category'), navigate: true);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.department-module.cartegory.create');
    }
}