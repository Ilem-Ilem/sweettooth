<?php

namespace App\Livewire\BranchDashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout("components.layouts.branch-dashboard")]
class Index extends Component
{
    public function render()
    {
        return view('livewire.branch-dashboard.index');
    }
}
