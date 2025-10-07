<div x-data="{
        openForm: false,
        openEditModal: false,
        bulkAction: '',
        selectAll: false,
        selected: [],
        rowIds: @json($employees->pluck('id')),
        showAlert: false,
        alertMessage: '',
        alertType: 'success'
     }"
     x-cloak
     @open-edit-modal.window="openEditModal = true"
     @close-edit-modal.window="openEditModal = false"
     @employee-created.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'"
     @employee-updated.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'"
     @employee-deleted.window="showAlert = true; alertMessage = $event.detail; alertType = 'warning'"
     @status-updated.window="showAlert = true; alertMessage = $event.detail; alertType = 'info'"
     @bulk-action-completed.window="showAlert = true; alertMessage = $event.detail; alertType = 'success'; selected = []; selectAll = false"
     x-effect="rowIds = JSON.parse($refs.rowIdsHolder?.dataset.rowIds || '[]'); selectAll = rowIds.length > 0 && rowIds.every(id => selected.includes(id)); selected = selected.filter(id => rowIds.includes(id))"
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
            <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">Employee Management</h2>
            <button
                @click="$dispatch('open-create-employee-modal')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Employee
            </button>
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

            <div class="flex flex-wrap items-center gap-2">
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

                <!-- Status Filter -->
                <select wire:model.live="filterStatus" class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="on_probation">On Probation</option>
                    <option value="on_leave">On Leave</option>
                    <option value="terminated">Terminated</option>
                </select>

                <!-- Position Filter -->
                <input type="text" wire:model.live.debounce.300ms="filterPosition" placeholder="Position"
                       class="px-3 py-2 border rounded-lg shadow-sm text-sm bg-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">

                <!-- Hire Date Range -->
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


        <!-- Hidden current page IDs for Alpine to consume -->
        <span x-ref="rowIdsHolder" data-row-ids='@json($employees->pluck("id"))' class="hidden"></span>

        <!-- Table -->
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 shadow rounded-lg">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-zinc-100 dark:bg-zinc-800 border-b dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox"
                                   x-model="selectAll"
                                   x-bind:indeterminate="selected.some(id => rowIds.includes(id)) && !selectAll"
                                   @change="selected = $event.target.checked ? [...rowIds] : selected.filter(id => !rowIds.includes(id))"
                                   class="rounded">
                        </th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Employee</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Position</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Branch</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Department</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Contact</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Hire Date</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b dark:border-zinc-700">
                            <td class="px-4 py-3">
