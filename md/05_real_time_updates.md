# 05 - Real-time Production Progress Updates

## Overview

This document outlines the implementation of real-time progress updates for production tracking. The system uses WebSockets and server-sent events to provide live progress monitoring across all production departments.

## Architecture

### Technology Stack

- **Laravel Broadcasting**: For server-side event broadcasting
- **Laravel Echo**: For client-side event listening
- **Redis/Socket.io**: For WebSocket server (configurable)
- **Database Triggers**: For automatic progress recalculation

### Event Flow

```
Production Update → Database Trigger → Progress Calculation → Event Broadcast → UI Update
```

## Server-Side Implementation

### 1. Broadcasting Configuration

#### Broadcasting Routes

```php
// routes/channels.php
Broadcast::channel('production-progress-{departmentId}', function ($user, $departmentId) {
    return $user->canAccessDepartment($departmentId);
});

Broadcast::channel('production-alerts-{departmentId}', function ($user, $departmentId) {
    return $user->canAccessDepartment($departmentId);
});

Broadcast::channel('production-updates-{branchId}', function ($user, $branchId) {
    return $user->branch_id === $branchId;
});
```

#### Broadcasting Events

```php
<?php

namespace App\Events;

use App\Models\ProductionRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductionRecord $record;
    public array $progressData;
    public int $departmentId;
    public int $branchId;

    public function __construct(ProductionRecord $record, array $progressData)
    {
        $this->record = $record;
        $this->progressData = $progressData;
        $this->departmentId = $record->dailyProduce->shift->department_id;
        $this->branchId = $record->dailyProduce->shift->branch_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("production-progress-{$this->departmentId}"),
            new Channel("production-updates-{$this->branchId}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'record_id' => $this->record->id,
            'batch_number' => $this->record->batch_number,
            'progress_percentage' => $this->progressData['overall_progress'],
            'status' => $this->progressData['status'],
            'produced_quantity' => $this->progressData['produced_quantity'],
            'remaining_quantity' => $this->progressData['remaining_quantity'],
            'estimated_completion' => $this->progressData['estimated_completion']?->toISOString(),
            'alerts' => $this->progressData['alerts'] ?? [],
            'timestamp' => now()->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'progress.updated';
    }
}

class ProductionAlertTriggered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $alertData;
    public int $departmentId;
    public int $branchId;

    public function __construct(array $alertData, int $departmentId, int $branchId)
    {
        $this->alertData = $alertData;
        $this->departmentId = $departmentId;
        $this->branchId = $branchId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("production-alerts-{$this->departmentId}"),
            new Channel("production-updates-{$this->branchId}"),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->alertData;
    }

    public function broadcastAs(): string
    {
        return 'alert.triggered';
    }
}
```

### 2. Progress Update Service

