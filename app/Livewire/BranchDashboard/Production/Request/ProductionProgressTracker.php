<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Events\ProductionRequest\ProgressUpdated;
use App\Models\ProductionProgressFeedback;
use App\Models\ProductionRequest;
use Livewire\Component;

class ProductionProgressTracker extends Component
{
    public $productionRequestId;
    public $selectedMilestone = 'in_production';
    public $progressPercentage = 0;
    public $notes = '';
    public $request;
    public $showForm = false;
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
    }

    public function updateProgress()
    {
        $this->validate([
            'selectedMilestone' => 'required|in:started,in_production,quality_check,completed',
            'progressPercentage' => 'required|integer|min:0|max:100',
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

            $this->request->update([
                'status' => $statusMap[$this->selectedMilestone],
            ]);

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

    private function resetForm()
    {
        $this->selectedMilestone = 'in_production';
        $this->progressPercentage = 0;
        $this->notes = '';
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.request.production-progress-tracker');
    }
}
