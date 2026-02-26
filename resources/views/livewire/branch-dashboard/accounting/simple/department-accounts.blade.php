<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Department Accounts</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Assign GL accounts to departments.</p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Department</label>
                <select wire:model="department_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Revenue Account</label>
                <select wire:model="revenue_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('revenue_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Tax Account</label>
                <select wire:model="tax_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('tax_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Receivable Account</label>
                <select wire:model="receivable_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('receivable_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Cash Account</label>
                <select wire:model="cash_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('cash_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-1">Bank Account</label>
                <select wire:model="bank_account_id" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2">
                    <option value="">Select Account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->account_name }}</option>
                    @endforeach
                </select>
                @error('bank_account_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" wire:click="save" class="px-4 py-2 rounded-lg bg-zinc-900 text-white hover:bg-zinc-800">
                Save
            </button>
        </div>
    </div>
</div>
