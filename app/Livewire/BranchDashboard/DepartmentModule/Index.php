<?php

namespace App\Livewire\BranchDashboard\DepartmentModule;

use App\Models\Branch;
use App\Models\Department;
use App\Livewire\BaseComponent;
use Livewire\Attributes\Computed;
use App\Models\DepartmentCategory;
use App\Models\ApprovalAuditRequest;
use App\Services\AuditService;
use App\Services\DepartmentApprovalService;
use App\Traits\Exportable;
use Livewire\Attributes\{Layout, On, Title, Url};

/**
 * Department Management Livewire Component
 * 
 * Handles listing, filtering, searching, and bulk operations on departments.
 * 
 * Key Features:
 * - Multi-branch support with branch context validation
 * - Advanced search and filtering capabilities
 * - Approval workflow for non-super-admin delete operations
 * - Bulk delete operations with audit logging
 * - CSV export functionality
 * - Cached department categories (4200 seconds)
 * 
 * Branch Security:
 * - Employees can only manage departments in their assigned branch
 * - Super admins can manage departments across all branches
 * - All operations are logged via AuditService
 * 
 * @see App\Livewire\BaseComponent
 * @see App\Services\AuditService
 */
#[Layout('components.layouts.app.branch-dashboard')]
#[Title("Manage Departments")]
class Index extends BaseComponent
{
    use Exportable;
    /**
     * Pagination: number of items per page
     * @var int
     */
    public ?int $quantity = 5;

    /**
     * Quick search filter across department names
     * @var string|null
     */
    public ?string $search = null;

    /**
     * Advanced search across multiple fields (name and description)
     * @var string|null
     */
    public ?string $advancedSearch = null;

    /**
     * Date range filter: start date for created_at
     * @var string|null
     */
    public ?string $dateFrom = null;

    /**
     * Date range filter: end date for created_at
     * @var string|null
     */
    public ?string $dateTo = null;

    /**
     * Current branch ID - required for all operations
     * Kept in URL via #[Url(keep: true)] to maintain branch context
     * @var string|null
     */
    #[Url(keep: true)]
    public ?string $b_id = null;

    /**
     * Handle branch change event from BranchSelector component
     * 
     * When super admin switches branches via BranchSelector,
     * this listener updates the current branch context and resets pagination.
     * 
     * @param mixed $branchId The new branch ID from BranchSelector event
     * @return void
     */
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    /**
     * Category filter - limits displayed departments to specific category
     * @var string|null
     */
    public ?string $filterCategory = null;

    /**
     * Branch filter - alternative filter for branch (rarely used, prefer b_id)
     * @var string|null
     */
    public ?string $filterBranch = null;

    /**
     * ID of department selected for delete operation
     * @var int|null
     */
    public ?int $selectedDepartmentId = null;

    /**
     * Controls visibility of delete reason modal
     * Non-super-admins must provide a reason for deletion
     * @var bool
     */
    public bool $showDeleteReasonModal = false;

    /**
     * Reason provided by user for delete operation
     * Required for non-super-admin users (minimum 5 characters)
     * @var string
     */
    public string $deleteReason = '';
  
    /**
     * Available bulk actions
     * Extends BaseComponent with custom bulk delete and export actions
     * @var array
     */
    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    /**
     * Get the model class for bulk operations
     * Required by BaseComponent trait
     * 
     * @return string
     */
    protected function getModelClass(): string
    {
        return Department::class;
    }

    /**
     * Get all selectable IDs for bulk operations
     * 
     * Returns all department IDs from the filtered query results.
     * Used by BaseComponent for select-all functionality.
     * 
     * @return array
     */
    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    /**
     * Get all department categories with caching
     * 
     * Cached for 4200 seconds (70 minutes) since categories rarely change.
     * Used for category filter dropdown and validation.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed(seconds: 4200)]
    public function getDepartmentCategories()
    {
        return DepartmentCategory::all();
    }


    /**
     * Build the filtered department query
     * 
     * Applies filters based on:
     * 1. Branch context: Filters by current branch OR departments with no branch (global departments)
     * 2. Search: Quick search on department name
     * 3. Advanced search: Searches name and description fields
     * 4. Category filter: Limits to specific department category
     * 5. Date range: Filters by creation date
     * 
     * The branch_id OR null condition allows both branch-specific and global departments.
     * Global departments (branch_id = null) are visible in all branches.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getFilteredQuery()
    {
        return Department::query()
            // Branch scoping: show branch-specific records OR global (null) records
            ->where(function ($query) {
                $query->where('branch_id', $this->b_id)
                      ->orWhereNull('branch_id');
            })
            // Quick search: filter by department name
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            // Advanced search: search across name and description
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            // Category filter: limit to departments in selected category
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            // Date range filter: start date
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            // Date range filter: end date
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            // Stable ordering so new items stay visible across pagination
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Apply filters and reset pagination
     * 
     * Called when user changes filter values.
     * Resets pagination to page 1 so filters take immediate effect.
     * 
     * @return void
     */
    public function applyFilters()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters to default state
     * 
     * Clears all search, filter, and date range criteria.
     * Resets pagination to page 1.
     * 
     * @return void
     */
    public function resetFilters()
    {
        $this->search = null;
        $this->advancedSearch = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->filterCategory = null;
        $this->filterBranch = null;
        $this->resetPage();
    }

