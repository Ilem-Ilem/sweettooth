<?php

namespace App\Livewire\BranchDashboard\Production\SalesDailyProduce;

use App\Livewire\BaseComponent;
use App\Models\Department;
use App\Models\ProductionRequest;
use App\Models\Shift;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    #[Url(keep: true)]
    public ?string $dept_slug = null;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?Department $department = null;

    public ?string $status = null;
    public ?string $search = null;
    public ?int $quantity = 10;
    public ?int $shift_id = null;

    protected function getModelClass(): string
    {
        return ProductionRequest::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->baseQuery()->pluck('id')->toArray();
    }

    public function mount(string $deptSlug)
    {
        $this->dept_slug = $deptSlug;
        $this->department = Department::where('slug', $deptSlug)->first();
        abort_unless($this->department, 404, 'Department not found');

        // pick active shift for non-super-admin if available
        if (!is_super_admin()) {
            $activeShift = request()->get('active_shift');
            $this->shift_id = $activeShift?->id;
        }
    }

    private function baseQuery()
    {
        $branchId = $this->b_id ?? request()->query('b_id');

        return ProductionRequest::query()
            ->where('production_department_id', $this->department->id)
            ->whereNotNull('sales_department_id')
            // Shift filter: if a shift is selected, allow matching shift or unassigned (super-admin)
            ->when($this->shift_id, fn ($q) => $q->where(function ($qq) {
                $qq->where('shift_id', $this->shift_id)
                   ->orWhereNull('shift_id');
            }))
            // Branch filter: accept requests tied to the branch via itemRequest, shift, or departments
            ->when($branchId, function ($q) use ($branchId) {
                $q->where(function ($scope) use ($branchId) {
                    $scope->whereHas('itemRequest', fn ($sub) => $sub->where('branch_id', $branchId))
                          ->orWhereHas('shift', fn ($sub) => $sub->where('branch_id', $branchId))
                          ->orWhereHas('productionDepartment', fn ($sub) => $sub->where(function ($qq) use ($branchId) {
                              $qq->where('branch_id', $branchId)->orWhereNull('branch_id');
                          }))
                          ->orWhereHas('salesDepartment', fn ($sub) => $sub->where(function ($qq) use ($branchId) {
                              $qq->where('branch_id', $branchId)->orWhereNull('branch_id');
                          }));
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where(function ($qq) use ($term) {
                    $qq->whereHas('recipe', fn ($r) => $r->where('product_name', 'like', $term))
                       ->orWhere('id', 'like', $term);
                });
            })
            ->with([
                'recipe:id,product_name,yield_quantity,uom_id,sku',
                'recipe.unitOfMeasure:id,symbol,name',
                'salesDepartment:id,name,slug',
                'progressFeedback' => fn ($q) => $q->latest()->limit(1),
                'shift:id,shift_date,shift_type',
                'dispatches:id,production_request_id,sales_department_id,quantity,status',
            ])
            ->orderByDesc('created_at');
    }

    public function render()
    {
        $rows = $this->baseQuery()->paginate($this->quantity ?? 10);

        return view('livewire.branch-dashboard.production.sales-daily-produce.index', [
            'rows' => $rows,
        ]);
    }
}
