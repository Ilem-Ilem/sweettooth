# Notification Center Blade Template - User-Friendly Redesign

## Overview
This document provides the updated blade template for displaying all notification types in a user-friendly manner.

---

## File Location
`resources/views/livewire/components/notification-center.blade.php`

---

## Complete Updated Template

```blade
<div
    x-data="{ open: false }"
    class="relative"
    @click.outside="open = false"
>
    {{-- Notification Bell Button --}}
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

    {{-- Notification Dropdown --}}
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        class="absolute right-0 mt-2 w-[400px] max-w-[90vw] rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-xl z-[9999999999999]"
        x-cloak
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                Notifications
                @if ($this->unreadCount > 0)
                    <span class="ml-2 text-xs font-normal text-zinc-500 dark:text-zinc-400">
                        ({{ $this->unreadCount }} unread)
                    </span>
                @endif
            </div>
            @if ($this->unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                >
                    Mark all read
                </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-[450px] overflow-auto">
            @forelse ($this->notifications as $notification)
                @php
                    $isUrgent = $notification->data['urgent'] ?? false;
                    $notificationClass = $isUrgent ? 'bg-red-50/50 dark:bg-red-900/10' : '';
                    $unreadClass = $notification->read_at === null ? 'border-l-4 border-l-indigo-500' : '';
                @endphp
                <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 {{ $notificationClass }} {{ $unreadClass }}" x-data="{ showDetails: false }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            {{-- Title --}}
                            @if(!empty($notification->data['title']))
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $notification->data['title'] }}
                                    </div>
                                    @if($isUrgent)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-600 text-white animate-pulse">
                                            URGENT
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            {{-- Message --}}
                            <div class="text-sm text-zinc-700 dark:text-zinc-200 {{ empty($notification->data['title']) ? 'font-medium' : '' }} mt-0.5">
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            </div>
                            
                            {{-- Summary Preview --}}
                            @if(!empty($notification->data['summary']))
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2">
                                    {{ $notification->data['summary'] }}
                                </div>
                            @endif
                            
                            {{-- Context Badges --}}
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                {{-- Branch Badge --}}
                                @if(!empty($notification->data['context']['branch']))
                                    <div class="inline-flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $notification->data['context']['branch'] }}
                                    </div>
                                @endif
                                
                                {{-- Priority Badge --}}
                                @if(!empty($notification->data['context']['priority']))
                                    @php
                                        $priorityColors = [
                                            'Urgent' => 'text-red-600 dark:text-red-400',
                                            'High' => 'text-orange-600 dark:text-orange-400',
                                            'Normal' => 'text-blue-600 dark:text-blue-400',
                                            'Low' => 'text-slate-600 dark:text-slate-400',
                                        ];
                                    @endphp
                                    <span class="text-xs {{ $priorityColors[$notification->data['context']['priority']] ?? '' }}">
                                        {{ $notification->data['context']['priority'] }}
                                    </span>
                                @endif
                                
                                {{-- Status Badge --}}
                                @if(!empty($notification->data['context']['status']))
                                    @php
                                        $statusColors = [
                                            'Success' => 'text-green-600 dark:text-green-400',
                                            'Failed' => 'text-red-600 dark:text-red-400',
                                            'Pending' => 'text-amber-600 dark:text-amber-400',
                                        ];
                                    @endphp
                                    <span class="text-xs {{ $statusColors[$notification->data['context']['status']] ?? '' }}">
                                        {{ $notification->data['context']['status'] }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Timestamp --}}
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5">
                                {{ $notification->created_at->diffForHumans() }}
                                <span class="mx-1">•</span>
                                <span title="{{ $notification->created_at->format('M d, Y H:i') }}">
                                    {{ $notification->created_at->format('M d, Y g:i A') }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Mark as Read Button --}}
                        @if ($notification->read_at === null)
                            <button
                                type="button"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="text-[11px] text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors"
                                title="Mark as read"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    
                    {{-- Action Button --}}
                    @if(!empty($notification->data['action_url']))
                        <a
                            href="{{ $notification->data['action_url'] }}"
                            class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mt-2.5 transition-colors"
                        >
                            {{ $notification->data['action_text'] ?? 'View Details' }}
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                    
                    {{-- Details Button --}}
                    <button
                        type="button"
                        @click="showDetails = true"
                        class="inline-flex items-center text-xs font-medium text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 mt-2.5 ml-3 transition-colors"
                    >
                        More info
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Details Modal --}}
                    <div
                        x-show="showDetails"
                        x-transition.opacity.duration.150ms
                        class="fixed inset-0 z-[90] flex items-center justify-center p-4"
                        x-cloak
                        @keydown.escape.window="showDetails = false"
                    >
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDetails = false"></div>
                        
                        {{-- Modal Content --}}
                        <div class="relative w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-2xl overflow-hidden">
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50">
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $notification->data['title'] ?? 'Notification Details' }}
                                </div>
                                <button
                                    type="button"
                                    @click="showDetails = false"
                                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- Modal Body --}}
                            <div class="px-5 py-4 max-h-[60vh] overflow-auto">
                                {{-- Main Message --}}
                                <div class="text-base text-zinc-700 dark:text-zinc-300 mb-3">
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                </div>
                                
                                {{-- Timestamp --}}
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">
                                    <svg class="inline w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $notification->created_at->format('M d, Y g:i A') }}
                                </div>
                                
                                {{-- Context Details --}}
                                @if(!empty($notification->data['context']))
                                    <div class="space-y-2">
                                        @foreach($notification->data['context'] as $key => $value)
                                            @if($key !== 'rejection_reason' && $key !== 'error_message')
                                                <div class="flex justify-between gap-4 py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 min-w-[120px]">
                                                        {{ str_replace('_', ' ', ucfirst($key)) }}:
                                                    </span>
                                                    <span class="text-sm text-zinc-800 dark:text-zinc-200 font-medium text-right">
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                
                                {{-- Special Content Sections --}}
                                
                                {{-- Rejection Reason --}}
                                @if(!empty($notification->data['context']['rejection_reason']))
                                    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <div>
                                                <div class="text-xs font-semibold text-amber-700 dark:text-amber-400">Feedback</div>
                                                <div class="text-sm text-amber-600 dark:text-amber-300 mt-1">
                                                    {{ $notification->data['context']['rejection_reason'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Error Message --}}
                                @if(!empty($notification->data['context']['error_message']))
                                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <div class="text-xs font-semibold text-red-700 dark:text-red-400">Error</div>
                                                <div class="text-sm text-red-600 dark:text-red-300 mt-1 font-mono">
                                                    {{ $notification->data['context']['error_message'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Items Lists (Inventory, Production) --}}
                                @if(!empty($notification->data['items_preview']))
                                    <div class="mt-4">
                                        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-2">Items</div>
                                        <ul class="text-sm text-zinc-700 dark:text-zinc-300 space-y-1">
                                            @foreach(array_slice($notification->data['items_preview'], 0, 5) as $item)
                                                <li class="flex justify-between">
                                                    <span>{{ $item['name'] ?? $item }}</span>
                                                    <span class="font-medium">{{ $item['quantity'] ?? '' }} {{ $item['unit'] ?? '' }}</span>
                                                </li>
                                            @endforeach
                                            @if(count($notification->data['items_preview']) > 5)
                                                <li class="text-xs text-zinc-500 dark:text-zinc-400 italic">
                                                    + {{ count($notification->data['items_preview']) - 5 }} more items
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                                
                                {{-- Low Stock Items Table --}}
                                @if(!empty($notification->data['low_stock_items']))
                                    <div class="mt-4 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <table class="min-w-full text-xs">
                                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Item</th>
                                                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Current</th>
                                                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Reorder</th>
                                                    <th class="px-3 py-2 text-right font-medium text-zinc-600 dark:text-zinc-300">Shortage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(array_slice($notification->data['low_stock_items'], 0, 5) as $item)
                                                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                                                        <td class="px-3 py-2 text-zinc-800 dark:text-zinc-200">{{ $item['name'] }}</td>
                                                        <td class="px-3 py-2 text-right text-zinc-800 dark:text-zinc-200">{{ $item['current_qty'] }} {{ $item['unit'] }}</td>
                                                        <td class="px-3 py-2 text-right text-zinc-500">{{ $item['reorder_level'] }} {{ $item['unit'] }}</td>
                                                        <td class="px-3 py-2 text-right text-red-600 dark:text-red-400 font-medium">{{ $item['shortage'] }} {{ $item['unit'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Modal Footer --}}
                            <div class="px-5 py-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 flex justify-between items-center">
                                @if(!empty($notification->data['action_url']))
                                    <a
                                        href="{{ $notification->data['action_url'] }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                                    >
                                        {{ $notification->data['action_text'] ?? 'View Details' }}
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span></span>
                                @endif
                                <button
                                    type="button"
                                    @click="showDetails = false"
                                    class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">No notifications yet</p>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">When you get notifications, they'll show up here</p>
                </div>
            @endforelse
        </div>
        
        {{-- Footer --}}
        @if(count($this->notifications) > 0)
            <div class="px-4 py-2 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 rounded-b-xl">
                <div class="text-xs text-center text-zinc-500 dark:text-zinc-400">
                    Showing {{ count($this->notifications) }} notification{{ count($this->notifications) !== 1 ? 's' : '' }}
                </div>
            </div>
        @endif
    </div>
</div>
```

