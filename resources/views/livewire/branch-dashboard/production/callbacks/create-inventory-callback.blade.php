<div class="p-3 space-y-3">
    <x-breadcrumb title="Create Inventory Callback" :items="[
        ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
        ['label' => 'Production'],
        ['label' => 'Create Inventory Callback'],
    ]" :compact="false" :with-icons="true" />

    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-4 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Create Inventory Callback</h2>
                <p class="text-sm opacity-90 mt-1">
                    Report raw materials or finished products to be returned to inventory
                </p>
            </div>
        </div>
    </div>

    <!-- Alert if no shift -->
    @if (!$currentShift)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Active Production Shift</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Please start a production shift to create callbacks.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Current Shift Info -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Current Shift</h3>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ $currentShift->shift_date }} - {{ $currentShift->department->name ?? 'N/A' }}
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Active
                </span>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 dark:border-zinc-700">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button wire:click="$set('callbackType', 'raw_material')"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors
                            {{ $callbackType === 'raw_material'
                                ? 'border-purple-600 text-purple-600 dark:text-purple-400'
                                : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Raw Materials
                    </button>
                    <button wire:click="$set('callbackType', 'finished_product')"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors
                            {{ $callbackType === 'finished_product'
                                ? 'border-purple-600 text-purple-600 dark:text-purple-400'
                                : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        Finished Products
                    </button>
                </nav>
            </div>

            <!-- Filters -->
            <div class="p-3">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search {{ $callbackType === 'raw_material' ? 'raw materials' : 'finished products' }}..."
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-purple-500">
            </div>
        </div>

        <!-- Raw Materials Table -->
        @if ($callbackType === 'raw_material')
            <x-table :headers="$rawMaterialHeaders" :rows="$rawMaterials" striped paginate persist
                :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[10, 20, 50, 100]">

                @interact('column_item', $row)
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->item->name ?? 'N/A' }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row->item->description ?? '' }}</div>
                    </div>
                @endinteract

                @interact('column_sku', $row)
                    <div class="text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                            {{ $row->item->sku ?? 'N/A' }}
                        </span>
                    </div>
                @endinteract

                @interact('column_dispatched_qty', $row)
                    <div class="text-center">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($row->quantity, 2) }}
                        </span>
                    </div>
                @endinteract

                @interact('column_uom', $row)
                    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $row->uom ?? $row->item->unit ?? 'N/A' }}
                    </div>
                @endinteract

                @interact('column_dispatch_time', $row)
                    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $row->dispatch_time ? $row->dispatch_time->format('M d, H:i') : 'N/A' }}
                    </div>
                @endinteract

                @interact('column_action', $row)
                    <div class="flex justify-center">
                        <button wire:click="openRawMaterialCallbackModal({{ $row->id }})"
                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-medium transition-colors">
                            Return
                        </button>
                    </div>
                @endinteract

            </x-table>
        @endif

        <!-- Finished Products Table -->
        @if ($callbackType === 'finished_product')
            <x-table :headers="$finishedProductHeaders" :rows="$finishedProducts" striped paginate persist
                :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[10, 20, 50, 100]">

                @interact('column_product', $row)
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->recipe->product->name ?? 'N/A' }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Recipe: {{ $row->recipe->name ?? 'N/A' }}</div>
                    </div>
                @endinteract

                @interact('column_sku', $row)
                    <div class="text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                            {{ $row->recipe->product->sku ?? 'N/A' }}
                        </span>
                    </div>
                @endinteract

                @interact('column_produced_qty', $row)
                    <div class="text-center">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format($row->produced_quantity, 2) }}
                        </span>
                    </div>
                @endinteract

                @interact('column_uom', $row)
                    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $row->recipe->product->unit ?? 'N/A' }}
                    </div>
                @endinteract

                @interact('column_action', $row)
                    <div class="flex justify-center">
                        <button wire:click="openFinishedProductCallbackModal({{ $row->id }})"
                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-medium transition-colors">
                            Reject
                        </button>
                    </div>
                @endinteract

            </x-table>
        @endif

        <!-- Help Panel -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <h4 class="font-semibold mb-1">Inventory Callback Instructions:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Raw Materials:</strong> Return damaged or unusable raw materials to inventory</li>
                        <li><strong>Finished Products:</strong> Reject quality-issue finished products</li>
                        <li><strong>Inventory Approval:</strong> All callbacks must be approved by inventory department</li>
                        <li><strong>Stock Adjustments:</strong> Approved callbacks automatically update stock levels</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Callback Modal -->
    @if($showCallbackModal)
        <div x-data="{ open: @entangle('showCallbackModal') }" x-show="open" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="$wire.closeCallbackModal()">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 dark:bg-black/70" @click="$wire.closeCallbackModal()"></div>

                <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full p-6">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $callbackType === 'raw_material' ? 'Return Raw Material' : 'Reject Finished Product' }}
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                Create inventory callback
                            </p>
                        </div>
                        <button @click="$wire.closeCallbackModal()"
                            class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit.prevent="submitCallback" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Quantity *
                            </label>
                            <input type="number" step="0.01" min="0" wire:model="callbackQuantity"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-purple-500"
                                required>
                            @error('callbackQuantity')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Unit of Measure
                            </label>
                            <input type="text" wire:model="callbackUom" disabled
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Reason *
                            </label>
                            <select wire:model="callbackReason"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-purple-500"
                                required>
                                <option value="">Select reason</option>
                                @foreach(($callbackType === 'raw_material' ? $rawMaterialReasonOptions : $finishedProductReasonOptions) as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('callbackReason')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea wire:model="callbackNotes" rows="3"
                                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-purple-500"
                                placeholder="Additional details..."></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="$wire.closeCallbackModal()"
                                class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                                Submit Callback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
