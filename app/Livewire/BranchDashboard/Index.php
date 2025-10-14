<?php

namespace App\Livewire\BranchDashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Employee;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.branch-dashboard.index',[
            'employee_total'=> Employee::count()
        ]);
    }
}
