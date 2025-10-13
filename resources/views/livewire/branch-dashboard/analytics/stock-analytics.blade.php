<div class="p-3 space-y-3">
    <x-breadcrumb
        title="Stock Analytics"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Analytics'],
            ['label' => 'Stock Analytics']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Item Selection & Controls -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Item</label>
                <select wire:model.live="selectedItemId"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="">Select an item...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->sku }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Time Range</label>
                <select wire:model.live="timeRange"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 3 Months</option>
                    <option value="180">Last 6 Months</option>
                    <option value="365">Last Year</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Chart Type</label>
                <select wire:model.live="chartType"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="line">Line Chart</option>
                    <option value="area">Area Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
            </div>
        </div>
    </div>

    @if($selectedItem)
        <!-- Stock Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Current Stock</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($selectedItem->getCurrentStock(request()->query('b_id')), 2) }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedItem->uom }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            @if($stockSummary)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Stock In</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            +{{ number_format($stockSummary->total_in ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedItem->uom }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Stock Out</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            -{{ number_format($stockSummary->total_out ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedItem->uom }}</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Movements</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($stockSummary->total_movements ?? 0) }}
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">transactions</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Stock Movement Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Stock Movement Over Time</h3>
            <div id="stockChart" wire:ignore></div>
        </div>

        <!-- Movement History Table -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Recent Stock Movements</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Before</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">After</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockMovementData as $movement)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $movement['date'] }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $movement['type'] === 'in' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $movement['type'] === 'out' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $movement['type'] === 'adjustment' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                    {{ ucfirst($movement['type']) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ number_format($movement['quantity'], 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($movement['quantity_before'], 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ number_format($movement['quantity_after'], 2) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $movement['notes'] ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No stock movements in the selected time range</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-8 text-center">
            <p class="text-zinc-600 dark:text-zinc-400">Please select an item to view analytics</p>
        </div>
    @endif

    <!-- Top Moving Items -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Top Moving Items (Last 30 Days)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($topMovingItems as $item)
            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                <div>
                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $item->sku }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $item->movement_count }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">movements</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let chart = null;

            function renderChart() {
                const movementData = @json($stockMovementData);
                const chartType = @json($chartType);

                if (!movementData || movementData.length === 0) {
                    return;
                }

                const dates = movementData.map(m => m.date);
                const quantities = movementData.map(m => m.quantity_after);

                const options = {
                    series: [{
                        name: 'Stock Level',
                        data: quantities
                    }],
                    chart: {
                        type: chartType,
                        height: 350,
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: dates,
                        labels: {
                            rotate: -45,
                            rotateAlways: true
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Quantity'
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy HH:mm'
                        }
                    },
                    theme: {
                        mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    }
                };

                if (chart) {
                    chart.destroy();
                }

                chart = new ApexCharts(document.querySelector("#stockChart"), options);
                chart.render();
            }

            renderChart();

            Livewire.on('refreshChart', () => {
                renderChart();
            });

            // Re-render chart when data changes
            Livewire.hook('morph.updated', ({ el, component }) => {
                setTimeout(() => renderChart(), 100);
            });
        });
    </script>
    @endpush
</div>
