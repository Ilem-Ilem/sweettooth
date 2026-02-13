<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Enums\ProductionRequestSourceType;
use App\Events\ProductionRequest\ProgressUpdated;
use App\Models\Department;
use App\Models\ProductionProgressFeedback;
use App\Models\ProductionRequest;
use App\Models\ProductDispatch;
use App\Models\SalesProductionRequestItem;
use App\Services\SalesProductionDispatchService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.branch-dashboard')]
class ProductionProgressTracker extends Component
{
    public $productionRequestId;
    public $selectedMilestone = 'in_production';
    public $progressPercentage = 0;
    public $notes = '';
    public $etaOverrideMinutes = null;
    public $request;
    public $showForm = false;
    public $dispatchQuantity = null;
    public $dispatchSalesDepartmentName = null;
    public $dispatchSalesDepartmentId = null;
    public array $availableSalesDepartments = [];
    public $milestones = [
        'started' => 'Production Started',
        'in_production' => 'In Production',
        'quality_check' => 'Quality Check',
        'completed' => 'Completed'
    ];

    public function mount($requestId = null)
    {
        $this->loadAvailableSalesDepartments();

        if ($requestId) {
            $this->productionRequestId = $requestId;
            $this->loadRequest();
        }
    }

    public function loadRequest()
    {
        $this->request = ProductionRequest::with('progressFeedback')
            ->find($this->productionRequestId);
        $this->etaOverrideMinutes = $this->request?->eta_override_minutes;

        $this->dispatchSalesDepartmentId = null;
        $this->dispatchSalesDepartmentName = null;

        if (! $this->request) {
            return;
        }

        // Resolve dispatch target from sales-demand source linkage.
        if (
            $this->request->source_type === ProductionRequestSourceType::SALES_DEMAND->value
            && ! empty($this->request->source_id)
        ) {
            $salesItem = SalesProductionRequestItem::query()
                ->with('request.salesDepartment')
                ->find((int) $this->request->source_id);

            $salesDept = $salesItem?->request?->salesDepartment;
            if ($salesDept) {
                $this->dispatchSalesDepartmentId = (int) $salesDept->id;
                $this->dispatchSalesDepartmentName = $salesDept->name;
                return;
            }
        }

        if (! empty($this->availableSalesDepartments)) {
            $this->dispatchSalesDepartmentId = (int) $this->availableSalesDepartments[0]['id'];
            $this->dispatchSalesDepartmentName = (string) $this->availableSalesDepartments[0]['name'];
        }
    }

