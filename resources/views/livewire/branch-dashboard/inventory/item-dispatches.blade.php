<div class="p-3 space-y-3">

    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            @apply bg-zinc-300 dark:bg-zinc-700 rounded-full;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            @apply bg-zinc-400 dark:bg-zinc-600;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>

    <x-breadcrumb
        title="Item Dispatches"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Inventory'],
            ['label' => 'Item Dispatches']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Pending Requests -->
    @if($pendingRequests->count() > 0)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Pending Dispatch Requests</h3>
        <div class="space-y-2">
            @foreach($pendingRequests as $request)
            <div class="flex justify-between items-center bg-white dark:bg-zinc-800 p-3 rounded-lg">
                <div>
                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $request->request_number }}</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $request->department->name ?? 'N/A' }}</p>
                </div>
                <button wire:click="openDispatchModal({{ $request->id }})"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    Dispatch
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Filters Section -->
    <div x-data="{ open: false }"
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 transition-all duration-300">
        <div class="flex justify-between items-center px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14.293 13H10v5l-4-4v-3.586L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </h2>
            <button @click="open = !open"
                class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="open ? 'Close' : 'Show Filters'"></span>
            </button>
        </div>

        <div x-show="open" x-collapse class="p-3 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Shift</label>
                    <select wire:model.live="filterShift"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">All Shifts</option>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="night">Night</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date To</label>
                    <input type="date" wire:model.live="filterDateTo"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Received</label>
                    <select wire:model.live="filterReceived"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="1">Received</option>
                        <option value="0">Not Received</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 justify-end pt-2.5 border-t border-zinc-200 dark:border-zinc-700">
                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <x-table
        :headers="[
            ['index' => 'request_number', 'label' => 'Request #'],
            ['index' => 'item_name', 'label' => 'Item'],
            ['index' => 'quantity', 'label' => 'Quantity'],
            ['index' => 'shift', 'label' => 'Shift'],
            ['index' => 'dispatch_time', 'label' => 'Dispatch Time'],
            ['index' => 'dispatcher', 'label' => 'Dispatcher'],
            ['index' => 'received_time', 'label' => 'Received Time'],
            ['index' => 'receiver', 'label' => 'Receiver'],
        ]"
        :rows="$dispatches"
        striped
        paginate
        persist
        :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 25, 50, 100]">

        @interact('column_request_number', $row)
            <span class="font-mono text-zinc-900 dark:text-zinc-100">
                {{ $row->itemRequest->request_number ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_item_name', $row)
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                {{ $row->item->name ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_quantity', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ number_format($row->quantity, 2) }} {{ $row->uom }}
            </span>
        @endinteract

        @interact('column_shift', $row)
            @php
                $shiftColors = [
                    'morning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'afternoon' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                    'night' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                ];
            @endphp
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $shiftColors[$row->shift] ?? '' }}">
                {{ ucfirst($row->shift) }}
            </span>
        @endinteract

        @interact('column_dispatch_time', $row)
            <span class="text-zinc-600 dark:text-zinc-400">
                {{ $row->dispatch_time ? $row->dispatch_time->format('Y-m-d H:i') : 'N/A' }}
            </span>
        @endinteract

        @interact('column_dispatcher', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->dispatcher->name ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_received_time', $row)
            @if($row->received_time)
            <span class="text-green-600 dark:text-green-400">
                {{ $row->received_time->format('Y-m-d H:i') }}
            </span>
            @else
            <span class="text-zinc-400 dark:text-zinc-500">Not Received</span>
            @endif
        @endinteract

        @interact('column_receiver', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->receiver->name ?? 'N/A' }}
            </span>
        @endinteract
    </x-table>

    <!-- Dispatch Modal -->
    <div x-data="{ show: @entangle('showDispatchModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="show = false">
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
            @click="$wire.closeModal()">
        </div>

        <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full md:w-1/2 lg:w-1/3 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Dispatch Items</h2>
                <button wire:click="closeModal"
                    class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Shift *</label>
                        <select wire:model="shift"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Shift</option>
                            <option value="morning">Morning</option>
                            <option value="afternoon">Afternoon</option>
                            <option value="night">Night</option>
                        </select>
                        @error('shift')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(!empty($dispatchItems))
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Item</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dispatchItems as $index => $item)
                                <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                    <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" wire:model="dispatchItems.{{ $index }}.quantity"
                                            class="w-24 px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                                        <span class="text-xs text-zinc-500 ml-1">/ {{ $item['remaining'] }} {{ $item['uom'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                <button wire:click="closeModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="dispatch"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Dispatch Items
                </button>
            </div>
        </div>
    </div>

</div>
