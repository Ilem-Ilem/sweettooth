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
        title="Production Requests"
        :items="[
            ['label' => 'Dashboard', 'url' => branch_route('branch-dashboard.index')],
            ['label' => 'Production'],
            ['label' => 'Production Requests']
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
            New Production Request
        </button>
    </div>

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
            <!-- Advanced Search -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Advanced Search</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by item request number or recipe name..."
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Shift</label>
                    <select wire:model.live="filterShift"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">All Shifts</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->shift_number }} - {{ $shift->employee->name ?? 'N/A' }}
                                ({{ $shift->shift_date ? $shift->shift_date->format('Y-m-d') : 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Recipe</label>
                    <select wire:model.live="filterRecipe"
                        class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">All Recipes</option>
                        @foreach($recipes as $recipe)
                            <option value="{{ $recipe->id }}">{{ $recipe->product_name }}</option>
                        @endforeach
                    </select>
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
            ['index' => 'item_request', 'label' => 'Item Request'],
            ['index' => 'department', 'label' => 'Department'],
            ['index' => 'shift', 'label' => 'Shift'],
            ['index' => 'recipe', 'label' => 'Recipe'],
            ['index' => 'planned_qty', 'label' => 'Planned Qty'],
            ['index' => 'ready_status', 'label' => 'Status'],
            ['index' => 'actions', 'label' => 'Actions'],
        ]"
        :rows="$productionRequests"
        striped
        paginate
        persist
        :filter="['quantity' => 'table_quantity', 'search' => 'search']"
        :quantity="[2, 10, 25, 50, 100]">

        @interact('column_item_request', $row)
            <button wire:click="viewProductionRequest({{ $row->id }})" class="font-mono text-blue-600 dark:text-blue-400 hover:underline">
                {{ $row->itemRequest->request_number ?? 'N/A' }}
            </button>
        @endinteract

        @interact('column_department', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->itemRequest->department->name ?? 'N/A' }}
            </span>
        @endinteract

        @interact('column_shift', $row)
            <div class="text-sm">
                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row->shift->shift_number ?? 'N/A' }}</div>
                <div class="text-zinc-500 dark:text-zinc-400">{{ $row->shift->employee->name ?? 'N/A' }}</div>
            </div>
        @endinteract

        @interact('column_recipe', $row)
            <span class="text-zinc-900 dark:text-zinc-100">
                {{ $row->recipe->product_name ?? 'No Recipe' }}
            </span>
        @endinteract

        @interact('column_planned_qty', $row)
            <span class="text-zinc-900 dark:text-zinc-100 font-semibold">
                {{ number_format($row->planned_production_quantity, 2) }}
            </span>
        @endinteract

        @interact('column_ready_status', $row)
            @php
                $isReady = $row->isReadyForProduction();
                $statusColor = $isReady
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                $statusText = $isReady ? 'Ready' : 'Pending Setup';
            @endphp
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                {{ $statusText }}
            </span>
        @endinteract

        @interact('column_actions', $row)
            <div class="flex gap-2">
                <button wire:click="edit({{ $row->id }})"
                    class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                    title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button wire:click="delete({{ $row->id }})"
                    wire:confirm="Are you sure you want to delete this production request?"
                    class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                    title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        @endinteract
    </x-table>

    <!-- Pending Item Requests Section -->
    @if($pendingItemRequests->count() > 0)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Pending Approved Item Requests
        </h3>
        <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">
            These approved item requests haven't been assigned to production yet. Click "New Production Request" to convert them.
        </p>
        <div class="space-y-2">
            @foreach($pendingItemRequests as $itemRequest)
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">{{ $itemRequest->request_number }}</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $itemRequest->department->name ?? 'N/A' }} •
                            {{ $itemRequest->requestDetails->count() }} items •
                            {{ $itemRequest->request_date ? $itemRequest->request_date->format('Y-m-d') : 'N/A' }}
                        </div>
                    </div>
                    <button wire:click="openCreateModal(); $set('item_request_id', {{ $itemRequest->id }})"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                        Convert to Production
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Create/Edit Modal -->
    <div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="show = false">
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
            @click="$wire.closeModal()">
        </div>

        <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full md:w-2/3 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $isEditing ? 'Edit Production Request' : 'New Production Request' }}
                </h2>
                <button wire:click="closeModal"
                    class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin">
                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-4">
                    <!-- Item Request Selection -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Item Request *</label>
                        <select wire:model="item_request_id" {{ $isEditing ? 'disabled' : '' }}
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 disabled:bg-zinc-100 dark:disabled:bg-zinc-700">
                            <option value="">Select Item Request</option>
                            @foreach($pendingItemRequests as $itemRequest)
                                <option value="{{ $itemRequest->id }}">
                                    {{ $itemRequest->request_number }} - {{ $itemRequest->department->name ?? 'N/A' }}
                                    ({{ $itemRequest->request_date ? $itemRequest->request_date->format('Y-m-d') : 'N/A' }})
                                </option>
                            @endforeach
                            @if($isEditing && $item_request_id)
                                @php
                                    $currentItemRequest = \App\Models\ItemRequest::find($item_request_id);
                                @endphp
                                @if($currentItemRequest)
                                    <option value="{{ $currentItemRequest->id }}" selected>
                                        {{ $currentItemRequest->request_number }} - {{ $currentItemRequest->department->name ?? 'N/A' }}
                                    </option>
                                @endif
                            @endif
                        </select>
                        @error('item_request_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Shift Selection -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Shift *</label>
                        <select wire:model="shift_id"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->shift_number }} - {{ $shift->employee->name ?? 'N/A' }}
                                    ({{ $shift->shift_date ? $shift->shift_date->format('Y-m-d') : 'N/A' }})
                                    - {{ ucfirst($shift->shift_type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Recipe Selection -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Recipe (Optional)</label>
                        <select wire:model="recipe_id"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Recipe</option>
                            @foreach($recipes as $recipe)
                                <option value="{{ $recipe->id }}">
                                    {{ $recipe->product_name }} ({{ $recipe->sku }})
                                    - Yield: {{ number_format($recipe->yield_quantity, 2) }} {{ $recipe->uom }}
                                </option>
                            @endforeach
                        </select>
                        @error('recipe_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Planned Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Planned Production Quantity *</label>
                        <input type="number" step="0.01" wire:model="planned_production_quantity"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter planned quantity">
                        @error('planned_production_quantity')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Notes</label>
                        <textarea wire:model="notes" rows="3"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter any additional notes"></textarea>
                        @error('notes')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                <button wire:click="closeModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="{{ $isEditing ? 'update' : 'save' }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    {{ $isEditing ? 'Update' : 'Create' }} Production Request
                </button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-data="{ show: @entangle('showDetailModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-hidden"
        @keydown.escape.window="show = false">
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50"
            @click="$wire.closeDetailModal()">
        </div>

        <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full md:w-2/3 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Production Request Details</h2>
                <button wire:click="closeDetailModal"
                    class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($selectedProductionRequest)
            <div class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin space-y-6">
                <!-- Production Request Information -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Production Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Item Request</p>
                            <p class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->itemRequest->request_number ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Status</p>
                            @php
                                $isReady = $selectedProductionRequest->isReadyForProduction();
                                $statusColor = $isReady
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                $statusText = $isReady ? 'Ready for Production' : 'Pending Setup';
                            @endphp
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Department</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->itemRequest->department->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Branch</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->itemRequest->branch->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Planned Quantity</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($selectedProductionRequest->planned_production_quantity, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Created At</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->created_at ? $selectedProductionRequest->created_at->format('Y-m-d H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @if($selectedProductionRequest->notes)
                    <div class="mt-4">
                        <p class="text-zinc-500 dark:text-zinc-400">Notes</p>
                        <p class="text-zinc-900 dark:text-zinc-100 mt-1">{{ $selectedProductionRequest->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Shift Information -->
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Shift Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Shift Number</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->shift->shift_number ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Shift Type</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ ucfirst($selectedProductionRequest->shift->shift_type ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Employee</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->shift->employee->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Shift Date</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->shift->shift_date ? $selectedProductionRequest->shift->shift_date->format('Y-m-d') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recipe Information -->
                @if($selectedProductionRequest->recipe_id)
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Recipe Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Product Name</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->recipe->product_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">SKU</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $selectedProductionRequest->recipe->sku ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Yield Quantity</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ number_format($selectedProductionRequest->recipe->yield_quantity, 2) }}
                                {{ $selectedProductionRequest->recipe->uom }}
                            </p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400">Cost Per Unit</p>
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                ${{ number_format($selectedProductionRequest->recipe->cost_per_unit, 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Recipe Ingredients -->
                    @if($selectedProductionRequest->recipe->ingredients && $selectedProductionRequest->recipe->ingredients->count() > 0)
                    <div class="mt-4">
                        <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Ingredients</h4>
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-zinc-100 dark:bg-zinc-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Item</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">Quantity</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">UOM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedProductionRequest->recipe->ingredients as $ingredient)
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $ingredient->item->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($ingredient->quantity, 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $ingredient->uom }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Requested Items -->
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Requested Items</h3>
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300">UOM</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">Requested</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-zinc-700 dark:text-zinc-300">Approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedProductionRequest->itemRequest->requestDetails as $detail)
                                <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                    <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $detail->item->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $detail->uom }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($detail->quantity_requested, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($detail->quantity_approved, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end">
                <button wire:click="closeDetailModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>
