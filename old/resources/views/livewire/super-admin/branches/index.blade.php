<div x-data="{
        openForm: false,
        openEditModal: false,
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
     @close-employee-form.window="openManagerForm = false"
     @open-edit-modal.window="openEditModal = true"
     @close-edit-modal.window="openEditModal = false"
     @branch-created.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'"
     @branch-updated.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'"
     @branch-deleted.window="showAlert = true; alertMessage = $event.detail; alertType = 'warning'"
     @status-updated.window="showAlert = true; alertMessage = $event.detail; alertType = 'info'"
     @bulk-action-completed.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'; selected = []; selectAll = false"
     x-effect="rowIds = JSON.parse($refs.rowIdsHolder?.dataset.rowIds || '[]'); selected = selected.filter(id => rowIds.includes(id)); selectAll = rowIds.length > 0 && rowIds.every(id => selected.includes(id))"
     >

     <div class="p-6">
         <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">Branch Management</h2>
            <button
                @click="openForm = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Branch
            </button>
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

            <div class="flex flex-wrap items-center gap-2">
                <!-- Region Filter -->
                <select wire:model.live="filterRegion" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        @if($region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endif
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model.live="filterStatus" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <!-- Country Filter -->
                <input type="text" wire:model.live.debounce.300ms="filterCountry" placeholder="Country"
                       class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">

                <!-- City Filter -->
                <input type="text" wire:model.live.debounce.300ms="filterCity" placeholder="City"
                       class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">

                <!-- Created Date Range -->
                <div class="flex items-center gap-1">
                    <input type="date" wire:model.live="dateFrom"
                           class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <span class="text-zinc-400 text-sm">to</span>
                    <input type="date" wire:model.live="dateTo"
                           class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                </div>
            </div>

            <!-- Bulk Action -->
            <div class="flex items-center gap-2" x-show="selected.length > 0">
                <select x-model="bulkAction" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">Bulk Actions</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="delete">Delete</option>
                </select>
                <button @click="$wire.applyBulkAction(bulkAction, selected); bulkAction = ''" wire:loading.attr="disabled"
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

        <!-- Hidden current page IDs for Alpine to consume -->
        <span x-ref="rowIdsHolder" data-row-ids='@json($branches->pluck("id"))' class="hidden"></span>

        <!-- Table -->
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 shadow rounded-lg relative">
            <div wire:loading.flex class="absolute inset-0 bg-white/50 dark:bg-zinc-900/50 items-center justify-center z-10">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-zinc-100 dark:bg-zinc-800 border-b dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox" wire:loading.attr="disabled"
                                   x-model="selectAll"
                                   x-bind:indeterminate="selected.some(id => rowIds.includes(id)) && !selectAll"
                                   @change="selected = $event.target.checked ? [...rowIds] : selected.filter(id => !rowIds.includes(id))"
                                   class="rounded">
                        </th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Branch Info</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Location</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Contact</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Manager</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b dark:border-zinc-700">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $branch->id }}" x-model="selected" class="rounded" wire:loading.attr="disabled">
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
                                <button wire:click="toggleStatus('{{ $branch->id }}')" class="focus:outline-none">
                                    @if($branch->is_active)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded cursor-pointer hover:bg-green-200">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded cursor-pointer hover:bg-red-200">Inactive</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit('{{ $branch->id }}')"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 p-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete('{{ $branch->id }}')"
                                            onclick="return confirm('Are you sure you want to delete this branch?')"
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

        <!-- Edit Modal -->
        <div x-show="openEditModal"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed top-0 right-0 h-full w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50">

            <h3 class="text-lg font-semibold mb-4 text-zinc-800 dark:text-zinc-100">Edit Branch</h3>

            <form wire:submit.prevent="updateBranch" class="space-y-4 max-h-[80vh] overflow-y-auto p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Branch Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Branch Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editName" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editName')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Branch Code -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Branch Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editCode" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editCode')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Full Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editLocation" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editLocation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editCountry" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editCountry')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            State / Region <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editState" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editState')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="editCity" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editCity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Postal Code
                        </label>
                        <input type="text" wire:model.defer="editPostalCode"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editPostalCode')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Timezone
                        </label>
                        <input type="text" wire:model.defer="editTimezone" placeholder="e.g., Africa/Lagos"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editTimezone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Contact Phone
                        </label>
                        <input type="text" wire:model.defer="editPhone"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editPhone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Contact Email
                        </label>
                        <input type="email" wire:model.defer="editEmail"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('editEmail')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Manager -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Branch Manager
                        </label>
                        <select wire:model.defer="editManagerUserId"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Manager</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('editManagerUserId')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.defer="editIsActive" class="rounded">
                        <label class="text-sm text-zinc-700 dark:text-zinc-300">Branch is active</label>
                        @error('editIsActive')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Branch Description
                        </label>
                        <textarea wire:model.defer="editDescription" rows="3"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                        @error('editDescription')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 border rounded-lg dark:border-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Update Branch
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Modal Overlay -->
        <div x-show="openEditModal" @click="$wire.cancelEdit()"
             x-transition.opacity class="fixed inset-0 bg-zinc-800/40 z-40"></div>

        <!-- Create Form -->
        <livewire:super-admin.components.create-branch />
        <livewire:super-admin.components.create-employee />

     </div>
</div>
