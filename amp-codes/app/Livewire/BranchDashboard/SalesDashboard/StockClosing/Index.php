<?php
namespace App\Livewire\BranchDashboard\SalesDashboard\StockClosing;

use App\Livewire\BaseComponent;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;
use App\Models\Department;
use App\Models\Branch;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

    #[Url(keep: true)]
    public ?string $salesDeptSlug = null;

    public ?string $branchId = null;
    public ?int $departmentId = null;
    public string $departmentName = 'Stock Closing';
    public string $branchName = '';

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?int $filterProductType = null;
    public ?string $filterStatus = null;

    // Stock closing data
    public array $stockClosings = [];
    public bool $isVerified = false;
    public ?string $currentShiftId = null;
    public string $shiftType = 'morning';
    public $stockDate;
    public $availableShifts = [];
    public $selectedShiftForViewing = null;

    public $selectedStockItem;

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'opening_quantity', 'label' => 'Opening Qty'],
        ['index' => 'addition_quantity', 'label' => 'Additions'],
        ['index' => 'callback_quantity', 'label' => 'Callbacks'],
        ['index' => 'redress_quantity', 'label' => 'Redresses'],
        ['index' => 'total_available', 'label' => 'Total Available'],
        ['index' => 'transfer_quantity', 'label' => 'Transfers'],
        ['index' => 'glovo_quantity', 'label' => 'Glovo Sales'],
        ['index' => 'quantity_sold', 'label' => 'Regular Sales'],
        ['index' => 'closing_quantity', 'label' => 'Closing Qty'],
        ['index' => 'amount', 'label' => 'Sales Amount'],
        ['index' => 'notes', 'label' => 'Notes'],
    ];

    public function updatedSelectedStockItem()
    {
       
    }

    public function loadSingleStockData(){
        
    }

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
        $this->mountBase();
        $this->loadBranchAndDepartment();
        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadAvailableShifts();
        $this->loadCurrentShift();
        $this->loadStockClosingData();
    }

    protected function loadBranchAndDepartment(): void
    {
        // Load branch
        $this->branchId = request('b_id');
        if ($this->branchId) {
            $branch = Branch::find($this->branchId);
            $this->branchName = $branch?->name ?? 'Unknown Branch';
        }

        // Load department from slug
        if ($this->salesDeptSlug) {
            // First try to find branch-specific department
            $department = Department::where('slug', $this->salesDeptSlug)
                ->where('branch_id', $this->branchId)
                ->first();

            // If not found, try to find global department (branch_id is null)
            if (!$department) {
                $department = Department::where('slug', $this->salesDeptSlug)
                    ->whereNull('branch_id')
                    ->first();
            }

            if ($department) {
                $this->departmentId = $department->id;
                $this->departmentName = $department->name;
            } else {
                $this->toast()->error('Department not found.')->send();
            }
        } else {
            // If no department slug provided, use employee's department
            $employee = auth('employees')->user();
            if ($employee && $employee->department_id) {
                $this->departmentId = $employee->department_id;
                $department = Department::find($employee->department_id);
                $this->departmentName = $department?->name ?? 'Stock Closing';
            }
        }

        // Validate branch access
        if (!$this->branchId) {
            $this->toast()->error('Branch not specified.')->send();
        }
    }

    /**
     * Load available shifts for date/shift selection
     */
    protected function loadAvailableShifts()
    {
        $employee = auth('employees')->user();
        $deptId = $this->departmentId ?? $employee->department_id;

        // Get shifts from last 30 days for the sales department
        $this->availableShifts = Shift::where('branch_id', $this->getBranchId())
            ->where('department_id', $deptId)
            ->where('shift_date', '>=', \Carbon\Carbon::today()->subDays(30))
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type', 'desc')
            ->get();
    }

    /**
     * When user selects a different shift to view
     */
    public function updatedSelectedShiftForViewing($shiftId)
    {
        if ($shiftId) {
            $shift = Shift::find($shiftId);
            if ($shift) {
                $this->stockDate = $shift->shift_date->format('Y-m-d');
                $this->shiftType = $shift->shift_type;
                $this->currentShiftId = $shiftId;
                $this->loadStockClosingData();
            }
        }
    }

    /**
     * When stock date changes
     */
    public function updatedStockDate($value)
    {
        $this->loadStockClosingData();
    }

    /**
     * Load current active shift or create new one
     */
    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        // Get active shift for today
        $activeShift = Shift::where('employee_id', $employee->id)
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
            $this->shiftType      = $activeShift->shift_type ?? 'morning';
        }
    }

    /**
     * Load stock closing data with calculations
     */
    public function loadStockClosingData()
    {
        // Show data even without active shift for viewing purposes
        // if (!$this->currentShiftId) {
        //     $this->stockClosings = [];
        //     return;
        // }

        // Get products from the department - filter by department
        $query = Product::query()->active(); // Only get active products

        // Filter by department using the scope
        if ($this->departmentId) {
            $query->forDepartment($this->departmentId);
        } else {
            // Fallback to employee's department if no departmentId is set
            $employee = auth('employees')->user();
            if ($employee && $employee->department_id) {
                // Use the same forDepartment scope for consistency
                $query->forDepartment($employee->department_id);
            }
        }

        $products = $query
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterProductType, function ($query) {
                $query->where('product_type_id', $this->filterProductType);
            })
            ->orderBy('name')
            ->get();

        $stockClosings = [];

        foreach ($products as $product) {
            // Get today's stock record for closing
            $todayStock = null;
            if ($this->currentShiftId) {
                $todayStock = ProductStock::where('sales_shift_id', $this->currentShiftId)
                    ->where('product_id', $product->id)
                    ->where('stock_date', $this->stockDate)
                    ->first();
            }

            if (!$todayStock) {
                // If no stock record exists, skip or create default
                continue;
            }

            // Calculate total available (opening + addition - callback - redress)
            $totalAvailable = $todayStock->calculateTotalAvailable();

            // Calculate closing (total available - transfer - glovo - sold)
            $closingQuantity = $todayStock->calculateClosing();

            $stockClosings[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_sku'       => $product->sku,
                'product_uom'       => $product->uom,
                'opening_quantity'  => $todayStock->opening_quantity,
                'addition_quantity' => $todayStock->addition_quantity,
                'callback_quantity' => $todayStock->callback_quantity,
                'redress_quantity'  => $todayStock->redress_quantity,
                'total_available'   => $totalAvailable,
                'transfer_quantity' => $todayStock->transfer_quantity,
                'glovo_quantity'    => $todayStock->glovo_quantity,
                'quantity_sold'     => $todayStock->quantity_sold,
                'closing_quantity'  => $closingQuantity,
                'amount'            => $todayStock->amount,
                'notes'             => $todayStock->notes,
                'is_saved'          => true, // Assuming it's loaded from DB
                'stock_id'          => $todayStock->id,
            ];
        }

        $this->stockClosings = $stockClosings;
    }

    /**
     * Update callback quantity for a product
     */
    public function updateCallbackQuantity($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['callback_quantity'] = (float) $value;
            $this->recalculateTotals($index);
        }
    }

    /**
     * Update redress quantity for a product
     */
    public function updateRedressQuantity($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['redress_quantity'] = (float) $value;
            $this->recalculateTotals($index);
        }
    }

    /**
     * Update transfer quantity for a product
     */
    public function updateTransferQuantity($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['transfer_quantity'] = (float) $value;
            $this->recalculateTotals($index);
        }
    }

    /**
     * Update glovo quantity for a product
     */
    public function updateGlovoQuantity($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['glovo_quantity'] = (float) $value;
            $this->recalculateTotals($index);
        }
    }

    /**
     * Update quantity sold for a product
     */
    public function updateQuantitySold($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['quantity_sold'] = (float) $value;
            $this->recalculateTotals($index);
        }
    }

    /**
     * Update sales amount for a product
     */
    public function updateAmount($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['amount'] = (float) $value;
        }
    }

    /**
     * Update notes for a product
     */
    public function updateNotes($productId, $value)
    {
        $index = collect($this->stockClosings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockClosings[$index]['notes'] = $value;
        }
    }

    /**
     * Recalculate totals for a stock item
     */
    private function recalculateTotals($index)
    {
        $item = $this->stockClosings[$index];
        $item['total_available'] = $item['opening_quantity'] + $item['addition_quantity'] - $item['callback_quantity'] - $item['redress_quantity'];
        $item['closing_quantity'] = $item['total_available'] - $item['transfer_quantity'] - $item['glovo_quantity'] - $item['quantity_sold'];
        $this->stockClosings[$index] = $item;
    }

    /**
     * Save all stock closings
     */
    public function saveStockClosings()
    {
        if (! $this->currentShiftId) {
            $this->toast()->error('No active shift found. Please clock in first.')->send();
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->stockClosings as $stockClosing) {
                ProductStock::where('id', $stockClosing['stock_id'])->update([
                    'callback_quantity' => $stockClosing['callback_quantity'],
                    'redress_quantity'  => $stockClosing['redress_quantity'],
                    'transfer_quantity' => $stockClosing['transfer_quantity'],
                    'glovo_quantity'    => $stockClosing['glovo_quantity'],
                    'quantity_sold'     => $stockClosing['quantity_sold'],
                    'closing_quantity'  => $stockClosing['closing_quantity'],
                    'amount'            => $stockClosing['amount'],
                    'notes'             => $stockClosing['notes'],
                ]);
            }

            DB::commit();
            $this->isVerified = true;
            $this->toast()->success('Stock closing saved successfully!')->send();
            $this->loadStockClosingData(); // Reload to show saved state
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error saving stock closing: ' . $e->getMessage())->send();
        }
    }

    /**
     * Check if stock closing is verified
     */
    public function checkVerificationStatus()
    {
        if (! $this->currentShiftId) {
            return false;
        }

        $count = ProductStock::where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate)
            ->where('closing_quantity', '>', 0)
            ->count();

        $this->isVerified = $count > 0;
        return $this->isVerified;
    }

    protected function getFilteredQuery()
    {
        return ProductStock::query()
            ->where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate);
    }

    public function updatedSearch()
    {
        $this->loadStockClosingData();
    }

    public function updatedFilterProductType()
    {
        $this->loadStockClosingData();
    }

    /**
     * Get rows for the table
     */
    public function getRowsProperty()
    {
        return collect($this->stockClosings)->map(function ($stock, $index) {
            $stock['index'] = $index;
            return (object) $stock;
        })->all();
    }

    public function render()
    {
        $employee = auth('employees')->user();
        $deptId = $this->departmentId ?? $employee->department_id;

        $productTypes = \App\Models\ProductType::whereHas('department', function ($q) use ($deptId) {
            $q->where('id', $deptId);
        })->active()->ordered()->get();

        return view('livewire.branch-dashboard.sales-dashboard.stock-closing.index', [
            'productTypes' => $productTypes,
            'rows' => $this->rows,
            'stockClosings' => $this->stockClosings,
        ]);
    }
}
