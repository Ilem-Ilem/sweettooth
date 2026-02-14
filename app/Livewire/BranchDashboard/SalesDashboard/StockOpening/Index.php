<?php
namespace App\Livewire\BranchDashboard\SalesDashboard\StockOpening;

use App\Livewire\BaseComponent;
use App\Livewire\Concerns\SalesDepartmentContext;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductDispatch;
use App\Models\ProductStock;
use App\Models\ProductType;
use App\Models\Shift;
use App\Services\SalesWorkflowService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

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
    public array $rows = [];
    public array $unclosedProducts = [];
    public bool $isVerified = false;
    public ?string $currentShiftId = null;
    public string $shiftType = 'morning';
    public $stockDate;
    public $availableShifts = [];
    public $selectedShiftForViewing = null;
    public array $productTypes = [];

    public ?ProductStock $selectedStockItem = null;
    private ?bool $productStocksHasDepartmentColumn = null;
    private ?array $cachedSalesDepartmentIds = null;
    private ?string $cachedSalesDepartmentSignature = null;

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
        return $this->b_id ?: $this->branchId ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->mountBase();
        $this->initializeDepartmentContext(); // Using trait method
        $this->departmentName = $this->departmentName ?: 'Stock Opening';
        $this->stockDate = Carbon::today()->format('Y-m-d');
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
        $salesDepartmentIds = $this->resolveEquivalentSalesDepartmentIds();
        if (empty($salesDepartmentIds)) {
            $this->availableShifts = [];

            return;
        }

        // Get shifts from last 30 days for the sales department
        $this->availableShifts = Shift::query()
            ->where('branch_id', $this->getBranchId())
            ->whereIn('department_id', $salesDepartmentIds)
            ->where('shift_date', '>=', Carbon::today()->subDays(30))
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
            ->where('shift_date', Carbon::today())
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
        $stockDate = Carbon::parse($this->stockDate)->toDateString();
        $yesterday = Carbon::parse($stockDate)->subDay()->toDateString();
        $salesDepartmentIds = $this->resolveEquivalentSalesDepartmentIds();
        $this->loadProductTypes($salesDepartmentIds);

        if (empty($salesDepartmentIds)) {
            $this->stockOpenings = [];
            $this->rows = [];
            $this->unclosedProducts = [];
            $this->isVerified = false;

            return;
        }
        $primarySalesDepartmentId = $this->resolvePrimarySalesDepartmentId($salesDepartmentIds);
        $hasDepartmentColumn = $this->hasProductStocksDepartmentColumn();

        $products = Product::query()
            ->active()
            ->whereIn('sales_department_id', $salesDepartmentIds)
            ->select(['id', 'name', 'sku', 'uom_id', 'product_type_id', 'shelf_life_days'])
            ->with(['unitOfMeasure:id,symbol'])
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

        if ($products->isEmpty()) {
            $this->stockOpenings = [];
            $this->rows = [];
            $this->unclosedProducts = [];
            $this->isVerified = $this->hasVerifiedStockOpening($salesDepartmentIds);

            return;
        }

        $productIds = $products->pluck('id')
            ->map(static fn ($id): string => (string) $id)
            ->values()
            ->all();

        $stockSelect = [
            'id',
            'product_id',
            'stock_date',
            'shift_type',
            'opening_quantity',
            'closing_quantity',
            'production_date',
            'expiry_date',
            'notes',
        ];
        if ($hasDepartmentColumn) {
            $stockSelect[] = 'department_id';
        }

        $yesterdayStocksQuery = ProductStock::query()
            ->select($stockSelect)
            ->whereIn('product_id', $productIds)
            ->where('stock_date', $yesterday)
            ->where('shift_type', $this->shiftType)
            ->orderBy('product_id');

        $todayStocksQuery = ProductStock::query()
            ->select($stockSelect)
            ->whereIn('product_id', $productIds)
            ->where('stock_date', $stockDate)
            ->where('shift_type', $this->shiftType)
            ->orderBy('product_id');

        if ($hasDepartmentColumn) {
            $yesterdayStocksQuery->whereIn('department_id', $salesDepartmentIds);
            $todayStocksQuery->whereIn('department_id', $salesDepartmentIds);

            if ($primarySalesDepartmentId !== null) {
                $yesterdayStocksQuery->orderByRaw('department_id = ? DESC', [$primarySalesDepartmentId]);
                $todayStocksQuery->orderByRaw('department_id = ? DESC', [$primarySalesDepartmentId]);
            }
        }

        $yesterdayStockByProduct = $this->buildPreferredStockMap(
            $yesterdayStocksQuery->orderByDesc('id')->get(),
            $primarySalesDepartmentId,
            $hasDepartmentColumn
        );
        $todayStockByProduct = $this->buildPreferredStockMap(
            $todayStocksQuery->orderByDesc('id')->get(),
            $primarySalesDepartmentId,
            $hasDepartmentColumn
        );

        $missingYesterdayStockProductIds = array_values(array_diff($productIds, array_keys($yesterdayStockByProduct)));
        $lastStockByProduct = [];

        if (! empty($missingYesterdayStockProductIds)) {
            $lastStocksQuery = ProductStock::query()
                ->select($stockSelect)
                ->whereIn('product_id', $missingYesterdayStockProductIds)
                ->where('stock_date', '<', $stockDate)
                ->where('shift_type', $this->shiftType)
                ->orderBy('product_id')
                ->orderBy('stock_date', 'desc');

            if ($hasDepartmentColumn) {
                $lastStocksQuery->whereIn('department_id', $salesDepartmentIds);
                if ($primarySalesDepartmentId !== null) {
                    $lastStocksQuery->orderByRaw('department_id = ? DESC', [$primarySalesDepartmentId]);
                }
            }

            $lastStockByProduct = $this->buildPreferredStockMap(
                $lastStocksQuery->orderByDesc('id')->get(),
                $primarySalesDepartmentId,
                $hasDepartmentColumn
            );
        }

        $dispatchStart = Carbon::parse($stockDate)->startOfDay();
        $dispatchEnd = (clone $dispatchStart)->addDay();

        $dispatchTotals = ProductDispatch::query()
            ->whereIn('product_id', $productIds)
            ->where('status', 'received')
            ->whereIn('sales_department_id', $salesDepartmentIds)
            ->where('received_at', '>=', $dispatchStart)
            ->where('received_at', '<', $dispatchEnd)
            ->selectRaw('product_id, SUM(COALESCE(received_quantity, quantity, 0)) as total_received, COUNT(*) as dispatch_count')
            ->groupBy('product_id')
            ->get();
        $dispatchSummaryByProduct = [];
        foreach ($dispatchTotals as $dispatchTotal) {
            $dispatchSummaryByProduct[(string) $dispatchTotal->product_id] = [
                'total' => (float) $dispatchTotal->total_received,
                'dispatch_count' => (int) $dispatchTotal->dispatch_count,
            ];
        }

        $stockOpenings = [];
        $unclosedProducts = [];

        foreach ($products as $product) {
            $productId = (string) $product->id;
            $yesterdayStock = $yesterdayStockByProduct[$productId] ?? null;
            $todayStock = $todayStockByProduct[$productId] ?? null;

            if (! $yesterdayStock) {
                $lastStock = $lastStockByProduct[$productId] ?? null;

                if ($lastStock !== null && (float) $lastStock->closing_quantity > 0) {
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

            $dispatchSummary = $dispatchSummaryByProduct[$productId] ?? [
                'total' => 0.0,
                'dispatch_count' => 0,
            ];
            $todayAdditions = (float) $dispatchSummary['total'];
            $dispatchCount = (int) $dispatchSummary['dispatch_count'];

            $yesterdayClosing = $yesterdayStock ? $yesterdayStock->closing_quantity : 0;
            $expectedOpening  = $yesterdayClosing + $todayAdditions;
            $actualOpening = $todayStock ? $todayStock->opening_quantity : $expectedOpening;
            $variance = $actualOpening - $expectedOpening;

            $varianceSource = 'None';
            if ($variance !== 0.0) {
                $varianceSource = 'Stock count difference vs expected opening (' . $yesterday . ' + production)';
            }

            $stockOpenings[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_sku'       => $product->sku,
                'product_uom'       => $product->unitOfMeasure?->symbol,
                'yesterday_closing' => $yesterdayClosing,
                'today_additions'   => $todayAdditions,
                'dispatch_count'    => $dispatchCount,
                'expected_opening'  => $expectedOpening,
                'actual_opening'    => $actualOpening,
                'variance'          => $variance,
                'variance_source'   => $varianceSource,
                'production_date'   => $todayStock ? $todayStock->production_date?->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                'expiry_date'       => $todayStock ? $todayStock->expiry_date?->format('Y-m-d') : null,
                'shelf_life_days'   => $product->shelf_life_days,
                'notes'             => $todayStock ? $todayStock->notes : '',
                'is_saved'          => $todayStock !== null,
            ];
        }

        $this->stockOpenings = $stockOpenings;
        $this->rows = array_map(static function (array $stock, int $index): object {
            $stock['index'] = $index;

            return (object) $stock;
        }, $stockOpenings, array_keys($stockOpenings));
        $this->unclosedProducts = $unclosedProducts;
        $this->isVerified = $this->hasVerifiedStockOpening($salesDepartmentIds);
    }

    /**
     * @param  Collection<int, ProductStock>  $stocks
     * @return array<string, ProductStock>
     */
    private function buildPreferredStockMap(Collection $stocks, ?int $primarySalesDepartmentId, bool $hasDepartmentColumn): array
    {
        $map = [];

        foreach ($stocks as $stock) {
            $productId = (string) $stock->product_id;
            if (! isset($map[$productId])) {
                $map[$productId] = $stock;

                continue;
            }

            if (! $hasDepartmentColumn || $primarySalesDepartmentId === null) {
                continue;
            }

            $existingDate = $map[$productId]->stock_date?->toDateString()
                ?? (string) ($map[$productId]->stock_date ?? '');
            $candidateDate = $stock->stock_date?->toDateString()
                ?? (string) ($stock->stock_date ?? '');
            if ($existingDate !== '' && $candidateDate !== '' && $existingDate !== $candidateDate) {
                continue;
            }

            if ((int) ($map[$productId]->department_id ?? 0) === $primarySalesDepartmentId) {
                continue;
            }

            if ((int) ($stock->department_id ?? 0) === $primarySalesDepartmentId) {
                $map[$productId] = $stock;
            }
        }

        return $map;
    }

    /**
     * @param  array<int>  $salesDepartmentIds
     */
    private function hasVerifiedStockOpening(array $salesDepartmentIds): bool
    {
        if (! $this->currentShiftId || empty($salesDepartmentIds)) {
            return false;
        }

        $query = ProductStock::query()
            ->where('stock_date', $this->stockDate)
            ->where('shift_type', $this->shiftType);

        if ($this->hasProductStocksDepartmentColumn()) {
            $query->whereIn('department_id', $salesDepartmentIds);
        }

        return $query->exists();
    }

    private function hasProductStocksDepartmentColumn(): bool
    {
        if ($this->productStocksHasDepartmentColumn === null) {
            $this->productStocksHasDepartmentColumn = Schema::hasColumn('product_stocks', 'department_id');
        }

        return $this->productStocksHasDepartmentColumn;
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

            $shelfLifeDays = (int) ($this->stockOpenings[$index]['shelf_life_days'] ?? 0);
            if ($shelfLifeDays > 0 && ! empty($value)) {
                $productionDate = Carbon::parse($value);
                $this->stockOpenings[$index]['expiry_date'] =
                    $productionDate->copy()->addDays($shelfLifeDays)->format('Y-m-d');
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

        $salesDepartmentIds = $this->resolveEquivalentSalesDepartmentIds();
        $primarySalesDepartmentId = $this->resolvePrimarySalesDepartmentId($salesDepartmentIds);
        if (empty($salesDepartmentIds) || $primarySalesDepartmentId === null) {
            $this->toast()->error('Sales department context is missing. Refresh and try again.')->send();

            return;
        }

        DB::beginTransaction();
        try {
            $hasDepartmentColumn = $this->hasProductStocksDepartmentColumn();

            foreach ($this->stockOpenings as $stockOpening) {
                // Use shift_id from shifts table (sales department shift)
                // sales_shift_id can be null since we're using the general shifts table
                // addition_quantity represents the total quantity yield (approved quantity sent from production)
                $lookup = [
                    'product_id'     => $stockOpening['product_id'],
                    'stock_date'     => $this->stockDate,
                    'shift_type'     => $this->shiftType,
                ];

                if ($hasDepartmentColumn) {
                    $lookup['department_id'] = $primarySalesDepartmentId;
                }

                ProductStock::updateOrCreate(
                    $lookup,
                    [
                        'sales_shift_id'    => null, // Nullable - we use shifts table instead
                        'department_id'     => $hasDepartmentColumn
                            ? $primarySalesDepartmentId
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
        $salesDepartmentIds = $this->resolveEquivalentSalesDepartmentIds();
        $this->isVerified = $this->hasVerifiedStockOpening($salesDepartmentIds);

        return $this->isVerified;
    }

    protected function getFilteredQuery()
    {
        $salesDepartmentIds = $this->resolveEquivalentSalesDepartmentIds();
        $query = ProductStock::query()
            ->where('stock_date', $this->stockDate)
            ->where('shift_type', $this->shiftType);

        if (! $this->hasProductStocksDepartmentColumn()) {
            return $query;
        }

        if (empty($salesDepartmentIds)) {
            $query->whereRaw('1 = 0');

            return $query;
        }

        return $query->whereIn('department_id', $salesDepartmentIds);
    }

    public function updatedSearch()
    {
        $search = trim((string) $this->search);
        if ($search !== '' && strlen($search) < 2) {
            return;
        }

        $this->loadStockOpeningData();
    }

    public function updatedFilterProductType()
    {
        $this->loadStockOpeningData();
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.stock-opening.index', [
            'productTypes' => $this->productTypes,
            'rows' => $this->rows,
            'stockOpenings' => $this->stockOpenings,
        ]);
    }

    /**
     * @param array<int> $salesDepartmentIds
     */
    private function loadProductTypes(array $salesDepartmentIds): void
    {
        if (empty($salesDepartmentIds)) {
            $this->productTypes = [];

            return;
        }

        $this->productTypes = ProductType::query()
            ->whereIn('id', Product::query()
                ->whereIn('sales_department_id', $salesDepartmentIds)
                ->select('product_type_id')
                ->distinct())
            ->active()
            ->ordered()
            ->get(['id', 'name'])
            ->map(static fn (ProductType $type): array => [
                'id' => (int) $type->id,
                'name' => (string) $type->name,
            ])
            ->all();
    }

    /**
     * Resolve equivalent sales department IDs from slug in branch/global scope.
     *
     * @return array<int>
     */
    private function resolveEquivalentSalesDepartmentIds(): array
    {
        $signature = implode('|', [
            (string) ($this->salesDeptSlug ?? ''),
            (string) ($this->departmentId ?? ''),
            (string) ($this->getBranchId() ?? ''),
        ]);
        if ($this->cachedSalesDepartmentSignature === $signature && $this->cachedSalesDepartmentIds !== null) {
            return $this->cachedSalesDepartmentIds;
        }

        if (! $this->departmentId && ! $this->salesDeptSlug) {
            $this->cachedSalesDepartmentSignature = $signature;
            $this->cachedSalesDepartmentIds = [];

            return $this->cachedSalesDepartmentIds;
        }

        $branchId = $this->getBranchId();
        $query = Department::query()
            ->whereHas('category', function ($categoryQuery) {
                $categoryQuery->whereRaw('LOWER(name) = ?', ['sales']);
            });

        if ($this->salesDeptSlug) {
            $query->where('slug', $this->salesDeptSlug);
        } elseif ($this->departmentId) {
            $query->where('id', (int) $this->departmentId);
        }

        if ($branchId) {
            $query->where(function ($scopeQuery) use ($branchId) {
                $scopeQuery->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            });
        }

        $ids = $query->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (! empty($ids)) {
            $this->cachedSalesDepartmentSignature = $signature;
            $this->cachedSalesDepartmentIds = $ids;

            return $this->cachedSalesDepartmentIds;
        }

        // Strict when slug is provided: do not fall back to unrelated department IDs.
        if ($this->salesDeptSlug) {
            $this->cachedSalesDepartmentSignature = $signature;
            $this->cachedSalesDepartmentIds = [];

            return $this->cachedSalesDepartmentIds;
        }

        $this->cachedSalesDepartmentSignature = $signature;
        $this->cachedSalesDepartmentIds = $this->departmentId ? [(int) $this->departmentId] : [];

        return $this->cachedSalesDepartmentIds;
    }

    /**
     * @param  array<int>  $departmentIds
     */
    private function resolvePrimarySalesDepartmentId(array $departmentIds): ?int
    {
        $departmentIds = array_values(array_unique(array_map(static fn ($id) => (int) $id, $departmentIds)));
        if (empty($departmentIds)) {
            return null;
        }

        if ($this->departmentId && in_array((int) $this->departmentId, $departmentIds, true)) {
            return (int) $this->departmentId;
        }

        return $departmentIds[0] ?? null;
    }
}
