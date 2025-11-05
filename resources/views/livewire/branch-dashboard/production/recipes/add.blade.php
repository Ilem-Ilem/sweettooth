<div class="p-3 space-y-3">

    <x-breadcrumb
        title="Add Recipe"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Production'],
            ['label' => 'Recipes', 'url' => branch_route('branch-dashboard.production.recipes.index', ['deptSlug'=>$dept_slug])],
            ['label' => 'Add']
        ]"
        :compact="false"
        :with-icons="true"/>

    <!-- Form Container -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <form wire:submit.prevent="save" class="space-y-6">

            <!-- Basic Recipe Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Product Name *</label>
                        <select wire:model.live="productName"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->name }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('productName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">SKU *</label>
                        <input type="text" wire:model="sku" readonly
                               class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200">
                        @error('sku') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Department</label>
                        <input type="text" value="{{ $department->name }}" readonly
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Product Type *</label>
                        <select wire:model="product_type"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Product Type</option>
                            <option value="gelato_base">Gelato Base</option>
                            <option value="gelato_flavor">Gelato Flavor</option>
                            <option value="pastry">Pastry</option>
                            <option value="hot_kitchen">Hot Kitchen</option>
                            <option value="beverage">Beverage</option>
                        </select>
                        @error('product_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Status *</label>
                        <select wire:model="status"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="testing">Testing</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Unit of Measure *</label>
                        <select wire:model="uom"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <option value="grams">Grams</option>
                            <option value="kg">Kilograms</option>
                            <option value="liters">Liters</option>
                            <option value="ml">Milliliters</option>
                            <option value="pcs">Pieces</option>
                            <option value="units">Units</option>
                        </select>
                        @error('uom') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Yield Quantity *</label>
                        <input type="number" step="0.01" wire:model="yield_quantity"
                               class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        @error('yield_quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Prep Time (minutes)</label>
                        <input type="number" wire:model="preparation_time"
                               class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        @error('preparation_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Ingredients *</h3>
                    <button type="button" wire:click="addIngredient"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Ingredient
                    </button>
                </div>

                @if(count($ingredients) > 0)
                <div class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg">
                    @foreach($ingredients as $index => $ingredient)
                    <div class="bg-white dark:bg-zinc-800 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">Ingredient #{{ $index + 1 }}</span>
                            <button type="button" wire:click="removeIngredient({{ $index }})"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Item *</label>
                                <select wire:model="ingredients.{{ $index }}.item_id"
                                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error("ingredients.{$index}.item_id") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Quantity *</label>
                                <input type="number" step="0.01" wire:model="ingredients.{{ $index }}.quantity"
                                       class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                                @error("ingredients.{$index}.quantity") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">UOM *</label>
                                <select wire:model="ingredients.{{ $index }}.uom"
                                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                                    <option value="grams">Grams</option>
                                    <option value="kg">Kilograms</option>
                                    <option value="liters">Liters</option>
                                    <option value="ml">Milliliters</option>
                                    <option value="pcs">Pieces</option>
                                    <option value="units">Units</option>
                                </select>
                                @error("ingredients.{$index}.uom") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Cost per Unit *</label>
                                <input type="number" step="0.01" wire:model="ingredients.{{ $index }}.cost_per_unit"
                                       class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                                @error("ingredients.{$index}.cost_per_unit") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Waste % (optional)</label>
                                <input type="number" step="0.01" wire:model="ingredients.{{ $index }}.waste_percentage"
                                       placeholder="e.g., 5 for 5%"
                                       class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                                @error("ingredients.{$index}.waste_percentage") <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Preparation Notes</label>
                                <input type="text" wire:model="ingredients.{{ $index }}.preparation_notes"
                                       placeholder="e.g., Dice into 1cm cubes"
                                       class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">General Notes</label>
                                <input type="text" wire:model="ingredients.{{ $index }}.notes"
                                       placeholder="Any additional notes"
                                       class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                    <p class="text-zinc-500 dark:text-zinc-400">No ingredients added yet. Click "Add Ingredient" to get started.</p>
                </div>
                @endif
                @error('ingredients') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Instructions Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Preparation Instructions</h3>
                    <button type="button" wire:click="addInstruction"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Step
                    </button>
                </div>

                @if(count($instructions) > 0)
                <div class="space-y-3 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg">
                    @foreach($instructions as $index => $instruction)
                    <div class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <input type="text" wire:model="instructions.{{ $index }}"
                               placeholder="Describe this step..."
                               class="flex-1 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-purple-500">
                        <button type="button" wire:click="removeInstruction({{ $index }})"
                                class="flex-shrink-0 p-2 text-red-600 hover:text-red-800 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <a href="{{ branch_route('branch-dashboard.production.recipes.index', ['deptSlug'=>$dept_slug]) }}"
                   class="px-6 py-2 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Create Recipe
                </button>
            </div>
        </form>
    </div>

</div>
