<?php

namespace App\Livewire\BranchDashboard\Accounting\Simple;

use App\Models\GlEntry;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\NotificationRecipientService;
use App\Notifications\GlPostingApprovedNotification;
use App\Notifications\GlPostingApprovalFailedNotification;

#[Layout('components.layouts.app.branch-dashboard')]
class PostingApprovals extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public ?int $quantity = 25;
    public ?string $search = null;
    public bool $showDetail = false;
    public array $detail = [];
    public string $transactionType = 'sales';

    private function getBranchId(): ?string
    {
        return $this->b_id ?: request()->query('b_id') ?: current_branch_id();
    }

    public function render()
    {
        $rows = $this->getPendingGroups();

        return view('livewire.branch-dashboard.accounting.simple.posting-approvals', [
            'rows' => $rows,
            'transactionType' => $this->transactionType,
        ]);
    }

    private function getPendingGroups()
    {
        $branchId = $this->getBranchId();
        $query = $this->transactionQuery($branchId);

        $transactions = $query->paginate($this->quantity ?? 25);

        $transactions->getCollection()->transform(function ($row) {
            $referenceType = $this->referenceTypeForTab($this->transactionType);
            if (!$referenceType) {
                $referenceType = match (true) {
                    $row instanceof Sale => Sale::class,
                    $row instanceof Purchase => Purchase::class,
                    $row instanceof Payment => Payment::class,
                    $row instanceof PurchasePayment => PurchasePayment::class,
                    $row instanceof StockMovement => StockMovement::class,
                    default => null,
                };
            }

            $drafts = GlEntry::query()
                ->where('status', 'draft')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $row->id);

            $row->reference_type = $referenceType;
            $row->reference_id = $row->id;
            $row->reference_number = $row->reference_number
                ?? $row->purchase_number
                ?? $row->sale_number
                ?? null;
            $row->entry_date = $row->sale_time
                ?? $row->purchase_date
                ?? $row->payment_time
                ?? $row->movement_date
                ?? $row->created_at;
            $row->line_count = $drafts->count();
            $row->total_debit = (float) $drafts->sum('debit');
            $row->total_credit = (float) $drafts->sum('credit');

            return $row;
        });

        return $transactions;
    }

    private function transactionQuery(?string $branchId)
    {
        $type = $this->transactionType;

        return match ($type) {
            'sales' => Sale::query()
                ->where('gl_posting_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('sale_number', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                })
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('sale_time', 'desc'),
            'purchases' => Purchase::query()
                ->where('gl_posting_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhere('supplier_name', 'like', '%' . $this->search . '%');
                })
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('purchase_date', 'desc'),
            'payments' => Payment::query()
                ->where('gl_posting_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                })
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('payment_time', 'desc'),
            'purchase_payments' => PurchasePayment::query()
                ->where('gl_posting_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                })
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('payment_date', 'desc'),
            'adjustments' => StockMovement::query()
                ->where('gl_posting_status', 'pending')
                ->when($this->search, function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%');
                })
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('movement_date', 'desc'),
            default => Sale::query()
                ->where('gl_posting_status', 'pending')
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->orderBy('sale_time', 'desc'),
        };
    }

    private function referenceTypeForTab(string $type): ?string
    {
        return match ($type) {
            'sales' => Sale::class,
            'purchases' => Purchase::class,
            'payments' => Payment::class,
            'purchase_payments' => PurchasePayment::class,
            'adjustments' => StockMovement::class,
            default => null,
        };
    }

    public function changeTransactionType(string $type): void
    {
        $this->transactionType = $type;
        $this->resetPage();
    }

    public function updatedQuantity(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function viewGroup(string $referenceType, int $referenceId): void
    {
        $entries = GlEntry::query()
            ->where('status', 'draft')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->with('glAccount')
            ->orderBy('id')
            ->get();

        if ($entries->isEmpty()) {
            session()->flash('error', 'No draft entries found for this reference.');
            return;
        }

        $first = $entries->first();
        $this->detail = [
            'reference' => $first->reference_number ?? $first->reference_label,
            'type' => class_basename($referenceType),
            'entries' => $entries->map(fn ($e) => [
                'id' => $e->id,
                'account' => $e->glAccount?->account_name ?? '-',
                'debit' => $e->debit,
                'credit' => $e->credit,
                'description' => $e->description,
                'entry_date' => optional($e->entry_date)->format('Y-m-d H:i'),
            ])->toArray(),
        ];
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detail = [];
    }

    public function approveGroup(string $referenceType, int $referenceId): void
    {
        $entries = GlEntry::query()
            ->where('status', 'draft')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->orderBy('id')
            ->get();

        if ($entries->isEmpty()) {
            session()->flash('error', 'No draft entries found for this reference.');
            return;
        }

        $branchId = $entries->first()->branch_id;
        $referenceNumber = $entries->first()->reference_number ?? null;

        try {
            DB::transaction(function () use ($entries, $referenceType, $referenceId) {
                foreach ($entries as $entry) {
                    $entry->post(auth()->id());
                }

                $this->markReferencePosted($referenceType, $referenceId);
            });

            $this->notifyApproved($referenceType, $referenceId, $referenceNumber, $branchId);
            session()->flash('message', 'Posting approved successfully.');
        } catch (\Throwable $e) {
            $this->notifyFailed($referenceType, $referenceId, $referenceNumber, $branchId, $e->getMessage());
            session()->flash('error', 'Posting approval failed: ' . $e->getMessage());
        }
    }

    private function markReferencePosted(string $referenceType, int $referenceId): void
    {
        $postable = [
            Sale::class,
            Purchase::class,
            Payment::class,
            StockMovement::class,
            PurchasePayment::class,
        ];

        if (! in_array($referenceType, $postable, true)) {
            return;
        }

        $referenceType::where('id', $referenceId)->update([
            'gl_posting_status' => 'posted',
            'gl_posted_at' => now(),
            'gl_posting_error' => null,
        ]);
    }

    private function notifyApproved(string $referenceType, int $referenceId, ?string $referenceNumber, ?string $branchId): void
    {
        $recipients = app(NotificationRecipientService::class)
            ->usersForRoles(config('notifications.roles.accounting', []), $branchId);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new GlPostingApprovedNotification($referenceType, $referenceId, $referenceNumber, $branchId)
        );
    }

    private function notifyFailed(string $referenceType, int $referenceId, ?string $referenceNumber, ?string $branchId, ?string $error): void
    {
        $recipients = app(NotificationRecipientService::class)
            ->usersForRoles(config('notifications.roles.accounting', []), $branchId);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new GlPostingApprovalFailedNotification($referenceType, $referenceId, $referenceNumber, $branchId, $error)
        );
    }
}
