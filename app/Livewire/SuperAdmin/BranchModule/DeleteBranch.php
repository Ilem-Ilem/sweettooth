<?php

namespace App\Livewire\SuperAdmin\BranchModule;

use App\Livewire\BaseComponent;
use Livewire\Component;
use App\Models\Branch;
use App\Models\User;

class DeleteBranch extends BaseComponent
{
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $advancedSearch = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // Filter fields
    public ?string $filterStatus = null;
    public ?string $filterCountry = null;
    public ?string $filterCity = null;
    public ?string $filterManager = null;

    // Modal states
    public bool $showBranchModal = false;
    public bool $showDeleteModal = false;
    public ?string $selectedBranchId = null;
    public bool $isEditing = false;


    protected array $bulkActions = [
        'delete' => ['label' => 'Delete Selected', 'method' => 'bulkDelete'],
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    protected function getModelClass(): string
    {
        return Branch::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        return Branch::query()->onlyTrashed()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->advancedSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('code', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('location', 'like', '%' . $this->advancedSearch . '%')
                      ->orWhere('city', 'like', '%' . $this->advancedSearch . '%');
                });
            })
            ->when($this->filterStatus !== null && $this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'active');
            })
            ->when($this->filterCountry, function ($query) {
                $query->where('country', 'like', '%' . $this->filterCountry . '%');
            })
            ->when($this->filterCity, function ($query) {
                $query->where('city', 'like', '%' . $this->filterCity . '%');
            })
            ->when($this->filterManager, function ($query) {
                $query->where('manager_user_id', $this->filterManager);
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
        $this->filterCountry = null;
        $this->filterCity = null;
        $this->filterManager = null;
        $this->resetPage();
    }

    // Export methods
    public function exportExcel()
    {
        $branches = $this->getFilteredQuery()->get();

        $csv = "ID,Name,Code,Location,Phone,Email,City,Country,Active,Created At\n";
        foreach ($branches as $branch) {
            $csv .= "{$branch->id},{$branch->name},{$branch->code},{$branch->location},{$branch->phone},{$branch->email},{$branch->city},{$branch->country}," . ($branch->is_active ? 'Yes' : 'No') . ",{$branch->created_at}\n";
        }

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, 'branches-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf()
    {
        $this->toast()->success('PDF export feature coming soon!')->send();
    }




    public function confirmDelete($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->showDeleteModal = true;
    }

    public function deleteBranch()
    {
        if ($this->selectedBranchId) {
            Branch::findOrFail($this->selectedBranchId)->destroy();
            $this->toast()->success('Branch deleted successfully!')->send();
            $this->showDeleteModal = false;
            $this->selectedBranchId = null;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedBranchId = null;
    }

    public function render()
    {
        $rows = $this->getFilteredQuery()->orderBy('created_at', 'DESC')->paginate($this->quantity ?? 10);
        $users = User::all();

        return view('livewire.super-admin.branch-module.delete-branch', [
            'headers' => [
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Branch Name'],
                ['index' => 'code', 'label' => 'Code'],
                ['index' => 'location', 'label' => 'Location'],
                ['index' => 'city', 'label' => 'City'],
                ['index' => 'action', 'label' => 'Actions', 'display' => true],
            ],
            'rows' => $rows,
            'users' => $users,
        ]);
    }
}
