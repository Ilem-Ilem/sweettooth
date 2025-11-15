<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\ReviewReports;

use App\Models\DepartmentReport;
use Livewire\Component;
use Livewire\Attributes\{Layout, On, Title, Url};
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Review Reports')]
class Index extends Component
{
    use Interactions, WithPagination;

    #[Url(keep: true)]
    public $b_id;

    public $selectedReport;
    public $showReviewModal = false;
    public $reviewNotes = '';
    public $filterCategory = 'all';
    public $filterDepartment = 'all';

    public function mount()
    {
        $this->b_id = $this->b_id ?? current_branch_id();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    public function openReviewModal($reportId)
    {
        $this->selectedReport = DepartmentReport::with(['department', 'generatedBy'])
            ->findOrFail($reportId);

        if (!$this->selectedReport->canBeReviewed()) {
            $this->toast()->error('This report cannot be reviewed')->send();
            return;
        }

        $this->reviewNotes = $this->selectedReport->review_notes ?? '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->selectedReport = null;
        $this->reviewNotes = '';
    }

    public function approveReport()
    {
        if (!$this->selectedReport) {
            $this->toast()->error('No report selected')->send();
            return;
        }

        try {
            $this->selectedReport->markAsReviewed(
                auth('employees')->id(),
                $this->reviewNotes
            );

            $this->toast()->success('Report approved successfully!')->send();
            $this->closeReviewModal();

        } catch (\Exception $e) {
            $this->toast()->error('Error approving report: ' . $e->getMessage())->send();
        }
    }

    public function rejectReport()
    {
        if (!$this->selectedReport) {
            $this->toast()->error('No report selected')->send();
            return;
        }

        if (empty($this->reviewNotes)) {
            $this->toast()->error('Please provide rejection notes')->send();
            return;
        }

        try {
            $this->selectedReport->update([
                'status' => 'rejected',
                'reviewed_by' => auth('employees')->id(),
                'reviewed_at' => now(),
                'review_notes' => $this->reviewNotes,
            ]);

            $this->toast()->success('Report rejected. Notes saved.')->send();
            $this->closeReviewModal();

        } catch (\Exception $e) {
            $this->toast()->error('Error rejecting report: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        $branchId = $this->b_id ?? current_branch_id();

        $query = DepartmentReport::query()
            ->with(['department', 'generatedBy', 'reviewedBy'])
            ->forBranch($branchId)
            ->whereIn('status', ['pending_review', 'reviewed', 'rejected']);

        if ($this->filterCategory !== 'all') {
            $query->byCategory($this->filterCategory);
        }

        if ($this->filterDepartment !== 'all') {
            $query->forDepartment($this->filterDepartment);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(15);

        $departments = \App\Models\Department::where('branch_id', $branchId)
            ->orderBy('name')
            ->get();

        $statusCounts = [
            'pending_review' => DepartmentReport::forBranch($branchId)
                ->where('status', 'pending_review')
                ->count(),
            'reviewed' => DepartmentReport::forBranch($branchId)
                ->where('status', 'reviewed')
                ->count(),
            'rejected' => DepartmentReport::forBranch($branchId)
                ->where('status', 'rejected')
                ->count(),
        ];

        return view('livewire.branch-dashboard.reporting-department.review-reports.index', [
            'reports' => $reports,
            'departments' => $departments,
            'statusCounts' => $statusCounts,
        ]);
    }
}
