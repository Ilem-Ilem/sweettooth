<div class="p-3 space-y-3">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-6">Request & Dispatch Analytics</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Requests</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ number_format($summary['total_requests']) }}</p>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ number_format($summary['pending']) }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-500">{{ number_format($summary['approved']) }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-500">{{ number_format($summary['completed']) }}</p>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Dispatches</p>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-500">{{ number_format($summary['total_dispatches']) }}</p>
            </div>
            <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Fulfillment Rate</p>
                <p class="text-2xl font-bold text-teal-600 dark:text-teal-500">{{ number_format($summary['fulfillment_rate'], 1) }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <select wire:model.live="statusFilter" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">From</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">To</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Request Trend</h3>
                <div id="requestTrendChart" class="h-80"></div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Department Analysis</h3>
                <div id="departmentChart" class="h-80"></div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Fulfillment Status</h3>
                <div id="fulfillmentChart" class="h-80"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Recent Requests</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Request #</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Requested By</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Shift</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($requests as $request)
                            <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $request->request_number }}</td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $request->department->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $request->requestedBy->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeColors = match($request->status) {
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'approved' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $request->shift ? ucfirst($request->shift) : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $requests->links() }}</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            new ApexCharts(document.querySelector("#requestTrendChart"), {
                series: @js($trendData['series']),
                chart: { type: 'line', height: 320, stacked: false },
                xaxis: { categories: @js($trendData['categories']) },
                stroke: { curve: 'smooth', width: 2 },
                colors: ['#F59E0B', '#8B5CF6', '#10B981']
            }).render();

            new ApexCharts(document.querySelector("#departmentChart"), {
                series: @js($departmentAnalysis['series']),
                chart: { type: 'donut', height: 320 },
                labels: @js($departmentAnalysis['labels'])
            }).render();

            new ApexCharts(document.querySelector("#fulfillmentChart"), {
                series: @js($fulfillmentRate['series']),
                chart: { type: 'pie', height: 320 },
                labels: @js($fulfillmentRate['labels']),
                colors: ['#F59E0B', '#8B5CF6', '#10B981', '#EF4444']
            }).render();
        });
    </script>
</div>
