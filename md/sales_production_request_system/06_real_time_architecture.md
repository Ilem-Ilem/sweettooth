# Sales to Production Request System - Real-time Architecture

## Broadcasting Overview

Real-time updates use Laravel Broadcasting with WebSocket/Polling to instantly notify users of changes.

### Supported Drivers
- **Development**: Redis with polling fallback
- **Production**: Pusher or Laravel WebSockets

---

## Broadcasting Channels

### 1. Production Request Channel
```
Channel Name: production-request.{request_id}
Access: Private (only request creator + target department)
Purpose: Updates for a specific request
```

#### Subscribers
- Sales user who created the request
- All production staff in target department
- Request managers/admins

#### Events Published
- `RequestCreated`
- `ProgressUpdated`
- `StatusChanged`
- `DispatchCreated`
- `DispatchAccepted`
- `DispatchRejected`

#### Example Usage
```javascript
// Sales user listens to their request
Echo.private(`production-request.42`)
    .listen('ProgressUpdated', (data) => {
        console.log('Production progress:', data);
        updateProgressBar(data.progress_percentage);
    });
```

---

### 2. Production Department Channel
```
Channel Name: production-dept.{department_id}
Access: Private (only dept members)
Purpose: Notifications of new requests for department
```

#### Subscribers
- All production staff in department
- Department manager
- Department lead

#### Events Published
- `NewRequest`
- `RequestAssigned`
- `RequestPriority Changed`
- `RequestCancelled`

#### Example Usage
```javascript
// Production staff monitors their department
Echo.private(`production-dept.2`)
    .listen('NewRequest', (data) => {
        showNotification('New production request', data.product_names);
        playSound('notification.mp3');
        addToRequestBoard(data);
    });
```

---

### 3. Sales User Channel
```
Channel Name: sales-user.{user_id}
Access: Private (only that user)
Purpose: Personalized notifications for sales user
```

#### Subscribers
- Specific sales user only

#### Events Published
- `ProductionStarted` - Production began on your request
- `ProgressUpdate` - Progress milestone reached
- `DispatchReady` - Products ready for verification
- `RequestCancelled` - Request was cancelled
- `FeedbackReceived` - Production sent feedback

#### Example Usage
```javascript
// Sales user gets personal notifications
Echo.private(`sales-user.${userId}`)
    .listen('DispatchReady', (data) => {
        showAlert('Dispatch is ready for verification');
        navigateTo(`/dispatch/${data.request_id}`);
    });
```

---

## Events Definition

### Event: RequestCreated
```php
class RequestCreated implements ShouldBroadcast
{
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('production-dept.' . $this->request->production_department_id);
    }

    public function broadcastWith(): array
    {
        return [
            'request_id' => $this->request->id,
            'status' => 'pending',
            'priority' => $this->request->priority,
            'products' => $this->request->products->pluck('name'),
            'created_by' => $this->request->createdBy->name,
            'created_at' => $this->request->created_at,
        ];
    }
}
```

### Event: ProgressUpdated
```php
class ProgressUpdated implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('production-request.' . $this->feedback->production_request_id),
            new PrivateChannel('sales-user.' . $this->request->created_by_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'request_id' => $this->feedback->production_request_id,
            'milestone' => $this->feedback->milestone,
            'progress_percentage' => $this->feedback->progress_percentage,
            'notes' => $this->feedback->notes,
            'updated_by' => $this->feedback->updatedBy->name,
            'timestamp' => $this->feedback->created_at,
        ];
    }
}
```

### Event: DispatchCreated
```php
class DispatchCreated implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('production-request.' . $this->request->id),
            new PrivateChannel('sales-user.' . $this->request->created_by_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'request_id' => $this->request->id,
            'status' => 'dispatched',
            'products' => $this->request->dispatches->map(fn($d) => [
                'product_name' => $d->product->name,
                'quantity_produced' => $d->quantity_produced,
                'quantity_dispatched' => $d->quantity_dispatched,
            ]),
            'dispatch_created_at' => now(),
        ];
    }
}
```

---

## Client-Side Implementation

### Vue 3 Component Example
```vue
<template>
  <div class="request-details">
    <div class="progress-section">
      <h2>{{ request.product_names }}</h2>
      <div class="progress-bar">
        <div :style="{ width: progress.progress_percentage + '%' }"></div>
      </div>
      <p>{{ progress.milestone }} - {{ progress.progress_percentage }}%</p>
      <p class="notes">{{ progress.notes }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Echo from 'laravel-echo'

const request = ref({})
const progress = ref({})

onMounted(() => {
  // Listen to request-specific updates
  Echo.private(`production-request.${route.params.id}`)
    .listen('ProgressUpdated', (data) => {
      progress.value = data
      // Optionally auto-scroll or play notification
      playNotificationSound()
    })
    .listen('DispatchCreated', (data) => {
      request.value.status = 'dispatched'
      showVerificationPrompt()
    })
})
</script>
```

