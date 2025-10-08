<div>
    <div x-data="{ show: @entangle('showCreatePermissionModal') }" x-show="show" x-cloak class="fixed inset-0 z-[60] overflow-hidden"
        @keydown.escape.window="show = false">
        <!-- Backdrop -->
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-70"
            @click="$wire.closeCreatePermissionModal()">
        </div>

        <!-- Slide-in Panel -->
        <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full md:w-1/2 bg-white dark:bg-zinc-900 shadow-2xl flex flex-col border-l-4 border-green-500">

            <!-- Header -->
            <div
                class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between bg-green-50 dark:bg-green-900/20">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Create New Permission</h2>
                <button wire:click="closeCreatePermissionModal"
                    class="p-2 hover:bg-green-100 dark:hover:bg-green-800 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form wire:submit.prevent="createPermission" class="space-y-4">
                    <!-- Permission Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Permission Name
                            *</label>
                        <input type="text" wire:model="permissionName"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. create-users" required>
                        @error('permissionName')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Use lowercase with hyphens (e.g.,
                            view-reports, edit-posts)</p>
                    </div>

                    <!-- Guard Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Guard Name
                            *</label>
                        <select wire:model="permissionGuard"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-green-500">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                        </select>
                        @error('permissionGuard')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-blue-800 dark:text-blue-200">This permission will be automatically
                                added to the role you're currently creating/editing.</p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div
                class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-end space-x-3 bg-zinc-50 dark:bg-zinc-800">
                <button wire:click="closeCreatePermissionModal"
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button wire:click="createPermission"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Permission
                </button>
            </div>
        </div>
    </div>
</div>
