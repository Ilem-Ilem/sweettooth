<div x-data>
    <div class="min-h-full">
        <!-- Admin View -->
        @if($userType === 'admin')
            <div class="p-6 max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">Department Management</h1>
                        <p class="text-zinc-600 dark:text-zinc-400 mt-2">Manage departments across all branches</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Add Department Button -->
                        <button wire:click="openAddDepartment"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Department
                        </button>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session()->has('message'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Branch Selector -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 mb-6 transition-colors">
                    <div class="items-center">
                        <label for="branch-select" class="text-zinc-700 dark:text-zinc-300 font-medium mr-4">Select
                            Branch:</label>
                        <select wire:model.live="selectedBranch" id="branch-select"
                            class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 mb-6 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <!-- Left Side: Filters -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Search departments..."
                                    class="pl-10 pr-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-zinc-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <!-- Status Filter -->
                            <div class="relative">
                                <button wire:click="toggleDropdown('status')" type="button"
                                    class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-600 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Status
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Status Dropdown -->
                                @if($openDropdown === 'status')
                                    <div
                                        class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg z-10 py-2 transition-all duration-200">
                                        <div
                                            class="px-4 py-2 text-sm font-medium text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-600">
                                            Filter by Status
                                        </div>
                                        <div class="p-2 space-y-1">
                                            <label
                                                class="flex items-center px-3 py-2 rounded hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer">
                                                <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500"
                                                    wire:model.live="statusFilters" value="active">
                                                <span class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">Active</span>
                                            </label>
                                            <label
                                                class="flex items-center px-3 py-2 rounded hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer">
                                                <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500"
                                                    wire:model.live="statusFilters" value="inactive">
                                                <span
                                                    class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">Inactive</span>
                                            </label>
                                            <label
                                                class="flex items-center px-3 py-2 rounded hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer">
                                                <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500"
                                                    wire:model.live="statusFilters" value="pending">
                                                <span
                                                    class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">Pending</span>
                                            </label>
                                        </div>
                                        <div
                                            class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-600 flex justify-between">
                                            <button wire:click="clearFilter('status')" type="button"
                                                class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300">
                                                Clear
                                            </button>
                                            <button wire:click="applyFilters" type="button"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Employees Filter -->
                            <div class="relative">
                                <button wire:click="toggleDropdown('employees')" type="button"
                                    class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-600 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    Employees
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Employees Dropdown -->
                                @if($openDropdown === 'employees')
                                    <div
                                        class="absolute top-full left-0 mt-2 w-56 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg z-10 py-2 transition-all duration-200">
                                        <div
                                            class="px-4 py-2 text-sm font-medium text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-600">
                                            Filter by Employee Count
                                        </div>
                                        <div class="p-4 space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Range</label>
                                                <div class="flex items-center space-x-3">
                                                    <input type="number" wire:model="employeesMin" placeholder="Min"
                                                        class="w-20 px-3 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-sm bg-zinc-50 dark:bg-zinc-600 text-zinc-900 dark:text-zinc-100">
                                                    <span class="text-zinc-400">to</span>
                                                    <input type="number" wire:model="employeesMax" placeholder="Max"
                                                        class="w-20 px-3 py-1 border border-zinc-300 dark:border-zinc-600 rounded text-sm bg-zinc-50 dark:bg-zinc-600 text-zinc-900 dark:text-zinc-100">
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-600 flex justify-between">
                                            <button wire:click="clearFilter('employees')" type="button"
                                                class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300">
                                                Clear
                                            </button>
                                            <button wire:click="applyFilters" type="button"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Date Filter -->
                            <div class="relative">
                                <button wire:click="toggleDropdown('date')" type="button"
                                    class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-600 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Date Created
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Date Dropdown -->
                                @if($openDropdown === 'date')
                                    <div
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg z-10 py-2 transition-all duration-200">
                                        <div
                                            class="px-4 py-2 text-sm font-medium text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-600">
                                            Filter by Date
                                        </div>
                                        <div class="p-4 space-y-3">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">From</label>
                                                <input type="date" wire:model="dateFrom"
                                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded text-sm bg-zinc-50 dark:bg-zinc-600 text-zinc-900 dark:text-zinc-100">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">To</label>
                                                <input type="date" wire:model="dateTo"
                                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded text-sm bg-zinc-50 dark:bg-zinc-600 text-zinc-900 dark:text-zinc-100">
                                            </div>
                                        </div>
                                        <div
                                            class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-600 flex justify-between">
                                            <button wire:click="clearFilter('date')" type="button"
                                                class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300">
                                                Clear
                                            </button>
                                            <button wire:click="applyFilters" type="button"
                                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Clear All Filters -->
                            @if($this->hasActiveFilters())
                                <button wire:click="clearAllFilters" type="button"
                                    class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium transition-colors">
                                    Clear All
                                </button>
                            @endif
                        </div>

                        <!-- Right Side: Export Buttons and Bulk Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Bulk Actions (visible when items selected) -->
                            @if(count($selectedDepartments) > 0)
                                <div class="flex items-center gap-2 mr-2 border-r border-zinc-300 dark:border-zinc-600 pr-2">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ count($selectedDepartments) }} selected
                                    </span>
                                    <select wire:model="bulkAction"
                                        class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate</option>
                                        <option value="deactivate">Deactivate</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                    <button wire:click="executeBulkAction" type="button"
                                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium text-sm transition-colors">
                                        Apply
                                    </button>
                                </div>
                            @endif

                            <!-- Export Dropdown -->
                            <div class="relative">
                                <button wire:click="toggleDropdown('export')" type="button"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Export
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <!-- Export Dropdown Menu -->
                                @if($openDropdown === 'export')
                                    <div
                                        class="absolute top-full right-0 mt-2 w-48 bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-lg shadow-lg z-10 py-2 transition-all duration-200">
                                        <button wire:click="exportData('csv')" type="button"
                                            class="flex items-center w-full px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-green-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Export as CSV
                                        </button>
                                        <button wire:click="exportData('excel')" type="button"
                                            class="flex items-center w-full px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-green-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Export as Excel
                                        </button>
                                        <button wire:click="exportData('pdf')" type="button"
                                            class="flex items-center w-full px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-red-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Export as PDF
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Print Button -->
                            <button wire:click="printData" type="button"
                                class="flex items-center gap-2 px-4 py-2 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if($this->hasActiveFilters())
                        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-600">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">Active filters:</span>

                                @foreach($statusFilters as $status)
                                    <div
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-sm">
                                        <span>{{ ucfirst($status) }}</span>
                                        <button wire:click="removeStatusFilter('{{ $status }}')" type="button"
                                            class="hover:text-blue-600 dark:hover:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach

                                @if($employeesMin || $employeesMax)
                                    <div
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm">
                                        <span>
                                            Employees:
                                            @if($employeesMin && $employeesMax)
                                                {{ $employeesMin }}-{{ $employeesMax }}
                                            @elseif($employeesMin)
                                                ≥ {{ $employeesMin }}
                                            @else
                                                ≤ {{ $employeesMax }}
                                            @endif
                                        </span>
                                        <button wire:click="clearFilter('employees')" type="button"
                                            class="hover:text-green-600 dark:hover:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif

                                @if($dateFrom || $dateTo)
                                    <div
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded-full text-sm">
                                        <span>
                                            Date:
                                            @if($dateFrom && $dateTo)
                                                {{ $dateFrom }} to {{ $dateTo }}
                                            @elseif($dateFrom)
                                                From {{ $dateFrom }}
                                            @else
                                                Until {{ $dateTo }}
                                            @endif
                                        </span>
                                        <button wire:click="clearFilter('date')" type="button"
                                            class="hover:text-purple-600 dark:hover:text-purple-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Department List -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm overflow-hidden transition-colors">
                    <div class="overflow-x-auto">
                        <!-- Hidden current page IDs for Alpine to compute indeterminate state -->
                        <span x-ref="deptRowIdsHolder" data-row-ids='@json($departments->pluck("id"))' class="hidden"></span>
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" wire:model.live="selectAll"
                                            x-bind:indeterminate="(() => { const ids = JSON.parse($refs.deptRowIdsHolder?.dataset.rowIds || '[]'); const sel = $wire.selectedDepartments || []; const c = sel.filter(id => ids.includes(id)).length; return c > 0 && c < ids.length; })()"
                                            class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Department</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Head</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Employees</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($departments as $dept)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" wire:model.live="selectedDepartments"
                                                value="{{ $dept->id }}"
                                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $dept->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $dept->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $dept->head?->name ?? 'Not Assigned' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $dept->employees_count ?? 0 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $dept->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                                                {{ $dept->status === 'inactive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                                                {{ $dept->status === 'pending' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}">
                                                {{ ucfirst($dept->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="editDepartment({{ $dept->id }})" type="button"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Edit</button>
                                            <button wire:click="deleteDepartment({{ $dept->id }})" type="button"
                                                wire:confirm="Are you sure you want to delete this department?"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-2">No departments found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Branch Head View -->
        @if($userType === 'branchHead')
            <div class="p-6 max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">Department Management</h1>
                        <p class="text-zinc-600 dark:text-zinc-400 mt-2">Manage departments for <span
                                class="font-medium">{{ $branches->firstWhere('id', $selectedBranch)?->name }}</span>
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Add Department Button -->
                        <button wire:click="openAddDepartment"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Department
                        </button>
                    </div>
                <!-- Success Message -->
                @if (session()->has('message'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Bulk actions and selection prompt (Branch Head view) -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        @if(count($selectedDepartments) > 0)
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ count($selectedDepartments) }} selected</span>
                                <select wire:model="bulkAction"
                                    class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm">
                                    <option value="">Bulk Actions</option>
                                    <option value="activate">Activate</option>
                                    <option value="deactivate">Deactivate</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button wire:click="executeBulkAction" type="button"
                                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium text-sm transition-colors">
                                    Apply
                                </button>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if(count($selectedDepartments) > 0)
                            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                                @if(!$selectAllPages && isset($totalResults) && $totalResults > count($selectedDepartments))
                                    <button wire:click="selectAllResults" type="button" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        Select all {{ $totalResults }} results
                                    </button>
                                @elseif($selectAllPages)
                                    <span>All {{ $totalResults }} results are selected.</span>
                                    <button wire:click="clearSelectAllPages" type="button" class="ml-2 text-blue-600 dark:text-blue-400 hover:underline">
                                        Clear selection
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Department List -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm overflow-hidden transition-colors">
                    <div class="overflow-x-auto">
                        <!-- Hidden current page IDs for Alpine to compute indeterminate state -->
                        <span x-ref="deptRowIdsHolder" data-row-ids='@json($departments->pluck("id"))' class="hidden"></span>
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" wire:model.live="selectAll"
                                            x-bind:indeterminate="(() => { const ids = JSON.parse($refs.deptRowIdsHolder?.dataset.rowIds || '[]'); const sel = $wire.selectedDepartments || []; const c = sel.filter(id => ids.includes(id)).length; return c > 0 && c < ids.length; })()"
                                            class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Department</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Head</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Employees</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($departments as $dept)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" wire:model.live="selectedDepartments"
                                                value="{{ $dept->id }}"
                                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $dept->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $dept->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $dept->head?->name ?? 'Not Assigned' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $dept->employees_count ?? 0 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $dept->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                                                {{ $dept->status === 'inactive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}">
                                                {{ ucfirst($dept->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="editDepartment({{ $dept->id }})" type="button"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Edit</button>
                                            <button wire:click="deleteDepartment({{ $dept->id }})" type="button"
                                                wire:confirm="Are you sure you want to delete this department?"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-2">No departments found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Department Slide-out Panel -->
        @if($showAddDepartment)
            <div
                class="fixed inset-y-0 right-0 w-full md:w-1/2 lg:w-1/2 bg-white dark:bg-zinc-800 shadow-xl z-50 overflow-y-auto transition-colors">
                <div class="p-6 h-full flex flex-col">
                    <!-- Header -->
                    <div
                        class="flex justify-between items-center mb-6 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $editingId ? 'Edit Department' : 'Add New Department' }}
                        </h2>
                        <button wire:click="closeAddDepartment" type="button"
                            class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Content -->
                    <div class="flex-1 overflow-y-auto">
                        <form wire:submit="saveDepartment" class="space-y-6">
                            <div>
                                <label for="dept-name"
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Department
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" id="dept-name" wire:model="name"
                                    class="w-full bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Enter department name">
                                @error('name')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="dept-description"
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                                <textarea id="dept-description" rows="3" wire:model="description"
                                    class="w-full bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Enter department description"></textarea>
                                @error('description')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="dept-head"
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Department
                                    Head</label>
                                <select id="dept-head" wire:model="headId"
                                    class="w-full bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Select Department Head</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('headId')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($userType === 'admin')
                                <div>
                                    <label for="dept-branch"
                                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Branch
                                        <span class="text-red-500">*</span></label>
                                    <select id="dept-branch" wire:model="branchId"
                                        class="w-full bg-zinc-50 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-zinc-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branchId')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                <div>
                                    <label
                                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Branch</label>
                                    <input type="text" disabled
                                        value="{{ $branches->firstWhere('id', $selectedBranch)?->name }}"
                                        class="w-full bg-zinc-100 dark:bg-zinc-600 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg px-4 py-2">
                                </div>
                            @endif

                            <div>
                                <label
                                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status
                                    <span class="text-red-500">*</span></label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" wire:model="status" value="active"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-zinc-700 dark:text-zinc-300">Active</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" wire:model="status" value="inactive"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-zinc-700 dark:text-zinc-300">Inactive</span>
                                    </label>
                                </div>
                                @error('status')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>

                    <!-- Fixed Footer with Buttons -->
                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700 mt-6">
                        <div class="flex justify-end space-x-3">
                            <button wire:click="closeAddDepartment" type="button"
                                class="px-4 py-2 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                Cancel
                            </button>
                            <button wire:click="saveDepartment" type="button"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                {{ $editingId ? 'Update Department' : 'Create Department' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backdrop -->
            <div wire:click="closeAddDepartment" class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>
        @endif
    </div>
</div>
