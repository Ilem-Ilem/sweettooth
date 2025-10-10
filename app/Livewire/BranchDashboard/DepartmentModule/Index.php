<?php

namespace App\Livewire\BranchDashboard\DepartmentModule;

use App\Livewire\BaseComponent;
use App\Models\Department;
use App\Models\Branch;
use App\Models\DepartmentCategory;
use Livewire\Attributes\{Layout, Url};

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    #[Url(keep: true)]
    public $b_id;

    // Filter fields
    public ?string $filterCategory = null;
    public ?string $filterBranch = null;

    // Modal states
    public bool $showDepartmentModal = false;
    public ?int $selectedDepartmentId = null;
    public bool $isEditing = false;

    // Department form fields
    public string $name = '';
    public ?string $branch_id = null;
    public ?string $category_id = null;
    public string $description = '';

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    protected function getModelClass(): string
    {
        return Department::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Department::query()
            ->where('b_id', '=', $this->b_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->advancedSearch . '%');

                });
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
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
        $this->filterCategory = null;
        $this->filterBranch = null;
        $this->resetPage();
    }

    // Export methods
    public function exportExcel()
    {
        $departments = $this->getFilteredQuery()->get();

        $csv = "ID,Name,Branch,Type,Description,Created At\n";
        foreach ($departments as $department) {
            $branchName = $department->branch ? $department->branch->name : 'N/A';
            $csv .= "\"{$department->id}\",\"{$department->name}\",\"{$branchName}\",\"{$department->type}\",\"{$department->description}\",\"{$department->created_at}\"\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'departments-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    // Modal methods
    public function openDepartmentModal()
    {
        $this->isEditing = false;
        $this->resetDepartmentForm();
        $this->showDepartmentModal = true;
    }

    public function editDepartment($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $this->isEditing = true;
        $this->selectedDepartmentId = $departmentId;
        $this->name = $department->name;
        $this->branch_id = $department->branch_id;
        $this->category_id = $department->category_id;
        $this->description = $department->description ?? '';
        $this->showDepartmentModal = true;
    }

    public function closeDepartmentModal()
    {
        $this->showDepartmentModal = false;
        $this->resetDepartmentForm();
    }

    public function resetDepartmentForm()
    {
        $this->name = '';
        $this->category_id = null;
        $this->description = '';
        $this->selectedDepartmentId = null;
        $this->isEditing = false;
    }

    public function saveDepartment()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $this->selectedDepartmentId,
            'category_id' => 'required|exists:department_categories,id',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'branch_id' => $this->b_id,
            'category_id' => $this->category_id,
            'description' => $this->description,
        ];

        if ($this->isEditing && $this->selectedDepartmentId) {
            Department::findOrFail($this->selectedDepartmentId)->update($data);
            $message = 'Department updated successfully!';
        } else {
            Department::create($data);
            $message = 'Department created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closeDepartmentModal();
    }

    // Delete methods
    public function deleteDepartment($departmentId): void
    {
        $this->selectedDepartmentId = $departmentId;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this department?')
            ->confirm('Confirm', 'confirmedDeleteDepartment', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDeleteDepartment', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDeleteDepartment(string $message): void
    {
        if ($this->selectedDepartmentId) {
            Department::findOrFail($this->selectedDepartmentId)->delete();
            $this->dialog()->success('Success', 'Department deleted successfully!')->send();
            $this->selectedDepartmentId = null;
        }
    }

    public function cancelledDeleteDepartment(string $message): void
    {
        $this->selectedDepartmentId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    // Bulk Delete
    public function bulkDeleteDepartments(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' department(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Department::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' department(s) deleted successfully!')->send();
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
        $categories = DepartmentCategory::all();

        return view('livewire.branch-dashboard.department-module.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Department Name'],
                ['index' => 'category', 'label' => 'Category'],
                ['index' => 'branch', 'label' => 'Branch'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'created_at', 'label' => 'Created At'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'branches' => $branches,
            'categories' => $categories,
        ]);
    }
}

#
