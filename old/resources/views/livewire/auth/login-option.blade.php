<div>
    <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8 p-6">
        <!-- Admin Card -->
        <div class="cursor-pointer rounded-2xl shadow-lg bg-white dark:bg-zinc-800 p-6 hover:shadow-2xl transition flex flex-col justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 mb-3">Login as Admin</h2>
            <p class="text-gray-600 dark:text-zinc-300 mb-6">Access administrative tools, manage system settings, and oversee staff activity.</p>
          </div>
          <a class="mt-auto inline-block px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" wire:navigate href="{{ route('login') }}">Login</a>
        </div>

        <!-- Staff Card -->
        <div class="cursor-pointer rounded-2xl shadow-lg bg-white dark:bg-zinc-800 p-6 hover:shadow-2xl transition flex flex-col justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 mb-3">Login as Staff</h2>
            <p class="text-gray-600 dark:text-zinc-300 mb-6">Enter your staff portal to manage tasks, view schedules, and access resources.</p>
          </div>
       <a class="mt-auto inline-block px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400" wire:navigate href="{{ route('branch-login') }}">Login</a>
        </div>
      </div></div>
