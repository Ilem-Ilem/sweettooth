<?php

namespace App\Livewire\BranchDashboard\Accounting;

use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app.branch-dashboard')]
class PostingStatusMonitor extends Component
{
    use WithPagination;

    public string $transactionType = 'sales'; // sales, purchases, payments, adjustments

    public string $status = 'all'; // all, pending, posted, failed

    public int $perPage = 25;

    public bool $isBulkPosting = false;

    public int $bulkPostingProgress = 0;

    public int $bulkPostingTotal = 0;

    public function render()
    {
        $failedTransactions = [];
        $stats = [];

        switch ($this->transactionType) {
            case 'sales':
                $failedTransactions = $this->getSalesData();
                $stats = [
                    'total' => Sale::count(),
                    'posted' => Sale::where('gl_posting_status', 'posted')->count(),
                    'pending' => Sale::where('gl_posting_status', 'pending')->count(),
                    'failed' => Sale::where('gl_posting_status', 'failed')->count(),
                ];
                break;

            case 'purchases':
                $failedTransactions = $this->getPurchasesData();
                $stats = [
                    'total' => Purchase::count(),
                    'posted' => Purchase::where('gl_posting_status', 'posted')->count(),
                    'pending' => Purchase::where('gl_posting_status', 'pending')->count(),
                    'failed' => Purchase::where('gl_posting_status', 'failed')->count(),
                ];
                break;

            case 'payments':
                $failedTransactions = $this->getPaymentsData();
                $stats = [
                    'total' => Payment::count(),
                    'posted' => Payment::where('gl_posting_status', 'posted')->count(),
                    'pending' => Payment::where('gl_posting_status', 'pending')->count(),
                    'failed' => Payment::where('gl_posting_status', 'failed')->count(),
                ];
                break;

            case 'adjustments':
                $failedTransactions = $this->getStockMovementsData();
                $stats = [
                    'total' => StockMovement::whereIn('type', ['damage', 'shrinkage'])->count(),
                    'posted' => StockMovement::whereIn('type', ['damage', 'shrinkage'])->where('gl_posting_status', 'posted')->count(),
                    'pending' => StockMovement::whereIn('type', ['damage', 'shrinkage'])->where('gl_posting_status', 'pending')->count(),
                    'failed' => StockMovement::whereIn('type', ['damage', 'shrinkage'])->where('gl_posting_status', 'failed')->count(),
                ];
                break;
        }

        return view('livewire.branch-dashboard.accounting.posting-status-monitor', [
            'transactions' => $failedTransactions,
            'stats' => $stats,
            'transactionType' => $this->transactionType,
            'status' => $this->status,
        ]);
    }

    private function getSalesData()
    {
        $query = Sale::query();

        if ($this->status !== 'all') {
            $query->where('gl_posting_status', $this->status);
        }

        return $query
            ->with(['salesShift', 'branch'])
            ->orderBy('sale_time', 'desc')
            ->paginate($this->perPage);
    }

    private function getPurchasesData()
    {
        $query = Purchase::query();

        if ($this->status !== 'all') {
            $query->where('gl_posting_status', $this->status);
        }

        return $query
            ->with(['branch'])
            ->orderBy('purchase_date', 'desc')
            ->paginate($this->perPage);
    }

    private function getPaymentsData()
    {
        $query = Payment::query();

        if ($this->status !== 'all') {
            $query->where('gl_posting_status', $this->status);
        }

        return $query
            ->with(['sale'])
            ->orderBy('payment_time', 'desc')
            ->paginate($this->perPage);
    }

    private function getStockMovementsData()
    {
        $query = StockMovement::whereIn('type', ['damage', 'shrinkage']);

        if ($this->status !== 'all') {
            $query->where('gl_posting_status', $this->status);
        }

        return $query
            ->with(['stock'])
            ->orderBy('movement_date', 'desc')
            ->paginate($this->perPage);
    }

    public function retryFailed(string $transactionType, int $transactionId)
    {
        $model = match ($transactionType) {
            'sales' => Sale::find($transactionId),
            'purchases' => Purchase::find($transactionId),
            'payments' => Payment::find($transactionId),
            'adjustments' => StockMovement::find($transactionId),
            default => null,
        };

        if (! $model || $model->gl_posting_status !== 'failed') {
            session()->flash('error', 'Transaction not found or not in failed state');

            return;
        }

        // Reset to pending - observer will retry on update
        $model->update(['gl_posting_status' => 'pending']);
        session()->flash('message', 'Posting retry initiated. Refresh to see updated status.');
    }

    public function changeTransactionType(string $type)
    {
        $this->transactionType = $type;
        $this->resetPage();
    }

    public function changeStatus(string $status)
    {
        $this->status = $status;
        $this->resetPage();
    }
}
