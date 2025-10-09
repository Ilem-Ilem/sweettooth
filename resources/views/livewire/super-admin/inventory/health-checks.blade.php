<div class="p-3 space-y-3">
    <x-breadcrumb title="Health Checks" :links="[['label' => 'Dashboard', 'url' => route('super-admin.dashboard')], ['label' => 'Inventory'], ['label' => 'Health Checks']]" />

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
                    <input type="text" wire:model.live="search" placeholder="Item name or SKU..."
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Condition</label>
                    <select wire:model.live="filterCondition"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Conditions</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Action Taken</label>
                    <select wire:model.live="filterActionTaken"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Checks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-700">Health Checks History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Condition</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Affected</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observations</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action Taken</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Checked By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($healthChecks as $check)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $check->check_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-xs">
                                <div class="font-medium text-gray-900">{{ $check->stock->item->name }}</div>
                                <div class="text-gray-500">{{ $check->stock->item->sku }}</div>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $check->stock->branch->name }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $check->condition === 'good' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $check->condition === 'fair' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $check->condition === 'poor' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $check->condition === 'damaged' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $check->condition === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($check->condition) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                @if ($check->quantity_affected)
                                    <span class="font-medium {{ $check->requiresAction() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ number_format($check->quantity_affected, 2) }}
                                    </span>
                                    <span class="text-gray-500">{{ strtoupper($check->stock->item->uom) }}</span>
                                @else
                                    <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $check->observations ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                @if ($check->hasActionTaken())
                                    <span class="text-gray-900">{{ $check->action_taken }}</span>
                                @else
                                    @if ($check->requiresAction())
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Pending
                                        </span>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $check->checker->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-sm text-gray-500">
                                No health checks found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $healthChecks->links() }}
        </div>
    </div>
</div>
