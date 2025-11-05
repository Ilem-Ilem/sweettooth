<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Models\Recipe;
use App\Models\Department;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app.branch-dashboard')]
class RecipeDetail extends Component
{
    #[Url(keep: true)]
    public $b_id;

    public $recipeId;
    public $recipe;
    public $batchSize = 1;


    #[Url(keep: true)]
    public $dept_slug;

    public $department;

    public function mount($id, $deptSlug)
    {
        $this->recipeId = $id;
        $this->dept_slug = $deptSlug;
        $this->department = Department::where('slug', $deptSlug)->first();

        if (!$this->department) {
            abort(404, 'Department not found');
        }

        $this->loadRecipe();
    }

    public function loadRecipe()
    {
        $this->recipe = Recipe::with(['ingredients.item', 'department', 'createdBy'])
            ->where('department_id', $this->department->id)
            ->findOrFail($this->recipeId);
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function updatedBatchSize()
    {
        // Ensure batch size is at least 1
        if ($this->batchSize < 1) {
            $this->batchSize = 1;
        }
    }

    public function getCalculatedIngredientsProperty()
    {
        return $this->recipe->calculateIngredientsForBatch($this->batchSize);
    }

    public function getTotalCostProperty()
    {
        return $this->recipe->calculateTotalCostForBatch($this->batchSize);
    }

    public function getTotalYieldProperty()
    {
        return $this->recipe->calculateYieldForBatch($this->batchSize);
    }

    public function getCostPerUnitProperty()
    {
        return $this->recipe->calculateCostPerUnit();
    }

    public function getInstructionsProperty()
    {
        return $this->recipe->getInstructionsArray();
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.recipe-detail');
    }
}
