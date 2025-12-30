<div>
    <!-- Kitchen Request Modal -->
    <div x-show="$wire.showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div class="absolute inset-0 bg-black/40" wire:click="closeModal"></div>
        <div class="relative w-full max-w-sm sm:max-w-2xl lg:max-w-4xl mx-auto rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg max-h-[95vh] flex flex-col overflow-hidden">

            <!-- Header -->
            <div class="flex-shrink-0 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Request Production</h2>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="px-6 py-4 space-y-4">

                    <!-- Department and Priority Selection -->
                    <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Department Selection -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Production Department *
                                </label>
                                <select wire:model.live="selectedDepartment" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 px-3 py-2">
                                    <option value="">Select Department</option>
                                    @foreach($this->availableDepartments as $dept)
                                        <option value="{{ $dept->id }}">
                                            {{ $dept->name }}
                                            @if($dept->staff_count)
                                                ({{ $dept->staff_count }} staff)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedDepartment')
                                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Priority Selection -->
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                    Priority
                                </label>
                                <select wire:model="priority" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 px-3 py-2">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Notes
                            </label>
                            <textarea wire:model="notes" rows="2" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 px-3 py-2" placeholder="Additional instructions for production..."></textarea>
                        </div>
                    </div>

                    <!-- Selected Products Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 rounded-lg border border-blue-200 dark:border-blue-800 transition-all duration-200">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        Selected Products
                                        @if(!empty($requestedItems))
                                            <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full ml-1">{{ count($requestedItems) }}</span>
                                        @endif
                                    </span>
                                </div>
                                @if(empty($requestedItems))
                                    <span class="text-xs text-zinc-500 italic">No products selected yet</span>
                                @elseif(count($requestedItems) > 4)
                                    <span class="text-xs text-zinc-500">Scroll to see all</span>
                                @endif
                            </div>

                            @if(!empty($requestedItems))
                                <div class="max-h-48 overflow-y-auto space-y-2 scrollbar-thin scrollbar-thumb-blue-300 dark:scrollbar-thumb-blue-600 scrollbar-track-transparent">
                                    @foreach($requestedItems as $productId => $qty)
                                        @php $product = \App\Models\Product::find($productId); @endphp
                                        @if($product)
                                        <div class="flex items-center gap-3 p-2 bg-white dark:bg-zinc-800/50 rounded-md border border-blue-100 dark:border-blue-700">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $product->name }}</div>
                                                <div class="text-xs text-zinc-500">{{ $this->currencySymbol }}{{ number_format($product->price ?? 0, 2) }}</div>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <label class="text-xs text-zinc-600 dark:text-zinc-400">Qty:</label>
                                                <input type="number" wire:model.live.debounce.300ms="requestedItems.{{ $productId }}" wire:change="updateQuantity('{{ $productId }}', $event.target.value)" min="1" value="{{ $qty }}" class="w-16 px-2 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                                            </div>
                                            <button type="button" wire:click="removeFromRequestedItems('{{ $productId }}')" class="p-1 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0" title="Remove {{ $product->name }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-zinc-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-sm">Select products from below to get started</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Available Products Section -->
                    <div class="flex-1 min-h-0">
                        @if($selectedDepartment)
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    Available Products from {{ $this->availableDepartments->where('id', $selectedDepartment)->first()->name ?? 'Selected Department' }}
                                </span>
                            </div>
                            <div class="h-full overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-transparent pr-2">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 pb-4">
                                    @foreach ($this->products as $product)
                                        @php $stock = \App\Models\ProductStock::query()->whereDate('stock_date', \Carbon\Carbon::today())->where('product_id', $product->id)->first(); $available = $stock ? max(0, (float) $stock->closing_quantity) : 0; @endphp
                                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 hover:border-zinc-300 dark:hover:border-zinc-600 hover:shadow-sm transition-all duration-200 @if(isset($requestedItems[$product->id])) ring-2 ring-blue-200 dark:ring-blue-800 @endif">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 text-sm mb-2 line-clamp-2">{{ $product->name }}</div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $this->currencySymbol }}{{ number_format($product->price ?? 0, 2) }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs px-2 py-1 rounded-full @if ($available === 0) bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 @elseif($available < 10) bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 @else bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 @endif">
                                                            @if ($available === 0) Out of Stock @elseif($available < 10) Low: {{ $available }} @else In Stock @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <button type="button" wire:click="addToRequestedItems('{{ $product->id }}', 10)" class="flex-shrink-0 px-3 py-2 text-xs font-medium rounded-md transition-all duration-200 transform hover:scale-105 @if(isset($requestedItems[$product->id])) bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900/50 dark:text-blue-300 dark:border-blue-700 @else bg-green-600 hover:bg-green-500 text-white shadow-sm hover:shadow-md @endif" @if(isset($requestedItems[$product->id])) disabled @endif>
                                                    @if(isset($requestedItems[$product->id]))
                                                        <div class="flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span>Added</span>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span>Add</span>
                                                        </div>
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12 text-zinc-500 h-full flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <h3 class="text-lg font-medium text-zinc-700 dark:text-zinc-300 mb-2">Select a Department</h3>
                                <p class="text-sm max-w-sm">Choose a production department above to view available products for manufacturing.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">

                <div class="flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                        wire:click="requestItems"
                        wire:loading.attr="disabled"

                        @if(empty($requestedItems)) disabled @endif
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md transition-colors duration-200 min-w-[160px] @if(empty($requestedItems)) bg-zinc-300 text-zinc-500 cursor-not-allowed @else bg-blue-600 hover:bg-blue-700 text-white @endif">
                        <span wire:loading.remove wire:target="requestItems">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Request
                        </span>
                        <span wire:loading wire:target="requestItems">
                            <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Custom scrollbar styling */
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgb(156 163 175);
        border-radius: 3px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgb(107 114 128);
    }

    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: rgb(75 85 99);
    }

    .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: rgb(55 65 81);
    }

    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    </style>
</div>