<div class="p-3 space-y-3" x-data="{ open: false }">
    <x-breadcrumb title="Stock Closing" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Sales Dashboard'],
        ['label' => 'Stock Closing'],
    ]" :compact="false" :with-icons="true" />
    <!-- Header with Status -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-4 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Daily Stock Closing</h2>
                <p class="text-sm opacity-90 mt-1">
                    {{ \Carbon\Carbon::parse($stockDate)->format('l, F d, Y') }} - {{ ucfirst($shiftType) }} Shift
                </p>
            </div>
            <div class="text-right">
                @if ($isVerified || $this->checkVerificationStatus())
                    <div class="flex items-center bg-green-500 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold">Verified</span>
                    </div>
                @else
                    <div class="flex items-center bg-yellow-500 px-4 py-2 rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-semibold">Not Verified</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Shift Selector -->
    @if(count($availableShifts) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                Select Shift to View/Edit
            </label>
            <select wire:model.live="selectedShiftForViewing"
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-red-500">
                <option value="">-- Current Shift --</option>
                @foreach($availableShifts as $shift)
                    <option value="{{ $shift->id }}">
                        {{ $shift->shift_date->format('M d, Y') }} - {{ ucfirst($shift->shift_type) }} Shift
                        @if($shift->id == $currentShiftId) (Current) @endif
                    </option>
                @endforeach
            </select>
        </div>
    @endif
    <!-- Alert if no shift -->
    @if (!$currentShiftId)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Active Shift</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Please clock in and select your shift
                        to begin stock closing.</p>
                </div>
            </div>
        </div>
    @endif
    <!-- Filters Section -->
    <div x-data="{ open: false }"
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
                class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-red-600 hover:bg-red-700 text-white transition-all duration-200">
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
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by product name or SKU..."
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Product Type</label>
                <select wire:model.live="filterProductType"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-red-500">
                    <option value="">All Types</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <!-- Stock Closing Table -->
    <x-table :$headers :$rows striped paginate persist collapsible
        :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 20, 50, 100]">

        @interact('column_product', $row)
            <div>
                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->product_name }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row->product_sku }}</div>
            </div>
        @endinteract

        @interact('column_opening_quantity', $row)
            <div class="text-center">
                <span class="font-medium text-zinc-700 dark:text-zinc-300">
                    {{ number_format($row->opening_quantity, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_addition_quantity', $row)
            <div class="text-center">
                <span class="font-medium text-blue-600 dark:text-blue-400">
                    {{ number_format($row->addition_quantity, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_callback_quantity', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.callback_quantity"
                    wire:change="updateCallbackQuantity({{ $row->product_id }}, $event.target.value)"
                    class="w-20 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_redress_quantity', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.redress_quantity"
                    wire:change="updateRedressQuantity({{ $row->product_id }}, $event.target.value)"
                    class="w-20 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_total_available', $row)
            <div class="text-center">
                <span class="font-semibold text-green-600 dark:text-green-400">
                    {{ number_format($row->total_available, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_transfer_quantity', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.transfer_quantity"
                    wire:change="updateTransferQuantity({{ $row->product_id }}, $event.target.value)"
                    class="w-20 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_glovo_quantity', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.glovo_quantity"
                    wire:change="updateGlovoQuantity({{ $row->product_id }}, $event.target.value)"
                    class="w-20 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_quantity_sold', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.quantity_sold"
                    wire:change="updateQuantitySold({{ $row->product_id }}, $event.target.value)"
                    class="w-20 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_closing_quantity', $row)
            <div class="text-center">
                <span class="font-semibold text-red-600 dark:text-red-400">
                    {{ number_format($row->closing_quantity, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_amount', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockClosings.{{ $row->index }}.amount"
                    wire:change="updateAmount({{ $row->product_id }}, $event.target.value)"
                    class="w-24 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
            </div>
        @endinteract

        @interact('column_notes', $row)
            <div>
                <input type="text"
                    wire:model.blur="stockClosings.{{ $row->index }}.notes"
                    wire:change="updateNotes({{ $row->product_id }}, $event.target.value)"
                    placeholder="Add notes..."
                    class="w-full px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded
                        bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500 text-sm">
            </div>
        @endinteract

    </x-table>
    <!-- Save Button -->
    @if (count($stockClosings) > 0 && !$isVerified && !$this->checkVerificationStatus())
        <div class="flex justify-end gap-3">
            <button wire:click="loadStockClosingData"
                class="px-6 py-2.5 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <button wire:click="saveStockClosings"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verify & Save Stock Closing
            </button>
        </div>
    @endif
    <!-- Information Panel -->
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-red-800 dark:text-red-200">
                <h4 class="font-semibold mb-1">Stock Closing Instructions:</h4>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Opening Qty:</strong> Stock at start of shift</li>
                    <li><strong>Additions:</strong> Products added during shift</li>
                    <li><strong>Callbacks:</strong> Items rejected/returned during shift</li>
                    <li><strong>Redresses:</strong> Items that needed fixing/adjustment</li>
                    <li><strong>Total Available:</strong> Opening + Additions - Callbacks - Redresses</li>
                    <li><strong>Transfers:</strong> Items sent to other departments</li>
                    <li><strong>Glovo Sales:</strong> Items sold through Glovo platform</li>
                    <li><strong>Regular Sales:</strong> Items sold directly to customers</li>
                    <li><strong>Closing Qty:</strong> Total Available - Transfers - Glovo - Regular Sales</li>
                    <li><strong>Sales Amount:</strong> Total revenue from sales</li>
                    <li>Use the shift selector to view or edit past shifts</li>
                    <li>Once verified and saved, stock closing cannot be modified for that shift</li>
                </ul>
            </div>
        </div>
    </div>
</div>
