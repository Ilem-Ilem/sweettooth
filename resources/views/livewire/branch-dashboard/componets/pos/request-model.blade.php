<div>
    <!-- Kitchen Request Modal -->
    <div x-show="showKitchenModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center"
        @click.outside="showKitchenModal = false">
        <div class="absolute inset-0 bg-black/40" @click="showKitchenModal = false"></div>
        <div
            class="relative w-full max-w-lg mx-auto rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg p-4">
            <div class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Request from {{ count($requestedItems) }} Kitchen</div>
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
                        <button type="button" wire:click="addToRequestedItems('{{ $product->id }}')"
                            class="bg-green-500 rounded py-2 px-5 cursor-pointer hover:bg-green-700 transition-all duration-70  @if(in_array($product->id, $requestedItems)) cursor-not-allowed @endif"
                            @if(in_array($product->id, $requestedItems))
                                disabled
                            @endif>
                           + Add
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="button" @click="showKitchenModal = false"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-700">Close</button>
                <button type="button" 
                @if($requestedItems == [])
                disabled
                @endif
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md bg-zinc-300 text-white cursor-not-allowed">Send
                    Request</button>
            </div>
        </div>
    </div>
</div>