```php
<?php

namespace App\Services;

use App\Events\ProductionProgressUpdated;
use App\Events\ProductionAlertTriggered;
use App\Models\ProductionRecord;
use App\Models\DailyProduce;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RealTimeProgressService
{
    protected ProgressCalculationService $calculator;

    public function __construct(ProgressCalculationService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function updateProgress(ProductionRecord $record): void
    {
        try {
            // Calculate new progress
            $progressData = $this->calculator->calculateBatchProgress($record);

            // Update database
            $record->update([
                'progress_percentage' => $progressData['overall_progress'],
                'dispatch_status' => $this->mapProgressToStatus($progressData['overall_progress']),
            ]);

            // Clear relevant caches
            $this->clearProgressCaches($record);

            // Broadcast update
            broadcast(new ProductionProgressUpdated($record, $progressData));

            // Check for alerts
            $this->checkAndTriggerAlerts($record, $progressData);

            Log::info('Production progress updated', [
                'record_id' => $record->id,
                'progress' => $progressData['overall_progress'],
                'status' => $progressData['status']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update production progress', [
                'record_id' => $record->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateDailyProduceProgress(DailyProduce $dailyProduce): void
    {
        try {
            $progressData = $this->calculator->calculateDailyProduceProgress($dailyProduce);

            $dailyProduce->update([
                'progress_percentage' => $progressData['progress_percentage'],
                'status' => $progressData['status'],
            ]);

            // Broadcast to department channel
            broadcast(new ProductionProgressUpdated($dailyProduce, $progressData));

        } catch (\Exception $e) {
            Log::error('Failed to update daily produce progress', [
                'daily_produce_id' => $dailyProduce->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function clearProgressCaches(ProductionRecord $record): void
    {
        $departmentId = $record->dailyProduce->shift->department_id;
        $shiftId = $record->dailyProduce->shift_id;
        $date = $record->dailyProduce->produce_date->format('Y-m-d');

        Cache::forget("production_progress_dept_{$departmentId}_shift_{$shiftId}_{$date}");
        Cache::forget("department_summary_{$departmentId}_{$date}");
        Cache::forget("production_alerts_{$departmentId}");
    }

    protected function checkAndTriggerAlerts(ProductionRecord $record, array $progressData): void
    {
        $alerts = [];

        // Check for delays
        if ($this->isDelayed($record, $progressData)) {
            $alerts[] = [
                'type' => 'delay',
                'severity' => 'medium',
                'message' => "Production delayed for batch {$record->batch_number}",
                'record_id' => $record->id,
                'timestamp' => now()->toISOString(),
            ];
        }

        // Check for quality issues
        if ($progressData['quality_progress'] < 80) {
            $alerts[] = [
                'type' => 'quality',
                'severity' => 'high',
                'message' => "Quality issues detected in batch {$record->batch_number}",
                'record_id' => $record->id,
                'timestamp' => now()->toISOString(),
            ];
        }

        // Check for completion milestones
        if ($progressData['overall_progress'] >= 100) {
            $alerts[] = [
                'type' => 'completion',
                'severity' => 'low',
                'message' => "Batch {$record->batch_number} completed successfully",
                'record_id' => $record->id,
                'timestamp' => now()->toISOString(),
            ];
        }

        // Broadcast alerts
        foreach ($alerts as $alert) {
            $departmentId = $record->dailyProduce->shift->department_id;
            $branchId = $record->dailyProduce->shift->branch_id;

            broadcast(new ProductionAlertTriggered($alert, $departmentId, $branchId));
        }
    }

    protected function isDelayed(ProductionRecord $record, array $progressData): bool
    {
        if (!$record->estimated_completion_time) {
            return false;
        }

        $expectedProgress = $this->calculateExpectedProgress($record);
        $actualProgress = $progressData['overall_progress'];

        // Consider delayed if actual progress is 20% behind expected
        return ($expectedProgress - $actualProgress) > 20;
    }

    protected function calculateExpectedProgress(ProductionRecord $record): float
    {
        $startTime = $record->created_at;
        $expectedEndTime = $record->estimated_completion_time ?? now()->addHours(8);
        $totalDuration = $startTime->diffInMinutes($expectedEndTime);
        $elapsedDuration = $startTime->diffInMinutes(now());

        if ($totalDuration <= 0) return 100;

        return min(100, ($elapsedDuration / $totalDuration) * 100);
    }

    protected function mapProgressToStatus(float $progress): string
    {
        if ($progress >= 100) return 'fully_dispatched';
        if ($progress > 50) return 'partial';
        if ($progress > 0) return 'available';
        return 'pending';
    }
}
```

### 3. Database Triggers (Optional Enhancement)

For automatic progress updates without application intervention:

```sql
-- Trigger to automatically update progress when production records change
DELIMITER ;;

CREATE TRIGGER production_record_progress_trigger
AFTER UPDATE ON production_records
FOR EACH ROW
BEGIN
    -- Only trigger if relevant fields changed
    IF OLD.quantity_produced != NEW.quantity_produced OR
       OLD.quantity_approved != NEW.quantity_approved OR
       OLD.quantity_rejected != NEW.quantity_rejected OR
       OLD.quantity_sent_out != NEW.quantity_sent_out THEN

        -- Insert into progress update queue
        INSERT INTO progress_update_queue (record_id, update_type, created_at)
        VALUES (NEW.id, 'production_record', NOW());
    END IF;
END;;

DELIMITER ;
```

## Client-Side Implementation

### 1. Echo Configuration

```javascript
// resources/js/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    auth: {
        headers: {
            Authorization: `Bearer ${window.Laravel.apiToken}`,
        },
    },
});
```

### 2. Progress Update Handler

