<div class="p-3 space-y-3">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">Stock Level Analytics</h2>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($summary['total_items']) }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Available Stock</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-500">{{ number_format($summary['total_available'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Low Stock Items</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ number_format($summary['low_stock_count']) }}</p>
                </div>
                <svg class="w-12 h-12 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Critical/Expired</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-500">{{ number_format($summary['critical_items'] + $summary['expired_items']) }}</p>
                </div>
                <svg class="w-12 h-12 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live="searchTerm" placeholder="Search by name or SKU..."
                   class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <select wire:model.live="selectedCategory"
                    class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}">{{ str_replace('_', ' ', ucfirst($category)) }}</option>
                @endforeach
            </select>

            <select wire:model.live="healthFilter"
                    class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                @foreach($healthStatuses as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="date" wire:model.live="dateFrom"
                       class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1">
                <input type="date" wire:model.live="dateTo"
                       class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1">
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Stock Levels Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Levels Overview</h3>
            <div id="stockLevelChart" class="h-80"></div>
        </div>

        <!-- Health Status Distribution -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Health Status Distribution</h3>
            <div id="healthDistributionChart" class="h-80"></div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Category Distribution -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Category Distribution</h3>
            <div id="categoryDistributionChart" class="h-80"></div>
        </div>

        <!-- Reserved vs Damaged -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Status Breakdown</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Reserved</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($summary['total_reserved'], 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $summary['total_available'] > 0 ? ($summary['total_reserved'] / $summary['total_available'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Damaged</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($summary['total_damaged'], 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $summary['total_available'] > 0 ? ($summary['total_damaged'] / $summary['total_available'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Items Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Items Details</h3>
        <div class="overflow-x-auto" wire:loading.class="opacity-50">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Available</th>
                        <th class="px-4 py-3">Reserved</th>
                        <th class="px-4 py-3">Damaged</th>
                        <th class="px-4 py-3">Reorder Level</th>
                        <th class="px-4 py-3">Health Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($stocks as $stock)
                        <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $stock->item->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stock->item->sku }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ str_replace('_', ' ', ucfirst($stock->item->category)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ number_format($stock->quantity_available, 2) }} {{ $stock->item->uom }}
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ number_format($stock->quantity_reserved, 2) }} {{ $stock->item->uom }}
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ number_format($stock->quantity_damaged, 2) }} {{ $stock->item->uom }}
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                @if($stock->item->reorder_level)
                                    {{ number_format($stock->item->reorder_level, 2) }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Not set</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeColors = match($stock->health_status) {
                                        'good' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                        default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                    {{ ucfirst($stock->health_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click="selectItem({{ $stock->item_id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No stock items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stocks->links() }}
        </div>
    </div>

    <!-- Item Details Modal -->
    @if($selectedItem)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click="clearItemSelection">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>

                <div class="relative bg-white dark:bg-zinc-800 rounded-lg max-w-2xl w-full p-6 border border-zinc-200 dark:border-zinc-700" wire:click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Item Details</h3>
                        <button wire:click="clearItemSelection" class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Item Name</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedItem->item->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">SKU</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedItem->item->sku }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Category</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ str_replace('_', ' ', ucfirst($selectedItem->item->category)) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Unit of Measure</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ strtoupper($selectedItem->item->uom) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Available Quantity</p>
                                <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($selectedItem->quantity_available, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Reserved Quantity</p>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ number_format($selectedItem->quantity_reserved, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Damaged Quantity</p>
                                <p class="font-medium text-red-600 dark:text-red-400">{{ number_format($selectedItem->quantity_damaged, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Average Cost</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($selectedItem->average_cost, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Health Status</p>
                                @php
                                    $badgeColors = match($selectedItem->health_status) {
                                        'good' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                        default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                    {{ ucfirst($selectedItem->health_status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Last Stock Take</p>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedItem->last_stock_take_date ? \Carbon\Carbon::parse($selectedItem->last_stock_take_date)->format('M d, Y') : 'Never' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button wire:click="clearItemSelection"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', function () {
            initCharts();
        });

        function initCharts() {
            // Stock Level Chart
            const stockLevelOptions = {
                series: @js($stockLevelChart['series']),
                chart: {
                    type: 'bar',
                    height: 320,
                    stacked: false,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @js($stockLevelChart['categories']),
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                    }
                },
                yaxis: {
                    title: {
                        text: 'Quantity'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#10B981', '#3B82F6', '#EF4444', '#F59E0B'],
                legend: {
                    position: 'top'
                }
            };
            new ApexCharts(document.querySelector("#stockLevelChart"), stockLevelOptions).render();

            // Health Distribution Chart
            const healthDistributionOptions = {
                series: @js($healthDistribution['series']),
                chart: {
                    type: 'donut',
                    height: 320
                },
                labels: @js($healthDistribution['labels']),
                colors: @js($healthDistribution['colors']),
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex]
                    }
                }
            };
            new ApexCharts(document.querySelector("#healthDistributionChart"), healthDistributionOptions).render();

            // Category Distribution Chart
            const categoryDistributionOptions = {
                series: @js($categoryDistribution['series']),
                chart: {
                    type: 'pie',
                    height: 320
                },
                labels: @js($categoryDistribution['labels']),
                legend: {
                    position: 'bottom'
                },
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']
            };
            new ApexCharts(document.querySelector("#categoryDistributionChart"), categoryDistributionOptions).render();
        }

        initCharts();
    </script>
</div>
