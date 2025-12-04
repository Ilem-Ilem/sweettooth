<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\ShiftClosing;

use App\Livewire\BaseComponent;
use App\Models\Shift;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Product;
use App\Models\Callback;
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

    #[Url(keep: true)]
    public ?string $salesDeptSlug = null;

    public ?string $branchId = null;
    public ?int $departmentId = null;
    public string $departmentName = 'Shift Closing';
    public string $branchName = '';
    public ?string $currentShiftId = null;
    public $shiftDate;
    public $shiftType = 'morning';

    // Closing data
    public array $closingStocks = [];
    public array $cashReconciliation = [];
    public array $salesSummary = [];
    public bool $isVerified = false;
    public $notes = '';

    // Cash reconciliation fields
    public $actualCash = 0;
    public $actualPos = 0;
    public $actualTransfer = 0;
    public $cashVarianceReason = '';
    public $posVarianceReason = '';
    public $transferVarianceReason = '';

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
        $this->loadBranchAndDepartment();
        $this->shiftDate = Carbon::today()->format('Y-m-d');
        $this->loadCurrentShift();
        $this->loadClosingData();
    }

    protected function loadBranchAndDepartment(): void
    {
        $this->branchId = request('b_id');
        if ($this->branchId) {
            $branch = Branch::find($this->branchId);
            $this->branchName = $branch?->name ?? 'Unknown Branch';
        }

        if ($this->salesDeptSlug) {
            $department = Department::where('slug', $this->salesDeptSlug)
                ->where('branch_id', $this->branchId)
                ->first();

            if (!$department) {
                $department = Department::where('slug', $this->salesDeptSlug)
                    ->whereNull('branch_id')
                    ->first();
            }

            if ($department) {
                $this->departmentId = $department->id;
                $this->departmentName = $department->name;
            }
        }
    }

    protected function loadCurrentShift()
    {
        $employee = auth('employees')->user();

        // Get active shift for this sales department
        $activeShift = Shift::where('employee_id', $employee->id)
            ->where('department_id', $this->departmentId)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            $this->currentShiftId = $activeShift->id;
            $this->shiftType = $activeShift->shift_type ?? 'morning';
        }
    }

    /**
     * Load all closing data
     *
     * TODO: This should be broken into separate methods:
     * - loadClosingStocks() - Load product stock closing
     * - loadSalesSummary() - Summarize all sales for the shift
     * - loadCashReconciliation() - Calculate expected vs actual cash
     * - loadPaymentSummary() - Break down by payment method
     * - loadExpiredProducts() - List products nearing expiry
     * - loadCallbacks() - List wastage/expired items
     */
    public function loadClosingData()
    {
        if (!$this->currentShiftId) {
            return;
        }

        $this->loadClosingStocks();
        $this->loadSalesSummary();
        $this->loadCashReconciliation();
    }

    /**
     * Load closing stock data with sold quantities, variances, and expiry flags
     */
    protected function loadClosingStocks()
    {
        $shift = Shift::find($this->currentShiftId);
        if (!$shift) return;

        $stocks = ProductStock::where('stock_date', $this->shiftDate)
            ->where('shift_type', $this->shiftType)
            ->whereHas('product', function($q) {
                $q->whereHas('departments', function($dq) {
                    $dq->where('department_id', $this->departmentId);
                });
            })
            ->with('product')
            ->get();

        $closingStocks = [];
        foreach ($stocks as $stock) {
            // Calculate sold quantity from SaleItems
            $soldQuantity = SaleItem::whereHas('sale', function($q) use ($shift) {
                $q->where('branch_id', $this->branchId)
                  ->where('department_id', $this->departmentId)
                  ->whereDate('sale_time', $this->shiftDate)
                  ->where('status', '!=', 'cancelled');
            })
            ->where('product_id', $stock->product_id)
            ->sum('quantity');

            // Calculate expected closing: opening + additions - sold
            $expectedClosing = ($stock->opening_quantity + $stock->addition_quantity) - $soldQuantity;

            // Actual closing (can be manually entered, defaults to expected)
            $actualClosing = $stock->closing_quantity ?? $expectedClosing;

            // Calculate variance
            $variance = $actualClosing - $expectedClosing;

            // Check if product is expired or near expiry
            $isExpired = false;
            $daysToExpiry = null;
            if ($stock->expiry_date) {
                $expiryDate = Carbon::parse($stock->expiry_date);
                $daysToExpiry = Carbon::now()->diffInDays($expiryDate, false);
                $isExpired = $daysToExpiry <= 0;
            }

            $closingStocks[] = [
                'product_id' => $stock->product_id,
                'product_name' => $stock->product->name ?? 'N/A',
                'product_uom' => $stock->product->uom ?? 'units',
                'opening_quantity' => $stock->opening_quantity,
                'addition_quantity' => $stock->addition_quantity,
                'sold_quantity' => $soldQuantity,
                'expected_closing' => $expectedClosing,
                'actual_closing' => $actualClosing,
                'variance' => $variance,
                'expiry_date' => $stock->expiry_date?->format('Y-m-d'),
                'is_expired' => $isExpired,
                'days_to_expiry' => $daysToExpiry,
                'notes' => $stock->notes ?? '',
            ];
        }

        $this->closingStocks = $closingStocks;
    }

    /**
     * Load comprehensive sales summary including payment methods, top products, and order types
     */
    protected function loadSalesSummary()
    {
        $shift = Shift::find($this->currentShiftId);
        if (!$shift) return;

        $sales = Sale::where('branch_id', $this->branchId)
            ->where('department_id', $this->departmentId)
            ->whereBetween('sale_time', [
                $shift->clock_in ?? $shift->shift_date->startOfDay(),
                now()
            ])
            ->where('status', '!=', 'cancelled')
            ->with(['saleItems.product', 'payments'])
            ->get();

        // Basic sales summary
        $totalSales = $sales->sum('total');
        $totalOrders = $sales->count();
        $totalDiscount = $sales->sum('discount');
        $totalTax = $sales->sum('tax');

        // Calculate cancelled/refunded orders
        $cancelledOrders = Sale::where('branch_id', $this->branchId)
            ->where('department_id', $this->departmentId)
            ->whereBetween('sale_time', [
                $shift->clock_in ?? $shift->shift_date->startOfDay(),
                now()
            ])
            ->where('status', 'cancelled')
            ->count();

        // Payment method breakdown
        $paymentBreakdown = [];
        foreach ($sales as $sale) {
            foreach ($sale->payments as $payment) {
                if ($payment->status === 'completed') {
                    $method = $payment->payment_method ?? 'unknown';
                    if (!isset($paymentBreakdown[$method])) {
                        $paymentBreakdown[$method] = 0;
                    }
                    $paymentBreakdown[$method] += $payment->amount;
                }
            }
        }

        // Top selling products
        $productSales = [];
        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $productId = $item->product_id;
                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'product_name' => $item->product->name ?? 'Unknown',
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }
                $productSales[$productId]['quantity'] += $item->quantity;
                $productSales[$productId]['revenue'] += $item->total;
            }
        }

        // Sort by quantity and get top 5
        usort($productSales, function($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });
        $topProducts = array_slice($productSales, 0, 5);

        // Order type breakdown
        $orderTypeBreakdown = $sales->groupBy('order_type')->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('total'),
            ];
        })->toArray();

        $this->salesSummary = [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'cancelled_orders' => $cancelledOrders,
            'payment_breakdown' => $paymentBreakdown,
            'top_products' => $topProducts,
            'order_type_breakdown' => $orderTypeBreakdown,
        ];
    }

    /**
     * Load cash reconciliation with variance tracking and threshold alerts
     */
    protected function loadCashReconciliation()
    {
        $shift = Shift::find($this->currentShiftId);
        if (!$shift) return;

        $payments = Payment::whereHas('sale', function($q) use ($shift) {
            $q->where('branch_id', $this->branchId)
              ->where('department_id', $this->departmentId)
              ->whereBetween('sale_time', [
                  $shift->clock_in ?? $shift->shift_date->startOfDay(),
                  now()
              ]);
        })
        ->where('status', 'completed')
        ->get();

        $expectedCash = $payments->where('payment_method', 'cash')->sum('amount');
        $expectedPos = $payments->where('payment_method', 'pos')->sum('amount');
        $expectedTransfer = $payments->where('payment_method', 'transfer')->sum('amount');

        $cashVariance = $this->actualCash - $expectedCash;
        $posVariance = $this->actualPos - $expectedPos;
        $transferVariance = $this->actualTransfer - $expectedTransfer;

        // Variance threshold (₦100 or 1% of expected, whichever is higher)
        $cashThreshold = max(100, $expectedCash * 0.01);
        $posThreshold = max(100, $expectedPos * 0.01);
        $transferThreshold = max(100, $expectedTransfer * 0.01);

        $this->cashReconciliation = [
            'expected_cash' => $expectedCash,
            'expected_pos' => $expectedPos,
            'expected_transfer' => $expectedTransfer,
            'actual_cash' => $this->actualCash,
            'actual_pos' => $this->actualPos,
            'actual_transfer' => $this->actualTransfer,
            'cash_variance' => $cashVariance,
            'pos_variance' => $posVariance,
            'transfer_variance' => $transferVariance,
            'cash_variance_reason' => $this->cashVarianceReason,
            'pos_variance_reason' => $this->posVarianceReason,
            'transfer_variance_reason' => $this->transferVarianceReason,
            'cash_requires_reason' => abs($cashVariance) > $cashThreshold,
            'pos_requires_reason' => abs($posVariance) > $posThreshold,
            'transfer_requires_reason' => abs($transferVariance) > $transferThreshold,
            'total_expected' => $expectedCash + $expectedPos + $expectedTransfer,
            'total_actual' => $this->actualCash + $this->actualPos + $this->actualTransfer,
            'total_variance' => $cashVariance + $posVariance + $transferVariance,
        ];
    }

    /**
     * Update actual closing quantity for a product
     */
    public function updateActualClosing($productId, $value)
    {
        $index = collect($this->closingStocks)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->closingStocks[$index]['actual_closing'] = (float) $value;
            $this->closingStocks[$index]['variance'] =
                $this->closingStocks[$index]['actual_closing'] -
                $this->closingStocks[$index]['expected_closing'];
        }
    }

    /**
     * Update notes for a stock item
     */
    public function updateStockNotes($productId, $value)
    {
        $index = collect($this->closingStocks)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($index !== false) {
            $this->closingStocks[$index]['notes'] = $value;
        }
    }

    /**
     * Reload cash reconciliation when actual amounts change
     */
    public function updated($property)
    {
        if (in_array($property, ['actualCash', 'actualPos', 'actualTransfer'])) {
            $this->loadCashReconciliation();
        }
    }

    /**
     * Complete shift closing with stock updates, callbacks, and cash reconciliation
     */
    public function saveShiftClosing()
    {
        if (!$this->currentShiftId) {
            $this->toast()->error('No active shift found.')->send();
            return;
        }

        // Validate variance reasons are provided when required
        if ($this->cashReconciliation['cash_requires_reason'] && empty($this->cashVarianceReason)) {
            $this->toast()->error('Cash variance reason is required for variances exceeding threshold.')->send();
            return;
        }

        if ($this->cashReconciliation['pos_requires_reason'] && empty($this->posVarianceReason)) {
            $this->toast()->error('POS variance reason is required for variances exceeding threshold.')->send();
            return;
        }

        if ($this->cashReconciliation['transfer_requires_reason'] && empty($this->transferVarianceReason)) {
            $this->toast()->error('Transfer variance reason is required for variances exceeding threshold.')->send();
            return;
        }

        DB::beginTransaction();
        try {
            $shift = Shift::find($this->currentShiftId);
            if (!$shift) {
                throw new \Exception('Shift not found');
            }

            // 1. UPDATE STOCK CLOSING
            foreach ($this->closingStocks as $stockData) {
                $productStock = ProductStock::where('stock_date', $this->shiftDate)
                    ->where('shift_type', $this->shiftType)
                    ->where('product_id', $stockData['product_id'])
                    ->first();

                if ($productStock) {
                    $productStock->closing_quantity = $stockData['actual_closing'];
                    $productStock->quantity_sold = $stockData['sold_quantity'];
                    $productStock->notes = $stockData['notes'];
                    $productStock->save();

                    // Create callback for significant variance
                    if (abs($stockData['variance']) > 0) {
                        Callback::create([
                            'branch_id' => $this->branchId,
                            'department_id' => $this->departmentId,
                            'product_id' => $stockData['product_id'],
                            'quantity' => abs($stockData['variance']),
                            'reason' => $stockData['variance'] < 0 ? 'shortage' : 'excess',
                            'callback_date' => $this->shiftDate,
                            'shift_type' => $this->shiftType,
                            'notes' => $stockData['notes'] . ' | Shift closing variance',
                            'status' => 'pending',
                        ]);
                    }

                    // Mark expired products
                    if ($stockData['is_expired']) {
                        // Create callback for expired items
                        Callback::create([
                            'branch_id' => $this->branchId,
                            'department_id' => $this->departmentId,
                            'product_id' => $stockData['product_id'],
                            'quantity' => $stockData['actual_closing'],
                            'reason' => 'expired',
                            'callback_date' => $this->shiftDate,
                            'shift_type' => $this->shiftType,
                            'notes' => 'Expired on ' . $stockData['expiry_date'],
                            'status' => 'approved',
                        ]);
                    }
                }
            }

            // 2. UPDATE SHIFT WITH CASH RECONCILIATION
            $shift->status = 'closed';
            $shift->clock_out = now(); // Use clock_out instead of closing_time

            // Store reconciliation data and notes together
            $reconciliationData = [
                'user_notes' => $this->notes,
                'cash_variance' => $this->cashReconciliation['total_variance'],
                'reconciliation' => [
                    'cash' => [
                        'expected' => $this->cashReconciliation['expected_cash'],
                        'actual' => $this->actualCash,
                        'variance' => $this->cashReconciliation['cash_variance'],
                        'reason' => $this->cashVarianceReason,
                    ],
                    'pos' => [
                        'expected' => $this->cashReconciliation['expected_pos'],
                        'actual' => $this->actualPos,
                        'variance' => $this->cashReconciliation['pos_variance'],
                        'reason' => $this->posVarianceReason,
                    ],
                    'transfer' => [
                        'expected' => $this->cashReconciliation['expected_transfer'],
                        'actual' => $this->actualTransfer,
                        'variance' => $this->cashReconciliation['transfer_variance'],
                        'reason' => $this->transferVarianceReason,
                    ],
                ],
            ];

            $shift->notes = json_encode($reconciliationData);
            $shift->save();

            DB::commit();
            $this->toast()->success('Shift closed successfully!')->send();
            $this->isVerified = true;
            $this->loadClosingData(); // Reload to reflect changes
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error closing shift: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.shift-closing.index');
    }
}
