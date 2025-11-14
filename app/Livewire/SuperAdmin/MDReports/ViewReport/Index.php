<?php

namespace App\Livewire\SuperAdmin\MDReports\ViewReport;

use App\Models\CompiledReport;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public CompiledReport $report;
    public $feedback = '';

    public function mount($id)
    {
        $this->report = CompiledReport::with([
            'branch',
            'compiledBy',
            'approvedBy',
            'departmentReports.department'
        ])->findOrFail($id);

        $this->feedback = $this->report->md_feedback ?? '';
    }

    public function submitFeedback()
    {
        $this->validate([
            'feedback' => 'nullable|string|max:1000',
        ]);

        try {
            $this->report->markAsReviewedByMD($this->feedback);

            $this->toast()->success('Feedback submitted successfully')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error submitting feedback: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.super-admin.md-reports.view-report.index');
    }
}
