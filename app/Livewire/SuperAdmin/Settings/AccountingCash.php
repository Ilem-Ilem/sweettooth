<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalAccountingCash;

class AccountingCash extends Component
{
    public $expensesAdd = true;
    public $expensesEdit = true;
    public $cashBank = true;
    public $profitLossReports = 'by_date';
    public $accountingEntries = 'auto';

    public function mount()
    {
        $settings = GlobalAccountingCash::first();
        
        if ($settings) {
            $expenses = $settings->expenses_categories ?? [];
            $this->expensesAdd = in_array('add', $expenses);
            $this->expensesEdit = in_array('edit', $expenses);
            
            $this->cashBank = $settings->cash_bank === 'enabled';
            $this->profitLossReports = $settings->profit_loss_reports ?? 'by_date';
            $this->accountingEntries = $settings->accounting_entries ?? 'auto';
        }
    }

    public function save()
    {
        $expenses = [];
        if ($this->expensesAdd) $expenses[] = 'add';
        if ($this->expensesEdit) $expenses[] = 'edit';

        GlobalAccountingCash::updateOrCreate(
            ['id' => 1],
            [
                'expenses_categories' => $expenses,
                'cash_bank' => $this->cashBank ? 'enabled' : 'disabled',
                'profit_loss_reports' => $this->profitLossReports,
                'accounting_entries' => $this->accountingEntries,
            ]
        );

        session()->flash('message', 'Accounting & Cash settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.accounting-cash');
    }
}
