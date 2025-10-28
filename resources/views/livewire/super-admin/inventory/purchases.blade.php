<div class="p-3 space-y-3">

<style>
.scrollbar-thin::-webkit-scrollbar {
    width: 8px;
    }
        .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    @apply bg-zinc-300 dark:bg-zinc-700 rounded-full;
    }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        @apply bg-zinc-400 dark:bg-zinc-600;
}
[x-cloak] {
    display: none !important;
    }
    </style>

<x-breadcrumb
        title="Purchases Management"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Inventory'],
            ['label' => 'Purchases']
        ]"
        :compact="false"
        :with-icons="true"
    />

    @if (session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-100 px-3 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-100 px-3 py-2 rounded text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <button wire:click="openCreateModal"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Purchase
        </button>
    </div>

    <!-- Filters -->
    <div x-data="{ open: false }"
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 transition-all duration-300">
        <div class="flex justify-between items-center px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
        <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14.293 13H10v5l-4-4v-3.586L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            Filters
        </h2>

        <button @click="open = !open"
        class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span x-text="open ? 'Close' : 'Show Filters'"></span>
        </button>
        </div>

        <div x-show="open" x-collapse class="p-3 space-y-3">
        <!-- Advanced Search -->
        <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Advanced Search</label>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by purchase number, supplier..."
        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Branch</label>
        <x-select.styled
                wire:model.live="filterBranch"
                    :options="$branches->map(fn($branch) => ['label' => $branch->name, 'value' => $branch->id])->toArray()"
                        select="label:label|value:value"
                        placeholder="All Branches"
                        searchable
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Payment Status</label>
                    <x-select.styled
                        wire:model.live="filterPaymentStatus"
                        :options="[
                            ['label' => 'Paid', 'value' => 'paid'],
                            ['label' => 'Partial', 'value' => 'partial'],
                            ['label' => 'Pending', 'value' => 'pending']
                        ]"
                        select="label:label|value:value"
                        placeholder="All Status"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                    <x-select.styled
                        wire:model.live="filterStatus"
                        :options="[
                            ['label' => 'Completed', 'value' => 'completed'],
                            ['label' => 'Pending', 'value' => 'pending'],
                            ['label' => 'Cancelled', 'value' => 'cancelled']
                        ]"
                        select="label:label|value:value"
                        placeholder="All Status"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date Range</label>
                    <div class="flex space-x-1">
                        <input type="date" wire:model.live="dateFrom"
                            class="w-1/2 px-2 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500 text-sm">
                        <input type="date" wire:model.live="dateTo"
                            class="w-1/2 px-2 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 justify-end pt-2.5 border-t border-zinc-200 dark:border-zinc-700">
                <button wire:click="applyFilters"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                    Apply
                </button>
                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
    <div class="px-3 py-2 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
    <h3 class="text-sm font-medium text-zinc-800 dark:text-zinc-100">Purchases List</h3>
            @can('create-purchases')
                <button wire:click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium">
                    New Purchase
                </button>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Purchase #</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $purchase->purchase_number }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $purchase->branch->name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-900">{{ $purchase->supplier_name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $purchase->currency }}</td>
                            <td class="px-3 py-2 text-xs font-medium text-gray-900">
                                ₦{{ number_format($purchase->landing_cost, 2) }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $purchase->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $purchase->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $purchase->payment_status === 'pending' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $purchase->purchaseItems->count() }}</td>
                            <td class="px-3 py-2 text-xs">
                                @can('delete-purchases')
                                    <button wire:click="delete({{ $purchase->id }})"
                                        onclick="return confirm('Are you sure you want to delete this purchase?')"
                                        class="text-red-600 hover:text-red-800">
                                        Delete
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-sm text-gray-500">
                                No purchases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $purchases->links() }}
        </div>
    </div>

    <!-- Create Purchase Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-4 border w-full max-w-4xl shadow-lg rounded-lg bg-white mb-10">
                <div class="px-3 py-2 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">Create New Purchase</h3>
                </div>
                <div class="p-3 max-h-[70vh] overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Branch <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="branch_id"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Purchase Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="purchase_date"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('purchase_date')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Payment Status <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="payment_status"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial</option>
                                    <option value="pending">Pending</option>
                                </select>
                                @error('payment_status')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Supplier Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="supplier_name"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('supplier_name')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Contact</label>
                                <input type="text" wire:model="supplier_contact"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('supplier_contact')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Currency <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="currency"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="NGN">NGN (₦)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                </select>
                                @error('currency')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Exchange Rate <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" wire:model="exchange_rate"
                                    {{ $currency === 'NGN' ? 'readonly' : '' }}
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('exchange_rate')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Other Costs (₦) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" wire:model="other_costs"
                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('other_costs')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                            <textarea wire:model="notes" rows="2"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('notes')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Purchase Items -->
                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-sm font-medium text-gray-700">Purchase Items</h4>
                                <button type="button" wire:click="addPurchaseItem"
                                    class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded-md text-xs font-medium">
                                    Add Item
                                </button>
                            </div>

                            <div class="space-y-2">
                                @foreach ($purchaseItems as $index => $item)
                                    <div class="grid grid-cols-12 gap-2 items-start border p-2 rounded bg-gray-50">
                                        <div class="col-span-4">
                                            <select wire:model="purchaseItems.{{ $index }}.item_id"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                                                <option value="">Select Item</option>
                                                @foreach ($items as $itemOption)
                                                    <option value="{{ $itemOption->id }}">{{ $itemOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('purchaseItems.' . $index . '.item_id')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-span-2">
                                            <input type="number" step="0.01"
                                                wire:model="purchaseItems.{{ $index }}.quantity"
                                                placeholder="Qty"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                                            @error('purchaseItems.' . $index . '.quantity')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-span-2">
                                            <select wire:model="purchaseItems.{{ $index }}.uom"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                                                <option value="">UOM</option>
                                                <option value="grams">Grams</option>
                                                <option value="kg">Kg</option>
                                                <option value="liters">Liters</option>
                                                <option value="ml">ml</option>
                                                <option value="pcs">Pcs</option>
                                                <option value="units">Units</option>
                                                <option value="bags">Bags</option>
                                                <option value="cartons">Cartons</option>
                                            </select>
                                            @error('purchaseItems.' . $index . '.uom')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-span-3">
                                            <input type="number" step="0.01"
                                                wire:model="purchaseItems.{{ $index }}.unit_fob_fc"
                                                placeholder="Unit Price"
                                                class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                                            @error('purchaseItems.' . $index . '.unit_fob_fc')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-span-1">
                                            <button type="button" wire:click="removePurchaseItem({{ $index }})"
                                                class="text-red-600 hover:text-red-800 text-xs">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2 border-t">
                            <button type="button" wire:click="closeModal"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700">
                                Create Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
