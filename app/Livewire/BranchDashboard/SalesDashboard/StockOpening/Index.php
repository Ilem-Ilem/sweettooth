<?php
namespace App\Livewire\BranchDashboard\SalesDashboard\StockOpening;

use App\Livewire\BaseComponent;
use App\Livewire\Concerns\SalesDepartmentContext;
use App\Models\Product;
use App\Models\ProductDispatch;
use App\Models\ProductStock;
use App\Models\Shift;
use App\Services\SalesWorkflowService;
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
    use WithPagination, Interactions, SalesDepartmentContext;

    #[Url(keep: true)]
    public ?string $b_id = null;

    // Note: salesDeptSlug, branchId, departmentId, departmentName, branchName
    // are now provided by SalesDepartmentContext trait

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?int $filterProductType = null;
    public ?string $filterStatus = null;

    // Stock opening data
    public array $stockOpenings = [];
    public array $unclosedProducts = [];
    public bool $isVerified = false;
    public ?string $currentShiftId = null;
    public string $shiftType = 'morning';
    public $stockDate;
    public $availableShifts = [];
    public $selectedShiftForViewing = null;

    public ?ProductStock $selectedStockItem = null;

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
        $this->initializeDepartmentContext(); // Using trait method
        $this->departmentName = $this->departmentName ?: 'Stock Opening';
        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadAvailableShifts();
        $this->loadCurrentShift();
        $this->loadStockOpeningData();
    }

    // loadBranchAndDepartment is now handled by SalesDepartmentContext trait

    /**
     * Load available shifts for date/shift selection
     */
    protected function loadAvailableShifts()
    {
        $employee = auth()->user();
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
        $employee = auth()->user();

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

        if ($this->departmentId) {
            $query->forDepartment($this->departmentId);
        } else {
            // Fallback to employee's department if no departmentId is set
            $employee = auth()->user();
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
        $unclosedProducts = [];

        foreach ($products as $product) {
            // Get yesterday's closing stock
            $yesterdayStock = ProductStock::where('product_id', $product->id)
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id'), function ($q) {
                    $q->where('department_id', $this->departmentId);
                })
                ->where('stock_date', $yesterday)
                ->where('shift_type', $this->shiftType)
                ->first();

            // If no closing for yesterday, surface last known closing for carry-forward
            if (!$yesterdayStock) {
                $lastStock = ProductStock::where('product_id', $product->id)
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id'), function ($q) {
                        $q->where('department_id', $this->departmentId);
                    })
                    ->where('stock_date', '<', $this->stockDate)
                    ->where('shift_type', $this->shiftType)
                    ->orderBy('stock_date', 'desc')
                    ->first();

                if ($lastStock && (float) $lastStock->closing_quantity > 0) {
                    $unclosedProducts[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'product_uom' => $product->unitOfMeasure?->symbol,
                        'last_closing' => $lastStock->closing_quantity,
                        'last_stock_date' => $lastStock->stock_date?->format('Y-m-d') ?? null,
                        'last_shift_type' => $lastStock->shift_type,
                    ];
                }
            }

            // Get today's additions strictly from received dispatches (sales-confirmed only).
            $todayAdditions = 0;
            $additionSources = [];

            $receivedDispatches = ProductDispatch::query()
                ->where('product_id', $product->id)
                ->where('status', 'received')
                ->whereDate('received_at', $this->stockDate)
                ->where('sales_department_id', $this->departmentId)
                ->orderBy('received_at')
                ->get([
                    'id',
                    'quantity',
                    'received_quantity',
                    'dispatch_time',
                    'received_at',
                    'shift_type',
                    'uom',
                    'notes',
                ]);

            foreach ($receivedDispatches as $dispatch) {
                $receivedQty = (float) ($dispatch->received_quantity ?? $dispatch->quantity ?? 0);
                if ($receivedQty <= 0) {
                    continue;
                }

                $todayAdditions += $receivedQty;
                $additionSources[] = [
                    'dispatch_id' => $dispatch->id,
                    'quantity_received' => $receivedQty,
                    'uom' => $dispatch->uom,
                    'shift' => $dispatch->shift_type,
                    'dispatch_time' => $dispatch->dispatch_time?->format('H:i'),
                    'received_time' => $dispatch->received_at?->format('H:i'),
                    'notes' => $dispatch->notes,
                ];
            }

            // Get today's existing stock record
            $todayStock = null;
            $todayStock = ProductStock::query()
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id'), function ($q) {
                    $q->where('department_id', $this->departmentId);
                })
                ->where('product_id', $product->id)
                ->where('stock_date', $this->stockDate)
                ->where('shift_type', $this->shiftType)
                ->first();

            $yesterdayClosing = $yesterdayStock ? $yesterdayStock->closing_quantity : 0;
            // Expected opening is previous closing + production sent today
            $expectedOpening  = $yesterdayClosing + $todayAdditions;
            // Actual opening is what we actually count (defaults to expected if not yet verified)
            $actualOpening = $todayStock ? $todayStock->opening_quantity : $expectedOpening;
            // Variance is the difference between actual count and expected
            $variance = $actualOpening - $expectedOpening;

            // Determine variance source
            $varianceSource = 'None';
            if ($variance != 0) {
                $varianceSource = 'Stock count difference vs expected opening (' . $yesterday . ' + production)';
            }

            $stockOpenings[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_sku'       => $product->sku,
                'product_uom'       => $product->unitOfMeasure?->symbol,
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
        $this->unclosedProducts = $unclosedProducts;
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
                $hasDepartmentColumn = \Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id');
                $lookup = [
                    'product_id'     => $stockOpening['product_id'],
                    'stock_date'     => $this->stockDate,
                    'shift_type'     => $this->shiftType,
                ];

                if ($hasDepartmentColumn) {
                    $lookup['department_id'] = $this->departmentId;
                }

                ProductStock::updateOrCreate(
                    $lookup,
                    [
                        'sales_shift_id'    => null, // Nullable - we use shifts table instead
                        'department_id'     => $hasDepartmentColumn
                            ? $this->departmentId
                            : null,
                        'opening_quantity'  => $stockOpening['actual_opening'],
                        'addition_quantity' => $stockOpening['today_additions'], // Total yield from production
                        'production_date'   => $stockOpening['production_date'],
                        'expiry_date'       => $stockOpening['expiry_date'],
                        'notes'             => $stockOpening['notes'],
                        'total_available'   => $stockOpening['actual_opening'] + $stockOpening['today_additions'],
                        'closing_quantity'  => $stockOpening['actual_opening'] + $stockOpening['today_additions'],
                        'is_workflow_verified' => true,
                        'verified_at'       => now(),
                        'verified_by'       => auth()->id() ?? auth()->id(),
                        'workflow_step'     => 'opening_verified',
                    ]
                );
            }

            DB::commit();
            $this->isVerified = true;

            // Mark workflow step as completed (skip for super admins)
            if (!is_super_admin() && !can_access_all_branches()) {
                $workflowService = app(SalesWorkflowService::class);
                $workflowService->completeStep(
                    auth()->id(),
                    $this->currentShiftId,
                    'stock_opening'
                );
            }

            $this->toast()->success('Stock opening completed! Redirecting to POS...')->send();

            // Auto-redirect to POS with department context
            $this->redirectToPos();

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

        $count = ProductStock::query()
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id'), function ($q) {
                $q->where('department_id', $this->departmentId);
            })
            ->where('stock_date', $this->stockDate)
            ->where('shift_type', $this->shiftType)
            ->count();

        $this->isVerified = $count > 0;
        return $this->isVerified;
    }

    protected function getFilteredQuery()
    {
        return ProductStock::query()
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('product_stocks', 'department_id'), function ($q) {
                $q->where('department_id', $this->departmentId);
            })
            ->where('stock_date', $this->stockDate)
            ->where('shift_type', $this->shiftType);
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
        $employee = auth()->user();
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
