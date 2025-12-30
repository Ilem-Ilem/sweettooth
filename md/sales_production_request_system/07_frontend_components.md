# Sales to Production Request System - Frontend Components

Detailed specifications for UI components in both Sales and Production departments.

## Table of Contents
1. Sales Department Components
2. Production Department Components
3. Shared Components
4. Component Props & Events
5. State Management
6. Styling & Responsive Design

---

## SALES DEPARTMENT COMPONENTS

### 1. ProductionRequestForm

**Purpose**: Create new production request  
**Location**: `resources/views/livewire/sales/production/CreateProductionRequest.php`  
**Type**: Livewire Component

#### Features
- Department selector dropdown
- Product search and multi-select
- Batch quantity input
- Priority selector
- Notes textarea
- Form validation
- Submit button with loading state

#### Data Structure
```php
class CreateProductionRequest extends Component
{
    public $selectedDepartment = null;
    public $selectedProducts = [];
    public $batchQuantities = [];
    public $priority = 'normal';
    public $notes = '';
    public $availableProducts = [];
    public $availableDepartments = [];
    
    public function mount()
    {
        $this->availableDepartments = Department::where('type', 'production')
            ->active()
            ->get();
            
        // Default to primary production partner
        if (auth()->user()->salesDepartment) {
            $this->selectedDepartment = 
                auth()->user()->salesDepartment->primary_production_partner_id;
        }
    }
    
    public function updatedSelectedDepartment()
    {
        // Load products for selected department
        $this->availableProducts = Product::where('department_id', $this->selectedDepartment)
            ->active()
            ->get();
    }
    
    public function addProduct($productId)
    {
        if (!in_array($productId, $this->selectedProducts)) {
            $this->selectedProducts[] = $productId;
            $this->batchQuantities[$productId] = 1;
        }
    }
    
    public function removeProduct($productId)
    {
        unset($this->selectedProducts[array_search($productId, $this->selectedProducts)]);
        unset($this->batchQuantities[$productId]);
    }
    
    public function submitRequest()
    {
        $this->validate([
            'selectedDepartment' => 'required|exists:departments,id',
            'selectedProducts' => 'required|array|min:1',
            'batchQuantities.*' => 'required|integer|min:1',
            'priority' => 'required|in:normal,urgent',
        ]);
        
        $request = ProductionRequest::create([
            'sales_department_id' => auth()->user()->department_id,
            'production_department_id' => $this->selectedDepartment,
            'status' => 'pending',
            'priority' => $this->priority,
            'created_by_id' => auth()->id(),
            'notes' => $this->notes,
        ]);
        
        // Create request items
        foreach ($this->selectedProducts as $productId) {
            ProductionRequestItem::create([
                'production_request_id' => $request->id,
                'product_id' => $productId,
                'batch_quantity' => $this->batchQuantities[$productId],
            ]);
        }
        
        // Broadcast event
        broadcast(new \App\Events\RequestCreated($request))->toOthers();
        
        $this->dispatch('request-created', ['requestId' => $request->id]);
        $this->reset();
        $this->dispatch('notify', ['message' => 'Request created successfully']);
    }
}
```

