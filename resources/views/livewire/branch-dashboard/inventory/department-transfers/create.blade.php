<div class="p-3 space-y-4">
    <x-breadcrumb
        title="New Inter-department Dispatch"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Inventory'],
            ['label' => 'Inter-department Dispatch', 'url' => branch_route('branch-dashboard.inventory.department-transfers')],
            ['label' => 'New Transfer']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Target Department</label>
                <select wire:model="to_department_id"
                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                    <option value="">Select department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('to_department_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Receiver (Employee)</label>
                <select wire:model="receiver_id"
                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                    <option value="">Select receiver</option>
                    @foreach($users as $user)
                        @if(! $to_department_id || (int) $user->department_id === (int) $to_department_id)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('receiver_id') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
            <textarea wire:model="notes" rows="2"
                class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200"></textarea>
        </div>

        <div class="space-y-3">
            @foreach($items as $index => $row)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Item</label>
                        <select wire:model="items.{{ $index }}.item_id"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                            <option value="">Select item</option>
                            @foreach($itemsList as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error("items.$index.item_id") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Quantity</label>
                        <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                        @error("items.$index.quantity") <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">UOM</label>
                        <select wire:model="items.{{ $index }}.uom_id"
                            class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                            <option value="">Default</option>
                            @foreach($uoms as $uom)
                                <option value="{{ $uom->id }}">{{ $uom->symbol ?? $uom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="removeItemRow({{ $index }})"
                            class="px-3 py-2 bg-zinc-200 hover:bg-zinc-300 rounded">
                            Remove
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" wire:click="addItemRow"
            class="px-3 py-2 bg-zinc-200 hover:bg-zinc-300 rounded">
            Add Item
        </button>
    </div>

    <div class="flex gap-2">
        <a href="{{ branch_route('branch-dashboard.inventory.department-transfers') }}"
           class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 rounded">
            Cancel
        </a>
        <button wire:click="save"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            Save Transfer
        </button>
    </div>
</div>
