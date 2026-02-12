<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\ProductionRequests;

use App\Livewire\BaseComponent;
use App\Livewire\Concerns\SalesDepartmentContext;
use App\Models\ProductionRequest;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use SalesDepartmentContext;

    public string $statusFilter = 'all';
    public array $requests = [];

    public function mount(): void
    {
        $this->mountBase();
        $this->initializeDepartmentContext();
        $this->loadRequests();
    }

    public function updatedStatusFilter(): void
    {
        $this->loadRequests();
    }

    protected function getModelClass(): string
    {
        return ProductionRequest::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->baseQuery()->pluck('id')->toArray();
    }

    private function loadRequests(): void
    {
        if (!$this->departmentId) {
            $this->requests = [];
            return;
        }

        $query = $this->baseQuery()->with([
            'recipe:id,product_name,yield_quantity,preparation_time',
            'productionDepartment:id,name,slug',
            'progressFeedback' => function ($q) {
                $q->latest()->limit(1);
            }
        ]);

        $this->requests = $query->get()->map(function ($request) {
            $yieldPerBatch = (float) ($request->recipe?->yield_quantity ?? 0);
            $planned = (float) ($request->planned_production_quantity ?? 0);
            $requested = (float) ($request->requested_units ?? $planned);
            $batchCount = $yieldPerBatch > 0 ? (int) ceil($planned / $yieldPerBatch) : 0;

            $latestProgress = $request->progressFeedback->first();
            $progressPercent = $latestProgress?->progress_percentage ?? 0;
            $milestone = $latestProgress?->milestone_label ?? 'Not started';

            $etaData = $this->calculateEta($request, $batchCount);

            return [
                'id' => $request->id,
                'recipe_name' => $request->recipe?->product_name ?? 'N/A',
                'status' => $request->status,
                'priority' => $request->priority,
                'requested_units' => $requested,
                'planned_units' => $planned,
                'batch_count' => $batchCount,
                'production_department' => $request->productionDepartment?->name ?? 'Production',
                'created_at' => $request->created_at?->format('M d, Y H:i') ?? '',
                'started_at' => $request->started_at?->format('M d, Y H:i') ?? null,
                'progress_percent' => $progressPercent,
                'milestone' => $milestone,
                'eta_label' => $etaData['eta_label'],
                'time_left' => $etaData['time_left'],
                'eta_source' => $etaData['eta_source'],
            ];
        })->toArray();
    }

    private function baseQuery()
    {
        $query = ProductionRequest::query()
            ->where('sales_department_id', $this->departmentId)
            ->when(!is_super_admin(), function ($q) {
                if (request()->has('active_shift')) {
                    $shift = request()->get('active_shift');
                    $q->where('shift_id', $shift?->id);
                }
            })
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query;
    }

    private function calculateEta(ProductionRequest $request, int $batchCount): array
    {
        if (!$request->started_at) {
            return [
                'eta_label' => 'Not started',
                'time_left' => 'N/A',
                'eta_source' => 'none',
            ];
        }

        $start = Carbon::parse($request->started_at);

        if (!is_null($request->eta_override_minutes)) {
            $eta = $start->copy()->addMinutes((int) $request->eta_override_minutes);
            return [
                'eta_label' => $eta->format('M d, Y H:i'),
                'time_left' => $eta->isPast() ? 'Ready/Overdue' : $eta->diffForHumans(now(), ['parts' => 2, 'short' => true]),
                'eta_source' => 'manual',
            ];
        }

        $prepMinutesPerBatch = (int) ($request->recipe?->preparation_time ?? 0);
        if ($prepMinutesPerBatch <= 0 || $batchCount <= 0) {
            return [
                'eta_label' => 'N/A',
                'time_left' => 'N/A',
                'eta_source' => 'auto',
            ];
        }

        $eta = $start->copy()->addMinutes($prepMinutesPerBatch * $batchCount);

        return [
            'eta_label' => $eta->format('M d, Y H:i'),
            'time_left' => $eta->isPast() ? 'Ready/Overdue' : $eta->diffForHumans(now(), ['parts' => 2, 'short' => true]),
            'eta_source' => 'auto',
        ];
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.production-requests.index');
    }
}