```javascript
// resources/js/components/ProductionProgressUpdater.js
export class ProductionProgressUpdater {
    constructor(departmentId, branchId) {
        this.departmentId = departmentId;
        this.branchId = branchId;
        this.channels = [];
        this.listeners = [];
    }

    initialize() {
        this.setupProgressChannel();
        this.setupAlertsChannel();
        this.setupBranchUpdatesChannel();
    }

    setupProgressChannel() {
        const channel = window.Echo.channel(`production-progress-${this.departmentId}`)
            .listen('.progress.updated', (data) => {
                this.handleProgressUpdate(data);
            });

        this.channels.push(channel);
    }

    setupAlertsChannel() {
        const channel = window.Echo.channel(`production-alerts-${this.departmentId}`)
            .listen('.alert.triggered', (data) => {
                this.handleAlert(data);
            });

        this.channels.push(channel);
    }

    setupBranchUpdatesChannel() {
        const channel = window.Echo.channel(`production-updates-${this.branchId}`)
            .listen('.progress.updated', (data) => {
                this.handleBranchUpdate(data);
            });

        this.channels.push(channel);
    }

    handleProgressUpdate(data) {
        // Update progress bars
        this.updateProgressBar(data.record_id, data.progress_percentage);

        // Update status badges
        this.updateStatusBadge(data.record_id, data.status);

        // Update quantities
        this.updateQuantities(data.record_id, {
            produced: data.produced_quantity,
            remaining: data.remaining_quantity
        });

        // Update estimated completion
        if (data.estimated_completion) {
            this.updateEstimatedTime(data.record_id, data.estimated_completion);
        }

        // Show toast notification
        this.showProgressToast(data);

        // Trigger any registered callbacks
        this.listeners.forEach(callback => callback('progress', data));
    }

    handleAlert(data) {
        // Show alert notification
        this.showAlertNotification(data);

        // Update alerts list
        this.addToAlertsList(data);

        // Trigger sound/notification based on severity
        this.handleAlertSeverity(data.severity);

        // Trigger callbacks
        this.listeners.forEach(callback => callback('alert', data));
    }

    handleBranchUpdate(data) {
        // Update branch-wide progress indicators
        this.updateBranchProgress(data);

        // Trigger callbacks for branch-level listeners
        this.listeners.forEach(callback => callback('branch', data));
    }

    updateProgressBar(recordId, progress) {
        const progressBar = document.querySelector(`[data-record-id="${recordId}"] .progress-fill`);
        const progressText = document.querySelector(`[data-record-id="${recordId}"] .progress-text`);

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        if (progressText) {
            progressText.textContent = `${progress.toFixed(1)}%`;
        }
    }

    updateStatusBadge(recordId, status) {
        const badge = document.querySelector(`[data-record-id="${recordId}"] .status-badge`);
        if (badge) {
            badge.className = `status-badge ${this.getStatusClasses(status)}`;
            badge.textContent = this.getStatusLabel(status);
        }
    }

    showProgressToast(data) {
        // Use toast library (e.g., Toastify, or custom implementation)
        showToast({
            message: `Batch ${data.batch_number}: ${data.progress_percentage.toFixed(1)}% complete`,
            type: 'info',
            duration: 3000
        });
    }

    showAlertNotification(alert) {
        const colors = {
            'low': 'bg-blue-500',
            'medium': 'bg-yellow-500',
            'high': 'bg-orange-500',
            'critical': 'bg-red-500'
        };

        showToast({
            message: alert.message,
            type: alert.severity,
            duration: alert.severity === 'critical' ? 0 : 5000, // Critical alerts stay until dismissed
            backgroundColor: colors[alert.severity] || colors.medium
        });
    }

    onUpdate(callback) {
        this.listeners.push(callback);
    }

    destroy() {
        // Clean up channels
        this.channels.forEach(channel => {
            window.Echo.leave(channel.name);
        });
        this.channels = [];
        this.listeners = [];
    }

    // Utility methods
    getStatusClasses(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'completed': 'bg-green-100 text-green-800'
        };
        return classes[status] || classes.pending;
    }

    getStatusLabel(status) {
        const labels = {
            'pending': 'Pending',
            'in_progress': 'In Progress',
            'completed': 'Completed'
        };
        return labels[status] || 'Unknown';
    }
}
```

### 3. Livewire Integration

```php
<?php

namespace App\Livewire\BranchDashboard\Production;

use Livewire\Attributes\On;
use App\Services\RealTimeProgressService;

class KitchenDashboard extends BaseDepartmentDashboard
{
    public $realTimeUpdates = [];

    public function mount()
    {
        parent::mount();

        // Initialize real-time updates
        $this->initializeRealTimeUpdates();
    }

    protected function initializeRealTimeUpdates()
    {
        // This will be handled by JavaScript component
        // But we can prepare data structures
        $this->realTimeUpdates = collect();
    }

    #[On('echo:production-progress-{departmentId},progress.updated')]
    public function handleProgressUpdate($data)
    {
        // Update local data
        $this->updateLocalProgressData($data);

        // Trigger UI refresh for specific components
        $this->dispatch('progress-updated', data: $data);
    }

    #[On('echo:production-alerts-{departmentId},alert.triggered')]
    public function handleAlert($alert)
    {
        // Add alert to local collection
        $this->realTimeUpdates->push($alert);

        // Trigger alert display
        $this->dispatch('alert-triggered', alert: $alert);

        // Auto-remove old alerts (keep last 10)
        if ($this->realTimeUpdates->count() > 10) {
            $this->realTimeUpdates->shift();
        }
    }

    private function updateLocalProgressData($data)
    {
        // Update cached progress data
        Cache::put(
            "production_progress_record_{$data['record_id']}",
            $data,
            now()->addMinutes(5)
        );

        // Update dashboard metrics if needed
        if (isset($data['department_metrics'])) {
            $this->departmentMetrics = array_merge(
                $this->departmentMetrics,
                $data['department_metrics']
            );
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.production.kitchen-dashboard', [
            // ... existing data
            'realTimeUpdates' => $this->realTimeUpdates,
        ]);
    }
}
```

