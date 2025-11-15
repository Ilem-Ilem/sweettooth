<div class="p-6 space-y-6">
    <x-breadcrumb title="Stock Variance Report" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Inventory'],
        ['label' => 'Reports'],
        ['label' => 'Variance'],
    ]" :compact="false" :with-icons="true" />

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-select.native label="Period Filter" wire:model.live="periodFilter">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </x-select.native>
            </div>

            @if($periodFilter === 'custom')
                <div class="flex-1 min-w-[200px]">
                    <x-input label="From Date" type="date" wire:model="customDateFrom" />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-input label="To Date" type="date" wire:model="customDateTo" />
                </div>
            @endif

            <div class="flex gap-2">
                <x-button color="primary" wire:click="generatePreview" :loading="$isLoading">
                    <x-icon name="arrow-path" class="w-4 h-4 mr-2" />
                    Generate Preview
                </x-button>

                @if($reportData)
                    <x-button color="secondary" wire:click="generateReport">
                        <x-icon name="document-check" class="w-4 h-4 mr-2" />
                        Save Report
                    </x-button>
                @endif
            </div>
        </div>
    </div>

    @if($reportData)
        {{-- Report Content --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-zinc-100">Stock Variance Report Generated</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Report covers period from {{ \Carbon\Carbon::parse($customDateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($customDateTo)->format('M d, Y') }}
            </p>
            <div class="mt-6 p-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-left max-w-2xl mx-auto">
                <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-4">Report Summary</h4>
                <div class="space-y-2 text-sm text-purple-800 dark:text-purple-200">
                    <p><strong>Total Variance Records:</strong> {{ count($reportData['all_variances'] ?? []) }}</p>
                    <p><strong>Period:</strong> {{ $reportData['period_info']['from'] ?? '' }} to {{ $reportData['period_info']['to'] ?? '' }}</p>
                    <p class="mt-4 text-xs text-purple-700 dark:text-purple-300">
                        Full variance analysis includes stock take discrepancies, accuracy rates, positive/negative variances, and root cause analysis.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-zinc-900 dark:text-zinc-100">No Report Generated</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Select a period and click "Generate Preview" to view the stock variance report.
            </p>
        </div>
    @endif

    {{-- Report Save Modal --}}
    @if($showReportModal && $generatedReport)
        <x-modal wire:model="showReportModal" title="Report Saved Successfully">
            <div class="space-y-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Your stock variance report has been saved successfully and is ready for review.
                    </p>
                </div>

                <div class="space-y-2 text-sm">
                    <p><strong>Report ID:</strong> #{{ $generatedReport->id }}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $generatedReport->status)) }}</span></p>
                    <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($generatedReport->period_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($generatedReport->period_to)->format('M d, Y') }}</p>
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
