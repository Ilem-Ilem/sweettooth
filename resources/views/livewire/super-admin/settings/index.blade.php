<div class="">
    <style>
        .active-tab {
            background-color: rgb(59 130 246);
            color: white;
        }
        .dark .active-tab {
            background-color: rgb(37 99 235);
        }
        .setting-group {
            border-left: 3px solid rgb(59 130 246);
        }
        .dark .setting-group {
            border-left-color: rgb(96 165 250);
        }
    </style>
</head>
<body class="h-full bg-zinc-100 dark:bg-zinc-900 transition-colors duration-300">
    <div x-data="{ darkMode: false }" x-init="darkMode = localStorage.getItem('darkMode') === 'true'; if(darkMode) document.documentElement.classList.add('dark')" class="min-h-screen">
        <!-- Header -->
        <header class="bg-white dark:bg-zinc-800 shadow transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">Business Configuration</h1>
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark')" class="ml-4 p-2 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg overflow-hidden transition-colors duration-300">
                <div class="flex flex-col md:flex-row">
                    <!-- Vertical Tabs -->
                    <div class="w-full md:w-1/4 bg-zinc-50 dark:bg-zinc-700 p-4 border-r border-zinc-200 dark:border-zinc-600 transition-colors duration-300">
                        <nav class="space-y-1">
                            @foreach($tabs as $tab)
                                <button
                                    wire:click="setActiveTab('{{ $tab['id'] }}')"
                                    class="w-full text-left px-4 py-2 rounded-md text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors duration-200 mb-1 {{ $activeTab === $tab['id'] ? 'active-tab' : '' }}"
                                >
                                    {{ $tab['name'] }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="w-full md:w-3/4 p-6">
                        @if($activeTab === 'business-config')
                            @livewire('super-admin.settings.business-configuration')
                        @elseif($activeTab === 'currency-localization')
                            @livewire('super-admin.settings.currency-localization')
                        @elseif($activeTab === 'branch-management')
                            @livewire('super-admin.settings.branch-management')
                        @elseif($activeTab === 'inventory-management')
                            @livewire('super-admin.settings.inventory-management')
                        @elseif($activeTab === 'employee-management')
                            @livewire('super-admin.settings.employee-management')
                        @elseif($activeTab === 'pos-configuration')
                            @livewire('super-admin.settings.pos-configuration')
                        @elseif($activeTab === 'accounting-cash')
                            @livewire('super-admin.settings.accounting-cash')
                        @elseif($activeTab === 'customer-supplier')
                            @livewire('super-admin.settings.customer-supplier')
                        @elseif($activeTab === 'reports-analytics')
                            @livewire('super-admin.settings.reports-analytics')
                        @elseif($activeTab === 'security-access')
                            @livewire('super-admin.settings.security-access')
                        @elseif($activeTab === 'notifications-alerts')
                            @livewire('super-admin.settings.notifications-alerts')
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

</div>
   