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

    public $selectedStockItem;

    // Table headers
    public array $headers = [
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'yesterday_closing', 'label' => 'Yesterday Closing', 'collapsible' => true],
        ['index' => 'today_additions', 'label' => 'Today\'s Additions', 'collapsible' => true],
        ['index' => 'expected_opening', 'label' => 'Expected Opening', 'collapsible' => true],
        ['index' => 'actual_opening', 'label' => 'Actual Opening'],
        ['index' => 'variance', 'label' => 'Variance', 'collapsible' => true],
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
        $this->loadCurrentShift();
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

        // Get all products from the department
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

//         dd($products);

        $stockOpenings = [];

        foreach ($products as $product) {
            // Get yesterday's closing stock
            $yesterdayStock = ProductStock::where('product_id', $product->id)
                ->where('stock_date', $yesterday)
                ->where('shift_type', $this->shiftType)
                ->first();

            // Get today's additions from kitchen dispatches (product_dispatches, not item_dispatches)
            $todayAdditions = 0;
            try {
                $todayAdditions = DB::table('product_dispatches')
                    ->where('product_id', $product->id)
                    ->whereDate('dispatch_date', $this->stockDate)
                    ->where('shift_type', $this->shiftType)
                    ->sum('quantity') ?? 0;
            } catch (\Exception $e) {
                // Table might not exist yet - ignore error
                $todayAdditions = 0;
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

            $stockOpenings[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_sku'       => $product->sku,
                'product_uom'       => $product->uom,
                'yesterday_closing' => $yesterdayClosing,
                'today_additions'   => $todayAdditions,
                'expected_opening'  => $expectedOpening,
                'actual_opening'    => $todayStock ? $todayStock->opening_quantity : $expectedOpening,
                'variance'          => $todayStock ? ($todayStock->opening_quantity - $expectedOpening) : 0,
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
                ProductStock::updateOrCreate(
                    [
                        'sales_shift_id' => $this->currentShiftId,
                        'product_id'     => $stockOpening['product_id'],
                        'stock_date'     => $this->stockDate,
                        'shift_type'     => $this->shiftType,
                    ],
                    [
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
