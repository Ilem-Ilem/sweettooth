<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Pos;

use App\Livewire\BaseComponent;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Receipt;
use App\Models\SalesShift;
use App\Models\Shift;
use App\Models\Table;
use App\Models\Branch;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\{Layout, Url, Computed};

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    #[Url(keep: true)]
    public ?string $salesDeptSlug = null;

    public ?string $branchId = null;
    public ?int $departmentId = null;
    public string $departmentName = 'POS';
    public string $branchName = '';

    public string $search = '';
    public array $cart = [];
    public float $subtotal = 0.0;
    public float $discount = 0.0;
    public float $tax = 0.0;
    public float $total = 0.0;
    public ?int $activeShiftId = null;
    public float $cashReceived = 0.0;
    public float $changeDue = 0.0;
    public ?int $currentSaleId = null;
    public string $orderType = 'dine-in';
    public array $payments = [];
    public float $paymentTotal = 0.0;
    public float $paymentRemaining = 0.0;

    // Table Management
    public ?int $selectedTableId = null;
    public bool $showTableManagement = false;
    public bool $showTableModal = false;
    public string $newTableNumber = '';
    public string $newTableName = '';
    public int $newTableCapacity = 4;

    protected $rules = [
        'discount' => 'numeric|min:0',
        'cashReceived' => 'numeric|min:0',
        'orderType' => 'in:dine-in,takeaway,delivery',
        'payments.*.method' => 'required|in:cash,transfer,pos',
        'payments.*.amount' => 'numeric|min:0',
    ];

    public function mount(): void
    {
        // dd( auth("employees")->id());
        $this->mountBase();
        $this->loadBranchAndDepartment();
        $this->loadActiveShift(); // Load shift first before checking stock verification

        // Check if stock has been verified for today's shift for this department
        if (!$this->checkStockVerification()) {
            // Ensure we have a department slug before redirecting
            if (!$this->salesDeptSlug) {
                $this->toast()->error('Department not found. Please contact administrator.')->send();
                return;
            }

            // Redirect to stock opening with department slug
            $this->redirectRoute('branch-dashboard.sales-dashboard.stock-opening.index', [
                'salesDeptSlug' => $this->salesDeptSlug,
                'b_id' => $this->branchId
            ]);
            return;
        }

        $this->payments = [['method' => 'cash', 'amount' => 0.0]];
        $this->recalculateTotals();
        $this->recalcPayments();
        $this->checkTableManagement();
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
            // If no slug provided, get from employee's department and set the slug
            $employee = auth('employees')->user();
            if ($employee && $employee->department_id) {
                $department = Department::find($employee->department_id);
                if ($department) {
                    $this->departmentId = $department->id;
                    $this->departmentName = $department->name;
                    $this->salesDeptSlug = $department->slug;
                }
            }
        }

        // Validate branch access
        if (!$this->branchId) {
            $this->toast()->error('Branch not specified.')->send();
        }
    }

    protected function checkTableManagement(): void
    {
        // Check if current department has table management enabled
        if ($this->departmentId) {
            $department = Department::find($this->departmentId);
            $this->showTableManagement = $department?->enable_table_management ?? false;
        } else {
            $this->showTableManagement = false;
        }
    }

    protected function checkStockVerification(): bool
    {
        // Check if stock opening has been saved for today's shift for this department
        // This is department-level, not employee-level: once any employee from the department
        // verifies stock for the shift, all other employees can access POS

        if (!$this->departmentId) {
            // No department specified, allow access
            return true;
        }

        // Get current shift type
        $shiftType = 'morning'; // default
        if ($this->activeShiftId) {
            $shift = Shift::find($this->activeShiftId);
            $shiftType = $shift?->shift_type ?? 'morning';
        }

        // Get products from this department
        $productIds = Product::query()
            ->forDepartment($this->departmentId)
            ->where(function ($q) {
                $q->whereNull('branch_id')
                    ->orWhere('branch_id', $this->branchId);
            })
            ->pluck('id');

        if ($productIds->isEmpty()) {
            // No products in this department, allow access
            return true;
        }

        // Check if any stock has been opened for today's shift for this department
        $stockCount = ProductStock::whereDate('stock_date', Carbon::today())
            ->where('shift_type', $shiftType)
            ->whereIn('product_id', $productIds)
            ->count();

        return $stockCount > 0;
    }

    public function toggleTableManagement(): void
    {
        if (!$this->departmentId) {
            $this->toast()->error('Department not found.')->send();
            return;
        }

        $department = Department::find($this->departmentId);
        if (!$department) {
            $this->toast()->error('Department not found.')->send();
            return;
        }

        $department->enable_table_management = !$department->enable_table_management;
        $department->save();

        $this->showTableManagement = $department->enable_table_management;

        // If enabling for the first time and no tables exist, create 8 default tables
        if ($department->enable_table_management && $department->tables()->count() === 0) {
            for ($i = 1; $i <= 8; $i++) {
                Table::create([
                    'branch_id' => $this->branchId,
                    'department_id' => $this->departmentId,
                    'table_number' => (string) $i,
                    'table_name' => 'Table ' . $i,
                    'status' => 'available',
                    'capacity' => 4,
                    'is_active' => true,
                ]);
            }
            $this->toast()->success('Table management enabled! 8 default tables created for ' . $department->name . '.')->send();
        } else {
            $message = $department->enable_table_management
                ? 'Table management enabled for ' . $department->name . '.'
                : 'Table management disabled for ' . $department->name . '.';
            $this->toast()->success($message)->send();
        }

        unset($this->tables);
    }

    protected function loadActiveShift(): void
    {
        $shift = Shift::where('employee_id', auth("employees")->id())
            ->where('status', 'active')
            ->whereDate('shift_date', Carbon::today())
            ->first();

        $this->activeShiftId = $shift?->id;
    }


    #[Computed]
    public function activeShift()
    {
        // return $this->activeShiftId ? SalesShift::find($this->activeShiftId) : null;

        return $this->activeShiftId ? Shift::find($this->activeShiftId) : null;
    }

    public function hasActiveShift(): bool
    {
        return $this->activeShiftId !== null;
    }

    public function getModelClass(): string
    {
        return Sale::class;
    }

    protected function getAllSelectableIds(): array
    {
        return [];
    }

    public function updatedSearch(): void
    {
        // no-op: search is used in the view via query
    }

    public function addToCart(string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $stock = $this->getTodayStockForProduct($productId);
        $available = $this->availableQuantity($stock);

        $lineKey = (string)$productId;
        $currentQty = $this->cart[$lineKey]['qty'] ?? 0;
        $newQty = $currentQty + 1;

        // Strict stock enforcement: cannot add if out of stock
        if ($available <= 0) {
            $this->toast()->error('Out of stock for ' . $product->name . '. Cannot add to cart.')->send();
            return;
        }

        // Strict stock enforcement: cannot exceed available quantity
        if ($newQty > $available) {
            $this->toast()->warning('Cannot add more than available stock (' . $available . ') for ' . $product->name)->send();
            return;
        }

        $this->cart[$lineKey] = [
            'product_id' => $productId,
            'name' => $product->name,
            'price' => (float)($product->price ?? 0),
            'qty' => $newQty,
            'low_stock' => $available < 10,
            'available' => $available,
        ];

        $this->recalculateTotals();
    }

    public function increment(string $lineKey): void
    {
        if (!isset($this->cart[$lineKey])) return;
        $productId = (string)$this->cart[$lineKey]['product_id'];
        $stock = $this->getTodayStockForProduct($productId);
        $available = $this->availableQuantity($stock);
        $newQty = $this->cart[$lineKey]['qty'] + 1;

        // Strict stock enforcement: cannot exceed available quantity
        if ($newQty > $available) {
            $this->toast()->warning('Cannot exceed available stock (' . $available . ') for ' . $this->cart[$lineKey]['name'])->send();
            return;
        }

        $this->cart[$lineKey]['qty'] = $newQty;
        $this->cart[$lineKey]['available'] = $available;
        $this->recalculateTotals();
    }

    public function decrement(string $lineKey): void
    {
        if (!isset($this->cart[$lineKey])) return;
        $newQty = max(1, $this->cart[$lineKey]['qty'] - 1);
        $this->cart[$lineKey]['qty'] = $newQty;
        $this->recalculateTotals();
    }

    public function updateQuantity(string $lineKey, $quantity): void
    {
        if (!isset($this->cart[$lineKey])) return;

        $qty = max(1, (float)$quantity);
        $productId = (string)$this->cart[$lineKey]['product_id'];
        $stock = $this->getTodayStockForProduct($productId);
        $available = $this->availableQuantity($stock);

        // Enforce stock limit
        if ($qty > $available) {
            $this->toast()->warning('Cannot exceed available stock (' . $available . ') for ' . $this->cart[$lineKey]['name'])->send();
            $qty = $available;
        }

        $this->cart[$lineKey]['qty'] = $qty;
        $this->cart[$lineKey]['available'] = $available;
        $this->recalculateTotals();
    }

    public function remove(string $lineKey): void
    {
        unset($this->cart[$lineKey]);
        $this->recalculateTotals();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->recalculateTotals();
    }

    public function updatedDiscount(): void
    {
        $this->recalculateTotals();
    }

    public function updatedCashReceived(): void
    {
        $this->recalculateTotals();
    }

    protected function recalculateTotals(): void
    {
        $this->subtotal = 0.0;
        foreach ($this->cart as $line) {
            $this->subtotal += $line['price'] * $line['qty'];
        }
        $subAfterDiscount = max(0, $this->subtotal - $this->discount);
        $this->tax = 0.0; // hook if needed
        $this->total = $subAfterDiscount + $this->tax;
        $this->changeDue = max(0, $this->paymentTotal - $this->total);
    }

    public function addPaymentRow(): void
    {
        $this->payments[] = ['method' => 'cash', 'amount' => 0.0];
        $this->recalcPayments();
    }

    public function removePaymentRow(int $index): void
    {
        if (isset($this->payments[$index])) {
            array_splice($this->payments, $index, 1);
        }
        if (empty($this->payments)) {
            $this->payments[] = ['method' => 'cash', 'amount' => 0.0];
        }
        $this->recalcPayments();
    }

    public function updatedPayments(): void
    {
        $this->recalcPayments();
    }

    protected function recalcPayments(): void
    {
        $total = 0.0;
        foreach ($this->payments as $p) {
            $total += (float)($p['amount'] ?? 0);
        }
        $this->paymentTotal = $total;
        $this->paymentRemaining = max(0, $this->total - $this->paymentTotal);
        $this->changeDue = max(0, $this->paymentTotal - $this->total);
    }

    public function completeSale(): void
    {
        try {
            // Check for active shift first
            if (!$this->hasActiveShift()) {
                $this->toast()->error('No active shift. Please start a shift before making sales.')->send();
                return;
            }

            if (empty($this->cart)) {
                $this->toast()->warning('Cart is empty.')->send();
                return;
            }

            $this->recalcPayments();
            if ($this->paymentTotal + 0.0001 < $this->total) {
                $this->toast()->warning('Payments do not cover the total.')->send();
                return;
            }

            // Validate payments
            $this->validate([
                'payments.*.method' => 'required|in:cash,transfer,pos',
                'payments.*.amount' => 'required|numeric|min:0',
            ]);

        DB::transaction(function () {
            $sale = Sale::create([
                'sales_shift_id' => null, // Nullable - using general shifts table instead
                'branch_id' => $this->branchId,
                'department_id' => $this->departmentId,
                'sold_by' => auth("employees")->id(),
                'sale_number' => 'POS-' . Carbon::now()->format('Ymd-His'),
                'sale_time' => Carbon::now(),
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'discount' => $this->discount,
                'total' => $this->total,
                'status' => 'completed',
                'order_type' => $this->orderType,
                'notes' => null,
            ]);

            $lowStockWarnings = [];

            foreach ($this->cart as $line) {
                $productId = (string)$line['product_id'];
                $qty = (float)$line['qty'];

                $stock = $this->getTodayStockForProduct($productId, forUpdate: true);
                $available = $this->availableQuantity($stock);

                // Process sale with available quantity or full quantity
                $actualQty = ($available > 0 && $qty > $available) ? $available : $qty;

                if ($actualQty < $qty) {
                    $lowStockWarnings[] = $line['name'] . ' (sold ' . $actualQty . ' of ' . $qty . ' requested)';
                }

                if ($actualQty > 0) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'department_id' => $this->departmentId,
                        'product_id' => $productId,
                        'quantity' => $actualQty,
                        'unit_price' => $line['price'],
                        'subtotal' => $actualQty * $line['price'],
                        'discount' => 0,
                        'total' => $actualQty * $line['price'],
                        'notes' => $actualQty < $qty ? 'Partial fulfillment: ' . $actualQty . '/' . $qty : null,
                    ]);

                    if ($stock) {
                        $stock->quantity_sold = (float)$stock->quantity_sold + $actualQty;
                        $stock->amount = (float)$stock->amount + ($actualQty * $line['price']);
                        $stock->updateCalculatedFields();
                        $stock->save();
                    }
                }
            }

            // Create receipt stored per sale
            $receiptHtml = $this->buildReceiptHtml($sale);
            $receipt = Receipt::create([
                'sale_id' => $sale->id,
                'content' => $receiptHtml,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'discount' => $this->discount,
                'total' => $this->total,
                'payments' => $this->payments,
                'change_due' => max(0, $this->paymentTotal - $this->total),
                'meta' => ['order_type' => $this->orderType, 'warnings' => $lowStockWarnings],
            ]);

            $this->currentSaleId = $sale->id;
            $this->dispatch('pos-receipt-ready', receipt_id: $receipt->id);

            if (!empty($lowStockWarnings)) {
                $this->toast()->warning('Sale completed with stock adjustments: ' . implode(', ', $lowStockWarnings))->send();
            } else {
                $this->toast()->success('Sale completed successfully!')->send();
            }
        });

            $this->clearCart();
            $this->discount = 0;
            $this->orderType = 'dine-in';
            $this->payments = [['method' => 'cash', 'amount' => 0.0]];
            $this->recalcPayments();
        } catch (\Exception $e) {
            \Log::error('POS Sale Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'branch_id' => $this->branchId,
                'department_id' => $this->departmentId,
                'cart' => $this->cart,
            ]);
            $this->toast()->error('Payment failed: ' . $e->getMessage())->send();
        }
    }

    public function holdSale(): void
    {
        // Check for active shift first
        if (!$this->hasActiveShift()) {
            $this->toast()->error('No active shift. Please start a shift before holding sales.')->send();
            return;
        }

        // Persist as draft without affecting stock
        $sale = Sale::create([
            'sales_shift_id' => null, // Nullable - using general shifts table instead
            'branch_id' => $this->branchId,
            'department_id' => $this->departmentId,
            'sold_by' => auth("employees")->id(),
            'sale_number' => 'HOLD-' . Carbon::now()->format('Ymd-His'),
            'sale_time' => Carbon::now(),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => 'hold',
            'order_type' => $this->orderType,
        ]);

        foreach ($this->cart as $line) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'department_id' => $this->departmentId,
                'product_id' => (string)$line['product_id'],
                'quantity' => (float)$line['qty'],
                'unit_price' => $line['price'],
                'subtotal' => $line['qty'] * $line['price'],
                'discount' => 0,
                'total' => $line['qty'] * $line['price'],
            ]);
        }

        $this->currentSaleId = $sale->id;
        $this->toast()->info('Sale put on hold.')->send();
        $this->clearCart();
    }

    public function resumeSale(int $saleId): void
    {
        $sale = Sale::with('saleItems.product')->find($saleId);
        if (!$sale || $sale->status !== 'hold') {
            $this->toast()->warning('Hold ticket not found.')->send();
            return;
        }
        $this->cart = [];
        foreach ($sale->saleItems as $item) {
            $this->cart[(string)$item->product_id] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'Product',
                'price' => (float)$item->unit_price,
                'qty' => (float)$item->quantity,
                'low_stock' => false,
                'available' => $this->availableQuantity($this->getTodayStockForProduct($item->product_id)),
            ];
        }
        $this->discount = (float)$sale->discount;
        $this->orderType = $sale->order_type ?? 'dine-in';
        $this->recalculateTotals();
    }

    public function getProductsProperty(): Collection
    {
        $q = Product::query()
            ->active()
            ->available();

        // CRITICAL: Filter by department - only show products assigned to this department
        if ($this->departmentId) {
            $q->forDepartment($this->departmentId);
        }

        // Search filter
        if (strlen($this->search)) {
            $q->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        return $q->orderBy('name')->limit(50)->get();
    }

    protected function getTodayStockForProduct(string $productId, bool $forUpdate = false): ?ProductStock
    {
        $q = ProductStock::query()
            ->whereDate('stock_date', Carbon::today())
            ->where('product_id', $productId);
        if ($forUpdate) {
            $q->lockForUpdate();
        }
        return $q->first();
    }

    protected function availableQuantity(?ProductStock $stock): float
    {
        if (!$stock) return 0.0;
        // available is closing quantity; if not up to date, compute
        $stock->updateCalculatedFields();
        return max(0, (float)$stock->closing_quantity);
    }

    protected function buildReceiptHtml(Sale $sale): string
    {
        $lines = '';
        foreach ($sale->saleItems()->with('product')->get() as $item) {
            $name = $item->product->name ?? 'Product';
            $qty = number_format($item->quantity, 2);
            $price = number_format($item->unit_price, 2);
            $total = number_format($item->total, 2);
            $lines .= "<tr><td class=\"pr-2\">{$name}</td><td class=\"text-right pr-2\">{$qty} x {$price}</td><td class=\"text-right\">{$total}</td></tr>";
        }
        $paymentsHtml = '';
        foreach ($this->payments as $p) {
            $m = strtoupper($p['method']);
            $a = number_format((float)$p['amount'], 2);
            $paymentsHtml .= "<div class=\"flex justify-between\"><span>{$m}</span><span>GHS {$a}</span></div>";
        }
        $subtotal = number_format($this->subtotal, 2);
        $discount = number_format($this->discount, 2);
        $tax = number_format($this->tax, 2);
        $total = number_format($this->total, 2);
        $change = number_format($this->changeDue, 2);
        $date = Carbon::now()->format('Y-m-d H:i');
        return <<<HTML
<div class="text-sm">
    <div class="text-center font-semibold">Sales Receipt</div>
    <div class="text-center text-xs text-zinc-500">{$date}</div>
    <hr class="my-2 border-zinc-200"/>
    <table class="w-full text-xs">
        <tbody>
            {$lines}
        </tbody>
    </table>
    <hr class="my-2 border-zinc-200"/>
    <div class="space-y-0.5">
        <div class="flex justify-between"><span>Subtotal</span><span>GHS {$subtotal}</span></div>
        <div class="flex justify-between"><span>Discount</span><span>GHS {$discount}</span></div>
        <div class="flex justify-between"><span>Tax</span><span>GHS {$tax}</span></div>
        <div class="flex justify-between font-semibold"><span>Total</span><span>GHS {$total}</span></div>
    </div>
    <hr class="my-2 border-zinc-200"/>
    <div class="space-y-0.5">
        {$paymentsHtml}
        <div class="flex justify-between"><span>Change</span><span>GHS {$change}</span></div>
    </div>
    <div class="mt-2 text-center text-xs">Thank you</div>
</div>
HTML;
    }

    // Table Management Methods
    public function selectTable(int $tableId): void
    {
        $table = Table::find($tableId);
        if (!$table) {
            $this->toast()->error('Table not found.')->send();
            return;
        }

        // Save current cart if there's anything in it and a table is selected
        if (!empty($this->cart) && $this->selectedTableId && $this->selectedTableId !== $tableId) {
            $this->saveTableTab();
        }

        $this->selectedTableId = $tableId;

        // Load the table's active sale if exists
        $activeSale = $table->getActiveSale();
        if ($activeSale) {
            $this->loadTableTab($activeSale);
        } else {
            $this->clearCart();
        }
    }

    protected function loadTableTab(Sale $sale): void
    {
        $this->cart = [];
        foreach ($sale->saleItems as $item) {
            $this->cart[(string)$item->product_id] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'Product',
                'price' => (float)$item->unit_price,
                'qty' => (float)$item->quantity,
                'low_stock' => false,
                'available' => $this->availableQuantity($this->getTodayStockForProduct($item->product_id)),
            ];
        }
        $this->discount = (float)$sale->discount;
        $this->orderType = $sale->order_type ?? 'dine-in';
        $this->currentSaleId = $sale->id;
        $this->recalculateTotals();
    }

    public function saveTableTab(): void
    {
        if (!$this->selectedTableId) {
            $this->toast()->warning('No table selected.')->send();
            return;
        }

        if (empty($this->cart)) {
            $this->toast()->warning('Cart is empty.')->send();
            return;
        }

        if (!$this->hasActiveShift()) {
            $this->toast()->error('No active shift.')->send();
            return;
        }

        $table = Table::find($this->selectedTableId);
        if (!$table) {
            $this->toast()->error('Table not found.')->send();
            return;
        }

        DB::transaction(function () use ($table) {
            // Check if there's an existing hold sale for this table
            $existingSale = $table->getActiveSale();

            if ($existingSale) {
                // Update existing sale
                $existingSale->update([
                    'subtotal' => $this->subtotal,
                    'tax' => $this->tax,
                    'discount' => $this->discount,
                    'total' => $this->total,
                    'order_type' => $this->orderType,
                ]);

                // Delete existing items
                $existingSale->saleItems()->delete();

                $sale = $existingSale;
            } else {
                // Create new sale
                $sale = Sale::create([
                    'sales_shift_id' => null, // Nullable - using general shifts table instead
                    'branch_id' => $this->branchId,
                    'department_id' => $this->departmentId,
                    'sold_by' => auth("employees")->id(),
                    'sale_number' => 'TAB-' . $table->table_number . '-' . Carbon::now()->format('Ymd-His'),
                    'sale_time' => Carbon::now(),
                    'subtotal' => $this->subtotal,
                    'tax' => $this->tax,
                    'discount' => $this->discount,
                    'total' => $this->total,
                    'status' => 'hold',
                    'order_type' => $this->orderType,
                    'notes' => 'Table: ' . $table->table_number,
                ]);
            }

            // Add items to sale
            foreach ($this->cart as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'department_id' => $this->departmentId,
                    'product_id' => (string)$line['product_id'],
                    'quantity' => (float)$line['qty'],
                    'unit_price' => $line['price'],
                    'subtotal' => $line['qty'] * $line['price'],
                    'discount' => 0,
                    'total' => $line['qty'] * $line['price'],
                ]);
            }

            $table->markAsOccupied();
            $this->currentSaleId = $sale->id;
        });

        $this->toast()->success('Tab saved for Table ' . $table->table_number)->send();
    }

    public function completeTableSale(): void
    {
        if (!$this->selectedTableId) {
            $this->toast()->warning('No table selected.')->send();
            return;
        }

        $table = Table::find($this->selectedTableId);
        if (!$table) {
            $this->toast()->error('Table not found.')->send();
            return;
        }

        // Use the existing completeSale method but update table_id
        $this->completeSale();

        // Mark table as available after payment
        $table->markAsAvailable();

        $this->selectedTableId = null;
    }

    public function clearTable(): void
    {
        if (!$this->selectedTableId) {
            return;
        }

        $table = Table::find($this->selectedTableId);
        if ($table) {
            // Delete any hold sales for this table
            $activeSale = $table->getActiveSale();
            if ($activeSale) {
                $activeSale->saleItems()->delete();
                $activeSale->delete();
            }
            $table->markAsAvailable();
        }

        $this->selectedTableId = null;
        $this->clearCart();
        $this->toast()->success('Table cleared.')->send();
    }

    #[Computed]
    public function tables()
    {
        if (!$this->departmentId || !$this->showTableManagement) {
            return collect([]);
        }

        return Table::where('branch_id', $this->branchId)
            ->where('department_id', $this->departmentId)
            ->where('is_active', true)
            ->orderBy('table_number')
            ->get();
    }

    public function createTable(): void
    {
        $this->validate([
            'newTableNumber' => 'required|string|max:10',
            'newTableName' => 'nullable|string|max:50',
            'newTableCapacity' => 'required|integer|min:1|max:20',
        ]);

        if (!$this->branchId || !$this->departmentId) {
            $this->toast()->error('Branch or department not found.')->send();
            return;
        }

        // Check if table number already exists in this department
        $exists = Table::where('branch_id', $this->branchId)
            ->where('department_id', $this->departmentId)
            ->where('table_number', $this->newTableNumber)
            ->exists();

        if ($exists) {
            $this->toast()->error('Table number already exists in this department.')->send();
            return;
        }

        Table::create([
            'branch_id' => $this->branchId,
            'department_id' => $this->departmentId,
            'table_number' => $this->newTableNumber,
            'table_name' => $this->newTableName ?: 'Table ' . $this->newTableNumber,
            'capacity' => $this->newTableCapacity,
            'status' => 'available',
            'is_active' => true,
        ]);

        $this->newTableNumber = '';
        $this->newTableName = '';
        $this->newTableCapacity = 4;
        $this->showTableModal = false;

        unset($this->tables);
        $this->toast()->success('Table created successfully.')->send();
    }

    public function deleteTable(int $tableId): void
    {
        $table = Table::find($tableId);
        if (!$table) {
            $this->toast()->error('Table not found.')->send();
            return;
        }

        if ($table->hasActiveSale()) {
            $this->toast()->error('Cannot delete table with active orders.')->send();
            return;
        }

        $table->delete();
        unset($this->tables);
        $this->toast()->success('Table deleted successfully.')->send();
    }

    public function toggleTableStatus(int $tableId): void
    {
        $table = Table::find($tableId);
        if (!$table) {
            $this->toast()->error('Table not found.')->send();
            return;
        }

        $table->update(['is_active' => !$table->is_active]);
        unset($this->tables);
        $this->toast()->success('Table status updated.')->send();
    }

    public function render()
    {
        return view('livewire.branch-dashboard.sales-dashboard.pos.index');
    }
}