    public function updateProgress()
    {
        $this->validate([
            'selectedMilestone' => 'required|in:started,in_production,quality_check,completed',
            'progressPercentage' => 'required|integer|min:0|max:100',
            'etaOverrideMinutes' => 'nullable|integer|min:0|max:100000',
        ]);

        try {
            $feedback = ProductionProgressFeedback::create([
                'production_request_id' => $this->productionRequestId,
                'milestone' => $this->selectedMilestone,
                'progress_percentage' => $this->progressPercentage,
                'notes' => $this->notes ?: null,
                'updated_by_id' => auth()->id(),
            ]);

            // Update request status based on milestone
            $statusMap = [
                'started' => 'in_progress',
                'in_production' => 'in_progress',
                'quality_check' => 'quality_check',
                'completed' => 'completed',
            ];

            $updates = [
                'status' => $statusMap[$this->selectedMilestone],
                'eta_override_minutes' => $this->etaOverrideMinutes !== '' ? $this->etaOverrideMinutes : null,
            ];

            if (in_array($this->selectedMilestone, ['started', 'in_production']) && !$this->request->started_at) {
                $updates['started_at'] = now();
            }

            if ($this->selectedMilestone === 'completed') {
                $updates['completed_at'] = now();
            }

            $this->request->update($updates);

            // Broadcast progress update
            broadcast(new ProgressUpdated($this->request, $feedback))->toOthers();

            $this->loadRequest();
            $this->resetForm();

            session()->flash('success', 'Progress updated successfully');
            $this->dispatch('progressUpdated', ['requestId' => $this->productionRequestId]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update progress: ' . $e->getMessage());
        }
    }

    public function dispatchToSales()
    {
        $this->validate([
            'dispatchQuantity' => 'required|numeric|min:0.01',
            'dispatchSalesDepartmentId' => 'required|exists:departments,id',
        ]);

        if (!$this->request) {
            session()->flash('error', 'No request loaded.');
            return;
        }

        $remaining = max(0, ($this->request->planned_production_quantity ?? 0) - $this->request->dispatches()->sum('quantity'));

        if ($this->dispatchQuantity > $remaining) {
            session()->flash('error', 'Cannot dispatch more than remaining quantity ('.number_format($remaining,2).').');
            return;
        }

        $branchId = request()->query('b_id') ?? current_branch_id();
        $uomSymbol = $this->request->recipe?->unitOfMeasure?->symbol ?? null;
        /** @var SalesProductionDispatchService $dispatchService */
        $dispatchService = app(SalesProductionDispatchService::class);
        $linkedSalesItem = $dispatchService->resolveLinkedSalesItemFromProductionRequest($this->request);
        try {
            DB::transaction(function () use (
                $branchId,
                $uomSymbol,
                $dispatchService,
                $linkedSalesItem
            ) {
                ProductDispatch::create([
                    'production_request_id' => $this->request->id,
                    'sales_production_request_item_id' => $linkedSalesItem?->id,
                    'sales_department_id' => (int) $this->dispatchSalesDepartmentId,
                    'branch_id' => $branchId,
                    'production_shift_id' => $this->request->shift_id,
                    'product_id' => $this->request->recipe?->product_id,
                    'uom' => $uomSymbol,
                    'quantity' => $this->dispatchQuantity,
                    'status' => 'pending_verification',
                    'dispatch_date' => now()->toDateString(),
                    'dispatch_time' => now(),
                    'shift_type' => $this->request->shift?->shift_type,
                    'dispatched_by_id' => auth()->id(),
                    'dispatched_by_type' => auth()->user() ? get_class(auth()->user()) : null,
                ]);

                if ($linkedSalesItem) {
                    $dispatchService->markItemAsDispatched($linkedSalesItem);
                }
            });
        } catch (\Throwable $e) {
            session()->flash('error', 'Dispatch failed: ' . $e->getMessage());
            return;
        }

        $dept = Department::find((int) $this->dispatchSalesDepartmentId);
        $this->dispatchSalesDepartmentName = $dept?->name ?? 'Sales Dept';

        $this->dispatchQuantity = null;
        $this->loadRequest();
        session()->flash('success', 'Dispatched to '.$this->dispatchSalesDepartmentName.'.');
    }

    public function updatedDispatchSalesDepartmentId($value): void
    {
        $dept = Department::find((int) $value);
        $this->dispatchSalesDepartmentName = $dept?->name ?? null;
    }

    private function loadAvailableSalesDepartments(): void
    {
        $branchId = request()->query('b_id') ?: current_branch_id();

        $this->availableSalesDepartments = Department::query()
            ->where(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            })
            ->where(function ($query) {
                $query->whereHas('category', function ($categoryQuery) {
                    $categoryQuery->whereRaw('LOWER(name) = ?', ['sales']);
                })->orWhereIn('slug', ['till', 'corner-store', 'confectionaries-sales']);
            })
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Department $department) => [
                'id' => $department->id,
                'name' => $department->name,
            ])
            ->toArray();
    }

    private function resetForm()
    {
        $this->selectedMilestone = 'in_production';
        $this->progressPercentage = 0;
        $this->notes = '';
        $this->etaOverrideMinutes = $this->request?->eta_override_minutes;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.request.production-progress-tracker');
    }
}
