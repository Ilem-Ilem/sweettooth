<div x-data="{

        bulkAction: '',
        selectAll: false,
        selected: @entangle('selected'),
        // IDs for the rows on the current page
        rowIds: @json($branches->pluck('id')),
        openManagerForm: false,
        showAlert: false,
        alertMessage: '',
        alertType: 'success'
     }"
     x-cloak
     @branch-deleted.window="showAlert = true; alertMessage = $event.detail; alertType = 'warning'"
     @bulk-action-completed.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'; selected = []; selectAll = false"
     x-effect="selectAll = rowIds.length > 0 && rowIds.every(id => selected.includes(id))"
     >

     <div class="p-6">
         <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">Branch Management</h2>

        </div>

        <!-- Filters and Actions -->
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <!-- Search -->
            <div class="flex items-center border rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 dark:border-zinc-700 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                </svg>
                <input type="text" placeholder="Search branches..."
                    class="outline-none border-none focus:ring-0 text-sm w-48 bg-transparent dark:text-zinc-100"
                    wire:model.live="search">
            </div>

            <div class="flex items-center gap-2">
                <!-- Region Filter -->
                <select wire:model.live="filterRegion" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        @if($region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endif
                    @endforeach
                </select>

            </div>

            <!-- Bulk Action -->
            <div class="flex items-center gap-2" x-show="selected.length > 0">
                <select x-model="bulkAction" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">Bulk Actions</option>
                    <option value="restore">Restore</option>
                    <option value="delete">Delete</option>
                </select>
                <button @click="$wire.applyBulkAction(bulkAction, selected); bulkAction = ''"
                        x-show="bulkAction"
                        class="bg-zinc-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-zinc-700 transition">
                    Apply
                </button>
            </div>

            <!-- Export Buttons -->
            <div class="flex gap-2">
                <button wire:click="export('excel')" class="bg-green-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-700 transition">Excel</button>
                <button wire:click="export('csv')" class="bg-yellow-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-yellow-600 transition">CSV</button>
                <button wire:click="export('pdf')" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600 transition">PDF</button>
            </div>
        </div>

                <!-- Selected Rows Count -->
        <template x-if="selected.length > 0">
            <div class="mb-2 text-sm text-blue-600 dark:text-blue-400 font-medium">
                <span x-text="selected.length"></span> rows selected
            </div>
        </template>

        <!-- Table -->
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 shadow rounded-lg">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-zinc-100 dark:bg-zinc-800 border-b dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox"
                                   x-model="selectAll"
                                   @change="selected = $event.target.checked ? [...rowIds] : selected.filter(id => !rowIds.includes(id))"
                                   class="rounded">
                        </th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Branch Info</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Location</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Contact</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Manager</th>
                         <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b dark:border-zinc-700">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $branch->id }}" x-model="selected" class="rounded">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch->code }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-zinc-900 dark:text-zinc-100">{{ $branch->city }}, {{ $branch->state }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch->country }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($branch->email)
                                    <div class="text-zinc-900 dark:text-zinc-100">{{ $branch->email }}</div>
                                @endif
                                @if($branch->phone)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch->phone }}</div>
                                @endif
                                @if(!$branch->email && !$branch->phone)
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $branch->manager->name ?? '-' }}</td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="restore('{{ $branch->id }}')"
                                            class="text-green-600 dark:text-green-400 hover:text-green-800 p-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h7V3m0 0l-9 9 9 9M13 21h8" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete('{{ $branch->id }}')"
                                            onclick="return confirm('Permanently delete this branch?')"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 p-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No branches found</p>
                                    <p class="text-sm">Get started by creating your first branch</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
     </div>
</div>
