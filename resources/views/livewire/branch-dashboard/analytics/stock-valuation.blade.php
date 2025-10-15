<div class="p-3 space-y-3">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-6">Stock Valuation</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-sm p-4">
                <p class="text-sm opacity-90">Total Value</p>
                <p class="text-2xl font-bold">₦{{ number_format($summary['total_value'], 2) }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Available</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-500">₦{{ number_format($summary['available_value'], 2) }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Reserved</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">₦{{ number_format($summary['reserved_value'], 2) }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Damaged</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-500">₦{{ number_format($summary['damaged_value'], 2) }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ $summary['total_items'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <input wire:model.live="searchTerm" placeholder="Search..." class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            <select wire:model.live="selectedCategory" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ str_replace('_', ' ', ucfirst($cat)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Valuation by Category</h3>
                <div id="categoryValuationChart" class="h-80"></div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top 10 Most Valuable Items</h3>
                <div class="space-y-2">
                    @foreach($topItems as $item)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-zinc-700/50 rounded">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->item->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ number_format($item->quantity_available + $item->quantity_reserved, 2) }} @ ₦{{ number_format($item->average_cost, 2) }}</p>
                            </div>
                            <p class="font-bold text-blue-600 dark:text-blue-400">₦{{ number_format($item->total_value, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Detailed Valuation</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <th wire:click="sortByColumn('item.name')" class="px-4 py-3 cursor-pointer">Item</th>
                            <th class="px-4 py-3">Category</th>
                            <th wire:click="sortByColumn('quantity_available')" class="px-4 py-3 cursor-pointer">Qty Available</th>
                            <th class="px-4 py-3">Qty Reserved</th>
                            <th wire:click="sortByColumn('average_cost')" class="px-4 py-3 cursor-pointer">Avg Cost</th>
                            <th wire:click="sortByColumn('available_value')" class="px-4 py-3 cursor-pointer">Available Value</th>
                            <th wire:click="sortByColumn('total_value')" class="px-4 py-3 cursor-pointer">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($stocks as $stock)
                            <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-4 py-3">
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $stock->item->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stock->item->sku }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ str_replace('_', ' ', ucfirst($stock->item->category)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ number_format($stock->quantity_available, 2) }}</td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ number_format($stock->quantity_reserved, 2) }}</td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">₦{{ number_format($stock->average_cost, 2) }}</td>
                                <td class="px-4 py-3 font-medium text-green-600 dark:text-green-400">₦{{ number_format($stock->available_value, 2) }}</td>
                                <td class="px-4 py-3 font-bold text-blue-600 dark:text-blue-400">₦{{ number_format($stock->total_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $stocks->links() }}</div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            new ApexCharts(document.querySelector("#categoryValuationChart"), {
                series: @js($categoryValuation['series']),
                chart: { type: 'pie', height: 320 },
                labels: @js($categoryValuation['labels']),
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true, formatter: (val) => '₦' + val.toFixed(0) }
            }).render();
        });
    </script>
    @endpush
</div>
