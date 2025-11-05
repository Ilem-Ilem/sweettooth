<?php

namespace App\Livewire\BranchDashboard\SalesDashboard;

use App\Livewire\BaseComponent;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class StockMonitor extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public $stockDate;
    public $shiftType = 'morning';
    public ?string $currentShiftId = null;

    // Stats
    public $totalProducts = 0;
    public $lowStockCount = 0;
    public $expiredCount = 0;
    public $criticalCount = 0;

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'current_stock', 'label' => 'Current Stock'],
        ['index' => 'opening', 'label' => 'Opening', 'collapsible' => true],
        ['index' => 'additions', 'label' => 'Additions', 'collapsible' => true],
        ['index' => 'sold', 'label' => 'Sold', 'collapsible' => true],
        ['index' => 'callbacks', 'label' => 'Callbacks', 'collapsible' => true],
        ['index' => 'closing', 'label' => 'Closing', 'collapsible' => true],
        ['index' => 'production_date', 'label' => 'Production Date', 'collapsible' => true],
        ['index' => 'shelf_life', 'label' => 'Shelf Life'],
        ['index' => 'action', 'label' => 'Action', 'collapsible' => true],
    ];

    protected function getModelClass(): string
    {
        return ProductStock::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadCurrentShift();
        $this->calculateStats();
    }

    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        $activeShift = Shift::where('employee_id', $employee->id)
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
            $this->shiftType = $activeShift->shift_type ?? 'morning';
        }
    }

    public function calculateStats()
    {
        if (!$this->currentShiftId) {
            return;
        }

        $stocks = ProductStock::where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate)
            ->with('product')
            ->get();

        $this->totalProducts = $stocks->count();
        $this->lowStockCount = $stocks->filter(function ($stock) {
            $currentStock = $stock->closing_quantity;
            return $currentStock > 0 && $currentStock < ($stock->opening_quantity * 0.3);
        })->count();

        $this->expiredCount = $stocks->filter(fn($s) => $s->getShelfLifeStatus() === 'expired')->count();
        $this->criticalCount = $stocks->filter(fn($s) => $s->getShelfLifeStatus() === 'critical')->count();
    }

    public function getRowsProperty()
    {
        if (!$this->currentShiftId) {
            return collect([]);
        }

        $query = ProductStock::with(['product', 'salesShift'])
            ->where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate);

        // Search filter
        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter (shelf life)
        if ($this->filterStatus) {
            $allRecords = $query->get()->filter(function ($stock) {
                return $stock->getShelfLifeStatus() === $this->filterStatus;
            });

            return $allRecords->paginate($this->quantity);
        }

        return $query->paginate($this->quantity);
    }

    protected function getFilteredQuery()
    {
        return ProductStock::query()
            ->where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function refreshData()
    {
        $this->calculateStats();
        $this->resetPage();
        $this->toast()->success('Stock data refreshed')->send();
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.stock-monitor', [
            'rows' => $this->rows,
        ]);
    }
}
