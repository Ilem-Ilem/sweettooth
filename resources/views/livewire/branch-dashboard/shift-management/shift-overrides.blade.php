<div class="p-3 space-y-3">
    <x-breadcrumb
        title="Shift Overrides"
        :items="[
            ['label' => 'Dashboard', 'url' => route('branch-dashboard.index')],
            ['label' => 'Shift Management', 'url' => route('branch-dashboard.shift-management.index', ['b_id' => $b_id])],
            ['label' => 'Overrides']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Shift Overrides</h1>
            <p class="text-zinc-600 dark:text-zinc-400">Manage shift exceptions and overrides</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Branch: <strong class="text-zinc-900 dark:text-white">{{ $branch->name ?? 'All' }}</strong></p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Search</label>
                <input type="text" wire:model.live="search" placeholder="Search employees..." class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                <select wire:model.live="selectedStatus" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Shifts</option>
                    <option value="with_override">With Override</option>
                    <option value="without_override">Without Override</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date From</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Date To</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Override Form -->
    @if($showOverrideForm)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Apply Shift Override</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Shift *</label>
                    <select wire:model="shift_id" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->employee->name }} - {{ $shift->shift_date->format('M j, Y') }} ({{ ucfirst($shift->shift_type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('shift_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Shift Type *</label>
                    <select wire:model="new_shift_type" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Shift Type</option>
                        @foreach($shiftConfigurations as $config)
                            <option value="{{ $config->shift_type }}">{{ $config->name }}</option>
                        @endforeach
                    </select>
                    @error('new_shift_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Clock In Time *</label>
                    <input type="time" wire:model="new_clock_in" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    @error('new_clock_in') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Clock Out Time *</label>
                    <input type="time" wire:model="new_clock_out" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    @error('new_clock_out') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Reason for Override *</label>
                    <textarea wire:model="override_reason" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Enter reason for shift override"></textarea>
                    @error('override_reason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button wire:click="storeOverride" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200 font-medium">Apply Override</button>
                <button wire:click="cancel" class="px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg transition duration-200 font-medium">Cancel</button>
            </div>
        </div>
    @endif

    <!-- Edit Override Form -->
    @if($showEditForm)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Edit Shift Override</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Employee</label>
                    <input type="text" value="{{ $shifts->firstWhere('id', $shift_id)?->employee->name ?? '' }}" disabled class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Shift Date</label>
                    <input type="text" value="{{ $shift_date }}" disabled class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Original Shift Type</label>
                    <input type="text" value="{{ $original_shift_type }}" disabled class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Shift Type *</label>
                    <select wire:model="new_shift_type" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Shift Type</option>
                        @foreach($shiftConfigurations as $config)
                            <option value="{{ $config->shift_type }}">{{ $config->name }}</option>
                        @endforeach
                    </select>
                    @error('new_shift_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Clock In Time *</label>
                    <input type="time" wire:model="new_clock_in" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    @error('new_clock_in') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">New Clock Out Time *</label>
                    <input type="time" wire:model="new_clock_out" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    @error('new_clock_out') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                    <select wire:model="override_status" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Reason for Override *</label>
                    <textarea wire:model="override_reason" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Enter reason for shift override"></textarea>
                    @error('override_reason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button wire:click="updateOverride" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200 font-medium">Update Override</button>
                <button wire:click="cancel" class="px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg transition duration-200 font-medium">Cancel</button>
            </div>
        </div>
    @endif

    <!-- Controls -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Shift Overrides</h2>
        </div>
        <button wire:click="createOverride" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Apply Override
        </button>
    </div>

    <!-- Overrides Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Original Shift</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">New Shift</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Times</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-750">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $shift->employee->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $shift->employee->employee_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-white">{{ $shift->shift_date->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-white">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 rounded text-xs font-semibold">
                                    {{ ucfirst($shift->shift_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-white">
                                @if(isset($shift->metadata['original_shift_type']))
                                    <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 rounded text-xs font-semibold">
                                        {{ ucfirst($shift->metadata['original_shift_type']) }}
                                    </span>
                                @else
                                    <span class="text-zinc-500 dark:text-zinc-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-zinc-900 dark:text-white">
                                @if($shift->clock_in)
                                    <div>{{ $shift->clock_in->format('H:i') }} - {{ $shift->clock_out ? $shift->clock_out->format('H:i') : 'N/A' }}</div>
                                @else
                                    <div>N/A</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if(isset($shift->metadata['override_reason']))
                                    @if(isset($shift->metadata['override_status']))
                                        @if($shift->metadata['override_status'] === 'approved')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Approved</span>
                                        @elseif($shift->metadata['override_status'] === 'rejected')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">Rejected</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">Pending</span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">Applied</span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Normal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button wire:click="edit({{ $shift->id }})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 font-medium text-sm">Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-zinc-500 dark:text-zinc-400">
                                <p class="text-lg font-medium text-zinc-900 dark:text-white">No shift overrides found</p>
                                <p class="text-sm">Click "Apply Override" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>