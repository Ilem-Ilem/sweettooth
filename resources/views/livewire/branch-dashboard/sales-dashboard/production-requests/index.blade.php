<div class="p-3 space-y-3" wire:poll.60s>
    <x-breadcrumb
        title="Production Requests"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Sales Dashboard'],
            ['label' => 'Production Requests']
        ]"
        :compact="false"
        :with-icons="true"/>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Your Production Requests</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Track progress and ETA for requests sent from {{ $departmentName }}
                </p>
            </div>
            <div>
                <select wire:model.live="statusFilter"
                        class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                    <option value="accepted">Accepted</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Production Dept</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Requested</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Planned</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Progress</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">ETA</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($requests as $req)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $req['recipe_name'] }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Request #{{ $req['id'] }} • {{ $req['created_at'] }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $req['production_department'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                                {{ number_format($req['requested_units'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                                {{ number_format($req['planned_units'], 2) }}
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $req['batch_count'] }} batch{{ $req['batch_count'] === 1 ? '' : 'es' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $req['milestone'] }}</div>
                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $req['progress_percent'] }}%</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $req['eta_label'] }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $req['time_left'] }}
                                    @if($req['eta_source'] === 'manual')
                                        • Manual
                                    @elseif($req['eta_source'] === 'auto')
                                        • Auto
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                    @if($req['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @elseif($req['status'] === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($req['status'] === 'quality_check') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                    @elseif($req['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($req['status'] === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-300 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $req['status'])) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                No production requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
