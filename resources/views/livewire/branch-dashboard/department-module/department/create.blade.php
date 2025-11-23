<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
            {{ $isEditing ? 'Edit Department' : 'Add New Department' }}</h2>

    </div>

    <!-- Scrollable Form Content -->
    <div
        class="flex-1 overflow-y-auto px-6 py-4 scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-700 scrollbar-track-transparent">
        <form wire:submit.prevent="saveDepartment" class="space-y-6">
            <!-- Department Name -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Department Name
                    *</label>
                <input type="text" wire:model="name"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter department name" required>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Category Selection -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Category
                    *</label>
                <x-select.styled wire:model="category_id" :options="$this->getDepartmentCategories()
                    ->map(fn($cat) => ['label' => $cat->name, 'value' => $cat->id])
                    ->toArray()" select="label:label|value:value"
                    placeholder="Select Category" searchable required />
                @error('category_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            @if(is_super_admin())
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Department
                    *</label>
                <x-select.styled wire:model="branch_id" :options="$this->getBranches()
                    ->map(fn($branch) => ['label' => $branch->name, 'value' => $branch->id])
                    ->toArray()" select="label:label|value:value"
                    placeholder="Select Department" searchable required />
                @error('category_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            @endif
            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                <textarea wire:model="description" rows="4"
                    class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                    placeholder="Department description (optional)"></textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>


            <button wire:click="saveDepartment"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <span wire:loading.remove wire:loading.target="saveDepartment">
                    {{ $isEditing ? 'Update Department' : 'Create Department' }}
                </span>
                <span wire:loading wire:loading.target="saveDepartment">
                    {{ $isEditing ? 'Updating' : 'Creating' }}
                </span>
                
            </button>
        </form>

    </div>
