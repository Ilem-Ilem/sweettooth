<?php

namespace App\Livewire\BranchDashboard\Accounting\Simple;

use App\Models\GlAccount;
use App\Models\ApprovalAuditRequest;
use App\Services\NotificationRecipientService;
use App\Notifications\ApprovalRequestCreated;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app.branch-dashboard')]
class ChartOfAccounts extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public string $search = '';
    public string $type = 'all';
    public int $perPage = 25;
    public bool $showCreate = false;

    public string $account_number = '';
    public string $account_name = '';
    public string $account_type = '';
    public ?string $account_category = null;
    public ?string $description = null;
    public string $normal_balance = '';
    public bool $is_header = false;
    public ?int $parent_account_id = null;
    public bool $is_active = true;
    public bool $allow_manual_entry = true;
    public ?float $opening_balance_amount = null;
    public string $opening_balance_type = '';
    public ?string $opening_balance_date = null;

    public bool $showEditOpening = false;
    public ?int $edit_account_id = null;
    public ?float $edit_opening_balance_amount = null;
    public string $edit_opening_balance_type = '';
    public ?string $edit_opening_balance_date = null;

    public bool $showEditBalance = false;
    public ?int $edit_balance_account_id = null;
    public ?float $edit_balance_amount = null;
    public string $edit_balance_type = '';

    public function mount(): void
    {
        $this->b_id = $this->b_id ?? current_branch_id();
    }

    public function toggleCreate(): void
    {
        $this->showCreate = ! $this->showCreate;
    }

    public function createAccount(): void
    {
        $branchId = $this->b_id ?? current_branch_id();

        $this->validate([
            'account_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('gl_accounts', 'account_number')->where('branch_id', $branchId),
            ],
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:asset,liability,equity,revenue,cost_of_goods_sold,expense,tax',
            'normal_balance' => 'nullable|string|in:debit,credit',
            'parent_account_id' => 'nullable|integer|exists:gl_accounts,id',
            'opening_balance_amount' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'nullable|string|in:debit,credit',
            'opening_balance_date' => 'nullable|date',
        ]);

        $normalBalance = $this->normal_balance;
        if ($normalBalance === '') {
            $normalBalance = in_array($this->account_type, ['asset', 'expense', 'cost_of_goods_sold'], true)
                ? 'debit'
                : 'credit';
        }

        $isHeader = (bool) $this->is_header;

        $openingDebit = 0;
        $openingCredit = 0;
        if ($this->opening_balance_amount !== null && $this->opening_balance_amount > 0) {
            $openingType = $this->opening_balance_type ?: $normalBalance;
            if ($openingType === 'credit') {
                $openingCredit = $this->opening_balance_amount;
            } else {
                $openingDebit = $this->opening_balance_amount;
            }
        }

        GlAccount::create([
            'branch_id' => $branchId,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'account_type' => $this->account_type,
            'account_category' => $this->account_category,
            'description' => $this->description,
            'opening_debit' => $openingDebit,
            'opening_credit' => $openingCredit,
            'opening_balance_date' => $this->opening_balance_date,
            'debit_balance' => $openingDebit,
            'credit_balance' => $openingCredit,
            'normal_balance' => $normalBalance,
            'is_header' => $isHeader,
            'parent_account_id' => $this->parent_account_id,
            'is_active' => $this->is_active,
            'allow_manual_entry' => $isHeader ? false : $this->allow_manual_entry,
        ]);

        $this->reset([
            'account_number',
            'account_name',
            'account_type',
            'account_category',
            'description',
            'normal_balance',
            'is_header',
            'parent_account_id',
            'is_active',
            'allow_manual_entry',
            'opening_balance_amount',
            'opening_balance_type',
            'opening_balance_date',
        ]);

        $this->showCreate = false;
        $this->resetPage();
        session()->flash('success', 'GL account created.');
    }

    public function openEditOpening(int $accountId): void
    {
        $account = GlAccount::forBranch($this->b_id ?? current_branch_id())->findOrFail($accountId);
        $this->edit_account_id = $account->id;
        $this->edit_opening_balance_date = $account->opening_balance_date?->format('Y-m-d');

        if ((float) $account->opening_credit > 0) {
            $this->edit_opening_balance_type = 'credit';
            $this->edit_opening_balance_amount = (float) $account->opening_credit;
        } else {
            $this->edit_opening_balance_type = 'debit';
            $this->edit_opening_balance_amount = (float) $account->opening_debit;
        }

        $this->showEditOpening = true;
    }

    public function closeEditOpening(): void
    {
        $this->showEditOpening = false;
        $this->edit_account_id = null;
        $this->edit_opening_balance_amount = null;
        $this->edit_opening_balance_type = '';
        $this->edit_opening_balance_date = null;
    }

    public function openEditBalance(int $accountId): void
    {
        $account = GlAccount::forBranch($this->b_id ?? current_branch_id())->findOrFail($accountId);
        $this->edit_balance_account_id = $account->id;

        if ((float) $account->credit_balance > 0) {
            $this->edit_balance_type = 'credit';
            $this->edit_balance_amount = (float) $account->credit_balance;
        } else {
            $this->edit_balance_type = 'debit';
            $this->edit_balance_amount = (float) $account->debit_balance;
        }

        $this->showEditBalance = true;
    }

    public function closeEditBalance(): void
    {
        $this->showEditBalance = false;
        $this->edit_balance_account_id = null;
        $this->edit_balance_amount = null;
        $this->edit_balance_type = '';
    }

    public function saveBalanceOverride(): void
    {
        $this->validate([
            'edit_balance_account_id' => 'required|exists:gl_accounts,id',
            'edit_balance_amount' => 'required|numeric|min:0',
            'edit_balance_type' => 'required|string|in:debit,credit',
        ]);

        $account = GlAccount::forBranch($this->b_id ?? current_branch_id())->findOrFail($this->edit_balance_account_id);

        $newDebit = $this->edit_balance_type === 'debit' ? (float) $this->edit_balance_amount : 0.0;
        $newCredit = $this->edit_balance_type === 'credit' ? (float) $this->edit_balance_amount : 0.0;

        $user = auth()->user();
        if (
            is_super_admin()
            || ($user && $user->hasAnyRole(['Super Admin', 'Managing Director', 'Admin', 'Accounting Manager']))
        ) {
            $account->update([
                'debit_balance' => $newDebit,
                'credit_balance' => $newCredit,
            ]);
            session()->flash('success', 'Account balance updated.');
            $this->closeEditBalance();
            return;
        }

        $payload = [
            'id' => $account->id,
            'debit_balance' => $newDebit,
            'credit_balance' => $newCredit,
        ];

        $approval = ApprovalAuditRequest::create([
            'branch_id' => $this->b_id ?? current_branch_id(),
            'requester_id' => auth()->id(),
            'requester_type' => auth()->user()?->getMorphClass() ?? \App\Models\User::class,
            'action' => 'accounting:gl_account_update',
            'description' => 'Override current balance for GL account ' . $account->account_number,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        $recipients = app(NotificationRecipientService::class)->usersForRoles(
            array_merge(
                config('notifications.roles.admin', []),
                config('notifications.roles.super_admin', []),
                config('notifications.roles.hr', [])
            ),
            $this->b_id ?? current_branch_id()
        );
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new ApprovalRequestCreated($approval));
        }

        session()->flash('success', 'Balance override submitted for approval.');
        $this->closeEditBalance();
    }

    public function saveOpeningBalance(): void
    {
        $this->validate([
            'edit_account_id' => 'required|exists:gl_accounts,id',
            'edit_opening_balance_amount' => 'nullable|numeric|min:0',
            'edit_opening_balance_type' => 'nullable|string|in:debit,credit',
            'edit_opening_balance_date' => 'nullable|date',
        ]);

        $account = GlAccount::forBranch($this->b_id ?? current_branch_id())->findOrFail($this->edit_account_id);

        $newDebit = 0.0;
        $newCredit = 0.0;
        $amount = (float) ($this->edit_opening_balance_amount ?? 0);
        $type = $this->edit_opening_balance_type ?: $account->normal_balance;
        if ($amount > 0) {
            if ($type === 'credit') {
                $newCredit = $amount;
            } else {
                $newDebit = $amount;
            }
        }

        $deltaDebit = $newDebit - (float) $account->opening_debit;
        $deltaCredit = $newCredit - (float) $account->opening_credit;

        $user = auth()->user();
        if (
            is_super_admin()
            || ($user && $user->hasAnyRole(['Super Admin', 'Managing Director', 'Admin', 'Accounting Manager']))
        ) {
            $account->update([
                'opening_debit' => $newDebit,
                'opening_credit' => $newCredit,
                'opening_balance_date' => $this->edit_opening_balance_date,
                'debit_balance' => (float) $account->debit_balance + $deltaDebit,
                'credit_balance' => (float) $account->credit_balance + $deltaCredit,
            ]);

            session()->flash('success', 'Opening balance updated.');
            $this->closeEditOpening();
            return;
        }

        $payload = [
            'id' => $account->id,
            'opening_debit' => $newDebit,
            'opening_credit' => $newCredit,
            'opening_balance_date' => $this->edit_opening_balance_date,
            'debit_balance' => (float) $account->debit_balance + $deltaDebit,
            'credit_balance' => (float) $account->credit_balance + $deltaCredit,
        ];

        $approval = ApprovalAuditRequest::create([
            'branch_id' => $this->b_id ?? current_branch_id(),
            'requester_id' => auth()->id(),
            'requester_type' => auth()->user()?->getMorphClass() ?? \App\Models\User::class,
            'action' => 'accounting:gl_account_update',
            'description' => 'Update opening balance for GL account ' . $account->account_number,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        $recipients = app(NotificationRecipientService::class)->usersForRoles(
            array_merge(
                config('notifications.roles.admin', []),
                config('notifications.roles.super_admin', []),
                config('notifications.roles.hr', [])
            ),
            $this->b_id ?? current_branch_id()
        );
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new ApprovalRequestCreated($approval));
        }

        session()->flash('success', 'Opening balance update submitted for approval.');
        $this->closeEditOpening();
    }

    public function render()
    {
        $query = GlAccount::forBranch($this->b_id ?? current_branch_id());

        if ($this->type !== 'all') {
            $query->where('account_type', $this->type);
        }

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery->where('account_number', 'like', '%' . $this->search . '%')
                    ->orWhere('account_name', 'like', '%' . $this->search . '%');
            });
        }

        $accounts = $query->orderBy('account_number')->paginate($this->perPage);

        return view('livewire.branch-dashboard.accounting.simple.chart-of-accounts', [
            'accounts' => $accounts,
            'type' => $this->type,
            'search' => $this->search,
            'headers' => GlAccount::forBranch($this->b_id ?? current_branch_id())
                ->where('is_header', true)
                ->orderBy('account_number')
                ->get(['id', 'account_number', 'account_name']),
        ]);
    }
}
