<?php

namespace App\Livewire\BranchDashboard\Accounting\Report;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.branch-dashboard.accounting.report.index');
    }
}
