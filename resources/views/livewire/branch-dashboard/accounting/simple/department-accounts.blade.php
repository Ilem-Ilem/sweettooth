<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Department Accounts</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Assign GL accounts to departments.</p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
        {{-- Debug Output --}}
        <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-800 rounded text-xs font-mono">
            <strong>Debug Values:</strong><br>
            dept_id: {{ var_export($department_id, true) }}<br>
            revenue: {{ var_export($revenue_account_id, true) }}<br>
            tax: {{ var_export($tax_account_id, true) }}<br>
            receivable: {{ var_export($receivable_account_id, true) }}<br>
            cash: {{ var_export($cash_account_id, true) }}<br>
            bank: {{ var_export($bank_account_id, true) }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Department</label>
                <select wire:model.live="department_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Revenue Account</label>
                <select wire:model.live="revenue_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('revenue_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Tax Account</label>
                <select wire:model.live="tax_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('tax_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Receivable Account</label>
                <select wire:model.live="receivable_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('receivable_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Cash Account</label>
                <select wire:model.live="cash_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('cash_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Bank Account</label>
                <select wire:model.live="bank_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">{{ $bankAccountUsesGlFallback ? 'Select GL Account' : 'Select Bank Account' }}</option>
                    @foreach ($bankAccountOptions as $account)
                        @if ($bankAccountUsesGlFallback)
                            <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                        @else
                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }}</option>
                        @endif
                    @endforeach
                </select>
                @if ($bankAccountUsesGlFallback)
                    <p class="mt-1 text-xs text-amber-600">No bank accounts found. Showing GL accounts as fallback.</p>
                @endif
                @error('bank_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button type="button" wire:click="save" wire:loading.attr="disabled" class="px-4 py-2 rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>

    {{-- Helper Section --}}
    <div x-data="{ open: false }" class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        <button @click="open = !open" type="button" class="flex w-full items-center justify-between px-5 py-4 text-left">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-zinc-900 dark:text-white">Field Descriptions & Usage Guide</span>
            </div>
            <svg :class="{ 'rotate-180': open }" class="h-5 w-5 transform text-zinc-500 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="border-t border-zinc-200 dark:border-zinc-800 px-5 pb-5">
            <div class="mt-4 space-y-6">
                {{-- Department --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Department</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Select the department you want to configure with specific General Ledger accounts. Each department can have different accounting mappings based on their operational needs. This links the department to its financial accounts for proper revenue and expense tracking.
                    </p>
                </div>

                {{-- Revenue Account --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Revenue Account</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        The GL account where all sales and income generated by this department are recorded. This is typically an income or revenue account (4000-4999 range). When the department makes sales, the revenue is credited to this account. Used for tracking departmental performance and revenue allocation in financial reports.
                    </p>
                </div>

                {{-- Tax Account --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Tax Account</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        The GL account for tracking tax liabilities collected by this department (e.g., VAT, sales tax). This is typically a liability account (2000-2999 range). Taxes collected from customers are recorded here until remitted to tax authorities. Ensures proper tax compliance and reporting for the department's transactions.
                    </p>
                </div>

                {{-- Receivable Account --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Receivable Account</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        The GL account for tracking money owed to the department by customers (accounts receivable). This is typically an asset account (1000-1999 range). Used when sales are made on credit or when customers owe payments. Helps monitor outstanding debts and cash flow for the department.
                    </p>
                </div>

                {{-- Cash Account --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Cash Account</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        The GL account for tracking physical cash transactions handled by this department. This is typically an asset account (1000-1999 range). Records cash sales, cash refunds, and petty cash movements. Essential for cash management and reconciliation processes specific to the department.
                    </p>
                </div>

                {{-- Bank Account --}}
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Bank Account</h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        The GL account for tracking electronic payments, transfers, and bank deposits for this department. This is typically an asset account (1000-1999 range). Records card payments, bank transfers, checks, and other non-cash transactions. Used for bank reconciliation and tracking electronic fund flows.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
        <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Department Account Mappings</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-900/60 dark:text-zinc-400">
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-3 py-2">Department</th>
                        <th class="px-3 py-2">Revenue</th>
                        <th class="px-3 py-2">Tax</th>
                        <th class="px-3 py-2">Receivable</th>
                        <th class="px-3 py-2">Cash</th>
                        <th class="px-3 py-2">Bank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($departments as $dept)
                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                            <td class="px-3 py-2 font-medium text-zinc-900 dark:text-white">{{ $dept->name }}</td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                                {{ $dept->revenueAccount?->account_number ? $dept->revenueAccount->account_number . ' - ' . $dept->revenueAccount->account_name : '-' }}
                            </td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                                {{ $dept->taxAccount?->account_number ? $dept->taxAccount->account_number . ' - ' . $dept->taxAccount->account_name : '-' }}
                            </td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                                {{ $dept->receivableAccount?->account_number ? $dept->receivableAccount->account_number . ' - ' . $dept->receivableAccount->account_name : '-' }}
                            </td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                                {{ $dept->cashAccount?->account_number ? $dept->cashAccount->account_number . ' - ' . $dept->cashAccount->account_name : '-' }}
                            </td>
                            <td class="px-3 py-2 text-zinc-700 dark:text-zinc-300">
                                @if ($dept->bankAccount)
                                    {{ $dept->bankAccount->bank_name }} - {{ $dept->bankAccount->account_number }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-4 text-zinc-500" colspan="6">No departments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
