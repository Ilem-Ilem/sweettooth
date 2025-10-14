<div @open-create-employee-modal.window="$wire.openModal()" @open-edit-employee-modal.window="$wire.openForEdit($event.detail.id)">
    <!-- Create/Edit Modal -->
    <div x-data="{ open: @entangle('isOpen') }"
         x-show="open"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-full md:w-2/3 lg:w-1/2 bg-white dark:bg-zinc-900 shadow-xl p-6 z-50 overflow-y-auto"
         style="display: none;">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">
                @if($isEditing)
                    Edit Employee
                @else
                    Create New Employee
                @endif
            </h3>
            <button wire:click="closeModal" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if($isEditing)
            <form wire:submit.prevent="updateEmployee" class="space-y-6">
        @else
            <form wire:submit.prevent="createEmployee" class="space-y-6">
        @endif

            <!-- Personal Information Section -->
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Personal Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Profile Photo -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                            Profile Photo
                        </label>
                        <input type="file" wire:model="profile_photo" accept="image/*"
                            class="text-sm text-zinc-500 dark:text-zinc-400
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100">
                        @error('profile_photo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="profile_photo" class="text-sm text-blue-600 mt-1">Uploading...</div>
                    </div>

                    <!-- Employee Number -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Employee Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="employee_number" required readonly
                            class="w-full px-3 py-2 border rounded-lg bg-zinc-50 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('employee_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.defer="name" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model.defer="email" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Phone Number
                        </label>
                        <input type="text" wire:model.defer="phone"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Date of Birth
                        </label>
                        <input type="date" wire:model.defer="date_of_birth"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Gender
                        </label>
                        <select wire:model.defer="gender"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nationality -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Nationality
                        </label>
                        <input type="text" wire:model.defer="nationality"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('nationality')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Address
                        </label>
                        <textarea wire:model.defer="address" rows="2"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                        @error('address')
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
                        <input type="text" wire:model.defer="emergency_contact_name"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('emergency_contact_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Contact Phone
                        </label>
                        <input type="text" wire:model.defer="emergency_contact_phone"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('emergency_contact_phone')
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
                        <input type="text" wire:model.defer="position" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('position')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Branch -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Branch
                        </label>
                        <select wire:model.defer="branch_id"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Department
                        </label>
                        <select wire:model.defer="department_id"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hire Date -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Hire Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.defer="hire_date" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('hire_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.defer="status" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_probation">On Probation</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Shift Preference -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Shift Preference
                        </label>
                        <select wire:model.defer="shift_preference"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                            <option value="">Select Shift</option>
                            <option value="morning">Morning</option>
                            <option value="afternoon">Afternoon</option>
                            <option value="night">Night</option>
                            <option value="rotating">Rotating</option>
                            <option value="flexible">Flexible</option>
                        </select>
                        @error('shift_preference')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Probation End Date -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Probation End Date
                        </label>
                        <input type="date" wire:model.defer="probation_end_date"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('probation_end_date')
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
                        <input type="number" step="0.01" wire:model.defer="salary"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('salary')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hourly Rate -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Hourly Rate
                        </label>
                        <input type="number" step="0.01" wire:model.defer="hourly_rate"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('hourly_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Tax ID
                        </label>
                        <input type="text" wire:model.defer="tax_id"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('tax_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bank Account -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Bank Account
                        </label>
                        <input type="text" wire:model.defer="bank_account"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100">
                        @error('bank_account')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="pb-4">
                <h4 class="text-md font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Additional Information</h4>
                <div class="grid grid-cols-1 gap-4">

                    <!-- Allergies -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Allergies / Medical Notes
                        </label>
                        <textarea wire:model.defer="allergies" rows="3"
                            class="w-full px-3 py-2 border rounded-lg dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100"></textarea>
                        @error('allergies')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-2 pt-4 sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700">
                <button type="button" wire:click="closeModal"
                    class="px-4 py-2 border rounded-lg dark:border-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    Cancel
                </button>
                @if($isEditing)
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateEmployee">Update Employee</span>
                        <span wire:loading wire:target="updateEmployee">Updating...</span>
                    </button>
                @else
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createEmployee">Create Employee</span>
                        <span wire:loading wire:target="createEmployee">Creating...</span>
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Modal Overlay -->
    <div x-data="{ open: @entangle('isOpen') }"
         x-show="open"
         @click="$wire.closeModal()"
         x-transition.opacity
         class="fixed inset-0 bg-zinc-800/40 z-40"
         style="display: none;">
    </div>
</div>
