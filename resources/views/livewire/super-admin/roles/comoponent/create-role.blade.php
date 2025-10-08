<div>
    <div x-data="{ show: @entangle('showRoleModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
         @keydown.escape.window="show = false">
        <!-- Backdrop -->
        <div x-show="show"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50"
             @click="$wire.closeRoleModal()">
        </div>

        <!-- Slide-in Panel -->
        <div x-show="show"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-full md:w-1/2 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl flex flex-col">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $isEditing ? 'Edit Role' : 'Add New Role' }}</h2>
                <button wire:click="closeRoleModal" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable Form Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-700 scrollbar-track-transparent">
                <form wire:submit.prevent="saveRole" class="space-y-6">
                    <!-- Role Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Role Name *</label>
                        <input type="text" wire:model="roleName" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500" placeholder="Enter role name" required>
                        @error('roleName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Guard Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Guard Name *</label>
                        <select wire:model="roleGuard" class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                        </select>
                        @error('roleGuard') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Permissions -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Assign Permissions</label>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="openCreatePermissionModal" class="text-sm px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New
                                </button>
                                <button type="button" wire:click="toggleAllPermissions" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline">
                                    {{ count($selectedPermissions) === count($allPermissions) ? 'Deselect All' : 'Select All' }}
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                            @foreach($allPermissions as $permission)
                                <label class="flex items-center p-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->id }}" class="w-4 h-4 text-blue-600 border-zinc-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                <button wire:click="closeRoleModal" class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="saveRole" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    {{ $isEditing ? 'Update Role' : 'Create Role' }}
                </button>
            </div>
        </div>
    </div>
</div>