    /**
     * Export filtered departments to CSV format
     * 
     * Generates a CSV file with the following columns:
     * - ID: Department ID
     * - Name: Department name
     * - Branch: Associated branch name (N/A if global)
     * - Type: Department type
     * - Description: Department description
     * - Created At: Creation timestamp
     * 
     * The export respects all active filters and returns a downloadable file
     * with filename: departments-YYYY-MM-DD.csv
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportExcel()
    {
        // Get filtered departments based on current filters
        $departments = $this->getFilteredQuery()->get();

        // Build CSV header row
        $csv = "ID,Name,Branch,Type,Description,Created At\n";
        
        // Add each department as a CSV row
        foreach ($departments as $department) {
            // Get branch name or show N/A if department is global
            $branchName = $department->branch ? $department->branch->name : 'N/A';
            // Format row with proper CSV escaping for quoted values
            $csv .= "\"{$department->id}\",\"{$department->name}\",\"{$branchName}\",\"{$department->type}\",\"{$department->description}\",\"{$department->created_at}\"\n";
        }

        // Stream the CSV file to browser for download
        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'departments-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Initiate department deletion
     * 
     * Different flows for super admin vs regular employees:
     * 
     * Super Admin Flow:
     * - Shows confirmation dialog
     * - Immediate deletion on confirmation
     * - Creates completed audit log entry
     * 
     * Employee Flow:
     * - Shows modal asking for deletion reason
     * - Reason must be at least 5 characters
     * - Creates ApprovalAuditRequest for super admin review
     * - Creates pending audit log entry
     * 
     * @param int $departmentId The ID of department to delete
     * @return void
     */
    public function deleteDepartment($departmentId): void
    {
        $this->selectedDepartmentId = $departmentId;

        if (is_super_admin()) {
            // Super admin: show confirmation dialog for immediate deletion
            $this->dialog()
                ->question('Warning!', 'Are you sure you want to delete this department?')
                ->confirm('Confirm', 'confirmedDeleteDepartment', 'Confirmed Successfully')
                ->cancel('Cancel', 'cancelledDeleteDepartment', 'Cancelled Successfully')
                ->send();
        } else {
            // Employee: show reason modal for approval workflow
            $this->showDeleteReasonModal = true;
        }
    }

    /**
     * Process confirmed department deletion
     * 
     * Handles two distinct deletion flows:
     * 
     * 1. Super Admin Delete:
     *    - Immediately deletes the department from database
     *    - Creates completed audit log entry
     *    - Shows success confirmation dialog
     * 
     * 2. Employee Delete:
     *    - Validates deletion reason (minimum 5 characters)
     *    - Creates ApprovalAuditRequest for super admin approval
     *    - Creates pending audit log entry
     *    - Shows success message with "pending approval" status
     *    - Super admin must review and approve before deletion
     * 
     * Security Note:
     * - Verifies department's branch_id matches current b_id context
     * - Prevents cross-branch deletion attempts
     * 
     * @param string $message Dialog message (from confirmation)
     * @return void
     */
    public function confirmedDeleteDepartment(string $message): void
    {
        if ($this->selectedDepartmentId) {
            // Find the department or fail if not found
            $department = Department::findOrFail($this->selectedDepartmentId);
            
            // Security check: ensure department belongs to current branch context
            if($department->branch_id != $this->b_id){
                $this->dialog()->error('Danger', 'You are not in the place to delete this department!')->send();
                return;
            }

            // Get current authenticated user (employee or super admin)
            $user = auth()->user() ?? auth()->user();
            
            if (is_super_admin()) {
                // ===== SUPER ADMIN DELETION =====
                // Log the deletion action as completed
                AuditService::log($user, 'delete', $department, 'Department deleted by super admin', 'completed');
                // Immediately delete the department from database
                $department->delete();
                // Show success confirmation
                $this->dialog()->success('Success', 'Department deleted successfully!')->send();
            } else {
                // ===== EMPLOYEE DELETION (APPROVAL REQUIRED) =====
                DepartmentApprovalService::requestDelete($department, $this->deleteReason);
                $this->toast()->success('Delete request submitted for approval')->send();
            }
            
            // Reset delete operation state
            $this->selectedDepartmentId = null;
            $this->deleteReason = '';
            $this->showDeleteReasonModal = false;
        }
    }

    /**
     * Cancel department deletion
     * 
     * Called when user clicks "Cancel" on confirmation dialog or modal.
     * Resets all deletion-related state variables.
     * 
     * @param string $message Dialog message (from cancellation)
     * @return void
     */
    public function cancelledDeleteDepartment(string $message): void
    {
        // Reset deletion state
        $this->selectedDepartmentId = null;
        $this->deleteReason = '';
        $this->showDeleteReasonModal = false;
        $this->toast()->info('Cancelled')->send();
    }

