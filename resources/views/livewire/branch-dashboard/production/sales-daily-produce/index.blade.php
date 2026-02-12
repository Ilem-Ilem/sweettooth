<div class="p-3 space-y-3">
    <x-breadcrumb
        title="Sales Requests → Production"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Production'],
            ['label' => 'Sales Requests']
        ]"
        :compact="false"
        :with-icons="true"/>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4 flex flex-wrap items-center gap-3">
        <div>
            <div class="text-sm text-zinc-500 dark:text-zinc-400">Department</div>
            <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $dept_slug }}</div>
        </div>
        @if($shift_id)
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                Active Shift: #{{ $shift_id }}
            </div>
        @else
            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                No active shift (Super Admin or unassigned)
            </div>
        @endif
        <div class="flex-1"></div>
        <div class="flex items-center gap-2">
            <select wire:model.live="status" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="rejected">Rejected</option>
                <option value="accepted">Accepted</option>
            </select>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search product or ID..."
                   class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-700 text-sm w-56">
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Sales Dept</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Shift</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Requested</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Planned</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Dispatched</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Remaining</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">ETA</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Progress</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($rows as $req)
                    @php
                        $latest = $req->progressFeedback->first();
                        $progress = $latest?->progress_percentage ?? 0;
                        $milestone = $latest?->milestone_label ?? 'Not started';
                        $yieldPerBatch = (float)($req->recipe?->yield_quantity ?? 0);
                        $planned = (float)($req->planned_production_quantity ?? 0);
                        $batchCount = $yieldPerBatch > 0 ? (int)ceil($planned / $yieldPerBatch) : 0;
                        $start = $req->started_at;
                        if ($start) {
                            $etaMinutes = $req->eta_override_minutes
                                ?: (($req->recipe?->preparation_time ?? 0) * max($batchCount, 1));
                            $etaAt = $etaMinutes ? \Carbon\Carbon::parse($start)->addMinutes($etaMinutes) : null;
                        } else {
                            $etaAt = null;
                        }
                        $etaLabel = $etaAt ? $etaAt->format('M d, H:i') : 'Not started';
                        $timeLeft = $etaAt ? ($etaAt->isPast() ? 'Ready/Overdue' : $etaAt->diffForHumans(now(), ['parts'=>2,'short'=>true])) : 'N/A';
                    @endphp
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                        <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">PR-{{ $req->id }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $req->recipe?->product_name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                Created {{ $req->created_at?->format('M d, H:i') }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $req->salesDepartment?->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                            @if($req->shift)
                                {{ $req->shift->shift_date?->format('M d') }} · {{ ucfirst($req->shift->shift_type ?? '') }}
                            @else
                                Unassigned
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                            {{ number_format($req->requested_units ?? $req->planned_production_quantity ?? 0, 2) }}
                        </td>
                        @php
                            $dispatchedQty = $req->dispatches->sum('quantity');
                            $plannedQty = (float)($req->planned_production_quantity ?? 0);
                            $remainingQty = max(0, $plannedQty - $dispatchedQty);
                        @endphp
                        <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                            {{ number_format($plannedQty, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                            {{ number_format($dispatchedQty, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                            {{ number_format($remainingQty, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-zinc-700 dark:text-zinc-300">
                            <div>{{ $etaLabel }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $timeLeft }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $milestone }}</div>
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $progress }}%</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                @if($req->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($req->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($req->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($req->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @else bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-300 @endif">
                                {{ ucfirst(str_replace('_',' ', $req->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center space-y-1">
                            <a href="{{ branch_route('branch-dashboard.production.request.progress', ['deptSlug' => $dept_slug, 'requestId' => $req->id]) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-blue-600 text-white hover:bg-blue-700">
                                Dispatch / Update
                            </a>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                Dispatch to {{ $req->salesDepartment?->name ?? 'Sales' }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                            No sales production requests.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
            {{ $rows->links() }}
        </div>
    </div>
</div>
