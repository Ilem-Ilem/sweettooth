<?php

namespace App\Livewire\BranchDashboard\AuditManagement;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Services\AuditService;
use App\Models\ApprovalRequest;
use App\Models\ApprovalAuditRequest;
use Livewire\Attributes\{Layout, On, Title, Url};

#[Layout('components.layouts.app.branch-dashboard')]
#[Title("Audit Mnagement")]
class Index extends Component
{
    use WithPagination;

    public $branchId;
    public $tab = 'logs';
    public $search = '';
    public $filterDepartment = '';
    public $filterAction = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'logged_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        if (is_super_admin()) {
            $this->branchId = request()->query('b_id') ?? current_branch_id();
        } else {
            $this->branchId = request()->query('b_id');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatingFilterAction()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function approveRequest($requestId)
    {
        $request = ApprovalAuditRequest::find($requestId);

        if ($request && $request->status === 'pending') {
            // Execute the approved action - delegates to action handler
            $auditable = $this->executeApprovedAction($request);

            $approver = auth()->user() ?? auth('employees')->user();
            $request->update([
                'status' => 'approved',
                'approver_id' => $approver->id,
                'approver_type' => get_class($approver),
                'approved_at' => now(),
            ]);


            // Log the approval action
            $actionName = str_replace(':', '_', $request->action);
            AuditService::log(
                $approver,
                "approve_{$actionName}",
                $auditable,
                'Approved by ' . $approver->name,
                'completed'
            );

            $this->dispatch('toast', message: 'Request approved successfully', type: 'success');
            $this->resetPage();
        }
    }

    /**
     * Execute an approved action based on the request type
     * Generic handler that works with any model/action combination
     * Supports: create, update, delete, and sync operations
     */
    private function executeApprovedAction(ApprovalAuditRequest $request)
    {
        // Parse action format: "action:model" or "action:model:relationship" (e.g., "create:department", "sync:employee:roles")
        $parts = explode(':', $request->action);
        $action = $parts[0];
        $model = $parts[1] ?? null;
        $relationship = $parts[2] ?? null;
        $modelId = $request->payload['id'] ?? null;

        $auditable = null;

        try {
            match ($action) {
                'create' => $auditable = $this->handleCreateAction($model, $request->payload),
                'update' => $auditable = $this->handleUpdateAction($model, $modelId, $request->payload),
                'delete' => $auditable = $this->handleDeleteAction($model, $modelId),
                'sync' => $auditable = $this->handleSyncAction($model, $modelId, $relationship, $request->payload),
                default => null,
            };
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $auditable;
    }

    /**
     * Handle create actions for any model
     */
    private function handleCreateAction(string $modelName, array $payload)
    {

        $modelClass = $modelName;
        if (!$modelClass) {
            return null;
        }

        // Extract only fillable fields from the payload
        $modelClass = "\\$modelClass";
        $model = new $modelClass;
        $fillable = $model->getFillable();
        $createData = array_intersect_key($payload, array_flip($fillable));
        $createData['slug'] = Str::slug($createData['name']);

        return $modelClass::create($createData);
    }

    /**
     * Handle update actions for any model
     */
    private function handleUpdateAction(string $modelName, ?int $modelId, array $payload)
    {


        $modelClass = $modelName;
        if (!$modelClass || !$modelId) {
            return null;
        }

        $auditable = $modelClass::find($modelId);
        if ($auditable) {
            // Extract only fillable fields from the payload
            $fillable = $auditable->getFillable();
            $updateData = array_intersect_key($payload, array_flip($fillable));
            $auditable->update($updateData);
        }

        return $auditable;
    }

    /**
     * Handle delete actions for any model
     */
    private function handleDeleteAction(string $modelName, ?int $modelId)
    {
        // $modelMap = [
        //     'department' => Department::class,

        // ];

        $modelClass = $modelName;
        if (!$modelClass || !$modelId) {
            return null;
        }

        $auditable = $modelClass::find($modelId);
        if ($auditable) {
            $auditable->delete();
        }

        return $auditable;
    }

    /**
     * Handle sync actions for many-to-many relationships
     * Supports syncing roles, permissions, and other relationships
     */
    private function handleSyncAction(string $modelName, ?int $modelId, ?string $relationship, array $payload)
    {
        $modelClass = $modelName;
        if (!$modelClass || !$modelId || !$relationship) {
            return null;
        }

        $auditable = $modelClass::find($modelId);
        if (!$auditable) {
            return null;
        }

        // Get the sync data from payload
        // Expected format: $payload['sync_data'] = [1, 2, 3] or [1 => ['pivot_col' => 'val'], ...]
        $syncData = $payload['sync_data'] ?? $payload[$relationship] ?? [];

        if (empty($syncData)) {
            return $auditable;
        }

        try {
            // Execute the sync operation
            if (method_exists($auditable, $relationship)) {
                // Direct relationship method exists
                $auditable->{$relationship}()->sync($syncData);
            } else {
                // Try as dynamic relationship
                $auditable->$relationship()->sync($syncData);
            }
        } catch (\Exception $e) {
            throw new \Exception("Failed to sync {$relationship}: " . $e->getMessage());
        }

        return $auditable;
    }

    public function rejectRequest($requestId)
    {
        $request = ApprovalAuditRequest::find($requestId);
        if ($request && $request->status === 'pending') {
            $approver = auth()->user() ?? auth('employees')->user();
            $request->update([
                'status' => 'rejected',
                'approver_id' => $approver->id,
                'approver_type' => get_class($approver),
                'denied_at' => now(),
            ]);

            // Log the rejection action
            AuditService::log(
                $approver,
                'reject_' . str_replace(':department', '', $request->action),
                null,
                'Rejected by ' . $approver->name,
                'completed'
            );

            $this->dispatch('toast', message: 'Request rejected', type: 'info');
            $this->resetPage();
        }
    }

    public function updatingTab()
    {
        $this->clearFilters();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDepartment = '';
        $this->filterAction = '';
        $this->filterStatus = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $logsQuery = AuditLog::query();
        $approvalsQuery = ApprovalAuditRequest::query();
        $isSuperAdmin = is_super_admin();

        // Apply search filter
        if ($this->search) {
            $logsQuery->where('description', 'like', "%{$this->search}%")
                ->orWhere('action', 'like', "%{$this->search}%");
            $approvalsQuery->where('description', 'like', "%{$this->search}%")
                ->orWhere('action', 'like', "%{$this->search}%");
        }

        // Apply branch filter (from branchId) - show all if no branch specified for super admin
        if ($this->branchId) {
            $logsQuery->where('branch_id', $this->branchId);
            $approvalsQuery->where(function ($q) {
                $q->where('branch_id', $this->branchId)
                    ->orWhereNull('branch_id'); // Show records with null branch_id too
            });
        } else if (!$isSuperAdmin && current_branch_id()) {
            // Non-super admin: filter to their branch
            $logsQuery->where('branch_id', current_branch_id());
            $approvalsQuery->where(function ($q) {
                $q->where('branch_id', current_branch_id())
                    ->orWhereNull('branch_id');
            });
        }

        // Apply department filter (all users)
        if ($this->filterDepartment) {
            $logsQuery->where('department_id', $this->filterDepartment);
            $approvalsQuery->where('department_id', $this->filterDepartment);
        }

        // Apply action filter
        if ($this->filterAction) {
            $logsQuery->where('action', $this->filterAction);
            $approvalsQuery->where('action', $this->filterAction);
        }

        // Apply status filter
        if ($this->filterStatus) {
            $logsQuery->where('status', $this->filterStatus);
            $approvalsQuery->where('status', $this->filterStatus);
        }

        // Apply date filter
        if ($this->filterDateFrom) {
            $logsQuery->whereDate('logged_at', '>=', $this->filterDateFrom);
            $approvalsQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $logsQuery->whereDate('logged_at', '<=', $this->filterDateTo);
            $approvalsQuery->whereDate('created_at', '<=', $this->filterDateTo);
        }

        // Get data based on tab
        if ($this->tab === 'logs') {
            $logs = $logsQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(15);

            $actions = AuditLog::distinct('action')->pluck('action')->sort();
            $statuses = ['pending', 'completed', 'rejected'];

            return view('livewire.branch-dashboard.audit-management.index', [
                'logs' => $logs,
                'approvals' => null,
                'actions' => $actions,
                'statuses' => $statuses,
                'isSuperAdmin' => $isSuperAdmin,
            ]);
        } else {
            $approvals = $approvalsQuery
                ->with('requester', 'approver')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $actions = ApprovalAuditRequest::distinct('action')->pluck('action')->sort();
            $statuses = ['pending', 'approved', 'rejected'];

            return view('livewire.branch-dashboard.audit-management.index', [
                'logs' => null,
                'approvals' => $approvals,
                'actions' => $actions,
                'statuses' => $statuses,
                'isSuperAdmin' => $isSuperAdmin,
            ]);
        }
    }
}
