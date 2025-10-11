<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ branch_route('branch-dashboard.index') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>


        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="branch_route('branch-dashboard.index')"
                    :current="request()->routeIs('branch-dashboard.index')" wire:navigate>{{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Organization Structure')" expandable
                :expanded="request()->routeIs('branch-dashboard.branch.departments.*') || request()->routeIs('branch-dashboard.branch.departments.*') ? true : false"
                class="grid" icon='building-office'>
                <flux:navlist.item icon="tag" :href="branch_route('branch-dashboard.branch.departments.category')"
                    :current="request()->routeIs('branch-dashboard.branch.departments.category')" wire:navigate>
                    {{ __('Department Categories') }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-storefront"
                    :href="branch_route('branch-dashboard.branch.departments.index')"
                    :current="request()->routeIs('branch-dashboard.branch.departments.index')" wire:navigate>
                    {{ __('Departments') }}
                </flux:navlist.item>

            </flux:navlist.group>

             <flux:navlist.group :heading="__('Employee Management')" expandable
                :expanded="request()->routeIs('branch-dashboard.employees.*') || request()->routeIs('branch-dashboard.assignments.*') ? true : false"
                class="grid" icon='users'>
                <flux:navlist.item icon="user" :href="branch_route('branch-dashboard.employees.index')"
                    :current="request()->routeIs('branch-dashboard.employees.index')" wire:navigate>{{ __('All Employees') }}
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


            <flux:navlist.group :heading="__('Inventory')" expandable
                :expanded="request()->routeIs('branch-dashboard.inventory.*') ? true : false" class="grid" icon='cube'>
                <flux:navlist.item icon="squares-2x2" :href="branch_route('branch-dashboard.inventory.items')"
                    :current="request()->routeIs('branch-dashboard.inventory.items')" wire:navigate>{{ __('Items') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cube-transparent" :href="branch_route('branch-dashboard.inventory.stocks')"
                    :current="request()->routeIs('branch-dashboard.inventory.stocks')" wire:navigate>{{ __('Stock Levels') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list" :href="branch_route('branch-dashboard.inventory.item-requests')"
                    :current="request()->routeIs('branch-dashboard.inventory.item-requests')" wire:navigate>{{ __('Item Requests') }}
                </flux:navlist.item>
                <flux:navlist.item icon="truck" :href="branch_route('branch-dashboard.inventory.item-dispatches')"
                    :current="request()->routeIs('branch-dashboard.inventory.item-dispatches')" wire:navigate>{{ __('Dispatches') }}
                </flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="branch_route('branch-dashboard.inventory.stock-movements')"
                    :current="request()->routeIs('branch-dashboard.inventory.stock-movements')" wire:navigate>{{ __('Stock Movements') }}
                </flux:navlist.item>
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
    <flux:main>
        <x-toast />
        <x-dialog />
        {{ $slot }}
    </flux:main>

    @fluxScripts
</body>

</html>