---

## Key Features

### 1. Visual Hierarchy
- **Title**: Bold, prominent for quick scanning
- **Message**: Regular weight, provides context
- **Summary**: Smaller, secondary information
- **Context Badges**: Icons + text for quick recognition

### 2. Urgency Indicators
- Red pulsing "URGENT" badge for critical notifications
- Left border indicator for unread notifications
- Color-coded priority badges

### 3. Context Badges
- Branch location with icon
- Priority level (Urgent/High/Normal/Low)
- Status (Success/Failed/Pending)

### 4. Special Content Sections
- Rejection reasons in amber warning boxes
- Error messages in red error boxes
- Item lists with quantity display
- Low stock tables with shortage highlighting

### 5. Responsive Design
- Mobile-friendly modal dialogs
- Truncated text with line-clamp
- Proper overflow handling

---

## CSS Utilities Used

```css
/* Line clamping for long text */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animation for urgent notifications */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Backdrop blur for modals */
.backdrop-blur-sm {
    --tw-backdrop-blur: blur(4px);
    backdrop-filter: var(--tw-backdrop-blur);
}
```

---

## Implementation Checklist

- [ ] Backup existing notification-center.blade.php
- [ ] Replace with updated template
- [ ] Test with each notification type
- [ ] Verify dark mode rendering
- [ ] Test mobile responsiveness
- [ ] Test modal interactions
- [ ] Verify accessibility (keyboard navigation)
- [ ] Test with empty state
- [ ] Test with 99+ notifications badge
