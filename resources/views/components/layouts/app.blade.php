<x-layouts.app.sidebar :title="$title ?? null">
    <flux:header sticky container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            {{-- <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip> --}}
            <div x-data="{
                open: false,
                query: '',
                isMac: navigator.platform.toUpperCase().includes('MAC'),
                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                }
            }" x-init="window.addEventListener('keydown', e => {
                if ((isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    toggle();
                }
                if (e.key === 'Escape') open = false;
            });" class="relative">
                <button @click="toggle" type="button"
                    class="inline-flex items-center gap-3 px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-150">
                    <!-- Search icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-600 dark:text-zinc-300"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span class="text-sm text-zinc-700 dark:text-zinc-200">Search</span>


                    <kbd
                        class="ml-1 inline-flex items-center gap-1 rounded border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                        <span x-text="isMac ? '⌘' : 'Ctrl'"></span>
                        <span class="text-[10px] opacity-70">+</span>
                        <span>K</span>
                    </kbd>
                </button>


                <!-- Search Modal -->
                <div x-show="open" x-transition.opacity.duration.300ms @click.away="open = false"
                    class="fixed inset-0 bg-black/40 flex items-start justify-center pt-24 z-50" x-cloak>
                    <!-- Modal Box -->
                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-4 scale-95" x-trap.noscroll.inert="open"
                        class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl p-6 mx-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">Quick Search</h2>
                            <button @click="open = false"
                                class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Search Input -->
                        <form @submit.prevent="$dispatch('search', query); open = false">
                            <input x-ref="searchInput" type="text" x-model="query" placeholder="Type to search..."
                                class="w-full border border-zinc-300 dark:border-zinc-700 rounded-lg p-3 text-base focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-zinc-800 dark:text-zinc-100">
                        </form>

                        <!-- Footer -->
                        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400 text-right">Press <kbd
                                class="px-1 py-0.5 border rounded">Esc</kbd> to close</p>
                    </div>
                </div>
            </div>

        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            @php
                $user = auth()->user() ?? auth('employees')->user();
            @endphp
            <flux:profile class="cursor-pointer" :initials="$user->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $user->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $user->name }}</span>
                                <span class="truncate text-xs">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main>

        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
