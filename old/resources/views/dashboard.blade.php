<x-layouts.app :title="__('Dashboard')">
    <div 
    x-data="{ darkMode: false }" 
    :class="darkMode ? 'dark' : ''" 
    class="min-h-screen bg-zinc-100 dark:bg-zinc-900 p-6"
>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Dashboard</h1>
        <button 
            @click="darkMode = !darkMode" 
            class="px-3 py-1 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition"
        >
            Toggle Theme
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid gap-6 md:grid-cols-3">
        <!-- Branches -->
        <div class="flex flex-col justify-between bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <!-- Branch Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-800 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M6 3v12M18 9v12M6 15h12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Branches</h2>
                    <p class="text-2xl font-bold text-zinc-700 dark:text-zinc-300">12</p>
                </div>
            </div>
            <a href="#" class="mt-4 inline-block text-center w-full py-2 rounded-lg bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 font-medium hover:opacity-90 transition">
                View Branches
            </a>
        </div>

        <!-- Staff -->
        <div class="flex flex-col justify-between bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <!-- Staff Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-800 dark:text-zinc-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M16 14c2.21 0 4 1.79 4 4v2H4v-2c0-2.21 1.79-4 4-4m4-2a4 4 0 110-8 4 4 0 010 8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Staff</h2>
                    <p class="text-2xl font-bold text-zinc-700 dark:text-zinc-300">45</p>
                </div>
            </div>
            <a href="#" class="mt-4 inline-block text-center w-full py-2 rounded-lg bg-zinc-900 dark:bg-zinc-200 text-white dark:text-zinc-900 font-medium hover:opacity-90 transition">
                Manage Staff
            </a>
        </div>

        <!-- Extra Placeholder Card -->
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-5 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
            Placeholder for Reports / Graph
        </div>
    </div>
</div>

</x-layouts.app>
