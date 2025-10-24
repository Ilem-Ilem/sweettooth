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

    // Date range filters
    public $filterStartDate = null;
    public $filterEndDate = null;
    public $filterMode = 'shift'; // 'shift' or 'date_range'

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

        // Get today's shift for the employee's DEPARTMENT (not specific employee)
        $this->currentShift = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->where('shift_date', today())
            ->orderBy('shift_type')
            ->first();

        if ($this->currentShift) {
            $this->selectedShiftId = $this->currentShift->id;
        }

        // Initialize date filters to today
        if (!$this->filterStartDate) {
            $this->filterStartDate = today()->format('Y-m-d');
        }
        if (!$this->filterEndDate) {
            $this->filterEndDate = today()->format('Y-m-d');
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

    public function updatedFilterStartDate()
    {
        $this->resetPage();
    }

    public function updatedFilterEndDate()
    {
        $this->resetPage();
    }

    public function updatedFilterMode()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filterRecipe = '';
        $this->filterVarianceType = '';
        $this->filterStartDate = today()->format('Y-m-d');
        $this->filterEndDate = today()->format('Y-m-d');
        $this->filterMode = 'shift';
        $this->resetPage();
    }

    public function render()
    {
        $branchId = $this->getBranchId();
        $employee = Auth::guard('employees')->user();

        // Get available shifts for this DEPARTMENT (not specific employee)
        $availableShifts = Shift::where('branch_id', $branchId)
            ->where('department_id', $employee->department_id)
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type')
            ->limit(30)
            ->get();

        // Get recipes for filter
        $recipes = Recipe::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        // Query raw material utilizations - DEPARTMENT BASED
        $query = RawMaterialUtilization::with(['shift', 'recipe', 'item'])
            ->whereHas('shift', function($q) use ($branchId, $employee) {
                $q->where('branch_id', $branchId)
                  ->where('department_id', $employee->department_id);
            });

        // Apply filters based on mode
        if ($this->filterMode === 'shift' && $this->selectedShiftId) {
            // Shift-based filtering
            $query->where('shift_id', $this->selectedShiftId);
        } elseif ($this->filterMode === 'date_range' && $this->filterStartDate && $this->filterEndDate) {
            // Date range filtering
            $query->whereHas('shift', function($q) {
                $q->whereBetween('shift_date', [$this->filterStartDate, $this->filterEndDate]);
            });
        }

        // Apply other filters
        $query->when($this->filterRecipe, fn($q) => $q->where('recipe_id', $this->filterRecipe))
              ->when($this->filterVarianceType, fn($q) => $q->where('variance_type', $this->filterVarianceType))
              ->orderBy('created_at', 'desc');

        $utilizations = $query->paginate(15);

        // Calculate summary statistics based on filter mode
        $summary = [
            'total_items' => 0,
            'within_tolerance' => 0,
            'over_used' => 0,
            'under_used' => 0,
            'total_cost_impact' => 0,
            'efficiency_percentage' => 0,
        ];

        // Build summary query based on filter mode
        $summaryQuery = RawMaterialUtilization::whereHas('shift', function($q) use ($branchId, $employee) {
            $q->where('branch_id', $branchId)
              ->where('department_id', $employee->department_id);
        });

        if ($this->filterMode === 'shift' && $this->selectedShiftId) {
            $summaryQuery->where('shift_id', $this->selectedShiftId);
        } elseif ($this->filterMode === 'date_range' && $this->filterStartDate && $this->filterEndDate) {
            $summaryQuery->whereHas('shift', function($q) {
                $q->whereBetween('shift_date', [$this->filterStartDate, $this->filterEndDate]);
            });
        }

        $allUtilizations = $summaryQuery->get();

        if ($allUtilizations->count() > 0) {
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
