<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Analytics;

use App\Helpers\Settings;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesShift;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\CurrencyFormattingService;
use App\Traits\Exportable;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use Exportable;

    public $dateFrom;
    public $dateTo;
    public $branchId;
    public $departmentId;
    public $orderType = 'all';
    public $paymentMethod = 'all';
    public $selectedPeriod = 'today';
    
    // Tab management
    public $activeTab = 'overview';
    
    // Real-time refresh
    public $autoRefresh = false;
    
    protected $queryString = [
        'dateFrom',
        'dateTo',
        'orderType',
        'paymentMethod',
        'activeTab'
    ];

    public function mount()
    {
        $this->branchId = auth()->user()->branch_id;
        $this->departmentId = auth()->user()->department_id;
        $this->setDateRange('today');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['dateFrom', 'dateTo'])) {
            $this->validateDateRange();
        }
    }

    protected function validateDateRange()
    {
        if ($this->dateFrom && $this->dateTo) {
            $from = Carbon::parse($this->dateFrom);
            $to = Carbon::parse($this->dateTo);

            if ($from->gt($to)) {
                $this->addError('dateFrom', 'Start date must be before end date');
                return false;
            }

            if ($from->diffInDays($to) > 365) {
                $this->addError('dateFrom', 'Date range cannot exceed 365 days');
                return false;
            }
        }
        return true;
    }

    protected function getCacheKey($suffix)
    {
        return sprintf(
            'analytics_%s_%s_%s_%s_%s_%s',
            $this->branchId,
            $this->departmentId,
            $this->dateFrom,
            $this->dateTo,
            $this->orderType,
            $suffix
        );
    }

    public function setDateRange($period)
    {
        $this->selectedPeriod = $period;
        
        switch ($period) {
            case 'today':
                $this->dateFrom = now()->startOfDay()->format('Y-m-d H:i:s');
                $this->dateTo = now()->endOfDay()->format('Y-m-d H:i:s');
                break;
            case 'yesterday':
                $this->dateFrom = now()->subDay()->startOfDay()->format('Y-m-d H:i:s');
                $this->dateTo = now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
                break;
            case 'this_week':
                $this->dateFrom = now()->startOfWeek()->format('Y-m-d H:i:s');
                $this->dateTo = now()->endOfWeek()->format('Y-m-d H:i:s');
                break;
            case 'last_week':
                $this->dateFrom = now()->subWeek()->startOfWeek()->format('Y-m-d H:i:s');
                $this->dateTo = now()->subWeek()->endOfWeek()->format('Y-m-d H:i:s');
                break;
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d H:i:s');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d H:i:s');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');
                break;
        }
    }

    #[Computed]
    public function salesOverview()
    {
        return Cache::remember($this->getCacheKey('overview'), 300, function() {
            $query = Sale::query()
                ->where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                ->where('status', '!=', 'cancelled');

            if ($this->orderType !== 'all') {
                $query->where('order_type', $this->orderType);
            }

            $sales = $query->get();

            // Cancelled sales for tracking
            $cancelledSales = Sale::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                ->where('status', 'cancelled')
                ->get();

            // Calculate core metrics
            $totalSales = $sales->sum('total');
            $totalOrders = $sales->count();
            $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
            $totalDiscount = $sales->sum('discount');
            $totalTax = $sales->sum('tax');
            $subtotal = $sales->sum('subtotal');

            // Calculate net revenue (total - discounts)
            $netRevenue = $totalSales - $totalDiscount;

            // Calculate refunds/cancellations
            $totalRefunds = $cancelledSales->sum('total');
            $refundCount = $cancelledSales->count();

            // Calculate actual revenue after refunds
            $actualRevenue = $netRevenue - $totalRefunds;

            // Previous period comparison
            $periodDiff = Carbon::parse($this->dateTo)->diffInDays(Carbon::parse($this->dateFrom));
            $prevFrom = Carbon::parse($this->dateFrom)->subDays($periodDiff + 1)->format('Y-m-d H:i:s');
            $prevTo = Carbon::parse($this->dateFrom)->subSecond()->format('Y-m-d H:i:s');

            $prevQuery = Sale::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$prevFrom, $prevTo])
                ->where('status', '!=', 'cancelled');

            if ($this->orderType !== 'all') {
                $prevQuery->where('order_type', $this->orderType);
            }

            $prevSales = $prevQuery->sum('total');
            $prevOrders = $prevQuery->count();

            $growthRate = $prevSales > 0 ? (($totalSales - $prevSales) / $prevSales) * 100 : 0;
            $orderGrowthRate = $prevOrders > 0 ? (($totalOrders - $prevOrders) / $prevOrders) * 100 : 0;

            return [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'avg_order_value' => $avgOrderValue,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'subtotal' => $subtotal,
                'net_revenue' => $netRevenue,
                'total_refunds' => $totalRefunds,
                'refund_count' => $refundCount,
                'actual_revenue' => $actualRevenue,
                'growth_rate' => $growthRate,
                'order_growth_rate' => $orderGrowthRate,
                'prev_sales' => $prevSales,
                'prev_orders' => $prevOrders,
                'refund_rate' => $totalOrders > 0 ? ($refundCount / ($totalOrders + $refundCount)) * 100 : 0,
                'discount_rate' => $subtotal > 0 ? ($totalDiscount / $subtotal) * 100 : 0,
            ];
        });
    }

    #[Computed]
    public function paymentBreakdown()
    {
        return Cache::remember($this->getCacheKey('payments'), 300, function() {
            $query = Payment::whereHas('sale', function($q) {
                $q->where('branch_id', $this->branchId)
                  ->where('department_id', $this->departmentId)
                  ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                  ->where('status', '!=', 'cancelled');
            })->where('status', 'completed');

            if ($this->paymentMethod !== 'all') {
                $query->where('payment_method', $this->paymentMethod);
            }

            return $query->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->get();
        });
    }

    #[Computed]
    public function orderTypeBreakdown()
    {
        return Cache::remember($this->getCacheKey('order_types'), 300, function() {
            return Sale::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                ->where('status', '!=', 'cancelled')
                ->select('order_type', DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('order_type')
                ->get();
        });
    }

    #[Computed]
    public function topSellingProducts()
    {
        return Cache::remember($this->getCacheKey('top_products'), 300, function() {
            return SaleItem::whereHas('sale', function($q) {
                $q->where('branch_id', $this->branchId)
                  ->where('department_id', $this->departmentId)
                  ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                  ->where('status', '!=', 'cancelled');
            })
            ->with('product')
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_id) as order_count')
            )
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
        });
    }

    #[Computed]
    public function hourlySalesData()
    {
        return Cache::remember($this->getCacheKey('hourly_sales'), 300, function() {
            return Sale::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                ->where('status', '!=', 'cancelled')
                ->select(
                    DB::raw('HOUR(sale_time) as hour'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        });
    }

    #[Computed]
    public function dailySalesData()
    {
        return Cache::remember($this->getCacheKey('daily_sales'), 300, function() {
            return Sale::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                ->where('status', '!=', 'cancelled')
                ->select(
                    DB::raw('DATE(sale_time) as date'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(total) as avg_order')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });
    }

    #[Computed]
    public function shiftPerformance()
    {
        return Cache::remember($this->getCacheKey('shifts'), 300, function() {
            return SalesShift::where('branch_id', $this->branchId)
                ->where('department_id', $this->departmentId)
                ->whereBetween('shift_date', [
                    Carbon::parse($this->dateFrom)->format('Y-m-d'),
                    Carbon::parse($this->dateTo)->format('Y-m-d')
                ])
                ->with(['employee', 'sales'])
                ->get()
                ->map(function($shift) {
                    $totalSales = $shift->sales->sum('total');
                    $totalOrders = $shift->sales->count();

                    return [
                        'shift_number' => $shift->shift_number,
                        'shift_type' => $shift->shift_type,
                        'shift_date' => $shift->shift_date,
                        'employee_name' => $shift->employee->name ?? 'N/A',
                        'total_sales' => $totalSales,
                        'total_orders' => $totalOrders,
                        'opening_cash' => $shift->opening_cash,
                        'closing_cash' => $shift->closing_cash,
                        'cash_variance' => $shift->cash_variance,
                        'status' => $shift->status
                    ];
                });
        });
    }

    #[Computed]
    public function categorySales()
    {
        return Cache::remember($this->getCacheKey('categories'), 300, function() {
            return SaleItem::whereHas('sale', function($q) {
                $q->where('branch_id', $this->branchId)
                  ->where('department_id', $this->departmentId)
                  ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                  ->where('status', '!=', 'cancelled');
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('product_types', 'products.product_type_id', '=', 'product_types.id')
            ->where('product_types.department_id', $this->departmentId) // Filter by department
            ->select(
                'product_types.id',
                'product_types.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as order_count'),
                DB::raw('COUNT(DISTINCT sale_items.product_id) as product_count')
            )
            ->groupBy('product_types.id', 'product_types.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        });
    }

    #[Computed]
    public function profitAnalysis()
    {
        return Cache::remember($this->getCacheKey('profit'), 300, function() {
            $items = SaleItem::whereHas('sale', function($q) {
                $q->where('branch_id', $this->branchId)
                  ->where('department_id', $this->departmentId)
                  ->whereBetween('sale_time', [$this->dateFrom, $this->dateTo])
                  ->where('status', '!=', 'cancelled');
            })
            ->with('product')
            ->get();

            $totalRevenue = $items->sum('total');
            $totalCost = $items->sum(function($item) {
                return ($item->product->cost_price ?? 0) * $item->quantity;
            });

            $grossProfit = $totalRevenue - $totalCost;
            $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

            return [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'gross_margin' => $grossMargin,
            ];
        });
    }

    public function refreshData()
    {
        // Clear all caches
        $suffixes = ['overview', 'payments', 'order_types', 'top_products', 'hourly_sales', 'daily_sales', 'shifts', 'categories', 'profit'];
        foreach ($suffixes as $suffix) {
            Cache::forget($this->getCacheKey($suffix));
        }

        // Reset computed properties
        unset($this->salesOverview);
        unset($this->paymentBreakdown);
        unset($this->orderTypeBreakdown);
        unset($this->topSellingProducts);
        unset($this->hourlySalesData);
        unset($this->dailySalesData);
        unset($this->shiftPerformance);
        unset($this->categorySales);
        unset($this->profitAnalysis);

        $this->dispatch('refresh-charts');
        $this->dispatch('notify', ['message' => 'Data refreshed successfully', 'type' => 'success']);
    }

    public function exportData($format = 'csv')
    {
        try {
            $data = $this->prepareExportData();

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($data);
                case 'excel':
                    return $this->exportToExcel($data);
                case 'pdf':
                    return $this->exportToPDF($data);
                default:
                    $this->dispatch('notify', ['message' => 'Invalid export format', 'type' => 'error']);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Export failed: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    protected function prepareExportData()
    {
        return [
            'overview' => $this->salesOverview,
            'payments' => $this->paymentBreakdown->toArray(),
            'order_types' => $this->orderTypeBreakdown->toArray(),
            'top_products' => $this->topSellingProducts->toArray(),
            'hourly_sales' => $this->hourlySalesData->toArray(),
            'daily_sales' => $this->dailySalesData->toArray(),
            'shifts' => $this->shiftPerformance->toArray(),
            'categories' => $this->categorySales->toArray(),
            'profit' => $this->profitAnalysis,
            'period' => [
                'from' => $this->dateFrom,
                'to' => $this->dateTo,
                'period' => $this->selectedPeriod
            ]
        ];
    }

    protected function exportToCSV($data)
    {
        $filename = 'sales_analytics_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Write overview section
        fputcsv($handle, ['Sales Analytics Report']);
        fputcsv($handle, ['Period', Carbon::parse($data['period']['from'])->format('Y-m-d H:i') . ' to ' . Carbon::parse($data['period']['to'])->format('Y-m-d H:i')]);
        fputcsv($handle, []);

        // Overview metrics
        fputcsv($handle, ['Overview Metrics']);
        fputcsv($handle, ['Metric', 'Value']);
        foreach ($data['overview'] as $key => $value) {
            fputcsv($handle, [ucwords(str_replace('_', ' ', $key)), is_numeric($value) ? number_format($value, 2) : $value]);
        }
        fputcsv($handle, []);

        // Top Products
        fputcsv($handle, ['Top Selling Products']);
        fputcsv($handle, ['Product', 'Quantity', 'Revenue', 'Orders']);
        foreach ($data['top_products'] as $product) {
            fputcsv($handle, [
                $product['product']['name'] ?? 'N/A',
                $product['total_quantity'],
                $product['total_revenue'],
                $product['order_count']
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::streamDownload(function() use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function exportToExcel($data)
    {
        return $this->export(
            'sales-analytics-' . now()->format('Y-m-d'),
            collect($data),
            'exports.sales.analytics',
            'excel'
        );
    }

    protected function exportToPDF($data)
    {
        return $this->export(
            'sales-analytics-' . now()->format('Y-m-d'),
            collect($data),
            'exports.sales.analytics',
            'pdf',
            false,
            ['orientation' => 'portrait', 'paper' => 'A4']
        );
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.analytics.index', [
            'overview' => $this->salesOverview,
            'payments' => $this->paymentBreakdown,
            'orderTypes' => $this->orderTypeBreakdown,
            'topProducts' => $this->topSellingProducts,
            'hourlySales' => $this->hourlySalesData,
            'dailySales' => $this->dailySalesData,
            'shifts' => $this->shiftPerformance,
            'categories' => $this->categorySales,
            'profit' => $this->profitAnalysis
        ]);
    }

    /**
     * Format currency value for display
     */
    protected function formatCurrency(float $amount): string
    {
        $service = new CurrencyFormattingService();
        return $service->format($amount);
    }

    /**
     * Get currency symbol
     */
    protected function getCurrencySymbol(?string $currency = null): string
    {
        $service = new CurrencyFormattingService();
        $currency = $currency ?? Settings::currencyLocalization('primary_currency', 'NGN');
        return $service->getSymbol($currency);
    }

    /**
     * Format percentage for display
     */
    protected function formatPercentage(float $value, int $decimals = 2): string
    {
        $service = new CurrencyFormattingService();
        return $service->formatPercentage($value, $decimals);
    }
}