<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Production Efficiency Report</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Analyze production performance, variance, and efficiency metrics
            </p>
        </div>
        <div class="flex gap-2">
            <x-button wire:click="refresh" color="secondary" icon="arrow-path">
                Refresh
            </x-button>
            <x-button wire:click="generateReport" color="primary" icon="document-plus">
                Generate & Save Report
            </x-button>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Period Filter --}}
            <div>
                <x-select.native
                    label="Period"
                    wire:model.live="periodFilter"
                    :options="[
                        ['label' => 'Today', 'value' => 'today'],
                        ['label' => 'Yesterday', 'value' => 'yesterday'],
                        ['label' => 'This Week', 'value' => 'week'],
                        ['label' => 'This Month', 'value' => 'month'],
                        ['label' => 'Last Month', 'value' => 'last_month'],
                        ['label' => 'Custom Range', 'value' => 'custom'],
                    ]"
                    select="label:label|value:value"
                />
            </div>

            {{-- Custom Date From --}}
            <div>
                <x-input
                    label="From Date"
                    type="date"
                    wire:model="customDateFrom"
                    :disabled="$periodFilter !== 'custom'"
                />
            </div>

            {{-- Custom Date To --}}
            <div>
                <x-input
                    label="To Date"
                    type="date"
                    wire:model="customDateTo"
                    :disabled="$periodFilter !== 'custom'"
                />
            </div>

            {{-- Generate Button --}}
            <div class="flex items-end">
                <x-button
                    wire:click="generatePreview"
                    wire:loading.attr="disabled"
                    wire:target="generatePreview"
                    color="primary"
                    class="w-full"
                >
                    <span wire:loading.remove wire:target="generatePreview">
                        <x-icon name="chart-bar" class="w-5 h-5 mr-2" />
                        Generate Preview
                    </span>
                    <span wire:loading wire:target="generatePreview">
                        <x-icon name="arrow-path" class="animate-spin w-5 h-5 mr-2" />
                        Generating...
                    </span>
                </x-button>
            </div>
        </div>
    </div>

    @if($reportData)
        {{-- Summary Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Total Planned --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Planned</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($summaryMetrics['total_planned'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-icon name="calendar" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            {{-- Total Actual --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Actual</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($summaryMetrics['total_actual'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-icon name="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            {{-- Variance --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Variance</p>
                        <p class="mt-2 text-3xl font-bold {{ ($summaryMetrics['total_variance'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($summaryMetrics['total_variance'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($summaryMetrics['total_variance'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 {{ ($summaryMetrics['total_variance'] ?? 0) >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-lg">
                        <x-icon name="{{ ($summaryMetrics['total_variance'] ?? 0) >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="w-8 h-8 {{ ($summaryMetrics['total_variance'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                    </div>
                </div>
            </div>

            {{-- Overall Efficiency --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overall Efficiency</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($summaryMetrics['overall_efficiency'] ?? 0, 1) }}%
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <x-icon name="chart-pie" class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daily Efficiency Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Production Trend</h3>
                <div class="h-64 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <x-icon name="chart-bar" class="w-16 h-16 mx-auto mb-2" />
                        <p>Chart will be rendered here</p>
                        <p class="text-sm">(Planned vs Actual Production)</p>
                    </div>
                </div>
            </div>

            {{-- Variance Distribution --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Variance Distribution</h3>
                <div class="h-64 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <x-icon name="chart-pie" class="w-16 h-16 mx-auto mb-2" />
                        <p>Pie Chart will be rendered here</p>
                        <p class="text-sm">(Over/Under/On-Target)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Efficiency Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Efficiency Analysis</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Planned</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Efficiency %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reportData['product_efficiency'] ?? [] as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $product['product_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                    {{ number_format($product['planned']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                    {{ number_format($product['actual']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <span class="{{ $product['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ $product['variance'] >= 0 ? '+' : '' }}{{ number_format($product['variance']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $product['efficiency_percentage'] >= 95 ? 'bg-green-100 text-green-800' : ($product['efficiency_percentage'] >= 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($product['efficiency_percentage'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No product data available for this period
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Export Actions --}}
        <div class="flex justify-end gap-4">
            <x-button wire:click="exportCsv" color="secondary" icon="document-arrow-down">
                Export CSV
            </x-button>
            <x-button wire:click="exportPdf" color="secondary" icon="document-text">
                Export PDF
            </x-button>
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12">
            <div class="text-center">
                <x-icon name="chart-bar" class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No Report Generated</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Select a date range and click "Generate Preview" to view the production efficiency report
                </p>
            </div>
        </div>
    @endif

    {{-- Report Modal --}}
    @if($showReportModal && $generatedReport)
        <x-modal wire:model="showReportModal" title="Report Generated Successfully" size="lg">
            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Your production efficiency report has been generated and saved.
                </p>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Report ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ substr($generatedReport->id, 0, 8) }}...</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ ucfirst(str_replace('_', ' ', $generatedReport->status)) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($generatedReport->period_from)->format('M d, Y') }} -
                                {{ \Carbon\Carbon::parse($generatedReport->period_to)->format('M d, Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Generated At</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $generatedReport->created_at->format('M d, Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <x-button wire:click="$set('showReportModal', false)" color="secondary">
                        Close
                    </x-button>
                    <x-button wire:click="submitForReview('{{ $generatedReport->id }}')" color="primary">
                        Submit for Review
                    </x-button>
                </div>
            </div>
        </x-modal>
    @endif
</div>
