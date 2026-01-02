# 03 - Bulk Actions Improvements & Enhancements

## Current State

### Implemented Bulk Actions
- ✅ **Delete** - Most components have working `bulkDelete()`
- ⚠️ **Export** - Defined but not implemented in 5 components, missing in 26 others

### Bulk Action Flow
1. User selects items (multi-select mode)
2. Chooses action from dropdown
3. `performBulkAction()` dispatches to specific handler
4. Handler method executes the action

## Missing Bulk Actions to Implement

### 1. Bulk Status Update
Update status for multiple items at once

```php
protected array $bulkActions = [
    'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
    'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    'activate' => ['label' => 'Activate Selected', 'method' => 'bulkActivate'],
    'deactivate' => ['label' => 'Deactivate Selected', 'method' => 'bulkDeactivate'],
    'archive' => ['label' => 'Archive Selected', 'method' => 'bulkArchive'],
];

public function bulkActivate(): void
{
    YourModel::whereIn('id', $this->selectedIds)
        ->update(['status' => 'active']);
    
    session()->flash('success', count($this->selectedIds) . ' items activated.');
    $this->resetBulkSelection();
}

public function bulkDeactivate(): void
{
    YourModel::whereIn('id', $this->selectedIds)
        ->update(['status' => 'inactive']);
    
    session()->flash('success', count($this->selectedIds) . ' items deactivated.');
    $this->resetBulkSelection();
}

public function bulkArchive(): void
{
    YourModel::whereIn('id', $this->selectedIds)
        ->update(['archived_at' => now()]);
    
    session()->flash('success', count($this->selectedIds) . ' items archived.');
    $this->resetBulkSelection();
}
```

### 2. Bulk Assignment
Assign items to users, departments, branches, etc.

```php
public array $assignToUser = null;

protected array $bulkActions = [
    'assign_to_user' => ['label' => 'Assign to User', 'method' => 'bulkAssignToUser'],
    'assign_to_department' => ['label' => 'Assign to Department', 'method' => 'bulkAssignToDepartment'],
];

public function bulkAssignToUser(): void
{
    if (!$this->assignToUser) {
        session()->flash('warning', 'Please select a user first.');
        return;
    }

    YourModel::whereIn('id', $this->selectedIds)
        ->update(['assigned_to' => $this->assignToUser]);
    
    session()->flash('success', count($this->selectedIds) . ' items assigned.');
    $this->resetBulkSelection();
    $this->assignToUser = null;
}

public function bulkAssignToDepartment(): void
{
    // Similar pattern for department assignment
}
```

### 3. Bulk Category/Tag Assignment

```php
public array $selectedTags = [];

protected array $bulkActions = [
    'add_tags' => ['label' => 'Add Tags', 'method' => 'bulkAddTags'],
    'remove_tags' => ['label' => 'Remove Tags', 'method' => 'bulkRemoveTags'],
];

public function bulkAddTags(): void
{
    if (empty($this->selectedTags)) {
        session()->flash('warning', 'Please select tags first.');
        return;
    }

    $items = YourModel::whereIn('id', $this->selectedIds)->get();
    
    foreach ($items as $item) {
        $item->tags()->syncWithoutDetaching($this->selectedTags);
    }
    
    session()->flash('success', 'Tags added to ' . count($items) . ' items.');
    $this->resetBulkSelection();
    $this->selectedTags = [];
}
```

### 4. Bulk Role Assignment (for Employees/Users)

```php
public ?string $selectedRole = null;

protected array $bulkActions = [
    'assign_role' => ['label' => 'Assign Role', 'method' => 'bulkAssignRole'],
    'remove_role' => ['label' => 'Remove Role', 'method' => 'bulkRemoveRole'],
];

public function bulkAssignRole(): void
{
    if (!$this->selectedRole) {
        session()->flash('warning', 'Please select a role.');
        return;
    }

    $users = User::whereIn('id', $this->selectedIds)->get();
    
    foreach ($users as $user) {
        $user->assignRole($this->selectedRole);
    }
    
    session()->flash('success', 'Role assigned to ' . count($users) . ' users.');
    $this->resetBulkSelection();
    $this->selectedRole = null;
}
```

