<?php

namespace App\Livewire\BranchDashboard\DepartmentModule;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.branch-dashboard')]
class Index extends Component
{
    use WithPagination;

    #[Url(as:'b_id', keep:true)]
    public $b_id;

    // Search and Filters
    public $search = '';
    public $statusFilters = [];
    public $employeesMin = '';
    public $employeesMax = '';
    public $dateFrom = '';
    public $dateTo = '';

    // UI State
    public $showAddDepartment = false;
    public $openDropdown = null;

    // Multi-select
    public $selectedDepartments = [];
    public $selectAll = false;

    // Bulk Action
    public $bulkAction = '';

    // Form Fields
    public $name = '';
    public $description = '';
    public $headId = '';
    public $status = 'active';
    public $editingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilters' => ['except' => []],
        'b_id',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'headId' => 'nullable|exists:employees,id',
        'status' => 'required|in:active,inactive,pending',
    ];

    public function mount()
    {
        // Ensure b_id is set for branch dashboard
        if (!$this->b_id) {
            abort(403, 'Branch ID is required');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedDepartments = $this->getFilteredDepartments()->pluck('id')->toArray();
        } else {
            $this->selectedDepartments = [];
        }
    }

    public function updatedSelectedDepartments()
    {
        $this->selectAll = count($this->selectedDepartments) === $this->getFilteredDepartments()->count();
    }

    public function toggleDropdown($dropdown)
    {
        $this->openDropdown = $this->openDropdown === $dropdown ? null : $dropdown;
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->openDropdown = null;
    }

    public function clearFilter($type)
    {
        switch ($type) {
            case 'status':
                $this->statusFilters = [];
                break;
            case 'employees':
                $this->employeesMin = '';
                $this->employeesMax = '';
                break;
            case 'date':
                $this->dateFrom = '';
                $this->dateTo = '';
                break;
        }
        $this->applyFilters();
    }

    public function removeStatusFilter($status)
    {
        $this->statusFilters = array_diff($this->statusFilters, [$status]);
        $this->applyFilters();
    }

    public function clearAllFilters()
    {
        $this->reset(['statusFilters', 'employeesMin', 'employeesMax', 'dateFrom', 'dateTo']);
        $this->applyFilters();
    }

    public function hasActiveFilters()
    {
        return !empty($this->statusFilters) ||
               $this->employeesMin ||
               $this->employeesMax ||
               $this->dateFrom ||
               $this->dateTo;
    }

    public function openAddDepartment()
    {
        $this->resetForm();
        $this->showAddDepartment = true;
    }

    public function closeAddDepartment()
    {
        $this->showAddDepartment = false;
        $this->resetForm();
    }

    public function saveDepartment()
    {
        $this->validate();

        if ($this->editingId) {
            $department = Department::where('id', $this->editingId)
                                    ->where('branch_id', $this->b_id)
                                    ->firstOrFail();
            $department->update([
                'name' => $this->name,
                'description' => $this->description,
                'head_id' => $this->headId,
                'branch_id' => $this->b_id,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Department updated successfully.');
        } else {
            Department::create([
                'name' => $this->name,
                'description' => $this->description,
                'head_id' => $this->headId,
                'branch_id' => $this->b_id,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Department created successfully.');
        }

        $this->closeAddDepartment();
    }

    public function editDepartment($id)
    {
        $department = Department::where('id', $id)
                                ->where('branch_id', $this->b_id)
                                ->firstOrFail();

        $this->editingId = $id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->headId = $department->head_id;
        $this->status = $department->status;

        $this->showAddDepartment = true;
    }

    public function deleteDepartment($id)
    {
        $department = Department::where('id', $id)
                                ->where('branch_id', $this->b_id)
                                ->firstOrFail();
        $department->delete();
        session()->flash('message', 'Department deleted successfully.');
        $this->selectedDepartments = array_diff($this->selectedDepartments, [$id]);
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedDepartments) || !$this->bulkAction) {
            return;
        }

        // Verify all selected departments belong to this branch
        $verifiedDepartments = Department::whereIn('id', $this->selectedDepartments)
                                        ->where('branch_id', $this->b_id)
                                        ->pluck('id')
                                        ->toArray();

        if (count($verifiedDepartments) !== count($this->selectedDepartments)) {
            session()->flash('error', 'Some selected departments do not belong to this branch.');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                Department::whereIn('id', $verifiedDepartments)
                         ->where('branch_id', $this->b_id)
                         ->delete();
                session()->flash('message', count($verifiedDepartments) . ' departments deleted.');
                break;

            case 'activate':
                Department::whereIn('id', $verifiedDepartments)
                         ->where('branch_id', $this->b_id)
                         ->update(['status' => 'active']);
                session()->flash('message', count($verifiedDepartments) . ' departments activated.');
                break;

            case 'deactivate':
                Department::whereIn('id', $verifiedDepartments)
                         ->where('branch_id', $this->b_id)
                         ->update(['status' => 'inactive']);
                session()->flash('message', count($verifiedDepartments) . ' departments deactivated.');
                break;
        }

        $this->selectedDepartments = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function exportData($format)
    {
        $this->openDropdown = null;

        // Export logic would go here
        session()->flash('message', "Exporting data as {$format}");

        // In production, you'd implement actual export similar to employee export
    }

    public function printData()
    {
        session()->flash('message', 'Preparing print view...');
    }

    private function resetForm()
    {
        $this->reset(['name', 'description', 'headId', 'status', 'editingId']);
        $this->resetValidation();
    }

    private function getFilteredDepartments()
    {
        return Department::with(['head'])
            ->where('branch_id', $this->b_id)
            ->when(!empty($this->statusFilters), fn($q) => $q->whereIn('status', $this->statusFilters))
            ->when($this->employeesMin, fn($q) => $q->where('employees_count', '>=', $this->employeesMin))
            ->when($this->employeesMax, fn($q) => $q->where('employees_count', '<=', $this->employeesMax))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when(
                $this->search,
                fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('head', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
            )
            ->latest()
            ->get();
    }

    public function render()
    {
        $departments = Department::with(['head'])
            ->where('branch_id', $this->b_id)
            ->when(!empty($this->statusFilters), fn($q) => $q->whereIn('status', $this->statusFilters))
            ->when($this->employeesMin, fn($q) => $q->where('employees_count', '>=', $this->employeesMin))
            ->when($this->employeesMax, fn($q) => $q->where('employees_count', '<=', $this->employeesMax))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when(
                $this->search,
                fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('head', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"))
            )
            ->latest()
            ->paginate(10);

        $branch = Branch::findOrFail($this->b_id);
        $employees = Employee::where('branch_id', $this->b_id)->get();

        return view('livewire.branch-dashboard.department-module.index', [
            'departments' => $departments,
            'branch' => $branch,
            'employees' => $employees,
        ]);
    }
}
