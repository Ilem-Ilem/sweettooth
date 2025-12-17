# 04 - Progress Visualization Components

## Overview

This document outlines the reusable UI components for visualizing production progress across different levels of the system. These components provide consistent, real-time progress tracking with rich visual feedback.

## Core Progress Components

### 1. Progress Bar Component

#### Basic Progress Bar

```php
<!-- resources/views/components/progress-bar.blade.php -->
@props([
    'progress' => 0,
    'color' => 'blue',
    'size' => 'md',
    'showPercentage' => true,
    'animated' => true,
    'striped' => false
])

@php
    $sizeClasses = [
        'sm' => 'h-1',
        'md' => 'h-2',
        'lg' => 'h-3',
        'xl' => 'h-4'
    ];

    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'red' => 'bg-red-500',
        'purple' => 'bg-purple-500',
        'orange' => 'bg-orange-500',
        'teal' => 'bg-teal-500',
        'pink' => 'bg-pink-500'
    ];

    $bgColorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/20',
        'green' => 'bg-green-100 dark:bg-green-900/20',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/20',
        'red' => 'bg-red-100 dark:bg-red-900/20',
        'purple' => 'bg-purple-100 dark:bg-purple-900/20',
        'orange' => 'bg-orange-100 dark:bg-orange-900/20',
        'teal' => 'bg-teal-100 dark:bg-teal-900/20',
        'pink' => 'bg-pink-100 dark:bg-pink-900/20'
    ];
@endphp

<div class="w-full {{ $bgColorClasses[$color] ?? 'bg-zinc-100 dark:bg-zinc-800' }} rounded-full {{ $sizeClasses[$size] ?? 'h-2' }} overflow-hidden">
    <div
        class="transition-all duration-500 {{ $colorClasses[$color] ?? 'bg-blue-500' }} {{ $sizeClasses[$size] ?? 'h-2' }} rounded-full
               {{ $animated ? 'transition-all duration-500' : '' }}
               {{ $striped ? 'bg-striped' : '' }}"
        style="width: {{ min(100, max(0, $progress)) }}%"
        role="progressbar"
        aria-valuenow="{{ $progress }}"
        aria-valuemin="0"
        aria-valuemax="100"
    ></div>
</div>

@if($showPercentage)
<div class="text-xs text-center mt-1 text-zinc-600 dark:text-zinc-400">
    {{ number_format($progress, 1) }}%
</div>
@endif
```

#### Multi-Segment Progress Bar

```php
<!-- resources/views/components/multi-progress-bar.blade.php -->
@props([
    'segments' => [],
    'size' => 'md',
    'showLabels' => false
])

@php
    $sizeClasses = [
        'sm' => 'h-1',
        'md' => 'h-2',
        'lg' => 'h-3',
        'xl' => 'h-4'
    ];

    $colorMap = [
        'pending' => 'bg-yellow-500',
        'in_progress' => 'bg-blue-500',
        'completed' => 'bg-green-500',
        'quality_check' => 'bg-purple-500',
        'dispatch' => 'bg-teal-500'
    ];
@endphp

<div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full {{ $sizeClasses[$size] ?? 'h-2' }} overflow-hidden flex">
    @foreach($segments as $segment)
        @php
            $width = ($segment['value'] / array_sum(array_column($segments, 'value'))) * 100;
            $color = $colorMap[$segment['status']] ?? 'bg-zinc-400';
        @endphp
        <div
            class="transition-all duration-500 {{ $color }} {{ $sizeClasses[$size] ?? 'h-2' }}"
            style="width: {{ $width }}%"
            title="{{ $segment['label'] }}: {{ $segment['value'] }}"
        ></div>
    @endforeach
</div>

@if($showLabels)
<div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400 mt-1">
    @foreach($segments as $segment)
    <span>{{ $segment['label'] }}</span>
    @endforeach
</div>
@endif
```

### 2. Status Indicator Component

#### Status Badge

