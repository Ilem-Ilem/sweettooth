<div class="p-6 space-y-6">
    <x-breadcrumb title="Stock Levels Report" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Inventory'],
        ['label' => 'Reports'],
        ['label' => 'Stock Levels'],
    ]" :compact="false" :with-icons="true" />

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-select.native label="Period Filter" wire:model.live="periodFilter">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </x-select.native>
            </div>

            @if($periodFilter === 'custom')
                <div class="flex-1 min-w-[200px]">
                    <x-input label="From Date" type="date" wire:model="customDateFrom" />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-input label="To Date" type="date" wire:model="customDateTo" />
                </div>
            @endif

            <div class="flex gap-2">
                <x-button color="primary" wire:click="generatePreview" :loading="$isLoading">
                    <x-icon name="arrow-path" class="w-4 h-4 mr-2" />
                    Generate Preview
                </x-button>

                @if($reportData)
                    <x-button color="secondary" wire:click="generateReport">
                        <x-icon name="document-check" class="w-4 h-4 mr-2" />
                        Save Report
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    @if($reportData)
        {{-- Summary Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Items</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['total_items'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-icon name="cube" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Stock Value</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            ₦{{ number_format($summaryMetrics['total_value'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Stock Health</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['stock_health'] ?? 0, 1) }}%
                        </p>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                        <x-icon name="heart" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Critical Items</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                            {{ number_format($summaryMetrics['critical_items'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Overview Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Low Stock Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 flex items-center">
                        <x-icon name="exclamation-circle" class="w-5 h-5 text-yellow-500 mr-2" />
                        Low Stock Items
                    </h3>
                </div>
                <div class="p-4">
                    @if(count($reportData['low_stock_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[400px] overflow-y-auto">
                            @foreach(($reportData['low_stock_items'] ?? []) as $item)
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                            {{ $item['current_stock'] }} {{ $item['uom'] }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Min: {{ $item['min_stock_level'] }} | Reorder: {{ $item['reorder_point'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No low stock items</p>
                    @endif
                </div>
            </div>

            {{-- Out of Stock Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 flex items-center">
                        <x-icon name="x-circle" class="w-5 h-5 text-red-500 mr-2" />
                        Out of Stock
                    </h3>
                </div>
                <div class="p-4">
                    @if(count($reportData['out_of_stock_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[400px] overflow-y-auto">
                            @foreach(($reportData['out_of_stock_items'] ?? []) as $item)
                                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                            OUT OF STOCK
                                        </span>
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Category: {{ $item['category'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No out of stock items</p>
                    @endif
                </div>
            </div>

            {{-- Overstock Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 flex items-center">
                        <x-icon name="arrow-trending-up" class="w-5 h-5 text-orange-500 mr-2" />
                        Overstock Items
                    </h3>
                </div>
                <div class="p-4">
                    @if(count($reportData['overstock_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[400px] overflow-y-auto">
                            @foreach(($reportData['overstock_items'] ?? []) as $item)
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                                            {{ $item['current_stock'] }} {{ $item['uom'] }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Max: {{ $item['max_stock_level'] }} | Over by: {{ $item['current_stock'] - $item['max_stock_level'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No overstock items</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Category Breakdown --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Category Breakdown</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Low Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Out of Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach(($reportData['category_breakdown'] ?? []) as $category)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $category['category'] }}</td>
                                    <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $category['item_count'] }}</td>
                                    <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">₦{{ number_format($category['total_value'], 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($category['low_stock_count'] > 0)
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                {{ $category['low_stock_count'] }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($category['out_of_stock_count'] > 0)
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                {{ $category['out_of_stock_count'] }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ABC Analysis --}}
        @if(isset($reportData['value_analysis']))
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">ABC Analysis</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Inventory classification by value</p>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 border-2 border-red-200 dark:border-red-800 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-red-900 dark:text-red-100">A Items - High Value</h4>
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">Top 80%</span>
                            </div>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $reportData['value_analysis']['a_items']['count'] }}</p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                ₦{{ number_format($reportData['value_analysis']['a_items']['value'], 2) }}
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                {{ $reportData['value_analysis']['a_items']['percentage'] }}% of total value
                            </p>
                        </div>

                        <div class="p-4 border-2 border-orange-200 dark:border-orange-800 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-orange-900 dark:text-orange-100">B Items - Medium Value</h4>
                                <span class="text-xs font-medium text-orange-600 dark:text-orange-400">80-95%</span>
                            </div>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $reportData['value_analysis']['b_items']['count'] }}</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                ₦{{ number_format($reportData['value_analysis']['b_items']['value'], 2) }}
                            </p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                {{ $reportData['value_analysis']['b_items']['percentage'] }}% of total value
                            </p>
                        </div>

                        <div class="p-4 border-2 border-green-200 dark:border-green-800 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-green-900 dark:text-green-100">C Items - Low Value</h4>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">95-100%</span>
                            </div>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['value_analysis']['c_items']['count'] }}</p>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                ₦{{ number_format($reportData['value_analysis']['c_items']['value'], 2) }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                {{ $reportData['value_analysis']['c_items']['percentage'] }}% of total value
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-zinc-100">No Report Generated</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Select a period and click "Generate Preview" to view the stock levels report.
            </p>
        </div>
    @endif

    {{-- Report Save Modal --}}
    @if($showReportModal && $generatedReport)
        <x-modal wire:model="showReportModal" title="Report Saved Successfully">
            <div class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Your stock levels report has been saved successfully and is ready for review.
                    </p>
                </div>

                <div class="space-y-2 text-sm">
                    <p><strong>Report ID:</strong> #{{ $generatedReport->id }}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $generatedReport->status)) }}</span></p>
                    <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($generatedReport->period_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($generatedReport->period_to)->format('M d, Y') }}</p>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button color="secondary" wire:click="$set('showReportModal', false)">
                        Close
                    </x-button>
                    <x-button color="primary" wire:click="submitForReview({{ $generatedReport->id }})">
                        Submit for Review
                    </x-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
