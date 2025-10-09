<div class="p-3 space-y-3">
    <x-breadcrumb title="Purchases Management" :links="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
        ['label' => 'Inventory'],
        ['label' => 'Purchases'],
    ]" />

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm">
            {{ session('error') }}
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
                    <input type="text" wire:model.live="search" placeholder="Purchase number or supplier..."
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
                    <label class="block text-xs font-medium text-gray-700 mb-1">Payment Status</label>
                    <select wire:model.live="filterPaymentStatus"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Purchases List</h3>
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
