<?php

namespace App\Livewire\SuperAdmin\Departments;

use App\Livewire\BaseComponent;
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
    public ?string $filterType = null;
    public ?string $filterBranch = null;

    // Modal states
    public bool $showDepartmentModal = false;
    public bool $showDeleteModal = false;
    public ?int $selectedDepartmentId = null;
    public bool $isEditing = false;

    // Department form fields
    public string $name = '';
    public ?string $branch_id = null;
    public string $type = 'production';
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
            ->with('branch')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhereHas('branch', function ($branchQuery) {
                          $branchQuery->where('name', 'like', '%' . $this->advancedSearch . '%');
                      });
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterBranch, function ($query) {
                $query->where('branch_id', $this->filterBranch);
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
        $this->filterType = null;
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
        $this->type = $department->type;
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
        $this->branch_id = null;
        $this->type = 'production';
        $this->description = '';
        $this->selectedDepartmentId = null;
        $this->isEditing = false;
    }

    public function saveDepartment()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $this->selectedDepartmentId,
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:production,sales',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'type' => $this->type,
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

    public function confirmDelete($departmentId)
    {
        $this->selectedDepartmentId = $departmentId;
        $this->showDeleteModal = true;
    }

    public function deleteDepartment()
    {
        if ($this->selectedDepartmentId) {
            Department::findOrFail($this->selectedDepartmentId)->delete();
            $this->toast()->success('Department deleted successfully!')->send();
            $this->showDeleteModal = false;
            $this->selectedDepartmentId = null;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedDepartmentId = null;
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $branches = Branch::where('is_active', true)->get();

        return view('livewire.super-admin.departments.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Department Name'],
                ['index' => 'branch', 'label' => 'Branch'],
                ['index' => 'type', 'label' => 'Type'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'created_at', 'label' => 'Created At'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'branches' => $branches,
        ]);
    }
}
