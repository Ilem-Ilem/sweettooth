<div class="w-full">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">View MD Report</h2>
            <a href="{{ route('branch-dashboard.md-reports.dashboard') }}" wire:navigate class="text-blue-600 hover:text-blue-800">
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if($report)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6">
                <!-- Report Header -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Branch</p>
                        <p class="text-lg font-semibold">{{ $report->branch?->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Compiled By</p>
                        <p class="text-lg font-semibold">{{ $report->compiledBy?->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Date</p>
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

                <!-- Report Items -->
                @if($report->items && count($report->items) > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Report Items</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold">Item</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach($report->items as $item)
                                        <tr>
                                            <td class="px-4 py-2">{{ $item->name ?? 'N/A' }}</td>
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
            <div class="text-center text-gray-500">
                <p>Report not found</p>
            </div>
        @endif
    </div>
</div>
