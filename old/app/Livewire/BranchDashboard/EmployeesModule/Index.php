<?php
namespace App\Livewire\BranchDashboard\EmployeesModule;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.branch-dashboard')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search           = '';
    public $filterDepartment = '';
    public $filterStatus     = '';

    #[Url(as:'b_id', keep:true)]
    public $b_id;

    // Edit functionality
    public $editingEmployee = null;
    public $editEmployeeNumber, $editName, $editEmail, $editPhone;
    public $editAddress, $editDateOfBirth, $editGender, $editNationality;
    public $editEmergencyContactName, $editEmergencyContactPhone;
    public $editPosition, $editHireDate, $editTerminationDate;
    public $editStatus, $editProbationEndDate, $editShiftPreference;
    public $editSalary, $editHourlyRate, $editTaxId, $editBankAccount;
    public $editAllergies, $editProfilePhoto;
    public $editLastPerformanceReviewDate, $editPerformanceRating;
    public $editb_id, $editDepartmentId;
    public $newProfilePhoto;

    protected $paginationTheme = 'tailwind';
    protected $queryString     = ['search', 'filterDepartment', 'filterStatus', 'b_id'];

    protected $listeners = ['refresh' => '$refresh', 'employeeCreated' => 'handleEmployeeCreated'];

    public function updatingSearch()
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
        $query = Employee::with(['branch', 'department'])->where('branch_id', $this->b_id);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('employee_number', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('position', 'like', "%{$this->search}%");
            });
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
        $employee = Employee::where('id', $employeeId)->where('branch_id', $this->b_id)->firstOrFail();

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
        $this->editb_id                  = $employee->branch_id;
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
            'editb_id'                  => 'nullable|exists:branches,id',
            'editDepartmentId'              => 'nullable|exists:departments,id',
            'newProfilePhoto'               => 'nullable|image|max:2048',
        ]);

        $employee = Employee::where('id', $this->editingEmployee)->where('branch_id', $this->b_id)->firstOrFail();

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
            'branch_id'                    => $this->editb_id,
            'department_id'                => $this->editDepartmentId,
        ];

        if ($this->newProfilePhoto) {
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
            $employee = Employee::where('id', $employeeId)->where('branch_id', $this->b_id)->firstOrFail();
            $employeeName = $employee->name;

            // Use soft delete instead of hard delete
            $employee->delete();
            $this->dispatch('employee-deleted', "Employee '{$employeeName}' has been moved to trash.");
        } catch (\Exception $e) {
            $this->dispatch('error', 'Unable to delete employee. ' . $e->getMessage());
        }
    }

    public function toggleStatus($employeeId)
    {
        $employee = Employee::where('id', $employeeId)->where('branch_id', $this->b_id)->firstOrFail();
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

        // Verify all selected employees belong to this branch
        $selectedEmployees = Employee::whereIn('id', $selectedIds)
                                    ->where('branch_id', $this->b_id)
                                    ->get();

        if ($selectedEmployees->count() !== count($selectedIds)) {
            $this->dispatch('error', 'Some selected employees do not belong to this branch.');
            return;
        }

        // Remove authenticated user from bulk actions
        $authId = auth()->id();
        $filteredIds = array_filter($selectedIds, fn($id) => $id != $authId);

        if (empty($filteredIds)) {
            $this->dispatch('error', 'You cannot perform bulk actions on yourself.');
            return;
        }

        switch ($action) {
            case 'delete':
                Employee::whereIn('id', $filteredIds)->where('branch_id', $this->b_id)->delete();
                $this->dispatch('bulk-action-completed', count($filteredIds) . ' employees moved to trash.');
                break;
            case 'activate':
                Employee::whereIn('id', $filteredIds)->where('branch_id', $this->b_id)->update(['status' => 'active']);
                $this->dispatch('bulk-action-completed', count($filteredIds) . ' employees activated.');
                break;
            case 'deactivate':
                Employee::whereIn('id', $filteredIds)->where('branch_id', $this->b_id)->update(['status' => 'inactive']);
                $this->dispatch('bulk-action-completed', count($filteredIds) . ' employees deactivated.');
                break;
        }
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
        $this->editb_id                  = null;
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
        return view('livewire.branch-dashboard.employees-module.index', [
            'employees'   => $this->employeesQuery()->paginate(10),
            'branches'    => Branch::where('is_active', true)->get(),
            'departments' => Department::all(),
        ]);
    }
}
