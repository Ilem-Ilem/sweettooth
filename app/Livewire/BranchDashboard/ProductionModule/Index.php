<?php

namespace App\Livewire\BranchDashboard\ProductionModule;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.branch-dashboard.production-module.index');
    }
}
