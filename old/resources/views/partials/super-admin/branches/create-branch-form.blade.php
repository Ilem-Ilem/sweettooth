<!-- Slide-in Create Form (half screen from right) -->
<div x-show="openForm" x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 h-full w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50">

    <h3 class="text-lg font-semibold mb-4 text-zinc-800 dark:text-zinc-100">Create New Branch</h3>
    <form wire:submit.prevent="createBranch" class="space-y-4 max-h-[80vh] overflow-y-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Branch Name -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Name <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="branch.name" required placeholder="Enter branch name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Branch Code -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Code
                </label>
                <input type="text" wire:model.defer="branch.code" required
                    placeholder="Unique branch code (e.g., BR001)"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Full Address <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="branch.location" required placeholder="Street, building, area"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Country -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Country <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="branch.country" required placeholder="Enter country name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- State -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    State / Region <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="branch.state" required placeholder="Enter state or region"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    City <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="branch.city" required placeholder="Enter city name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Postal Code -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Postal Code
                </label>
                <input type="text" wire:model.defer="branch.postal_code" required
                    placeholder="Enter postal / ZIP code"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Timezone -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Timezone
                </label>
                <input type="text" wire:model.defer="branch.timezone" required placeholder="e.g., Africa/Lagos"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Contact Phone
                </label>
                <input type="text" wire:model.defer="branch.phone" required placeholder="Enter phone number"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Contact Email
                </label>
                <input type="email" wire:model.defer="branch.email" required placeholder="Enter email address"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <!-- Manager -->
            <div class="col-span-1 md:col-span-2 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Branch Manager <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="branch.manager_user_id" required
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        <option value="">Select Manager</option>

                    </select>
                </div>
                <button type="button" @click="openManagerForm = true"
                    class="bg-green-600 text-white px-3 py-2 rounded-lg shadow hover:bg-green-700 transition">
                    +
                </button>
            </div>

            <!-- Is Active -->
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="branch.is_active" value="1" checked class="rounded">
                <label class="text-sm text-zinc-700 dark:text-zinc-300">Mark branch as active</label>
            </div>

            <!-- Description -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Description
                </label>
                <textarea wire:model.defer="branch.description" rows="3" placeholder="Add details or notes about the branch"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <button type="button" @click="openForm = false"
                class="px-4 py-2 border rounded-lg dark:border-zinc-700 dark:text-zinc-200">Cancel
            </button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save
            </button>
        </div>
    </form>

</div>

<!-- Overlay -->
<div x-show="openForm" @click="openForm = false" x-transition.opacity class="fixed inset-0 bg-zinc-800/40 z-40">
</div>

<!-- Slide-in Add Manager Form -->
<div x-show="openManagerForm" x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 h-full w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50">

    <h3 class="text-lg font-semibold mb-4 text-zinc-800 dark:text-zinc-100">Create New Manager</h3>
    <form wire:submit.prevent="createManager" class="space-y-4 max-h-[80vh] overflow-y-auto p-4">
        <div class="grid grid-cols-1 gap-4">

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Manager Name <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="manager.name" required placeholder="Enter manager name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" wire:model.defer="manager.email" required placeholder="Enter manager email"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Phone
                </label>
                <input type="text" wire:model.defer="manager.phone" placeholder="Enter phone number"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" wire:model.defer="manager.password" required placeholder="Enter password"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
            </div>

        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <button type="button" @click="openManagerForm = false"
                class="px-4 py-2 border rounded-lg dark:border-zinc-700 dark:text-zinc-200">Cancel
            </button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save Manager
            </button>
        </div>
    </form>
</div>

<!-- Overlay -->
<div x-show="openManagerForm" @click="openManagerForm = false" x-transition.opacity
    class="fixed inset-0 bg-zinc-800/40 z-40">
</div>
