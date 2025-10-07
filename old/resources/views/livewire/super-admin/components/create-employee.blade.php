<div>
    <!-- Slide-in Add Manager Form -->
    <div x-show="openManagerForm" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 h-full w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50">
        <div class="max-w-4xl mx-auto bg-white dark:bg-zinc-900 p-6 rounded shadow h-[90vh] overflow-y-auto">
            <h2 class="text-3xl font-bold mb-6 text-zinc-800 dark:text-zinc-100">📝 Create New Employee</h2>

            <form wire:submit.prevent="createEmployee" class="space-y-6">

                <!-- Group 1: Basic Info -->
                <div>
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" placeholder="Enter full name" required
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email" placeholder="Enter email address" required
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">Phone</label>
                        <input type="text" wire:model="phone" placeholder="Enter phone number"
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            
                <!-- Group 2: Employment Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="position" placeholder="Enter job title" required
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">
                            Hire Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="hire_date" required
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('hire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            
                <!-- Group 3: Personal Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">Date of Birth</label>
                        <input type="date" wire:model="date_of_birth"
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('date_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">Gender</label>
                        <select wire:model="gender"
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">Nationality</label>
                        <input type="text" wire:model="nationality" placeholder="Enter nationality"
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('nationality') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="mb-4">
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300">Address</label>
                        <textarea wire:model="address" placeholder="Enter address"
                            class="mt-2 w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            
                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        Create Employee
                    </button>
                </div>
            
            </form>
            
        </div>


    </div>

    <!-- Overlay -->
    <div x-show="openManagerForm" @click="openManagerForm = false" x-transition.opacity
        class="fixed inset-0 bg-zinc-800/40 z-40">
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.on("Employee-created", () => {
                // fire an Alpine event
                window.dispatchEvent(new CustomEvent("close-employee-form"));
            });
        });
    </script>
    
</div>
