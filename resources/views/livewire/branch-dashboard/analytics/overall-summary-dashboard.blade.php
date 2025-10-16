<div class="p-3 space-y-3">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Overall Analytics Summary</h2>

        {{-- Quick Links --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            <a href="{{ branch_route('branch-dashboard.analytics.stock-level') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Stock Level
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.stock-movement') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Stock Movement
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.purchase') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Purchases
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.request-dispatch') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Requests
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.stock-valuation') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Valuation
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.alerts') }}"
                class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded text-sm font-medium transition-colors">
                Alerts
            </a>
        </div>

        {{-- Date Filter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

    {{-- Summary Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-sm p-4">
            <p class="text-sm opacity-90">Total Stock Value</p>
            <p class="text-3xl font-bold">₦{{ number_format($summary['total_stock_value'], 2) }}</p>
            <p class="text-xs opacity-75 mt-2">{{ $summary['total_items'] }} items in inventory</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-sm p-4">
            <p class="text-sm opacity-90">Purchase Value</p>
            <p class="text-3xl font-bold">₦{{ number_format($summary['total_purchase_value'], 2) }}</p>
            <p class="text-xs opacity-75 mt-2">{{ $summary['total_purchases'] }} purchases</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-sm p-4">
            <p class="text-sm opacity-90">Stock Movements</p>
            <p class="text-3xl font-bold">{{ number_format($summary['total_movements']) }}</p>
            <p class="text-xs opacity-75 mt-2">In: {{ number_format($summary['stock_in'], 0) }} | Out: {{ number_format($summary['stock_out'], 0) }}</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-sm p-4">
            <p class="text-sm opacity-90">Requests</p>
            <p class="text-3xl font-bold">{{ $summary['total_requests'] }}</p>
            <p class="text-xs opacity-75 mt-2">Pending: {{ $summary['pending_requests'] }} | Done: {{ $summary['completed_requests'] }}</p>
        </div>
    </div>

    {{-- Alert Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg shadow-sm p-4">
            <div class="flex items-center gap-3">
                <svg class="w-10 h-10 text-yellow-600 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Low Stock Items</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ $summary['low_stock_items'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg shadow-sm p-4">
            <div class="flex items-center gap-3">
                <svg class="w-10 h-10 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Critical/Expired</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-500">{{ $summary['critical_items'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg shadow-sm p-4">
            <div class="flex items-center gap-3">
                <svg class="w-10 h-10 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Requests</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ $summary['pending_requests'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts and Data --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Stock Health Overview --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Stock Health Overview</h3>
            <div class="h-80 flex items-center justify-center">
                <canvas id="healthOverviewChart"></canvas>
            </div>
        </div>

        {{-- Top Alerts --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top Alerts</h3>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($alerts as $alert)
                    <div class="p-3 rounded {{ $alert['type'] === 'critical' ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500' : 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500' }}">
                        <div class="flex items-start gap-2">
                            @if($alert['type'] === 'critical')
                                <svg class="w-5 h-5 text-red-600 dark:text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @endif
                            <div>
                                <p class="font-medium {{ $alert['type'] === 'critical' ? 'text-red-800 dark:text-red-300' : 'text-yellow-800 dark:text-yellow-300' }}">
                                    {{ $alert['message'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No alerts at this time</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Recent Stock Activity</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3">Date/Time</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Moved By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($recentActivity as $activity)
                        <tr class="bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($activity->movement_date)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $activity->stock->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->stock->item->sku }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeColors = match($activity->type) {
                                        'in' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'out' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'transfer' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'damaged' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeColors }}">
                                    {{ ucfirst($activity->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium {{ $activity->type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $activity->type === 'in' ? '+' : '-' }}{{ number_format($activity->quantity, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ $activity->mover->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No recent activity
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the chart container
        const chartContainer = document.getElementById('healthOverviewChart');
        console.log(@json($healthOverview['series']))
        // Create the chart
        const healthOverviewChart = new Chart(chartContainer, {
            type: 'doughnut',
            data: {
                labels: @json($healthOverview['labels']),
                datasets: [{
                    data: @json($healthOverview['series']),
                    
                    backgroundColor: [
                        '#10B981', // Green for healthy
                        '#F59E0B', // Yellow for low stock
                        '#EF4444', // Red for critical
                        '#6B7280'  // Gray for expired
                    ],
                    borderColor: [
                        '#10B981',
                        '#F59E0B', 
                        '#EF4444',
                        '#6B7280'
                    ],
                    borderWidth: 1,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--tw-text-zinc-800').trim() || 
                                   (document.documentElement.classList.contains('dark') ? '#f4f4f5' : '#27272a'),
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
        
        // Function to update chart when new data arrives
        function updateChart(newData) {
            healthOverviewChart.data.datasets[0].data = newData.series;
            healthOverviewChart.data.labels = newData.labels;
            healthOverviewChart.update();
        }
        
        // Listen for Livewire events if needed
        document.addEventListener('livewire:navigated', function() {
            // Chart will be reinitialized when Livewire navigates
        });
    });
</script>