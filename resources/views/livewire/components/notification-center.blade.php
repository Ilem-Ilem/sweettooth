<div
    x-data="{ open: false }"
    class="relative"
    @click.outside="open = false"
>
    <button
        type="button"
        @click="open = !open"
        class="relative inline-flex items-center justify-center h-10 w-10 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-150"
        aria-label="Notifications"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-600 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0m6 0H9" />
        </svg>
        @if ($this->unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center text-[10px] font-bold bg-red-600 text-white rounded-full min-w-[18px] h-[18px] px-1">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        class="absolute right-0 mt-2 w-[360px] max-w-[90vw] rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-xl z-[9999999999999]"
        x-cloak
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Notifications</div>
            @if ($this->unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-xs text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                >
                    Mark all read
                </button>
            @endif
        </div>

        <div class="max-h-[420px] overflow-auto">
            @forelse ($this->notifications as $notification)
                @php
                    $summaryData = collect($notification->data ?? [])
                        ->except(['action_url', 'action_text', 'message', 'branch_name', 'type'])
                        ->toArray();
                @endphp
                <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800" x-data="{ showSummary: false }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm text-zinc-800 dark:text-zinc-100">
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            </div>
                            @if (!empty($notification->data['branch_name']))
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    Branch: {{ $notification->data['branch_name'] }}
                                </div>
                            @endif
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        @if ($notification->read_at === null)
                            <button
                                type="button"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="text-[11px] text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                            >
                                Mark read
                            </button>
                        @endif
                    </div>
                    <button
                        type="button"
                        @click="showSummary = true"
                        class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mt-2"
                    >
                        {{ $notification->data['action_text'] ?? 'View notification' }}
                    </button>

                    <div
                        x-show="showSummary"
                        x-transition.opacity.duration.150ms
                        class="fixed inset-0 z-[90] flex items-center justify-center"
                        x-cloak
                    >
                        <div class="absolute inset-0 bg-black/40" @click="showSummary = false"></div>
                        <div class="relative w-[720px] max-w-[92vw] rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-2xl">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notification Details</div>
                                <button
                                    type="button"
                                    @click="showSummary = false"
                                    class="text-xs text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                                >
                                    Close
                                </button>
                            </div>
                            <div class="px-5 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                </div>
                                <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $notification->created_at->format('M d, Y H:i') }}
                                </div>
                                <div class="mt-4 space-y-1 text-sm">
                                    <div><span class="font-semibold">Type:</span> {{ $notification->data['display_type'] ?? $notification->data['type'] ?? $notification->type }}</div>
                                    @if (!empty($notification->data['branch_name']))
                                        <div><span class="font-semibold">Branch:</span> {{ $notification->data['branch_name'] }}</div>
                                    @endif
                                    @if (!empty($summaryData))
                                        @foreach ($summaryData as $key => $value)
                                            <div>
                                                <span class="font-semibold">{{ str_replace('_', ' ', ucfirst((string) $key)) }}:</span>
                                                @if (is_array($value))
                                                    {{ implode(', ', array_map(fn ($item) => is_scalar($item) ? (string) $item : json_encode($item), $value)) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-zinc-500 dark:text-zinc-400 text-center">
                    No notifications yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
