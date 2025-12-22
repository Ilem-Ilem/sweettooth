<div class="p-3 space-y-3">

    <x-breadcrumb
        title="Kitchen Module"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Production'],
            ['label' => 'Kitchen']
        ]"
        :compact="false"
        :with-icons="true"/>

    <!-- Tab Navigation -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 mb-4">
        <div class="border-b border-zinc-200 dark:border-zinc-700">
            <nav class="flex">
                <button wire:click="switchTab('dashboard')"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors
                               {{ $activeTab === 'dashboard'
                                   ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                   : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0M8 5a2 2 0 012-2h2a2 2 0 012 2v0"/>
                    </svg>
                    Dashboard
                </button>
                <button wire:click="switchTab('stock-monitor')"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors
                               {{ $activeTab === 'stock-monitor'
                                   ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                   : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Stock Monitor
                </button>
            </nav>
        </div>
    </div>

    @if($activeTab === 'dashboard')
    <!-- Shift Selector -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Current Shift</h3>
    @endif

    @if($activeTab === 'stock-monitor')
        @include('livewire.branch-dashboard.production.kitchen-module.stock-monitor-content')
    @endif

    @if($activeTab === 'dashboard')
    @if($currentShift)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ ucfirst($currentShift->shift_type) }} - {{ $currentShift->shift_date->format('M d, Y') }}
                    </p>
                @else
                    <p class="text-sm text-red-500 dark:text-red-400 mt-1">No active shift for today</p>
                @endif
            </div>

            @if($todayShifts->count() > 1)
            <select wire:model.live="selectedShiftId"
                    class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                @foreach($todayShifts as $shift)
                    <option value="{{ $shift->id }}">{{ ucfirst($shift->shift_type) }}</option>
                @endforeach
            </select>
            @endif
        </div>
    </div>

    @if($currentShift)
        <!-- Shift Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Recipes</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $shiftSummary['total_recipes'] }}</p>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <p class="text-xs text-green-600 dark:text-green-400 font-medium">Completed</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $shiftSummary['completed'] }}</p>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">In Progress</p>
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 mt-1">{{ $shiftSummary['in_progress'] }}</p>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Pending</p>
                <p class="text-2xl font-bold text-orange-700 dark:text-orange-300 mt-1">{{ $shiftSummary['pending'] }}</p>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Produced</p>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">{{ number_format($shiftSummary['total_produced']) }}</p>
            </div>

            <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-lg p-4">
                <p class="text-xs text-teal-600 dark:text-teal-400 font-medium">Requested</p>
                <p class="text-2xl font-bold text-teal-700 dark:text-teal-300 mt-1">{{ number_format($shiftSummary['total_requested']) }}</p>
            </div>
        </div>

        <!-- Production Requests -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Today's Production Requests</h3>
            </div>

            @if($productionRequests->count() > 0)
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($productionRequests as $request)
                    <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $request['recipe_name'] }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    Request: {{ $request['item_request_number'] }} •
                                    Planned: {{ number_format($request['planned_quantity'], 2) }} {{ $request['uom'] }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $request['status_color'] }}">
                                {{ ucfirst(str_replace('_', ' ', $request['status'])) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm">No production requests for this shift</p>
                </div>
            @endif
        </div>

        <!-- Items to Collect from Inventory -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Items to Collect from Inventory</h3>
            </div>

            @if(count($itemsToCollect) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Item</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Requested</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Approved</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Dispatched</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($itemsToCollect as $item)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ number_format($item['quantity_requested'], 2) }} {{ $item['uom'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ number_format($item['quantity_approved'], 2) }} {{ $item['uom'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ number_format($item['quantity_dispatched'], 2) }} {{ $item['uom'] }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item['status']['color'] }}">
                                        {{ $item['status']['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">No items to collect for this shift</p>
                </div>
            @endif
        </div>

        <!-- Current Production Status -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Production Status</h3>
                <a href="{{ branch_route('branch-dashboard.production.daily-produce.index', ['b_id' => $b_id]) }}"
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    View Full Dashboard →
                </a>
            </div>

            @if($dailyProduces->count() > 0)
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($dailyProduces as $produce)
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $produce['recipe_name'] }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ number_format($produce['produced_quantity'], 2) }} / {{ number_format($produce['requested_quantity'], 2) }} {{ $produce['uom'] }}
                                    @if($produce['batches_count'] > 0)
                                        <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs rounded-full">
                                            {{ $produce['batches_count'] }} batches
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $produce['status'] === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ ucfirst($produce['status']) }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${{ $produce['progress_percentage'] }}%`"></div>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            {{ number_format($produce['progress_percentage'], 1) }}% Complete
                            @if(!$produce['can_produce'])
                                <span class="ml-2 text-red-500">⚠ Insufficient ingredients</span>
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p class="text-sm">No production records for this shift</p>
                </div>
            @endif
        </div>

    @else
        <!-- No Active Shift -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-8">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">No Active Shift</h3>
                <p class="text-yellow-700 dark:text-yellow-300">
                    There is no active shift for your department today. Please contact your supervisor.
                </p>
            </div>
        </div>
    @endif

    @endif

    @if($activeTab === 'stock-monitor')
        <!-- Stock Monitor Content -->
                    </select>
                </div>
            </div>

            @if(isset($rows) && $rows->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Product</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Produced</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Available</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Sent to Sales</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($rows as $row)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                                <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $row->product_name }}</td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($row->produced_quantity, 2) }} {{ $row->uom }}</td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($row->available_quantity, 2) }} {{ $row->uom }}</td>
                                <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($row->sent_out_quantity, 2) }} {{ $row->uom }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $row->status === 'not_sent' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' :
                                           ($row->status === 'partially_sent' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                           'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                        {{ ucfirst(str_replace('_', ' ', $row->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($row->available_quantity > 0)
                                        <button wire:click="openSendOutModal({{ $row->id }})"
                                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                            Send Out
                                        </button>
                                    @else
                                        <span class="text-xs text-zinc-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm">No production data available for the selected period</p>
                </div>
            @endif
        </div>

        <!-- Send Out Modal -->
        @if($showSendOutModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Send Product to Sales</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Quantity to Send</label>
                        <input type="number" wire:model="sendOutQuantity" step="0.01"
                               class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200"
                               placeholder="Enter quantity">
                        @error('sendOutQuantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Notes (Optional)</label>
                        <textarea wire:model="sendOutNotes" rows="3"
                                  class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200"
                                  placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="closeSendOutModal"
                            class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                        Cancel
                    </button>
                    <button wire:click="sendToSales"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        Send to Sales
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

</div>
