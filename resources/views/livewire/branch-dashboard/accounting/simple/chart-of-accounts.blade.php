<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Chart of Accounts</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Search, filter, and review GL accounts.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="toggleCreate"
                class="rounded-full border border-zinc-900 bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800 dark:border-white dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
            >
                {{ $showCreate ? 'Close' : 'New Account' }}
            </button>
            <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <span class="text-zinc-400">Search</span>
                <input class="w-52 border-none bg-transparent p-0 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none dark:text-white" type="text" placeholder="Account number or name" wire:model.debounce.300ms="search" />
            </div>
            <select class="rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:outline-none dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200" wire:model="type">
                <option value="all">All Types</option>
                <option value="asset">Asset</option>
                <option value="liability">Liability</option>
                <option value="equity">Equity</option>
                <option value="revenue">Revenue</option>
                <option value="cost_of_goods_sold">COGS</option>
                <option value="expense">Expense</option>
                <option value="tax">Tax</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Showing</p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                {{ $accounts->total() }} accounts
            </p>
            <p class="mt-1 text-xs text-zinc-500">Filtered by {{ $type === 'all' ? 'all types' : $type }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Active Filter</p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                {{ $search === '' ? 'None' : 'Search applied' }}
            </p>
            <p class="mt-1 text-xs text-zinc-500">Use search to narrow the list.</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Per Page</p>
            <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">{{ $accounts->perPage() }}</p>
            <p class="mt-1 text-xs text-zinc-500">Pagination enabled</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($showCreate)
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Create GL Account</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Account Number</label>
                    <input wire:model.defer="account_number" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="text" placeholder="e.g. 4010" />
                    @error('account_number') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Account Name</label>
                    <input wire:model.defer="account_name" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="text" placeholder="e.g. Sales Revenue" />
                    @error('account_name') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Account Type</label>
                    <select wire:model.defer="account_type" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <option value="">Select type</option>
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="revenue">Revenue</option>
                        <option value="cost_of_goods_sold">COGS</option>
                        <option value="expense">Expense</option>
                        <option value="tax">Tax</option>
                    </select>
                    @error('account_type') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Normal Balance</label>
                    <select wire:model.defer="normal_balance" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <option value="">Auto</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                    @error('normal_balance') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Parent Account (Optional)</label>
                    <select wire:model.defer="parent_account_id" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <option value="">None</option>
                        @foreach ($headers as $header)
                            <option value="{{ $header->id }}">{{ $header->account_number }} - {{ $header->account_name }}</option>
                        @endforeach
                    </select>
                    @error('parent_account_id') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Description</label>
                    <textarea wire:model.defer="description" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" rows="3" placeholder="Optional description"></textarea>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Amount</label>
                    <input wire:model.defer="opening_balance_amount" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="number" step="0.01" placeholder="0.00" />
                    @error('opening_balance_amount') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Type</label>
                    <select wire:model.defer="opening_balance_type" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <option value="">Auto (Normal)</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                    @error('opening_balance_type') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Date</label>
                    <input wire:model.defer="opening_balance_date" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="date" />
                    @error('opening_balance_date') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <label class="inline-flex items-center gap-2">
                    <input wire:model.defer="is_header" type="checkbox" class="rounded border-zinc-300 text-zinc-900">
                    Header Account
                </label>
                <label class="inline-flex items-center gap-2">
                    <input wire:model.defer="is_active" type="checkbox" class="rounded border-zinc-300 text-zinc-900">
                    Active
                </label>
                <label class="inline-flex items-center gap-2">
                    <input wire:model.defer="allow_manual_entry" type="checkbox" class="rounded border-zinc-300 text-zinc-900">
                    Allow Manual Entry
                </label>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button wire:click="createAccount" class="rounded-full bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800">
                    Create Account
                </button>
                <button wire:click="toggleCreate" class="rounded-full border border-zinc-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-zinc-700 hover:border-zinc-300">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/60 dark:text-zinc-400">
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-4 py-3">Number</th>
                        <th class="px-4 py-3">Account</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Actions</th>
                        <th class="px-4 py-3 text-right">Balance</th>
                        <th class="px-4 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($accounts as $account)
                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $account->account_number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $account->account_name }}</div>
                                <div class="text-xs text-zinc-500">{{ $account->description ?? 'No description' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs uppercase tracking-wide text-zinc-500">{{ str_replace('_', ' ', $account->account_type) }}</td>
                            <td class="px-4 py-3">
                                <button
                                    wire:click="openEditOpening({{ $account->id }})"
                                    class="text-xs font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                                >
                                    Edit Opening
                                </button>
                                <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                <button
                                    wire:click="openEditBalance({{ $account->id }})"
                                    class="text-xs font-semibold text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                                >
                                    Override Balance
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-zinc-900 dark:text-white">
                                {{ number_format($account->getBalance(), 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($account->is_active)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">Active</span>
                                @else
                                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-zinc-500" colspan="4">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-800">
            {{ $accounts->links() }}
        </div>
    </div>

    @if ($showEditOpening)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Edit Opening Balance</h2>
                    <button wire:click="closeEditOpening" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">✕</button>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Amount</label>
                        <input wire:model.defer="edit_opening_balance_amount" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="number" step="0.01" />
                        @error('edit_opening_balance_amount') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Type</label>
                        <select wire:model.defer="edit_opening_balance_type" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <option value="">Auto (Normal)</option>
                            <option value="debit">Debit</option>
                            <option value="credit">Credit</option>
                        </select>
                        @error('edit_opening_balance_type') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Opening Balance Date</label>
                        <input wire:model.defer="edit_opening_balance_date" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="date" />
                        @error('edit_opening_balance_date') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="closeEditOpening" class="rounded-full border border-zinc-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-zinc-700 hover:border-zinc-300">Cancel</button>
                    <button wire:click="saveOpeningBalance" class="rounded-full bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800">Submit</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showEditBalance)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Override Current Balance</h2>
                    <button wire:click="closeEditBalance" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">✕</button>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Balance Amount</label>
                        <input wire:model.defer="edit_balance_amount" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900" type="number" step="0.01" />
                        @error('edit_balance_amount') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Balance Type</label>
                        <select wire:model.defer="edit_balance_type" class="mt-2 w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <option value="">Select</option>
                            <option value="debit">Debit</option>
                            <option value="credit">Credit</option>
                        </select>
                        @error('edit_balance_type') <div class="mt-1 text-xs text-rose-500">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button wire:click="closeEditBalance" class="rounded-full border border-zinc-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-zinc-700 hover:border-zinc-300">Cancel</button>
                    <button wire:click="saveBalanceOverride" class="rounded-full bg-zinc-900 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-zinc-800">Submit</button>
                </div>
            </div>
        </div>
    @endif
</div>
