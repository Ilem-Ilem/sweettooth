<?php

namespace App\Livewire\BranchDashboard\Inventory\ShiftClosing;

use App\Livewire\BaseComponent;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Carbon\Carbon;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?string $branchId = null;
    public string $branchName = '';
    public ?string $currentShiftId = null;
    public $shiftDate;
    public $shiftType = 'morning';

    // Closing data
    public array $closingStocks = [];
    public bool $isVerified = false;
    public $notes = '';

    protected function getModelClass(): string
    {
        return Shift::class;
    }

    protected function getAllSelectableIds(): array
    {
        return [];
    }

    public function mount()
    {
        $this->mountBase();
        $this->branchId = request('b_id');
        if ($this->branchId) {
            $branch = Branch::find($this->branchId);
            $this->branchName = $branch?->name ?? 'Unknown Branch';
        }

        $this->shiftDate = Carbon::today()->format('Y-m-d');
        $this->loadCurrentShift();
        $this->loadClosingStockData();
    }

    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        // Get active shift for today (Inventory department)
        $activeShift = Shift::where('employee_id', $employee->id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->whereHas('department', function($q) {
                $q->where('name', 'Inventory'); // Inventory is not department-based
            })
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
            $this->shiftType = $activeShift->shift_type ?? 'morning';
        }
    }

    /**
     * Load stock closing data
     *
     * TODO: Enhance this method to:
     * - Load all raw material stocks from the Stock table
     * - Calculate variance between system count and physical count
     * - Show opening quantity, items used/dispatched during shift, expected closing
     * - Allow manual entry of actual closing count
     * - Track reasons for variances
     */
    public function loadClosingStockData()
    {
        if (!$this->currentShiftId) {
            $this->closingStocks = [];
            return;
        }

        // BASIC IMPLEMENTATION: Get all stocks
        $stocks = Stock::with('item')
            ->where('branch_id', $this->branchId)
            ->get();

        $closingStocks = [];
        foreach ($stocks as $stock) {
            $closingStocks[] = [
                'item_id' => $stock->item_id,
                'item_name' => $stock->item->name ?? 'N/A',
                'opening_quantity' => $stock->quantity ?? 0,
                'expected_closing' => $stock->quantity ?? 0, // TODO: Calculate based on movements
                'actual_closing' => $stock->quantity ?? 0,
                'variance' => 0,
                'notes' => '',
            ];
        }

        $this->closingStocks = $closingStocks;
    }

    /**
     * Save shift closing
     *
     * TODO: Implement full closing logic:
     * - Update Stock quantities with actual closing counts
     * - Create StockMovement records for variances
     * - Close the active shift (update status to 'closed')
     * - Generate shift closure report
     * - Send notifications to supervisor
     * - Lock data to prevent further modifications
     */
    public function saveShiftClosing()
    {
        if (!$this->currentShiftId) {
            $this->toast()->error('No active shift found.')->send();
            return;
        }

        DB::beginTransaction();
        try {
            // BASIC IMPLEMENTATION: Just mark shift as closed
            $shift = Shift::find($this->currentShiftId);
            if ($shift) {
                $shift->status = 'closed';
                $shift->notes = $this->notes;
                $shift->save();
            }

            // TODO:
            // 1. Update all Stock records with actual_closing values
            // 2. Create StockMovement records for each variance
            // 3. Calculate total variance value
            // 4. Generate shift_closure_summary record
            // 5. Send notification to inventory manager

            DB::commit();
            $this->toast()->success('Shift closed successfully!')->send();
            $this->isVerified = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error closing shift: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.shift-closing.index');
    }
}
