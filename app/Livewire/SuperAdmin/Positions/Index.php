<?php

namespace App\Livewire\SuperAdmin\Positions;

use App\Livewire\BaseComponent;
use App\Models\Position;
use App\Models\Department;

class Index extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $filterDepartment = null;
    public ?int $filterLevel = null;

    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Modal states
    public bool $showPositionModal = false;
    public ?int $selectedId = null;
    public bool $isEditing = false;

    // Form fields
    public string $name = '';
    public ?int $department_id = null;
    public ?int $role_id = null;
    public ?int $reports_to = null;
    public int $level = 3;
    public string $description = '';

    protected function getModelClass(): string
    {
        return Position::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Position::query()
            ->with(['department', 'reportsTo'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->filterDepartment, function ($query) {
                $query->where('department_id', $this->filterDepartment);
            })
            ->when($this->filterLevel, function ($query) {
                $query->where('level', $this->filterLevel);
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
        $this->filterDepartment = null;
        $this->filterLevel = null;
        $this->resetPage();
    }

    public function exportExcel()
    {
        $this->toast()->success('Excel export feature coming soon!')->send();
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }

    public function openPositionModal()
    {
        $this->isEditing = false;
        $this->resetForm();
        $this->showPositionModal = true;
    }

    public function editPosition($id)
    {
        $position = Position::findOrFail($id);
        $this->isEditing = true;
        $this->selectedId = $id;
        $this->name = $position->name;
        $this->department_id = $position->department_id;
        $this->role_id = $position->role_id;
        $this->reports_to = $position->reports_to;
        $this->level = $position->level;
        $this->description = $position->description ?? '';
        $this->showPositionModal = true;
    }

    public function closePositionModal()
    {
        $this->showPositionModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->department_id = null;
        $this->role_id = null;
        $this->reports_to = null;
        $this->level = 3;
        $this->description = '';
        $this->selectedId = null;
        $this->isEditing = false;
    }

    public function savePosition()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'role_id' => 'nullable|exists:roles,id',
            'reports_to' => 'nullable|exists:positions,id',
            'level' => 'required|integer|min:1|max:3',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'department_id' => $this->department_id,
            'role_id' => $this->role_id,
            'reports_to' => $this->reports_to,
            'level' => $this->level,
            'description' => $this->description,
        ];

        if ($this->isEditing && $this->selectedId) {
            Position::findOrFail($this->selectedId)->update($data);
            $message = 'Position updated successfully!';
        } else {
            Position::create($data);
            $message = 'Position created successfully!';
        }

        $this->toast()->success($message)->send();
        $this->closePositionModal();
    }

    public function deletePosition($id): void
    {
        $this->selectedId = $id;

        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete this position?')
            ->confirm('Confirm', 'confirmedDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedDelete(string $message): void
    {
        if ($this->selectedId) {
            Position::findOrFail($this->selectedId)->delete();
            $this->dialog()->success('Success', 'Position deleted successfully!')->send();
            $this->selectedId = null;
        }
    }

    public function cancelledDelete(string $message): void
    {
        $this->selectedId = null;
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function bulkDeletePositions(): void
    {
        $this->dialog()
            ->question('Warning!', 'Are you sure you want to delete ' . count($this->selectedIds) . ' position(s)?')
            ->confirm('Confirm', 'confirmedBulkDelete', 'Confirmed Successfully')
            ->cancel('Cancel', 'cancelledBulkDelete', 'Cancelled Successfully')
            ->send();
    }

    public function confirmedBulkDelete(string $message): void
    {
        Position::whereIn('id', $this->selectedIds)->delete();
        $this->dialog()->success('Success', count($this->selectedIds) . ' position(s) deleted successfully!')->send();
        $this->selectedIds = [];
    }

    public function cancelledBulkDelete(string $message): void
    {
        $this->dialog()->error('Cancelled', $message)->send();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);
        $departments = Department::all();
        $positions = Position::all();
        $roles = \Spatie\Permission\Models\Role::where('guard_name', 'employees')->get();

        return view('livewire.super-admin.positions.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Position Name'],
                ['index' => 'department', 'label' => 'Department'],
                ['index' => 'role', 'label' => 'Role'],
                ['index' => 'reports_to', 'label' => 'Reports To'],
                ['index' => 'level', 'label' => 'Level'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'departments' => $departments,
            'positions' => $positions,
            'roles' => $roles,
        ]);
    }
}
