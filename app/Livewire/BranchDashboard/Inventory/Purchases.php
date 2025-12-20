<?php

namespace App\Livewire\BranchDashboard\Inventory;

use App\Helpers\Settings;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Branch;
use Livewire\Component;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Livewire\WithPagination;
use App\Models\StockMovement;
use App\Models\PurchaseApprovalRequest;
use App\Services\AuditService;
use App\Services\CurrencyFormattingService;
use App\Services\PurchaseAuditApprovalService;
use App\Traits\Exportable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Url, On};
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Purchases extends Component
{
    use WithPagination, Interactions, Exportable;
    #[Url(keep: true)]
    public ?string $b_id = null;
    public $purchaseId;
    public $purchase_date;
    public $supplier_name;
    public $supplier_contact;
    public $other_costs = 0;
    public $payment_status = 'pending';
    public $notes;

    public $quantity = [];

    public $purchaseItems = [];
    public $itemIndex = 0;

    public $search = '';
    public $filterPaymentStatus = '';
    public $filterStatus = '';
    public $sortColumn = 'purchase_date';
    public $sortDirection = 'desc';
    public $viewMode = 'table'; // table, cards, list, timeline, stats

    public $showModal = false;
    public $isEditing = false;

    // Request Approval Modal
    public $showRequestModal = false;
    public $requestNotes = '';
    public $pendingPurchaseData = [];

    // View Detail Modal
    public $showDetailModal = false;
    public $detailPurchase = null;

    protected $rules = [
        'purchase_date' => 'required|date',
        'supplier_name' => 'required|string|max:255',
        'supplier_contact' => 'nullable|string|max:255',
        'other_costs' => 'nullable|numeric|min:0',
        'payment_status' => 'required|in:paid,partial,pending',
        'notes' => 'nullable|string',
        'purchaseItems' => 'required|array|min:1',
        'purchaseItems.*.item_id' => 'required|exists:items,id',
        'purchaseItems.*.quantity' => 'required|numeric|min:0.01',
        'purchaseItems.*.uom' => 'required|string|min:1',
        'purchaseItems.*.unit_price' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->purchase_date = now()->format('Y-m-d');
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $query = Purchase::with(['branch', 'recorder', 'purchaseItems.item'])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPaymentStatus, fn ($q) => $q->where('payment_status', $this->filterPaymentStatus))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortColumn, $this->sortDirection);

        $purchases = $query->paginate(15);
        $items = Item::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('livewire.branch-dashboard.inventory.purchases', [
            'purchases' => $purchases,
            'items' => $items,
            'summary' => $this->getPurchaseSummary(),
            'purchasesByStatus' => $this->getPurchasesByStatus(),
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-purchases'); // TODO: Enable permissions after testing
        $this->resetFields();
        $this->addPurchaseItem();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function addPurchaseItem()
    {
        $this->purchaseItems[] = [
            'id' => $this->itemIndex++,
            'item_id' => '',
            'quantity' => 0,
            'uom' => '',
            'unit_price' => 0,
        ];
    }

    public function removePurchaseItem($index)
    {
        unset($this->purchaseItems[$index]);
        $this->purchaseItems = array_values($this->purchaseItems);
    }

    public function save()
    {
        // TODO: Enable permissions after testing
        // if ($this->isEditing) {
        //     $this->authorize('edit-purchases');
        // } else {
        //     $this->authorize('create-purchases');
        // }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Keep modal open and show validation errors
            throw $e;
        }

        // Super admins create immediately with stock updates
        if (is_super_admin()) {
            $this->executeImmediatePurchaseCreation();
            return;
        }

        // Regular employees save as draft and then request approval
        $this->savePurchaseDraft();
    }

    private function executeImmediatePurchaseCreation()
    {
        DB::beginTransaction();
        try {
            $actor = current_actor();

            if (!$actor) {
                abort(403, "No Authenticated User Found");
            }

            $branchId = $this->getBranchId();
            $branch = is_super_admin() ? Branch::where('id', $branchId)->First() :
                Auth::guard('web')->user()->branch;

            $purchaseNumber = Purchase::generatePurchaseNumber($branch->code);

            $totalCost = 0;

            foreach ($this->purchaseItems as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalCost += $itemTotal;
            }

            $landingCost = $totalCost + ($this->other_costs ?? 0);
            $totalCost = $landingCost; // Set total_cost equal to landing_cost
            
            // For NGN currency (branch dashboard only uses NGN)
            $totalFobNgn = $landingCost - ($this->other_costs ?? 0);

            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'recorded_by_id' => $actor->id,
                'recorded_by_type' => get_class($actor),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $this->purchase_date,
                'supplier_name' => $this->supplier_name,
                'supplier_contact' => $this->supplier_contact,
                'total_fob_fc' => $totalFobFc,
                'total_fob_ngn' => $totalFobNgn,
                'other_costs' => $this->other_costs ?? 0,
                'landing_cost' => $landingCost,
                'total_cost' => $totalCost,
                'payment_status' => $this->payment_status,
                'notes' => $this->notes,
                'status' => 'approved', // Super admins bypass approval
            ]);

            foreach ($this->purchaseItems as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $totalItemCost = $quantity * $unitPrice;

                $costProportion = ($landingCost - ($this->other_costs ?? 0)) > 0 ? ($totalItemCost / ($landingCost - ($this->other_costs ?? 0))) : 0;
                $allocatedOtherCosts = ($this->other_costs ?? 0) * $costProportion;
                $landingCostItem = $totalItemCost + $allocatedOtherCosts;
                $costPerUnit = $quantity > 0 ? ($landingCostItem / $quantity) : 0;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $quantity,
                    'uom' => $item['uom'],
                    'fob_fc' => 0,
                    'fob_ngn' => $unitPrice,
                    'other_costs' => $allocatedOtherCosts,
                    'landing_cost' => $landingCostItem,
                    'total_cost' => $landingCostItem,
                    'cost_per_unit' => $costPerUnit,
                ]);

                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $branchId,
                        'item_id' => $item['item_id'],
                    ],
                    [
                        'quantity_available' => 0,
                        'quantity_reserved' => 0,
                        'average_cost' => 0,
                    ]
                );

                $stock->updateAverageCost($quantity, $costPerUnit);
                $quantity_before = $stock->quantity_available;
                $stock->quantity_available += $quantity;
                $stock->last_stock_take_date = now();
                $stock->save();

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'in',
                    'quantity_before'=>$quantity_before,
                    'quantity_after'=>$stock->quantity_available,
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Purchase',
                    'reference_id' => $purchase->id,
                    'moved_by_type'   => get_class($actor),
                    'moved_by_id'     => $actor->id,
                    'movement_date' => $this->purchase_date,
                    'notes' => 'Purchase: ' . $purchaseNumber,
                ]);
            }

            // Log the purchase creation
            AuditService::log(
                $actor,
                'create',
                $purchase,
                "Created purchase #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
                "Total FOB FC: {$purchase->total_fob_fc}, Total FOB NGN: {$purchase->total_fob_ngn}, " .
                "Landing Cost: {$purchase->landing_cost}, Payment Status: {$purchase->payment_status}. " .
                "Items: " . count($this->purchaseItems),
                'completed'
            );

            DB::commit();
            $this->toast()->success('Purchase created successfully.')->send();
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error creating purchase: ' . $e->getMessage())->send();
        }
    }

    /**
     * Save purchase as draft (for regular employees)
     */
    private function savePurchaseDraft()
    {
        DB::beginTransaction();
        try {
            $actor = current_actor();

            if (!$actor) {
                abort(403, "No Authenticated User Found");
            }

            $branchId = $this->getBranchId();

            $purchaseNumber = Purchase::generatePurchaseNumber(
                Auth::guard('web')->user()->branch->code
            );

            $totalFobFc = 0;
            $totalFobNgn = 0;

            foreach ($this->purchaseItems as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalFobNgn += $itemTotal;
            }

            $landingCost = $totalFobNgn + ($this->other_costs ?? 0);

            // Create the purchase with draft status
            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'recorded_by_id' => $actor->id,
                'recorded_by_type' => get_class($actor),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $this->purchase_date,
                'supplier_name' => $this->supplier_name,
                'supplier_contact' => $this->supplier_contact,
                'total_fob_fc' => $totalFobNgn,
                'total_fob_ngn' => $totalFobNgn,
                'other_costs' => $this->other_costs ?? 0,
                'landing_cost' => $landingCost,
                'payment_status' => $this->payment_status,
                'notes' => $this->notes,
                'status' => 'draft', // Save as draft
            ]);

            // Add purchase items (no stock updates)
            foreach ($this->purchaseItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'uom' => $item['uom'],
                    'fob_fc' => $item['unit_price'],
                    'fob_ngn' => $item['unit_price'],
                    'other_costs' => 0, // Will be allocated on approval
                    'total_cost' => $item['quantity'] * $item['unit_price'],
                    'cost_per_unit' => $item['unit_price'],
                ]);
            }

            // Log the draft creation
            AuditService::log(
                $actor,
                'create',
                $purchase,
                "Saved purchase draft #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
                "Total FOB NGN: {$purchase->total_fob_ngn}, Landing Cost: {$purchase->landing_cost}. " .
                "Items: " . count($this->purchaseItems) . ". Status: Draft",
                'completed'
            );

            DB::commit();
            
            $this->toast()->success('Purchase saved as draft successfully. Click "Request Approval" button to submit for approval.')->send();
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast()->error('Error saving purchase: ' . $e->getMessage())->send();
        }
    }

    /**
     * Request approval for a draft purchase
     */
    public function requestPurchaseApproval($purchaseId)
    {
        try {
            $purchase = Purchase::findOrFail($purchaseId);

            if ($purchase->branch_id !== $this->getBranchId()) {
                throw new \Exception('Unauthorized action.');
            }

            if ($purchase->status !== 'draft') {
                throw new \Exception('Only draft purchases can be submitted for approval.');
            }

            $this->pendingPurchaseData = $purchase->toArray();
            $this->requestNotes = '';
            $this->showRequestModal = true;
        } catch (\Exception $e) {
            $this->toast()->error($e->getMessage())->send();
        }
    }

    /**
     * Submit purchase approval request to auditor
     */
    public function submitPurchaseApprovalRequest()
    {
        $this->validate([
            'requestNotes' => 'nullable|string|max:500',
        ]);

        try {
            $actor = Auth::guard('web')->user();
            $branchId = $this->getBranchId();

            // Find the purchase by ID from pending data
            $purchaseId = $this->pendingPurchaseData['id'] ?? null;
            if (!$purchaseId) {
                throw new \Exception('Purchase ID not found');
            }

            // Use PurchaseAuditApprovalService to create approval request
            PurchaseAuditApprovalService::requestPurchaseApproval(
                $actor,
                $purchaseId,
                $this->requestNotes ?? 'No reason provided'
            );

            $this->toast()->success('Purchase approval request submitted successfully!')->send();
            $this->closeRequestModal();
            $this->resetFields();
        } catch (\Exception $e) {
            $this->toast()->error('Failed: ' . $e->getMessage())->send();
        }
    }

    /**
     * Close request approval modal
     */
    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->requestNotes = '';
        $this->pendingPurchaseData = [];
        $this->resetValidation();
    }

        public function delete($id)
        {
            // $this->authorize('delete-purchases'); // TODO: Enable permissions after testing

            try {
                $purchase = Purchase::findOrFail($id);

                if ($purchase->branch_id !== $this->getBranchId()) {
                    throw new \Exception('Unauthorized action.');
                }

                // Only allow deletion of draft purchases
                if ($purchase->status !== 'draft') {
                    throw new \Exception('Only draft purchases can be deleted.');
                }

                $purchaseNumber = $purchase->purchase_number;
                $supplierName = $purchase->supplier_name;
                $itemCount = $purchase->purchaseItems()->count();
                $landingCost = $purchase->landing_cost;

                $purchase->delete();

                // Log the purchase deletion
                AuditService::log(
                    current_actor(),
                    'delete',
                    $purchase,
                    "Deleted purchase draft #{$purchaseNumber} from {$supplierName}. Items: {$itemCount}, Landing Cost: {$landingCost}",
                    'completed'
                );

                $this->toast()->success('Purchase deleted successfully.')->send();
            } catch (\Exception $e) {
                $this->toast()->error($e->getMessage())->send();
            }
        }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }



    public function resetFields()
    {
        $this->purchaseId = null;
        $this->purchase_date = now()->format('Y-m-d');
        $this->supplier_name = '';
        $this->supplier_contact = '';
        $this->other_costs = 0;
        $this->payment_status = 'pending';
        $this->notes = '';
        $this->purchaseItems = [];
        $this->itemIndex = 0;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterPaymentStatus = '';
        $this->filterStatus = '';
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function getPurchaseSummary()
    {
        $branchId = $this->getBranchId();
        $query = Purchase::where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        $purchases = $query->get();

        return [
            'total_purchases' => $purchases->count(),
            'total_cost' => $purchases->sum('landing_cost'),
            'paid_count' => $purchases->where('payment_status', 'paid')->count(),
            'pending_count' => $purchases->where('payment_status', 'pending')->count(),
            'partial_count' => $purchases->where('payment_status', 'partial')->count(),
        ];
    }

    public function getPurchasesByStatus()
    {
        $branchId = $this->getBranchId();
        $query = Purchase::where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        return $query->get()->groupBy('payment_status')->map(function ($group) {
            return [
                'status' => $group->first()->payment_status,
                'count' => $group->count(),
                'total_cost' => $group->sum('landing_cost'),
                'purchases' => $group,
            ];
        })->sortByDesc('total_cost');
    }

    /**
     * Update UOM when item is selected
     */
    public function updateItemUom($index, $itemId)
    {
        if (empty($itemId)) {
            return;
        }

        $item = Item::find($itemId);
        if ($item) {
            $this->purchaseItems[$index]['uom'] = $item->uom;
        }
    }

    /**
     * View purchase details
     */
    public function viewDetail($purchaseId)
    {
        try {
            $this->detailPurchase = Purchase::with(['purchaseItems.item', 'recorder', 'approvalRequest'])
                ->findOrFail($purchaseId);

            if ($this->detailPurchase->branch_id !== $this->getBranchId()) {
                throw new \Exception('Unauthorized action.');
            }

            $this->showDetailModal = true;
        } catch (\Exception $e) {
            $this->toast()->error($e->getMessage())->send();
        }
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailPurchase = null;
        $this->resetValidation();
    }

    public function exportPDF()
    {
        try {
            $purchases = Purchase::with('purchaseItems.item')
                ->where('branch_id', $this->getBranchId())
                ->when($this->search, fn($q) => $q->where('supplier_name', 'like', '%' . $this->search . '%')->orWhere('purchase_number', 'like', '%' . $this->search . '%'))
                ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->get();

            if ($purchases->isEmpty()) {
                $this->toast()->warning('No purchases to export.')->send();
                return;
            }

            $data = $purchases->map(function ($purchase) {
                return [
                    'purchase_number' => $purchase->purchase_number ?? 'N/A',
                    'supplier_name' => $purchase->supplier_name ?? 'N/A',
                    'purchase_date' => $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : 'N/A',
                    'landing_cost' => $purchase->landing_cost ?? 0,
                    'other_costs' => $purchase->other_costs ?? 0,
                    'total_cost' => ($purchase->landing_cost ?? 0) + ($purchase->other_costs ?? 0),
                    'payment_status' => ucfirst($purchase->payment_status ?? 'pending'),
                    'status' => ucfirst($purchase->status ?? 'pending'),
                    'item_count' => $purchase->purchaseItems->count(),
                ];
            });

            return $this->export(
                'purchases-' . now()->format('Y-m-d'),
                $data,
                'exports.inventory.purchases',
                'pdf',
                false,
                ['orientation' => 'landscape', 'paper' => 'A4']
            );
        } catch (\Exception $e) {
            $this->toast()->error('Export failed: ' . $e->getMessage())->send();
            return;
        }
    }

    public function exportExcel()
    {
        try {
            $purchases = Purchase::with('purchaseItems.item')
                ->where('branch_id', $this->getBranchId())
                ->when($this->search, fn($q) => $q->where('supplier_name', 'like', '%' . $this->search . '%')->orWhere('purchase_number', 'like', '%' . $this->search . '%'))
                ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->get();

            if ($purchases->isEmpty()) {
                $this->toast()->warning('No purchases to export.')->send();
                return;
            }

            $data = $purchases->map(function ($purchase) {
                return [
                    'purchase_number' => $purchase->purchase_number ?? 'N/A',
                    'supplier_name' => $purchase->supplier_name ?? 'N/A',
                    'purchase_date' => $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : 'N/A',
                    'landing_cost' => $purchase->landing_cost ?? 0,
                    'other_costs' => $purchase->other_costs ?? 0,
                    'total_cost' => ($purchase->landing_cost ?? 0) + ($purchase->other_costs ?? 0),
                    'payment_status' => ucfirst($purchase->payment_status ?? 'pending'),
                    'status' => ucfirst($purchase->status ?? 'pending'),
                    'item_count' => $purchase->purchaseItems->count(),
                ];
            });

            return $this->export(
                'purchases-' . now()->format('Y-m-d'),
                $data,
                'exports.inventory.purchases',
                'excel'
            );
        } catch (\Exception $e) {
            $this->toast()->error('Export failed: ' . $e->getMessage())->send();
            return;
        }
    }

    public function exportCSV()
    {
        try {
            $purchases = Purchase::with('purchaseItems.item')
                ->where('branch_id', $this->getBranchId())
                ->when($this->search, fn($q) => $q->where('supplier_name', 'like', '%' . $this->search . '%')->orWhere('purchase_number', 'like', '%' . $this->search . '%'))
                ->when($this->filterPaymentStatus, fn($q) => $q->where('payment_status', $this->filterPaymentStatus))
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->get();

            if ($purchases->isEmpty()) {
                $this->toast()->warning('No purchases to export.')->send();
                return;
            }

            $csvData = [
                ['Purchase #', 'Supplier', 'Date', 'Landing Cost', 'Other Costs', 'Total Cost', 'Payment Status', 'Status', 'Items'],
            ];

            foreach ($purchases as $purchase) {
                $csvData[] = [
                    $purchase->purchase_number ?? 'N/A',
                    $purchase->supplier_name ?? 'N/A',
                    $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : 'N/A',
                    number_format($purchase->landing_cost ?? 0, 2),
                    number_format($purchase->other_costs ?? 0, 2),
                    number_format(($purchase->landing_cost ?? 0) + ($purchase->other_costs ?? 0), 2),
                    ucfirst($purchase->payment_status ?? 'pending'),
                    ucfirst($purchase->status ?? 'pending'),
                    $purchase->purchaseItems->count(),
                ];
            }

            $filename = 'purchases-' . now()->format('Y-m-d-His') . '.csv';
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
            $this->toast()->error('Export failed: ' . $e->getMessage())->send();
            return;
        }
    }

    /**
     * Format currency value for inventory pricing
     */
    protected function formatCurrency(float $amount): string
    {
        $service = new CurrencyFormattingService();
        return $service->format($amount);
    }

    /**
     * Get currency symbol for display
     */
    protected function getCurrencySymbol(?string $currency = null): string
    {
        $service = new CurrencyFormattingService();
        $currency = $currency ?? Settings::currencyLocalization('primary_currency', 'NGN');
        return $service->getSymbol($currency);
    }

    /**
     * Get primary currency for inventory operations
     */
    protected function getPrimaryCurrency(): string
    {
        return Settings::currencyLocalization('primary_currency', 'NGN');
    }
}
