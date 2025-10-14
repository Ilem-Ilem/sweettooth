<?php

namespace App\Livewire\SuperAdmin\Inventory;

use App\Models\Branch;
use App\Models\HealthCheck;
use Livewire\Component;
use Livewire\WithPagination;

class HealthChecks extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBranch = '';
    public $filterCondition = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterActionTaken = '';

    public function render()
    {
        $query = HealthCheck::with(['stock.item', 'stock.branch', 'checker'])
            ->when($this->search, function ($q) {
                $q->whereHas('stock.item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBranch, function ($q) {
                $q->whereHas('stock', function ($query) {
                    $query->where('branch_id', $this->filterBranch);
                });
            })
            ->when($this->filterCondition, fn($q) => $q->where('condition', $this->filterCondition))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('check_date', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('check_date', '<=', $this->filterDateTo))
            ->when($this->filterActionTaken !== '', function ($q) {
                if ($this->filterActionTaken === '1') {
                    $q->whereNotNull('action_taken')->where('action_taken', '!=', '');
                } else {
                    $q->where(function ($query) {
                        $query->whereNull('action_taken')->orWhere('action_taken', '');
                    });
                }
            })
            ->orderBy('check_date', 'desc');

        $healthChecks = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('livewire.super-admin.inventory.health-checks', [
            'healthChecks' => $healthChecks,
            'branches' => $branches,
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterBranch = '';
        $this->filterCondition = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterActionTaken = '';
    }
}
