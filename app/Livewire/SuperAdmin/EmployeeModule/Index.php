<?php

namespace App\Livewire\SuperAdmin\EmployeeModule;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;

class Index extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Filter fields
    public ?string $filterStatus = null;
    public ?string $filterDepartment = null;
    public ?string $filterBranch = null;
    public ?string $filterGender = null;
    public ?string $filterShift = null;
    public ?string $hireDateFrom = null;
    public ?string $hireDateTo = null;
    public ?string $terminationDateFrom = null;
    public ?string $terminationDateTo = null;

    // Delete state
    public ?string $selectedEmployeeId = null;

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
            ->with(['branch', 'department'])
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

        return response()->streamDownload(function() use ($csv) {
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
            ->confirm('Confirm', 'confirmedDeleteEmployee', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDeleteEmployee', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDeleteEmployee(string $message): void
    {
        if ($this->selectedEmployeeId) {
            Employee::findOrFail($this->selectedEmployeeId)->delete();
            $this->dialog()->success('Success', 'Employee deleted successfully!')->send();
            $this->selectedEmployeeId = null;
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
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Employee::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' employee(s) deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::all();
        $statuses = ['active', 'inactive', 'terminated', 'on_probation', 'on_leave'];
        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];
        $shifts = ['morning', 'afternoon', 'night', 'rotating', 'flexible'];

        return view('livewire.super-admin.employee-module.index', [
            'headers' => [
                ['index' => 'employee_number', 'label' => 'Employee #'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'email', 'label' => 'Email'],
                ['index' => 'position', 'label' => 'Position'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'branch', 'label' => 'Branch'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'hire_date', 'label' => 'Hire Date'],
                ['index' => 'salary', 'label' => 'Salary'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'branches' => $branches,
            'departments' => $departments,
            'statuses' => $statuses,
            'genders' => $genders,
            'shifts' => $shifts,
        ]);
    }
}
