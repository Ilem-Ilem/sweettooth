<div class="space-y-6">
    <div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-zinc-50 p-6 shadow-sm dark:border-zinc-800 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-900/60">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Accounting</p>
                <h1 class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">Bank Accounts</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Manage operational bank accounts and keep balances aligned with GL.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="text-zinc-400">Search</span>
                    <input class="w-56 border-none bg-transparent p-0 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none dark:text-white" type="text" placeholder="Bank, number, type" wire:model.debounce.300ms="search" />
                </div>
                <button class="rounded-full border border-zinc-900 bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800 dark:border-white dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" type="button" wire:click="toggleForm">
                    {{ $showForm ? 'Close Form' : 'New Account' }}
                </button>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs uppercase tracking-wide text-zinc-500">Accounts</p>
                <p class="mt-2 text-xl font-semibold text-zinc-900 dark:text-white">{{ $rows->total() }}</p>
                <p class="text-xs text-zinc-500">Tracked bank accounts</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs uppercase tracking-wide text-zinc-500">Active</p>
                <p class="mt-2 text-xl font-semibold text-zinc-900 dark:text-white">{{ $rows->getCollection()->where('is_active', true)->count() }}</p>
                <p class="text-xs text-zinc-500">Visible in operations</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs uppercase tracking-wide text-zinc-500">Page Size</p>
                <p class="mt-2 text-xl font-semibold text-zinc-900 dark:text-white">{{ $rows->perPage() }}</p>
                <p class="text-xs text-zinc-500">Adjust in table</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <x-table
            :$headers
            :$rows
            striped
            paginate
            persist
            :filter="['quantity' => 'quantity', 'search' => 'search']"
            :quantity="[10, 25, 50, 100]"
        >
            @interact('column_bank_name', $row)
                <div class="font-semibold text-zinc-900 dark:text-white">{{ $row->bank_name }}</div>
                <div class="text-xs text-zinc-500">{{ $row->glAccount?->account_number }} · {{ $row->glAccount?->account_name }}</div>
            @endinteract

            @interact('column_account_number', $row)
                <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $row->account_number }}</span>
            @endinteract

            @interact('column_account_type', $row)
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                    {{ str_replace('_', ' ', $row->account_type) }}
                </span>
            @endinteract

            @interact('column_balance', $row)
                <span class="font-semibold text-zinc-900 dark:text-white">
                    {{ number_format($row->getCurrentBalance() ?? 0, 2) }}
                </span>
            @endinteract

            @interact('column_status', $row)
                @if ($row->is_active)
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">Active</span>
                @else
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Inactive</span>
                @endif
            @endinteract

            @interact('column_action', $row)
                <button class="rounded-full border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:border-blue-300 hover:bg-blue-50 dark:border-blue-900/50 dark:text-blue-200 dark:hover:bg-blue-900/30" type="button" wire:click="edit({{ $row->id }})">
                    Edit
                </button>
            @endinteract
        </x-table>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[2fr,1fr]">
        @if ($showForm)
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Add / Edit Bank Account</h2>
                    <p class="mt-1 text-xs text-zinc-500">Link each bank account to a GL asset account.</p>
                </div>
                @if ($editingId)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">Editing</span>
                @endif
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs text-zinc-500">Bank Name</label>
                    <input class="mt-1 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="text" wire:model="bank_name" />
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Account Number</label>
                    <input class="mt-1 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="text" wire:model="account_number" />
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Account Type</label>
                    <select class="mt-1 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" wire:model="account_type">
                        <option value="checking">Checking</option>
                        <option value="savings">Savings</option>
                        <option value="money_market">Money Market</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-zinc-500">GL Account</label>
                    <select class="mt-1 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" wire:model="gl_account_id">
                        <option value="">Select GL account</option>
                        @foreach ($glAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-zinc-500">Opening Balance</label>
                    <input class="mt-1 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="number" step="0.01" wire:model="opening_balance" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" />
                    <span class="text-xs text-zinc-600">Active</span>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button class="rounded-full bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800" type="button" wire:click="save">Save Account</button>
                <button class="rounded-full border border-zinc-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-zinc-700 hover:border-zinc-300" type="button" wire:click="resetForm">Reset</button>
            </div>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-6 text-sm text-zinc-600 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold">Account form hidden</p>
                        <p class="text-xs text-zinc-500">Click “New Account” to add or edit a bank account.</p>
                    </div>
                    <button class="rounded-full border border-zinc-900 bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800 dark:border-white dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" type="button" wire:click="toggleForm">
                        Open Form
                    </button>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">What’s the difference?</h2>
                <div class="mt-3 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900/60">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Bank Accounts</p>
                        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                            Real-world accounts used for deposits, payments, and reconciliation.
                        </p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900/60">
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Chart of Accounts</p>
                        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                            The full list of GL accounts used for categorizing every transaction.
                        </p>
                    </div>
                    <div class="text-xs text-zinc-500">
                        Bank accounts map to one or more GL asset accounts for reporting and reconciliation.
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">Reconciliation</h2>
                <p class="mt-2 text-xs text-zinc-500">Start a reconciliation session for a bank account.</p>
                <div class="mt-3">
                    <a class="text-sm text-blue-600" href="{{ route('branch-dashboard.accounting.bank-reconciliation-manage', ['b_id' => request()->query('b_id')]) }}">Open Reconciliation</a>
                </div>
            </div>
        </div>
    </div>
</div>
