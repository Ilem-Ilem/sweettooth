<div class="p-6 space-y-6">
    <x-breadcrumb title="Reorder Report" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Inventory'],
        ['label' => 'Reports'],
        ['label' => 'Reorder'],
    ]" :compact="false" :with-icons="true" />

    {{-- Summary Metrics --}}
    @if($reportData)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Items</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['total_items'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-icon name="shopping-cart" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Critical Items</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                            {{ number_format($summaryMetrics['critical_items'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Urgent Items</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                            {{ number_format($summaryMetrics['urgent_items'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <x-icon name="clock" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Estimated Cost</p>
                        <p class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            ₦{{ number_format($summaryMetrics['estimated_cost'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Suppliers</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($summaryMetrics['unique_suppliers'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <x-icon name="building-storefront" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Priority Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Critical Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 flex items-center">
                        <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-500 mr-2" />
                        Critical Priority
                    </h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">Requires immediate action</p>
                </div>
                <div class="p-4">
                    @if(count($reportData['critical_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            @foreach(($reportData['critical_items'] ?? []) as $item)
                                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border-2 border-red-300 dark:border-red-700">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-bold rounded bg-red-600 text-white">
                                            CRITICAL
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Current:</span>
                                            <span class="font-medium text-red-600 dark:text-red-400">{{ $item['current_stock'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Order:</span>
                                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['suggested_order_qty'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Cost:</span>
                                            <span class="font-medium text-zinc-900 dark:text-zinc-100">₦{{ number_format($item['estimated_cost'], 2) }}</span>
                                        </div>
                                        @if($item['days_until_stockout'] !== null)
                                            <div>
                                                <span class="text-zinc-500 dark:text-zinc-400">Days left:</span>
                                                <span class="font-medium text-red-600 dark:text-red-400">{{ $item['days_until_stockout'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Supplier: {{ $item['supplier'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No critical items</p>
                    @endif
                </div>
            </div>

            {{-- Urgent Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border-b border-orange-200 dark:border-orange-800">
                    <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-100 flex items-center">
                        <x-icon name="clock" class="w-5 h-5 text-orange-500 mr-2" />
                        Urgent Priority
                    </h3>
                    <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">Order soon</p>
                </div>
                <div class="p-4">
                    @if(count($reportData['urgent_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            @foreach(($reportData['urgent_items'] ?? []) as $item)
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                                            URGENT
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Current:</span>
                                            <span class="font-medium">{{ $item['current_stock'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Order:</span>
                                            <span class="font-medium">{{ $item['suggested_order_qty'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="text-zinc-500 dark:text-zinc-400">Cost:</span>
                                            <span class="font-medium">₦{{ number_format($item['estimated_cost'], 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Supplier: {{ $item['supplier'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No urgent items</p>
                    @endif
                </div>
            </div>

            {{-- Normal Items --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 flex items-center">
                        <x-icon name="information-circle" class="w-5 h-5 text-blue-500 mr-2" />
                        Normal Priority
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Plan ahead</p>
                </div>
                <div class="p-4">
                    @if(count($reportData['normal_items'] ?? []) > 0)
                        <div class="space-y-3 max-h-[500px] overflow-y-auto">
                            @foreach(($reportData['normal_items'] ?? []) as $item)
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item['item_name'] }}</p>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-400">SKU: {{ $item['sku'] }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs mt-2">
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Current:</span>
                                            <span class="font-medium">{{ $item['current_stock'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-500 dark:text-zinc-400">Order:</span>
                                            <span class="font-medium">{{ $item['suggested_order_qty'] }} {{ $item['uom'] }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="text-zinc-500 dark:text-zinc-400">Cost:</span>
                                            <span class="font-medium">₦{{ number_format($item['estimated_cost'], 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                                        Supplier: {{ $item['supplier'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-zinc-500 dark:text-zinc-400 py-8">No normal priority items</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Supplier Breakdown --}}
        @if(count($reportData['by_supplier'] ?? []) > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Reorder by Supplier</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Group orders to minimize shipping costs</p>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach(($reportData['by_supplier'] ?? []) as $supplier)
                            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $supplier['supplier'] }}</h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $supplier['item_count'] }} items</p>
                                    </div>
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                        ₦{{ number_format($supplier['total_cost'], 2) }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($supplier['items'] as $item)
                                        <div class="flex justify-between text-sm py-1 border-t border-zinc-100 dark:border-zinc-700">
                                            <span class="text-zinc-700 dark:text-zinc-300">{{ $item['item_name'] }}</span>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ $item['quantity'] }} units</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Category Breakdown --}}
        @if(count($reportData['by_category'] ?? []) > 0)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Reorder by Category</h3>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Items</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Critical</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach(($reportData['by_category'] ?? []) as $category)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                        <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $category['category'] }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $category['item_count'] }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($category['critical_count'] > 0)
                                                <span class="px-2 py-1 text-xs rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    {{ $category['critical_count'] }}
                                                </span>
                                            @else
                                                <span class="text-zinc-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">₦{{ number_format($category['total_cost'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-2">
            <x-button color="secondary" wire:click="generateReport">
                <x-icon name="document-check" class="w-4 h-4 mr-2" />
                Save Report
            </x-button>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-zinc-100">Loading Reorder Report...</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Report generates automatically on page load
            </p>
        </div>
    @endif

    {{-- Report Save Modal --}}
    @if($showReportModal && $generatedReport)
        <x-modal wire:model="showReportModal" title="Report Saved Successfully">
            <div class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Your reorder report has been saved successfully and is ready for review.
                    </p>
                </div>

                <div class="space-y-2 text-sm">
                    <p><strong>Report ID:</strong> #{{ $generatedReport->id }}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $generatedReport->status)) }}</span></p>
                    <p><strong>Generated:</strong> {{ $generatedReport->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button color="secondary" wire:click="$set('showReportModal', false)">
                        Close
                    </x-button>
                    <x-button color="primary" wire:click="submitForReview({{ $generatedReport->id }})">
                        Submit for Review
                    </x-button>
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
