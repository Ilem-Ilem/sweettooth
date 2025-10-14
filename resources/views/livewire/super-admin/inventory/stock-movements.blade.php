<div class="p-3 space-y-3">
    <x-breadcrumb title="Stock Movements" :links="[['label' => 'Dashboard', 'url' => route('super-admin.dashboard')], ['label' => 'Inventory'], ['label' => 'Stock Movements']]" />

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Filters</h3>
            <button wire:click="resetFilters" class="text-xs text-blue-600 hover:text-blue-800">
                Reset Filters
            </button>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Movement Type</label>
                    <select wire:model.live="filterType"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="in">In</option>
                        <option value="out">Out</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="transfer">Transfer</option>
                        <option value="return">Return</option>
                        <option value="damaged">Damaged</option>
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
            </div>
        </div>
    </div>

    <!-- Stock Movements Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-700">Stock Movements History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($movements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-900">
                                {{ $movement->movement_date->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <div class="font-medium text-gray-900">{{ $movement->item->name }}</div>
                                <div class="text-gray-500">{{ $movement->item->sku }}</div>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $movement->branch->name }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $movement->movement_type === 'in' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $movement->movement_type === 'out' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $movement->movement_type === 'adjustment' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $movement->movement_type === 'transfer' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $movement->movement_type === 'return' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $movement->movement_type === 'damaged' ? 'bg-orange-100 text-orange-800' : '' }}">
                                    {{ ucfirst($movement->movement_type) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="font-medium {{ $movement->isInbound() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->isInbound() ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                                </span>
                                <span class="text-gray-500">{{ strtoupper($movement->item->uom) }}</span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $movement->reference_type ? class_basename($movement->reference_type) : 'N/A' }}
                                {{ $movement->reference_id ? '#' . $movement->reference_id : '' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $movement->recorder ? $movement->recorder->name : 'System' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $movement->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-sm text-gray-500">
                                No stock movements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $movements->links() }}
        </div>
    </div>
</div>
