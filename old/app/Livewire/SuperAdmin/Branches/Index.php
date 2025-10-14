<?php

namespace App\Livewire\SuperAdmin\Branches;

use App\Models\Branch;
use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRegion = '';
    public $filterStatus = '';
    public $filterCountry = '';
    public $filterCity = '';
    public $dateFrom = '';
    public $dateTo = '';

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

    public function updatingFilterCountry()
    {
        $this->resetPage();
    }

    public function updatingFilterCity()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    private function branchesQuery()
    {
        $query = Branch::with('manager');

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

        if ($this->filterStatus !== null && $this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        if (! empty($this->filterCountry)) {
            $query->where('country', 'like', "%{$this->filterCountry}%");
        }

        if (! empty($this->filterCity)) {
            $query->where('city', 'like', "%{$this->filterCity}%");
        }

        if (! empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (! empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->latest();
    }

    public function updatedfilterStatus(){
        $this->branchesQuery();
    }

    public function edit($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $this->editingBranch = $branchId;
        $this->editName = $branch->name;
        $this->editCode = $branch->code;
        $this->editLocation = $branch->location;
        $this->editCountry = $branch->country;
        $this->editState = $branch->state;
        $this->editCity = $branch->city;
        $this->editPostalCode = $branch->postal_code;
        $this->editTimezone = $branch->timezone;
        $this->editPhone = $branch->phone;
        $this->editEmail = $branch->email;
        $this->editDescription = $branch->description;
        $this->editManagerUserId = $branch->manager_user_id;
        $this->editIsActive = $branch->is_active;

        $this->dispatch('open-edit-modal');
    }

    public function updateBranch()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editCode' => 'required|string|max:50|unique:branches,code,' . $this->editingBranch,
            'editLocation' => 'required|string|max:255',
            'editCountry' => 'required|string|max:100',
            'editState' => 'required|string|max:100',
            'editCity' => 'required|string|max:100',
            'editPostalCode' => 'nullable|string|max:20',
            'editTimezone' => 'nullable|string|max:50',
            'editPhone' => 'nullable|string|max:20',
            'editEmail' => 'nullable|email|max:255|unique:branches,email,' . $this->editingBranch,
            'editDescription' => 'nullable|string',
            'editManagerUserId' => 'nullable|exists:employees,id',
            'editIsActive' => 'boolean',
        ]);

        $branch = Branch::findOrFail($this->editingBranch);
        $branch->update([
            'name' => $this->editName,
            'code' => $this->editCode,
            'location' => $this->editLocation,
            'country' => $this->editCountry,
            'state' => $this->editState,
            'city' => $this->editCity,
            'postal_code' => $this->editPostalCode,
            'timezone' => $this->editTimezone,
            'phone' => $this->editPhone,
            'email' => $this->editEmail,
            'description' => $this->editDescription,
            'manager_user_id' => $this->editManagerUserId,
            'is_active' => $this->editIsActive,
        ]);

        $this->resetEditFields();
        $this->dispatch('close-edit-modal');
        $this->dispatch('branch-updated', 'Branch updated successfully!');
    }

    public function delete($branchId)
    {
        try {
            $branch = Branch::findOrFail($branchId);
            $branchName = $branch->name;
            $branch->delete();
            $this->dispatch('branch-deleted', "Branch '{$branchName}' has been deleted.");
        } catch (\Exception $e) {
            $this->dispatch('error', 'Unable to delete branch. It may have related records.');
        }
    }

    public function toggleStatus($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $branch->update(['is_active' => !$branch->is_active]);
        $status = $branch->is_active ? 'activated' : 'deactivated';
        $this->dispatch('status-updated', "Branch has been {$status}.");
    }

    public function applyBulkAction($action, $selectedIds)
    {
        if (empty($selectedIds)) {
            $this->dispatch('error', 'Please select at least one branch.');
            return;
        }

        switch ($action) {
            case 'delete':
                Branch::whereIn('id', $selectedIds)->delete();
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' branches deleted.');
                break;
            case 'activate':
                Branch::whereIn('id', $selectedIds)->update(['is_active' => true]);
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' branches activated.');
                break;
            case 'deactivate':
                Branch::whereIn('id', $selectedIds)->update(['is_active' => false]);
                $this->dispatch('bulk-action-completed', count($selectedIds) . ' branches deactivated.');
                break;
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

    private function resetEditFields()
    {
        $this->editingBranch = null;
        $this->editName = null;
        $this->editCode = null;
        $this->editLocation = null;
        $this->editCountry = null;
        $this->editState = null;
        $this->editCity = null;
        $this->editPostalCode = null;
        $this->editTimezone = null;
        $this->editPhone = null;
        $this->editEmail = null;
        $this->editDescription = null;
        $this->editManagerUserId = null;
        $this->editIsActive = null;
    }

    public function cancelEdit()
    {
        $this->resetEditFields();
        $this->dispatch('close-edit-modal');
    }

    public function handleBranchCreated()
    {
        $this->dispatch('branch-created', 'Branch created successfully!');
    }

    public function updatedPage()
    {
        // Clear selection on page change to avoid confusion across pages
        $this->reset('selected');
    }

    public function render()
    {
        return view('livewire.super-admin.branches.index', [
            'branches' => $this->branchesQuery()->paginate(10),
            'employees' => Employee::all(),
            'regions' => Branch::distinct()->pluck('state')->filter(),
        ]);
    }
}
