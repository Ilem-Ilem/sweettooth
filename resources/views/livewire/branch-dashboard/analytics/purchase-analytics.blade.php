<div class="p-3 space-y-3">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">Purchase Analytics</h2>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Purchases</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($summary['total_purchases']) }}</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Spent</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-500">₦{{ number_format($summary['total_spent'], 2) }}</p>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Avg Purchase Value</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">₦{{ number_format($summary['avg_purchase_value'], 2) }}</p>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ number_format($summary['total_items'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live="supplierFilter" placeholder="Search supplier..."
                   class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select wire:model.live="paymentStatus"
                    class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
                <option value="pending">Pending</option>
            </select>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">From</label>
                <input type="date" wire:model.live="dateFrom"
                       class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">To</label>
                <input type="date" wire:model.live="dateTo"
                       class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2 bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Purchase Trend</h3>
            <div id="purchaseTrendChart" class="h-80"></div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top Suppliers by Spending</h3>
            <div id="supplierChart" class="h-80"></div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Cost Breakdown</h3>
            <div id="costBreakdownChart" class="h-80"></div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top 10 Purchased Items</h3>
            <div class="space-y-2">
                @foreach($topItems as $item)
                    <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-zinc-700/50 rounded">
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->item->name }}</span>
                        <div class="text-right">
                            <p class="font-bold text-green-600 dark:text-green-400">₦{{ number_format($item->total_cost, 2) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($item->total_quantity, 2) }} {{ $item->item->uom }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Purchase Records Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Purchase Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3">Purchase #</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Total Cost</th>
                        <th class="px-4 py-3">Payment Status</th>
                        <th class="px-4 py-3">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($purchases as $purchase)
                        <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $purchase->purchase_number }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $purchase->supplier_name }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">₦{{ number_format($purchase->total_cost, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeColors = match($purchase->payment_status) {
                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'pending' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $purchase->recorder->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No purchases found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $purchases->links() }}</div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            new ApexCharts(document.querySelector("#purchaseTrendChart"), {
                series: @js($trendData['series']),
                chart: { type: 'line', height: 320 },
                xaxis: { categories: @js($trendData['categories']) },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#10B981', '#3B82F6']
            }).render();

            new ApexCharts(document.querySelector("#supplierChart"), {
                series: @js($supplierAnalysis['series']),
                chart: { type: 'bar', height: 320 },
                plotOptions: { bar: { horizontal: true } },
                xaxis: { categories: @js($supplierAnalysis['labels']) },
                colors: ['#3B82F6']
            }).render();

            new ApexCharts(document.querySelector("#costBreakdownChart"), {
                series: @js($costBreakdown['series']),
                chart: { type: 'pie', height: 320 },
                labels: @js($costBreakdown['labels']),
                colors: ['#3B82F6', '#F59E0B', '#10B981']
            }).render();
        });
    </script>
    @endpush
</div>
