<div class="p-3 space-y-3">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Movement Analytics</h2>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Movements</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($summary['total_movements']) }}</p>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stock In</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-500">{{ number_format($summary['total_in'], 2) }}</p>
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stock Out</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-500">{{ number_format($summary['total_out'], 2) }}</p>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Adjustments</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ number_format($summary['total_adjustments']) }}</p>
                </div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Damaged</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-500">{{ number_format($summary['total_damaged'], 2) }}</p>
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Transfers</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ number_format($summary['total_transfers']) }}</p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" wire:model.live="searchTerm" placeholder="Search by item..."
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <select wire:model.live="movementType"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($movementTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">From</label>
                <input type="date" wire:model.live="dateFrom"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">To</label>
                <input type="date" wire:model.live="dateTo"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Movement Trend --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Movement Trend</h3>
            <div id="movementTrendChart" class="h-80"></div>
        </div>

        {{-- Movement Type Distribution --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Movement Type Distribution</h3>
            <div id="typeDistributionChart" class="h-80"></div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Top Moved Items --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top 10 Most Moved Items</h3>
            <div class="space-y-3">
                @foreach($topMovedItems as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700/50 rounded">
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->stock->item->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->stock->item->sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($item->total_moved, 2) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->stock->item->uom }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Stock Velocity --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Velocity (Frequency)</h3>
            <div id="velocityChart" class="h-80"></div>
        </div>
    </div>

    {{-- Movements Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Recent Movements</h3>
        <div class="overflow-x-auto" wire:loading.class="opacity-50">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Before</th>
                        <th class="px-4 py-3">After</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Moved By</th>
                        <th class="px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($movements as $movement)
                        <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($movement->movement_date)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $movement->stock->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->stock->item->sku }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeColors = match($movement->type) {
                                        'in' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'out' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'transfer' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'damaged' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        'return' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                    {{ ucfirst($movement->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium {{ $movement->type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ number_format($movement->quantity_before, 2) }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ number_format($movement->quantity_after, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($movement->reference_type)
                                    <span class="text-xs text-zinc-900 dark:text-zinc-100">{{ $movement->reference_type }} #{{ $movement->reference_id }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($movement->mover)
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ $movement->mover->name }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">System</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-zinc-900 dark:text-zinc-100">{{ $movement->notes ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No movements found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:navigated', function () {
        initCharts();
    });

    function initCharts() {
        // Movement Trend Chart
        const trendOptions = {
            series: @js($trendData['series']),
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: true }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @js($trendData['categories'])
            },
            yaxis: {
                title: { text: 'Quantity' }
            },
            colors: ['#10B981', '#EF4444', '#F59E0B'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3
                }
            },
            legend: { position: 'top' }
        };
        new ApexCharts(document.querySelector("#movementTrendChart"), trendOptions).render();

        // Type Distribution Chart
        const typeOptions = {
            series: @js($typeDistribution['series']),
            chart: {
                type: 'donut',
                height: 320
            },
            labels: @js($typeDistribution['labels']),
            colors: ['#10B981', '#EF4444', '#F59E0B', '#3B82F6', '#F97316', '#8B5CF6'],
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector("#typeDistributionChart"), typeOptions).render();

        // Velocity Chart
        const velocityOptions = {
            series: @js($velocityAnalysis['series']),
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    distributed: true
                }
            },
            dataLabels: { enabled: true },
            xaxis: {
                categories: @js($velocityAnalysis['labels'])
            },
            colors: ['#3B82F6'],
            legend: { show: false }
        };
        new ApexCharts(document.querySelector("#velocityChart"), velocityOptions).render();
    }

    initCharts();
</script>
