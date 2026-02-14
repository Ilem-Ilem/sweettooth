<div class="p-3 space-y-3">
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
                @if ($isVerified)
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
                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
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
                <input type="text" wire:model.live.debounce.600ms="search"
                    placeholder="Search by product name or SKU..."
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Product Type</label>
                <select wire:model.live="filterProductType"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        <a href="#unclosed-products"
           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-100 text-amber-900 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-100 dark:hover:bg-amber-900/50 transition">
            View Unclosed Products
        </a>
    </div>
    <div id="unclosed-products" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-100">Unclosed Products</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                        These products have a previous closing but none for {{ \Carbon\Carbon::parse($stockDate)->subDay()->format('M d, Y') }}.
                        Review and carry forward if needed.
                    </p>
                </div>
            </div>
            @if(!empty($unclosedProducts))
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-amber-900 dark:text-amber-100">
                                <th class="py-2 pr-4">Product</th>
                                <th class="py-2 pr-4">SKU</th>
                                <th class="py-2 pr-4">Last Closing</th>
                                <th class="py-2 pr-4">Last Stock Date</th>
                                <th class="py-2 pr-4">Shift</th>
                            </tr>
                        </thead>
                        <tbody class="text-amber-900 dark:text-amber-100">
                            @foreach($unclosedProducts as $item)
                                <tr class="border-t border-amber-200 dark:border-amber-800">
                                    <td class="py-2 pr-4">{{ $item['product_name'] }}</td>
                                    <td class="py-2 pr-4 text-xs text-amber-700 dark:text-amber-300">{{ $item['product_sku'] }}</td>
                                    <td class="py-2 pr-4 font-semibold">
                                        {{ number_format($item['last_closing'], 2) }} {{ $item['product_uom'] }}
                                    </td>
                                    <td class="py-2 pr-4">{{ $item['last_stock_date'] ?? '-' }}</td>
                                    <td class="py-2 pr-4">{{ ucfirst($item['last_shift_type'] ?? '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                    No unclosed products found for this date.
                </div>
            @endif
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
                @if(($row->dispatch_count ?? 0) > 0)
                    <div class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                        {{ number_format($row->dispatch_count) }} dispatch{{ $row->dispatch_count == 1 ? '' : 'es' }} received
                    </div>
                @endif
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

        @interact('column_variance_source', $row)
            <div class="text-center">
                @if($row->variance != 0)
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $row->variance > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $row->variance_source }}
                    </span>
                @else
                    <span class="text-zinc-400 dark:text-zinc-600">-</span>
                @endif
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
    @if (count($stockOpenings) > 0 && !$isVerified)
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
                    <li><strong>Previous Closing:</strong> Stock from previous shift/day's closing count</li>
                    <li><strong>Production Sent:</strong> Products received from production dispatches (received-only)</li>
                    <li><strong>Expected Opening:</strong> Previous closing + Production sent</li>
                    <li><strong>Actual Opening:</strong> Physically count and enter the actual quantity you have at START of shift</li>
                    <li><strong>Variance:</strong> Difference between expected and actual opening (investigate if significant)</li>
                    <li><strong>Variance From:</strong> Shows which shift/day caused the variance (Previous closing or Production)</li>
                    <li><strong>Production Date:</strong> When the product was made (affects expiry calculation)</li>
                    <li>Note: Closing stock is tracked separately at end of day</li>
                    <li><strong>Shelf Life Status:</strong> Fresh (green), Warning (yellow), Critical (orange), Expired (red)</li>
                    <li>Use the shift selector to view or edit past shifts</li>
                    <li>Once verified and saved, stock opening cannot be modified for that shift</li>
                </ul>
            </div>
        </div>
    </div>
</div>
