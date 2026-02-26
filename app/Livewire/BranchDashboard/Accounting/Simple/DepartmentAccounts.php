<?php

namespace App\Livewire\BranchDashboard\Accounting\Simple;

use App\Models\Department;
use App\Models\AuditLog;
use App\Models\BankAccount;
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
    public bool $bankAccountUsesGlFallback = false;

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
        $hasBankAccounts = BankAccount::active()
            ->where('branch_id', $this->b_id)
            ->exists();

        $this->validate([
            'department_id' => 'required|exists:departments,id',
            'revenue_account_id' => 'nullable|exists:gl_accounts,id',
            'tax_account_id' => 'nullable|exists:gl_accounts,id',
            'receivable_account_id' => 'nullable|exists:gl_accounts,id',
            'cash_account_id' => 'nullable|exists:gl_accounts,id',
            'bank_account_id' => $hasBankAccounts
                ? 'nullable|exists:bank_accounts,id'
                : 'nullable|exists:gl_accounts,id',
        ]);

        $branchId = $this->b_id ?? current_branch_id();

        $before = Department::where('branch_id', $branchId)
            ->find($this->department_id);

        // Debug: Log the values being saved
        \Log::info('DepartmentAccounts save()', [
            'department_id' => $this->department_id,
            'branch_id' => $branchId,
            'revenue_account_id' => $this->revenue_account_id,
            'tax_account_id' => $this->tax_account_id,
            'receivable_account_id' => $this->receivable_account_id,
            'cash_account_id' => $this->cash_account_id,
            'bank_account_id' => $this->bank_account_id,
        ]);

        $updateData = [
            'revenue_account_id' => $this->revenue_account_id,
            'tax_account_id' => $this->tax_account_id,
            'receivable_account_id' => $this->receivable_account_id,
            'cash_account_id' => $this->cash_account_id,
            'bank_account_id' => $this->bank_account_id,
        ];

        $updated = Department::where('branch_id', $branchId)
            ->where('id', $this->department_id)
            ->update($updateData);

        // Debug: Log the update result
        \Log::info('DepartmentAccounts update result', [
            'updated_rows' => $updated,
            'department_exists' => Department::where('branch_id', $branchId)->where('id', $this->department_id)->exists(),
        ]);

        if ($updated > 0) {
            $department = Department::where('branch_id', $branchId)
                ->find($this->department_id);

            $this->revenue_account_id = $department?->revenue_account_id;
            $this->tax_account_id = $department?->tax_account_id;
            $this->receivable_account_id = $department?->receivable_account_id;
            $this->cash_account_id = $department?->cash_account_id;
            $this->bank_account_id = $department?->bank_account_id;
        }

        AuditLog::create([
            'branch_id' => $branchId,
            'causer_type' => auth()->user()?->getMorphClass() ?? \App\Models\User::class,
            'causer_id' => auth()->id(),
            'auditable_type' => Department::class,
            'auditable_id' => $this->department_id,
            'setting_type' => 'accounting',
            'action' => 'department_accounts_update',
            'description' => 'Department account mappings update attempt',
            'old_values' => [
                'revenue_account_id' => $before?->revenue_account_id,
                'tax_account_id' => $before?->tax_account_id,
                'receivable_account_id' => $before?->receivable_account_id,
                'cash_account_id' => $before?->cash_account_id,
                'bank_account_id' => $before?->bank_account_id,
            ],
            'new_values' => [
                'revenue_account_id' => $this->revenue_account_id,
                'tax_account_id' => $this->tax_account_id,
                'receivable_account_id' => $this->receivable_account_id,
                'cash_account_id' => $this->cash_account_id,
                'bank_account_id' => $this->bank_account_id,
            ],
            'details' => [
                'department_id' => $this->department_id,
                'updated_rows' => $updated,
                'has_bank_accounts' => $hasBankAccounts,
                'bank_account_mode' => $this->bankAccountUsesGlFallback ? 'gl_fallback' : 'bank_accounts',
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $updated > 0 ? 'completed' : 'failed',
            'logged_at' => now(),
            'user_id' => auth()->id(),
        ]);

        $this->toast()->success('Department accounts updated.')->send();
    }

    public function render()
    {
        $departments = Department::where('branch_id', $this->b_id)
            ->where(function ($query) {
                $query->whereHas('category', function ($categoryQuery) {
                    $categoryQuery->whereIn('name', ['sales', 'production']);
                })
                    ->orWhere('name', 'like', '%inventory%');
            })
            ->with([
                'revenueAccount',
                'taxAccount',
                'receivableAccount',
                'cashAccount',
                'bankAccount',
            ])
            ->orderBy('name')
            ->get();

        $bankAccounts = BankAccount::active()
            ->where('branch_id', $this->b_id)
            ->orderBy('bank_name')
            ->get();

        $accounts = GlAccount::active()
            ->forBranch($this->b_id ?? current_branch_id())
            ->detailAccounts()
            ->orderBy('account_name')
            ->get();

        $this->bankAccountUsesGlFallback = $bankAccounts->isEmpty();

        return view('livewire.branch-dashboard.accounting.simple.department-accounts', [
            'departments' => $departments,
            'accounts' => $accounts,
            'bankAccountOptions' => $bankAccounts->isNotEmpty() ? $bankAccounts : $accounts,
            'bankAccountUsesGlFallback' => $this->bankAccountUsesGlFallback,
        ]);
    }
}
