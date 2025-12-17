<div>
    <x-breadcrumb title="Organization Helper" :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Organization Helper']]" :compact="false" :with-icons="true" />

    <div class="max-w-7xl mx-auto py-6">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h1 class="ml-2 text-2xl font-medium text-gray-900 dark:text-white">
                        Organization Workflow Guide
                    </h1>
                </div>
                <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                    This guide explains how to use the Organization section effectively. Each subsection includes step-by-step workflows with visual aids.
                </p>
            </div>

            <!-- Departments Section -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h2 class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">Departments</h2>
                </div>

                <!-- Departments Workflow Illustration -->
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 text-center">Department Creation Workflow</h3>
                    <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-4">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mb-2">1</div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Create Category</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Sales, Production, etc.</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg mb-2">2</div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Add Department</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Link to category</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg mb-2">3</div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Approval Process</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Super admin review</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg mb-2">4</div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Active Department</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Ready for use</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Department Categories -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Department Categories</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></span>
                                <span>Organize departments into categories (Sales, Production, etc.)</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></span>
                                <span>Super admins can create/edit/delete categories</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3"></span>
                                <span>Used to group related departments for better organization</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-white dark:bg-zinc-600 rounded">
                            <strong>📋 Key Actions:</strong>
                            <ul class="mt-2 text-xs space-y-1">
                                <li>• Create new categories</li>
                                <li>• Edit existing categories</li>
                                <li>• Delete unused categories</li>
                                <li>• Export category list</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Departments Management -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Department Management</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3"></span>
                                <span>Create and manage individual departments</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mt-2 mr-3"></span>
                                <span>Approval workflow for non-super-admin changes</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3"></span>
                                <span>Link departments to categories and branches</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-white dark:bg-zinc-600 rounded">
                            <strong>🔄 Approval Flow:</strong>
                            <div class="mt-2 text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xs font-bold">1</span>
                                    <span>Submit Request</span>
                                </div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">2</span>
                                    <span>Super Admin Review</span>
                                </div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">3</span>
                                    <span>Approval/Changes Applied</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Management Section -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h2 class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">Employee Management</h2>
                </div>

                <!-- Employee Lifecycle Illustration -->
                <div class="mb-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 text-center">Employee Lifecycle</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div class="font-semibold text-sm">Hire</div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">Create profile</div>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="font-semibold text-sm">Onboard</div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">Assign role</div>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="font-semibold text-sm">Work</div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">Clock in/out</div>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="font-semibold text-sm">Manage</div>
                            <div class="text-xs text-gray-600 dark:text-gray-300">Leave & reviews</div>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <!-- All Employees -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-t-4 border-indigo-500">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">All Employees</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <div>• View employee list with details</div>
                            <div>• Advanced search and filtering</div>
                            <div>• Bulk operations and exports</div>
                            <div>• Department and role overview</div>
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="text-center">
                                    <div class="font-bold text-green-600">👥</div>
                                    <div>Active Staff</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-blue-600">📊</div>
                                    <div>Analytics</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create Employee -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-t-4 border-green-500">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Create Employee</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <div>• Complete employee information</div>
                            <div>• Upload profile photo</div>
                            <div>• Set compensation details</div>
                            <div>• Assign department and role</div>
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2">
                            <div class="text-xs font-semibold mb-2">📋 Form Sections:</div>
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                <div>👤 Personal</div>
                                <div>💼 Employment</div>
                                <div>💰 Salary</div>
                                <div>🔐 Access</div>
                            </div>
                        </div>
                    </div>

                    <!-- Clock-In Board -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-t-4 border-orange-500">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Clock-In Board</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <div>• Real-time attendance tracking</div>
                            <div>• Shift start/end times</div>
                            <div>• Break time management</div>
                            <div>• Work hour calculations</div>
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2">
                            <div class="text-xs font-semibold mb-2">⏰ Daily Flow:</div>
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs mr-2">✓</span>
                                    Clock In
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs mr-2">✓</span>
                                    Work Period
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs mr-2">✓</span>
                                    Clock Out
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Management Illustration -->
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <!-- Roles & Permissions -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-l-4 border-red-500">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Roles & Permissions</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <div>• Define user roles and permissions</div>
                            <div>• Set access control levels</div>
                            <div>• Configure security policies</div>
                            <div>• Manage role hierarchies</div>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900/30 rounded p-2 text-xs">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <strong>Super Admin Only</strong>
                            </div>
                        </div>
                        <div class="mt-2 bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <strong>🔑 Permission Types:</strong>
                            <div class="grid grid-cols-2 gap-1 mt-1">
                                <div>• View Access</div>
                                <div>• Edit Rights</div>
                                <div>• Delete Permissions</div>
                                <div>• Admin Controls</div>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Roles -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Assign Roles</h3>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <div>• Assign roles to employees</div>
                            <div>• Bulk role assignments</div>
                            <div>• Role-based access control</div>
                            <div>• Permission inheritance</div>
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2">
                            <strong>🔄 Assignment Process:</strong>
                            <div class="mt-2 space-y-2 text-xs">
                                <div class="flex items-center">
                                    <span class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs mr-2">1</span>
                                    Select Employee(s)
                                </div>
                                <div class="flex items-center">
                                    <span class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center text-white text-xs mr-2">2</span>
                                    Choose Role(s)
                                </div>
                                <div class="flex items-center">
                                    <span class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs mr-2">3</span>
                                    Apply Changes
                                </div>
                                <div class="flex items-center">
                                    <span class="w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs mr-2">4</span>
                                    Permissions Updated
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Management Section -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">Leave Management</h2>
                </div>

                <!-- Leave Request Flow Illustration -->
                <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 text-center">Leave Request Process Flow</h3>
                    <div class="flex flex-col lg:flex-row items-center justify-center space-y-4 lg:space-y-0 lg:space-x-6">
                        <!-- Employee Side -->
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-semibold text-blue-600 mb-2">👤 Employee</div>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center bg-white dark:bg-zinc-700 rounded-lg p-3 shadow">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm mr-3">1</div>
                                    <div>
                                        <div class="font-semibold text-sm">Apply for Leave</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-300">Select dates & reason</div>
                                    </div>
                                </div>
                                <div class="flex items-center bg-white dark:bg-zinc-700 rounded-lg p-3 shadow">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm mr-3">3</div>
                                    <div>
                                        <div class="font-semibold text-sm">Receive Response</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-300">Approved/Rejected</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l3-3m0 0l3 3m-3-3v6m4-13a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>

                        <!-- Manager Side -->
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-semibold text-green-600 mb-2">👔 Manager</div>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center bg-white dark:bg-zinc-700 rounded-lg p-3 shadow">
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm mr-3">2</div>
                                    <div>
                                        <div class="font-semibold text-sm">Review Request</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-300">Check balance & dates</div>
                                    </div>
                                </div>
                                <div class="flex items-center bg-white dark:bg-zinc-700 rounded-lg p-3 shadow">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm mr-3">4</div>
                                    <div>
                                        <div class="font-semibold text-sm">Update Status</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-300">Approve/Reject with notes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Apply Leave -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-lg p-4 text-center border border-blue-200 dark:border-blue-700">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 mx-auto">
                            📝
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Apply Leave</h3>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            Request time off with dates, reason, and supporting documents
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <strong>📋 Required:</strong>
                            <ul class="mt-1 space-y-1">
                                <li>• Start/End dates</li>
                                <li>• Leave type</li>
                                <li>• Reason</li>
                                <li>• Contact info</li>
                            </ul>
                        </div>
                    </div>

                    <!-- My Leaves -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-lg p-4 text-center border border-green-200 dark:border-green-700">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 mx-auto">
                            📋
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">My Leaves</h3>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            View your leave requests, status, and history
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <strong>📊 Status Types:</strong>
                            <div class="grid grid-cols-2 gap-1 mt-1">
                                <div class="text-yellow-600">⏳ Pending</div>
                                <div class="text-green-600">✅ Approved</div>
                                <div class="text-red-600">❌ Rejected</div>
                                <div class="text-blue-600">📅 Scheduled</div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Balance -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-lg p-4 text-center border border-purple-200 dark:border-purple-700">
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 mx-auto">
                            ⚖️
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Leave Balance</h3>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            Check available leave days and balances
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <strong>📈 Balance Info:</strong>
                            <ul class="mt-1 space-y-1">
                                <li>• Annual leave</li>
                                <li>• Sick leave</li>
                                <li>• Emergency leave</li>
                                <li>• Used vs Available</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Approve Leaves -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg p-4 text-center border border-orange-200 dark:border-orange-700">
                        <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl mb-3 mx-auto">
                            ✅
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Approve Leaves</h3>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mb-3">
                            Managers review and approve leave requests
                        </div>
                        <div class="bg-white dark:bg-zinc-600 rounded p-2 text-xs">
                            <strong>⚡ Quick Actions:</strong>
                            <div class="grid grid-cols-2 gap-1 mt-1">
                                <div class="text-green-600">✓ Approve</div>
                                <div class="text-red-600">✗ Reject</div>
                                <div class="text-blue-600">💬 Comment</div>
                                <div class="text-purple-600">📋 Details</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Types Management -->
                <div class="mt-6 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700">
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Leave Types Management</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Configure leave policies and approval workflows</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">⚙️ Configuration Options:</h4>
                            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                <li>• Create custom leave types</li>
                                <li>• Set approval hierarchies</li>
                                <li>• Configure balance limits</li>
                                <li>• Define carry-over rules</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">🔒 Access Level:</h4>
                            <div class="bg-red-100 dark:bg-red-900/30 rounded p-2 text-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <strong>Super Admin / HR Only</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Management Section -->
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">Audit Management</h2>
                </div>

                <!-- Audit Process Flow -->
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 text-center">Audit & Approval Workflow</h3>
                    <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-4">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">User Action</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Create/Update/Delete</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Audit Request</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Submit for approval</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Super Admin Review</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Approve/Reject</div>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-sm">Action Completed</div>
                                <div class="text-xs text-gray-600 dark:text-gray-300">Logged & Applied</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Audit Logs -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-lg p-4 border border-green-200 dark:border-green-700">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Audit Logs</h3>
                        </div>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Complete Activity History:</strong> Every system action is logged</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Advanced Filtering:</strong> By user, action type, date range</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Change Tracking:</strong> Who changed what and when</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Compliance Monitoring:</strong> Regulatory requirements met</span>
                            </div>
                        </div>
                        <div class="mt-4 bg-white dark:bg-zinc-600 rounded p-3">
                            <strong>🔍 Search & Filter Options:</strong>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                                <div>• User name</div>
                                <div>• Action type</div>
                                <div>• Date range</div>
                                <div>• Module area</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/30 dark:to-red-900/30 rounded-lg p-4 border border-orange-200 dark:border-orange-700">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Pending Approvals</h3>
                        </div>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Review Queue:</strong> All pending approval requests</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-red-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Decision Making:</strong> Approve or reject with reasons</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-indigo-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Data Integrity:</strong> Ensure quality before applying changes</span>
                            </div>
                            <div class="flex items-start">
                                <span class="inline-block w-3 h-3 bg-pink-500 rounded-full mt-2 mr-3"></span>
                                <span><strong>Full Audit Trail:</strong> Every decision is permanently logged</span>
                            </div>
                        </div>
                        <div class="mt-4 bg-white dark:bg-zinc-600 rounded p-3">
                            <strong>⚡ Quick Actions:</strong>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                                <div class="text-green-600">✓ Approve</div>
                                <div class="text-red-600">✗ Reject</div>
                                <div class="text-blue-600">👁️ View Details</div>
                                <div class="text-purple-600">💬 Add Notes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Warning -->
                <div class="mt-6 bg-gradient-to-r from-yellow-50 to-red-50 dark:from-yellow-900/20 dark:to-red-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-700">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">🔐 Critical Security & Compliance</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Audit Management is the backbone of system security and regulatory compliance. It ensures every action is tracked, reviewed, and approved appropriately.
                            </p>
                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">🛡️ Security Benefits:</h4>
                                    <ul class="text-gray-600 dark:text-gray-300 space-y-1">
                                        <li>• Complete activity traceability</li>
                                        <li>• Fraud prevention and detection</li>
                                        <li>• Unauthorized access prevention</li>
                                        <li>• Data integrity assurance</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">⚖️ Compliance Features:</h4>
                                    <ul class="text-gray-600 dark:text-gray-300 space-y-1">
                                        <li>• Regulatory audit trails</li>
                                        <li>• Change management</li>
                                        <li>• Approval workflows</li>
                                        <li>• Accountability tracking</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflow Summary -->
            <div class="p-6 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">🎯 Key Workflows</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">For Employees:</h3>
                        <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <li>• Apply for leave → Check balance → Submit request</li>
                            <li>• Clock in/out daily → View attendance records</li>
                            <li>• View personal information → Update profile if needed</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">For Managers/Admins:</h3>
                        <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <li>• Create departments → Assign employees → Manage roles</li>
                            <li>• Approve leave requests → Review audit logs → Maintain security</li>
                            <li>• Monitor employee activities → Ensure compliance → Generate reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>