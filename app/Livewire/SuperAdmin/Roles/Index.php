<?php

namespace App\Livewire\SuperAdmin\Roles;

use App\Livewire\BaseComponent;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    protected function getModelClass(): string
    {
        return Role::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('guard_name', 'like', '%' . $this->advancedSearch . '%');
                });
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
        $this->resetPage();
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->paginate($this->quantity ?? 10);

        return view('livewire.super-admin.roles.index', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Role Name'],
                ['index' => 'guard_name', 'label' => 'Guard'],
                ['index' => 'created_at', 'label' => 'Created At'],
            ],
            'rows' => $rows,
        ]);
    }
}