<input type="checkbox" value="{{ $employee->id }}" x-model="selected" class="rounded" @click.stop>
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
<button wire:click="toggleStatus('{{ $employee->id }}')" class="focus:outline-none" @click.stop>
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                            'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
                                            'on_probation' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                            'on_leave' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                            'terminated' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded cursor-pointer hover:opacity-80 {{ $statusColors[$employee->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $employee->status)) }}
                                    </span>
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="$dispatch('open-edit-employee-modal', { id: '{{ $employee->id }}' })"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 p-1 rounded" @click.stop>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
<button wire:click="delete('{{ $employee->id }}')"
                                            onclick="return confirm('Are you sure you want to delete this employee?')"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 p-1 rounded" @click.stop>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-lg font-medium mb-2">No employees found</p>
                                    <p class="text-sm">Get started by adding your first employee</p>
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

        <!-- Edit Modal -->
        <div x-show="openEditModal"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed top-0 right-0 h-full w-full md:w-2/3 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50 overflow-y-auto">

            <h3 class="text-lg font-semibold mb-4 text-zinc-800 dark:text-zinc-100">Edit Employee</h3>

            <form wire:submit.prevent="updateEmployee" class="space-y-6">

                <!-- Personal Information Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Profile Photo -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                Profile Photo
                            </label>
                            <div class="flex items-center gap-4">
                                @if($editProfilePhoto)
                                    <img src="{{ Storage::url($editProfilePhoto) }}"
                                         alt="Current photo"
                                         class="w-20 h-20 rounded-full object-cover">
                                @endif
                                <input type="file" wire:model="newProfilePhoto" accept="image/*"
                                    class="text-sm text-zinc-500 dark:text-zinc-400
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100">
                            </div>
                            @error('newProfilePhoto')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee Number -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Employee Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.defer="editEmployeeNumber" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editEmployeeNumber')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.defer="editName" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editName')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" wire:model.defer="editEmail" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editEmail')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Phone Number
                            </label>
                            <input type="text" wire:model.defer="editPhone"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editPhone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Date of Birth
                            </label>
                            <input type="date" wire:model.defer="editDateOfBirth"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editDateOfBirth')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Gender
                            </label>
                            <select wire:model.defer="editGender"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                            @error('editGender')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Nationality
                            </label>
                            <input type="text" wire:model.defer="editNationality"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editNationality')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Address
                            </label>
                            <textarea wire:model.defer="editAddress" rows="2"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                            @error('editAddress')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Emergency Contact</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Emergency Contact Name -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Contact Name
                            </label>
                            <input type="text" wire:model.defer="editEmergencyContactName"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editEmergencyContactName')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Phone -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Contact Phone
                            </label>
                            <input type="text" wire:model.defer="editEmergencyContactPhone"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editEmergencyContactPhone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employment Details Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Employment Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.defer="editPosition" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editPosition')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Branch -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Branch
                            </label>
                            <select wire:model.defer="editBranchId"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('editBranchId')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Department
                            </label>
                            <select wire:model.defer="editDepartmentId"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('editDepartmentId')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hire Date -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Hire Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model.defer="editHireDate" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editHireDate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.defer="editStatus" required
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_probation">On Probation</option>
                                <option value="on_leave">On Leave</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            @error('editStatus')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shift Preference -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Shift Preference
                            </label>
                            <select wire:model.defer="editShiftPreference"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                                <option value="">Select Shift</option>
                                <option value="morning">Morning</option>
                                <option value="afternoon">Afternoon</option>
                                <option value="night">Night</option>
                                <option value="rotating">Rotating</option>
                                <option value="flexible">Flexible</option>
                            </select>
                            @error('editShiftPreference')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Probation End Date -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Probation End Date
                            </label>
                            <input type="date" wire:model.defer="editProbationEndDate"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editProbationEndDate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Termination Date -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Termination Date
                            </label>
                            <input type="date" wire:model.defer="editTerminationDate"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editTerminationDate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Compensation & Financial Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Compensation & Financial</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Salary -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Salary
                            </label>
                            <input type="number" step="0.01" wire:model.defer="editSalary"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editSalary')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hourly Rate -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Hourly Rate
                            </label>
                            <input type="number" step="0.01" wire:model.defer="editHourlyRate"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editHourlyRate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax ID -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Tax ID
                            </label>
                            <input type="text" wire:model.defer="editTaxId"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editTaxId')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bank Account -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Bank Account
                            </label>
                            <input type="text" wire:model.defer="editBankAccount"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editBankAccount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Additional Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Last Performance Review Date -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Last Performance Review
                            </label>
                            <input type="date" wire:model.defer="editLastPerformanceReviewDate"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editLastPerformanceReviewDate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Performance Rating -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Performance Rating (0-5)
                            </label>
                            <input type="number" step="0.1" min="0" max="5" wire:model.defer="editPerformanceRating"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            @error('editPerformanceRating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Allergies -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Allergies / Medical Notes
                            </label>
                            <textarea wire:model.defer="editAllergies" rows="3"
                                class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                            @error('editAllergies')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-2 pt-4 sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 border rounded-lg dark:border-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Modal Overlay -->
        <div x-show="openEditModal" @click="$wire.cancelEdit()"
             x-transition.opacity class="fixed inset-0 bg-zinc-800/40 z-40"></div>

        <!-- Create Form Component (to be created separately) -->
        <livewire:super-admin.components.create-employee-full />

    </div>
</div>
