<div class="w-full">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">View Managing Director Report</h2>
            <a href="{{ route('branch-dashboard.md-reports.dashboard') }}" wire:navigate class="text-blue-600 hover:text-blue-800">
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if($report)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6">
                <!-- Report Header -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Branch</p>
                        <p class="text-lg font-semibold">{{ $report->branch?->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Compiled By</p>
                        <p class="text-lg font-semibold">{{ $report->compiledBy?->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Compilation Date</p>
                        <p class="text-lg font-semibold">{{ $report->compilation_date?->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <p class="text-lg font-semibold">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $report->status === 'reviewed_by_md' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Report Summary -->
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Report Summary</h3>
                    <div class="prose dark:prose-invert max-w-none">
                        @if($report->executive_summary)
                            <p><strong>Executive Summary:</strong> {{ $report->executive_summary }}</p>
                        @endif

                        @if($report->recommendations)
                            <p><strong>Recommendations:</strong> {{ $report->recommendations }}</p>
                        @endif

                        @if($report->key_metrics)
                            <p><strong>Key Metrics:</strong> {{ $report->key_metrics }}</p>
                        @endif
                    </div>
                </div>

                <!-- Report Items -->
                @if($report->items && count($report->items) > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Detailed Report Items</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold">Item Name</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($report->items as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <td class="px-4 py-2 font-medium">{{ $item->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $item->value ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                <p class="text-lg">Report not found</p>
                <p class="mt-2">The requested Managing Director report could not be located.</p>
                <a href="{{ route('branch-dashboard.md-reports.dashboard') }}" wire:navigate class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Return to Reports Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
