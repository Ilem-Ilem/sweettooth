<?php

namespace App\Livewire\BranchDashboard\DepartmentModule\Cartegory;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\DepartmentCategory;
use App\Livewire\Concerns\CachesDepartmentCategories;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Edit Category')]
class Edit extends Component
{
    use CachesDepartmentCategories, Interactions;

    public $categoryId;
    public string $name = '';
    public string $description = '';

    public function mount($id)
    {
        $category = DepartmentCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:department_categories,name,' . $this->categoryId,
            'description' => 'required|string|max:1000',
        ];
    }

    public function editCategory()
    {
        $this->validate();

        DepartmentCategory::find($this->categoryId)->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->bumpCategoryCacheVersion(); // Invalidate cache

        $this->toast()->success('Category updated successfully!')->send();
        $this->redirect(route('branch-dashboard.branch.departments.category'), navigate: true);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.department-module.cartegory.edit');
    }
}