<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Callbacks;

use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class CreateStockCallback extends CreateDispatchCallback
{
    public function mount()
    {
        $this->sourceMode = 'stock';
        $this->lockSource = true;
        $this->pageTitle = 'Stock Callbacks';
        $this->pageSubtitle = 'Return products back to production from product stock';

        parent::mount();
    }
}
