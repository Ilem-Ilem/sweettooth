<?php

namespace App\Livewire\SuperAdmin\Branches;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Employee;
use Livewire\WithPagination;

class Deleted extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRegion = '';
    public $filterStatus = '';

    // Edit functionality
    public $editingBranch = null;
    public $editName, $editCode, $editLocation, $editCountry;
    public $editState, $editCity, $editPostalCode, $editTimezone;
    public $editPhone, $editEmail, $editDescription;
    public $editManagerUserId, $editIsActive;

    public array $selected = [];

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'filterRegion', 'filterStatus'];

    protected $listeners = ['refresh' => '$refresh', 'branchCreated' => 'handleBranchCreated'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRegion()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    private function branchesQuery()
    {
        // $query = Branch::de with('manager');
        $query = Branch::onlyTrashed()->with('manager');
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->filterRegion)) {
            $query->where('state', $this->filterRegion);
        }

        if ($this->filterStatus != null || $this->filterStatus != '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        return $query->latest();
    }

    public function updatedfilterStatus(){
        $this->branchesQuery();
    }


    public function delete($branchId)
    {
        try {
            $branch = Branch::withTrashed()->findOrFail($branchId);
            $branchName = $branch->name;
            $branch->forceDelete();
            $this->dispatch('branch-deleted', "Branch '{$branchName}' permanently deleted.");
            $this->reset('selected');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Unable to permanently delete branch.');
        }
    }

    public function restore($branchId)
    {
        $branch = Branch::onlyTrashed()->findOrFail($branchId);
        $branch->restore();
        $this->dispatch('bulk-action-completed', "Branch '{$branch->name}' restored.");
        $this->reset('selected');
    }

    public function applyBulkAction($action, $selectedIds)
    {
        if (empty($selectedIds)) {
            $this->dispatch('error', 'Please select at least one branch.');
            return;
        }

        switch ($action) {
            case 'restore':
                Branch::onlyTrashed()->whereIn('id', $selectedIds)->restore();
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' branches restored.');
                break;
            case 'delete':
                Branch::withTrashed()->whereIn('id', $selectedIds)->forceDelete();
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' branches permanently deleted.');
                break;
            default:
                $this->dispatch('error', 'Select a valid bulk action.');
                return;
        }

        $this->reset('selected');
    }

    public function export($format)
    {
        $branches = $this->branchesQuery()->get();

        switch ($format) {
            case 'csv':
                return $this->exportCsv($branches);
            case 'excel':
                return $this->exportExcel($branches);
            case 'pdf':
                return $this->exportPdf($branches);
        }
    }

    private function exportCsv($branches)
    {
        $filename = 'branches_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Headers
        fputcsv($handle, ['Name', 'Code', 'Location', 'City', 'State', 'Country', 'Phone', 'Email', 'Manager', 'Status']);

        foreach ($branches as $branch) {
            fputcsv($handle, [
                $branch->name,
                $branch->code,
                $branch->location,
                $branch->city,
                $branch->state,
                $branch->country,
                $branch->phone,
                $branch->email,
                $branch->manager->name ?? 'N/A',
                $branch->is_active ? 'Active' : 'Inactive'
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportExcel($branches)
    {
        // For now, export as CSV format until Excel package is installed
        return $this->exportCsv($branches);
    }

    private function exportPdf($branches)
    {
        // For now, export as CSV format until PDF package is installed
        return $this->exportCsv($branches);
    }


    public function render()
    {
        return view('livewire.super-admin.branches.deleted', [
            'branches' => $this->branchesQuery()->paginate(10),
            'employees' => Employee::all(),
            'regions' => Branch::distinct()->pluck('state')->filter(),
        ]);
    }
}
