<div class="p-3 space-y-3">

    <div class="flex items-center justify-between">
        <x-breadcrumb
            title="Daily Production Tracking"
            :items="[
                ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
                ['label' => 'Production'],
                ['label' => 'Daily Produce']
            ]"
            :compact="false"
            :with-icons="true"/>

        <button wire:click="toggleHelpModal"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2"
                title="How to use this page">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Help
        </button>
    </div>

    <!-- Shift Selection -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Select Shift</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    @if($currentShift)
                        Current: {{ ucfirst($currentShift->shift_type) }} - {{ $currentShift->shift_date->format('M d, Y') }}
                        @if($productionRequestsCount > 0)
                            <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded-full">
                                {{ $productionRequestsCount }} production request(s)
                            </span>
                        @else
                            <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs rounded-full">
                                No production requests for this shift
                            </span>
                        @endif
                    @else
                        No active shift for today
                    @endif
                </p>
            </div>
            <div class="flex gap-2 items-center">
                <select wire:model.live="selectedShiftId"
                        class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                    <option value="">Select Shift</option>
                    @foreach($availableShifts as $shift)
                        <option value="{{ $shift->id }}">
                            {{ ucfirst($shift->shift_type) }} - {{ $shift->shift_date->format('M d, Y') }}
                        </option>
                    @endforeach
                </select>
                <button wire:click="saveAllQuantities"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save All
                </button>
            </div>
        </div>
    </div>

    @if(empty($dailyProduces))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">No Production Data Available</h3>
                    <p class="text-yellow-700 dark:text-yellow-300 mt-2">
                        @if($productionRequestsCount == 0)
                            No production requests found for the selected shift.
                        @else
                            Daily produce records exist but may not have loaded properly. Try refreshing.
                        @endif
                    </p>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">To start production:</p>
                        <ol class="text-sm text-yellow-700 dark:text-yellow-300 list-decimal list-inside space-y-1 ml-2">
                            <li>Create a <strong>Production Request</strong> for this shift</li>
                            <li>Wait for <strong>Item Request</strong> to be approved</li>
                            <li>Wait for ingredients to be <strong>dispatched</strong> from inventory</li>
                            <li>Then you can track production here</li>
                        </ol>
                    </div>
                    <a href="{{ branch_route('branch-dashboard.production.request.create') }}"
                       class="inline-block mt-4 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium">
                        Create Production Request
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Daily Produce Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" rowspan="2">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" rowspan="2">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" colspan="2">Opening</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" colspan="6">Production & Dispatch</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" colspan="3">Closing & Variance</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase" rowspan="2">Actions</th>
                        </tr>
                        <tr>
                            <!-- Opening -->
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Opening</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Requested</th>

                            <!-- Production & Dispatch -->
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400" title="Auto-calculated from batches">
                                Produced <span class="text-blue-500">🔄</span>
                            </th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400" title="Damaged/Rejected Items">
                                Callback <span class="text-red-500">⚠</span>
                            </th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400" title="Produced - Callback">
                                Net Available <span class="text-green-500">✓</span>
                            </th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Sent Out</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Order</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Closing</th>

                            <!-- Closing & Variance -->
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Expected</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Variance</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400">Variance %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($dailyProduces as $produce)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <!-- Product -->
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $produce['recipe_name'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $produce['uom'] }}</p>
                                    @if($produce['production_records_count'] > 0)
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded-full">
                                            {{ $produce['production_records_count'] }} batches
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Production Status (Computed from ingredients + production) -->
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $produce['status_badge_color'] }}">
                                        {{ ucfirst(str_replace('_', ' ', $produce['computed_status'])) }}
                                    </span>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $produce['item_request_number'] }}</p>

                                    <!-- Producability Information -->
                                    @php
                                        $prod = $produce['producability'];
                                        $canMake = $prod['producable_quantity'];
                                        $requested = $prod['requested_quantity'];
                                    @endphp

                                    @if($canMake == 0)
                                        <p class="text-xs text-red-600 dark:text-red-400 font-semibold">❌ Cannot produce</p>
                                        <p class="text-xs text-red-500">Missing: {{ $prod['limiting_ingredient'] }}</p>
                                    @elseif($canMake < $requested)
                                        <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold">
                                            ⚠ Can make {{ number_format($canMake) }} of {{ number_format($requested) }}
                                        </p>
                                        <p class="text-xs text-orange-500">
                                            Limited by: {{ $prod['limiting_ingredient'] }}
                                        </p>
                                        <p class="text-xs text-red-500">Shortage: {{ number_format($prod['shortage_percentage'], 1) }}%</p>
                                    @else
                                        <p class="text-xs text-green-600 dark:text-green-400 font-semibold">
                                            ✓ Can make all {{ number_format($requested) }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <!-- Opening -->
                            <td class="px-4 py-3 text-center text-sm text-zinc-900 dark:text-zinc-100">
                                {{ number_format($produce['opening_quantity'], 2) }}
                            </td>

                            <!-- Requested -->
                            <td class="px-4 py-3 text-center text-sm text-zinc-900 dark:text-zinc-100">
                                {{ number_format($produce['requested_quantity'], 2) }}
                            </td>

                            <!-- Produced (READ-ONLY - Auto from batches) -->
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($produce['produced_quantity'], 2) }}
                                    </span>
                                    @if($produce['production_records_count'] > 0)
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $produce['production_records_count'] }} batch(es)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Callback (EDITABLE - Damaged items) -->
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0"
                                       wire:model.blur="editingQuantities.{{ $produce['id'] }}.callback_quantity"
                                       wire:change="updateQuantity({{ $produce['id'] }}, 'callback_quantity')"
                                       class="w-24 px-2 py-1 text-center border border-red-300 dark:border-red-600 rounded bg-red-50 dark:bg-red-900/20 text-zinc-900 dark:text-zinc-100 text-sm focus:ring-2 focus:ring-red-500">
                            </td>

                            <!-- Net Available (AUTO-CALCULATED) -->
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold {{ $produce['net_available'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ number_format($produce['net_available'], 2) }}
                                </span>
                            </td>

                            <!-- Sent Out (EDITABLE) -->
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0"
                                       wire:model.blur="editingQuantities.{{ $produce['id'] }}.sent_out_quantity"
                                       wire:change="updateQuantity({{ $produce['id'] }}, 'sent_out_quantity')"
                                       class="w-24 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm">
                            </td>

                            <!-- Order (EDITABLE) -->
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0"
                                       wire:model.blur="editingQuantities.{{ $produce['id'] }}.order_quantity"
                                       wire:change="updateQuantity({{ $produce['id'] }}, 'order_quantity')"
                                       class="w-24 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm">
                            </td>

                            <!-- Closing (EDITABLE) -->
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0"
                                       wire:model.blur="editingQuantities.{{ $produce['id'] }}.closing_quantity"
                                       wire:change="updateQuantity({{ $produce['id'] }}, 'closing_quantity')"
                                       class="w-24 px-2 py-1 text-center border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm">
                            </td>

                            <!-- Expected Closing -->
                            <td class="px-4 py-3 text-center text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ number_format($produce['expected_closing'], 2) }}
                            </td>

                            <!-- Variance -->
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-semibold {{ $produce['has_variance_issue'] ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($produce['variance'], 2) }}
                                </span>
                            </td>

                            <!-- Variance % -->
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-semibold {{ $produce['has_variance_issue'] ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($produce['variance_percentage'], 1) }}%
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @php
                                        $canProduce = $produce['producability']['producable_quantity'] > 0;
                                    @endphp

                                    @if($canProduce)
                                        <button wire:click="openRecordModal({{ $produce['id'] }})"
                                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium w-full"
                                                title="Record Production Batch">
                                            Record Batch
                                        </button>
                                    @else
                                        <button disabled
                                                class="px-3 py-1 bg-gray-400 text-white rounded text-xs font-medium w-full cursor-not-allowed"
                                                title="Cannot produce - {{ $produce['producability']['limiting_ingredient'] }} missing">
                                            Record Batch
                                        </button>
                                    @endif

                                    @if($produce['manual_status'] === 'completed')
                                        @if($canProduce)
                                            <button wire:click="markInProgress({{ $produce['id'] }})"
                                                    class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-xs font-medium w-full"
                                                    title="Reopen for production">
                                                Reopen
                                            </button>
                                        @else
                                            <button disabled
                                                    class="px-3 py-1 bg-gray-400 text-white rounded text-xs font-medium w-full cursor-not-allowed"
                                                    title="Cannot reopen - no ingredients">
                                                Reopen
                                            </button>
                                        @endif
                                    @else
                                        <button wire:click="markComplete({{ $produce['id'] }})"
                                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium w-full"
                                                title="Mark this product as completed">
                                            Complete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Ingredient Analysis Row (Expandable Details) -->
                        @if(!empty($produce['producability']['ingredient_analysis']))
                        <tr class="bg-zinc-50 dark:bg-zinc-900/50">
                            <td colspan="16" class="px-4 py-3">
                                <details class="text-xs">
                                    <summary class="cursor-pointer font-medium text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100">
                                        📊 View Ingredient Analysis ({{ count($produce['producability']['ingredient_analysis']) }} ingredients)
                                    </summary>
                                    <div class="mt-3 overflow-x-auto">
                                        <table class="w-full text-xs">
                                            <thead class="bg-zinc-100 dark:bg-zinc-800">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-300">Ingredient</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Per Product</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Requested</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Approved</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Dispatched</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Can Make</th>
                                                    <th class="px-3 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">Shortage</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                                @foreach($produce['producability']['ingredient_analysis'] as $ingredient)
                                                <tr class="{{ $ingredient['is_limiting'] ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                                    <td class="px-3 py-2 font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ $ingredient['item_name'] }}
                                                        @if($ingredient['is_limiting'])
                                                            <span class="ml-1 px-1.5 py-0.5 bg-red-500 text-white text-[10px] rounded-full">LIMITING</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-zinc-700 dark:text-zinc-300">
                                                        {{ number_format($ingredient['quantity_per_product'], 2) }} {{ $ingredient['uom'] }}
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-zinc-700 dark:text-zinc-300">
                                                        {{ number_format($ingredient['quantity_requested'], 2) }} {{ $ingredient['uom'] }}
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="{{ $ingredient['quantity_approved'] < $ingredient['quantity_requested'] ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                            {{ number_format($ingredient['quantity_approved'], 2) }} {{ $ingredient['uom'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="{{ $ingredient['quantity_dispatched'] < $ingredient['quantity_approved'] ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-green-600 dark:text-green-400 font-semibold' }}">
                                                            {{ number_format($ingredient['quantity_dispatched'], 2) }} {{ $ingredient['uom'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center font-semibold">
                                                        <span class="{{ $ingredient['producable_quantity'] == 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                                            {{ number_format($ingredient['producable_quantity']) }} pcs
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        @if($ingredient['shortage'] > 0)
                                                            <span class="text-red-600 dark:text-red-400">
                                                                -{{ number_format($ingredient['shortage'], 2) }} {{ $ingredient['uom'] }}
                                                            </span>
                                                        @else
                                                            <span class="text-green-600 dark:text-green-400">✓</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </details>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Produced</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    {{ number_format(collect($dailyProduces)->sum('produced_quantity'), 2) }}
                </p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <p class="text-xs text-red-600 dark:text-red-400 font-medium">Total Callback (Damaged)</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">
                    {{ number_format(collect($dailyProduces)->sum('callback_quantity'), 2) }}
                </p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <p class="text-xs text-green-600 dark:text-green-400 font-medium">Total Net Available</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">
                    {{ number_format(collect($dailyProduces)->sum('net_available'), 2) }}
                </p>
            </div>
            <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-lg p-4">
                <p class="text-xs text-teal-600 dark:text-teal-400 font-medium">Total Sent Out</p>
                <p class="text-2xl font-bold text-teal-700 dark:text-teal-300 mt-1">
                    {{ number_format(collect($dailyProduces)->sum('sent_out_quantity'), 2) }}
                </p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Total Closing</p>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">
                    {{ number_format(collect($dailyProduces)->sum('closing_quantity'), 2) }}
                </p>
            </div>
        </div>
    @endif

    <!-- Help Modal -->
    @if($showHelpModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="help-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="toggleHelpModal"></div>

            <!-- Spacing element for proper centering -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full z-50">
                <!-- Header -->
                <div class="bg-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-white">Daily Production Tracking - User Guide</h3>
                        </div>
                        <button wire:click="toggleHelpModal" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-zinc-800 px-6 py-6 max-h-[70vh] overflow-y-auto">

                    <!-- Overview Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">📊 Overview</h4>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            This page helps you track daily production activities for your shift. It shows what you can produce based on available ingredients,
                            tracks actual production, manages damaged items, and calculates closing inventory.
                        </p>
                    </div>

                    <!-- Workflow Section -->
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">🔄 Production Workflow</h4>
                        <ol class="space-y-2 text-sm text-blue-800 dark:text-blue-200 list-decimal list-inside">
                            <li><strong>Create Production Request</strong> → System calculates ingredients needed</li>
                            <li><strong>Inventory Approves & Dispatches</strong> → Ingredients sent to production</li>
                            <li><strong>View This Page</strong> → See what you can produce with dispatched ingredients</li>
                            <li><strong>Record Batches</strong> → Click "Record Batch" to log production</li>
                            <li><strong>Track Distribution</strong> → Enter Sent Out, Orders, Callbacks, Closing</li>
                            <li><strong>Complete</strong> → Mark production as complete when done</li>
                        </ol>
                    </div>

                    <!-- Column Definitions -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">📋 Column Definitions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Opening -->
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <h5 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-2">🏁 Opening</h5>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    <strong>What it is:</strong> Stock quantity at the start of this shift (from previous shift's closing)
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                    <strong>Status:</strong> <span class="text-zinc-500">Auto-calculated</span>
                                </p>
                            </div>

                            <!-- Requested -->
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <h5 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-2">📝 Requested</h5>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    <strong>What it is:</strong> How many units you planned to produce (from Production Request)
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                    <strong>Status:</strong> <span class="text-zinc-500">From request</span>
                                </p>
                            </div>

                            <!-- Produced -->
                            <div class="border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                <h5 class="font-semibold text-blue-900 dark:text-blue-100 text-sm mb-2">🔄 Produced (Auto)</h5>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    <strong>What it is:</strong> Total quantity produced (automatically summed from all batches you record)
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">READ-ONLY - Auto-calculated</span>
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    ℹ️ Updates automatically when you click "Record Batch"
                                </p>
                            </div>

                            <!-- Callback -->
                            <div class="border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                                <h5 class="font-semibold text-red-900 dark:text-red-100 text-sm mb-2">⚠ Callback (Damaged)</h5>
                                <p class="text-xs text-red-700 dark:text-red-300">
                                    <strong>What it is:</strong> Damaged, rejected, or unusable items
                                </p>
                                <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">EDITABLE</span>
                                </p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    ℹ️ Enter damaged quantity here - reduces net available
                                </p>
                            </div>

                            <!-- Net Available -->
                            <div class="border border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                                <h5 class="font-semibold text-green-900 dark:text-green-100 text-sm mb-2">✓ Net Available (Auto)</h5>
                                <p class="text-xs text-green-700 dark:text-green-300">
                                    <strong>What it is:</strong> Usable quantity after removing damaged items
                                </p>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                    <strong>Formula:</strong> <code>Produced - Callback</code>
                                </p>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">Auto-calculated</span>
                                </p>
                            </div>

                            <!-- Sent Out -->
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <h5 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-2">📤 Sent Out</h5>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    <strong>What it is:</strong> Quantity sent to sales departments or customers
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">EDITABLE</span>
                                </p>
                            </div>

                            <!-- Order -->
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <h5 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-2">📋 Order</h5>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    <strong>What it is:</strong> Quantity ordered but not yet sent out
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">EDITABLE</span>
                                </p>
                            </div>

                            <!-- Closing -->
                            <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-3">
                                <h5 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm mb-2">🔒 Closing</h5>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                    <strong>What it is:</strong> Actual physical count at end of shift
                                </p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">EDITABLE</span>
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                                    ℹ️ This becomes next shift's opening
                                </p>
                            </div>

                            <!-- Expected Closing -->
                            <div class="border border-purple-200 dark:border-purple-700 bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                                <h5 class="font-semibold text-purple-900 dark:text-purple-100 text-sm mb-2">🎯 Expected Closing (Auto)</h5>
                                <p class="text-xs text-purple-700 dark:text-purple-300">
                                    <strong>What it is:</strong> What closing SHOULD be based on calculations
                                </p>
                                <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                                    <strong>Formula:</strong> <code>Opening + Net Available - Sent Out - Order</code>
                                </p>
                                <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">Auto-calculated</span>
                                </p>
                            </div>

                            <!-- Variance -->
                            <div class="border border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3">
                                <h5 class="font-semibold text-orange-900 dark:text-orange-100 text-sm mb-2">📊 Variance & %</h5>
                                <p class="text-xs text-orange-700 dark:text-orange-300">
                                    <strong>What it is:</strong> Difference between actual closing and expected closing
                                </p>
                                <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                    <strong>Formula:</strong> <code>Closing - Expected Closing</code>
                                </p>
                                <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                    <strong>Status:</strong> <span class="font-semibold">Auto-calculated</span>
                                </p>
                                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                    ⚠️ High variance (>5) indicates possible issues
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">🎯 Key Features</h4>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <div>
                                    <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Producable Quantity Analysis</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                        Click the expandable row to see detailed ingredient analysis. Shows which ingredient is limiting your production capacity.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <div>
                                    <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Smart Buttons</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                        "Record Batch" button automatically disables if you don't have enough ingredients. Tooltip shows what's missing.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <div>
                                    <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Real-time Summary Cards</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                        Bottom cards show totals across all products: Total Produced, Damaged, Net Available, Sent Out, and Closing.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-3">💡 Tips</h4>
                        <ul class="space-y-2 text-sm text-yellow-800 dark:text-yellow-200 list-disc list-inside">
                            <li>Always check "Producable Quantity" before starting - it shows if you have enough ingredients</li>
                            <li>Record batches as you produce them, don't wait until end of shift</li>
                            <li>Enter callback/damaged items immediately to get accurate net available</li>
                            <li>At end of shift, do physical count and enter as "Closing"</li>
                            <li>If variance is high, investigate - it could indicate theft, waste, or counting errors</li>
                            <li>Click "Save All" button after making changes to multiple rows</li>
                        </ul>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 flex justify-end">
                    <button wire:click="toggleHelpModal"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Record Production Batch Modal -->
    @if($showRecordModal && $recordingProduce)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="record-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeRecordModal"></div>

            <!-- Spacing element for proper centering -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full z-50">
                <!-- Header -->
                <div class="bg-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Record Production Batch</h3>
                            <p class="text-sm text-blue-100 mt-1">{{ $recordingProduce->recipe->product_name ?? 'N/A' }}</p>
                        </div>
                        <button wire:click="closeRecordModal" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-zinc-800 px-6 py-6 space-y-6">

                    <!-- Production Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-blue-600 dark:text-blue-400 font-medium">UOM</p>
                                <p class="text-blue-900 dark:text-blue-100 font-semibold">{{ $recordingProduce->recipe->uom ?? 'pcs' }}</p>
                            </div>
                            <div>
                                <p class="text-blue-600 dark:text-blue-400 font-medium">Already Produced</p>
                                <p class="text-blue-900 dark:text-blue-100 font-semibold">{{ number_format($recordingProduce->produced_quantity, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-blue-600 dark:text-blue-400 font-medium">Requested</p>
                                <p class="text-blue-900 dark:text-blue-100 font-semibold">{{ number_format($recordingProduce->requested_quantity, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-blue-600 dark:text-blue-400 font-medium">Batches</p>
                                <p class="text-blue-900 dark:text-blue-100 font-semibold">{{ $recordingProduce->productionRecords->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Produced -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Quantity Produced <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" wire:model.live="batchQuantityProduced"
                               class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                        @error('batchQuantityProduced')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quality Control Section -->
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Quality Control</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Approved Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-green-700 dark:text-green-300 mb-2">
                                    Quantity Approved <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" wire:model.live="batchQuantityApproved"
                                       class="w-full px-4 py-2 border border-green-300 dark:border-green-600 rounded-lg bg-green-50 dark:bg-green-900/20 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-green-500">
                                @error('batchQuantityApproved')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rejected Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-red-700 dark:text-red-300 mb-2">
                                    Quantity Rejected <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" wire:model.live="batchQuantityRejected"
                                       class="w-full px-4 py-2 border border-red-300 dark:border-red-600 rounded-lg bg-red-50 dark:bg-red-900/20 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500">
                                @error('batchQuantityRejected')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Validation Helper -->
                        @if($batchQuantityProduced > 0)
                        <div class="mt-3 p-3 {{ abs(($batchQuantityApproved + $batchQuantityRejected) - $batchQuantityProduced) < 0.01 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' }} border rounded-lg">
                            <p class="text-sm">
                                <span class="font-semibold">Total: </span>
                                <span class="{{ abs(($batchQuantityApproved + $batchQuantityRejected) - $batchQuantityProduced) < 0.01 ? 'text-green-700 dark:text-green-300' : 'text-orange-700 dark:text-orange-300' }}">
                                    {{ number_format($batchQuantityApproved + $batchQuantityRejected, 2) }}
                                </span>
                                / {{ number_format($batchQuantityProduced, 2) }}
                                @if(abs(($batchQuantityApproved + $batchQuantityRejected) - $batchQuantityProduced) < 0.01)
                                    <span class="ml-2 text-green-600 dark:text-green-400">✓ Balanced</span>
                                @else
                                    <span class="ml-2 text-orange-600 dark:text-orange-400">⚠ Must equal produced quantity</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Quality Status -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Quality Status <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="batchQualityStatus"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="acceptable">Acceptable</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        @error('batchQualityStatus')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rejection Reason (if rejected quantity > 0) -->
                    @if($batchQuantityRejected > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <label class="block text-sm font-medium text-red-700 dark:text-red-300 mb-2">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="batchRejectionReason" rows="3"
                                  class="w-full px-4 py-2 border border-red-300 dark:border-red-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-red-500"
                                  placeholder="Explain why items were rejected..."></textarea>
                        @error('batchRejectionReason')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Notes (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea wire:model="batchNotes" rows="2"
                                  class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                  placeholder="Any additional notes about this batch..."></textarea>
                    </div>

                </div>

                <!-- Footer -->
                <div class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 flex justify-end gap-3">
                    <button wire:click="closeRecordModal"
                            class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg font-medium">
                        Cancel
                    </button>
                    <button wire:click="recordBatch"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Record Batch
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
