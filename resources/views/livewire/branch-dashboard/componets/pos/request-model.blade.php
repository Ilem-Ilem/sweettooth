<div>
    <!-- Kitchen Request Modal -->
    <div x-show="showKitchenModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center"
        @click.outside="showKitchenModal = false">
        <div class="absolute inset-0 bg-black/40" @click="showKitchenModal = false"></div>
        <div
            class="relative w-full max-w-2xl mx-auto rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg p-4 max-h-[90vh] flex flex-col">
            <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Request Products from Kitchen</div>

            <!-- Selected Products Section -->
            @if(!empty($requestedItems))
            <div class="mb-4 border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Selected Products ({{ count($requestedItems) }})</div>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($requestedItems as $productId => $qty)
                        @php
                            $product = \App\Models\Product::find($productId);
                        @endphp
                        @if($product)
                        <div class="flex items-center gap-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $product->name }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-zinc-600 dark:text-zinc-400">Batches:</label>
                                <input type="number"
                                    wire:model.live.debounce.300ms="requestedItems.{{ $productId }}"
                                    wire:change="updateQuantity('{{ $productId }}', $event.target.value)"
                                    min="1"
                                    value="{{ $qty }}"
                                    class="w-20 px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                            </div>
                            <button type="button"
                                wire:click="removeFromRequestedItems('{{ $productId }}')"
                                class="p-1 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Available Products Section -->
            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Available Products</div>
            <div class="space-y-2 h-100 overflow-y-hidden overflow-y-scroll">
                @foreach ($this->products as $product)
                    @php
                        $stock = \App\Models\ProductStock::query()
                            ->whereDate('stock_date', \Carbon\Carbon::today())
                            ->where('product_id', $product->id)
                            ->first();
                        $available = 0;
                        if ($stock) {
                            $stock->updateCalculatedFields();
                            $available = max(0, (float) $stock->closing_quantity);
                        }
                    @endphp
                    <div
                        class="rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3 flex items-center gap-4">
                        <div class="flex-1">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $product->name }}</div>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-zinc-500">GHS
                                    {{ number_format($product->price ?? 0, 2) }}</span>
                                <span class="text-xs">
                                    @if ($available === 0)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-900/30">Out
                                            of Stock</span>
                                    @elseif($available < 10)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-orange-700 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/30">Low
                                            Stock ({{ $available }})</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-emerald-700 bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-900/30">In
                                            Stock ({{ $available }})</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <button type="button" wire:click="addToRequestedItems('{{ $product->id }}', 10)"
                            class="text-white rounded py-2 px-5 cursor-pointer transition-all duration-200 @if(isset($requestedItems[$product->id])) bg-zinc-400 cursor-not-allowed @else bg-green-600 hover:bg-green-700 @endif"
                            @if(isset($requestedItems[$product->id]))
                                disabled
                            @endif>
                           @if(isset($requestedItems[$product->id])) Added @else + Add @endif
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="button" @click="showKitchenModal = false"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-700">Close</button>
                <button type="button"
                    wire:click="requestItems"
                    wire:loading.attr="disabled"
                    @if(empty($requestedItems))
                        disabled
                    @endif
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md transition-colors duration-200"
                    :class="@js(empty($requestedItems)) ? 'bg-zinc-300 text-zinc-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'">
                    <span wire:loading.remove wire:target="requestItems">Send Request</span>
                    <span wire:loading wire:target="requestItems">Sending...</span>
                </button>
            </div>
        </div>
    </div>
</div>
