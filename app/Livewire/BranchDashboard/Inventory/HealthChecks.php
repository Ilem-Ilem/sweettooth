<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Models\HealthCheck;
use App\Models\Stock;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, On, Url};
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.branch-dashboard')]
class HealthChecks extends Component
{
    use WithPagination;
    #[Url(keep:true)]
    public ?string $b_id = null;

    // Listen for branch changes from BranchSelector (for super admins)
    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public $search = '';
    public $filterCondition = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterActionTaken = '';

    public $showModal = false;
    public $healthCheckId;
    public $stock_id = '';
    public $check_date;
    public $condition = '';
    public $quantity_affected;
    public $observations;
    public $action_taken;

    protected $rules = [
        'stock_id' => 'required|exists:stocks,id',
        'check_date' => 'required|date',
        'condition' => 'required|in:good,fair,poor,damaged,expired',
        'quantity_affected' => 'nullable|numeric|min:0',
        'observations' => 'nullable|string',
        'action_taken' => 'nullable|string',
    ];

       public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }


    public function mount()
    {
        $this->check_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = HealthCheck::with(['stock.item', 'stock.branch', 'checker'])
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('stock.item', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
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
        $stocks = Stock::with('item')
            ->where('branch_id', $branchId)
            ->get();

        return view('livewire.branch-dashboard.inventory.health-checks', [
            'healthChecks' => $healthChecks,
            'stocks' => $stocks,
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-health-checks'); // TODO: Enable permissions after testing
        $this->resetFields();
        $this->showModal = true;
    }

    public function save()
    {
        // $this->authorize('create-health-checks'); // TODO: Enable permissions after testing
        $this->validate();

        // Verify stock belongs to branch
        $stock = Stock::findOrFail($this->stock_id);
        if ($stock->branch_id !== $this->getBranchId()) {
            session()->flash('error', 'Invalid stock selection.');
            return;
        }

        $actor = current_actor();

        $healthCheck = HealthCheck::create([
            'stock_id' => $this->stock_id,
            'checked_by_id' => $actor->id,
            'checked_by_type'=>get_class($actor),
            'check_date' => $this->check_date,
            'condition' => $this->condition,
            'quantity_affected' => $this->quantity_affected,
            'observations' => $this->observations,
            'action_taken' => $this->action_taken,
        ]);

        // Log the health check
        $stock = Stock::findOrFail($this->stock_id);
        AuditService::log(
            $actor,
            'create',
            $healthCheck,
            "Created health check for item '{$stock->item->name}'. " .
            "Condition: {$this->condition}, Qty Affected: {$this->quantity_affected} {$stock->item->unitOfMeasure?->symbol}. " .
            "Observations: {$this->observations}. Action: {$this->action_taken}",
            'completed'
        );

        session()->flash('success', 'Health check recorded successfully.');
        $this->closeModal();
        $this->resetFields();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function resetFields()
    {
        $this->healthCheckId = null;
        $this->stock_id = '';
        $this->check_date = now()->format('Y-m-d');
        $this->condition = '';
        $this->quantity_affected = null;
        $this->observations = '';
        $this->action_taken = '';
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterCondition = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterActionTaken = '';
    }
}
