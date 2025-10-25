<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgb(156 163 175) rgb(243 244 246);
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgb(243 244 246);
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgb(156 163 175);
            border-radius: 3px;
        }
        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: rgb(55 65 81);
        }
        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgb(107 114 128);
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body class="h-full bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <div id="app" class="h-full flex flex-col">
        <!-- Header Section -->
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">Modern POS</h1>
                    <div class="hidden md:block">
                        <span class="text-blue-100">Cashier:</span>
                        <span class="font-medium">John Smith</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="current-time" class="text-lg font-medium">12:00:00 PM</div>
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-blue-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button id="logout-btn" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-medium transition-colors">Logout</button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-4 py-6 flex flex-col lg:flex-row gap-6 overflow-hidden">
            <!-- Left Column - Product Selection (70%) -->
            <div class="lg:w-7/12 flex flex-col gap-6">
                <!-- Product Search and Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Product Selection</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 dark:text-gray-300">View:</span>
                            <button id="view-toggle" class="bg-primary-100 dark:bg-gray-700 text-primary-700 dark:text-gray-300 px-3 py-1 rounded-lg text-sm font-medium transition-colors">Dropdown</button>
                        </div>
                    </div>

                    <!-- Searchable Dropdown -->
                    <div id="dropdown-view" class="space-y-4">
                        <div class="relative">
                            <input type="text" id="product-search" placeholder="Search products..." class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute right-3 top-3 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button class="category-filter bg-primary-100 dark:bg-gray-700 text-primary-700 dark:text-gray-300 px-3 py-1 rounded-lg text-sm font-medium transition-colors active" data-category="all">All</button>
                            <button class="category-filter bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-lg text-sm font-medium transition-colors" data-category="beverages">Beverages</button>
                            <button class="category-filter bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-lg text-sm font-medium transition-colors" data-category="food">Food</button>
                            <button class="category-filter bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-lg text-sm font-medium transition-colors" data-category="merchandise">Merchandise</button>
                        </div>

                        <div class="relative">
                            <div id="product-dropdown" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-y-auto scrollbar-thin hidden">
                                <!-- Product options will be populated here -->
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div id="selected-product" class="text-gray-700 dark:text-gray-300">
                                No product selected
                            </div>
                            <button id="add-to-cart" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Add to Cart (Ctrl+P)
                            </button>
                        </div>
                    </div>

                    <!-- Grid View (Hidden by default) -->
                    <div id="grid-view" class="hidden">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <!-- Product grid items will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Transaction History</h2>
                        <button id="toggle-history" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div id="history-content" class="max-h-60 overflow-y-auto scrollbar-thin">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                                </tr>
                            </thead>
                            <tbody id="history-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Transaction history will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Cart & Payment (30%) -->
            <div class="lg:w-5/12 flex flex-col gap-6">
                <!-- Cart Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-colors flex flex-col h-full">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Shopping Cart</h2>
                    
                    <!-- Customer Selection -->
                    <div class="mb-4">
                        <label for="customer-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer</label>
                        <div class="relative">
                            <select id="customer-select" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                                <option value="">Walk-in Customer</option>
                                <!-- Customer options will be populated here -->
                            </select>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div id="cart-items" class="flex-1 overflow-y-auto scrollbar-thin mb-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <!-- Cart items will be populated here -->
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            Cart is empty
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span id="subtotal" class="font-medium text-gray-800 dark:text-white">$0.00</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <div class="flex items-center space-x-2">
                                <button id="apply-discount" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 text-sm font-medium transition-colors">Apply Discount</button>
                                <span id="discount-amount" class="font-medium text-red-600 dark:text-red-400">-$0.00</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-gray-800 dark:text-white">Total:</span>
                            <span id="total" class="text-gray-800 dark:text-white">$0.00</span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <button id="clear-cart" class="bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg font-medium transition-colors">Clear Cart (Ctrl+C)</button>
                        <button id="quick-discount" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg font-medium transition-colors">Quick Discount (Ctrl+D)</button>
                        <button id="void-transaction" class="bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-medium transition-colors">Void Last</button>
                        <button id="open-drawer" class="bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium transition-colors">Open Drawer</button>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 transition-colors">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Payment</h2>
                    
                    <!-- Payment Methods -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button class="payment-method bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-800 dark:text-white py-2 rounded-lg font-medium border-2 border-transparent hover:border-primary-500 transition-colors" data-method="cash">Cash</button>
                            <button class="payment-method bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-800 dark:text-white py-2 rounded-lg font-medium border-2 border-transparent hover:border-primary-500 transition-colors" data-method="card">Card</button>
                            <button class="payment-method bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-800 dark:text-white py-2 rounded-lg font-medium border-2 border-transparent hover:border-primary-500 transition-colors" data-method="mobile">Mobile</button>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div id="payment-form" class="space-y-4 hidden">
                        <!-- Cash Payment -->
                        <div id="cash-payment" class="space-y-2 hidden">
                            <label for="amount-tendered" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount Tendered</label>
                            <input type="number" id="amount-tendered" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="0.00">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Change:</span>
                                <span id="change-amount" class="font-medium text-green-600 dark:text-green-400">$0.00</span>
                            </div>
                        </div>

                        <!-- Card/Mobile Payment -->
                        <div id="digital-payment" class="space-y-2 hidden">
                            <label for="transaction-id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction ID</label>
                            <input type="text" id="transaction-id" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="Enter transaction ID">
                        </div>

                        <!-- Custom Note -->
                        <div>
                            <label for="transaction-note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Note (Optional)</label>
                            <textarea id="transaction-note" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" rows="2" placeholder="Add a note for this transaction..."></textarea>
                        </div>

                        <!-- Payment Status -->
                        <div id="payment-status" class="hidden p-3 rounded-lg text-center font-medium"></div>

                        <!-- Process Payment Button -->
                        <button id="process-payment" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-lg font-medium text-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Process Payment (Ctrl+Enter)
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Discount Modal -->
    <div id="discount-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-md mx-4 fade-in">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Apply Discount</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Type</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="discount-type" value="percentage" class="text-primary-600 focus:ring-primary-500" checked>
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Percentage</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="discount-type" value="fixed" class="text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Fixed Amount</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="discount-value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Value</label>
                    <input type="number" id="discount-value" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="0">
                    <div class="mt-2 grid grid-cols-3 gap-2">
                        <button class="quick-discount-btn bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-700 dark:text-gray-300 py-1 rounded transition-colors" data-value="5">5%</button>
                        <button class="quick-discount-btn bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-700 dark:text-gray-300 py-1 rounded transition-colors" data-value="10">10%</button>
                        <button class="quick-discount-btn bg-gray-100 dark:bg-gray-700 hover:bg-primary-100 dark:hover:bg-primary-900 text-gray-700 dark:text-gray-300 py-1 rounded transition-colors" data-value="20">20%</button>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button id="cancel-discount" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button id="apply-discount-btn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="help-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto fade-in">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Keyboard Shortcuts</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Add selected product to cart</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-sm font-medium">Ctrl + P</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Clear cart</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-sm font-medium">Ctrl + C</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Apply default discount</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-sm font-medium">Ctrl + D</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Process payment</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-sm font-medium">Ctrl + Enter</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Show this help</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-sm font-medium">Ctrl + H</span>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button id="close-help" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Close</button>
            </div>
        </div>
    </div>

    <!-- Language Selector -->
    <div class="fixed bottom-4 right-4 z-40">
        <button id="language-toggle" class="bg-white dark:bg-gray-800 shadow-lg rounded-full p-3 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
        </button>
    </div>

    <!-- Help Button -->
    <div class="fixed bottom-4 left-4 z-40">
        <button id="help-btn" class="bg-primary-600 hover:bg-primary-700 shadow-lg rounded-full p-3 text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

    <script>
        // Sample Data
        const sampleProducts = [
            { id: 1, name: "Coffee", price: 3.50, category: "beverages", stock: 15, image: "https://via.placeholder.com/40" },
            { id: 2, name: "Tea", price: 2.50, category: "beverages", stock: 20, image: "https://via.placeholder.com/40" },
            { id: 3, name: "Soda", price: 2.00, category: "beverages", stock: 30, image: "https://via.placeholder.com/40" },
            { id: 4, name: "Water", price: 1.50, category: "beverages", stock: 25, image: "https://via.placeholder.com/40" },
            { id: 5, name: "Juice", price: 3.00, category: "beverages", stock: 18, image: "https://via.placeholder.com/40" },
            { id: 6, name: "Sandwich", price: 5.50, category: "food", stock: 12, image: "https://via.placeholder.com/40" },
            { id: 7, name: "Salad", price: 6.50, category: "food", stock: 8, image: "https://via.placeholder.com/40" },
            { id: 8, name: "Pasta", price: 8.00, category: "food", stock: 10, image: "https://via.placeholder.com/40" },
            { id: 9, name: "Pizza", price: 10.00, category: "food", stock: 6, image: "https://via.placeholder.com/40" },
            { id: 10, name: "Burger", price: 7.50, category: "food", stock: 15, image: "https://via.placeholder.com/40" },
            { id: 11, name: "T-shirt", price: 15.00, category: "merchandise", stock: 20, image: "https://via.placeholder.com/40" },
            { id: 12, name: "Mug", price: 8.00, category: "merchandise", stock: 25, image: "https://via.placeholder.com/40" },
            { id: 13, name: "Cap", price: 12.00, category: "merchandise", stock: 15, image: "https://via.placeholder.com/40" },
            { id: 14, name: "Notebook", price: 5.00, category: "merchandise", stock: 30, image: "https://via.placeholder.com/40" },
            { id: 15, name: "Pen", price: 2.00, category: "merchandise", stock: 50, image: "https://via.placeholder.com/40" },
            { id: 16, name: "Cake", price: 4.50, category: "food", stock: 7, image: "https://via.placeholder.com/40" },
            { id: 17, name: "Cookie", price: 1.50, category: "food", stock: 25, image: "https://via.placeholder.com/40" },
            { id: 18, name: "Smoothie", price: 4.50, category: "beverages", stock: 12, image: "https://via.placeholder.com/40" },
            { id: 19, name: "Bag", price: 20.00, category: "merchandise", stock: 10, image: "https://via.placeholder.com/40" },
            { id: 20, name: "Keychain", price: 3.00, category: "merchandise", stock: 40, image: "https://via.placeholder.com/40" }
        ];

        const sampleCustomers = [
            { id: 1, name: "John Doe", phone: "555-0101", loyaltyPoints: 150 },
            { id: 2, name: "Jane Smith", phone: "555-0102", loyaltyPoints: 75 },
            { id: 3, name: "Robert Johnson", phone: "555-0103", loyaltyPoints: 200 },
            { id: 4, name: "Emily Davis", phone: "555-0104", loyaltyPoints: 50 },
            { id: 5, name: "Michael Wilson", phone: "555-0105", loyaltyPoints: 300 }
        ];

        // Translations
        const translations = {
            en: {
                title: "Modern POS",
                cashier: "Cashier:",
                logout: "Logout",
                productSelection: "Product Selection",
                view: "View:",
                dropdown: "Dropdown",
                grid: "Grid",
                searchPlaceholder: "Search products...",
                all: "All",
                beverages: "Beverages",
                food: "Food",
                merchandise: "Merchandise",
                noProductSelected: "No product selected",
                addToCart: "Add to Cart (Ctrl+P)",
                transactionHistory: "Transaction History",
                shoppingCart: "Shopping Cart",
                customer: "Customer",
                walkInCustomer: "Walk-in Customer",
                subtotal: "Subtotal:",
                discount: "Discount:",
                applyDiscount: "Apply Discount",
                total: "Total:",
                clearCart: "Clear Cart (Ctrl+C)",
                quickDiscount: "Quick Discount (Ctrl+D)",
                voidLast: "Void Last",
                openDrawer: "Open Drawer",
                payment: "Payment",
                paymentMethod: "Payment Method",
                cash: "Cash",
                card: "Card",
                mobile: "Mobile",
                amountTendered: "Amount Tendered",
                change: "Change:",
                transactionId: "Transaction ID",
                customNote: "Custom Note (Optional)",
                processPayment: "Process Payment (Ctrl+Enter)",
                discountModalTitle: "Apply Discount",
                discountType: "Discount Type",
                percentage: "Percentage",
                fixedAmount: "Fixed Amount",
                discountValue: "Discount Value",
                cancel: "Cancel",
                apply: "Apply",
                helpTitle: "Keyboard Shortcuts",
                addProduct: "Add selected product to cart",
                clearCartShortcut: "Clear cart",
                applyDiscountShortcut: "Apply default discount",
                processPaymentShortcut: "Process payment",
                showHelp: "Show this help",
                close: "Close",
                cartEmpty: "Cart is empty",
                lowStock: "Low stock",
                outOfStock: "Out of stock",
                paymentSuccess: "Payment Successful!",
                paymentInsufficient: "Insufficient Amount",
                paymentError: "Payment Error",
                confirmClearCart: "Are you sure you want to clear the cart?",
                confirmVoid: "Are you sure you want to void the last transaction?",
                confirmLogout: "Are you sure you want to logout?"
            },
            es: {
                title: "Punto de Venta Moderno",
                cashier: "Cajero:",
                logout: "Cerrar Sesión",
                productSelection: "Selección de Productos",
                view: "Vista:",
                dropdown: "Desplegable",
                grid: "Cuadrícula",
                searchPlaceholder: "Buscar productos...",
                all: "Todos",
                beverages: "Bebidas",
                food: "Comida",
                merchandise: "Mercancía",
                noProductSelected: "Ningún producto seleccionado",
                addToCart: "Agregar al Carrito (Ctrl+P)",
                transactionHistory: "Historial de Transacciones",
                shoppingCart: "Carrito de Compras",
                customer: "Cliente",
                walkInCustomer: "Cliente sin registrar",
                subtotal: "Subtotal:",
                discount: "Descuento:",
                applyDiscount: "Aplicar Descuento",
                total: "Total:",
                clearCart: "Vaciar Carrito (Ctrl+C)",
                quickDiscount: "Descuento Rápido (Ctrl+D)",
                voidLast: "Anular Última",
                openDrawer: "Abrir Cajón",
                payment: "Pago",
                paymentMethod: "Método de Pago",
                cash: "Efectivo",
                card: "Tarjeta",
                mobile: "Móvil",
                amountTendered: "Cantidad Entregada",
                change: "Cambio:",
                transactionId: "ID de Transacción",
                customNote: "Nota Personalizada (Opcional)",
                processPayment: "Procesar Pago (Ctrl+Enter)",
                discountModalTitle: "Aplicar Descuento",
                discountType: "Tipo de Descuento",
                percentage: "Porcentaje",
                fixedAmount: "Monto Fijo",
                discountValue: "Valor del Descuento",
                cancel: "Cancelar",
                apply: "Aplicar",
                helpTitle: "Atajos de Teclado",
                addProduct: "Agregar producto seleccionado al carrito",
                clearCartShortcut: "Vaciar carrito",
                applyDiscountShortcut: "Aplicar descuento predeterminado",
                processPaymentShortcut: "Procesar pago",
                showHelp: "Mostrar esta ayuda",
                close: "Cerrar",
                cartEmpty: "El carrito está vacío",
                lowStock: "Stock bajo",
                outOfStock: "Agotado",
                paymentSuccess: "¡Pago Exitoso!",
                paymentInsufficient: "Cantidad Insuficiente",
                paymentError: "Error de Pago",
                confirmClearCart: "¿Está seguro de que desea vaciar el carrito?",
                confirmVoid: "¿Está seguro de que desea anular la última transacción?",
                confirmLogout: "¿Está seguro de que desea cerrar sesión?"
            }
        };

        // Application State
        const state = {
            products: [],
            customers: [],
            cart: [],
            transactions: [],
            selectedProduct: null,
            selectedPaymentMethod: null,
            discount: { type: null, value: 0 },
            currentLanguage: 'en',
            isDarkMode: false,
            currentView: 'dropdown'
        };

        // DOM Elements
        const elements = {
            // Header
            currentTime: document.getElementById('current-time'),
            themeToggle: document.getElementById('theme-toggle'),
            logoutBtn: document.getElementById('logout-btn'),
            
            // Product Selection
            productSearch: document.getElementById('product-search'),
            productDropdown: document.getElementById('product-dropdown'),
            selectedProduct: document.getElementById('selected-product'),
            addToCart: document.getElementById('add-to-cart'),
            categoryFilters: document.querySelectorAll('.category-filter'),
            viewToggle: document.getElementById('view-toggle'),
            dropdownView: document.getElementById('dropdown-view'),
            gridView: document.getElementById('grid-view'),
            
            // Cart
            cartItems: document.getElementById('cart-items'),
            subtotal: document.getElementById('subtotal'),
            discountAmount: document.getElementById('discount-amount'),
            total: document.getElementById('total'),
            customerSelect: document.getElementById('customer-select'),
            clearCart: document.getElementById('clear-cart'),
            quickDiscount: document.getElementById('quick-discount'),
            voidTransaction: document.getElementById('void-transaction'),
            openDrawer: document.getElementById('open-drawer'),
            applyDiscount: document.getElementById('apply-discount'),
            
            // Payment
            paymentMethods: document.querySelectorAll('.payment-method'),
            paymentForm: document.getElementById('payment-form'),
            cashPayment: document.getElementById('cash-payment'),
            digitalPayment: document.getElementById('digital-payment'),
            amountTendered: document.getElementById('amount-tendered'),
            changeAmount: document.getElementById('change-amount'),
            transactionId: document.getElementById('transaction-id'),
            transactionNote: document.getElementById('transaction-note'),
            paymentStatus: document.getElementById('payment-status'),
            processPayment: document.getElementById('process-payment'),
            
            // Transaction History
            toggleHistory: document.getElementById('toggle-history'),
            historyContent: document.getElementById('history-content'),
            historyBody: document.getElementById('history-body'),
            
            // Modals
            discountModal: document.getElementById('discount-modal'),
            discountValue: document.getElementById('discount-value'),
            cancelDiscount: document.getElementById('cancel-discount'),
            applyDiscountBtn: document.getElementById('apply-discount-btn'),
            quickDiscountBtns: document.querySelectorAll('.quick-discount-btn'),
            helpModal: document.getElementById('help-modal'),
            closeHelp: document.getElementById('close-help'),
            helpBtn: document.getElementById('help-btn'),
            
            // Language
            languageToggle: document.getElementById('language-toggle')
        };

        // Initialize the application
        function init() {
            // Load data from localStorage or use sample data
            state.products = JSON.parse(localStorage.getItem('pos-products')) || sampleProducts;
            state.customers = JSON.parse(localStorage.getItem('pos-customers')) || sampleCustomers;
            state.transactions = JSON.parse(localStorage.getItem('pos-transactions')) || [];
            state.currentLanguage = localStorage.getItem('pos-language') || 'en';
            state.isDarkMode = localStorage.getItem('pos-theme') === 'dark';
            
            // Apply saved theme
            if (state.isDarkMode) {
                document.documentElement.classList.add('dark');
            }
            
            // Update UI with initial data
            updateClock();
            setInterval(updateClock, 1000);
            populateCustomerSelect();
            populateProductDropdown();
            updateCartDisplay();
            updateTransactionHistory();
            applyTranslations();
            
            // Set up event listeners
            setupEventListeners();
            
            // Set up keyboard shortcuts
            setupKeyboardShortcuts();
        }

        // Update the clock
        function updateClock() {
            const now = new Date();
            elements.currentTime.textContent = now.toLocaleTimeString();
        }

        // Populate customer select dropdown
        function populateCustomerSelect() {
            elements.customerSelect.innerHTML = `<option value="">${translations[state.currentLanguage].walkInCustomer}</option>`;
            
            state.customers.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.id;
                option.textContent = `${customer.name} (${customer.phone})`;
                elements.customerSelect.appendChild(option);
            });
        }

        // Populate product dropdown
        function populateProductDropdown(filterText = '', category = 'all') {
            elements.productDropdown.innerHTML = '';
            
            const filteredProducts = state.products.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(filterText.toLowerCase());
                const matchesCategory = category === 'all' || product.category === category;
                return matchesSearch && matchesCategory;
            });
            
            if (filteredProducts.length === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'p-3 text-gray-500 dark:text-gray-400 text-center';
                noResults.textContent = 'No products found';
                elements.productDropdown.appendChild(noResults);
            } else {
                filteredProducts.forEach(product => {
                    const productOption = document.createElement('div');
                    productOption.className = `flex items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors ${product.stock === 0 ? 'opacity-50' : ''}`;
                    productOption.dataset.id = product.id;
                    
                    productOption.innerHTML = `
                        <img src="${product.image}" alt="${product.name}" class="w-10 h-10 rounded-md mr-3">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 dark:text-white">${product.name}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">$${product.price.toFixed(2)}</div>
                        </div>
                        ${product.stock < 5 && product.stock > 0 ? 
                            `<span class="text-xs bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 px-2 py-1 rounded">${translations[state.currentLanguage].lowStock}</span>` : 
                            ''}
                        ${product.stock === 0 ? 
                            `<span class="text-xs bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-2 py-1 rounded">${translations[state.currentLanguage].outOfStock}</span>` : 
                            ''}
                    `;
                    
                    productOption.addEventListener('click', () => {
                        if (product.stock > 0) {
                            selectProduct(product);
                            elements.productDropdown.classList.add('hidden');
                        }
                    });
                    
                    elements.productDropdown.appendChild(productOption);
                });
            }
            
            // Show dropdown if there are results and search is active
            if (filterText && filteredProducts.length > 0) {
                elements.productDropdown.classList.remove('hidden');
            }
        }

        // Select a product
        function selectProduct(product) {
            state.selectedProduct = product;
            elements.selectedProduct.textContent = `${product.name} - $${product.price.toFixed(2)}`;
            elements.addToCart.disabled = false;
        }

        // Add product to cart
        function addProductToCart() {
            if (!state.selectedProduct) return;
            
            const existingItem = state.cart.find(item => item.id === state.selectedProduct.id);
            
            if (existingItem) {
                if (existingItem.quantity < state.selectedProduct.stock) {
                    existingItem.quantity += 1;
                } else {
                    showNotification('Cannot add more items, stock limit reached', 'error');
                    return;
                }
            } else {
                if (state.selectedProduct.stock > 0) {
                    state.cart.push({
                        ...state.selectedProduct,
                        quantity: 1
                    });
                } else {
                    showNotification('Product is out of stock', 'error');
                    return;
                }
            }
            
            updateCartDisplay();
            showNotification('Product added to cart', 'success');
        }

        // Update cart display
        function updateCartDisplay() {
            elements.cartItems.innerHTML = '';
            
            if (state.cart.length === 0) {
                elements.cartItems.innerHTML = `<div class="p-8 text-center text-gray-500 dark:text-gray-400">${translations[state.currentLanguage].cartEmpty}</div>`;
                elements.clearCart.disabled = true;
                elements.processPayment.disabled = true;
            } else {
                state.cart.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.className = 'flex items-center p-3 border-b border-gray-200 dark:border-gray-700 fade-in';
                    
                    cartItem.innerHTML = `
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 dark:text-white">${item.name}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">$${item.price.toFixed(2)}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="decrease-quantity w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" data-id="${item.id}">-</button>
                            <span class="quantity w-8 text-center font-medium text-gray-800 dark:text-white">${item.quantity}</span>
                            <button class="increase-quantity w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" data-id="${item.id}">+</button>
                            <button class="remove-item w-8 h-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 transition-colors" data-id="${item.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="ml-4 text-right">
                            <div class="font-medium text-gray-800 dark:text-white">$${(item.price * item.quantity).toFixed(2)}</div>
                        </div>
                    `;
                    
                    elements.cartItems.appendChild(cartItem);
                });
                
                elements.clearCart.disabled = false;
                elements.processPayment.disabled = false;
            }
            
            updateCartTotals();
        }

        // Update cart totals
        function updateCartTotals() {
            const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            let discountAmount = 0;
            if (state.discount.type === 'percentage') {
                discountAmount = subtotal * (state.discount.value / 100);
            } else if (state.discount.type === 'fixed') {
                discountAmount = state.discount.value;
            }
            
            const total = Math.max(0, subtotal - discountAmount);
            
            elements.subtotal.textContent = `$${subtotal.toFixed(2)}`;
            elements.discountAmount.textContent = `-$${discountAmount.toFixed(2)}`;
            elements.total.textContent = `$${total.toFixed(2)}`;
            
            // Update change calculation if cash payment is selected
            if (state.selectedPaymentMethod === 'cash' && elements.amountTendered.value) {
                calculateChange();
            }
        }

        // Calculate change for cash payment
        function calculateChange() {
            const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountAmount = 0;
            if (state.discount.type === 'percentage') {
                discountAmount = subtotal * (state.discount.value / 100);
            } else if (state.discount.type === 'fixed') {
                discountAmount = state.discount.value;
            }
            const total = Math.max(0, subtotal - discountAmount);
            
            const amountTendered = parseFloat(elements.amountTendered.value) || 0;
            const change = amountTendered - total;
            
            if (change >= 0) {
                elements.changeAmount.textContent = `$${change.toFixed(2)}`;
                elements.changeAmount.className = 'font-medium text-green-600 dark:text-green-400';
            } else {
                elements.changeAmount.textContent = `-$${Math.abs(change).toFixed(2)}`;
                elements.changeAmount.className = 'font-medium text-red-600 dark:text-red-400';
            }
        }

        // Update transaction history
        function updateTransactionHistory() {
            elements.historyBody.innerHTML = '';
            
            if (state.transactions.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `<td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No transactions yet</td>`;
                elements.historyBody.appendChild(emptyRow);
            } else {
                // Show only the last 10 transactions
                const recentTransactions = state.transactions.slice(-10).reverse();
                
                recentTransactions.forEach(transaction => {
                    const row = document.createElement('tr');
                    row.className = 'fade-in';
                    
                    row.innerHTML = `
                        <td class="px-4 py-2 text-sm text-gray-800 dark:text-white">${transaction.id}</td>
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">${new Date(transaction.timestamp).toLocaleTimeString()}</td>
                        <td class="px-4 py-2 text-sm text-gray-800 dark:text-white">$${transaction.total.toFixed(2)}</td>
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 capitalize">${transaction.paymentMethod}</td>
                    `;
                    
                    elements.historyBody.appendChild(row);
                });
            }
        }

        // Apply translations to the UI
        function applyTranslations() {
            const t = translations[state.currentLanguage];
            
            // Update all text content based on data-translate attributes
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                if (t[key]) {
                    element.textContent = t[key];
                }
            });
            
            // Update placeholders
            if (elements.productSearch) {
                elements.productSearch.placeholder = t.searchPlaceholder;
            }
            
            // Update selected product text
            if (state.selectedProduct) {
                elements.selectedProduct.textContent = `${state.selectedProduct.name} - $${state.selectedProduct.price.toFixed(2)}`;
            } else {
                elements.selectedProduct.textContent = t.noProductSelected;
            }
            
            // Update cart empty message
            if (state.cart.length === 0) {
                elements.cartItems.innerHTML = `<div class="p-8 text-center text-gray-500 dark:text-gray-400">${t.cartEmpty}</div>`;
            }
        }

        // Show notification
        function showNotification(message, type = 'info') {
            // Remove existing notification
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create new notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 fade-in ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Process payment
        function processPayment() {
            if (state.cart.length === 0) {
                showNotification('Cart is empty', 'error');
                return;
            }
            
            const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let discountAmount = 0;
            if (state.discount.type === 'percentage') {
                discountAmount = subtotal * (state.discount.value / 100);
            } else if (state.discount.type === 'fixed') {
                discountAmount = state.discount.value;
            }
            const total = Math.max(0, subtotal - discountAmount);
            
            // Validate payment based on method
            if (state.selectedPaymentMethod === 'cash') {
                const amountTendered = parseFloat(elements.amountTendered.value) || 0;
                if (amountTendered < total) {
                    showNotification(translations[state.currentLanguage].paymentInsufficient, 'error');
                    elements.paymentStatus.textContent = translations[state.currentLanguage].paymentInsufficient;
                    elements.paymentStatus.className = 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
                    elements.paymentStatus.classList.remove('hidden');
                    return;
                }
            } else if (state.selectedPaymentMethod === 'card' || state.selectedPaymentMethod === 'mobile') {
                if (!elements.transactionId.value.trim()) {
                    showNotification('Transaction ID is required', 'error');
                    return;
                }
            } else {
                showNotification('Please select a payment method', 'error');
                return;
            }
            
            // Create transaction
            const transaction = {
                id: 'T' + Date.now(),
                timestamp: new Date().toISOString(),
                items: [...state.cart],
                subtotal,
                discount: state.discount,
                total,
                paymentMethod: state.selectedPaymentMethod,
                customerId: elements.customerSelect.value ? parseInt(elements.customerSelect.value) : null,
                note: elements.transactionNote.value
            };
            
            // Add to transaction history
            state.transactions.push(transaction);
            localStorage.setItem('pos-transactions', JSON.stringify(state.transactions));
            
            // Update product stock
            state.cart.forEach(cartItem => {
                const product = state.products.find(p => p.id === cartItem.id);
                if (product) {
                    product.stock -= cartItem.quantity;
                }
            });
            localStorage.setItem('pos-products', JSON.stringify(state.products));
            
            // Show success message
            showNotification(translations[state.currentLanguage].paymentSuccess, 'success');
            elements.paymentStatus.textContent = translations[state.currentLanguage].paymentSuccess;
            elements.paymentStatus.className = 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200';
            elements.paymentStatus.classList.remove('hidden');
            
            // Generate receipt
            generateReceipt(transaction);
            
            // Clear cart and reset form
            setTimeout(() => {
                state.cart = [];
                state.discount = { type: null, value: 0 };
                updateCartDisplay();
                elements.paymentForm.classList.add('hidden');
                elements.paymentStatus.classList.add('hidden');
                elements.transactionNote.value = '';
                updateTransactionHistory();
                populateProductDropdown(); // Refresh product list to show updated stock
            }, 2000);
        }

        // Generate receipt
        function generateReceipt(transaction) {
            let receiptText = `=== MODERN POS RECEIPT ===\n`;
            receiptText += `Transaction ID: ${transaction.id}\n`;
            receiptText += `Date: ${new Date(transaction.timestamp).toLocaleString()}\n`;
            receiptText += `Cashier: John Smith\n`;
            receiptText += `----------------------------\n`;
            
            transaction.items.forEach(item => {
                receiptText += `${item.name} x${item.quantity} - $${(item.price * item.quantity).toFixed(2)}\n`;
            });
            
            receiptText += `----------------------------\n`;
            receiptText += `Subtotal: $${transaction.subtotal.toFixed(2)}\n`;
            
            if (transaction.discount.type) {
                const discountValue = transaction.discount.type === 'percentage' 
                    ? `${transaction.discount.value}%` 
                    : `$${transaction.discount.value.toFixed(2)}`;
                receiptText += `Discount (${discountValue}): -$${(transaction.subtotal - transaction.total).toFixed(2)}\n`;
            }
            
            receiptText += `TOTAL: $${transaction.total.toFixed(2)}\n`;
            receiptText += `Payment Method: ${transaction.paymentMethod.toUpperCase()}\n`;
            
            if (transaction.note) {
                receiptText += `Note: ${transaction.note}\n`;
            }
            
            receiptText += `============================\n`;
            receiptText += `Thank you for your business!\n`;
            
            // In a real application, this would send to a printer
            // For simulation, we'll just log it and show an alert
            console.log(receiptText);
            alert('Receipt generated and sent to printer (check console for details)');
        }

        // Set up event listeners
        function setupEventListeners() {
            // Theme toggle
            elements.themeToggle.addEventListener('click', () => {
                state.isDarkMode = !state.isDarkMode;
                if (state.isDarkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('pos-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('pos-theme', 'light');
                }
            });
            
            // Logout button
            elements.logoutBtn.addEventListener('click', () => {
                if (confirm(translations[state.currentLanguage].confirmLogout)) {
                    // In a real app, this would redirect to login
                    alert('Logged out successfully');
                }
            });
            
            // Product search
            elements.productSearch.addEventListener('input', (e) => {
                const searchText = e.target.value;
                const activeCategory = document.querySelector('.category-filter.active').dataset.category;
                
                if (searchText) {
                    populateProductDropdown(searchText, activeCategory);
                    elements.productDropdown.classList.remove('hidden');
                } else {
                    elements.productDropdown.classList.add('hidden');
                }
            });
            
            // Product search focus
            elements.productSearch.addEventListener('focus', () => {
                if (elements.productSearch.value) {
                    const activeCategory = document.querySelector('.category-filter.active').dataset.category;
                    populateProductDropdown(elements.productSearch.value, activeCategory);
                    elements.productDropdown.classList.remove('hidden');
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!elements.productSearch.contains(e.target) && !elements.productDropdown.contains(e.target)) {
                    elements.productDropdown.classList.add('hidden');
                }
            });
            
            // Category filters
            elements.categoryFilters.forEach(filter => {
                filter.addEventListener('click', () => {
                    elements.categoryFilters.forEach(f => f.classList.remove('active', 'bg-primary-100', 'dark:bg-primary-900', 'text-primary-700', 'dark:text-primary-300'));
                    filter.classList.add('active', 'bg-primary-100', 'dark:bg-primary-900', 'text-primary-700', 'dark:text-primary-300');
                    
                    const category = filter.dataset.category;
                    const searchText = elements.productSearch.value;
                    
                    if (searchText) {
                        populateProductDropdown(searchText, category);
                    }
                });
            });
            
            // Add to cart
            elements.addToCart.addEventListener('click', addProductToCart);
            
            // View toggle
            elements.viewToggle.addEventListener('click', () => {
                if (state.currentView === 'dropdown') {
                    state.currentView = 'grid';
                    elements.viewToggle.textContent = translations[state.currentLanguage].grid;
                    elements.dropdownView.classList.add('hidden');
                    elements.gridView.classList.remove('hidden');
                } else {
                    state.currentView = 'dropdown';
                    elements.viewToggle.textContent = translations[state.currentLanguage].dropdown;
                    elements.gridView.classList.add('hidden');
                    elements.dropdownView.classList.remove('hidden');
                }
            });
            
            // Cart event delegation
            elements.cartItems.addEventListener('click', (e) => {
                const target = e.target.closest('button');
                if (!target) return;
                
                const productId = parseInt(target.dataset.id);
                const cartItem = state.cart.find(item => item.id === productId);
                
                if (target.classList.contains('increase-quantity')) {
                    if (cartItem.quantity < cartItem.stock) {
                        cartItem.quantity += 1;
                        updateCartDisplay();
                    } else {
                        showNotification('Cannot add more items, stock limit reached', 'error');
                    }
                } else if (target.classList.contains('decrease-quantity')) {
                    if (cartItem.quantity > 1) {
                        cartItem.quantity -= 1;
                        updateCartDisplay();
                    }
                } else if (target.classList.contains('remove-item')) {
                    state.cart = state.cart.filter(item => item.id !== productId);
                    updateCartDisplay();
                    showNotification('Product removed from cart', 'info');
                }
            });
            
            // Clear cart
            elements.clearCart.addEventListener('click', () => {
                if (confirm(translations[state.currentLanguage].confirmClearCart)) {
                    state.cart = [];
                    state.discount = { type: null, value: 0 };
                    updateCartDisplay();
                    showNotification('Cart cleared', 'info');
                }
            });
            
            // Apply discount button
            elements.applyDiscount.addEventListener('click', () => {
                elements.discountModal.classList.remove('hidden');
            });
            
            // Quick discount button
            elements.quickDiscount.addEventListener('click', () => {
                // Apply 10% discount as default quick discount
                state.discount = { type: 'percentage', value: 10 };
                updateCartTotals();
                showNotification('10% discount applied', 'success');
            });
            
            // Void transaction
            elements.voidTransaction.addEventListener('click', () => {
                if (state.transactions.length === 0) {
                    showNotification('No transactions to void', 'error');
                    return;
                }
                
                if (confirm(translations[state.currentLanguage].confirmVoid)) {
                    const voidedTransaction = state.transactions.pop();
                    localStorage.setItem('pos-transactions', JSON.stringify(state.transactions));
                    
                    // Restore product stock
                    voidedTransaction.items.forEach(item => {
                        const product = state.products.find(p => p.id === item.id);
                        if (product) {
                            product.stock += item.quantity;
                        }
                    });
                    localStorage.setItem('pos-products', JSON.stringify(state.products));
                    
                    updateTransactionHistory();
                    populateProductDropdown(); // Refresh product list
                    showNotification('Last transaction voided', 'info');
                }
            });
            
            // Open drawer
            elements.openDrawer.addEventListener('click', () => {
                // In a real application, this would trigger a hardware signal
                showNotification('Cash drawer opened', 'success');
            });
            
            // Payment methods
            elements.paymentMethods.forEach(method => {
                method.addEventListener('click', () => {
                    // Remove active state from all methods
                    elements.paymentMethods.forEach(m => {
                        m.classList.remove('bg-primary-500', 'text-white', 'border-primary-500');
                        m.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-white');
                    });
                    
                    // Add active state to selected method
                    method.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-white');
                    method.classList.add('bg-primary-500', 'text-white', 'border-primary-500');
                    
                    state.selectedPaymentMethod = method.dataset.method;
                    elements.paymentForm.classList.remove('hidden');
                    
                    // Show appropriate payment form
                    if (state.selectedPaymentMethod === 'cash') {
                        elements.cashPayment.classList.remove('hidden');
                        elements.digitalPayment.classList.add('hidden');
                    } else {
                        elements.cashPayment.classList.add('hidden');
                        elements.digitalPayment.classList.remove('hidden');
                    }
                });
            });
            
            // Amount tendered input
            elements.amountTendered.addEventListener('input', calculateChange);
            
            // Process payment
            elements.processPayment.addEventListener('click', processPayment);
            
            // Discount modal
            elements.cancelDiscount.addEventListener('click', () => {
                elements.discountModal.classList.add('hidden');
            });
            
            elements.applyDiscountBtn.addEventListener('click', () => {
                const discountType = document.querySelector('input[name="discount-type"]:checked').value;
                const discountValue = parseFloat(elements.discountValue.value);
                
                if (isNaN(discountValue) || discountValue <= 0) {
                    showNotification('Please enter a valid discount value', 'error');
                    return;
                }
                
                if (discountType === 'percentage' && discountValue > 100) {
                    showNotification('Discount percentage cannot exceed 100%', 'error');
                    return;
                }
                
                const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                
                if (discountType === 'fixed' && discountValue > subtotal) {
                    showNotification('Discount amount cannot exceed subtotal', 'error');
                    return;
                }
                
                state.discount = { type: discountType, value: discountValue };
                updateCartTotals();
                elements.discountModal.classList.add('hidden');
                showNotification('Discount applied successfully', 'success');
            });
            
            // Quick discount buttons in modal
            elements.quickDiscountBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    elements.discountValue.value = btn.dataset.value;
                });
            });
            
            // Toggle transaction history
            elements.toggleHistory.addEventListener('click', () => {
                elements.historyContent.classList.toggle('hidden');
            });
            
            // Language toggle
            elements.languageToggle.addEventListener('click', () => {
                state.currentLanguage = state.currentLanguage === 'en' ? 'es' : 'en';
                localStorage.setItem('pos-language', state.currentLanguage);
                applyTranslations();
            });
            
            // Help button
            elements.helpBtn.addEventListener('click', () => {
                elements.helpModal.classList.remove('hidden');
            });
            
            elements.closeHelp.addEventListener('click', () => {
                elements.helpModal.classList.add('hidden');
            });
        }

        // Set up keyboard shortcuts
        function setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ctrl+P - Add product to cart
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    addProductToCart();
                }
                
                // Ctrl+C - Clear cart
                if (e.ctrlKey && e.key === 'c') {
                    e.preventDefault();
                    if (state.cart.length > 0) {
                        if (confirm(translations[state.currentLanguage].confirmClearCart)) {
                            state.cart = [];
                            state.discount = { type: null, value: 0 };
                            updateCartDisplay();
                            showNotification('Cart cleared', 'info');
                        }
                    }
                }
                
                // Ctrl+D - Apply quick discount
                if (e.ctrlKey && e.key === 'd') {
                    e.preventDefault();
                    state.discount = { type: 'percentage', value: 10 };
                    updateCartTotals();
                    showNotification('10% discount applied', 'success');
                }
                
                // Ctrl+Enter - Process payment
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    processPayment();
                }
                
                // Ctrl+H - Show help
                if (e.ctrlKey && e.key === 'h') {
                    e.preventDefault();
                    elements.helpModal.classList.remove('hidden');
                }
            });
        }

        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>