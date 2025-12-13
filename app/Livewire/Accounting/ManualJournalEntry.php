<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Livewire\Attributes\Layout;


#[Layout('components.layouts.app.branch-dashboard')]
class ManualJournalEntry extends Component
{
    public $entryDate = null;
    public $description = '';
    public $referenceNumber = '';
    public $remarks = '';
    public $selectedPeriodId = null;
    public $costCenter = '';

    public $lineItems = [];
    public $nextLineId = 1;

    public $showForm = false;
    public $draftEntries = [];

    public function mount()
    {
        $this->entryDate = now()->toDateString();
        $this->loadDraftEntries();
    }

    public function addLineItem()
    {
        $this->lineItems[$this->nextLineId] = [
            'id' => $this->nextLineId,
            'account_id' => '',
            'debit' => 0,
            'credit' => 0,
        ];
        $this->nextLineId++;
    }

    public function removeLineItem($lineId)
    {
        unset($this->lineItems[$lineId]);
    }

    public function calculateTotals()
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($this->lineItems as $item) {
            $totalDebit += floatval($item['debit'] ?? 0);
            $totalCredit += floatval($item['credit'] ?? 0);
        }

        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }

    public function saveDraft()
    {
        try {
            $this->validate([
                'entryDate' => 'required|date',
                'description' => 'required|string|min:5',
                'lineItems' => 'required|array|min:2',
            ]);

            $totals = $this->calculateTotals();

            if (!$totals['balanced']) {
                throw new \Exception('Journal entry must be balanced (debit = credit)');
            }

            // Store as session draft
            session()->push('journal_entry_drafts', [
                'entry_date' => $this->entryDate,
                'description' => $this->description,
                'reference_number' => $this->referenceNumber,
                'remarks' => $this->remarks,
                'cost_center' => $this->costCenter,
                'line_items' => $this->lineItems,
                'created_at' => now(),
            ]);

            $this->dispatch('success', message: 'Draft saved successfully');
            $this->resetForm();
            $this->loadDraftEntries();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to save draft: ' . $e->getMessage());
        }
    }

    public function postEntry()
    {
        try {
            $this->validate([
                'selectedPeriodId' => 'required|exists:accounting_periods,id',
                'entryDate' => 'required|date',
                'description' => 'required|string|min:5',
                'lineItems' => 'required|array|min:2',
            ]);

            $totals = $this->calculateTotals();

            if (!$totals['balanced']) {
                throw new \Exception('Journal entry must be balanced (debit = credit)');
            }

            $period = AccountingPeriod::find($this->selectedPeriodId);
            if (!$period || $period->status !== 'open') {
                throw new \Exception('Selected period is not open for posting');
            }

            \DB::beginTransaction();

            foreach ($this->lineItems as $item) {
                if (empty($item['account_id'])) {
                    continue;
                }

                $account = GlAccount::find($item['account_id']);
                if (!$account) {
                    throw new \Exception('Invalid GL account');
                }

                GlEntry::create([
                    'gl_account_id' => $account->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'manual',
                    'reference_number' => $this->referenceNumber ?: 'MAN-' . now()->timestamp,
                    'description' => $this->description,
                    'debit' => floatval($item['debit'] ?? 0),
                    'credit' => floatval($item['credit'] ?? 0),
                    'entry_date' => Carbon::parse($this->entryDate),
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                    'entered_by_type' => get_class(auth()->user()),
                    'branch_id' => auth()->user()->branch_id ?? null,
                    'cost_center' => $this->costCenter,
                    'remarks' => $this->remarks,
                ]);
            }

            \DB::commit();

            $this->dispatch('success', message: 'Journal entry posted successfully');
            $this->resetForm();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->dispatch('error', message: 'Failed to post entry: ' . $e->getMessage());
        }
    }

    protected function resetForm()
    {
        $this->lineItems = [];
        $this->nextLineId = 1;
        $this->entryDate = now()->toDateString();
        $this->description = '';
        $this->referenceNumber = '';
        $this->remarks = '';
        $this->costCenter = '';
        $this->selectedPeriodId = null;
        $this->showForm = false;
    }

    protected function loadDraftEntries()
    {
        $this->draftEntries = session()->get('journal_entry_drafts', []);
    }

    public function render()
    {
        return view('livewire.accounting.manual-journal-entry', [
            'accounts' => GlAccount::where('is_active', true)
                ->where('allow_manual_entry', true)
                ->orderBy('account_number')
                ->get(),
            'periods' => AccountingPeriod::where('status', 'open')
                ->orderBy('period_start', 'desc')
                ->get(),
            'totals' => $this->calculateTotals(),
        ]);
    }
}
