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

    <x-breadcrumb title="Stock Analytics" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Analytics'],
        ['label' => 'Stock Analytics'],
    ]" :compact="false" :with-icons="true" />

    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">Total Branch Value</p>
                <p class="text-2xl font-bold mt-1">₦{{ number_format($totalStockValue, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4 space-y-4">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Filters & Options</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Item</label>
                <select wire:model.live="selectedItemId"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select Item --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected($selectedItemId == $item->id)>{{ $item->name }}
                            ({{ $item->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Time Range</label>
                <select wire:model.live="timeRange"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="day" @selected($timeRange == 'day')>Last 24 Hours</option>
                    <option value="week" @selected($timeRange == 'week')>Last Week</option>
                    <option value="month" @selected($timeRange == 'month')>Last Month</option>
                    <option value="year" @selected($timeRange == 'year')>Last Year</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Custom Date Range</label>
                <div class="flex space-x-2">
                    <input type="date" wire:model.live="dateFrom"
                        class="w-1/2 px-2 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 text-sm">
                    <input type="date" wire:model.live="dateTo"
                        class="w-1/2 px-2 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>
    </div>

    @if ($selectedItem)
        <!-- Loading Indicator -->
        <div wire:loading
            class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span>Updating...</span>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Item Stock Value</p>
                        <p class="text-2xl font-bold mt-1">₦{{ number_format($selectedItemStockValue, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>

            @if ($stockSummary)
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Current Available</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stockSummary->current_available, 2) }}
                                {{ $selectedItem->uom }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Movements</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stockSummary->total_movements) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Avg Movement</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stockSummary->avg_movement, 2) }}
                                {{ $selectedItem->uom }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" wire:key="charts-container-{{ $selectedItemId }}">
            <!-- Stock Movement Trend -->
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Stock Movement Trend</h3>
                    <div class="flex items-center space-x-2">
                        <select wire:model.live="trendViewMode"
                            class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="chart" @selected($trendViewMode == 'chart')>Chart</option>
                            <option value="table" @selected($trendViewMode == 'table')>Table</option>
                        </select>
                        @if ($trendViewMode === 'chart')
                            <select wire:model.live="trendChartType"
                                class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                                <option value="line" @selected($trendChartType == 'line')>Line</option>
                                <option value="bar" @selected($trendChartType == 'bar')>Bar</option>
                            </select>
                        @endif
                    </div>
                </div>
                @if ($trendViewMode === 'chart')
                    <div id="trendChart" style="height: 300px;" wire:key="trendChart-{{ $selectedItemId }}"></div>
                @else
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50 sticky top-0">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Date</th>
                                    <th
                                        class="px-2 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Before</th>
                                    <th
                                        class="px-2 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        After</th>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Type</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($stockMovementTrend as $movement)
                                    <tr>
                                        <td class="px-2 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $movement->movement_date->format('Y-m-d H:i') }}</td>
                                        <td class="px-2 py-2 text-right text-zinc-600 dark:text-zinc-400">
                                            {{ number_format($movement->quantity_before, 2) }}</td>
                                        <td
                                            class="px-2 py-2 text-right text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ number_format($movement->quantity_after, 2) }}</td>
                                        <td class="px-2 py-2">
                                            <span
                                                class="px-2 py-0.5 text-xs rounded-full {{ $movement->type === 'in' || $movement->type === 'return' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-2 py-6 text-center text-zinc-500">No data
                                            available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $stockMovementTrend->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Movement Types Distribution -->
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Movement Types</h3>
                    <div class="flex items-center space-x-2">
                        <select wire:model.live="typesViewMode"
                            class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="chart" @selected($typesViewMode == 'chart')>Chart</option>
                            <option value="table" @selected($typesViewMode == 'table')>Table</option>
                        </select>
                        @if ($typesViewMode === 'chart')
                            <select wire:model.live="typesChartType"
                                class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                                <option value="bar" @selected($typesChartType == 'bar')>Bar</option>
                                <option value="pie" @selected($typesChartType == 'pie')>Pie</option>
                                <option value="donut" @selected($typesChartType == 'donut')>Donut</option>
                            </select>
                        @endif
                    </div>
                </div>
                @if ($typesViewMode === 'chart')
                    <div id="typesChart" style="height: 300px;" wire:key="typesChart-{{ $selectedItemId }}"></div>
                @else
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Type</th>
                                    <th
                                        class="px-2 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($movementsByType as $movement)
                                    <tr>
                                        <td class="px-2 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ ucfirst($movement->type) }}</td>
                                        <td
                                            class="px-2 py-2 text-right text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ number_format($movement->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-2 py-6 text-center text-zinc-500">No data
                                            available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $movementsByType->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Department Usage -->
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Department Usage</h3>
                    <div class="flex items-center space-x-2">
                        <select wire:model.live="deptViewMode"
                            class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="chart" @selected($deptViewMode == 'chart')>Chart</option>
                            <option value="table" @selected($deptViewMode == 'table')>Table</option>
                        </select>
                        @if ($deptViewMode === 'chart')
                            <select wire:model.live="deptChartType"
                                class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                                <option value="bar" @selected($deptChartType == 'bar')>Bar</option>
                                <option value="pie" @selected($deptChartType == 'pie')>Pie</option>
                                <option value="donut" @selected($deptChartType == 'donut')>Donut</option>
                            </select>
                        @endif
                    </div>
                </div>
                @if ($deptViewMode === 'chart')
                    <div id="deptChart" style="height: 300px;" wire:key="deptChart-{{ $selectedItemId }}"></div>
                @else
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Department</th>
                                    <th
                                        class="px-2 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($departmentUsage as $dept)
                                    <tr>
                                        <td class="px-2 py-2 text-zinc-900 dark:text-zinc-100">
                                            {{ $dept->department_name }}</td>
                                        <td
                                            class="px-2 py-2 text-right text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ number_format($dept->total_quantity, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-2 py-6 text-center text-zinc-500">No data
                                            available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $departmentUsage->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Movement Frequency -->
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Movement Frequency</h3>
                    <div class="flex items-center space-x-2">
                        <select wire:model.live="freqViewMode"
                            class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="chart" @selected($freqViewMode == 'chart')>Chart</option>
                            <option value="table" @selected($freqViewMode == 'table')>Table</option>
                        </select>
                        @if ($freqViewMode === 'chart')
                            <select wire:model.live="freqChartType"
                                class="px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                                <option value="line" @selected($freqChartType == 'line')>Line</option>
                                <option value="bar" @selected($freqChartType == 'bar')>Bar</option>
                            </select>
                        @endif
                    </div>
                </div>
                @if ($freqViewMode === 'chart')
                    <div id="freqChart" style="height: 300px;" wire:key="freqChart-{{ $selectedItemId }}"></div>
                @else
                    <div class="overflow-x-auto scrollbar-thin">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Date</th>
                                    <th
                                        class="px-2 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                        Count</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($movementFrequency as $freq)
                                    <tr>
                                        <td class="px-2 py-2 text-zinc-900 dark:text-zinc-100">{{ $freq->date }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-right text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ $freq->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-2 py-6 text-center text-zinc-500">No data
                                            available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $movementFrequency->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stock Summary Cards -->
        @if ($stockSummary)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Inbound</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                                {{ number_format($stockSummary->total_in, 2) }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Outbound</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400 mt-1">
                                {{ number_format($stockSummary->total_out, 2) }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Adjustments</p>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                                {{ number_format($stockSummary->total_adjustments, 2) }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Damaged</p>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                                {{ number_format($stockSummary->total_damaged, 2) }}</p>
                        </div>
                        <div
                            class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333 .192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-zinc-400 dark:text-zinc-500 mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-lg font-medium text-zinc-600 dark:text-zinc-400">No Item Selected</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-500 mt-1">Please select an item from the dropdown above to
                view analytics</p>
        </div>
    @endif

    <!-- Additional Analytics Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top Moving Items</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Item</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Movements</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Total Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($topMovingItems as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $item->name }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                        {{ $item->movement_count }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($item->total_quantity, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-zinc-500">No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Low Stock Alerts</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Item</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Available</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                    Reorder</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $item->name }}
                                    </td>
                                    <td
                                        class="px-4 py-2 text-sm text-right text-red-600 dark:text-red-400 font-semibold">
                                        {{ number_format($item->quantity_available, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-right text-zinc-600 dark:text-zinc-400">
                                        {{ number_format($item->reorder_level, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-4 py-6 text-center text-green-600 dark:text-green-400">All items are
                                        well stocked!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize chart storage
            window.apexCharts = window.apexCharts || {};

            // Theme settings
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#e4e4e7' : '#18181b';
            const gridColor = isDark ? '#3f3f46' : '#e4e4e7';

            // Base chart options
            const baseOptions = {
                chart: {
                    fontFamily: 'inherit',
                    toolbar: {
                        show: true
                    },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        speed: 400,
                    },
                },
                theme: {
                    mode: isDark ? 'dark' : 'light'
                },
                grid: {
                    borderColor: gridColor
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                },
                legend: {
                    labels: {
                        colors: textColor
                    },
                    position: 'bottom'
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light'
                },
            };

            // Destroy all charts to prevent memory leaks
            function destroyCharts() {
                ['trend', 'types', 'dept', 'freq'].forEach(chart => {
                    if (window.apexCharts[chart]) {
                        try {
                            window.apexCharts[chart].destroy();
                        } catch (e) {
                            // console.error(`Error destroying ${chart} chart:`, e);
                        }
                        delete window.apexCharts[chart];
                    }
                });
            }

            // Render Trend Chart
            function renderTrendChart() {
                const el = document.getElementById('trendChart');
                if (!el || !el.offsetParent) {
                    // console.log('Trend chart element not found or not visible');
                    return;
                }

                const data = @json($stockMovementTrend ?? []);
                console.log(data);
                const chartType = '{{ $trendChartType ?? 'line' }}';
                const viewMode = '{{ $trendViewMode ?? 'chart' }}';

                if (viewMode !== 'chart' || !data || data.length === 0) {
                    // console.log('Trend chart: Invalid view mode or no data');
                    return;
                }

                if (window.apexCharts.trend) {
                    window.apexCharts.trend.destroy();
                    delete window.apexCharts.trend;
                }

                // console.log('Rendering trend chart with data:', data);

                const options = {
                    ...baseOptions,
                    chart: {
                        ...baseOptions.chart,
                        type: chartType === 'line' ? 'line' : 'bar',
                        height: 300
                    },
                    series: [{
                        name: 'Stock Level',
                        data: data.map(d => ({
                            x: new Date(d.date).getTime(),
                            y: parseFloat(d.quantity_after),
                        })),
                    }],
                    colors: ['#3b82f6'],
                    stroke: {
                        curve: 'smooth',
                        width: chartType === 'line' ? 3 : 2
                    },
                    fill: {
                        type: 'solid'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeUTC: false,
                            format: 'yyyy-MM-dd HH:mm'
                        },
                    },
                };

                window.apexCharts.trend = new ApexCharts(el, options);
                window.apexCharts.trend.render();
            }

            // Render Types Chart
            function renderTypesChart() {
                const el = document.getElementById('typesChart');
                if (!el || !el.offsetParent) {
                    // console.log('Types chart element not found or not visible');
                    return;
                }

                const data = @json($movementsByType ?? []);
                const chartType = '{{ $typesChartType ?? 'bar' }}';
                const viewMode = '{{ $typesViewMode ?? 'chart' }}';

                if (viewMode !== 'chart' || !data || Object.keys(data).length === 0) {
                    // console.log('Types chart: Invalid view mode or no data');
                    return;
                }

                if (window.apexCharts.types) {
                    window.apexCharts.types.destroy();
                    delete window.apexCharts.types;
                }

                const labels = Object.keys(data).map(k => k.charAt(0).toUpperCase() + k.slice(1));
                const values = Object.values(data).map(v => parseFloat(v));

                // console.log('Rendering types chart with labels:', labels, 'values:', values);

                const options = {
                    ...baseOptions,
                    chart: {
                        ...baseOptions.chart,
                        type: chartType === 'bar' ? 'bar' : (chartType === 'donut' ? 'donut' : 'pie'),
                        height: 300,
                    },
                    series: chartType === 'bar' ? [{
                        name: 'Quantity',
                        data: values
                    }] : values,
                    labels: labels,
                    colors: ['#22c55e', '#ef4444', '#3b82f6', '#fbbf24', '#a855f7', '#ec4899'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            distributed: true
                        },
                        pie: {
                            donut: {
                                size: chartType === 'donut' ? '70%' : '0%'
                            }
                        },
                    },
                    xaxis: {
                        categories: chartType === 'bar' ? labels : undefined
                    },
                    legend: {
                        show: ['pie', 'donut'].includes(chartType)
                    },
                };

                window.apexCharts.types = new ApexCharts(el, options);
                window.apexCharts.types.render();
            }

            // Render Department Chart
            function renderDeptChart() {
                const el = document.getElementById('deptChart');
                if (!el || !el.offsetParent) {
                    // console.log('Dept chart element not found or not visible');
                    return;
                }

                const data = @json($departmentUsage ?? []);
                const chartType = '{{ $deptChartType ?? 'bar' }}';
                const viewMode = '{{ $deptViewMode ?? 'chart' }}';

                if (viewMode !== 'chart' || !data || data.length === 0) {
                    // console.log('Dept chart: Invalid view mode or no data');
                    return;
                }

                if (window.apexCharts.dept) {
                    window.apexCharts.dept.destroy();
                    delete window.apexCharts.dept;
                }

                const labels = data.map(d => d.department_name);
                const values = data.map(d => parseFloat(d.total_quantity));

                // console.log('Rendering dept chart with labels:', labels, 'values:', values);

                const options = {
                    ...baseOptions,
                    chart: {
                        ...baseOptions.chart,
                        type: chartType === 'bar' ? 'bar' : (chartType === 'donut' ? 'donut' : 'pie'),
                        height: 300,
                    },
                    series: chartType === 'bar' ? [{
                        name: 'Quantity Dispatched',
                        data: values
                    }] : values,
                    labels: labels,
                    colors: ['#3b82f6', '#10b981', '#fbbf24', '#ef4444', '#a855f7', '#ec4899'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            distributed: true
                        },
                        pie: {
                            donut: {
                                size: chartType === 'donut' ? '70%' : '0%'
                            }
                        },
                    },
                    xaxis: {
                        categories: chartType === 'bar' ? labels : undefined
                    },
                    legend: {
                        show: ['pie', 'donut'].includes(chartType)
                    },
                };

                window.apexCharts.dept = new ApexCharts(el, options);
                window.apexCharts.dept.render();
            }

            // Render Frequency Chart
            function renderFreqChart() {
                const el = document.getElementById('freqChart');
                if (!el || !el.offsetParent) {
                    // console.log('Freq chart element not found or not visible');
                    return;
                }

                const data = @json($movementFrequency ?? []);
                const chartType = '{{ $freqChartType ?? 'line' }}';
                const viewMode = '{{ $freqViewMode ?? 'chart' }}';

                if (viewMode !== 'chart' || !data || data.length === 0) {
                    // console.log('Freq chart: Invalid view mode or no data');
                    return;
                }

                if (window.apexCharts.freq) {
                    window.apexCharts.freq.destroy();
                    delete window.apexCharts.freq;
                }

                // console.log('Rendering freq chart with data:', data);

                const options = {
                    ...baseOptions,
                    chart: {
                        ...baseOptions.chart,
                        type: chartType === 'line' ? 'line' : 'bar',
                        height: 300
                    },
                    series: [{
                        name: 'Movement Count',
                        data: data.map(d => ({
                            x: new Date(d.date).getTime(),
                            y: parseInt(d.count, 10),
                        })),
                    }],
                    colors: ['#a855f7'],
                    stroke: {
                        curve: 'smooth',
                        width: chartType === 'line' ? 3 : 2
                    },
                    fill: {
                        type: 'solid'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeUTC: false,
                            format: 'yyyy-MM-dd'
                        },
                    },
                };

                window.apexCharts.freq = new ApexCharts(el, options);
                window.apexCharts.freq.render();
            }

            // Initialize all charts
            function initAllCharts() {
                destroyCharts();
                // console.log('Initializing all charts for item:', '{{ $selectedItemId }}');
                setTimeout(() => {
                    renderTrendChart();
                    renderTypesChart();
                    renderDeptChart();
                    renderFreqChart();
                }, 100); // Increased delay to ensure DOM is ready
            }

            // Initialize charts when ApexCharts is loaded
            if (typeof ApexCharts !== 'undefined') {
                initAllCharts();
            } else {
                // console.log('ApexCharts not loaded, waiting...');
                const checkApex = setInterval(() => {
                    if (typeof ApexCharts !== 'undefined') {
                        // console.log('ApexCharts loaded, initializing charts');
                        clearInterval(checkApex);
                        initAllCharts();
                    }
                }, 100);
            }

            // Re-render on Livewire updates
            Livewire.on('charts-updated', () => {
                // console.log('Received charts-updated event');
                initAllCharts();
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', () => {
                destroyCharts();
            });
        });
    </script>
</div>