## Performance Optimization

### 1. Connection Management

```javascript
// Connection pooling and reconnection logic
class ConnectionManager {
    constructor() {
        this.connections = new Map();
        this.maxConnections = 5;
        this.reconnectDelay = 1000;
    }

    getOrCreateConnection(departmentId) {
        if (this.connections.has(departmentId)) {
            return this.connections.get(departmentId);
        }

        if (this.connections.size >= this.maxConnections) {
            // Reuse oldest connection
            const oldestKey = this.connections.keys().next().value;
            const oldestConnection = this.connections.get(oldestKey);
            oldestConnection.disconnect();
            this.connections.delete(oldestKey);
        }

        const connection = new ProductionProgressUpdater(departmentId);
        this.connections.set(departmentId, connection);
        connection.initialize();

        return connection;
    }

    cleanup() {
        this.connections.forEach(connection => connection.destroy());
        this.connections.clear();
    }
}
```

### 2. Debouncing Updates

```javascript
// Debounce rapid progress updates
class DebouncedProgressUpdater {
    constructor(updater, delay = 500) {
        this.updater = updater;
        this.delay = delay;
        this.timeoutId = null;
        this.pendingUpdates = new Map();
    }

    update(recordId, data) {
        // Store latest data for this record
        this.pendingUpdates.set(recordId, data);

        // Clear existing timeout
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        // Set new timeout
        this.timeoutId = setTimeout(() => {
            // Process all pending updates
            this.pendingUpdates.forEach((updateData, id) => {
                this.updater.updateProgressBar(id, updateData.progress_percentage);
            });

            this.pendingUpdates.clear();
            this.timeoutId = null;
        }, this.delay);
    }
}
```

### 3. Offline Support

```javascript
// Cache updates for offline users
class OfflineProgressCache {
    constructor() {
        this.cache = new Map();
        this.maxCacheSize = 100;
    }

    storeUpdate(recordId, data) {
        if (this.cache.size >= this.maxCacheSize) {
            // Remove oldest entry
            const firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }

        this.cache.set(recordId, {
            data,
            timestamp: Date.now()
        });
    }

    getPendingUpdates() {
        return Array.from(this.cache.entries()).map(([recordId, entry]) => ({
            recordId,
            data: entry.data,
            timestamp: entry.timestamp
        }));
    }

    clearUpdate(recordId) {
        this.cache.delete(recordId);
    }

    replayUpdates(updater) {
        const pending = this.getPendingUpdates();
        pending.forEach(({ recordId, data }) => {
            updater.handleProgressUpdate(data);
            this.clearUpdate(recordId);
        });
    }
}
```

## Security Considerations

### 1. Authentication

- All WebSocket connections require authentication
- Channel authorization checks department access
- Token-based authentication for API calls

### 2. Rate Limiting

- Limit update frequency per user/department
- Implement exponential backoff for failed connections
- Rate limit broadcast events

### 3. Data Validation

- Validate all incoming progress data
- Sanitize alert messages
- Verify user permissions for updates

## Monitoring and Debugging

### 1. Event Logging

```php
// Log all broadcast events for debugging
class BroadcastingLogger
{
    public function handleBroadcastingEvent($event, $channels)
    {
        Log::info('Broadcasting event', [
            'event' => get_class($event),
            'channels' => $channels,
            'data' => $event->broadcastWith(),
            'timestamp' => now()
        ]);
    }
}
```

### 2. Performance Monitoring

```php
// Monitor WebSocket connection health
class WebSocketMonitor
{
    public function recordConnection($departmentId, $status)
    {
        // Store connection metrics
        Cache::put("ws_connection_{$departmentId}", [
            'status' => $status,
            'timestamp' => now(),
            'connections' => Cache::get("ws_active_connections_{$departmentId}", 0)
        ], now()->addMinutes(5));
    }

    public function getConnectionStats($departmentId)
    {
        return Cache::get("ws_connection_{$departmentId}", []);
    }
}
```

## Deployment Considerations

### 1. Environment Configuration

```env
# WebSocket Configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# Redis for queue and cache
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Progress update settings
PROGRESS_UPDATE_DEBOUNCE_MS=500
PROGRESS_CACHE_TTL_MINUTES=5
MAX_CONCURRENT_CONNECTIONS=1000
```

### 2. Scaling

- Use Redis clustering for high availability
- Implement horizontal scaling for WebSocket servers
- Use load balancers for multiple application servers
- Implement database read replicas for progress queries

### 3. Fallback Mechanisms

- Graceful degradation when WebSockets fail
- Polling fallback for critical updates
- Offline queue for updates when connection lost
- Progressive enhancement for real-time features