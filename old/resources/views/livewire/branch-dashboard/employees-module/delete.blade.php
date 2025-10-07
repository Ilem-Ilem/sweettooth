<div x-data="{
        bulkAction: '',
        selectAll: false,
        selected: @entangle('selected'),
        rowIds: @json($employees->pluck('id')),
        showAlert: false,
        alertMessage: '',
        alertType: 'success'
     }"
     x-cloak
     @employee-deleted.window="showAlert = true; alertMessage = $event.detail; alertType = 'warning'"
     @bulk-action-completed.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'; selected = []; selectAll = false"
     x-effect="selectAll = rowIds.length > 0 && rowIds.every(id => selected.includes(id))"
>
    <!-- Alert Component -->
    <div x-show="showAlert"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div class="rounded-lg shadow-lg p-4"
             :class="{
                'bg-green-50 border border-green-200 text-green-800': alertType === 'success',
                'bg-yellow-50 border border-yellow-200 text-yellow-800': alertType === 'warning',
                'bg-blue-50 border border-blue-200 text-blue-800': alertType === 'info',
                'bg-red-50 border border-red-200 text-red-800': alertType === 'error'
             }">
            <div class="flex items-center justify-between">
                <p x-text="alertMessage" class="text-sm font-medium"></p>
                <button @click="showAlert = false" class="ml-4 text-sm font-bold">×</button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">Deleted Employees</h2>
        </div>

        <!-- Filters and Actions -->
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <!-- Search -->
            <div class="flex items-center border rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 dark:border-zinc-700 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                </svg>
                <input type="text" placeholder="Search employees..."
                    class="outline-none border-none focus:ring-0 text-sm w-48 bg-transparent dark:text-zinc-100"
                    wire:model.live="search">
            </div>

            <div class="flex items-center gap-2">
                <!-- Branch Filter -->
                <select wire:model.live="filterBranch" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>

                <!-- Department Filter -->
                <select wire:model.live="filterDepartment" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
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
                <span class="text-sm text-blue-600 dark:text-blue-400">(<span x-text="selected.length"></span> rows selected)</span>
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
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Employee</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Position</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Branch</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Department</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Contact</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Hire Date</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b dark:border-zinc-700">
                            <td class="px-4 py-3">
                                <input type="checkbox" value="{{ $employee->id }}" x-model="selected" class="rounded">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($employee->profile_photo)
                                        <img src="{{ Storage::url($employee->profile_photo) }}"
                                             alt="{{ $employee->name }}"
                                             class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                            <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                                {{ substr($employee->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $employee->name }}</div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $employee->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $employee->position }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $employee->branch->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $employee->department->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="text-zinc-900 dark:text-zinc-100">{{ $employee->email }}</div>
                                @if($employee->phone)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $employee->phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="restore('{{ $employee->id }}')"
                                            class="text-green-600 dark:text-green-400 hover:text-green-800 p-1 rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h7V3m0 0l-9 9 9 9M13 21h8" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete('{{ $employee->id }}')"
                                            onclick="return confirm('Permanently delete this employee?')"
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
                            <td colspan="8" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No deleted employees</p>
                                    <p class="text-sm">Deleted employees will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</div>
