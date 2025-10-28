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
                <div id="categoryValuationChart" class="h-80" wire:ignore>
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
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
                <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-100 mb-4">Top 10 Most Valuable Items</h3>
                <div id="topItemsChart" class="h-80" wire:ignore>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    let categoryChart, topItemsChart;
    let chartData = {
        categoryValuation: @js($categoryValuation),
        topItems: @js($topItems->map(function($item) {
            return [
                'name' => $item->item->name,
                'value' => $item->total_value,
                'quantity' => $item->quantity_available + $item->quantity_reserved,
                'avg_cost' => $item->average_cost
            ];
        })->values()->toArray())
    };

    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
    });

    document.addEventListener('livewire:navigated', function () {
        initCharts();
    });

    // Listen for chart update events from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('chartsUpdated', (event) => {
            const data = event[0];
            chartData.categoryValuation = data.categoryValuation;
            chartData.topItems = data.topItems;
            updateCharts();
        });
    });

    function initCharts() {
        // Destroy existing charts if they exist
        if (categoryChart) categoryChart.destroy();
        if (topItemsChart) topItemsChart.destroy();

        // Clear loading spinners
        const categoryContainer = document.getElementById('categoryValuationChart');
        const topItemsContainer = document.getElementById('topItemsChart');
        if (categoryContainer) categoryContainer.innerHTML = '';
        if (topItemsContainer) topItemsContainer.innerHTML = '';

        const themeColors = getThemeColors();

        // Category Valuation Chart - Pie Chart
        const pieData = chartData.categoryValuation.labels.map((label, index) => ({
            name: label,
            y: chartData.categoryValuation.series[index]
        }));

        categoryChart = Highcharts.chart('categoryValuationChart', {
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
                pointFormat: '<b>₦{point.y:,.2f}</b> ({point.percentage:.1f}%)',
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                borderRadius: 8,
                shadow: true,
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
                        format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                        style: {
                            fontSize: '11px',
                            color: themeColors.textColor
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Valuation',
                colorByPoint: true,
                data: pieData
            }],
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
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

        // Top Items Chart - Horizontal Bar Chart
        const topItemsData = chartData.topItems.map(item => item.value);
        const topItemsLabels = chartData.topItems.map(item => item.name);

        topItemsChart = Highcharts.chart('topItemsChart', {
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
                categories: topItemsLabels,
                title: {
                    text: null
                },
                labels: {
                    style: {
                        color: themeColors.textColor,
                        fontSize: '10px'
                    }
                },
                gridLineColor: themeColors.gridColor
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Value (₦)',
                    align: 'high',
                    style: {
                        color: themeColors.textColor
                    }
                },
                labels: {
                    style: {
                        color: themeColors.textColor
                    },
                    formatter: function() {
                        return '₦' + Highcharts.numberFormat(this.value, 0, '.', ',');
                    }
                },
                gridLineColor: themeColors.gridColor
            },
            tooltip: {
                backgroundColor: themeColors.backgroundColor,
                borderWidth: 1,
                borderRadius: 8,
                shadow: true,
                style: {
                    color: themeColors.textColor
                },
                formatter: function() {
                    const item = chartData.topItems[this.point.index];
                    return '<b>' + this.point.category + '</b><br/>' +
                           'Value: <b>₦' + Highcharts.numberFormat(this.y, 2, '.', ',') + '</b><br/>' +
                           'Qty: ' + Highcharts.numberFormat(item.quantity, 2) + '<br/>' +
                           'Avg Cost: ₦' + Highcharts.numberFormat(item.avg_cost, 2);
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            color: themeColors.textColor,
                            fontSize: '10px'
                        },
                        formatter: function() {
                            return '₦' + Highcharts.numberFormat(this.y, 0, '.', ',');
                        }
                    },
                    colorByPoint: true
                }
            },
            series: [{
                name: 'Item Value',
                data: topItemsData,
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
        // Update Category Chart
        if (categoryChart && chartData.categoryValuation) {
            const pieData = chartData.categoryValuation.labels.map((label, index) => ({
                name: label,
                y: chartData.categoryValuation.series[index]
            }));
            categoryChart.series[0].setData(pieData, true);
        }

        // Update Top Items Chart
        if (topItemsChart && chartData.topItems) {
            const topItemsData = chartData.topItems.map(item => item.value);
            const topItemsLabels = chartData.topItems.map(item => item.name);
            topItemsChart.series[0].setData(topItemsData, false);
            topItemsChart.xAxis[0].setCategories(topItemsLabels, false);
            topItemsChart.redraw();
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

    initCharts();
</script>
    @endpush
</div>
