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
            @if(count($availableDepartments ?? []) > 0)
                <div class="flex-1 min-w-[200px]">
                    <x-select.native label="Department" wire:model.live="selectedDepartmentId">
                        <option value="">Select Department</option>
                        @foreach($availableDepartments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </x-select.native>
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
        {{-- Narrative Insights --}}
        @if(!empty($narrative))
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Insights</h3>
                @if(!empty($narrative['overview']))
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $narrative['overview'] }}</p>
                @endif
                @if(!empty($narrative['highlights']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Highlights</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['highlights'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($narrative['concerns']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Concerns</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['concerns'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($narrative['recommendations']))
                    <div class="mt-3">
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Recommendations</p>
                        <ul class="list-disc pl-5 text-sm text-zinc-700 dark:text-zinc-300">
                            @foreach($narrative['recommendations'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        {{-- Data Tables --}}
        @if(!empty($tablesData))
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Data Tables</h3>
                </div>
                <div class="p-4 space-y-6">
                    @foreach($tablesData as $tableKey => $table)
                        <div>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                                {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $tableKey)) }}
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                    <thead>
                                        <tr>
                                            @foreach(($table['headers'] ?? []) as $header)
                                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                                    {{ $header }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @foreach(($table['rows'] ?? []) as $row)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                                @foreach($row as $cell)
                                                    <td class="px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300">
                                                        {{ is_numeric($cell) ? number_format($cell, 2) : $cell }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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

    @include('livewire.partials.department-select-modal')

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
