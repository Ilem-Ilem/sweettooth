<?php
namespace App\Livewire\BranchDashboard\SalesDashboard\StockOpening;

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
    public string $departmentName = 'Stock Opening';
    public string $branchName = '';

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?int $filterProductType = null;
    public ?string $filterStatus = null;

    // Stock opening data
    public array $stockOpenings = [];
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
        ['index' => 'yesterday_closing', 'label' => 'Previous Closing', 'collapsible' => true],
        ['index' => 'today_additions', 'label' => 'Production Sent', 'collapsible' => true],
        ['index' => 'expected_opening', 'label' => 'Expected Opening', 'collapsible' => true],
        ['index' => 'actual_opening', 'label' => 'Actual Opening'],
        ['index' => 'variance', 'label' => 'Variance', 'collapsible' => true],
        ['index' => 'variance_source', 'label' => 'Variance From', 'collapsible' => true],
        ['index' => 'production_date', 'label' => 'Production Date', 'collapsible' => true],
        ['index' => 'shelf_life', 'label' => 'Shelf Life', 'collapsible' => true],
        ['index' => 'notes', 'label' => 'Notes', 'collapsible' => true],
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
        $this->loadStockOpeningData();
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
                $this->departmentName = $department?->name ?? 'Stock Opening';
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
                $this->loadStockOpeningData();
            }
        }
    }

    /**
     * When stock date changes
     */
    public function updatedStockDate($value)
    {
        $this->loadStockOpeningData();
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
     * Load stock opening data with yesterday's closing and today's additions
     */
    public function loadStockOpeningData()
    {
        // Show data even without active shift for viewing purposes
        // if (!$this->currentShiftId) {
        //     $this->stockOpenings = [];
        //     return;
        // }

        $yesterday = \Carbon\Carbon::parse($this->stockDate)->subDay()->format('Y-m-d');

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

        $stockOpenings = [];

        foreach ($products as $product) {
            // Get yesterday's closing stock
            $yesterdayStock = ProductStock::where('product_id', $product->id)
                ->where('stock_date', $yesterday)
                ->where('shift_type', $this->shiftType)
                ->first();

            // Get today's additions from production records (quantity_sent_out)
            // This pulls from production_records table where batches were marked as sent to sales
            $todayAdditions = 0;
            $additionSources = [];

            try {
                // Get production records for this product (match by product name from recipe)
                $productionRecords = DB::table('production_records')
                    ->join('daily_produces', 'production_records.daily_produce_id', '=', 'daily_produces.id')
                    ->join('shifts', 'daily_produces.shift_id', '=', 'shifts.id')
                    ->join('recipes', 'daily_produces.recipe_id', '=', 'recipes.id')
                    ->where('recipes.product_name', $product->name) // Match by product name
                    ->whereDate('shifts.shift_date', $this->stockDate)
                    ->where('shifts.shift_type', $this->shiftType)
                    ->select(
                        'production_records.quantity_sent_out',
                        'production_records.batch_number',
                        'production_records.quantity_approved',
                        'production_records.quantity_produced',
                        'production_records.quantity_rejected',
                        'recipes.yield_quantity',
                        'shifts.shift_type',
                        'shifts.shift_date'
                    )
                    ->get();

                foreach ($productionRecords as $record) {
                    // Use yield produced (quantity_approved) not batch
                    $todayAdditions += $record->quantity_approved ?? 0;
                    if ($record->quantity_approved > 0) {
                        // Calculate yield percentage
                        $yieldPercentage = 0;
                        if ($record->quantity_produced > 0) {
                            $yieldPercentage = ($record->quantity_approved / $record->quantity_produced) * 100;
                        }

                        $additionSources[] = [
                            'batch' => $record->batch_number,
                            'quantity_sent' => $record->quantity_sent_out,
                            'quantity_produced' => $record->quantity_produced,
                            'quantity_approved' => $record->quantity_approved,
                            'quantity_rejected' => $record->quantity_rejected,
                            'recipe_yield' => $record->yield_quantity ?? 0,
                            'actual_yield_percentage' => round($yieldPercentage, 2),
                            'shift' => $record->shift_type,
                            'date' => $record->shift_date,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Table might not exist yet or query error - log for debugging
                $todayAdditions = 0;
                $additionSources = [];
                // Uncomment for debugging: \Log::error('Stock opening query error: ' . $e->getMessage());
            }

            // Get today's existing stock record
            $todayStock = null;
            if ($this->currentShiftId) {
                $todayStock = ProductStock::where('sales_shift_id', $this->currentShiftId)
                    ->where('product_id', $product->id)
                    ->where('stock_date', $this->stockDate)
                    ->first();
            }

            $yesterdayClosing = $yesterdayStock ? $yesterdayStock->closing_quantity : 0;
            // Expected opening is yesterday's closing (what we expect to find before adding today's production)
            $expectedOpening  = $yesterdayClosing;
            // Actual opening is what we actually count (defaults to expected if not yet verified)
            $actualOpening = $todayStock ? $todayStock->opening_quantity : $yesterdayClosing;
            // Variance is the difference between actual count and expected (yesterday's closing)
            $variance = $actualOpening - $expectedOpening;
            // Total expected after additions
            $expectedWithAdditions = $expectedOpening + $todayAdditions;

            // Determine variance source
            $varianceSource = 'None';
            if ($variance != 0) {
                // Variance is between actual opening and expected (yesterday's closing)
                $varianceSource = 'Stock count difference from previous closing (' . $yesterday . ')';
            }

            $stockOpenings[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_sku'       => $product->sku,
                'product_uom'       => $product->uom,
                'yesterday_closing' => $yesterdayClosing,
                'today_additions'   => $todayAdditions,
                'addition_sources'  => $additionSources,
                'expected_opening'  => $expectedOpening,
                'actual_opening'    => $actualOpening,
                'variance'          => $variance,
                'variance_source'   => $varianceSource,
                'production_date'   => $todayStock ? $todayStock->production_date?->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d'),
                'expiry_date'       => $todayStock ? $todayStock->expiry_date?->format('Y-m-d') : null,
                'shelf_life_days'   => $product->shelf_life_days,
                'notes'             => $todayStock ? $todayStock->notes : '',
                'is_saved'          => $todayStock !== null,
            ];
        }

        $this->stockOpenings = $stockOpenings;
    }

    /**
     * Update actual opening quantity for a product
     */
    public function updateActualOpening($productId, $value)
    {
        $index = collect($this->stockOpenings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockOpenings[$index]['actual_opening'] = (float) $value;
            $this->stockOpenings[$index]['variance']       =
            $this->stockOpenings[$index]['actual_opening'] -
            $this->stockOpenings[$index]['expected_opening'];
        }
    }

    /**
     * Update production date for a product
     */
    public function updateProductionDate($productId, $value)
    {
        $index = collect($this->stockOpenings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockOpenings[$index]['production_date'] = $value;

            // Auto-calculate expiry date based on shelf life
            $product = Product::find($productId);
            if ($product && $product->shelf_life_days > 0) {
                $productionDate                             = \Carbon\Carbon::parse($value);
                $this->stockOpenings[$index]['expiry_date'] =
                $productionDate->copy()->addDays($product->shelf_life_days)->format('Y-m-d');
            }
        }
    }

    /**
     * Update notes for a product
     */
    public function updateNotes($productId, $value)
    {
        $index = collect($this->stockOpenings)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->stockOpenings[$index]['notes'] = $value;
        }
    }


    /**
     * Save all stock openings
     */
    public function saveStockOpenings()
    {
        if (! $this->currentShiftId) {
            $this->toast()->error('No active shift found. Please clock in first.')->send();
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->stockOpenings as $stockOpening) {
                // Use shift_id from shifts table (sales department shift)
                // sales_shift_id can be null since we're using the general shifts table
                // addition_quantity represents the total quantity yield (approved quantity sent from production)
                ProductStock::updateOrCreate(
                    [
                        'product_id'     => $stockOpening['product_id'],
                        'stock_date'     => $this->stockDate,
                        'shift_type'     => $this->shiftType,
                    ],
                    [
                        'sales_shift_id'    => null, // Nullable - we use shifts table instead
                        'opening_quantity'  => $stockOpening['actual_opening'],
                        'addition_quantity' => $stockOpening['today_additions'], // Total yield from production
                        'production_date'   => $stockOpening['production_date'],
                        'expiry_date'       => $stockOpening['expiry_date'],
                        'notes'             => $stockOpening['notes'],
                        'total_available'   => $stockOpening['actual_opening'] + $stockOpening['today_additions'],
                        'closing_quantity'  => $stockOpening['actual_opening'] + $stockOpening['today_additions'],
                    ]
                );
            }

            DB::commit();
            $this->isVerified = true;
            $this->toast()->success('Stock opening saved successfully!')->send();
            $this->loadStockOpeningData(); // Reload to show saved state
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error saving stock opening: ' . $e->getMessage())->send();
        }
    }

    /**
     * Check if stock opening is verified
     */
    public function checkVerificationStatus()
    {
        if (! $this->currentShiftId) {
            return false;
        }

        $count = ProductStock::where('sales_shift_id', $this->currentShiftId)
            ->where('stock_date', $this->stockDate)
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
        $this->loadStockOpeningData();
    }

    public function updatedFilterProductType()
    {
        $this->loadStockOpeningData();
    }

    /**
     * Get rows for the table
     */
    public function getRowsProperty()
    {
        return collect($this->stockOpenings)->map(function ($stock, $index) {
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

        return view('livewire.branch-dashboard.sales-dashboard.stock-opening.index', [
            'productTypes' => $productTypes,
            'rows' => $this->rows,
            'stockOpenings' => $this->stockOpenings,
        ]);
    }
}
