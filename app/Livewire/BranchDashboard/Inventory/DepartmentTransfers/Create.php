<?php

namespace App\Livewire\BranchDashboard\Inventory\DepartmentTransfers;

use App\Models\Department;
use App\Models\DepartmentTransfer;
use App\Models\DepartmentTransferItem;
use App\Models\Item;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class Create extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $to_department_id;
    public $receiver_id;
    public $notes;
    public $items = [];

    protected $rules = [
        'to_department_id' => 'required|integer|exists:departments,id',
        'receiver_id' => 'required|string|exists:users,id',
        'items.*.item_id' => 'required|integer|exists:items,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.uom_id' => 'nullable|integer|exists:units_of_measure,id',
    ];

    public function getBranchId(): ?string
    {
        return $this->b_id ?? request()->query('b_id') ?? current_branch_id();
    }

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->items = [
            ['item_id' => null, 'quantity' => null, 'uom_id' => null],
        ];
    }

    #[On('branch-changed')]
    public function handleBranchChange($branchId)
    {
        $this->b_id = $branchId;
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        return view('livewire.branch-dashboard.inventory.department-transfers.create', [
            'departments' => Department::where('branch_id', $branchId)->orderBy('name')->get(),
            'itemsList' => Item::where('branch_id', $branchId)->orderBy('name')->get(),
            'uoms' => UnitOfMeasure::query()->orderBy('name')->get(),
            'users' => User::where('branch_id', $branchId)->orderBy('name')->get(),
        ]);
    }

    public function addItemRow()
    {
        $this->items[] = ['item_id' => null, 'quantity' => null, 'uom_id' => null];
    }

    public function removeItemRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate();

        $branchId = $this->getBranchId();
        $user = Auth::user();

        $receiver = User::where('id', $this->receiver_id)
            ->where('branch_id', $branchId)
            ->first();

        if (! $receiver || (int) $receiver->department_id !== (int) $this->to_department_id) {
            $this->toast()->error('Invalid receiver', 'Receiver must belong to the target department.')->send();
            return;
        }

        DB::transaction(function () use ($branchId, $user, $receiver) {
            $transfer = DepartmentTransfer::create([
                'branch_id' => $branchId,
                'from_department_id' => $user?->department_id,
                'to_department_id' => $this->to_department_id,
                'requested_by_id' => $user->id,
                'requested_by_type' => get_class($user),
                'receiver_id' => $receiver->id,
                'receiver_type' => \App\Models\User::class,
                'status' => 'pending_approval',
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $row) {
                DepartmentTransferItem::create([
                    'department_transfer_id' => $transfer->id,
                    'item_id' => $row['item_id'],
                    'uom_id' => $row['uom_id'],
                    'quantity' => $row['quantity'],
                    'quantity_dispatched' => 0,
                    'quantity_received' => 0,
                ]);
            }
        });

        return redirect()->route('branch-dashboard.inventory.department-transfers', ['b_id' => $branchId])
            ->with('success', 'Transfer created and pending approval.');
    }
}
