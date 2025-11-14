<div>
    <div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <!-- Header Section -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $compiledReport->compilation_title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $compiledReport->compilation_description }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ branch_route('branch-dashboard.reporting.send-to-md') }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>

        <!-- Report Metadata Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                <span class="mt-2 inline-flex px-3 py-1 text-sm font-semibold rounded-full
                    {{ $compiledReport->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : '' }}
                    {{ $compiledReport->status === 'pending_approval' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                    {{ $compiledReport->status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                    {{ $compiledReport->status === 'sent_to_md' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                    {{ str_replace('_', ' ', ucfirst($compiledReport->status)) }}
                </span>
            </div>

            <!-- Compilation Date -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">Compilation Date</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                    {{ \Carbon\Carbon::parse($compiledReport->compilation_date)->format('M d, Y') }}
                </p>
            </div>

            <!-- Period -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">Reporting Period</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                    {{ \Carbon\Carbon::parse($compiledReport->period_from)->format('M d') }} -
                    {{ \Carbon\Carbon::parse($compiledReport->period_to)->format('M d, Y') }}
                </p>
            </div>

            <!-- Compiled By -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">Compiled By</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                    {{ $compiledReport->compiledBy?->name ?? 'N/A' }}
                </p>
            </div>
        </div>

        <!-- Executive Summary -->
        @if($compiledReport->executive_summary)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Executive Summary</h2>

                <!-- Overview -->
                @if(isset($compiledReport->executive_summary['overview']))
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Overview</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($compiledReport->executive_summary['overview'] as $key => $value)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ is_numeric($value) ? $value : (is_string($value) ? $value : json_encode($value)) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Highlights -->
                @if(isset($compiledReport->executive_summary['highlights']) && count($compiledReport->executive_summary['highlights']) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Highlights
                        </h3>
                        <div class="space-y-2">
                            @foreach($compiledReport->executive_summary['highlights'] as $highlight)
                                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <span class="text-green-600 dark:text-green-400">✓</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $highlight['message'] }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $highlight['department'] }} - {{ ucfirst($highlight['category']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Concerns -->
                @if(isset($compiledReport->executive_summary['concerns']) && count($compiledReport->executive_summary['concerns']) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Concerns
                        </h3>
                        <div class="space-y-2">
                            @foreach($compiledReport->executive_summary['concerns'] as $concern)
                                <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <span class="text-red-600 dark:text-red-400">!</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $concern['message'] }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $concern['department'] }} - {{ ucfirst($concern['category']) }} ({{ ucfirst($concern['severity']) }} priority)</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Recommendations -->
        @if($compiledReport->recommendations && count($compiledReport->recommendations) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recommendations</h2>
                <div class="space-y-4">
                    @foreach($compiledReport->recommendations as $recommendation)
                        <div class="border dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $recommendation['title'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $recommendation['description'] }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $recommendation['priority'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $recommendation['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $recommendation['priority'] === 'low' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}">
                                    {{ ucfirst($recommendation['priority']) }}
                                </span>
                            </div>
                            @if(isset($recommendation['action_items']) && count($recommendation['action_items']) > 0)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action Items:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($recommendation['action_items'] as $action)
                                            <li class="text-sm text-gray-600 dark:text-gray-400">{{ $action }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Included Department Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Included Department Reports</h2>

            <!-- Production Reports -->
            @if($productionReports->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Production Reports</h3>
                    <div class="space-y-2">
                        @foreach($productionReports as $report)
                            <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $report->report_name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $report->department?->name }} • Generated by {{ $report->generatedBy?->name }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Sales Reports -->
            @if($salesReports->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Sales Reports</h3>
                    <div class="space-y-2">
                        @foreach($salesReports as $report)
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $report->report_name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $report->department?->name }} • Generated by {{ $report->generatedBy?->name }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Inventory Reports -->
            @if($inventoryReports->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Inventory Reports</h3>
                    <div class="space-y-2">
                        @foreach($inventoryReports as $report)
                            <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $report->report_name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $report->department?->name }} • Generated by {{ $report->generatedBy?->name }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
