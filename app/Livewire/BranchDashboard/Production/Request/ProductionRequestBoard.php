<?php

namespace App\Livewire\BranchDashboard\Production\Request;

use App\Models\ProductionRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ProductionRequestBoard extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $priorityFilter = '';
    public $userDepartmentId;
    public $selectedRequest = null;
    public $showDetails = false;
    public $showProgressForm = false;

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->department) {
            $this->userDepartmentId = $user->department->id;
        }
    }

    public function getRequestsProperty()
    {
        $query = ProductionRequest::where('production_department_id', $this->userDepartmentId)
            ->with(['salesDepartment', 'createdBy', 'progressFeedback']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        return $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
    }

    public function viewDetails($requestId)
    {
        $this->selectedRequest = ProductionRequest::with(['progressFeedback', 'dispatches'])->find($requestId);
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->selectedRequest = null;
        $this->showDetails = false;
        $this->showProgressForm = false;
    }

    public function startProduction($requestId)
    {
        $request = ProductionRequest::find($requestId);
        if ($request) {
            $request->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            $this->dispatch('requestUpdated');
        }
    }

    public function markCompleted($requestId)
    {
        $request = ProductionRequest::find($requestId);
        if ($request) {
            $request->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $this->dispatch('requestUpdated');
        }
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'quality_check' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'dispatched' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-300'
        };
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'quality_check' => 'Quality Check',
            'completed' => 'Completed',
            'dispatched' => 'Dispatched',
            default => ucfirst(str_replace('_', ' ', $status))
        };
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.request.production-request-board', [
            'requests' => $this->requests,
        ]);
    }
}
