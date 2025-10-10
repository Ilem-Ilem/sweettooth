<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>
            <flux:navlist.item icon="shield-check" :href="route('super-admin.roles.index')"
                :current="request()->routeIs('super-admin.roles.*')" wire:navigate>{{ __('Roles & Permissions') }}
            </flux:navlist.item>

            <flux:navlist.group :heading="__('branch Management')" expandable
                :expanded="request()->routeIs('super-admin.branches.*')? true : false" class="grid">
                <flux:navlist.item icon="building-office-2" :href="route('super-admin.branches.index')"
                    :current="request()->routeIs('super-admin.branches.index')" wire:navigate>{{ __('All Branches') }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-office-2" :href="route('super-admin.branches.deleted')"
                    :current="request()->routeIs('super-admin.branches.deleted')" wire:navigate>
                    {{ __('Deleted Branches') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Organization Structure')" expandable
                :expanded="request()->routeIs('super-admin.department*') || request()->routeIs('super-admin.positions.*') ? true : false"
                class="grid" icon='building-office'>
                <flux:navlist.item icon="tag" :href="route('super-admin.department-categories.index')"
                    :current="request()->routeIs('super-admin.department-categories.*')" wire:navigate>
                    {{ __('Department Categories') }}
                </flux:navlist.item>
                <flux:navlist.item icon="building-storefront" :href="route('super-admin.departments.index')"
                    :current="request()->routeIs('super-admin.departments.*')" wire:navigate>
                    {{ __('Departments') }}
                </flux:navlist.item>

            </flux:navlist.group>

            <flux:navlist.group :heading="__('Employee Management')" expandable
                :expanded="request()->routeIs('super-admin.employee.*') || request()->routeIs('super-admin.assignments.*') ? true : false"
                class="grid" icon='users'>
                <flux:navlist.item icon="user" :href="route('super-admin.employee.index')"
                    :current="request()->routeIs('super-admin.employee.index')" wire:navigate>{{ __('All Employees') }}
                </flux:navlist.item>
                <flux:navlist.item icon="user-plus" :href="route('super-admin.employee.create')"
                    :current="request()->routeIs('super-admin.employee.create')" wire:navigate>
                    {{ __('Create Employee') }}
                </flux:navlist.item>
               <flux:navlist.item icon="briefcase" :href="route('super-admin.role-assignments.index')"
                    :current="request()->routeIs('super-admin.role-assignments.*')" wire:navigate>
                    {{ __('Positions') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Inventory')">
               <flux:navlist.group :heading="__('Inventory')" expandable
                :expanded="request()->routeIs('super-admin.inventory.*') ? true : false" class="grid" icon='cube'>
                <flux:navlist.item icon="squares-2x2" :href="route('super-admin.inventory.items')"
                    :current="request()->routeIs('super-admin.inventory.items')" wire:navigate>{{ __('Items') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cube-transparent" :href="route('super-admin.inventory.stocks')"
                    :current="request()->routeIs('super-admin.inventory.stocks')" wire:navigate>{{ __('Stock Levels') }}
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
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
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
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
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


    <x-toast />
    <x-dialog />
    {{ $slot }}

    @fluxScripts
</body>

</html>
