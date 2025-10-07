<?php

namespace App\Livewire\SuperAdmin\DepartmentModule;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $selectedBranch = '';
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
    public array $currentPageIds = [];
    public bool $selectAllPages = false;

    // Bulk Action
    public $bulkAction = '';

    // Form Fields
    public $name = '';
    public $description = '';
    public $headId = '';
    public $branchId = '';
    public $status = 'active';
    public $editingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedBranch' => ['except' => ''],
        'statusFilters' => ['except' => []],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'headId' => 'nullable|exists:users,id',
        'branchId' => 'required|exists:branches,id',
        'status' => 'required|in:active,inactive,pending',
    ];

    public function mount()
    {
        // Set default branch for branch heads
        if (Auth::user()->role === 'branch_head') {
            $this->selectedBranch = Auth::user()->branch_id;
        }
    }

    public function updatedSelectAll($value)
    {
        // Selecting current page should exit cross-page selection mode
        $this->selectAllPages = false;

        // Only toggle selection for the current page rows
        if ($value) {
            $this->selectedDepartments = array_values(array_unique(array_merge($this->selectedDepartments, $this->currentPageIds)));
        } else {
            // Remove only the current page IDs from the selection
            $this->selectedDepartments = array_values(array_diff($this->selectedDepartments, $this->currentPageIds));
        }

        // Recompute selectAll against the current page
        $this->selectAll = count(array_intersect($this->selectedDepartments, $this->currentPageIds)) === count($this->currentPageIds);
    }

    public function updatedSelectedDepartments()
    {
        // Keep the master checkbox in sync for the current page only
        $this->selectAll = count(array_intersect($this->selectedDepartments, $this->currentPageIds)) === count($this->currentPageIds);

        // If user manually adjusts selection, exit cross-page mode unless still covering all results
        if ($this->selectAllPages) {
            // If selection differs from all filtered IDs, disable selectAllPages
            $allIds = $this->getFilteredDepartments()->pluck('id')->toArray();
            $diff = array_diff($allIds, $this->selectedDepartments);
            if (!empty($diff)) {
                $this->selectAllPages = false;
            }
        }
    }

    public function toggleDropdown($dropdown)
    {
        $this->openDropdown = $this->openDropdown === $dropdown ? null : $dropdown;
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->openDropdown = null;
        $this->selectAllPages = false;
        $this->selectedDepartments = [];
        $this->selectAll = false;
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

        // Set default branch for branch heads
        if (Auth::user()->role === 'branch_head') {
            $this->branchId = Auth::user()->branch_id;
        }
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
            $department = Department::findOrFail($this->editingId);
            $department->update([
                'name' => $this->name,
                'description' => $this->description,
                'head_id' => $this->headId,
                'branch_id' => $this->branchId,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Department updated successfully.');
        } else {
            Department::create([
                'name' => $this->name,
                'description' => $this->description,
                'head_id' => $this->headId,
                'branch_id' => $this->branchId,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Department created successfully.');
        }

        $this->closeAddDepartment();
    }

    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);

        $this->editingId = $id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->headId = $department->head_id;
        $this->branchId = $department->branch_id;
        $this->status = $department->status;

        $this->showAddDepartment = true;
    }

    public function deleteDepartment($id)
    {
        Department::findOrFail($id)->delete();
        session()->flash('message', 'Department deleted successfully.');
        $this->selectedDepartments = array_diff($this->selectedDepartments, [$id]);
    }

    public function updatedPage()
    {
        // Clear selections when changing pages to avoid cross-page confusion
        $this->selectedDepartments = [];
        $this->selectAll = false;
        $this->selectAllPages = false;
    }

    public function selectAllResults()
    {
        // Select all filtered results across all pages
        $this->selectedDepartments = $this->getFilteredDepartments()->pluck('id')->toArray();
        $this->selectAllPages = true;
        $this->selectAll = count(array_intersect($this->selectedDepartments, $this->currentPageIds)) === count($this->currentPageIds);
    }

    public function clearSelectAllPages()
    {
        // Revert to only selecting the current page (or clear entirely)
        $this->selectAllPages = false;
        $this->selectedDepartments = array_values(array_intersect($this->selectedDepartments, $this->currentPageIds));
        $this->selectAll = count(array_intersect($this->selectedDepartments, $this->currentPageIds)) === count($this->currentPageIds);
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedDepartments) || !$this->bulkAction) {
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                Department::whereIn('id', $this->selectedDepartments)->delete();
                session()->flash('message', count($this->selectedDepartments) . ' departments deleted.');
                break;

            case 'activate':
                Department::whereIn('id', $this->selectedDepartments)->update(['status' => 'active']);
                session()->flash('message', count($this->selectedDepartments) . ' departments activated.');
                break;

            case 'deactivate':
                Department::whereIn('id', $this->selectedDepartments)->update(['status' => 'inactive']);
                session()->flash('message', count($this->selectedDepartments) . ' departments deactivated.');
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
        // For now, just show a message
        session()->flash('message', "Exporting data as {$format}");

        // In production, you'd implement actual export:
        // return response()->download(...);
    }

    public function printData()
    {
        // Print logic would go here
        session()->flash('message', 'Preparing print view...');
    }

    private function resetForm()
    {
        $this->reset(['name', 'description', 'headId', 'branchId', 'status', 'editingId']);
        $this->resetValidation();
    }

    private function getFilteredDepartments()
    {
        return Department::with(['branch', 'head'])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
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
        // Build base query once for reuse
        $baseQuery = Department::with(['branch', 'head'])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
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
            );

        $departments = (clone $baseQuery)->latest()->paginate(10);
        $this->currentPageIds = $departments->pluck('id')->toArray();
        $totalResults = (clone $baseQuery)->count();

        $branches = Branch::all();
        $users = User::where('role', 'department_head')->get();

        $userType = Auth::user()->role === 'admin' ? 'admin' : 'branchHead';

        return view('livewire.super-admin.department-module.index', [
            'departments' => $departments,
            'branches' => $branches,
            'users' => $users,
            'userType' => $userType,
            'totalResults' => $totalResults,
        ]);
    }
}