### 5. Bulk Data Import/Upload

```php
public $importFile = null;

protected array $bulkActions = [
    'import' => ['label' => 'Import Data', 'method' => 'bulkImport'],
];

public function bulkImport(): void
{
    if (!$this->importFile) {
        session()->flash('warning', 'Please select a file to import.');
        return;
    }

    // Process file
    \Excel::import(new YourImport, $this->importFile);
    
    session()->flash('success', 'Data imported successfully.');
    $this->resetBulkSelection();
    $this->importFile = null;
}
```

### 6. Bulk Send Notifications/Messages

```php
public ?string $messageTemplate = null;

protected array $bulkActions = [
    'send_notification' => ['label' => 'Send Notification', 'method' => 'bulkSendNotification'],
    'send_email' => ['label' => 'Send Email', 'method' => 'bulkSendEmail'],
];

public function bulkSendNotification(): void
{
    if (!$this->messageTemplate) {
        session()->flash('warning', 'Please select a message template.');
        return;
    }

    $users = User::whereIn('id', $this->selectedIds)->get();
    
    foreach ($users as $user) {
        $user->notify(new YourNotification($this->messageTemplate));
    }
    
    session()->flash('success', 'Notifications sent to ' . count($users) . ' users.');
    $this->resetBulkSelection();
}
```

## BaseComponent Enhancement

Update `app/Livewire/BaseComponent.php` to support more features:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

abstract class BaseComponent extends Component
{
    use WithPagination, Interactions;

    public array $selectedIds = [];
    public bool $selectAll = false;
    public bool $bulkMode = false;
    public ?string $selectedBulkAction = null;
    
    // New properties for enhanced bulk actions
    public ?string $bulkSearchQuery = null;
    public array $bulkFilters = [];
    public ?string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export', 'method' => 'exportSelected'],
    ];

    // Action configuration
    protected array $actionConfig = [
        'confirmDeleteMessage' => 'Are you sure you want to delete these items?',
        'showActionButtons' => true,
        'requireConfirmation' => ['delete', 'archive'],
        'allowMultipleSelections' => true,
    ];

    protected function mountBase(): void
    {
        $this->resetBulkSelection();
    }

    protected function resetBulkSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->bulkMode = false;
        $this->selectedBulkAction = null;
        $this->bulkSearchQuery = null;
    }

    public function toggleBulkMode(): void
    {
        $this->bulkMode = !$this->bulkMode;
        if (!$this->bulkMode) {
            $this->resetBulkSelection();
        }
    }

    public function updatedSelectAll($value): void
    {
        $this->selectedIds = $value ? $this->getAllSelectableIds() : [];
    }

    public function toggleSelectId($id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_filter(
                $this->selectedIds,
                fn($selectedId) => $selectedId !== $id
            );
        } else {
            $this->selectedIds[] = $id;
        }

        $this->selectAll = count($this->selectedIds) === count($this->getAllSelectableIds());
    }

    public function performBulkAction(): void
    {
        if (empty($this->selectedBulkAction)) {
            session()->flash('error', 'Please select a bulk action.');
            return;
        }

        $action = $this->bulkActions[$this->selectedBulkAction] ?? null;

        if (!$action || !isset($action['method'])) {
            session()->flash('error', 'Invalid or undefined bulk action.');
            return;
        }

        $method = $action['method'];

        if (!method_exists($this, $method)) {
            session()->flash('error', "The method '{$method}' does not exist.");
            return;
        }

        // Check if action requires confirmation
        if (in_array($this->selectedBulkAction, $this->actionConfig['requireConfirmation'] ?? [])) {
            $this->confirm(
                'Confirm Action',
                $method,
                'Action completed successfully',
                'Action cancelled'
            );
        } else {
            try {
                $this->{$method}();
            } catch (\Throwable $e) {
                session()->flash('error', "Error: " . $e->getMessage());
            }
        }
    }

    public function getSelectedCount(): int
    {
        return count($this->selectedIds);
    }

    public function hasSelected(): bool
    {
        return $this->getSelectedCount() > 0;
    }

    protected function exportSelected(): void
    {
        if (empty($this->selectedIds)) {
            session()->flash('info', 'No items selected for export.');
            return;
        }

        session()->flash('success', 'Export started for ' . count($this->selectedIds) . ' items.');
    }

    // Abstract methods for child components
    abstract protected function getModelClass(): string;
    abstract protected function getAllSelectableIds(): array;
}
```

## Blade Template Updates

Update bulk action buttons in your Livewire view:

```blade
<div class="bulk-actions-bar" wire:loading.remove>
    @if($this->bulkMode && $this->hasSelected())
        <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-lg">
            <span class="text-sm font-medium">
                {{ $this->getSelectedCount() }} item{{ $this->getSelectedCount() !== 1 ? 's' : '' }} selected
            </span>

            <!-- Action Dropdown -->
            <select 
                wire:model.live="selectedBulkAction" 
                class="form-select text-sm"
                @if(empty($this->bulkActions)) disabled @endif
            >
                <option value="">Select Action...</option>
                @foreach($this->bulkActions as $key => $action)
                    <option value="{{ $key }}">{{ $action['label'] }}</option>
                @endforeach
            </select>

            <!-- Execute Button -->
            <button 
                wire:click="performBulkAction"
                @if(empty($this->selectedBulkAction)) disabled @endif
                class="btn btn-primary btn-sm"
            >
                Execute
            </button>

            <!-- Cancel Button -->
            <button 
                wire:click="resetBulkSelection"
                class="btn btn-secondary btn-sm"
            >
                Clear Selection
            </button>
        </div>
    @endif
