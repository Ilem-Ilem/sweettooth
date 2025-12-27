<?php

namespace App\Livewire\BranchDashboard\ReportingDepartment\SendToMD;

use App\Models\CompiledReport;
use App\Models\User;
use App\Services\Reports\ReportCompilationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
#[Title('Send Reports to MD')]
class Index extends Component
{
    use Interactions, WithPagination;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?User $selectedMdUser = null;

    public $showSendModal = false;

    public ?CompiledReport $reportToSend = null;

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

    public function sendToMD($reportId)
    {
        $this->reportToSend = CompiledReport::findOrFail($reportId);

        if (! $this->reportToSend->canBeSentToMD()) {
            $this->toast()->error('Report must be approved before sending to MD')->send();

            return;
        }

        // Get MD users (SuperAdmin users)
        $mdUsers = User::all();

        if ($mdUsers->isEmpty()) {
            $this->toast()->error('No MD users found in the system')->send();

            return;
        }

        // If only one MD user, send directly
        if ($mdUsers->count() === 1) {
            $this->selectedMdUser = $mdUsers->first()->id;
            $this->processSend();

            return;
        }

        $this->showSendModal = true;
    }

    public function processSend()
    {
        if (! $this->selectedMdUser) {
            $this->toast()->error('Please select an MD user')->send();

            return;
        }

        try {
            $compilationService = new ReportCompilationService;

            $compilationService->sendToMD($this->reportToSend, $this->selectedMdUser);

            $this->toast()->success('Report sent to MD successfully!')->send();
            $this->showSendModal = false;
            $this->reportToSend = null;
            $this->selectedMdUser = null;

        } catch (\Exception $e) {
            $this->toast()->error('Error sending report: '.$e->getMessage())->send();
        }
    }

    public function approveReport($reportId)
    {
        try {
            $report = CompiledReport::findOrFail($reportId);

            if (! $report->canBeApproved()) {
                $this->toast()->error('Report cannot be approved in its current state')->send();

                return;
            }

            $compilationService = new ReportCompilationService;
            $compilationService->approve($report, auth()->id());

            $this->toast()->success('Report approved successfully!')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error approving report: '.$e->getMessage())->send();
        }
    }

    public function render()
    {
        $compiledReports = CompiledReport::query()
            ->with(['compiledBy', 'approvedBy', 'mdUser'])
            ->forBranch($this->b_id ?? current_branch_id())
            ->orderBy('compilation_date', 'desc')
            ->paginate(10);

        $mdUsers = User::all();

        return view('livewire.branch-dashboard.reporting-department.send-to-md.index', [
            'compiledReports' => $compiledReports,
            'mdUsers' => $mdUsers,
        ]);
    }
}
