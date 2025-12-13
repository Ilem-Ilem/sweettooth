<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\GlAccount;

#[Layout('components.layouts.app.branch-dashboard')]
class GlAccountList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterCategory = '';
    public $filterActive = true;
    public $sortBy = 'account_number';
    public $sortDir = 'asc';

    protected $queryString = ['search', 'filterType', 'filterCategory'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
    }

    public function toggleStatus($accountId)
    {
        $account = GlAccount::find($accountId);
        if ($account) {
            $account->update(['is_active' => !$account->is_active]);
            $this->dispatch('success', message: 'Account status updated');
        }
    }

    public function render()
    {
        $query = GlAccount::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('account_number', 'like', "%{$this->search}%")
                  ->orWhere('account_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterType) {
            $query->where('account_type', $this->filterType);
        }

        if ($this->filterCategory) {
            $query->where('account_category', $this->filterCategory);
        }

        if ($this->filterActive) {
            $query->where('is_active', true);
        }

        $accounts = $query
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);

        return view('livewire.accounting.gl-account-list', [
            'accounts' => $accounts,
            'types' => [
                'asset' => 'Asset',
                'liability' => 'Liability',
                'equity' => 'Equity',
                'revenue' => 'Revenue',
                'cost_of_goods_sold' => 'Cost of Goods Sold',
                'expense' => 'Expense',
                'other_income' => 'Other Income',
                'other_expense' => 'Other Expense',
                'tax' => 'Tax',
            ],
        ]);
    }
}