```php
<!-- resources/views/components/status-badge.blade.php -->
@props([
    'status' => 'pending',
    'size' => 'md',
    'variant' => 'filled' // filled, outline, dot
])

@php
    $statusConfig = [
        'pending' => [
            'color' => 'yellow',
            'icon' => '⏳',
            'label' => 'Pending'
        ],
        'in_progress' => [
            'color' => 'blue',
            'icon' => '🔄',
            'label' => 'In Progress'
        ],
        'completed' => [
            'color' => 'green',
            'icon' => '✅',
            'label' => 'Completed'
        ],
        'cancelled' => [
            'color' => 'red',
            'icon' => '❌',
            'label' => 'Cancelled'
        ],
        'on_hold' => [
            'color' => 'orange',
            'icon' => '⏸️',
            'label' => 'On Hold'
        ],
        'quality_check' => [
            'color' => 'purple',
            'icon' => '🔍',
            'label' => 'Quality Check'
        ],
        'ready_for_dispatch' => [
            'color' => 'teal',
            'icon' => '📦',
            'label' => 'Ready'
        ]
    ];

    $config = $statusConfig[$status] ?? $statusConfig['pending'];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base'
    ];

    $colorClasses = [
        'filled' => [
            'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'green' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'red' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            'teal' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300'
        ],
        'outline' => [
            'yellow' => 'border border-yellow-300 text-yellow-700 dark:border-yellow-600 dark:text-yellow-300',
            'blue' => 'border border-blue-300 text-blue-700 dark:border-blue-600 dark:text-blue-300',
            'green' => 'border border-green-300 text-green-700 dark:border-green-600 dark:text-green-300',
            'red' => 'border border-red-300 text-red-700 dark:border-red-600 dark:text-red-300',
            'orange' => 'border border-orange-300 text-orange-700 dark:border-orange-600 dark:text-orange-300',
            'purple' => 'border border-purple-300 text-purple-700 dark:border-purple-600 dark:text-purple-300',
            'teal' => 'border border-teal-300 text-teal-700 dark:border-teal-600 dark:text-teal-300'
        ],
        'dot' => [
            'yellow' => 'text-yellow-600 dark:text-yellow-400',
            'blue' => 'text-blue-600 dark:text-blue-400',
            'green' => 'text-green-600 dark:text-green-400',
            'red' => 'text-red-600 dark:text-red-400',
            'orange' => 'text-orange-600 dark:text-orange-400',
            'purple' => 'text-purple-600 dark:text-purple-400',
            'teal' => 'text-teal-600 dark:text-teal-400'
        ]
    ];
@endphp

@if($variant === 'dot')
    <span class="inline-flex items-center space-x-1">
        <span class="w-2 h-2 rounded-full {{ $colorClasses[$variant][$config['color']] }}"></span>
        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $config['label'] }}</span>
    </span>
@elseif($variant === 'outline')
    <span class="{{ $sizeClasses[$size] ?? 'px-2.5 py-1 text-sm' }} rounded-full font-medium {{ $colorClasses[$variant][$config['color']] }}">
        {{ $config['icon'] }} {{ $config['label'] }}
    </span>
@else
    <span class="{{ $sizeClasses[$size] ?? 'px-2.5 py-1 text-sm' }} rounded-full font-medium {{ $colorClasses[$variant][$config['color']] }}">
        {{ $config['icon'] }} {{ $config['label'] }}
    </span>
@endif
```

#### Progress Timeline Component

```php
<!-- resources/views/components/progress-timeline.blade.php -->
@props([
    'items' => [],
    'currentStep' => 0,
    'orientation' => 'vertical' // vertical, horizontal
])

@php
    $statusColors = [
        'pending' => 'border-zinc-300 bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800',
        'in_progress' => 'border-blue-500 bg-blue-500',
        'completed' => 'border-green-500 bg-green-500',
        'failed' => 'border-red-500 bg-red-500'
    ];

    $textColors = [
        'pending' => 'text-zinc-500 dark:text-zinc-400',
        'in_progress' => 'text-blue-700 dark:text-blue-300',
        'completed' => 'text-green-700 dark:text-green-300',
        'failed' => 'text-red-700 dark:text-red-300'
    ];
@endphp

@if($orientation === 'horizontal')
    <!-- Horizontal Timeline -->
    <div class="flex items-center w-full">
        @foreach($items as $index => $item)
            @php
                $isCompleted = $index < $currentStep;
                $isCurrent = $index === $currentStep;
                $isPending = $index > $currentStep;
                $status = $isCompleted ? 'completed' : ($isCurrent ? 'in_progress' : 'pending');
            @endphp

            <!-- Step Circle -->
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center {{ $statusColors[$status] }}">
                    @if($isCompleted)
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @elseif($isCurrent)
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                    @else
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $index + 1 }}</span>
                    @endif
                </div>

                <!-- Step Label -->
                <span class="text-xs mt-1 text-center {{ $textColors[$status] }}">
                    {{ $item['label'] }}
                </span>

                <!-- Time -->
                @if(isset($item['time']))
                <span class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">
                    {{ $item['time'] }}
                </span>
                @endif
            </div>

            <!-- Connector Line -->
            @if(!$loop->last)
                <div class="flex-1 h-0.5 mx-2 {{ $index < $currentStep ? 'bg-green-500' : 'bg-zinc-300 dark:bg-zinc-600' }}"></div>
            @endif
        @endforeach
    </div>
@else
    <!-- Vertical Timeline -->
    <div class="space-y-4">
        @foreach($items as $index => $item)
            @php
                $isCompleted = $index < $currentStep;
                $isCurrent = $index === $currentStep;
                $isPending = $index > $currentStep;
                $status = $isCompleted ? 'completed' : ($isCurrent ? 'in_progress' : 'pending');
            @endphp

            <div class="flex items-start space-x-3">
                <!-- Timeline Line -->
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center {{ $statusColors[$status] }}">
                        @if($isCompleted)
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @elseif($isCurrent)
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $index + 1 }}</span>
                        @endif
                    </div>

                    @if(!$loop->last)
                        <div class="w-0.5 h-8 {{ $index < $currentStep ? 'bg-green-500' : 'bg-zinc-300 dark:bg-zinc-600' }}"></div>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 pb-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium {{ $textColors[$status] }}">
                            {{ $item['label'] }}
                        </h4>
                        @if(isset($item['time']))
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $item['time'] }}
                        </span>
                        @endif
                    </div>

                    @if(isset($item['description']))
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        {{ $item['description'] }}
                    </p>
                    @endif

                    @if(isset($item['progress']) && $isCurrent)
                    <div class="mt-2">
                        <x-progress-bar :progress="$item['progress']" size="sm" />
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
```

