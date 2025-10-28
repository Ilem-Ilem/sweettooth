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
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Select Item</label>
                <x-select.styled
                    wire:model.live="selectedItem"
                    :options="$availableItems"
                    select="label:name|value:id"
                    searchable
                    placeholder="Search items..."
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Movement Type</label>
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
            <div id="movementTrendChart" class="h-80" wire:ignore>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading chart...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Movement Type Distribution --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Movement Type Distribution</h3>
            <div id="typeDistributionChart" class="h-80" wire:ignore>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading chart...</p>
                    </div>
                </div>
            </div>
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
            <div id="velocityChart" class="h-80" wire:ignore>
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="animate-spin h-10 w-10 mx-auto text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading chart...</p>
                    </div>
                </div>
            </div>
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


    @push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    let trendChart, typeChart, velocityChart;
    let chartData = {
        trendData: @js($trendData),
        typeDistribution: @js($typeDistribution),
        velocityAnalysis: @js($velocityAnalysis)
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Highcharts !== 'undefined') {
            initCharts();
        }
    });

    document.addEventListener('livewire:navigated', function () {
        if (typeof Highcharts !== 'undefined') {
            initCharts();
        }
    });

    // Listen for chart update events from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('chartsUpdated', (event) => {
            const data = event[0];
            chartData.trendData = data.trendData;
            chartData.typeDistribution = data.typeDistribution;
            chartData.velocityAnalysis = data.velocityAnalysis;
            updateCharts();
        });
    });

    function initCharts() {
        if (typeof Highcharts === 'undefined') {
            console.error('Highcharts is not loaded');
            return;
        }
        // Destroy existing charts if they exist
        if (trendChart) trendChart.destroy();
        if (typeChart) typeChart.destroy();
        if (velocityChart) velocityChart.destroy();

        // Clear loading spinners
        const trendContainer = document.getElementById('movementTrendChart');
        const typeContainer = document.getElementById('typeDistributionChart');
        const velocityContainer = document.getElementById('velocityChart');
        if (trendContainer) trendContainer.innerHTML = '';
        if (typeContainer) typeContainer.innerHTML = '';
        if (velocityContainer) velocityContainer.innerHTML = '';

        const themeColors = getThemeColors();

        // Movement Trend Chart - Area/Spline Chart with Gradients
        trendChart = Highcharts.chart('movementTrendChart', {
            chart: {
                type: 'areaspline',
                height: 320,
                backgroundColor: 'transparent'
            },
            title: {
                text: null
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: chartData.trendData.categories,
                title: {
                    text: 'Date',
                    style: {
                        color: themeColors.textColor
                    }
                },
                labels: {
                    style: {
                        color: themeColors.textColor
                    }
                },
                gridLineColor: themeColors.gridColor,
                gridLineWidth: 1,
                gridLineDashStyle: 'Dot'
            },
            yAxis: {
                title: {
                    text: 'Quantity',
                    style: {
                        color: themeColors.textColor
                    }
                },
                labels: {
                    style: {
                        color: themeColors.textColor
                    }
                },
                gridLineColor: themeColors.gridColor,
                min: 0
            },
            tooltip: {
                shared: true,
                crosshairs: true,
                valueDecimals: 2,
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                borderRadius: 8,
                shadow: true,
                style: {
                    color: themeColors.textColor
                }
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.3,
                    marker: {
                        enabled: true,
                        radius: 5,
                        lineWidth: 2,
                        lineColor: '#ffffff'
                    },
                    lineWidth: 3
                }
            },
            series: [
                {
                    name: 'Stock In',
                    data: chartData.trendData.series[0].data,
                    color: '#10B981',
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                        stops: [
                            [0, 'rgba(16, 185, 129, 0.5)'],
                            [1, 'rgba(16, 185, 129, 0.05)']
                        ]
                    },
                    zIndex: 3
                },
                {
                    name: 'Stock Out',
                    data: chartData.trendData.series[1].data,
                    color: '#EF4444',
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                        stops: [
                            [0, 'rgba(239, 68, 68, 0.5)'],
                            [1, 'rgba(239, 68, 68, 0.05)']
                        ]
                    },
                    zIndex: 2
                },
                {
                    name: 'Adjustments',
                    data: chartData.trendData.series[2].data,
                    color: '#F59E0B',
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                        stops: [
                            [0, 'rgba(245, 158, 11, 0.5)'],
                            [1, 'rgba(245, 158, 11, 0.05)']
                        ]
                    },
                    zIndex: 1
                }
            ],
            legend: {
                align: 'center',
                verticalAlign: 'top',
                floating: false,
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                borderColor: themeColors.gridColor,
                borderRadius: 5,
                itemStyle: {
                    color: themeColors.textColor
                },
                itemHoverStyle: {
                    color: themeColors.textColor
                }
            },
            exporting: {
                enabled: true,
                buttons: {
                    contextButton: {
                        menuItems: ['viewFullscreen', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator', 'downloadCSV', 'downloadXLS']
                    }
                }
            },
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        }
                    }
                }]
            }
        });

        // Type Distribution Chart - Pie Chart
        const pieData = chartData.typeDistribution.labels.map((label, index) => ({
            name: label,
            y: chartData.typeDistribution.series[index]
        }));

        typeChart = Highcharts.chart('typeDistributionChart', {
            chart: {
                type: 'pie',
                height: 320,
                backgroundColor: 'transparent'
            },
            title: {
                text: null
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<b>{point.y}</b> movements ({point.percentage:.1f}%)',
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                style: {
                    color: themeColors.textColor
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            fontSize: '11px',
                            color: themeColors.textColor
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Movement Types',
                colorByPoint: true,
                data: pieData
            }],
            colors: ['#10B981', '#EF4444', '#F59E0B', '#3B82F6', '#F97316', '#8B5CF6'],
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal',
                itemStyle: {
                    color: themeColors.textColor
                },
                itemHoverStyle: {
                    color: themeColors.textColor
                }
            },
            exporting: {
                enabled: true,
                buttons: {
                    contextButton: {
                        menuItems: ['viewFullscreen', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator', 'downloadCSV', 'downloadXLS']
                    }
                }
            }
        });

        // Velocity Chart - Horizontal Bar Chart
        velocityChart = Highcharts.chart('velocityChart', {
            chart: {
                type: 'bar',
                height: 320,
                backgroundColor: 'transparent'
            },
            title: {
                text: null
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: chartData.velocityAnalysis.labels,
                title: {
                    text: null
                },
                labels: {
                    style: {
                        color: themeColors.textColor
                    }
                },
                gridLineColor: themeColors.gridColor
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Movement Frequency',
                    align: 'high',
                    style: {
                        color: themeColors.textColor
                    }
                },
                labels: {
                    style: {
                        color: themeColors.textColor
                    }
                },
                gridLineColor: themeColors.gridColor
            },
            tooltip: {
                valueSuffix: ' movements',
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                style: {
                    color: themeColors.textColor
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            color: themeColors.textColor
                        }
                    },
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Movement Frequency',
                data: chartData.velocityAnalysis.series[0].data,
                showInLegend: false
            }],
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#84CC16'],
            exporting: {
                enabled: true,
                buttons: {
                    contextButton: {
                        menuItems: ['viewFullscreen', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG', 'separator', 'downloadCSV', 'downloadXLS']
                    }
                }
            }
        });
    }

    function updateCharts() {
        // Update Trend Chart
        if (trendChart && chartData.trendData) {
            trendChart.series[0].setData(chartData.trendData.series[0].data, false);
            trendChart.series[1].setData(chartData.trendData.series[1].data, false);
            trendChart.series[2].setData(chartData.trendData.series[2].data, false);
            trendChart.xAxis[0].setCategories(chartData.trendData.categories, false);
            trendChart.redraw();
        }

        // Update Pie Chart
        if (typeChart && chartData.typeDistribution) {
            const pieData = chartData.typeDistribution.labels.map((label, index) => ({
                name: label,
                y: chartData.typeDistribution.series[index]
            }));
            typeChart.series[0].setData(pieData, true);
        }

        // Update Velocity Chart
        if (velocityChart && chartData.velocityAnalysis) {
            velocityChart.series[0].setData(chartData.velocityAnalysis.series[0].data, false);
            velocityChart.xAxis[0].setCategories(chartData.velocityAnalysis.labels, false);
            velocityChart.redraw();
        }
    }

    // Detect theme and return appropriate colors
    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            textColor: isDark ? '#e4e4e7' : '#27272a',
            gridColor: isDark ? '#3f3f46' : '#e4e4e7',
            backgroundColor: isDark ? '#27272a' : '#ffffff'
        };
    }

    // Initialize charts when script loads
    if (typeof Highcharts !== 'undefined') {
        initCharts();
    } else {
        // Wait for Highcharts to load
        setTimeout(() => {
            if (typeof Highcharts !== 'undefined') {
                initCharts();
            }
        }, 100);
    }
</script>
    @endpush
</div>