#### Blade Template
```blade
<div class="production-request-form">
    <form wire:submit="submitRequest">
        <!-- Department Selector -->
        <div class="form-group">
            <label for="department">Target Production Department</label>
            <select wire:model="selectedDepartment" id="department" class="form-control">
                <option value="">-- Select Department --</option>
                @foreach($availableDepartments as $dept)
                    <option value="{{ $dept->id }}">
                        {{ $dept->name }} ({{ $dept->staff_count }} staff)
                    </option>
                @endforeach
            </select>
            @error('selectedDepartment')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Product Selector -->
        @if($selectedDepartment)
            <div class="form-group">
                <label>Available Products</label>
                <div class="products-grid">
                    @foreach($availableProducts as $product)
                        <div class="product-card">
                            <div class="product-header">
                                <h4>{{ $product->name }}</h4>
                                <span class="sku">{{ $product->sku }}</span>
                            </div>
                            <div class="product-info">
                                <p>Batch Size: {{ $product->batch_size }}</p>
                                <p>Est. Time: {{ $product->prep_time }}</p>
                            </div>
                            
                            @if(in_array($product->id, $selectedProducts))
                                <div class="selected-indicator">
                                    <input 
                                        type="number" 
                                        wire:model="batchQuantities.{{ $product->id }}"
                                        min="1"
                                        class="batch-input"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="removeProduct({{ $product->id }})"
                                        class="btn-remove"
                                    >
                                        Remove
                                    </button>
                                </div>
                            @else
                                <button 
                                    type="button"
                                    wire:click="addProduct({{ $product->id }})"
                                    class="btn-add"
                                >
                                    + Add
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Selected Products Summary -->
        @if(count($selectedProducts) > 0)
            <div class="form-group selected-products">
                <label>Selected Products ({{ count($selectedProducts) }})</label>
                <div class="summary-list">
                    @foreach($selectedProducts as $productId)
                        @php
                            $product = collect($availableProducts)->find(fn($p) => $p->id == $productId);
                        @endphp
                        @if($product)
                            <div class="summary-item">
                                <span>{{ $product->name }}</span>
                                <span class="quantity">Batches: {{ $batchQuantities[$productId] ?? 1 }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Priority -->
        <div class="form-group">
            <label for="priority">Priority</label>
            <select wire:model="priority" id="priority" class="form-control">
                <option value="normal">Normal</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>

        <!-- Notes -->
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea 
                wire:model="notes" 
                id="notes" 
                class="form-control"
                rows="3"
                placeholder="Additional instructions for production..."
            ></textarea>
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Create Request</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>
</div>

<style scoped>
.production-request-form {
    max-width: 800px;
}

.form-group {
    margin-bottom: 2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.product-card {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s;
}

.product-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);
}

.product-card.selected {
    border-color: #10b981;
    background-color: #ecfdf5;
}

.selected-products {
    background-color: #f3f4f6;
    padding: 1rem;
    border-radius: 8px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #d1d5db;
}
</style>
```

---

### 2. ProductionRequestDashboard

**Purpose**: View all production requests and track progress  
**Location**: `resources/views/livewire/sales/production/RequestDashboard.php`  
**Type**: Livewire Component with Real-time Updates

#### Features
- List all requests (with pagination)
- Real-time progress updates
- Status filters
- Priority indicators
- Progress bar visualization
- Request details modal
- Dispatch notification alerts

#### Component Code
```php
class RequestDashboard extends Component
{
    public $requests = [];
    public $statusFilter = [];
    public $priorityFilter = null;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $page = 1;
    public $perPage = 20;
    public $selectedRequest = null;
    public $showDetails = false;

    protected $listeners = [
        'echo:production-request.{request_id},ProgressUpdated' => 'onProgressUpdated',
        'echo:sales-user.' . auth()->id() . ',DispatchCreated' => 'onDispatchCreated',
    ];

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $query = ProductionRequest::where('created_by_id', auth()->id())
            ->with('productionDepartment', 'progressFeedback');

        if (!empty($this->statusFilter)) {
            $query->whereIn('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $this->requests = $query
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate($this->perPage);
    }

    public function onProgressUpdated($data)
    {
        $this->loadRequests();
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "Progress update on {$data['product_names']}"
        ]);
    }

    public function onDispatchCreated($data)
    {
        $this->loadRequests();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Dispatch ready for verification!',
            'action_url' => route('sales.dispatch.verify', $data['request_id'])
        ]);
    }

    public function viewDetails($requestId)
    {
        $this->selectedRequest = ProductionRequest::with('progressFeedback', 'dispatches')
            ->find($requestId);
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedRequest = null;
    }

    public function render()
    {
        return view('livewire.sales.production.request-dashboard');
    }
}
```

---

### 3. DispatchVerificationWidget

