<?php
namespace App\Livewire\SuperAdmin\Employee;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Delete extends Component
{
    use WithPagination, WithFileUploads;

    public $search           = '';
    public $filterBranch     = '';
    public $filterDepartment = '';
    public $filterStatus     = '';

    public array $selected = [];

    // Edit functionality (not used on deleted view but kept for consistency)
    public $editingEmployee = null;
    public $editEmployeeNumber, $editName, $editEmail, $editPhone;
    public $editAddress, $editDateOfBirth, $editGender, $editNationality;
    public $editEmergencyContactName, $editEmergencyContactPhone;
    public $editPosition, $editHireDate, $editTerminationDate;
    public $editStatus, $editProbationEndDate, $editShiftPreference;
    public $editSalary, $editHourlyRate, $editTaxId, $editBankAccount;
    public $editAllergies, $editProfilePhoto;
    public $editLastPerformanceReviewDate, $editPerformanceRating;
    public $editBranchId, $editDepartmentId;
    public $newProfilePhoto;

    protected $paginationTheme = 'tailwind';
    protected $queryString     = ['search', 'filterBranch', 'filterDepartment', 'filterStatus'];

    protected $listeners = ['refresh' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBranch()
    {
        $this->resetPage();
    }

    public function updatingFilterDepartment()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    private function employeesQuery()
    {
        $query = Employee::onlyTrashed()->with(['branch', 'department']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('employee_number', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('position', 'like', "%{$this->search}%");
            });
        }

        if (! empty($this->filterBranch)) {
            $query->where('branch_id', $this->filterBranch);
        }

        if (! empty($this->filterDepartment)) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->filterStatus !== null && $this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->latest();
    }

    public function edit($employeeId)
    {
        $employee                            = Employee::findOrFail($employeeId);
        $this->editingEmployee               = $employeeId;
        $this->editEmployeeNumber            = $employee->employee_number;
        $this->editName                      = $employee->name;
        $this->editEmail                     = $employee->email;
        $this->editPhone                     = $employee->phone;
        $this->editAddress                   = $employee->address;
        $this->editDateOfBirth               = $employee->date_of_birth;
        $this->editGender                    = $employee->gender;
        $this->editNationality               = $employee->nationality;
        $this->editEmergencyContactName      = $employee->emergency_contact_name;
        $this->editEmergencyContactPhone     = $employee->emergency_contact_phone;
        $this->editPosition                  = $employee->position;
        $this->editHireDate                  = $employee->hire_date;
        $this->editTerminationDate           = $employee->termination_date;
        $this->editStatus                    = $employee->status;
        $this->editProbationEndDate          = $employee->probation_end_date;
        $this->editShiftPreference           = $employee->shift_preference;
        $this->editSalary                    = $employee->salary;
        $this->editHourlyRate                = $employee->hourly_rate;
        $this->editTaxId                     = $employee->tax_id;
        $this->editBankAccount               = $employee->bank_account;
        $this->editAllergies                 = $employee->allergies;
        $this->editProfilePhoto              = $employee->profile_photo;
        $this->editLastPerformanceReviewDate = $employee->last_performance_review_date;
        $this->editPerformanceRating         = $employee->performance_rating;
        $this->editBranchId                  = $employee->branch_id;
        $this->editDepartmentId              = $employee->department_id;

        $this->dispatch('open-edit-modal');
    }

    public function updateEmployee()
    {
        $this->validate([
            'editEmployeeNumber'            => 'required|string|max:50|unique:employees,employee_number,' . $this->editingEmployee,
            'editName'                      => 'required|string|max:255',
            'editEmail'                     => 'required|email|max:255|unique:employees,email,' . $this->editingEmployee,
            'editPhone'                     => 'nullable|string|max:50',
            'editAddress'                   => 'nullable|string',
            'editDateOfBirth'               => 'nullable|date',
            'editGender'                    => 'nullable|in:male,female,other,prefer_not_to_say',
            'editNationality'               => 'nullable|string|max:100',
            'editEmergencyContactName'      => 'nullable|string|max:255',
            'editEmergencyContactPhone'     => 'nullable|string|max:50',
            'editPosition'                  => 'required|string|max:255',
            'editHireDate'                  => 'required|date',
            'editTerminationDate'           => 'nullable|date|after:editHireDate',
            'editStatus'                    => 'required|in:active,inactive,terminated,on_probation,on_leave',
            'editProbationEndDate'          => 'nullable|date',
            'editShiftPreference'           => 'nullable|in:morning,afternoon,night,rotating,flexible',
            'editSalary'                    => 'nullable|numeric|min:0',
            'editHourlyRate'                => 'nullable|numeric|min:0',
            'editTaxId'                     => 'nullable|string|max:50',
            'editBankAccount'               => 'nullable|string|max:100',
            'editAllergies'                 => 'nullable|string',
            'editLastPerformanceReviewDate' => 'nullable|date',
            'editPerformanceRating'         => 'nullable|numeric|min:0|max:5',
            'editBranchId'                  => 'nullable|exists:branches,id',
            'editDepartmentId'              => 'nullable|exists:departments,id',
            'newProfilePhoto'               => 'nullable|image|max:2048',
        ]);

        $employee = Employee::findOrFail($this->editingEmployee);

        $data = [
            'employee_number'              => $this->editEmployeeNumber,
            'name'                         => $this->editName,
            'email'                        => $this->editEmail,
            'phone'                        => $this->editPhone,
            'address'                      => $this->editAddress,
            'date_of_birth'                => $this->editDateOfBirth,
            'gender'                       => $this->editGender,
            'nationality'                  => $this->editNationality,
            'emergency_contact_name'       => $this->editEmergencyContactName,
            'emergency_contact_phone'      => $this->editEmergencyContactPhone,
            'position'                     => $this->editPosition,
            'hire_date'                    => $this->editHireDate,
            'termination_date'             => $this->editTerminationDate,
            'status'                       => $this->editStatus,
            'probation_end_date'           => $this->editProbationEndDate,
            'shift_preference'             => $this->editShiftPreference,
            'salary'                       => $this->editSalary,
            'hourly_rate'                  => $this->editHourlyRate,
            'tax_id'                       => $this->editTaxId,
            'bank_account'                 => $this->editBankAccount,
            'allergies'                    => $this->editAllergies,
            'last_performance_review_date' => $this->editLastPerformanceReviewDate,
            'performance_rating'           => $this->editPerformanceRating,
            'branch_id'                    => $this->editBranchId,
            'department_id'                => $this->editDepartmentId,
        ];

        // Handle profile photo upload
        if ($this->newProfilePhoto) {
            // Delete old photo if exists
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $data['profile_photo'] = $this->newProfilePhoto->store('employees', 'public');
        }

        $employee->update($data);

        $this->resetEditFields();
        $this->dispatch('close-edit-modal');
        $this->dispatch('employee-updated', 'Employee updated successfully!');
    }