### 3. Progress Card Component

#### Production Item Progress Card

```php
<!-- resources/views/components/production-progress-card.blade.php -->
@props([
    'item' => [],
    'showDetails' => true,
    'showTimeline' => false
])

<div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 hover:shadow-md transition-shadow">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $item['name'] }}
            </h4>
            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                {{ $item['recipe'] }} • Batch {{ $item['batch_number'] }}
            </p>
        </div>
        <x-status-badge :status="$item['status']" size="sm" />
    </div>

    <!-- Progress Bar -->
    <div class="mb-3">
        <x-progress-bar
            :progress="$item['progress']"
            :color="$item['status'] === 'completed' ? 'green' : ($item['status'] === 'in_progress' ? 'blue' : 'yellow')"
            size="md"
            show-percentage="true"
        />
    </div>

    <!-- Metrics -->
    @if($showDetails)
    <div class="grid grid-cols-2 gap-3 mb-3">
        <div class="text-center">
            <p class="text-xs text-zinc-600 dark:text-zinc-400">Produced</p>
            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ number_format($item['produced'], 1) }} {{ $item['unit'] }}
            </p>
        </div>
        <div class="text-center">
            <p class="text-xs text-zinc-600 dark:text-zinc-400">Target</p>
            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                {{ number_format($item['target'], 1) }} {{ $item['unit'] }}
            </p>
        </div>
    </div>
    @endif

    <!-- Timeline -->
    @if($showTimeline && isset($item['timeline']))
    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3">
        <x-progress-timeline :items="$item['timeline']" :current-step="$item['current_step']" orientation="horizontal" />
    </div>
    @endif

    <!-- Alerts -->
    @if(!empty($item['alerts']))
    <div class="mt-3 space-y-1">
        @foreach($item['alerts'] as $alert)
        <div class="text-xs px-2 py-1 rounded {{ $alert['color'] }}">
            {{ $alert['icon'] }} {{ $alert['message'] }}
        </div>
        @endforeach
    </div>
    @endif

    <!-- Action Buttons -->
    @if(isset($item['actions']))
    <div class="flex space-x-2 mt-3">
        @foreach($item['actions'] as $action)
        <button
            class="px-3 py-1 text-xs rounded {{ $action['color'] }} transition-colors"
            wire:click="{{ $action['method'] }}"
        >
            {{ $action['label'] }}
        </button>
        @endforeach
    </div>
    @endif
</div>
```

### 4. Dashboard Widgets

#### KPI Card with Progress

