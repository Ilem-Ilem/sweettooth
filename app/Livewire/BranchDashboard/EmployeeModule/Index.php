<?php

namespace App\Livewire\BranchDashboard\EmployeeModule;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use App\Models\ApprovalAuditRequest;
use App\Services\AuditService;
use App\Services\EmployeeApprovalService;
use App\Services\EmployeeAuditService;
use App\Traits\AuditableSyncTrait;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\{Layout, Url, On};

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use AuditableSyncTrait;
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    #[Url(keep: true)]
    public  $b_id;

    public function mount()
    {
        // Set b_id from current branch context (works for both employees and super admins)
        $this->b_id = current_branch_id();
    }

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
        $this->resetFilters();
    }

    // Filter fields
    #[Url()]
    public ?string $filterStatus = null;
    #[Url()]
    public ?string $filterDepartment = null;
    #[Url()]
    public ?string $filterBranch = null;
    #[Url()]
    public ?string $filterGender = null;
    #[Url()]
    public ?string $filterShift = null;
    #[Url()]
    public ?string $hireDateFrom = null;
    #[Url()]

    public ?string $hireDateTo = null;
    public ?string $terminationDateFrom = null;
    #[Url()]
    public ?string $terminationDateTo = null;
    #[Url()]

    // Delete state
    public ?string $selectedEmployeeId = null;

    // Role assignment state
    public bool $showRoleModal = false;
    public ?string $employeeIdForRole = null;
    public array $selectedRoles = [];
    
    // Role reason modal state
    public bool $showRoleReasonModal = false;
    public string $roleReason = '';
    public bool $savingRoles = false;

    // Delete reason modal state
    public bool $showDeleteReasonModal = false;
    public string $deleteReason = '';
    public bool $deletingEmployee = false;

    protected function getModelClass(): string
    {
        return Employee::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Employee::query()
        ->latest()
            ->where('branch_id', $this->b_id)
            ->with(['branch', 'department', 'roles'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('employee_number', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->advancedSearch . '%')
                        ->orWhere('employee_number', 'like', '%' . $this->advancedSearch . '%')
                        ->orWhere('position', 'like', '%' . $this->advancedSearch . '%')
                        ->orWhere('phone', 'like', '%' . $this->advancedSearch . '%')
                        ->orWhereHas('branch', function ($branchQuery) {
                            $branchQuery->where('name', 'like', '%' . $this->advancedSearch . '%');
                        })
                        ->orWhereHas('department', function ($deptQuery) {
                            $deptQuery->where('name', 'like', '%' . $this->advancedSearch . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDepartment, function ($query) {
                $query->where('department_id', $this->filterDepartment);
            })
            ->when($this->filterBranch, function ($query) {
                $query->where('branch_id', $this->filterBranch);
            })
            ->when($this->filterGender, function ($query) {
                $query->where('gender', $this->filterGender);
            })
            ->when($this->filterShift, function ($query) {
                $query->where('shift_preference', $this->filterShift);
            })
            ->when($this->hireDateFrom, function ($query) {
                $query->whereDate('hire_date', '>=', $this->hireDateFrom);
            })
            ->when($this->hireDateTo, function ($query) {
                $query->whereDate('hire_date', '<=', $this->hireDateTo);
            })
            ->when($this->terminationDateFrom, function ($query) {
                $query->whereDate('termination_date', '>=', $this->terminationDateFrom);
            })
            ->when($this->terminationDateTo, function ($query) {
                $query->whereDate('termination_date', '<=', $this->terminationDateTo);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->advancedSearch = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->filterStatus = null;
        $this->filterDepartment = null;
        $this->filterBranch = null;
        $this->filterGender = null;
        $this->filterShift = null;
        $this->hireDateFrom = null;
        $this->hireDateTo = null;
        $this->terminationDateFrom = null;
        $this->terminationDateTo = null;
        $this->resetPage();
    }

    // Export methods
    public function exportExcel()
    {
        $employees = $this->getFilteredQuery()->get();

        $csv = "Employee #,Name,Email,Position,Department,Branch,Status,Hire Date,Salary\n";
        foreach ($employees as $employee) {
            $branchName = $employee->branch ? $employee->branch->name : 'N/A';
            $deptName = $employee->department ? $employee->department->name : 'N/A';
            $csv .= "\"{$employee->employee_number}\",\"{$employee->name}\",\"{$employee->email}\",\"{$employee->position}\",\"{$deptName}\",\"{$branchName}\",\"{$employee->status}\",\"{$employee->hire_date}\",\"{$employee->salary}\"\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'employees-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    // Delete methods
    public function deleteEmployee($employeeId): void
    {
        $this->selectedEmployeeId = $employeeId;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this employee?')
            ->confirm('Confirm', 'initiateDeleteEmployee', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDeleteEmployee', 'Cancelled Successfully')
            ->send();
    }

    public function initiateDeleteEmployee(string $message): void
    {
        if (!is_super_admin()) {
            $this->showDeleteReasonModal = true;
        } else {
            $this->confirmedDeleteEmployee($message);
        }
    }

    public function closeDeleteReasonModal(): void
    {
        $this->showDeleteReasonModal = false;
        $this->deleteReason = '';
        $this->selectedEmployeeId = null;
    }

    public function proceedWithDeleteReason(): void
    {
        if (strlen($this->deleteReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }
        
        $this->showDeleteReasonModal = false;
        $this->confirmedDeleteEmployee('Confirmed Successfully');
    }

    public function confirmedDeleteEmployee(string $message): void
    {
        if ($this->deletingEmployee) return;
        
        $this->deletingEmployee = true;

        try {
            if ($this->selectedEmployeeId) {
                $employee = Employee::findOrFail($this->selectedEmployeeId);
                $user = current_actor();

                if (!is_super_admin()) {
                    // EMPLOYEE: Create approval request using EmployeeApprovalService
                    EmployeeApprovalService::requestDelete(
                        $employee,
                        $this->deleteReason
                    );

                    $this->toast()->success('Employee deletion request submitted for approval!')->send();
                    $this->selectedEmployeeId = null;
                    return;
                }

                // SUPER ADMIN: Delete immediately
                EmployeeAuditService::logEmployeeTermination($employee, $user, 'Employee deleted by admin');
                $employee->delete();
                $this->dialog()->success('Success', 'Employee deleted successfully!')->send();
                $this->selectedEmployeeId = null;
            }
        } finally {
            $this->deletingEmployee = false;
        }
    }

    public function cancelledDeleteEmployee(string $message): void
    {
        $this->selectedEmployeeId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Bulk Delete
    public function bulkDeleteEmployees(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' employee(s)?')
            ->confirm('Confirm', 'initiateBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function initiateBulkDelete(string $message): void
    {
        if (!is_super_admin()) {
            $this->showDeleteReasonModal = true;
        } else {
            $this->confirmedBulkDelete($message);
        }
    }

    public function confirmedBulkDelete(string $message): void
    {
        if ($this->deletingEmployee) return;
        
        $this->deletingEmployee = true;

        try {
            $user = current_actor();
            $employees = Employee::whereIn('id', $this->selectedIds)->get();

            if (!is_super_admin()) {
                // EMPLOYEE: Create approval requests for each employee
                foreach ($employees as $employee) {
                    EmployeeApprovalService::requestDelete(
                        $employee,
                        $this->deleteReason
                    );
                }

                $this->toast()->success(count($this->selectedIds) . ' employee deletion request(s) submitted for approval!')->send();
                $this->selectedIds = [];
                $this->deleteReason = '';
                return;
            }

            // SUPER ADMIN: Delete immediately
            foreach ($employees as $employee) {
                EmployeeAuditService::logEmployeeTermination($employee, $user, 'Deleted in bulk by admin');
                $employee->delete();
            }

            $this->dialog()->success('Success', count($this->selectedIds) . ' employee(s) deleted successfully!')->send();
            $this->selectedIds = [];
        } finally {
            $this->deletingEmployee = false;
        }
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Role assignment methods
    public function openRoleModal($employeeId): void
    {
        $this->employeeIdForRole = $employeeId;
        $employee = Employee::find($employeeId);
        $this->selectedRoles = $employee ? $employee->roles->pluck('id')->toArray() : [];
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->employeeIdForRole = null;
        $this->selectedRoles = [];
    }

    public function initiateRoleSave(): void
    {
        if (!is_super_admin()) {
            $this->showRoleReasonModal = true;
        } else {
            $this->saveRoles();
        }
    }

    public function closeRoleReasonModal(): void
    {
        $this->showRoleReasonModal = false;
        $this->roleReason = '';
    }

    public function proceedWithRoleReason(): void
    {
        if (strlen($this->roleReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }
        
        $this->showRoleReasonModal = false;
        $this->saveRoles();
    }

    public function saveRoles(): void
    {
        if ($this->savingRoles) return;
        
        $this->savingRoles = true;

        try {
            if (!$this->employeeIdForRole) return;

            $employee = Employee::findOrFail($this->employeeIdForRole);
            $user = current_actor();

            if (!is_super_admin()) {
                // EMPLOYEE: Create approval request using EmployeeApprovalService
                EmployeeApprovalService::requestRoleSync(
                    $employee,
                    $this->selectedRoles,
                    $this->roleReason
                );

                $this->toast()->success('Role update request submitted for approval!')->send();
                $this->closeRoleModal();
                $this->roleReason = '';
                return;
            }

            // SUPER ADMIN: Update immediately
            $oldRoles = $employee->roles->pluck('name')->toArray();
            $this->syncWithAudit(
                $employee,
                'roles',
                $this->selectedRoles,
                "Updated roles for {$employee->name}"
            );

            // Log role change
            if ($oldRoles !== $this->selectedRoles) {
                EmployeeAuditService::logRoleChange(
                    $employee,
                    $oldRoles,
                    $this->selectedRoles,
                    "Roles updated by super admin",
                    $user
                );
            }

            $this->toast()->success('Roles updated successfully!')->send();
            $this->closeRoleModal();
            $this->roleReason = '';

        } finally {
            $this->savingRoles = false;
        }
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::all();
        $statuses = ['active', 'inactive', 'terminated', 'on_probation', 'on_leave'];
        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];
        $shifts = ['morning', 'afternoon', 'night', 'rotating', 'flexible'];
        $roles = Role::where('guard_name', 'employees')->where('name', '!=', 'MD')->get();

        return view('livewire.branch-dashboard.employee-module.index', [
            'headers' => [
                ['index' => 'employee_number', 'label' => 'Employee #'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'email', 'label' => 'Email'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'hire_date', 'label' => 'Hire Date'],
                ['index' => 'salary', 'label' => 'Salary'],
                ['index' => 'roles', 'label' => 'Roles'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'departments' => $departments,
            'statuses' => $statuses,
            'genders' => $genders,
            'shifts' => $shifts,
            'roles' => $roles,
        ]);
    }
}

#'livewire.branch-dashbord.employee-module.index'
