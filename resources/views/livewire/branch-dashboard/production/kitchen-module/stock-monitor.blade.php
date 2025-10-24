<div class="p-3 space-y-3">
    <x-breadcrumb title="Production Monitor" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Production'],
        ['label' => 'Kitchen'],
        ['label' => 'Production Monitor'],
    ]" :compact="false" :with-icons="true" />

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-lg p-4 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Production Monitor</h2>
                <p class="text-sm opacity-90 mt-1">
                    Track products made in production - {{ auth('employees')->user()->department->name ?? 'Your Department' }}
                    @if($currentShift)
                        - {{ \Carbon\Carbon::parse($currentShift->shift_date)->format('l, F d, Y') }} ({{ ucfirst($currentShift->shift_type) }} Shift)
                    @endif
                </p>
            </div>
            <button wire:click="$refresh"
                class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Shift Selection -->
    @if(count($availableShifts) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-3">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Shift</label>
            <select wire:model.live="selectedShiftId"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Select a Shift --</option>
                @foreach($availableShifts as $shift)
                    <option value="{{ $shift->id }}">
                        {{ $shift->shift_date->format('M d, Y') }} - {{ ucfirst($shift->shift_type) }} Shift
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Filters Section -->
    <div x-data="{ open: true }"
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="flex justify-between items-center px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14.293 13H10v5l-4-4v-3.586L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </h2>
            <button @click="open = !open"
                class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white transition-all duration-200">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8h16M4 16h16" />
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="open ? 'Close' : 'Show Filters'"></span>
            </button>
        </div>

        <div x-show="open" x-collapse class="p-3 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by product name..."
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status Filter</label>
                    <select wire:model.live="filterStatus"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="all">All Products</option>
                        <option value="not_sent">Not Sent to Sales</option>
                        <option value="partially_sent">Partially Sent</option>
                        <option value="sent">Fully Sent</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Monitor Table -->
    <x-table :$headers :$rows striped paginate persist collapsible
        :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 20, 50, 100]">

        @interact('column_product', $row)
            <div>
                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->product_name }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($row->shift_type) }} Shift</div>
            </div>
        @endinteract

        @interact('column_produced', $row)
            <div class="text-center">
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($row->produced_quantity, 2) }}
                </span>
                <span class="text-sm text-zinc-600 dark:text-zinc-400 ml-1">
                    {{ $row->uom }}
                </span>
            </div>
        @endinteract

        @interact('column_available', $row)
            @php
                $availableQty = $row->available_quantity;
                $statusColor = 'text-green-600 dark:text-green-400';
                if ($availableQty <= 0) {
                    $statusColor = 'text-zinc-500 dark:text-zinc-400';
                } elseif ($availableQty < ($row->produced_quantity * 0.3)) {
                    $statusColor = 'text-orange-600 dark:text-orange-400';
                }
            @endphp
            <div class="text-center">
                <span class="text-2xl font-bold {{ $statusColor }}">
                    {{ number_format($availableQty, 2) }}
                </span>
                <span class="text-sm text-zinc-600 dark:text-zinc-400 ml-1">
                    {{ $row->uom }}
                </span>
            </div>
        @endinteract

        @interact('column_sent_out', $row)
            <div class="text-center text-sm font-medium">
                @if($row->sent_out_quantity > 0)
                    <span class="text-purple-600 dark:text-purple-400">
                        {{ number_format($row->sent_out_quantity, 2) }} {{ $row->uom }}
                    </span>
                @else
                    <span class="text-zinc-400 dark:text-zinc-600">-</span>
                @endif
            </div>
        @endinteract

        @interact('column_orders', $row)
            <div class="text-center text-sm font-medium">
                @if($row->order_quantity > 0)
                    <span class="text-indigo-600 dark:text-indigo-400">
                        {{ number_format($row->order_quantity, 2) }} {{ $row->uom }}
                    </span>
                @else
                    <span class="text-zinc-400 dark:text-zinc-600">-</span>
                @endif
            </div>
        @endinteract

        @interact('column_callbacks', $row)
            <div class="text-center text-sm font-medium">
                @if($row->callback_quantity > 0)
                    <span class="text-red-600 dark:text-red-400">
                        {{ number_format($row->callback_quantity, 2) }} {{ $row->uom }}
                    </span>
                @else
                    <span class="text-zinc-400 dark:text-zinc-600">-</span>
                @endif
            </div>
        @endinteract

        @interact('column_production_date', $row)
            <div class="text-center text-xs text-zinc-600 dark:text-zinc-400">
                {{ $row->produce_date ? $row->produce_date->format('M d, Y') : 'N/A' }}
            </div>
        @endinteract

        @interact('column_actions', $row)
            <div class="flex justify-center space-x-2">
                @if($row->available_quantity > 0)
                    <button wire:click="openSendOutModal({{ $row->id }})"
                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Send to Sales
                    </button>
                @else
                    <span class="px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 text-xs font-medium rounded-lg">
                        All Sent
                    </span>
                @endif
            </div>
        @endinteract

    </x-table>

    <!-- Info Panel -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-indigo-800 dark:text-indigo-200">
                <h4 class="font-semibold mb-1">Production Monitor Guide:</h4>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Produced:</strong> Total quantity produced during shift</li>
                    <li><strong>Available in Production:</strong> Products still in production area (not sent to sales yet)</li>
                    <li><strong>Sent to Sales:</strong> Products transferred to sales/front area</li>
                    <li><strong>Via Orders:</strong> Products sent out for customer orders</li>
                    <li><strong>Green:</strong> Products available to send to sales</li>
                    <li><strong>Grey:</strong> All products have been sent out</li>
                    <li>Use "Send to Sales" to transfer products from production to sales area</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Send Out Modal -->
    @if($showSendOutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showSendOutModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-zinc-500 bg-opacity-75 dark:bg-zinc-900 dark:bg-opacity-75"
                    @click="$wire.closeSendOutModal()"></div>

                <!-- Modal panel -->
                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-lg px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-zinc-800 sm:my-8 sm:align-middle sm:p-6">

                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" @click="$wire.closeSendOutModal()"
                            class="text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div>
                        <div
                            class="flex items-center justify-center w-12 h-12 mx-auto bg-indigo-100 rounded-full dark:bg-indigo-900/30">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                                Send Products to Sales
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Transfer products from production area to sales/front area.
                                    <br>
                                    <strong>Available to send: {{ number_format($maxSendableQuantity, 2) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Quantity to Send <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="sendOutQuantity" step="0.01" min="0.01"
                                max="{{ $maxSendableQuantity }}"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500">
                            @error('sendOutQuantity')
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea wire:model="sendOutNotes" rows="2"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-indigo-500"
                                placeholder="Any notes about this transfer..."></textarea>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button" wire:click="sendToSales"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Confirm Send Out
                        </button>
                        <button type="button" wire:click="closeSendOutModal"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md shadow-sm hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-zinc-700 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-600 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
