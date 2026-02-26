<div class="space-y-6 p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Branch-wide overview</p>
        </div>
        @if(request()->query('b_id'))
            <div class="bg-blue-100 dark:bg-blue-900 px-4 py-2 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-200">Branch ID: {{ request()->query('b_id') }}</p>
            </div>
        @endif
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_employees'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Employees</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_employees'] }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Branches Managed</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['branches_managed'] }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ branch_route('branch-dashboard.employee.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-center">
                Manage Employees
            </a>
            <a href="{{ branch_route('branch-dashboard.organization.helper') }}" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-center">
                Organization
            </a>
            <a href="{{ branch_route('branch-dashboard.reporting.dashboard') }}" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-center">
                Reports
            </a>
            <a href="{{ branch_route('branch-dashboard.analytics.overview') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition text-center">
                Analytics
            </a>
        </div>
    </div>

    <!-- Recent Employees -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Employees</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b dark:border-gray-700">
                    <tr>
                        <th class="text-left py-2 px-4 font-medium text-gray-700 dark:text-gray-300">Name</th>
                        <th class="text-left py-2 px-4 font-medium text-gray-700 dark:text-gray-300">Email</th>
                        <th class="text-left py-2 px-4 font-medium text-gray-700 dark:text-gray-300">Department</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEmployees as $employee)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4">{{ $employee->name }}</td>
                            <td class="py-3 px-4">{{ $employee->email }}</td>
                            <td class="py-3 px-4">{{ $employee->department?->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-center text-gray-500 dark:text-gray-400">No recent employees</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
