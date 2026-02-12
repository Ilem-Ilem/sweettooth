<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Events\ProductionRequest\ProgressUpdated;
use App\Models\ProductionProgressFeedback;
use App\Models\ProductionRequest;
use App\Models\ProductDispatch;
use App\Models\Department;
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
    public $milestones = [
        'started' => 'Production Started',
        'in_production' => 'In Production',
        'quality_check' => 'Quality Check',
        'completed' => 'Completed'
    ];

    public function mount($requestId = null)
    {
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

        if ($this->request?->sales_department_id) {
            $dept = Department::find($this->request->sales_department_id);
            $this->dispatchSalesDepartmentName = $dept?->name ?? 'Sales Dept';
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

        ProductDispatch::create([
            'production_request_id' => $this->request->id,
            'sales_department_id' => $this->request->sales_department_id,
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

        $this->dispatchQuantity = null;
        $this->loadRequest();
        session()->flash('success', 'Dispatched to '.$this->dispatchSalesDepartmentName.'.');
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
