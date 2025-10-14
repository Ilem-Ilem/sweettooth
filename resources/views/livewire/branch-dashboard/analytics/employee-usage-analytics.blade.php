<div class="p-3 space-y-3">
    <x-breadcrumb
        title="Employee Usage Analytics"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Analytics'],
            ['label' => 'Employee Usage']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Employee Selection & Controls -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select Employee</label>
                <select wire:model.live="selectedEmployeeId"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                    <option value="">Select an employee...</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->employee_number ?? 'N/A' }}</option>
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
        </div>
    </div>

    @if($selectedEmployee)
        <!-- Employee Summary Cards -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $selectedEmployee->name }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $selectedEmployee->employee_number ?? 'N/A' }} | {{ $selectedEmployee->position ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        @if($employeeSummary)
        <!-- Request Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Requests</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($employeeSummary->total_requests) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Approved</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($employeeSummary->approved) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ number_format($employeeSummary->pending) }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Rejected</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ number_format($employeeSummary->rejected) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Completed</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ number_format($employeeSummary->completed) }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Request Trends Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Request Trends Over Time</h3>
            <div id="requestTrendsChart" wire:ignore></div>
        </div>

        <!-- Most Requested Items -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Most Requested Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Item Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">SKU</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">Request Count</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mostRequestedItems as $item)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->sku }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($item->request_count) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                {{ number_format($item->total_quantity, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500">No item requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Recent Requests</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Request #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Department</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Items</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeRequests as $request)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $request->request_number }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $request->request_date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $request->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $request->requestDetails->count() }} items
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $request->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $request->status === 'completed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500">No recent requests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-8 text-center">
            <p class="text-zinc-600 dark:text-zinc-400">Please select an employee to view analytics</p>
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let chart = null;

            function renderChart() {
                const trendsData = @json($requestTrends);

                if (!trendsData || trendsData.length === 0) {
                    return;
                }

                const dates = trendsData.map(t => t.date);
                const counts = trendsData.map(t => t.count);

                const options = {
                    series: [{
                        name: 'Requests',
                        data: counts
                    }],
                    chart: {
                        type: 'area',
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
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
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
                            text: 'Number of Requests'
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.floor(val);
                            }
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy'
                        }
                    },
                    theme: {
                        mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    },
                    colors: ['#3b82f6']
                };

                if (chart) {
                    chart.destroy();
                }

                chart = new ApexCharts(document.querySelector("#requestTrendsChart"), options);
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
