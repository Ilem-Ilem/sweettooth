<div class="space-y-6">
    <!-- Page Title -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Production Dashboard</h1>
        <p class="text-gray-600 mt-2">Monitor production queue, recipes, and team status</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 flex flex-wrap gap-4 items-center justify-between">
        <div class="flex gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select wire:model.debounce-500ms="selectedDepartment" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Departments</option>
                    <option value="bakery">Bakery</option>
                    <option value="gelato">Gelato</option>
                    <option value="confectionaries">Confectionaries</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button wire:click="refresh" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Refresh
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Queue -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Today's Queue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $queueCount ?? '0' }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2h2a2 2 0 012 2v9a2 2 0 01-2 2h-2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2H2a2 2 0 01-2-2V7a2 2 0 012-2h2V5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Completed Today</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">{{ $completedCount ?? '0' }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">In Progress</p>
                    <p class="text-2xl font-bold text-orange-600 mt-2">{{ $inProgressCount ?? '0' }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Staff on Duty -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Staff on Duty</p>
                    <p class="text-2xl font-bold text-purple-600 mt-2">{{ $staffOnDuty ?? '0' }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 00-6 0v1H3a2 2 0 00-2 2v1a2 2 0 002 2h12a2 2 0 002-2v-1a2 2 0 00-2-2h-3V6z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline View -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Production Timeline</h3>
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="w-24 text-sm font-medium text-gray-600">06:00 AM</div>
                <div class="flex-1 border-l-4 border-green-500 pl-4 pb-4">
                    <p class="font-medium text-gray-900">Morning Batch - Sourdough</p>
                    <p class="text-sm text-gray-600">Oven ready • 24 loaves produced</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-24 text-sm font-medium text-gray-600">10:00 AM</div>
                <div class="flex-1 border-l-4 border-blue-500 pl-4 pb-4">
                    <p class="font-medium text-gray-900">Gelato Production</p>
                    <p class="text-sm text-gray-600">In progress • Vanilla & Chocolate</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-24 text-sm font-medium text-gray-600">02:00 PM</div>
                <div class="flex-1 border-l-4 border-yellow-500 pl-4 pb-4">
                    <p class="font-medium text-gray-900">Afternoon Batch - Croissants</p>
                    <p class="text-sm text-gray-600">Pending • Est. 30 units</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Queue -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Production Queue</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-900">Recipe</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-900">Quantity</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-900">Status</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-900">Assigned To</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-900">Est. Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900">Sourdough Bread</td>
                        <td class="px-4 py-3 text-right text-gray-600">24 units</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Completed</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">John Smith</td>
                        <td class="px-4 py-3 text-center text-gray-600">2h 15m</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-900">Vanilla Gelato</td>
                        <td class="px-4 py-3 text-right text-gray-600">15 L</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">In Progress</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">Maria Garcia</td>
                        <td class="px-4 py-3 text-center text-gray-600">45m</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