**Purpose**: Accept or reject dispatched products  
**Location**: `resources/views/livewire/sales/dispatch/VerifyDispatch.php`  
**Type**: Livewire Component

#### Features
- Display dispatch details
- Product verification checklist
- Quantity comparison (requested vs received)
- Accept/Reject buttons
- Feedback textarea
- Success/Error messages

#### Component Code
```php
class VerifyDispatch extends Component
{
    public $request;
    public $dispatch;
    public $verified = [];
    public $rejectionReason = '';
    public $rejectionNotes = '';
    public $showRejectionForm = false;

    public function mount(ProductionRequest $request)
    {
        $this->request = $request;
        $this->dispatch = $request->dispatches->first();
    }

    public function acceptDispatch()
    {
        $this->request->update(['status' => 'accepted']);
        $this->dispatch->update(['status' => 'accepted']);

        broadcast(new \App\Events\DispatchAccepted($this->request))->toOthers();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Dispatch accepted successfully'
        ]);

        return redirect()->route('sales.requests');
    }

    public function rejectDispatch()
    {
        $this->validate([
            'rejectionReason' => 'required|in:quality,quantity,missing,other',
            'rejectionNotes' => 'required|min:10',
        ]);

        $this->request->update(['status' => 'rejected']);
        $this->dispatch->update(['status' => 'rejected']);

        // Create feedback notification
        ProductionRequestFeedback::create([
            'production_request_id' => $this->request->id,
            'type' => 'rejection',
            'reason' => $this->rejectionReason,
            'message' => $this->rejectionNotes,
            'created_by_id' => auth()->id(),
        ]);

        broadcast(new \App\Events\DispatchRejected($this->request))->toOthers();

        $this->dispatch('notify', [
            'type' => 'warning',
            'message' => 'Dispatch rejected. Feedback sent to production.'
        ]);

        return redirect()->route('sales.requests');
    }

    public function render()
    {
        return view('livewire.sales.dispatch.verify-dispatch');
    }
}
```

---

## PRODUCTION DEPARTMENT COMPONENTS

### 1. ProductionRequestBoard

**Purpose**: Real-time queue of production requests  
**Location**: `resources/views/livewire/production/RequestBoard.php`  
**Type**: Livewire Component with WebSocket

#### Features
- Real-time list of incoming requests
- Priority sorting (urgent first)
- Status badges
- Quick action buttons
- Request notifications with sound
- Auto-refresh on new requests

#### Component Code
```php
class ProductionRequestBoard extends Component
{
    public $requests = [];
    public $selectedRequest = null;
    public $showDetails = false;

    protected $listeners = [
        'echo:production-dept.' . auth()->user()->department_id . ',NewRequest' => 'onNewRequest',
    ];

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requests = ProductionRequest::where('production_department_id', auth()->user()->department_id)
            ->where('status', '!=', 'accepted')
            ->with('createdBy', 'progressFeedback')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function onNewRequest($data)
    {
        $this->loadRequests();
        
        // Play notification sound
        $this->dispatch('play-sound', ['sound' => 'notification']);
        
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "New production request: {$data['product_names']}",
            'duration' => 10
        ]);
    }

    public function viewRequest($requestId)
    {
        $this->selectedRequest = ProductionRequest::with('products', 'progressFeedback')
            ->find($requestId);
        $this->showDetails = true;
    }

    public function startProduction($requestId)
    {
        $request = ProductionRequest::find($requestId);
        $request->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Create initial progress record
        ProductionProgressFeedback::create([
            'production_request_id' => $request->id,
            'milestone' => 'started',
            'progress_percentage' => 0,
            'notes' => 'Production started',
            'updated_by_id' => auth()->id(),
        ]);

        broadcast(new \App\Events\ProgressUpdated($request))->toOthers();

        $this->loadRequests();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Production started'
        ]);
    }

    public function render()
    {
        return view('livewire.production.request-board');
    }
}
```

---

### 2. ProductionProgressTracker

**Purpose**: Update production progress with milestones  
**Location**: `resources/views/livewire/production/ProgressTracker.php`  
**Type**: Livewire Component

