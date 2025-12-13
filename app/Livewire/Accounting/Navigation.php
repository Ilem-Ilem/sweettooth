<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\Attributes\Layout;


#[Layout('components.layouts.app.branch-dashboard')]
class Navigation extends Component
{
    public $activeItem = null;
    public $user = null;
    public $isSuperAdminOrMd = false;

    public function mount()
    {
        $this->user = auth()->user() ?? auth()->guard('employees')->user();
        
        // Check if user is Super Admin or MD
        $this->isSuperAdminOrMd = $this->user && 
            ($this->user->hasRole(['Super Admin', 'MD'], null));
    }

    public function render()
    {
        return view('livewire.accounting.navigation', [
            'canAccessDashboard' => $this->user && $this->user->hasPermissionTo('access_accounting'),
            'canViewReports' => $this->user && $this->user->hasPermissionTo('view_financial_reports'),
            'canManageAccounts' => $this->user && $this->user->hasPermissionTo('manage_accounts'),
            'canManagePeriods' => $this->user && $this->user->hasPermissionTo('manage_periods'),
            'canCreateJournalEntries' => $this->user && $this->user->hasPermissionTo('create_journal_entries'),
            'isSuperAdminOrMd' => $this->isSuperAdminOrMd,
        ]);
    }
}
