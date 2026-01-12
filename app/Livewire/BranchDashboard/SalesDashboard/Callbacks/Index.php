<?php

namespace App\Livewire\BranchDashboard\SalesDashboard\Callbacks;

use App\Livewire\BaseComponent;
use App\Models\ProductDispatchCallback;
use App\Models\SalesShift;
use App\Traits\Exportable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends BaseComponent
{
    use WithPagination, Interactions, Exportable;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?int $quantity = 20;
    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public $availableShifts = [];

    // Create callback modal
    public $showCreateModal = false;
    public $selectedProduct = null;
    public $callbackQuantity = 0;
    public $callbackReason = '';
    public $callbackNotes = '';
    public $callbackUom = 'kg';
    public $currentSalesShiftId = null;

    protected array $bulkActions = [
        'export' => ['label' => 'Export Selected', 'method' => 'exportSelected'],
    ];

    // Reason options
    public array $reasonOptions = [
        'expired' => 'Expired',
        'damaged' => 'Damaged',
        'quality_issue' => 'Quality Issue',
        'customer_return' => 'Customer Return',
        'over_stock' => 'Over Stock',
        'wrong_item' => 'Wrong Item',
        'other' => 'Other',
    ];

    // Table headers
    public array $headers = [
        ['index' => 'callback_id', 'label' => 'Callback ID'],
        ['index' => 'product', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Quantity'],
        ['index' => 'reason', 'label' => 'Reason'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'callback_time', 'label' => 'Callback Time'],
        ['index' => 'recorded_by', 'label' => 'Recorded By'],
        ['index' => 'action', 'label' => 'Action'],
    ];

    // Status options for filter
    public array $statusOptions = [
        'pending' => 'Pending',
        'approved_by_production' => 'Approved by Production',
        'received_by_production' => 'Received by Production',
        'completed' => 'Completed',
    ];

    protected function getModelClass(): string
    {
        return ProductDispatchCallback::class;
    }

    protected function getAllSelectableIds(): array
    {
        return $this->getFilteredQuery()->pluck('id')->toArray();
    }

    protected function getFilteredQuery()
    {
        $query = ProductDispatchCallback::query()
            ->with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->whereHas('salesShift', function ($q) {
                $q->where('branch_id', $this->getBranchId());
            });

        return $query;
    }

    public function getBranchId()
    {
        return $this->b_id ?: request()->query('b_id');
    }

    public function mount()
    {
        $this->startDate = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
        $this->endDate = \Carbon\Carbon::today()->format('Y-m-d');
        $this->loadAvailableShifts();
        $this->loadCurrentSalesShift();
    }

    protected function loadAvailableShifts()
    {
        $branchId = $this->getBranchId();

        // Get sales shifts from last 30 days
        $this->availableShifts = \App\Models\SalesShift::where('branch_id', $branchId)
            ->where('shift_date', '>=', now()->subDays(30))
            ->with('department')
            ->orderBy('shift_date', 'desc')
            ->orderBy('shift_type', 'desc')
            ->get();
    }

    protected function loadCurrentSalesShift()
    {
        $employee = auth()->user();

        // First try to find active sales shift for this employee
        $activeShift = \App\Models\SalesShift::where('branch_id', $this->getBranchId())
            ->where('shift_date', \Carbon\Carbon::today())
            ->where('status', 'active')
            ->where('employee_id', $employee->id)
            ->first();

        // If not found, try to find any active sales shift in the employee's department
        if (!$activeShift && $employee->department_id) {
            $activeShift = \App\Models\SalesShift::where('branch_id', $this->getBranchId())
                ->where('shift_date', \Carbon\Carbon::today())
                ->where('status', 'active')
                ->where('department_id', $employee->department_id)
                ->first();
        }

        if ($activeShift) {
            $this->currentSalesShiftId = $activeShift->id;
        }
    }

    public function openCreateModal()
    {
        if (!$this->currentSalesShiftId) {
            $this->toast()->error('No active sales shift found. Please start a shift first.')->send();
            return;
        }

        $this->selectedProduct = null;
        $this->callbackQuantity = 0;
        $this->callbackReason = '';
        $this->callbackNotes = '';
        $this->callbackUom = 'kg';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->selectedProduct = null;
        $this->callbackQuantity = 0;
        $this->callbackReason = '';
        $this->callbackNotes = '';
        $this->callbackUom = 'kg';
    }

    public function createCallback()
    {
        $this->validate([
            'selectedProduct' => 'required|exists:products,id',
            'callbackQuantity' => 'required|numeric|min:0.01',
            'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,over_stock,wrong_item,other',
            'callbackUom' => 'required|string',
        ], [
            'selectedProduct.required' => 'Please select a product',
            'callbackQuantity.required' => 'Callback quantity is required',
            'callbackQuantity.min' => 'Callback quantity must be greater than 0',
            'callbackReason.required' => 'Please select a callback reason',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $employee = auth()->user();

            // Create callback record
            ProductDispatchCallback::create([
                'product_dispatch_id' => null, // Direct callback without dispatch
                'sales_shift_id' => $this->currentSalesShiftId,
                'product_id' => $this->selectedProduct,
                'recorded_by' => $employee->id,
                'quantity' => $this->callbackQuantity,
                'uom' => $this->callbackUom,
                'reason' => $this->callbackReason,
                'status' => 'pending',
                'notes' => $this->callbackNotes,
                'callback_time' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->toast()->success('Product callback created successfully! Awaiting production approval.')->send();
            $this->closeCreateModal();
            $this->resetPage();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            $this->toast()->error('Error creating callback: ' . $e->getMessage())->send();
        }
    }

    public function getRowsProperty()
    {
        $query = ProductDispatchCallback::with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->whereHas('salesShift', function ($q) {
                $q->where('branch_id', $this->getBranchId());
            });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('product', function ($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('recordedBy', function ($employeeQuery) {
                    $employeeQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('callback_time', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('callback_time', '<=', $this->endDate);
        }

        return $query->orderBy('callback_time', 'desc')->paginate($this->quantity);
    }

    public function viewDetails($callbackId)
    {
        $callback = ProductDispatchCallback::with([
            'product',
            'productDispatch',
            'salesShift',
            'recordedBy',
            'approvedBy',
            'receivedBy'
        ])->find($callbackId);

        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        // Store for modal display
        $this->dispatch('show-callback-details', callback: $callback->toArray());
    }

    /**
     * Get filtered callbacks for export
     */
    private function getFilteredCallbacks()
    {
        $query = ProductDispatchCallback::with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->whereHas('salesShift', function ($q) {
                $q->where('branch_id', $this->getBranchId());
            });

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('product', function ($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('recordedBy', function ($employeeQuery) {
                    $employeeQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Date range filter
        if ($this->startDate) {
            $query->whereDate('callback_time', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('callback_time', '<=', $this->endDate);
        }

        return $query->orderBy('callback_time', 'desc')->get();
    }

    protected function exportSelected(): void
    {
        if (empty($this->selectedIds)) {
            session()->flash('info', 'No callbacks selected for export.');
            return;
        }

        $callbacks = ProductDispatchCallback::whereIn('id', $this->selectedIds)
            ->with(['product', 'salesShift', 'recordedBy', 'approvedBy', 'receivedBy'])
            ->orderBy('callback_time', 'desc')
            ->get();

        $this->export(
            'callbacks_' . date('Y-m-d'),
            $callbacks,
            'exports.sales.callbacks',
            'excel'
        );

        session()->flash('success', count($this->selectedIds) . ' callbacks exported successfully.');
        $this->resetBulkSelection();
    }

    /**
     * Export callbacks as Excel
     * Note: PDF/Excel exports cannot be returned directly from Livewire.
     */
    public function exportExcel()
    {
        session()->flash('info', 'Excel export coming soon. Please use CSV export instead.');
    }

    /**
     * Export callbacks as CSV
     */
    public function exportCSV()
    {
        try {
            $callbacks = $this->getFilteredCallbacks();

            if ($callbacks->isEmpty()) {
                session()->flash('warning', 'No callbacks to export.');
                return;
            }

            $csvData = [
                ['Callback ID', 'Product', 'SKU', 'Quantity', 'UOM', 'Reason', 'Status', 'Callback Time', 'Recorded By', 'Approved By', 'Received By', 'Notes'],
            ];

            foreach ($callbacks as $callback) {
                $csvData[] = [
                    $callback->id ?? 'N/A',
                    $callback->product?->name ?? 'N/A',
                    $callback->product?->sku ?? 'N/A',
                    $callback->quantity ?? 0,
                    $callback->uom ?? 'kg',
                    ucfirst(str_replace('_', ' ', $callback->reason ?? 'N/A')),
                    ucfirst(str_replace('_', ' ', $callback->status ?? 'pending')),
                    $callback->callback_time ? \Carbon\Carbon::parse($callback->callback_time)->format('Y-m-d H:i') : 'N/A',
                    $callback->recordedBy?->name ?? 'N/A',
                    $callback->approvedBy?->name ?? 'N/A',
                    $callback->receivedBy?->name ?? 'N/A',
                    $callback->notes ?? 'N/A',
                ];
            }

            $filename = 'product-callbacks-' . now()->format('Y-m-d-His') . '.csv';
            $handle = fopen('php://temp', 'r+');

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }

            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response()->streamDownload(function () use ($csv) {
                echo $csv;
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: ' . $e->getMessage());
            return;
        }
    }

    public function exportCallbacks()
    {
        $this->toast()->info('Export feature coming soon.')->send();
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved_by_production' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'received_by_production' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    public function render()
    {
        $products = \App\Models\Product::where('is_active', 1)
            ->where(function ($query) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $this->getBranchId());
            })
            ->orderBy('name')
            ->get();

        $currentSalesShift = $this->currentSalesShiftId ? \App\Models\SalesShift::find($this->currentSalesShiftId) : null;

        return view('livewire.branch-dashboard.sales-dashboard.callbacks.index', [
            'rows' => $this->rows,
            'products' => $products,
            'currentSalesShift' => $currentSalesShift,
            'availableShifts' => $this->availableShifts,
        ]);
    }
}
