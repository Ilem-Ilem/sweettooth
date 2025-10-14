<div class="p-3 space-y-3">

    <style>
        /* Custom scrollbar styles */
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
        title="Items Management"
        :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Inventory'],
            ['label' => 'Items Management']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <button wire:click="openCreateModal"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Item
        </button>
    </div>

    <!-- Export Buttons -->
    <div class="flex justify-end items-center space-x-2">
        <button wire:click="exportExcel"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Excel
        </button>
        <button wire:click="exportPdf"
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Export PDF
        </button>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-data="{ selectedIds: @entangle('selectedIds') }" x-show="selectedIds.length > 0" x-cloak
        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                    <span x-text="selectedIds.length"></span> item(s) selected
                </span>
                <button wire:click="toggleBulkMode"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline text-left">
                    Clear Selection
                </button>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button wire:click="bulkDeleteItems"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div x-data="{ open: false, advanced: false }"
        class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 transition-all duration-300">
        <!-- Header / Toggle Button -->
        <div class="flex justify-between items-center px-3 py-2 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L14.293 13H10v5l-4-4v-3.586L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </h2>

            <button @click="open = !open"
                class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8h16M4 16h16" />
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span x-text="open ? 'Close' : 'Show Filters'"></span>
            </button>
        </div>

        <!-- Filter Body -->
        <div x-show="open" x-collapse class="p-3 space-y-3">
            <!-- Basic Filters -->
            <div class="">
                <!-- Advanced Search Toggle -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Advanced
                        Search</label>
                    <button @click="advanced = !advanced"
                        class="flex items-center w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2 text-zinc-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path x-show="!advanced" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M12 5l7 7-7 7" />
                            <path x-show="advanced" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 12H5m7 7l-7-7 7-7" />
                        </svg>
                        <span x-text="advanced ? 'Hide Advanced' : 'Show Advanced'"></span>
                    </button>
                </div>
            </div>

            <!-- Advanced Search Dropdown -->
            <div x-show="advanced" x-collapse
                class="p-2.5 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 mt-2">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Search -->
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Search</label>
                        <div class="relative">
                            <input type="text" wire:model.live="advancedSearch" placeholder="Search keyword..."
                                class="w-full pl-10 pr-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-zinc-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date
                            Range</label>
                        <div class="flex space-x-2">
                            <input type="date" wire:model.live="dateFrom"
                                class="w-1/2 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                            <input type="date" wire:model.live="dateTo"
                                class="w-1/2 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Branch Filter -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Branch</label>
                    <x-select.styled
                        wire:model.live="filterBranch"
                        :options="$branches->map(fn($branch) => ['label' => $branch->name, 'value' => $branch->id])->toArray()"
                        select="label:label|value:value"
                        placeholder="All Branches"
                        searchable
                    />
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Category</label>
                    <x-select.styled
                        wire:model.live="filterCategory"
                        :options="[
                            ['label' => 'Raw Material', 'value' => 'raw_material'],
                            ['label' => 'Packaging', 'value' => 'packaging'],
                            ['label' => 'Consumable', 'value' => 'consumable'],
                            ['label' => 'Equipment', 'value' => 'equipment']
                        ]"
                        select="label:label|value:value"
                        placeholder="All Categories"
                        searchable
                    />
                </div>

                <!-- Stock Level Filter -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Stock Level</label>
                    <x-select.styled
                        wire:model.live="filterStockLevel"
                        :options="[
                            ['label' => 'Low Stock (Below Reorder Level)', 'value' => 'low'],
                            ['label' => 'High Stock (Above Reorder Level)', 'value' => 'high'],
                            ['label' => 'Out of Stock', 'value' => 'out_of_stock']
                        ]"
                        select="label:label|value:value"
                        placeholder="All Stock Levels"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                    <x-select.styled
                        wire:model.live="filterStatus"
                        :options="[
                            ['label' => 'Active', 'value' => 'active'],
                            ['label' => 'Inactive', 'value' => 'inactive']
                        ]"
                        select="label:label|value:value"
                        placeholder="All Status"
                    />
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2 justify-end pt-2.5 border-t border-zinc-200 dark:border-zinc-700">
                <button wire:click="applyFilters"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                    </svg>
                    Apply
                </button>
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
    <x-table :$headers :$rows selectable wire:model="selectedIds" striped paginate persist :filter="['quantity' => 'quantity', 'search' => 'search']"
        :quantity="[10, 25, 50, 100]">
        @interact('column_sku', $row)
            <span class="font-mono text-zinc-900 dark:text-zinc-100">
                {{ $row->sku }}
            </span>
        @endinteract

        @interact('column_branch', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->branch ? $row->branch->name : 'N/A' }}
            </span>
        @endinteract

        @interact('column_category', $row)
            <span
                class="px-2 py-1 text-xs font-semibold rounded-full
                {{ $row->category === 'raw_material' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                {{ $row->category === 'packaging' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                {{ $row->category === 'consumable' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                {{ $row->category === 'equipment' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $row->category)) }}
            </span>
        @endinteract

        @interact('column_uom', $row)
            <span class="text-zinc-600 dark:text-zinc-400 uppercase">
                {{ $row->uom }}
            </span>
        @endinteract

        @interact('column_stock', $row)
            @php
                $currentStock = $row->getCurrentStock();
                $isBelowReorder = $row->isBelowReorderLevel();
                $stock = $row->stocks()->where('branch_id', $row->branch_id)->first();
            @endphp
            <div x-data="{ editing: false, quantity: {{ $currentStock }} }" class="flex items-center space-x-2">
                <div x-show="!editing" @click="editing = true"
                    class="cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 px-2 py-1 rounded transition-colors">
                    <span class="font-medium {{ $isBelowReorder ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                        {{ number_format($currentStock, 2) }}
                    </span>
                    <svg class="w-4 h-4 inline ml-1 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div x-show="editing" class="flex items-center space-x-1" x-cloak>
                    <input type="number" step="0.01" x-model="quantity"
                        @keydown.enter="$wire.updateStock({{ $row->id }}, quantity).then(() => editing = false)"
                        @keydown.escape="editing = false; quantity = {{ $currentStock }}"
                        class="w-24 px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    <button @click="$wire.updateStock({{ $row->id }}, quantity).then(() => editing = false)"
                        class="p-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
                        title="Save">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                    <button @click="editing = false; quantity = {{ $currentStock }}"
                        class="p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                        title="Cancel">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endinteract

        @interact('column_reorder_level', $row)
            <span class="text-zinc-600 dark:text-zinc-400">
                {{ $row->reorder_level ? number_format($row->reorder_level, 2) : 'N/A' }}
            </span>
        @endinteract

        @interact('column_status', $row)
            <span
                class="px-2 py-1 text-xs font-semibold rounded-full
                {{ $row->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                {{ ucfirst($row->status) }}
            </span>
        @endinteract

        @interact('column_action', $row)
            <div class="flex items-center space-x-2">
                <button wire:click="openStockModal({{ $row->id }})"
                    class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Manage Stock">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </button>
                <button wire:click="openEditModal({{ $row->id }})"
                    class="p-2 text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                    title="Edit Item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button wire:click="delete({{ $row->id }})"
                    class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Delete Item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        @endinteract
    </x-table>

    <!-- Create/Edit Item Modal (Slide-in) -->
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
            class="fixed inset-y-0 right-0 w-full md:w-1/2 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $isEditing ? 'Edit Item' : 'Add New Item' }}</h2>
                <button wire:click="closeModal"
                    class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Form Content -->
            <div
                class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-700 scrollbar-track-transparent">
                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Branch -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Branch *</label>
                        <x-select.styled
                            wire:model="branch_id"
                            :options="$branches->map(fn($branch) => ['label' => $branch->name, 'value' => $branch->id])->toArray()"
                            select="label:label|value:value"
                            placeholder="Select Branch"
                            searchable
                            required
                        />
                        @error('branch_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Item Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Item Name *</label>
                        <input type="text" wire:model.live="name"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter item name" required>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Category *</label>
                        <x-select.styled
                            wire:model.live="category"
                            :options="[
                                ['label' => 'Raw Material', 'value' => 'raw_material'],
                                ['label' => 'Packaging', 'value' => 'packaging'],
                                ['label' => 'Consumable', 'value' => 'consumable'],
                                ['label' => 'Equipment', 'value' => 'equipment']
                            ]"
                            select="label:label|value:value"
                            placeholder="Select Category"
                            required
                        />
                        @error('category')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SKU (Auto-generated but editable) -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">SKU *</label>
                        <input type="text" wire:model="sku"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 font-mono"
                            placeholder="Auto-generated from category and name" required>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Auto-generated, but you can modify it</p>
                        @error('sku')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unit of Measure -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Unit of Measure *</label>
                        <x-select.styled
                            wire:model="uom"
                            :options="[
                                ['label' => 'Grams', 'value' => 'grams'],
                                ['label' => 'Kilograms (Kg)', 'value' => 'kg'],
                                ['label' => 'Liters', 'value' => 'liters'],
                                ['label' => 'Milliliters (ml)', 'value' => 'ml'],
                                ['label' => 'Pieces (Pcs)', 'value' => 'pcs'],
                                ['label' => 'Units', 'value' => 'units'],
                                ['label' => 'Bags', 'value' => 'bags'],
                                ['label' => 'Cartons', 'value' => 'cartons']
                            ]"
                            select="label:label|value:value"
                            placeholder="Select UOM"
                            searchable
                            required
                        />
                        @error('uom')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Reorder Level -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Reorder Level</label>
                        <input type="number" step="0.01" wire:model="reorder_level"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter reorder level">
                        @error('reorder_level')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Max Stock Level -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Max Stock Level</label>
                        <input type="number" step="0.01" wire:model="max_stock_level"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter max stock level">
                        @error('max_stock_level')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Status *</label>
                        <x-select.styled
                            wire:model="status"
                            :options="[
                                ['label' => 'Active', 'value' => 'active'],
                                ['label' => 'Inactive', 'value' => 'inactive']
                            ]"
                            select="label:label|value:value"
                            placeholder="Select Status"
                            required
                        />
                        @error('status')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                <button wire:click="closeModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    {{ $isEditing ? 'Update Item' : 'Create Item' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Stock Management Modal (Slide-in) -->
    <div x-data="{ show: @entangle('showStockModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="show = false">
        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
            @click="$wire.closeStockModal()">
        </div>

        <!-- Slide-in Panel -->
        <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
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
                <button wire:click="closeStockModal"
                    class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
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
                        $item = \App\Models\Item::with(['stocks', 'branch'])->find($stockItemId);
                        $stock = $item ? $item->stocks()->where('branch_id', $item->branch_id)->first() : null;
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
                                    <span class="text-zinc-600 dark:text-zinc-400">Branch:</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ $item->branch->name }}</span>
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

                        <!-- Current Stock Overview -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <div class="text-xs text-green-600 dark:text-green-400 mb-1">Available</div>
                                <div class="text-xl font-bold text-green-700 dark:text-green-300">
                                    {{ number_format($stock->quantity_available ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                <div class="text-xs text-yellow-600 dark:text-yellow-400 mb-1">Reserved</div>
                                <div class="text-xl font-bold text-yellow-700 dark:text-yellow-300">
                                    {{ number_format($stock->quantity_reserved ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <div class="text-xs text-red-600 dark:text-red-400 mb-1">Damaged</div>
                                <div class="text-xl font-bold text-red-700 dark:text-red-300">
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
                                <input type="number" step="0.01" wire:model="stockQuantity"
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
                                <input type="number" step="0.01" wire:model="stockReserved"
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
                                <input type="number" step="0.01" wire:model="stockDamaged"
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
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Total Stock:</span>
                                    <span class="text-xl font-bold text-blue-900 dark:text-blue-100">
                                        {{ number_format(($stockQuantity ?? 0) + ($stockReserved ?? 0) + ($stockDamaged ?? 0), 2) }}
                                    </span>
                                </div>
                            </div>
                        </form>
                    @endif
                @endif
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                <button wire:click="closeStockModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="saveStock"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Update Stock
                </button>
            </div>
        </div>
    </div>

</div>
