<?php

namespace App\Livewire\BranchDashboard\Accounting\Simple;

use App\Models\Department;
use App\Models\GlAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class DepartmentAccounts extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?int $department_id = null;
    public ?int $revenue_account_id = null;
    public ?int $tax_account_id = null;
    public ?int $receivable_account_id = null;
    public ?int $cash_account_id = null;
    public ?int $bank_account_id = null;

    public function mount(): void
    {
        $this->b_id = $this->b_id ?? current_branch_id();
    }

    public function updatedDepartmentId($value): void
    {
        $department = Department::where('branch_id', $this->b_id)
            ->find($value);

        $this->revenue_account_id = $department?->revenue_account_id;
        $this->tax_account_id = $department?->tax_account_id;
        $this->receivable_account_id = $department?->receivable_account_id;
        $this->cash_account_id = $department?->cash_account_id;
        $this->bank_account_id = $department?->bank_account_id;
    }

    public function save(): void
    {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'revenue_account_id' => 'nullable|exists:gl_accounts,id',
            'tax_account_id' => 'nullable|exists:gl_accounts,id',
            'receivable_account_id' => 'nullable|exists:gl_accounts,id',
            'cash_account_id' => 'nullable|exists:gl_accounts,id',
            'bank_account_id' => 'nullable|exists:gl_accounts,id',
        ]);

        $department = Department::where('branch_id', $this->b_id)
            ->findOrFail($this->department_id);

        $department->update([
            'revenue_account_id' => $this->revenue_account_id,
            'tax_account_id' => $this->tax_account_id,
            'receivable_account_id' => $this->receivable_account_id,
            'cash_account_id' => $this->cash_account_id,
            'bank_account_id' => $this->bank_account_id,
        ]);

        $this->toast()->success('Department accounts updated.')->send();
    }

    public function render()
    {
        $departments = Department::where('branch_id', $this->b_id)
            ->orderBy('name')
            ->get();

        $accounts = GlAccount::active()
            ->detailAccounts()
            ->orderBy('account_name')
            ->get();

        return view('livewire.branch-dashboard.accounting.simple.department-accounts', [
            'departments' => $departments,
            'accounts' => $accounts,
        ]);
    }
}
