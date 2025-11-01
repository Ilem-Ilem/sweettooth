<div class="space-y-6">
    {{-- Department Selector --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Production Departments</h2>

        <div class="flex flex-wrap gap-3">
            @forelse($departments as $department)
                <button
                    wire:click="selectDepartment({{ $department->id }})"
                    class="px-4 py-2 rounded-lg font-medium transition-all duration-200 flex flex-col items-start
                        {{ $selectedDepartment == $department->id
                            ? 'bg-blue-600 text-white shadow-md'
                            : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600'
                        }}"
                >
                    <span>{{ $department->name }}</span>
                    @if($department->category)
                        <span class="text-xs opacity-70">{{ $department->category->name }}</span>
                    @endif
                </button>
            @empty
                <div class="text-zinc-500 dark:text-zinc-400">
                    No departments available for this branch.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Department Pages Grid --}}
    @if($selectedDepartment && count($pages) > 0)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                {{ $departments->firstWhere('id', $selectedDepartment)?->name }} Pages
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($pages as $page)
                    <a
                        href="{{ route($page->route_name) }}"
                        class="group flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-600 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-md transition-all duration-200"
                    >
                        @if($page->icon)
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                {{-- <x-dynamic-component :component="$page->icon" class="w-6 h-6" /> --}}i
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                {{ $page->name }}
                            </h4>
                        </div>

                        <svg class="w-5 h-5 text-zinc-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif($selectedDepartment)
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6">
            <div class="text-center text-zinc-500 dark:text-zinc-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-lg font-medium">No pages available for this department</p>
                <p class="text-sm mt-2">Pages will appear here once they are configured.</p>
            </div>
        </div>
    @endif
</div>
