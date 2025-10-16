<!-- Create/Edit Product Modal (Slide-in) -->
<div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
    @keydown.escape.window="show = false">
    <!-- Backdrop -->
    <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
        @click="$wire.closeModal()">
    </div>

    <!-- Slide-in Panel -->
    <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full md:w-2/3 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                {{ $isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
            <button wire:click="closeModal"
                class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable Form Content -->
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
            <form wire:submit.prevent="save">
                <!-- Basic Information Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h3>

                    <!-- Product Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Product Name *</label>
                        <input type="text" wire:model.live="name"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter product name" required>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Product Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Product Type *</label>
                        <x-select.styled
                            wire:model.live="product_type_id"
                            :options="$productTypes"
                            select="label:name|value:id"
                            placeholder="Select Product Type"
                            required
                        />
                        @error('product_type_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">SKU *</label>
                        <input type="text" wire:model="sku"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 font-mono uppercase"
                            placeholder="Auto-generated" required>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Auto-generated based on type and name</p>
                        @error('sku')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter product description"></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Recipe Yield Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Recipe Yield Information</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                        Define how many units one recipe batch produces for accurate ingredient calculations
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Recipe Yield -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Recipe Yield (Units) *</label>
                            <input type="number" step="0.01" wire:model="recipe_yield"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., 12" required>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">How many units does one recipe batch produce?</p>
                            @error('recipe_yield')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Yield Percentage -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Yield % *</label>
                            <input type="number" step="0.01" wire:model="yield_percentage"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="100" required>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Accounts for waste/loss (100% = no waste)</p>
                            @error('yield_percentage')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Recipe Yield Weight -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Total Batch Weight/Volume (g/ml)</label>
                            <input type="number" step="0.01" wire:model="recipe_yield_weight"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., 1200">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Total weight/volume of one recipe batch</p>
                            @error('recipe_yield_weight')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Unit Weight -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Unit Weight/Volume (g/ml)</label>
                            <input type="number" step="0.01" wire:model="unit_weight"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., 100">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Weight/volume of one unit</p>
                            @error('unit_weight')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing & Details Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4 mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Pricing & Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Selling Price *</label>
                            <input type="number" step="0.01" wire:model="price"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00" required>
                            @error('price')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Cost -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Cost Price</label>
                            <input type="number" step="0.01" wire:model="cost"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="0.00">
                            @error('cost')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Shelf Life -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Shelf Life (Days) *</label>
                            <input type="number" wire:model="shelf_life_days"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                                placeholder="0" required>
                            @error('shelf_life_days')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Unit of Measure -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Unit of Measure *</label>
                            <x-select.styled
                                wire:model="uom"
                                :options="[
                                    ['label' => 'Pieces (Pcs)', 'value' => 'pcs'],
                                    ['label' => 'Units', 'value' => 'units'],
                                    ['label' => 'Grams', 'value' => 'grams'],
                                    ['label' => 'Kilograms (Kg)', 'value' => 'kg'],
                                    ['label' => 'Liters', 'value' => 'liters'],
                                    ['label' => 'Milliliters (ml)', 'value' => 'ml']
                                ]"
                                select="label:label|value:value"
                                placeholder="Select UOM"
                                required
                            />
                            @error('uom')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Status</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Is Active -->
                        <div>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="is_active"
                                    class="w-5 h-5 text-blue-600 border-zinc-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Active</span>
                            </label>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 ml-8">Product is active in the system</p>
                        </div>

                        <!-- Is Available -->
                        <div>
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="is_available"
                                    class="w-5 h-5 text-blue-600 border-zinc-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Available for Production</span>
                            </label>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 ml-8">Available for production/sale</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
            <button wire:click="closeModal"
                class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button wire:click="save"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                {{ $isEditing ? 'Update Product' : 'Create Product' }}
            </button>
        </div>
    </div>
</div>
