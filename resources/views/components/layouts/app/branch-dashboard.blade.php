<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ branch_route('branch-dashboard.index') }}"
            class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>


        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="branch_route('branch-dashboard.index')"
                    :current="request()->routeIs('branch-dashboard.index')" wire:navigate>{{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Organization')" class="grid">
                <flux:navlist.group :heading="__('Organization')" expandable
                    :expanded="request()->routeIs('branch-dashboard.branch.departments.*') || request()->routeIs('branch-dashboard.branch.departments.*') ? true : false"
                    class="grid">
                    <flux:navlist.item icon="tag"
                        :href="branch_route('branch-dashboard.branch.departments.category')"
                        :current="request()->routeIs('branch-dashboard.branch.departments.category')" wire:navigate>
                        {{ __('Department Categories') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-storefront"
                        :href="branch_route('branch-dashboard.branch.departments.index')"
                        :current="request()->routeIs('branch-dashboard.branch.departments.index')" wire:navigate>
                        {{ __('Departments') }}
                    </flux:navlist.item>

                </flux:navlist.group>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Employee Management')" expandable
                :expanded="request()->routeIs('branch-dashboard.employees.*') || request()->routeIs('branch-dashboard.assignments.*') ? true : false"
                class="grid" icon='users'>
                <flux:navlist.item icon="user" :href="branch_route('branch-dashboard.employees.index')"
                    :current="request()->routeIs('branch-dashboard.employees.index')" wire:navigate>
                    {{ __('All Employees') }}
                </flux:navlist.item>
                <flux:navlist.item icon="user-plus" :href="branch_route('branch-dashboard.employee.create')"
                    :current="request()->routeIs('branch-dashboard.employee.create')" wire:navigate>
                    {{ __('Create Employee') }}
                </flux:navlist.item>
                {{-- <flux:navlist.item icon="briefcase" :href="branch_route('branch-dashboard.role-assignments.index')"
                    :current="request()->routeIs('branch-dashboard.role-assignments.*')" wire:navigate>
                    {{ __('Positions') }}
                </flux:navlist.item> --}}
            </flux:navlist.group>


            <flux:navlist.group :heading="__('Inventory')" icon='cube'>
                <flux:navlist.group :heading="__('Inventory Management')" expandable
                    :expanded="request()->routeIs('branch-dashboard.inventory.*') ? true : false" class="grid"
                    icon='cube'>
                    <flux:navlist.item icon="squares-2x2" :href="branch_route('branch-dashboard.inventory.items')"
                        :current="request()->routeIs('branch-dashboard.inventory.items')" wire:navigate>
                        {{ __('Items') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck" :href="branch_route('branch-dashboard.inventory.purchases')"
                        :current="request()->routeIs('branch-dashboard.inventory.purchases')" wire:navigate>
                        {{ __('Purchases') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="cube-transparent"
                        :href="branch_route('branch-dashboard.inventory.stocks')"
                        :current="request()->routeIs('branch-dashboard.inventory.stocks')" wire:navigate>
                        {{ __('Stock Levels') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="cube-transparent"
                        :href="branch_route('branch-dashboard.inventory.health-checks')"
                        :current="request()->routeIs('branch-dashboard.inventory.health-checks')" wire:navigate>
                        {{ __('Health Check') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list"
                        :href="branch_route('branch-dashboard.inventory.item-requests')"
                        :current="request()->routeIs('branch-dashboard.inventory.item-requests')" wire:navigate>
                        {{ __('Item Requests') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck"
                        :href="branch_route('branch-dashboard.inventory.item-dispatches')"
                        :current="request()->routeIs('branch-dashboard.inventory.item-dispatches')" wire:navigate>
                        {{ __('Dispatches') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="chart-bar"
                        :href="branch_route('branch-dashboard.inventory.stock-movements')"
                        :current="request()->routeIs('branch-dashboard.inventory.stock-movements')" wire:navigate>
                        {{ __('Stock Movements') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>


            <flux:navlist.group :heading="__('Analytics')" expandable
                :expanded="request()->routeIs('branch-dashboard.analytics.*') ? true : false" class="grid"
                icon='chart-bar-square'>
                <flux:navlist.item icon="squares-plus" :href="branch_route('branch-dashboard.analytics.overview')"
                    :current="request()->routeIs('branch-dashboard.analytics.overview')">
                    {{ __('Overview Dashboard') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cube" :href="branch_route('branch-dashboard.analytics.stock-level')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-level')" wire:navigate>
                    {{ __('Stock Levels') }}
                </flux:navlist.item>
                <flux:navlist.item icon="arrows-right-left"
                    :href="branch_route('branch-dashboard.analytics.stock-movement')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-movement')">
                    {{ __('Stock Movement') }}
                </flux:navlist.item>
                <flux:navlist.item icon="shopping-cart" :href="branch_route('branch-dashboard.analytics.purchase')"
                    :current="request()->routeIs('branch-dashboard.analytics.purchase')">
                    {{ __('Purchases') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list"
                    :href="branch_route('branch-dashboard.analytics.request-dispatch')"
                    :current="request()->routeIs('branch-dashboard.analytics.request-dispatch')">
                    {{ __('Requests & Dispatch') }}
                </flux:navlist.item>
                <flux:navlist.item icon="currency-dollar"
                    :href="branch_route('branch-dashboard.analytics.stock-valuation')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-valuation')">
                    {{ __('Stock Valuation') }}
                </flux:navlist.item>
     
                <flux:navlist.item icon="bell-alert" :href="branch_route('branch-dashboard.analytics.alerts')"
                    :current="request()->routeIs('branch-dashboard.analytics.alerts')">
                    {{ __('Alerts Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>


            <flux:navlist.group :heading="__('Production')">
                <flux:navlist.item icon="tag" :href="branch_route('branch-dashboard.production.product-types')"
                    :current="request()->routeIs('branch-dashboard.production.product-types')" wire:navigate>
                    {{ __('Product Types') }}
                </flux:navlist.item>
                <flux:navlist.group :heading="__('Kitchen')" expandable
                    :expanded="request()->routeIs('branch-dashboard.production.*') ? true : false" class="grid"
                    icon='cog'>

                    <flux:navlist.item icon="cube" :href="branch_route('branch-dashboard.production.products')"
                        :current="request()->routeIs('branch-dashboard.production.products')" wire:navigate>
                        {{ __('Products') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list"
                        :href="branch_route('branch-dashboard.production.recipes.index')"
                        :current="request()->routeIs('branch-dashboard.production.recipes.*')" wire:navigate>
                        {{ __('Recipes') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="home-modern"
                        :href="branch_route('branch-dashboard.production.kitchen.index')"
                        :current="request()->routeIs('branch-dashboard.production.kitchen.index')" wire:navigate>
                        {{ __('Kitchen Dashboard') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="eye"
                        :href="branch_route('branch-dashboard.production.kitchen.stock-monitor')"
                        :current="request()->routeIs('branch-dashboard.production.kitchen.stock-monitor')"
                        wire:navigate>
                        {{ __('Production Monitor') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="document-text"
                        :href="branch_route('branch-dashboard.production.request.index')"
                        :current="request()->routeIs('branch-dashboard.production.request.*')" wire:navigate>
                        {{ __('Production Requests') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="chart-bar"
                        :href="branch_route('branch-dashboard.production.daily-produce.index')"
                        :current="request()->routeIs('branch-dashboard.production.daily-produce.*')" wire:navigate>
                        {{ __('Daily Produce') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="beaker"
                        :href="branch_route('branch-dashboard.production.raw-material-tracking')"
                        :current="request()->routeIs('branch-dashboard.production.raw-material-tracking')"
                        wire:navigate>
                        {{ __('Raw Material Tracking') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Sales')" class="grid">
                <flux:navlist.item icon="clipboard-document-check"
                    :href="branch_route('branch-dashboard.sales-dashboard.stock-opening.index')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.stock-opening.*')" wire:navigate>
                    {{ __('Stock Opening') }}
                </flux:navlist.item>

                <flux:navlist.item icon="clipboard-document-check"
                    :href="branch_route('branch-dashboard.sales-dashboard.stock-monitor')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.stock-monitor')" wire:navigate>
                    Monitor Product Stock
                </flux:navlist.item>

                <flux:navlist.group :heading="__('POS System')" class="grid" expandable>
                    <flux:navlist.item icon="clipboard-document-check"
                        :href="branch_route('branch-dashboard.sales-dashboard.pos.index')"
                        :current="request()->routeIs('branch-dashboard.sales-dashboard.pos.*')"
                        wire:navigate>
                        {{ __('POS') }}
                    </flux:navlist.item>


                </flux:navlist.group>
            </flux:navlist.group>

        </flux:navlist>
        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="branch_route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ branch_route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="branch_route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ branch_route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{-- navbar section --}}
    <flux:header sticky container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Sidebar Toggle -->
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <!-- Navbar Left -->
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <!-- Clock In Section + Digital Clock -->
        <div class="flex items-center gap-6 text-sm text-zinc-700 dark:text-zinc-200 me-3">
            <!-- Livewire Clock In/Out Component -->
            @livewire('branch-dashboard.header-clock-in-out', ['b_id' => request()->query('b_id')])

            <!-- Digital Clock -->
            <div x-data="{ currentTime: '' }" x-init="setInterval(() => {
                const now = new Date();
                currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }, 1000);"
                class="font-mono text-base md:text-lg tracking-widest" x-text="currentTime">
            </div>
        </div>

        <!-- Search Navbar -->
        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
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
                            <input x-ref="searchInput" type="text" x-model="query"
                                placeholder="Type to search..."
                                class="w-full border border-zinc-300 dark:border-zinc-700 rounded-lg p-3 text-base focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-zinc-800 dark:text-zinc-100" />
                        </form>

                        <!-- Footer -->
                        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400 text-right">
                            Press <kbd class="px-1 py-0.5 border rounded">Esc</kbd> to close
                        </p>
                    </div>
                </div>
            </div>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>

    </flux:header>

    <flux:main>
        <div wire:loading
            class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span>Updating...</span>
        </div>
        <x-toast />
        <x-dialog />
        {{ $slot }}
    </flux:main>

    @fluxScripts
    @stack('scripts')
</body>

</html>
