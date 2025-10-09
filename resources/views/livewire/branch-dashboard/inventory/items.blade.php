<div class="p-3 space-y-3">
    <x-breadcrumb title="Items Management" :links="[['label' => 'Dashboard', 'url' => route('branch.dashboard')], ['label' => 'Inventory'], ['label' => 'Items']]" />

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Low Stock Alert -->
    @if ($lowStockCount > 0)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-3 py-2 rounded text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ $lowStockCount }} {{ $lowStockCount === 1 ? 'item is' : 'items are' }} below reorder level</span>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.live="search" placeholder="Name or SKU..."
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
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
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Items List</h3>
            @can('create-items')
                <button wire:click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium">
                    Add Item
                </button>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">UOM</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reorder Level</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $item->sku }}</td>
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $item->name }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $item->category === 'raw_material' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $item->category === 'packaging' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $item->category === 'consumable' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $item->category === 'equipment' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ strtoupper($item->uom) }}</td>
                            <td class="px-3 py-2 text-xs">
                                @php
                                    $currentStock = $item->getCurrentStock();
                                    $isBelowReorder = $item->isBelowReorderLevel();
                                @endphp
                                <span class="font-medium {{ $isBelowReorder ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($currentStock, 2) }}
                                </span>
                                @if ($isBelowReorder)
                                    <span class="ml-1 text-red-600">⚠</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $item->reorder_level ? number_format($item->reorder_level, 2) : 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <div class="flex space-x-2">
                                    @can('edit-items')
                                        <button wire:click="openEditModal({{ $item->id }})"
                                            class="text-blue-600 hover:text-blue-800">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('delete-items')
                                        <button wire:click="delete({{ $item->id }})"
                                            onclick="return confirm('Are you sure you want to delete this item?')"
                                            class="text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-sm text-gray-500">
                                No items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-4 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <div class="px-3 py-2 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">
                        {{ $isEditing ? 'Edit Item' : 'Create New Item' }}
                    </h3>
                </div>
                <div class="p-3">
                    <form wire:submit.prevent="save" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    SKU <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="sku"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('sku')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="category"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="raw_material">Raw Material</option>
                                    <option value="packaging">Packaging</option>
                                    <option value="consumable">Consumable</option>
                                    <option value="equipment">Equipment</option>
                                </select>
                                @error('category')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Item Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="name"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Unit of Measure <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="uom"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select UOM</option>
                                    <option value="grams">Grams</option>
                                    <option value="kg">Kilograms (Kg)</option>
                                    <option value="liters">Liters</option>
                                    <option value="ml">Milliliters (ml)</option>
                                    <option value="pcs">Pieces (Pcs)</option>
                                    <option value="units">Units</option>
                                    <option value="bags">Bags</option>
                                    <option value="cartons">Cartons</option>
                                </select>
                                @error('uom')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="status"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level</label>
                                <input type="number" step="0.01" wire:model="reorder_level"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('reorder_level')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Max Stock Level</label>
                                <input type="number" step="0.01" wire:model="max_stock_level"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('max_stock_level')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" wire:click="closeModal"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700">
                                {{ $isEditing ? 'Update Item' : 'Create Item' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
