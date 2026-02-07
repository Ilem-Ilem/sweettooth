<div class="p-6 space-y-6">
    <x-breadcrumb title="Stock Movement Report" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Inventory'],
        ['label' => 'Reports'],
        ['label' => 'Stock Movement'],
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Movements</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['total_movements'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-icon name="arrows-right-left" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Inbound</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ number_format($summaryMetrics['movements_in'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-icon name="arrow-down-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Outbound</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                            {{ number_format($summaryMetrics['movements_out'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <x-icon name="arrow-up-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Net Value</p>
                        <p class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            ₦{{ number_format($summaryMetrics['net_movement_value'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <x-icon name="currency-dollar" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Daily Average</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['avg_daily_movements'] ?? 0, 1) }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <x-icon name="chart-bar" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Narrative Insights --}}
        @if(!empty($narrative))
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Insights</h3>
                @if(!empty($narrative['overview']))
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $narrative['overview'] }}</p>
                @endif
                @if(!empty($narrative['highlights']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Highlights</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['highlights'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($narrative['concerns']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Concerns</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['concerns'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($narrative['recommendations']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Recommendations</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['recommendations'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
        {{-- Movement by Category --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Movements by Category</h3>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Movements</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Inbound</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Outbound</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Net Movement</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Net Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach(($reportData['by_category'] ?? []) as $category)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $category['category'] }}</td>
                                    <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($category['movements_count']) }}</td>
                                    <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400">{{ number_format($category['in_quantity']) }}</td>
                                    <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">{{ number_format($category['out_quantity']) }}</td>
                                    <td class="px-4 py-3 text-sm font-medium {{ $category['net_movement'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $category['net_movement'] >= 0 ? '+' : '' }}{{ number_format($category['net_movement']) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium {{ $category['total_value'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        ₦{{ number_format($category['total_value'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Moving Items --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top Moving Items</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach(array_slice($reportData['by_item'] ?? [], 0, 12) as $item)
                        <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</h4>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    {{ $item['movements_count'] }} moves
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mt-3">
                                <div>
                                    <span class="text-zinc-500 dark:text-zinc-400">In:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">{{ number_format($item['in_quantity']) }}</span>
                                </div>
                                <div>
                                    <span class="text-zinc-500 dark:text-zinc-400">Out:</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($item['out_quantity']) }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-zinc-500 dark:text-zinc-400">Net:</span>
                                    <span class="font-medium {{ $item['net_movement'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item['net_movement'] >= 0 ? '+' : '' }}{{ number_format($item['net_movement']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Movements by Source --}}
        @if(count($reportData['by_source'] ?? []) > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Movements by Source</h3>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Source</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Total Movements</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Inbound</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Outbound</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Total Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach(($reportData['by_source'] ?? []) as $source)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                        <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $source['source'] }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($source['movements_count']) }}</td>
                                        <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400">{{ number_format($source['in_count']) }}</td>
                                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">{{ number_format($source['out_count']) }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">₦{{ number_format($source['total_value'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Data Tables --}}
        @if(!empty($tablesData))
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Data Tables</h3>
                </div>
                <div class="p-4 space-y-6">
                    @foreach($tablesData as $tableKey => $table)
                        <div>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $tableKey)) }}
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                    <thead>
                                        <tr>
                                            @foreach(($table['headers'] ?? []) as $header)
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                                    {{ $header }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @foreach(($table['rows'] ?? []) as $row)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                                @foreach($row as $cell)
                                                    <td class="px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300">
                                                        {{ is_numeric($cell) ? number_format($cell, 2) : $cell }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-zinc-100">No Report Generated</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Select a period and click "Generate Preview" to view the stock movement report.
            </p>
        </div>
    @endif

    {{-- Report Save Modal --}}
    @if($showReportModal && $generatedReport)
        <x-modal wire:model="showReportModal" title="Report Saved Successfully">
            <div class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Your stock movement report has been saved successfully and is ready for review.
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
