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
        title="Stock Movements"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Inventory'],
            ['label' => 'Stock Movements']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Filters Section -->
    <div x-data="{ open: false }"
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 transition-all duration-300">
        <div class="flex justify-between items-center px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Movement Type</label>
                    <select wire:model.live="filterType"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="in">In</option>
                        <option value="out">Out</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date To</label>
                    <input type="date" wire:model.live="filterDateTo"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex flex-wrap gap-2 justify-end pt-2.5 border-t border-zinc-200 dark:border-zinc-700">
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

    <!-- Table -->
    <x-table
        :headers="[
            ['index' => 'movement_date', 'label' => 'Date'],
            ['index' => 'item_name', 'label' => 'Item'],
            ['index' => 'movement_type', 'label' => 'Type'],
            ['index' => 'quantity', 'label' => 'Quantity'],
            ['index' => 'reference', 'label' => 'Reference'],
            ['index' => 'recorder', 'label' => 'Recorded By'],
            ['index' => 'notes', 'label' => 'Notes'],
        ]"
        :rows="$movements"
        striped
        paginate
        persist
        :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 25, 50, 100]">

        @interact('column_movement_date', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->movement_date ? $row->movement_date->format('Y-m-d H:i') : 'N/A' }}
            </span>
        @endinteract

        @interact('column_item_name', $row)
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                {{ $row->item->name ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_movement_type', $row)
            @php
                $typeColors = [
                    'in' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'out' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'transfer' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                ];
                $typeIcons = [
                    'in' => '↓',
                    'out' => '↑',
                    'adjustment' => '⟳',
                    'transfer' => '⇄',
                ];
            @endphp
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$row->movement_type] ?? '' }}">
                {{ $typeIcons[$row->movement_type] ?? '' }} {{ ucfirst($row->movement_type) }}
            </span>
        @endinteract

        @interact('column_quantity', $row)
            @php
                $quantityClass = $row->movement_type === 'in' ? 'text-green-600 dark:text-green-400' : ($row->movement_type === 'out' ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100');
            @endphp
            <span class="font-medium {{ $quantityClass }}">
                {{ $row->movement_type === 'in' ? '+' : ($row->movement_type === 'out' ? '-' : '') }}{{ number_format($row->quantity, 2) }}
            </span>
        @endinteract

        @interact('column_reference', $row)
            @if($row->reference_type && $row->reference_id)
            <span class="text-zinc-600 dark:text-zinc-400 text-sm">
                {{ class_basename($row->reference_type) }} #{{ $row->reference_id }}
            </span>
            @else
            <span class="text-zinc-400 dark:text-zinc-500">N/A</span>
            @endif
        @endinteract

        @interact('column_recorder', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->recorder->name ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_notes', $row)
            <span class="text-zinc-600 dark:text-zinc-400 text-sm">
                {{ $row->notes ? Str::limit($row->notes, 50) : 'N/A' }}
            </span>
        @endinteract
    </x-table>

</div>
