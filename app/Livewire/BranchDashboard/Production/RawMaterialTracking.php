<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Models\RawMaterialUtilization;
use App\Models\Shift;
use App\Models\Recipe;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class RawMaterialTracking extends Component
{
    use Interactions, WithPagination;

    #[Url(keep: true)]
    public $b_id;

    public $selectedShiftId = null;
    public $filterRecipe = '';
    public $filterVarianceType = '';
    public $currentShift = null;

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function loadCurrentShift()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get today's shift for the employee
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('employee_id', $employee->id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
        }
    }

    public function updatedSelectedShiftId()
    {
        $this->resetPage();
    }

    public function updatedFilterRecipe()
    {
        $this->resetPage();
    }

    public function updatedFilterVarianceType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filterRecipe = '';
        $this->filterVarianceType = '';
        $this->resetPage();
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get available shifts for this employee
        $availableShifts = Shift::where('branch_id', $branchId)
            ->where('employee_id', $employee->id)
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type')
            ->limit(30)
            ->get();

        // Get recipes for filter
        $recipes = Recipe::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        // Query raw material utilizations
        $query = RawMaterialUtilization::with(['shift', 'recipe', 'item'])
            ->when($this->selectedShiftId, fn($q) => $q->where('shift_id', $this->selectedShiftId))
            ->when($this->filterRecipe, fn($q) => $q->where('recipe_id', $this->filterRecipe))
            ->when($this->filterVarianceType, fn($q) => $q->where('variance_type', $this->filterVarianceType))
            ->orderBy('created_at', 'desc');

        $utilizations = $query->paginate(15);

        // Calculate summary statistics
        $summary = [
            'total_items' => 0,
            'within_tolerance' => 0,
            'over_used' => 0,
            'under_used' => 0,
            'total_cost_impact' => 0,
            'efficiency_percentage' => 0,
        ];

        if ($this->selectedShiftId) {
            $allUtilizations = RawMaterialUtilization::where('shift_id', $this->selectedShiftId)->get();

            $summary['total_items'] = $allUtilizations->count();
            $summary['within_tolerance'] = $allUtilizations->where('variance_type', 'within_tolerance')->count();
            $summary['over_used'] = $allUtilizations->where('variance_type', 'over_used')->count();
            $summary['under_used'] = $allUtilizations->where('variance_type', 'under_used')->count();
            $summary['total_cost_impact'] = $allUtilizations->sum('cost_impact');

            $totalRequired = $allUtilizations->sum('quantity_required');
            $totalUsed = $allUtilizations->sum('quantity_used');

            if ($totalRequired > 0) {
                $summary['efficiency_percentage'] = (($totalRequired - abs($totalUsed - $totalRequired)) / $totalRequired) * 100;
            }
        }

        return view('livewire.branch-dashboard.production.raw-material-tracking', [
            'utilizations' => $utilizations,
            'availableShifts' => $availableShifts,
            'recipes' => $recipes,
            'summary' => $summary,
        ]);
    }
}
