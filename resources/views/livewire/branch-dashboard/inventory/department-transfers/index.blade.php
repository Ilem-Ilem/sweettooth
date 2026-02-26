<div class="p-3 space-y-4">
    <x-breadcrumb
        title="Inter-department Dispatch"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Inventory'],
            ['label' => 'Inter-department Dispatch']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <div class="flex items-center justify-between">
        <div class="text-sm text-zinc-600 dark:text-zinc-400">
            Send items from inventory to other departments with approval and receipt confirmation.
        </div>
        <div class="flex gap-2">
            <button wire:click="exportCSV"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">
                Export CSV
            </button>
            <a href="{{ branch_route('branch-dashboard.inventory.department-transfers.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                New Transfer
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200"
                    placeholder="Transfer number or department">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                    <option value="">All</option>
                    <option value="pending_approval">Pending Approval</option>
                    <option value="approved">Approved</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="received">Received</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-700/40 text-zinc-600 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3 text-left">Transfer #</th>
                        <th class="px-4 py-3 text-left">To Department</th>
                        <th class="px-4 py-3 text-left">Receiver</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $transfer->transfer_number }}</div>
                                <div class="text-xs text-zinc-500">
                                    {{ $transfer->items->count() }} {{ Str::plural('item', $transfer->items->count()) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $transfer->toDepartment?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $transfer->receiver?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium bg-zinc-100 dark:bg-zinc-700">
                                    {{ Str::headline($transfer->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $transfer->created_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                @if($transfer->status === 'pending_approval')
                                    <button wire:click="approveTransfer({{ $transfer->id }})"
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
                                        Approve
                                    </button>
                                @endif
                                @if($transfer->status === 'approved')
                                    <button wire:click="dispatchTransfer({{ $transfer->id }})"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded">
                                        Dispatch
                                    </button>
                                @endif
                                @if($transfer->status === 'dispatched')
                                    <button wire:click="receiveTransfer({{ $transfer->id }})"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                                        Receive
                                    </button>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50/60 dark:bg-zinc-900/30">
                            <td colspan="6" class="px-4 py-3">
                                <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                    @foreach($transfer->items as $item)
                                        <span class="inline-block mr-3">
                                            {{ $item->item?->name }}: {{ $item->quantity }} {{ $item->unitOfMeasure?->symbol ?? $item->item?->uomSymbol }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-zinc-500">No transfers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $transfers->links() }}
        </div>
    </div>

</div>
