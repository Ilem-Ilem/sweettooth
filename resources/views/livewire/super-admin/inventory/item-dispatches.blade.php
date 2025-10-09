<div class="p-3 space-y-3">
    <x-breadcrumb title="Item Dispatches" :links="[['label' => 'Dashboard', 'url' => route('super-admin.dashboard')], ['label' => 'Inventory'], ['label' => 'Item Dispatches']]" />

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Filters</h3>
            <button wire:click="resetFilters" class="text-xs text-blue-600 hover:text-blue-800">
                Reset Filters
            </button>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.live="search" placeholder="Request # or Item..."
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                    <select wire:model.live="filterBranch"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Shift</label>
                    <select wire:model.live="filterShift"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Shifts</option>
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                        <option value="night">Night</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" wire:model.live="filterDateTo"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Received</label>
                    <select wire:model.live="filterReceived"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1">Received</option>
                        <option value="0">Not Received</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispatches Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-700">Item Dispatches List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dispatch Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Request #</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dispatched By</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dispatches as $dispatch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-900">
                                {{ $dispatch->dispatch_time->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-3 py-2 text-xs font-medium text-blue-600">
                                {{ $dispatch->itemRequest->request_number }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $dispatch->itemRequest->branch->name }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $dispatch->itemRequest->department->name }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <div class="font-medium text-gray-900">{{ $dispatch->item->name }}</div>
                                <div class="text-gray-500">{{ $dispatch->item->sku }}</div>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-900">
                                {{ number_format($dispatch->quantity, 2) }}
                                <span class="text-gray-500">{{ strtoupper($dispatch->uom) }}</span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($dispatch->shift) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $dispatch->dispatcher->name }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                @if ($dispatch->isReceived())
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                    <div class="text-gray-500 mt-0.5">
                                        {{ $dispatch->received_time->format('Y-m-d H:i') }}
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-sm text-gray-500">
                                No item dispatches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $dispatches->links() }}
        </div>
    </div>
</div>
