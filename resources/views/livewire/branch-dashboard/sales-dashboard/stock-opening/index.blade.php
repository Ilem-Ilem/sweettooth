<div class="p-3 space-y-3" x-data="{ open: false }">
    <x-breadcrumb title="Stock Opening" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Sales Dashboard'],
        ['label' => 'Stock Opening'],
    ]" :compact="false" :with-icons="true" />
    <!-- Header with Status -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-4 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Daily Stock Opening</h2>
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
                        to begin stock opening.</p>
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
                class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
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
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Product Type</label>
                <select wire:model.live="filterProductType"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <!-- Stock Opening Table -->
    <x-table :$headers :$rows striped paginate persist collapsible
        :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 20, 50, 100]">

        @interact('column_product', $row)
            <div>
                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->product_name }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row->product_sku }}</div>
            </div>
        @endinteract

        @interact('column_yesterday_closing', $row)
            <div class="text-center">
                <span class="font-medium text-zinc-700 dark:text-zinc-300">
                    {{ number_format($row->yesterday_closing, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_today_additions', $row)
            <div class="text-center">
                <span class="font-medium text-blue-600 dark:text-blue-400">
                    {{ number_format($row->today_additions, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_expected_opening', $row)
            <div class="text-center">
                <span class="font-semibold text-green-600 dark:text-green-400">
                    {{ number_format($row->expected_opening, 2) }} {{ $row->product_uom }}
                </span>
            </div>
        @endinteract

        @interact('column_actual_opening', $row)
            <div class="flex justify-center">
                <input type="number" step="0.01" min="0"
                    wire:model.blur="stockOpenings.{{ $row->index }}.actual_opening"
                    wire:change="updateActualOpening({{ $row->product_id }}, $event.target.value)"
                    @if($row->is_saved) readonly @endif
                    class="w-24 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        @if($row->is_saved) bg-zinc-100 dark:bg-zinc-700 cursor-not-allowed @else bg-white dark:bg-zinc-800 @endif
                        text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
            </div>
        @endinteract

        @interact('column_variance', $row)
            @php
                $variance = $row->variance;
                $varianceClass = $variance == 0 ? 'text-green-600 dark:text-green-400' : ($variance > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400');
                $varianceIcon = $variance == 0 ? '=' : ($variance > 0 ? '↑' : '↓');
            @endphp
            <div class="text-center">
                <span class="font-semibold {{ $varianceClass }}">
                    {{ $varianceIcon }} {{ number_format(abs($variance), 2) }}
                </span>
            </div>
        @endinteract

        @interact('column_production_date', $row)
            <div class="flex justify-center">
                <input type="date"
                    wire:model.blur="stockOpenings.{{ $row->index }}.production_date"
                    wire:change="updateProductionDate({{ $row->product_id }}, $event.target.value)"
                    @if($row->is_saved) readonly @endif
                    class="w-36 px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-center
                        @if($row->is_saved) bg-zinc-100 dark:bg-zinc-700 cursor-not-allowed @else bg-white dark:bg-zinc-800 @endif
                        text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
            </div>
        @endinteract

        @interact('column_shelf_life', $row)
            <div class="text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                    {{ $row->shelf_life_days }} days
                </span>
            </div>
        @endinteract

        @interact('column_notes', $row)
            <div>
                <input type="text"
                    wire:model.blur="stockOpenings.{{ $row->index }}.notes"
                    wire:change="updateNotes({{ $row->product_id }}, $event.target.value)"
                    @if($row->is_saved) readonly @endif
                    placeholder="Add notes..."
                    class="w-full px-2 py-1 border border-zinc-300 dark:border-zinc-600 rounded
                        @if($row->is_saved) bg-zinc-100 dark:bg-zinc-700 cursor-not-allowed @else bg-white dark:bg-zinc-800 @endif
                        text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
        @endinteract

    </x-table>
    <!-- Save Button -->
    @if (count($stockOpenings) > 0 && !$isVerified && !$this->checkVerificationStatus())
        <div class="flex justify-end gap-3">
            <button wire:click="loadStockOpeningData"
                class="px-6 py-2.5 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <button wire:click="saveStockOpenings"
                class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verify & Save Stock Opening
            </button>
        </div>
    @endif
    <!-- Information Panel -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
                <h4 class="font-semibold mb-1">Stock Opening Instructions:</h4>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Expected Opening:</strong> Yesterday's closing + Today's additions from kitchen</li>
                    <li><strong>Actual Opening:</strong> Physically count and enter the actual quantity you have</li>
                    <li><strong>Variance:</strong> Difference between expected and actual (investigate if significant)
                    </li>
                    <li><strong>Production Date:</strong> When the product was made (affects expiry calculation)</li>
                    <li><strong>Shelf Life Status:</strong> Fresh (green), Warning (yellow), Critical (orange), Expired
                        (red)</li>
                    <li>Once verified and saved, stock opening cannot be modified for this shift</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-hidden" @keydown.escape.window="open = false" x-cloak>
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 dark:bg-black/70" @click="open = false"></div>
        <!-- Panel (slide-in) -->
        <div x-show="open" x-transition:enter="slideIn" x-transition:leave="slideOut"
            class="absolute right-0 top-0 h-full w-full sm:w-1/2 bg-white dark:bg-zinc-800 shadow-2xl flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-lg font-semibold">Stock Opening – Butter Croissant</h2>
                <button @click="open = false; $dispatch('close-modal')"
                    class="p-2 rounded hover:bg-zinc-100 dark:hover:bg-zinc-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Scrollable Form -->
            <div class="flex-1 overflow-y-auto p-4 space-y-6">
                <!-- ==== PRODUCT BLOCK ==== -->
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">Product</label>
                        <div class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            Butter Croissant
                        </div>
                        <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PT-BUT-001</div>
                    </div>
                </div>
                <!-- ==== YESTERDAY'S CLOSING ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Yesterday's Closing
                    </label>
                    <div class="mt-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        0.00 pcs
                    </div>
                </div>
                <!-- ==== TODAY'S ADDITIONS ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Today's Additions
                    </label>
                    <input type="number" step="0.01" min="0" value="0"
                        class="mt-1 block w-full rounded border border-zinc-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-700 px-3 py-2 text-sm
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500">
                </div>
                <!-- ==== EXPECTED OPENING ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Expected Opening
                    </label>
                    <div class="mt-1 text-sm font-semibold text-green-600 dark:text-green-400">
                        0.00 pcs
                    </div>
                </div>
                <!-- ==== ACTUAL OPENING ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Actual Opening
                    </label>
                    <input type="number" step="0.01" min="0" value="0"
                        class="mt-1 block w-full rounded border border-zinc-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-700 px-3 py-2 text-sm
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500">
                </div>
                <!-- ==== VARIANCE ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Variance
                    </label>
                    <div class="mt-1 text-sm font-semibold text-green-600 dark:text-green-400">
                        = 0.00
                    </div>
                </div>
                <!-- ==== PRODUCTION DATE ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Production Date
                    </label>
                    <input type="date" value="2025-10-20"
                        class="mt-1 block w-full rounded border border-zinc-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-700 px-3 py-2 text-sm
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500">
                </div>
                <!-- ==== SHELF LIFE ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Shelf Life
                    </label>
                    <span
                        class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        Fresh
                    </span>
                </div>
                <!-- ==== NOTES ==== -->
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">
                        Notes
                    </label>
                    <textarea rows="3" placeholder="Add notes..."
                        class="mt-1 block w-full rounded border border-zinc-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-700 px-3 py-2 text-sm
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <!-- Footer -->
            <div
                class="flex justify-end gap-3 p-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                <button @click="open = false; $dispatch('close-modal')"
                    class="px-4 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded transition">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
