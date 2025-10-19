<div class="p-3 space-y-3">

    <x-breadcrumb
        title="Production Requests"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Production'],
            ['label' => 'Requests']
        ]"
        :compact="false"
        :with-icons="true"/>

    <!-- Status Summary Alerts -->
    @if(isset($statusSummary) && $statusSummary->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @if($statusSummary->has('completed'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">Completed</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $statusSummary['completed'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($statusSummary->has('partially_dispatched'))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Partially Dispatched</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $statusSummary['partially_dispatched'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($statusSummary->has('partially_approved'))
        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Partially Approved</p>
                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $statusSummary['partially_approved'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($statusSummary->has('approved'))
        <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-teal-600 dark:text-teal-400 font-medium">Approved</p>
                    <p class="text-2xl font-bold text-teal-700 dark:text-teal-300">{{ $statusSummary['approved'] }}</p>
                </div>
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($statusSummary->has('cancelled'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-red-600 dark:text-red-400 font-medium">Cancelled</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $statusSummary['cancelled'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        @if($statusSummary->has('pending'))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $statusSummary['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div class="flex gap-2">
            <input type="text" wire:model.live="search" placeholder="Search requests or recipes..."
                   class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">

            <select wire:model.live="shiftFilter"
                    class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                <option value="all">All Shifts</option>
                @foreach($allShifts as $shift)
                    <option value="{{ $shift->id }}">
                        {{ ucfirst($shift->shift_type) }} - {{ $shift->shift_date->format('M d, Y') }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                    class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="partially_approved">Partially Approved</option>
                <option value="partially_dispatched">Partially Dispatched</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <a href="{{ branch_route('branch-dashboard.production.request.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Production Request
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Request #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Shift</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Recipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($requests as $request)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                        <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $request->itemRequest->request_number ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $request->shift->shift_type === 'morning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                {{ ucfirst($request->shift->shift_type ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                            {{ $request->recipe->product_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ number_format($request->planned_production_quantity, 2) }} {{ $request->recipe->uom ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $request->getStatusBadgeColor() }}">
                                {{ ucfirst(str_replace('_', ' ', $request->getComputedStatus())) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openViewModal({{ $request->id }})"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                @if($request->canBeCancelled())
                                <button wire:click="cancelRequest({{ $request->id }})"
                                        wire:confirm="Are you sure you want to cancel this request?"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        title="Cancel Request">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                            No production requests found. Create your first request!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

    <!-- View Request Modal -->
    @if($showViewModal && $viewingRequest)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeViewModal"></div>

            <!-- Spacing element for proper centering -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full z-50">
                <!-- Header -->
                <div class="bg-white dark:bg-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                Production Request Details
                            </h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                                Request #{{ $viewingRequest->itemRequest->request_number ?? 'N/A' }}
                            </p>
                        </div>
                        <button wire:click="closeViewModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white dark:bg-zinc-800 px-6 py-4 space-y-4">
                    <!-- Request Info -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Recipe</p>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">{{ $viewingRequest->recipe->product_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Planned Quantity</p>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">
                                {{ number_format($viewingRequest->planned_production_quantity, 2) }} {{ $viewingRequest->recipe->uom ?? '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Shift</p>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">
                                {{ ucfirst($viewingRequest->shift->shift_type ?? 'N/A') }} - {{ $viewingRequest->shift->shift_date->format('M d, Y') ?? '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Status</p>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $viewingRequest->getStatusBadgeColor() }} mt-1">
                                {{ ucfirst(str_replace('_', ' ', $viewingRequest->getComputedStatus())) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Department</p>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">{{ $viewingRequest->itemRequest->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Requested By</p>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">{{ $viewingRequest->itemRequest->requester->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Request Items Table -->
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Requested Items</h4>
                        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <table class="w-full">
                                <thead class="bg-zinc-50 dark:bg-zinc-900">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Item</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Requested</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Approved</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Dispatched</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($requestItems as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $item['item_name'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 text-right">
                                            {{ number_format($item['quantity_requested'], 2) }} {{ $item['uom'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 text-right">
                                            {{ number_format($item['quantity_approved'], 2) }} {{ $item['uom'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400 text-right">
                                            {{ number_format($item['quantity_dispatched'], 2) }} {{ $item['uom'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item['status']['class'] }}">
                                                {{ $item['status']['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-zinc-50 dark:bg-zinc-900 px-6 py-4 flex justify-end gap-3">
                    @if($viewingRequest->canBeCancelled())
                    <button wire:click="cancelRequest({{ $viewingRequest->id }})"
                            wire:confirm="Are you sure you want to cancel this request?"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                        Cancel Request
                    </button>
                    @endif
                    <button wire:click="closeViewModal"
                            class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
