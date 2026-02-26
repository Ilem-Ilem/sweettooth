<div class="space-y-6">
    <div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-zinc-50 p-6 shadow-sm dark:border-zinc-800 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-900/60">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Accounting</p>
                <h1 class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">Posting Approvals</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Approve draft GL entries grouped by transaction.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="text-zinc-400">Search</span>
                    <input class="w-56 border-none bg-transparent p-0 text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none dark:text-white" type="text" placeholder="Reference, ID, description" wire:model.debounce.300ms="search" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <button class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ $transactionType === 'sales' ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' }}"
                wire:click="changeTransactionType('sales')">Sales</button>
        <button class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ $transactionType === 'purchases' ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' }}"
                wire:click="changeTransactionType('purchases')">Purchases</button>
        <button class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ $transactionType === 'payments' ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' }}"
                wire:click="changeTransactionType('payments')">Payments</button>
        <button class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ $transactionType === 'purchase_payments' ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' }}"
                wire:click="changeTransactionType('purchase_payments')">Purchase Payments</button>
        <button class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ $transactionType === 'adjustments' ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200' }}"
                wire:click="changeTransactionType('adjustments')">Adjustments</button>
    </div>

    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/30 dark:text-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-900/30 dark:text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <x-table
            :headers="[
                ['index' => 'reference', 'label' => 'Reference'],
                ['index' => 'type', 'label' => 'Type'],
                ['index' => 'lines', 'label' => 'Lines'],
                ['index' => 'total', 'label' => 'Totals'],
                ['index' => 'date', 'label' => 'Date'],
                ['index' => 'action', 'label' => 'Action', 'display' => true],
            ]"
            :rows="$rows"
            striped
            paginate
            persist
            :filter="['quantity' => 'quantity', 'search' => 'search']"
            :quantity="[10, 25, 50, 100]"
        >
            @interact('column_reference', $row)
                <div class="flex flex-col">
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $row->reference_number ?? ('#' . $row->reference_id) }}</span>
                    <span class="text-xs text-zinc-500">{{ $row->reference_id }}</span>
                </div>
            @endinteract

            @interact('column_type', $row)
                <span class="text-sm text-zinc-700 dark:text-zinc-200">{{ class_basename($row->reference_type) }}</span>
            @endinteract

            @interact('column_lines', $row)
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-200">{{ $row->line_count }}</span>
            @endinteract

            @interact('column_total', $row)
                <div class="text-sm text-zinc-700 dark:text-zinc-200">
                    <div>Debit: <span class="font-semibold">{{ number_format($row->total_debit ?? 0, 2) }}</span></div>
                    <div>Credit: <span class="font-semibold">{{ number_format($row->total_credit ?? 0, 2) }}</span></div>
                </div>
            @endinteract

            @interact('column_date', $row)
                <span class="text-zinc-600 dark:text-zinc-300">{{ optional($row->entry_date)->format('Y-m-d H:i') }}</span>
            @endinteract

            @interact('column_action', $row)
                <div class="flex items-center gap-2">
                    <button class="rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800" type="button"
                            wire:click="viewGroup('{{ $row->reference_type }}', {{ $row->reference_id }})">
                        View
                    </button>
                    <button class="rounded-full border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-200 dark:hover:bg-emerald-900/30" type="button"
                            wire:click="approveGroup('{{ $row->reference_type }}', {{ $row->reference_id }})">
                        Approve
                    </button>
                </div>
            @endinteract
        </x-table>
    </div>

    <div
        x-data="{ show: @entangle('showDetail') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="show = false"
    >
        <div
            x-show="show"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40"
            @click="$wire.closeDetail()"
        ></div>

        <div
            x-show="show"
            x-transition:enter="transform transition ease-in-out duration-200"
            x-transition:enter-start="translate-y-4 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-4 opacity-0"
            class="fixed inset-x-4 top-16 mx-auto w-full max-w-3xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
        >
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Draft Entries</p>
                    <h3 class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ $detail['type'] ?? 'Reference' }} {{ $detail['reference'] ?? '' }}
                    </h3>
                </div>
                <button class="rounded-full border border-zinc-200 px-3 py-1 text-xs font-semibold text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800" type="button" wire:click="closeDetail">
                    Close
                </button>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-zinc-200 text-left text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700">
                        <tr>
                            <th class="py-2 pr-4">Account</th>
                            <th class="py-2 pr-4">Description</th>
                            <th class="py-2 pr-4">Debit</th>
                            <th class="py-2 pr-4">Credit</th>
                            <th class="py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($detail['entries'] ?? []) as $entry)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="py-2 pr-4 text-zinc-900 dark:text-white">{{ $entry['account'] ?? '-' }}</td>
                                <td class="py-2 pr-4 text-zinc-600 dark:text-zinc-300">{{ $entry['description'] ?? '-' }}</td>
                                <td class="py-2 pr-4 text-zinc-900 dark:text-white">{{ number_format($entry['debit'] ?? 0, 2) }}</td>
                                <td class="py-2 pr-4 text-zinc-900 dark:text-white">{{ number_format($entry['credit'] ?? 0, 2) }}</td>
                                <td class="py-2 text-zinc-600 dark:text-zinc-300">{{ $entry['entry_date'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