```php
<!-- resources/views/components/kpi-progress-card.blade.php -->
@props([
    'title' => '',
    'value' => 0,
    'target' => 0,
    'unit' => '',
    'color' => 'blue',
    'icon' => '',
    'trend' => null, // up, down, neutral
    'progress' => null
])

@php
    $progressPercentage = $target > 0 ? ($value / $target) * 100 : 0;
    $isAboveTarget = $value >= $target;

    $trendColors = [
        'up' => 'text-green-600 dark:text-green-400',
        'down' => 'text-red-600 dark:text-red-400',
        'neutral' => 'text-zinc-600 dark:text-zinc-400'
    ];

    $colorClasses = [
        'blue' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
        'green' => 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
        'yellow' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800',
        'red' => 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
        'purple' => 'bg-purple-50 border-purple-200 dark:bg-purple-900/20 dark:border-purple-800'
    ];
@endphp

<div class="{{ $colorClasses[$color] ?? 'bg-zinc-50 border-zinc-200 dark:bg-zinc-900/20 dark:border-zinc-800' }} border rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $title }}</p>
            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">
                {{ is_numeric($value) ? number_format($value, 1) : $value }}
                @if($unit)
                    <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">{{ $unit }}</span>
                @endif
            </p>

            @if($target > 0)
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                Target: {{ number_format($target, 1) }} {{ $unit }}
                <span class="{{ $isAboveTarget ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $isAboveTarget ? '+' : '' }}{{ number_format($progressPercentage - 100, 1) }}%)
                </span>
            </p>
            @endif
        </div>

        @if($icon)
        <div class="p-2 rounded-lg bg-white dark:bg-zinc-800">
            {!! $icon !!}
        </div>
        @endif
    </div>

    <!-- Progress Bar -->
    @if($progress !== null)
    <div class="mt-3">
        <x-progress-bar :progress="$progress" :color="$color" size="sm" />
    </div>
    @endif

    <!-- Trend Indicator -->
    @if($trend)
    <div class="flex items-center mt-2">
        @if($trend === 'up')
            <svg class="w-4 h-4 {{ $trendColors[$trend] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        @elseif($trend === 'down')
            <svg class="w-4 h-4 {{ $trendColors[$trend] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 011.414 0l4-4a1 1 0 01-1.414 1.414L11 14.586V3a1 1 0 10-2 0v11.586l-2.293-2.293a1 1 0 111.414-1.414z" clip-rule="evenodd" />
            </svg>
        @else
            <svg class="w-4 h-4 {{ $trendColors[$trend] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
        @endif
        <span class="text-xs {{ $trendColors[$trend] }} ml-1">vs last shift</span>
    </div>
    @endif
</div>
```

### 5. Real-time Progress Updates

#### WebSocket Integration

```javascript
// resources/js/components/progress-realtime.js
class ProgressRealtime {
    constructor(departmentId) {
        this.departmentId = departmentId;
        this.subscriptions = [];
    }

    subscribe(callback) {
        // Subscribe to progress updates via Echo
        const channel = Echo.channel(`production-progress-${this.departmentId}`)
            .listen('ProgressUpdated', (e) => {
                this.updateProgressBar(e.data.itemId, e.data.progress);
                this.updateStatusBadge(e.data.itemId, e.data.status);
                callback(e.data);
            });

        this.subscriptions.push(channel);
        return channel;
    }

    updateProgressBar(itemId, progress) {
        const progressBar = document.querySelector(`[data-progress-id="${itemId}"] .progress-fill`);
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        const progressText = document.querySelector(`[data-progress-id="${itemId}"] .progress-text`);
        if (progressText) {
            progressText.textContent = `${progress.toFixed(1)}%`;
        }
    }

    updateStatusBadge(itemId, status) {
        const badge = document.querySelector(`[data-status-id="${itemId}"]`);
        if (badge) {
            // Update badge classes and text based on status
            badge.className = this.getStatusClasses(status);
            badge.textContent = this.getStatusLabel(status);
        }
    }

    getStatusClasses(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'completed': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
        };
        return `px-2 py-1 rounded-full text-xs font-medium ${classes[status] || classes.pending}`;
    }

    getStatusLabel(status) {
        const labels = {
            'pending': 'Pending',
            'in_progress': 'In Progress',
            'completed': 'Completed'
        };
        return labels[status] || 'Unknown';
    }

    unsubscribe() {
        this.subscriptions.forEach(subscription => {
            subscription.stopListening('ProgressUpdated');
        });
        this.subscriptions = [];
    }
}

// Usage
const progressUpdater = new ProgressRealtime(departmentId);
progressUpdater.subscribe((data) => {
    console.log('Progress updated:', data);
    showToast(`Progress updated for ${data.itemName}`, 'info');
});
```

## Responsive Design

All components are designed to be fully responsive:

- **Mobile**: Simplified views with essential information only
- **Tablet**: Condensed layouts with touch-friendly interactions
- **Desktop**: Full-featured components with detailed information

## Accessibility

All progress components include proper ARIA attributes:
- `role="progressbar"` for progress bars
- `aria-valuenow`, `aria-valuemin`, `aria-valuemax` for current values
- Screen reader friendly status text
- Keyboard navigation support
- High contrast color schemes

## Performance Optimization

### CSS Optimization
- Use CSS custom properties for dynamic colors
- Minimize layout shifts with fixed dimensions
- Use `will-change` for animated elements

### JavaScript Optimization
- Debounce rapid progress updates
- Use `requestAnimationFrame` for smooth animations
- Implement virtual scrolling for large lists

### Caching Strategy
- Cache component templates
- Use service workers for offline progress viewing
- Implement progressive loading for dashboard data