<div x-show="activeTab === 'financial'" class="tab-content">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Salary</label>
                <p class="text-zinc-900 dark:text-white font-medium text-lg">
                    {{ $employee->salary ? '$' . number_format($employee->salary, 2) : '—' }}
                </p>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Hourly Rate</label>
                <p class="text-zinc-900 dark:text-white font-medium">
                    {{ $employee->hourly_rate ? '$' . number_format($employee->hourly_rate, 2) : '—' }}
                </p>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Tax ID</label>
                <p class="text-zinc-900 dark:text-white font-medium">{{ $employee->tax_id ?? '—' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Bank Account</label>
                <p class="text-zinc-900 dark:text-white font-medium">
                    {{ $employee->bank_account ? $employee->bank_account : '—' }}
                </p>
            </div>
        </div>
    </div>
</div>