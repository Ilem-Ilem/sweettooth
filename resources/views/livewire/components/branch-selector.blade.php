<div>
    @if($this->isSuperAdmin && $this->branches->count() > 0)
    <div class="sticky top-0 z-40 flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-purple-50 via-blue-50 to-indigo-50 dark:from-purple-900/30 dark:via-blue-900/30 dark:to-indigo-900/30 border-b-2 border-purple-300 dark:border-purple-700 shadow-sm">
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="p-2 bg-purple-600 dark:bg-purple-500 rounded-lg shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-300 uppercase tracking-wide">Super Admin View</span>
                <p class="text-xs text-purple-700 dark:text-purple-400">Multi-Branch Access</p>
            </div>
        </div>

        <div class="flex-1 max-w-md">
            <div class="flex items-end gap-2">
                <div class="relative flex-1">
                    <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 mb-1">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                        </svg>
                        Select Branch
                    </label>
                    <select wire:model="selectedBranch"
                            class="w-full rounded-lg border-2 border-purple-300 dark:border-purple-600 bg-white dark:bg-zinc-800 text-sm font-medium text-zinc-900 dark:text-zinc-100 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 shadow-sm transition-all duration-200 hover:border-purple-400 dark:hover:border-purple-500">
                        <option value="">-- Select a Branch --</option>
                        @foreach($this->branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }} @if($branch->code)({{ $branch->code }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button"
                        wire:click="changeBranch"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="changeBranch">Switch</span>
                    <span wire:loading wire:target="changeBranch" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                    </span>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-800 rounded-lg border-2 border-purple-200 dark:border-purple-700 shadow-sm">
            <div class="flex flex-col">
                <span class="text-[10px] font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider">Acting as</span>
                <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $this->currentBranchName }}</span>
            </div>
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-lg shadow-green-500/50"></div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="text-xs text-purple-700 dark:text-purple-300">
                <span class="font-medium">{{ $this->branches->count() }}</span> branches available
            </div>
        </div>
    </div>
    @endif
</div>