---

## Broadcasting Configuration

### .env Settings
```
BROADCAST_DRIVER=redis
# or for production:
# BROADCAST_DRIVER=pusher

PUSHER_APP_ID=xxxxx
PUSHER_APP_KEY=xxxxx
PUSHER_APP_SECRET=xxxxx
PUSHER_APP_CLUSTER=mt1
```

### Redis Broadcasting Setup
```php
// config/broadcasting.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
],
```

### Start Broadcasting Server
```bash
# Development - use Redis with polling
php artisan serve

# Production - use Laravel WebSockets
php artisan websockets:serve
# or
composer require beyondcode/laravel-websockets
```

---

## Real-time Update Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ Production Staff Updates Progress                               │
│                                                                 │
│  PATCH /api/production-requests/{id}/progress                 │
│  { "milestone": "in_production", "progress_percentage": 50 }   │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         v
┌─────────────────────────────────────────────────────────────────┐
│ Controller Creates Progress Feedback                            │
│                                                                 │
│  $feedback = ProductionProgressFeedback::create([...])         │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         v
┌─────────────────────────────────────────────────────────────────┐
│ Model Broadcasts Event                                          │
│                                                                 │
│  protected $dispatchesEvents = [                               │
│    'created' => ProgressUpdated::class,                        │
│  ]                                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         v
┌─────────────────────────────────────────────────────────────────┐
│ Event Published to Broadcasting Service                         │
│ (Redis queue)                                                   │
│                                                                 │
│ Channel: production-request.{request_id}                        │
│ Event: ProgressUpdated                                          │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         v
┌──────────────────────────┬──────────────────────────────────────┐
│                          │                                      │
│                          v                                      v
│              ┌────────────────────┐            ┌────────────────┐
│              │ Sales User Browser │            │ Other Admins   │
│              │ (WebSocket/Polling)│            │ (WebSocket)    │
│              │                    │            │                │
│              │ Receives Update    │            │ Receives Update│
│              │ Updates UI         │            │ Updates UI     │
│              └────────────────────┘            └────────────────┘
│
└──────────────────────────────────────────────────────────────────
```

---

## Fallback & Reliability

### If WebSocket Connection Fails
1. Client automatically falls back to polling
2. Polls API endpoint every 3 seconds
3. Shows "Offline" indicator
4. Queues actions to retry when online

### If Broadcasting Service Crashes
1. Events are queued in Redis
2. Automatic retry with exponential backoff
3. Admin notified via email
4. Manual broadcast recovery available

### Data Consistency
- Events are idempotent (safe to receive twice)
- Latest timestamp wins
- Conflict resolution via timestamps
- Audit trail for all changes

---

## Performance Optimization

### Event Batching
For high-frequency updates, batch events:
```php
// Instead of broadcasting on every update
// Collect updates and broadcast every 5 seconds
dispatch(
    new BroadcastProgressBatch($request),
    'broadcast'
)->delay(now()->addSeconds(5));
```

### Channel Authorization
```php
// Broadcast::channel()
Broadcast::channel('production-request.{id}', function ($user, $id) {
    $request = ProductionRequest::find($id);
    
    return $user->id === $request->created_by_id ||
           $user->department_id === $request->production_department_id;
});
```

### Presence Channels (Optional)
Track who's viewing a request in real-time:
```
Channel: presence.production-request.{id}

Subscribers:
- John Sales (viewing)
- Jane Production (viewing)
- Bob Manager (viewing)

Show: "3 people viewing this request"
```

---

## Monitoring & Debugging

### Broadcasting Dashboard
```
php artisan tinker
>>> Broadcasting::channel('production-request.42')->subscribers()
```

### Test Broadcasting Locally
```bash
# Terminal 1: Start Redis
redis-server

# Terminal 2: Start Laravel app
php artisan serve

# Terminal 3: Listen to broadcasts
php artisan tinker
>>> Illuminate\Broadcasting\BroadcasterManager::listen('production-request.42')

# Terminal 4: Send test broadcast
>>> event(new App\Events\ProgressUpdated($request))
```

### Logs
Broadcasting logs in:
```
storage/logs/laravel.log
```

Look for:
```
[2025-12-27 12:30:45] local.INFO: Broadcasting ProgressUpdated to production-request.42
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Updates not appearing | Check WebSocket connection, verify channels configured |
| Duplicate updates | Ensure events are idempotent, check queue config |
| Slow updates | Reduce polling interval, check server resources |
| Connection drops | Verify Redis/Pusher credentials, check network |
| High latency | Add caching, optimize database queries, scale servers |
