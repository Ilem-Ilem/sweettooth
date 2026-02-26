<?php

namespace App\Livewire\BranchDashboard\Inventory\DepartmentTransfers;

use App\Models\Department;
use App\Models\DepartmentTransfer;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\SidebarVisibilityService;
use App\Services\UomConversionService;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Index extends Component
{
    use WithPagination, Interactions, Exportable;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $search = '';
    public $statusFilter = '';
    public function getBranchId(): ?string
    {
        return $this->b_id ?? request()->query('b_id') ?? current_branch_id();
    }

    public function mount()
    {
        $this->b_id = current_branch_id();
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
        $this->resetPage();
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $transfers = DepartmentTransfer::with([
            'toDepartment',
            'fromDepartment',
            'receiver',
            'items.item',
            'items.unitOfMeasure',
        ])
            ->where('branch_id', $branchId)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('transfer_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('toDepartment', fn ($d) => $d->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.branch-dashboard.inventory.department-transfers.index', [
            'transfers' => $transfers,
        ]);
    }

    public function approveTransfer(int $transferId)
    {
        $transfer = DepartmentTransfer::with('toDepartment')->findOrFail($transferId);
        $user = Auth::user();

        if (! SidebarVisibilityService::canSeeInventory($user) && ! SidebarVisibilityService::isAdmin($user) && ! SidebarVisibilityService::isSuperAdmin($user)) {
            $this->toast()->error('Not allowed', 'Only inventory/admin can approve.')->send();
            return;
        }

        $transfer->update([
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'approved_by_type' => get_class($user),
            'approved_at' => now(),
        ]);

        $this->toast()->success('Approved', 'Transfer approved. Inventory can dispatch now.')->send();
    }

    public function dispatchTransfer(int $transferId, UomConversionService $uomService)
    {
        $transfer = DepartmentTransfer::with(['items.item', 'items.unitOfMeasure'])->findOrFail($transferId);
        $user = Auth::user();

        if (! SidebarVisibilityService::canSeeInventory($user) && ! SidebarVisibilityService::isAdmin($user) && ! SidebarVisibilityService::isSuperAdmin($user)) {
            $this->toast()->error('Not allowed', 'Only inventory/admin can dispatch.')->send();
            return;
        }

        if ($transfer->status !== 'approved') {
            $this->toast()->error('Invalid status', 'Transfer must be approved before dispatch.')->send();
            return;
        }

        $branchId = $this->getBranchId();

        DB::transaction(function () use ($transfer, $user, $branchId, $uomService) {
            foreach ($transfer->items as $itemRow) {
                $stock = Stock::where('branch_id', $branchId)
                    ->where('item_id', $itemRow->item_id)
                    ->lockForUpdate()
                    ->first();

                if (! $stock) {
                    throw new \RuntimeException('Stock not found for item.');
                }

                $itemBaseUomId = $itemRow->item?->uom_id;
                $dispatchUomId = $itemRow->uom_id ?: $itemBaseUomId;

                $dispatchQtyBase = $dispatchUomId && $itemBaseUomId
                    ? ($uomService->tryConvert((float) $itemRow->quantity, $dispatchUomId, $itemBaseUomId, [
                        'branch_id' => $branchId,
                        'item_id' => $itemRow->item_id,
                    ]) ?? (float) $itemRow->quantity)
                    : (float) $itemRow->quantity;

                if ($stock->quantity_available < $dispatchQtyBase) {
                    throw new \RuntimeException('Insufficient stock for one or more items.');
                }

                $quantityBefore = $stock->quantity_available;
                $stock->quantity_available -= $dispatchQtyBase;
                $stock->save();
                $quantityAfter = $stock->quantity_available;

                $itemRow->update([
                    'quantity_dispatched' => $itemRow->quantity,
                ]);

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'department_transfer',
                    'quantity' => -$dispatchQtyBase,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'movement_date' => now(),
                    'reference_type' => DepartmentTransfer::class,
                    'reference_id' => $transfer->id,
                    'moved_by_id' => $user->id,
                    'moved_by_type' => get_class($user),
                    'notes' => "Department transfer {$transfer->transfer_number}",
                ]);
            }

            $transfer->update([
                'status' => 'dispatched',
                'dispatched_by_id' => $user->id,
                'dispatched_by_type' => get_class($user),
                'dispatched_at' => now(),
            ]);
        });

        $this->toast()->success('Dispatched', 'Transfer dispatched to department.')->send();
    }

    public function receiveTransfer(int $transferId)
    {
        $transfer = DepartmentTransfer::with('items')->findOrFail($transferId);
        $user = Auth::user();

        if (! $user || (int) $user->id !== (int) $transfer->receiver_id) {
            $this->toast()->error('Not allowed', 'Only the assigned receiver can confirm receipt.')->send();
            return;
        }

        if ($transfer->status !== 'dispatched') {
            $this->toast()->error('Invalid status', 'Transfer must be dispatched before receipt.')->send();
            return;
        }

        DB::transaction(function () use ($transfer, $user) {
            foreach ($transfer->items as $itemRow) {
                $itemRow->update([
                    'quantity_received' => $itemRow->quantity_dispatched,
                ]);
            }

            $transfer->update([
                'status' => 'received',
                'received_by_id' => $user->id,
                'received_by_type' => get_class($user),
                'received_at' => now(),
            ]);
        });

        $this->toast()->success('Received', 'Receipt confirmed.')->send();
    }

    public function exportCSV()
    {
        $branchId = $this->getBranchId();

        $data = DepartmentTransfer::with(['toDepartment', 'fromDepartment', 'receiver', 'items.item', 'items.unitOfMeasure'])
            ->where('branch_id', $branchId)
            ->latest('created_at')
            ->get();

        return $this->export(
            'department_transfers',
            $data,
            'exports.inventory.department_transfers'
        );
    }
}