    public function delete($employeeId)
    {
        try {
            $employee     = Employee::withTrashed()->findOrFail($employeeId);
            $employeeName = $employee->name;

            // Delete profile photo if exists
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            $employee->forceDelete();
            $this->dispatch('employee-deleted', "Employee '{$employeeName}' permanently deleted.");
            $this->reset('selected');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Unable to permanently delete employee.');
        }
    }

    public function restore($employeeId)
    {
        $employee = Employee::onlyTrashed()->findOrFail($employeeId);
        $employee->restore();
        $this->dispatch('bulk-action-completed', "Employee '{$employee->name}' restored.");
        $this->reset('selected');
    }

    public function toggleStatus($employeeId)
    {
        $employee  = Employee::findOrFail($employeeId);
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);
        $this->dispatch('status-updated', "Employee status updated to {$newStatus}.");
    }

    public function applyBulkAction($action, $selectedIds)
    {
        if (empty($selectedIds)) {
            $this->dispatch('error', 'Please select at least one employee.');
            return;
        }

        switch ($action) {
            case 'restore':
                Employee::onlyTrashed()->whereIn('id', $selectedIds)->restore();
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' employees restored.');
                break;
            case 'delete':
                $employees = Employee::withTrashed()->whereIn('id', $selectedIds)->get();
                foreach ($employees as $employee) {
                    if ($employee->profile_photo) {
                        Storage::disk('public')->delete($employee->profile_photo);
                    }
                }
                Employee::withTrashed()->whereIn('id', $selectedIds)->forceDelete();
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' employees permanently deleted.');
                break;
            default:
                $this->dispatch('error', 'Select a valid bulk action.');
                return;
        }

        $this->reset('selected');
    }

    public function export($format)
    {
        $employees = $this->employeesQuery()->get();

        switch ($format) {
            case 'csv':
                return $this->exportCsv($employees);
            case 'excel':
                return $this->exportExcel($employees);
            case 'pdf':
                return $this->exportPdf($employees);
        }
    }

    private function exportCsv($employees)
    {
        $filename = 'employees_' . date('Y-m-d') . '.csv';
        $handle   = fopen('php://temp', 'w');

        // Headers
        fputcsv($handle, [
            'Employee Number', 'Name', 'Email', 'Phone', 'Position',
            'Branch', 'Department', 'Hire Date', 'Status', 'Salary',
        ]);

        foreach ($employees as $employee) {
            fputcsv($handle, [
                $employee->employee_number,
                $employee->name,
                $employee->email,
                $employee->phone,
                $employee->position,
                $employee->branch->name ?? 'N/A',
                $employee->department->name ?? 'N/A',
                $employee->hire_date,
                ucfirst($employee->status),
                $employee->salary ?? 'N/A',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportExcel($employees)
    {
        return $this->exportCsv($employees);
    }

    private function exportPdf($employees)
    {
        return $this->exportCsv($employees);
    }

    private function resetEditFields()
    {
        $this->editingEmployee               = null;
        $this->editEmployeeNumber            = null;
        $this->editName                      = null;
        $this->editEmail                     = null;
        $this->editPhone                     = null;
        $this->editAddress                   = null;
        $this->editDateOfBirth               = null;
        $this->editGender                    = null;
        $this->editNationality               = null;
        $this->editEmergencyContactName      = null;
        $this->editEmergencyContactPhone     = null;
        $this->editPosition                  = null;
        $this->editHireDate                  = null;
        $this->editTerminationDate           = null;
        $this->editStatus                    = null;
        $this->editProbationEndDate          = null;
        $this->editShiftPreference           = null;
        $this->editSalary                    = null;
        $this->editHourlyRate                = null;
        $this->editTaxId                     = null;
        $this->editBankAccount               = null;
        $this->editAllergies                 = null;
        $this->editProfilePhoto              = null;
        $this->editLastPerformanceReviewDate = null;
        $this->editPerformanceRating         = null;
        $this->editBranchId                  = null;
        $this->editDepartmentId              = null;
        $this->newProfilePhoto               = null;
    }

    public function cancelEdit()
    {
        $this->resetEditFields();
        $this->dispatch('close-edit-modal');
    }

    public function handleEmployeeCreated()
    {
        $this->dispatch('employee-created', 'Employee created successfully!');
    }

    public function render()
    {
        return view('livewire.super-admin.employee.delete', [
            'employees'   => $this->employeesQuery()->paginate(10),
            'branches'    => Branch::where('is_active', true)->get(),
            'departments' => Department::all(),
        ]);
    }
}
