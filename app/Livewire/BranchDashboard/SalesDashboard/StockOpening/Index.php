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

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use WithPagination, Interactions;

    #[Url(keep: true)]
    public $b_id;

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
        $this->stockDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadAvailableShifts();
        $this->loadCurrentShift();
        $this->loadStockOpeningData();
    }

    /**
     * Load available shifts for date/shift selection
     */
    protected function loadAvailableShifts()
    {
        $employee = auth('employees')->user();

        // Get shifts from last 30 days for the sales department
        $this->availableShifts = Shift::where('branch_id', $this->getBranchId())
            ->where('department_id', $employee->department_id)
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

        $employee  = auth('employees')->user();
        $yesterday = \Carbon\Carbon::parse($this->stockDate)->subDay()->format('Y-m-d');

        // Get ALL products from the sales department
        // We'll show production data where it exists
        $products = Product::whereHas('productType', function ($q) use ($employee) {
            $q->where('department_id', $employee->department_id);
        })
            ->where(function ($q) {
                $q->whereNull('branch_id')
                    ->orWhere('branch_id', $this->getBranchId());
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterProductType, function ($query) {
                $query->where('product_type_id', $this->filterProductType);
            })
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
                        'shifts.shift_type',
                        'shifts.shift_date'
                    )
                    ->get();

                foreach ($productionRecords as $record) {
                    $todayAdditions += $record->quantity_sent_out ?? 0;
                    if ($record->quantity_sent_out > 0) {
                        $additionSources[] = [
                            'batch' => $record->batch_number,
                            'quantity' => $record->quantity_sent_out,
                            'produced' => $record->quantity_produced,
                            'approved' => $record->quantity_approved,
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
            $expectedOpening  = $yesterdayClosing + $todayAdditions;
            $actualOpening = $todayStock ? $todayStock->opening_quantity : $expectedOpening;
            $variance = $actualOpening - $expectedOpening;

            // Determine variance source
            $varianceSource = 'None';
            if ($variance != 0) {
                if ($todayAdditions > 0 && $yesterdayClosing > 0) {
                    // Both sources contributed
                    $varianceSource = 'Previous closing + Production';
                } elseif ($todayAdditions > 0) {
                    // Only production additions
                    $varianceSource = 'Production (' . $this->shiftType . ' shift)';
                } elseif ($yesterdayClosing > 0) {
                    // Only previous closing
                    $varianceSource = 'Previous closing (' . $yesterday . ')';
                } else {
                    $varianceSource = 'Unknown';
                }
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
                ProductStock::updateOrCreate(
                    [
                        'product_id'     => $stockOpening['product_id'],
                        'stock_date'     => $this->stockDate,
                        'shift_type'     => $this->shiftType,
                    ],
                    [
                        'sales_shift_id'    => null, // Make nullable - we use shifts table instead
                        'opening_quantity'  => $stockOpening['actual_opening'],
                        'addition_quantity' => $stockOpening['today_additions'],
                        'production_date'   => $stockOpening['production_date'],
                        'expiry_date'       => $stockOpening['expiry_date'],
                        'notes'             => $stockOpening['notes'],
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
        $employee     = auth('employees')->user();
        $productTypes = \App\Models\ProductType::whereHas('department', function ($q) use ($employee) {
            $q->where('id', $employee->department_id);
        })->active()->ordered()->get();

        return view('livewire.branch-dashboard.sales-dashboard.stock-opening.index', [
            'productTypes' => $productTypes,
            'rows' => $this->rows,
            'stockOpenings' => $this->stockOpenings,
        ]);
    }
}
