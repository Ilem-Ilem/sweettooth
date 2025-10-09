<div class="p-3 space-y-3">
    <x-breadcrumb title="Stock Levels" :links="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Inventory'], ['label' => 'Stocks']]" />

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Filters</h3>
            <button wire:click="resetFilters" class="text-xs text-blue-600 hover:text-blue-800">
                Reset Filters
            </button>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model.live="filterCategory"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <option value="raw_material">Raw Material</option>
                        <option value="packaging">Packaging</option>
                        <option value="consumable">Consumable</option>
                        <option value="equipment">Equipment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="filterStatus"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="overstock">Overstock</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stocks Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-700">Stock Levels</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Available</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reserved</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reorder Level</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avg Cost</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($stocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $stock->item->name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $stock->item->sku }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $stock->branch->name }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $stock->item->category === 'raw_material' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $stock->item->category === 'packaging' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $stock->item->category === 'consumable' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $stock->item->category === 'equipment' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $stock->item->category)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="font-medium {{ $stock->isBelowReorderLevel() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($stock->available_quantity, 2) }}
                                </span>
                                <span class="text-gray-500">{{ strtoupper($stock->item->uom) }}</span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ number_format($stock->reserved_quantity, 2) }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="font-medium {{ $stock->exceedsMaxLevel() ? 'text-orange-600' : 'text-gray-900' }}">
                                    {{ number_format($stock->total_quantity, 2) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $stock->item->reorder_level ? number_format($stock->item->reorder_level, 2) : 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                GHS {{ number_format($stock->average_cost, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-sm text-gray-500">
                                No stock records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $stocks->links() }}
        </div>
    </div>
</div>
