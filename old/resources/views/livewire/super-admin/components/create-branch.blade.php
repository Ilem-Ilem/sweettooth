<div @close-form.window="openForm = false">
<!-- Slide-in Create Form (half screen from right) -->
<div x-show="openForm" x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 h-full md:w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50">

    <h3 class="text-lg font-semibold mb-4 text-zinc-800 dark:text-zinc-100">Create New Branch</h3>
    <form wire:submit.prevent="createBranch" class="space-y-4 max-h-[80vh] overflow-y-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Branch Name -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Name <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="name" required placeholder="Enter branch name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Branch Code -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Code
                </label>
                <input type="text" wire:model.defer="code" required placeholder="Unique branch code (e.g., BR001)"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Full Address <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="location" required placeholder="Street, building, area"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Country <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="country" required placeholder="Enter country name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('country')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- State -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    State / Region <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="state" required placeholder="Enter state or region"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('state')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    City <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="city" required placeholder="Enter city name"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('city')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Postal Code -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Postal Code
                </label>
                <input type="text" wire:model.defer="postal_code" required placeholder="Enter postal / ZIP code"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('postal_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Timezone -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Timezone
                </label>
                <input type="text" wire:model.defer="timezone" required placeholder="e.g., Africa/Lagos"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('timezone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Contact Phone
                </label>
                <input type="text" wire:model.defer="phone" required placeholder="Enter phone number"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Contact Email
                </label>
                <input type="email" wire:model.defer="email" required placeholder="Enter email address"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Manager -->
            <div class="col-span-1 md:col-span-2 flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Branch Manager <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.defer="manager_user_id" required
                        class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        <option value="">Select Manager</option>
                        @foreach ($employees as $Mg)
                            <option value="{{ $Mg->id }}">{{ $Mg->name }}</option>
                        @endforeach
                    </select>
                    @error('manager_user_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="button" @click="openManagerForm = true"
                    class="bg-green-600 text-white px-3 py-2 rounded-lg shadow hover:bg-green-700 transition">
                    +
                </button>
            </div>

            <!-- Is Active -->
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="is_active" value="1" checked class="rounded">
                <label class="text-sm text-zinc-700 dark:text-zinc-300">Mark branch as active</label>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Branch Description
                </label>
                <textarea wire:model.defer="description" rows="3" placeholder="Add details or notes about the branch"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
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


</div>
