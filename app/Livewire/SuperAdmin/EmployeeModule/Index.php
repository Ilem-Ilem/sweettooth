<?php

namespace App\Livewire\SuperAdmin\EmployeeModule;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\EmployeesExport;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Filter properties
    public $search = '';
    public $hireDateFrom;
    public $hireDateTo;
    public $status = 'all';
    public $department = 'all';
    public $gender = 'all';
    public $branch = 'all';
    public $shiftPreference = 'all';
    public $terminationDateFrom;
    public $terminationDateTo;
    public $perPage = 10;
    public $quantity = 10; // For table component

    // Form properties for adding/editing employee
    public $employeeId;
    public $branchId;
    public $departmentId;
    public $employeeNumber;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $dateOfBirth;
    public $genderForm;
    public $nationality;
    public $emergencyContactName;
    public $emergencyContactPhone;
    public $position;
    public $hireDate;
    public $terminationDate;
    public $statusForm = 'active';
    public $probationEndDate;
    public $shiftPreferenceForm;
    public $salary;
    public $hourlyRate;
    public $taxId;
    public $bankAccount;
    public $allergies;
    public $profilePhoto;
    public $lastPerformanceReviewDate;
    public $performanceRating;

    // Modal controls
    public $showAddModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $selectedEmployee;

    // Selected employees for bulk delete
    public $selectedIds = [];

    public function mount()
    {
        // Initialize if needed
    }

    public function updatedQuantity()
    {
        $this->perPage = $this->quantity;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->hireDateFrom = null;
        $this->hireDateTo = null;
        $this->status = 'all';
        $this->department = 'all';
        $this->gender = 'all';
        $this->branch = 'all';
        $this->shiftPreference = 'all';
        $this->terminationDateFrom = null;
        $this->terminationDateTo = null;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function openEditModal($id)
    {
        $employee = Employee::findOrFail($id);
        $this->employeeId = $employee->id;
        $this->branchId = $employee->branch_id;
        $this->departmentId = $employee->department_id;
        $this->employeeNumber = $employee->employee_number;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->address = $employee->address;
        $this->dateOfBirth = $employee->date_of_birth;
        $this->genderForm = $employee->gender;
        $this->nationality = $employee->nationality;
        $this->emergencyContactName = $employee->emergency_contact_name;
        $this->emergencyContactPhone = $employee->emergency_contact_phone;
        $this->position = $employee->position;
        $this->hireDate = $employee->hire_date;
        $this->terminationDate = $employee->termination_date;
        $this->statusForm = $employee->status;
        $this->probationEndDate = $employee->probation_end_date;
        $this->shiftPreferenceForm = $employee->shift_preference;
        $this->salary = $employee->salary;
        $this->hourlyRate = $employee->hourly_rate;
        $this->taxId = $employee->tax_id;
        $this->bankAccount = $employee->bank_account;
        $this->allergies = $employee->allergies;
        // Profile photo handled separately
        $this->lastPerformanceReviewDate = $employee->last_performance_review_date;
        $this->performanceRating = $employee->performance_rating;

        $this->showEditModal = true;
    }

    public function openViewModal($id)
    {
        $this->selectedEmployee = Employee::with(['department', 'branch'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeModals()
    {
        $this->showAddModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
    }

    public function resetForm()
    {
        $this->employeeId = null;
        $this->branchId = null;
        $this->departmentId = null;
        $this->employeeNumber = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->dateOfBirth = null;
        $this->genderForm = null;
        $this->nationality = '';
        $this->emergencyContactName = '';
        $this->emergencyContactPhone = '';
        $this->position = '';
        $this->hireDate = null;
        $this->terminationDate = null;
        $this->statusForm = 'active';
        $this->probationEndDate = null;
        $this->shiftPreferenceForm = null;
        $this->salary = null;
        $this->hourlyRate = null;
        $this->taxId = '';
        $this->bankAccount = '';
        $this->allergies = '';
        $this->profilePhoto = null;
        $this->lastPerformanceReviewDate = null;
        $this->performanceRating = null;
    }

    public function saveEmployee()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeId,
            'position' => 'required|string|max:255',
            'hireDate' => 'required|date',
            'salary' => 'nullable|numeric',
            // Add other validations as per schema
        ]);

        $data = [
            'branch_id' => $this->branchId,
            'department_id' => $this->departmentId,
            'employee_number' => $this->employeeNumber ?: $this->generateEmployeeNumber(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->dateOfBirth,
            'gender' => $this->genderForm,
            'nationality' => $this->nationality,
            'emergency_contact_name' => $this->emergencyContactName,
            'emergency_contact_phone' => $this->emergencyContactPhone,
            'position' => $this->position,
            'hire_date' => $this->hireDate,
            'termination_date' => $this->terminationDate,
            'status' => $this->statusForm,
            'probation_end_date' => $this->probationEndDate,
            'shift_preference' => $this->shiftPreferenceForm,
            'salary' => $this->salary,
            'hourly_rate' => $this->hourlyRate,
            'tax_id' => $this->taxId,
            'bank_account' => $this->bankAccount,
            'allergies' => $this->allergies,
            'last_performance_review_date' => $this->lastPerformanceReviewDate,
            'performance_rating' => $this->performanceRating,
        ];

        if ($this->profilePhoto) {
            $data['profile_photo'] = $this->profilePhoto->store('photos', 'public');
        }

        if ($this->employeeId) {
            Employee::find($this->employeeId)->update($data);
        } else {
            Employee::create($data);
        }

        $this->closeModals();
        $this->resetForm();
    }

    public function deleteEmployee($id)
    {
        Employee::find($id)->delete();
    }

    public function deleteSelected()
    {
        Employee::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
    }

    public function exportExcel()
    {
        // return Excel::download(new EmployeesExport($this->getFilteredEmployeesQuery()), 'employees.xlsx');
    }

    public function exportPdf()
    {
        // $employees = $this->getFilteredEmployeesQuery()->get();
        // $pdf = Pdf::loadView('exports.employees-pdf', compact('employees'));
        // return response()->streamDownload(function () use ($pdf) {
        //     echo $pdf->output();
        // }, 'employees.pdf');
    }

    private function generateEmployeeNumber()
    {
        // Logic to generate unique employee number, e.g., EMP-XXX
        $last = Employee::max('employee_number');
        $num = $last ? intval(substr($last, 4)) + 1 : 1;
        return 'EMP-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    private function getFilteredEmployeesQuery()
    {
        $query = Employee::with(['department', 'branch']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->hireDateFrom) {
            $query->where('hire_date', '>=', $this->hireDateFrom);
        }

        if ($this->hireDateTo) {
            $query->where('hire_date', '<=', $this->hireDateTo);
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->department !== 'all') {
            $query->where('department_id', $this->department);
        }

        if ($this->gender !== 'all') {
            $query->where('gender', $this->gender);
        }

        if ($this->branch !== 'all') {
            $query->where('branch_id', $this->branch);
        }

        if ($this->shiftPreference !== 'all') {
            $query->where('shift_preference', $this->shiftPreference);
        }

        if ($this->terminationDateFrom) {
            $query->where('termination_date', '>=', $this->terminationDateFrom);
        }

        if ($this->terminationDateTo) {
            $query->where('termination_date', '<=', $this->terminationDateTo);
        }

        return $query;
    }

    public function render()
    {
        $rows = $this->getFilteredEmployeesQuery()->paginate($this->perPage);

        $headers = [
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
        ];

        $departments = Department::all();
        $branches = Branch::all();
        $statuses = ['active', 'inactive', 'terminated', 'on_probation', 'on_leave'];
        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];
        $shifts = ['morning', 'afternoon', 'night', 'rotating', 'flexible'];

        return view('livewire.super-admin.employee-module.index', compact(
            'headers',
            'rows',
            'departments',
            'branches',
            'statuses',
            'genders',
            'shifts'
        ));
    }
}