    /**
     * Initiate bulk deletion of departments
     * 
     * Similar to single delete, but handles multiple selected departments.
     * 
     * Super Admin Flow:
     * - Shows confirmation dialog with count
     * - Immediate deletion on confirmation
     * 
     * Employee Flow:
     * - Shows reason modal for approval workflow
     * 
     * @return void
     */
    public function bulkDeleteDepartments(): void
    {
        if (is_super_admin()) {
            // Super admin: show confirmation with count
            $this->dialog()
                ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' department(s)?')
                ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
                ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
                ->send();
        } else {
            // Employee: show reason modal for approval workflow
            $this->showDeleteReasonModal = true;
        }
    }

    /**
     * Process confirmed bulk department deletion
     * 
     * Super Admin: 
     * - Logs each deletion with "bulk" annotation
     * - Performs single batch delete query for efficiency
     * - Shows success count
     * 
     * Employee:
     * - Validates reason (minimum 5 characters)
     * - Creates separate ApprovalAuditRequest for each department
     * - Only creates requests for departments in current branch
     * - Shows count of approval requests submitted
     * 
     * Branch Security:
     * - For employees, only processes departments matching current b_id
     * 
     * @param string $message Dialog message (from confirmation)
     * @return void
     */
    public function confirmedBulkDelete(string $message): void
    {
        // Get current authenticated user (employee or super admin)
        $user = auth()->user() ?? auth()->user();
        
        if (is_super_admin()) {
            // ===== SUPER ADMIN BULK DELETE =====
            // Log each department deletion as completed
            foreach ($this->selectedIds as $id) {
                $department = Department::find($id);
                if ($department) {
                    AuditService::log($user, 'delete', $department, 'Department deleted by super admin (bulk)', 'completed');
                }
            }
            
            // Single batch delete query for efficiency
            Department::whereIn('id', $this->selectedIds)->delete();
            $this->dialog()->success('Success', count($this->selectedIds) . ' department(s) deleted successfully!')->send();
        } else {
            // ===== EMPLOYEE BULK DELETE (APPROVAL REQUIRED) =====
            // Validate deletion reason length
            if (strlen($this->deleteReason) < 5) {
                $this->toast()->error('Reason must be at least 5 characters long')->send();
                return;
            }
            
            // Process each selected department
            foreach ($this->selectedIds as $id) {
                $department = Department::find($id);
                
                // Only process departments in current branch (security check)
                if ($department && $department->branch_id == $this->b_id) {
                    // Create approval request for this department
                    ApprovalAuditRequest::create([
                        'branch_id' => $this->b_id,
                        'requester_id' => $user->id,
                        'requester_type' => get_class($user),
                        'action' => 'delete:'.\App\Models\Department::class,
                        'description' => $this->deleteReason,
                        'payload' => $department->toArray(),
                        'status' => 'pending',
                    ]);
                    
                    // Log as pending audit entry
                    AuditService::log($user, 'delete', $department, $this->deleteReason, 'pending');
                }
            }
            
            $this->toast()->success(count($this->selectedIds) . ' delete request(s) submitted for approval')->send();
        }
        
        // Reset bulk delete operation state
        $this->selectedIds = [];
        $this->deleteReason = '';
        $this->showDeleteReasonModal = false;
    }

    /**
     * Cancel bulk deletion
     * 
     * Called when user clicks "Cancel" on confirmation dialog or modal.
     * Resets all bulk deletion state variables.
     * 
     * @param string $message Dialog message (from cancellation)
     * @return void
     */
    public function cancelledBulkDelete(string $message): void
    {
        // Reset bulk delete state
        $this->selectedIds = [];
        $this->deleteReason = '';
        $this->showDeleteReasonModal = false;
        $this->toast()->info('Cancelled')->send();
    }

    protected function exportSelected(): void
    {
        if (empty($this->selectedIds)) {
            session()->flash('info', 'No departments selected for export.');
            return;
        }

        $departments = Department::whereIn('id', $this->selectedIds)
            ->with(['category', 'branch'])
            ->get();

        $this->export(
            'departments_' . date('Y-m-d'),
            $departments,
            'exports.departments',
            'excel'
        );

        session()->flash('success', count($this->selectedIds) . ' departments exported successfully.');
        $this->resetBulkSelection();
    }

    /**
     * Render the department management view
     * 
     * Fetches filtered and paginated department data, then passes it to
     * the view along with table headers and branch context.
     * 
     * Data Structure Passed to View:
     * - headers: Array of column definitions for the table
     *   - Each header contains: index (field name), label (display text)
     *   - Actions column has display=true to always show
     * - rows: Paginated collection of departments matching all filters
     * - b_id: Current branch ID for maintaining context in the view
     * 
     * The quantity property controls pagination size (default 10 per page).
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Get filtered departments with pagination
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 5);

        // Return view with table structure and data
        return view('livewire.branch-dashboard.department-module.index', [
            // Table column headers definition
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Department Name'],
                ['index' => 'category', 'label' => 'Category'],
                ['index' => 'branch', 'label' => 'Branch'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'created_at', 'label' => 'Created At'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            // Paginated department data
            'rows' => $rows,
            // Current branch ID for maintaining context
            'b_id' => $this->b_id,
        ]);
    }
}

#