#### Features
- Current request display
- Milestone selector
- Progress percentage slider
- Notes textarea
- Update history timeline
- Save button with validation

#### Component Code
```php
class ProgressTracker extends Component
{
    public $request;
    public $currentMilestone = 'in_production';
    public $progressPercentage = 0;
    public $notes = '';
    public $progressHistory = [];

    public function mount(ProductionRequest $request)
    {
        $this->request = $request;
        $this->loadProgressHistory();
        
        // Get latest milestone
        $latest = $request->progressFeedback()->latest()->first();
        if ($latest) {
            $this->currentMilestone = $latest->milestone;
            $this->progressPercentage = $latest->progress_percentage;
        }
    }

    public function loadProgressHistory()
    {
        $this->progressHistory = $this->request->progressFeedback()
            ->latest()
            ->get();
    }

    public function updateProgress()
    {
        $this->validate([
            'currentMilestone' => 'required|in:started,in_production,quality_check,completed',
            'progressPercentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Create progress record
        $feedback = ProductionProgressFeedback::create([
            'production_request_id' => $this->request->id,
            'milestone' => $this->currentMilestone,
            'progress_percentage' => $this->progressPercentage,
            'notes' => $this->notes,
            'updated_by_id' => auth()->id(),
        ]);

        // Update request status
        if ($this->currentMilestone === 'completed') {
            $this->request->update(['status' => 'quality_check']);
        } elseif ($this->currentMilestone === 'started') {
            $this->request->update(['status' => 'in_progress']);
        }

        // Broadcast update
        broadcast(new \App\Events\ProgressUpdated($feedback))->toOthers();

        // Clear form
        $this->notes = '';

        // Reload history
        $this->loadProgressHistory();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Progress updated successfully'
        ]);
    }

    public function completeProduction()
    {
        $this->request->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        ProductionProgressFeedback::create([
            'production_request_id' => $this->request->id,
            'milestone' => 'completed',
            'progress_percentage' => 100,
            'notes' => 'Production complete',
            'updated_by_id' => auth()->id(),
        ]);

        broadcast(new \App\Events\ProgressUpdated($this->request))->toOthers();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Production marked complete. Ready to dispatch.'
        ]);

        return redirect()->route('production.dispatch', $this->request->id);
    }

    public function render()
    {
        return view('livewire.production.progress-tracker');
    }
}
```

---

## SHARED COMPONENTS

### 1. RequestStatusBadge

**Purpose**: Display status with appropriate styling  
**Location**: `resources/views/components/request-status-badge.blade.php`  
**Type**: Blade Component

```blade
@props(['status' => 'pending', 'size' => 'md'])

@php
    $colors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'quality_check' => 'bg-purple-100 text-purple-800',
        'completed' => 'bg-green-100 text-green-800',
        'dispatched' => 'bg-indigo-100 text-indigo-800',
        'accepted' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
    ];

    $icons = [
        'pending' => 'clock',
        'in_progress' => 'play',
        'quality_check' => 'check-circle',
        'completed' => 'check-double',
        'dispatched' => 'send',
        'accepted' => 'check',
        'rejected' => 'x-circle',
        'cancelled' => 'ban',
    ];

    $sizes = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-2 text-sm',
        'lg' => 'px-4 py-3 text-base',
    ];
@endphp

<span class="inline-flex items-center gap-1 {{ $colors[$status] ?? $colors['pending'] }} {{ $sizes[$size] ?? $sizes['md'] }} rounded-full font-medium">
    <i class="fas fa-{{ $icons[$status] }}"></i>
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
```

---

### 2. ProgressTimeline

**Purpose**: Show milestone history  
**Location**: `resources/views/components/progress-timeline.blade.php`  
**Type**: Blade Component

