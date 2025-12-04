<!-- Stock Management Modal (Slide-in) -->
<div x-data="{ 
    show: @entangle('showStockModal').live,
    total: 0,
    available: 0,
    reserved: 0,
    damaged: 0,
    updateTotal() { 
        this.available = parseFloat($wire.stockQuantity || 0);
        this.reserved = parseFloat($wire.stockReserved || 0);
        this.damaged = parseFloat($wire.stockDamaged || 0);
        this.total = (this.available + this.reserved + this.damaged).toFixed(2);
    },
    init() {
        $watch('$wire.stockQuantity', () => this.updateTotal());
        $watch('$wire.stockReserved', () => this.updateTotal());
        $watch('$wire.stockDamaged', () => this.updateTotal());
        this.updateTotal();
    }
}" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
    @keydown.escape.window="show = false; $wire.closeStockModal()">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-150"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
        @click="show = false; $wire.closeStockModal()">
    </div>

    <!-- Slide-in Panel -->
    <div x-show="show" x-transition:enter="transform transition ease-in-out duration-200"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full md:w-1/2 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Manage Stock
            </h2>
            <button @click="show = false; $wire.closeStockModal()"
                class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div
            class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-700 scrollbar-track-transparent">

            @if($stockItemId)
                @php
                    // Use the branch ID from the validated request (passed via b_id query parameter)
                    $branchId = request()->query('b_id');
                    $item = \App\Models\Item::with(['stocks', 'branch'])->find($stockItemId);
                    $stock = $item ? $item->stocks()->where('branch_id', $branchId)->first() : null;
                @endphp

                @if($item)
                    <!-- Item Details -->
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Item Information</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Name:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">SKU:</span>
                                <span class="font-mono text-zinc-900 dark:text-zinc-100">{{ $item->sku }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Category:</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">UOM:</span>
                                <span class="text-zinc-900 dark:text-zinc-100">{{ strtoupper($item->uom) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Stock Overview (DYNAMIC - Updates as you type) -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                            <div class="text-xs text-green-600 dark:text-green-400 mb-1">Available</div>
                            <div class="text-xl font-bold text-green-700 dark:text-green-300" x-text="available.toFixed(2)">
                                {{ number_format($stock->quantity_available ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                            <div class="text-xs text-yellow-600 dark:text-yellow-400 mb-1">Reserved</div>
                            <div class="text-xl font-bold text-yellow-700 dark:text-yellow-300" x-text="reserved.toFixed(2)">
                                {{ number_format($stock->quantity_reserved ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                            <div class="text-xs text-red-600 dark:text-red-400 mb-1">Damaged</div>
                            <div class="text-xl font-bold text-red-700 dark:text-red-300" x-text="damaged.toFixed(2)">
                                {{ number_format($stock->quantity_damaged ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Stock Edit Form -->
                    <form wire:submit.prevent="saveStock" class="space-y-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Update Stock Quantities</h3>

                        <!-- Available Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Available Quantity *
                            </label>
                            <input type="number" step="0.01" wire:model.live="stockQuantity"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter available quantity" required>
                            @error('stockQuantity')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Reserved Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Reserved Quantity *
                            </label>
                            <input type="number" step="0.01" wire:model.live="stockReserved"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter reserved quantity" required>
                            @error('stockReserved')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Damaged Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Damaged Quantity *
                            </label>
                            <input type="number" step="0.01" wire:model.live="stockDamaged"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter damaged quantity" required>
                            @error('stockDamaged')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Notes
                            </label>
                            <textarea wire:model="stockNotes" rows="3"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="Add notes about this stock adjustment (optional)"></textarea>
                            @error('stockNotes')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Total Calculation -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 transition-all duration-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Total Stock:</span>
                                <span class="text-xl font-bold text-blue-900 dark:text-blue-100" x-text="total"></span>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div
            class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
            <button @click="show = false; $wire.closeStockModal()"
                class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors duration-150">
                Cancel
            </button>
            <button x-data="{ saving: false }"
                @click="saving = true; $wire.saveStock().finally(() => { saving = false })"
                :disabled="saving"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                <span x-show="!saving">Update Stock</span>
                <span x-show="saving" class="flex items-center space-x-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    <span>Saving...</span>
                </span>
            </button>
        </div>
    </div>
</div>
