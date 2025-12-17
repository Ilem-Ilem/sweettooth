<div>
    <x-breadcrumb title="Inventory Helper" :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Inventory Helper']]" :compact="false" :with-icons="true" />

    <div class="max-w-7xl mx-auto py-6">
        <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h1 class="ml-2 text-2xl font-medium text-gray-900 dark:text-white">
                        Inventory Management Guide
                    </h1>
                </div>
                <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                    Master your inventory with this comprehensive guide featuring animated workflows, visual diagrams, and step-by-step processes.
                </p>
            </div>

            <!-- Inventory Lifecycle Animation -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                    Inventory Lifecycle Flow
                </h2>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6">
                    <div class="flex flex-col lg:flex-row items-center justify-center space-y-6 lg:space-y-0 lg:space-x-8">
                        <!-- Planning Phase -->
                        <div class="flex flex-col items-center animate-fade-in-up" style="animation-delay: 0.1s">
                            <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white mb-3 shadow-lg animate-bounce">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-lg text-gray-900 dark:text-white">Planning</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Assess needs & forecast</div>
                            </div>
                            <div class="mt-2 px-3 py-1 bg-blue-100 dark:bg-blue-800 rounded-full text-xs font-medium text-blue-800 dark:text-blue-200">
                                📋 Requirements
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block animate-pulse">
                            <svg class="w-12 h-12 text-gray-400 animate-bounce" style="animation-delay: 0.5s" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>

                        <!-- Procurement Phase -->
                        <div class="flex flex-col items-center animate-fade-in-up" style="animation-delay: 0.3s">
                            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center text-white mb-3 shadow-lg animate-pulse">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-lg text-gray-900 dark:text-white">Procurement</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Purchase & receive items</div>
                            </div>
                            <div class="mt-2 px-3 py-1 bg-green-100 dark:bg-green-800 rounded-full text-xs font-medium text-green-800 dark:text-green-200">
                                🛒 Purchases
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block animate-pulse">
                            <svg class="w-12 h-12 text-gray-400 animate-bounce" style="animation-delay: 0.7s" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>

                        <!-- Storage Phase -->
                        <div class="flex flex-col items-center animate-fade-in-up" style="animation-delay: 0.5s">
                            <div class="w-20 h-20 bg-purple-500 rounded-full flex items-center justify-center text-white mb-3 shadow-lg animate-bounce">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-lg text-gray-900 dark:text-white">Storage</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Stock management & tracking</div>
                            </div>
                            <div class="mt-2 px-3 py-1 bg-purple-100 dark:bg-purple-800 rounded-full text-xs font-medium text-purple-800 dark:text-purple-200">
                                📦 Stock Levels
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block animate-pulse">
                            <svg class="w-12 h-12 text-gray-400 animate-bounce" style="animation-delay: 0.9s" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>

                        <!-- Distribution Phase -->
                        <div class="flex flex-col items-center animate-fade-in-up" style="animation-delay: 0.7s">
                            <div class="w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center text-white mb-3 shadow-lg animate-pulse">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-lg text-gray-900 dark:text-white">Distribution</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Issue & dispatch items</div>
                            </div>
                            <div class="mt-2 px-3 py-1 bg-orange-100 dark:bg-orange-800 rounded-full text-xs font-medium text-orange-800 dark:text-orange-200">
                                🚚 Dispatches
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Core Inventory Features -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                    Core Inventory Features
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Items Management -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl p-6 border border-blue-200 dark:border-blue-700 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4 animate-spin-slow">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">Items Management</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Create and manage your inventory catalog with detailed specifications.</p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 animate-ping"></span>
                                <span>Add new items with SKU, categories, UOM</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3 animate-ping" style="animation-delay: 0.2s"></span>
                                <span>Update item details and specifications</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-3 animate-ping" style="animation-delay: 0.4s"></span>
                                <span>Set reorder levels and pricing</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-white dark:bg-zinc-700 rounded-lg">
                            <div class="text-xs font-semibold text-gray-900 dark:text-white mb-2">📋 Key Actions:</div>
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                <div>• Create Item</div>
                                <div>• Edit Details</div>
                                <div>• Set Categories</div>
                                <div>• Configure UOM</div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Orders -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl p-6 border border-green-200 dark:border-green-700 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mr-4 animate-bounce">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">Purchase Orders</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Track purchases, manage suppliers, and monitor procurement costs.</p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3 animate-pulse"></span>
                                <span>Create purchase orders with multiple items</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 animate-pulse" style="animation-delay: 0.2s"></span>
                                <span>Track payment status and delivery</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-orange-500 rounded-full mr-3 animate-pulse" style="animation-delay: 0.4s"></span>
                                <span>Calculate total costs including additional fees</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-white dark:bg-zinc-700 rounded-lg">
                            <div class="text-xs font-semibold text-gray-900 dark:text-white mb-2">💰 Purchase Flow:</div>
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center">
                                    <span class="w-4 h-4 bg-yellow-500 rounded-full flex items-center justify-center text-white text-xs mr-2">1</span>
                                    Create Order
                                </div>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs mr-2">2</span>
                                    Add Items & Costs
                                </div>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center text-white text-xs mr-2">3</span>
                                    Process Payment
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Management -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl p-6 border border-purple-200 dark:border-purple-700 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4 animate-pulse">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">Stock Management</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">Monitor stock levels, track movements, and manage inventory health.</p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-3 animate-bounce"></span>
                                <span>Real-time stock level monitoring</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-3 animate-bounce" style="animation-delay: 0.2s"></span>
                                <span>Low stock alerts and reorder points</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="w-2 h-2 bg-orange-500 rounded-full mr-3 animate-bounce" style="animation-delay: 0.4s"></span>
                                <span>Stock movement tracking and history</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-white dark:bg-zinc-700 rounded-lg">
                            <div class="text-xs font-semibold text-gray-900 dark:text-white mb-2">📊 Stock Status:</div>
                            <div class="grid grid-cols-3 gap-1 text-xs text-center">
                                <div class="text-green-600">✓ In Stock</div>
                                <div class="text-yellow-600">⚠ Low Stock</div>
                                <div class="text-red-600">✗ Out of Stock</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request & Dispatch Workflow -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-3 animate-pulse"></div>
                    Request & Dispatch Process
                </h2>

                <div class="bg-gradient-to-br from-orange-50 via-yellow-50 to-red-50 dark:from-orange-900/20 dark:via-yellow-900/20 dark:to-red-900/20 rounded-lg p-6">
                    <div class="flex flex-col lg:flex-row items-center justify-center space-y-8 lg:space-y-0 lg:space-x-12">
                        <!-- Request Phase -->
                        <div class="flex flex-col items-center text-center max-w-xs">
                            <div class="relative">
                                <div class="w-24 h-24 bg-orange-500 rounded-full flex items-center justify-center text-white mb-4 shadow-xl animate-pulse">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center animate-ping">
                                    <span class="text-white text-xs font-bold">1</span>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Item Request</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Department requests items for production or operations</p>
                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-3 w-full">
                                <div class="text-xs font-semibold mb-2">📝 Request Details:</div>
                                <ul class="text-xs space-y-1">
                                    <li>• Item & quantity needed</li>
                                    <li>• Required by date</li>
                                    <li>• Purpose/usage</li>
                                    <li>• Approver notification</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block animate-bounce">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>

                        <!-- Approval Phase -->
                        <div class="flex flex-col items-center text-center max-w-xs">
                            <div class="relative">
                                <div class="w-24 h-24 bg-yellow-500 rounded-full flex items-center justify-center text-white mb-4 shadow-xl animate-pulse" style="animation-delay: 0.5s">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center animate-ping">
                                    <span class="text-white text-xs font-bold">2</span>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Approval</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Inventory manager reviews and approves requests</p>
                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-3 w-full">
                                <div class="text-xs font-semibold mb-2">✅ Approval Actions:</div>
                                <ul class="text-xs space-y-1">
                                    <li>• Check stock availability</li>
                                    <li>• Verify request validity</li>
                                    <li>• Approve or reject</li>
                                    <li>• Add approval notes</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden lg:block animate-bounce" style="animation-delay: 0.5s">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>

                        <!-- Dispatch Phase -->
                        <div class="flex flex-col items-center text-center max-w-xs">
                            <div class="relative">
                                <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center text-white mb-4 shadow-xl animate-pulse" style="animation-delay: 1s">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center animate-ping">
                                    <span class="text-white text-xs font-bold">3</span>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Dispatch</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Items are issued and delivered to requesting department</p>
                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-3 w-full">
                                <div class="text-xs font-semibold mb-2">🚚 Dispatch Process:</div>
                                <ul class="text-xs space-y-1">
                                    <li>• Pick items from stock</li>
                                    <li>• Update inventory records</li>
                                    <li>• Generate dispatch note</li>
                                    <li>• Confirm delivery</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Check & Analytics -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-3 h-3 bg-indigo-500 rounded-full mr-3 animate-pulse"></div>
                    Health Monitoring & Analytics
                </h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Health Check Dashboard -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-xl p-6 border border-indigo-200 dark:border-indigo-700">
                        <div class="flex items-center mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 animate-spin-slow">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">Health Check Dashboard</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Monitor inventory health and identify issues</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                                    <span class="text-sm font-medium">Stock Levels</span>
                                </div>
                                <span class="text-xs bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded">Optimal</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3 animate-pulse" style="animation-delay: 0.3s"></div>
                                    <span class="text-sm font-medium">Reorder Alerts</span>
                                </div>
                                <span class="text-xs bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded">5 Items</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3 animate-pulse" style="animation-delay: 0.6s"></div>
                                    <span class="text-sm font-medium">Expired Items</span>
                                </div>
                                <span class="text-xs bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200 px-2 py-1 rounded">2 Items</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse" style="animation-delay: 0.9s"></div>
                                    <span class="text-sm font-medium">Stock Turnover</span>
                                </div>
                                <span class="text-xs bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">85%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics & Insights -->
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/30 dark:to-cyan-900/30 rounded-xl p-6 border border-teal-200 dark:border-teal-700">
                        <div class="flex items-center mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4 animate-bounce">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl text-gray-900 dark:text-white">Analytics & Insights</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Data-driven inventory optimization</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    Stock Movement Trends
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Track how inventory moves through your system over time</p>
                            </div>

                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Purchase Analytics
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Analyze purchasing patterns and supplier performance</p>
                            </div>

                            <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Stock Valuation
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Monitor inventory value and financial impact</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="mt-6 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-lg p-6">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Inventory Management Best Practices
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                            <h4 class="font-semibold text-green-600 mb-2">✅ Do's</h4>
                            <ul class="text-sm space-y-1">
                                <li>• Regular stock counts</li>
                                <li>• Set appropriate reorder levels</li>
                                <li>• Track expiry dates</li>
                                <li>• Maintain accurate records</li>
                            </ul>
                        </div>
                        <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                            <h4 class="font-semibold text-red-600 mb-2">❌ Don'ts</h4>
                            <ul class="text-sm space-y-1">
                                <li>• Overstock items</li>
                                <li>• Ignore low stock alerts</li>
                                <li>• Mix different batches</li>
                                <li>• Skip regular audits</li>
                            </ul>
                        </div>
                        <div class="bg-white dark:bg-zinc-700 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-600 mb-2">🎯 Goals</h4>
                            <ul class="text-sm space-y-1">
                                <li>• 95%+ inventory accuracy</li>
                                <li>• Minimize stockouts</li>
                                <li>• Optimize carrying costs</li>
                                <li>• Improve turnover ratio</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }
    </style>
</div>