```blade
@props(['feedbacks' => [], 'compact' => false])

<div class="progress-timeline {{ $compact ? 'compact' : '' }}">
    @foreach($feedbacks as $feedback)
        <div class="timeline-item">
            <div class="timeline-marker" style="background-color: var(--color-{{ $feedback->milestone }})">
                <i class="fas fa-{{ $feedback->milestone === 'completed' ? 'check-circle' : 'circle' }}"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-header">
                    <strong>{{ $feedback->milestone_label }}</strong>
                    <span class="timestamp">{{ $feedback->created_at->format('H:i') }}</span>
                </div>
                @if(!$compact)
                    <div class="progress-bar-mini">
                        <div class="fill" style="width: {{ $feedback->progress_percentage }}%"></div>
                    </div>
                @endif
                @if($feedback->notes)
                    <p class="notes">{{ $feedback->notes }}</p>
                @endif
            </div>
        </div>
    @endforeach
</div>

<style scoped>
.progress-timeline {
    position: relative;
    padding: 2rem 0 0 2rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.timeline-content {
    flex: 1;
    padding: 0.75rem 1rem;
    background-color: #f9fafb;
    border-radius: 6px;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.timestamp {
    font-size: 0.75rem;
    color: #6b7280;
}

.progress-bar-mini {
    width: 100%;
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin: 0.5rem 0;
}

.progress-bar-mini .fill {
    height: 100%;
    background-color: #3b82f6;
    transition: width 0.3s;
}

.notes {
    font-size: 0.875rem;
    color: #4b5563;
    margin: 0.5rem 0 0 0;
}

.compact .progress-bar-mini {
    display: none;
}
</style>
```

---

## STATE MANAGEMENT

### Livewire Component State Flow
```
User Action
    ↓
Livewire Event Handler
    ↓
Validation
    ↓
Database Update
    ↓
Broadcast Event
    ↓
Real-time Update to Subscribed Clients
    ↓
Component Re-render (Blade)
```

---

## RESPONSIVE DESIGN

### Breakpoints
```scss
// Mobile First
$breakpoints: (
    'sm': 640px,
    'md': 768px,
    'lg': 1024px,
    'xl': 1280px,
    '2xl': 1536px,
);

// Usage
@media (min-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

### Mobile Optimizations
- Stack forms vertically
- Full-width buttons
- Collapsible sections
- Bottom sheet modals
- Touch-friendly sizing (48px+ tap targets)

---

## ACCESSIBILITY

### WCAG 2.1 Compliance
- Semantic HTML5
- ARIA labels on interactive elements
- Keyboard navigation support
- Color contrast ratios ≥ 4.5:1
- Focus indicators visible
- Form error messages linked to inputs

### Example
```blade
<button 
    aria-label="Start production on Croissants request"
    aria-describedby="production-help"
    class="btn btn-primary"
>
    Start Production
</button>
<span id="production-help" class="help-text">
    This will mark the request as in_progress
</span>
```

---

## TESTING COMPONENTS

### Unit Tests
```php
class CreateProductionRequestTest extends TestCase
{
    test('user can create production request')
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();
        $product = Product::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateProductionRequest::class)
            ->set('selectedDepartment', $department->id)
            ->call('addProduct', $product->id)
            ->set('batchQuantities', [$product->id => 10])
            ->call('submitRequest')
            ->assertDispatched('request-created');

        $this->assertDatabaseHas('production_requests', [
            'created_by_id' => $user->id,
            'production_department_id' => $department->id,
        ]);
    }
}
```

---

## PERFORMANCE OPTIMIZATION

### Component Optimization
1. Use `wire:lazy` for below-fold content
2. Use `wire:loading` for async operations
3. Defer validation with `wire:blur`
4. Use `wire:model.defer` to reduce updates
5. Implement pagination (20-50 items)
6. Eager load relationships

### Example
```blade
<input 
    wire:model.defer="batchQuantities"
    wire:blur="validateBatchQuantities"
    type="number"
>
```

---

## BROWSER SUPPORT

- Chrome/Edge: Latest 2 versions
- Firefox: Latest 2 versions
- Safari: Latest 2 versions
- Mobile: iOS 12+, Android 5+