</div>

<!-- Select All Checkbox -->
<div class="flex items-center">
    <input 
        type="checkbox" 
        wire:model.live="selectAll"
        @if(!$this->bulkMode) disabled @endif
        class="form-checkbox"
    >
    <label class="ml-2 text-sm">Select All on This Page</label>
</div>

<!-- Individual Row Checkbox -->
<input 
    type="checkbox" 
    wire:model.live="selectedIds"
    value="{{ $item->id }}"
    @if(!$this->bulkMode) disabled @endif
    class="form-checkbox"
>
```

## Confirmation Dialog

```php
public function confirmBulkDelete(): void
{
    $this->confirm(
        'Confirm Delete',
        'bulkDelete',
        count($this->selectedIds) . ' item(s) deleted successfully!',
        'Delete cancelled'
    );
}

public function bulkDelete(string $message): void
{
    $this->getModelClass()::whereIn('id', $this->selectedIds)->delete();
    
    session()->flash('success', $message);
    $this->resetBulkSelection();
    $this->dispatch('refreshTable');
}
```

## Auditing Bulk Actions

Track bulk operations in audit log:

```php
use App\Traits\Auditable;

public function bulkDelete(string $message): void
{
    $count = $this->getModelClass()::whereIn('id', $this->selectedIds)->delete();
    
    // Log the bulk action
    \App\Models\AuditLog::create([
        'user_id' => auth()->id(),
        'action' => 'bulk_delete',
        'model_type' => $this->getModelClass(),
        'model_ids' => implode(',', $this->selectedIds),
        'old_values' => null,
        'new_values' => null,
        'description' => "Bulk deleted {$count} items",
    ]);
    
    session()->flash('success', $message);
    $this->resetBulkSelection();
}
```

## Permissions for Bulk Actions

Check authorization before executing:

```php
public function performBulkAction(): void
{
    if (empty($this->selectedBulkAction)) {
        session()->flash('error', 'Please select a bulk action.');
        return;
    }

    $action = $this->bulkActions[$this->selectedBulkAction] ?? null;

    if (!$this->canPerformAction($this->selectedBulkAction)) {
        session()->flash('error', 'You do not have permission to perform this action.');
        return;
    }

    // Execute action...
}

protected function canPerformAction(string $action): bool
{
    $permission = match($action) {
        'delete' => 'delete',
        'export' => 'export',
        'activate' => 'activate',
        'deactivate' => 'deactivate',
        default => $action,
    };

    return auth()->user()->can($permission, $this->getModelClass());
}
```
