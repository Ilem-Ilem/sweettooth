<?php

namespace App\Livewire\BranchDashboard\Accounting;

use App\Models\AccountingPeriod;
use App\Models\GlAccount;
use App\Models\GlEntry;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.app.branch-dashboard')]
class ManualJournalEntry extends Component
{
    public string $reference = '';
    public ?int $periodId = null;
    public string $description = '';
    public string $entryDate = '';
    public string $status = 'draft';
    public array $lines = [
        ['account_id' => null, 'debit' => 0, 'credit' => 0, 'description' => ''],
        ['account_id' => null, 'debit' => 0, 'credit' => 0, 'description' => ''],
    ];

    public function mount()
    {
        $this->entryDate = now()->format('Y-m-d');
        $this->periodId = AccountingPeriod::where('status', 'open')
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now())
            ->first()?->id;
    }

    public function addLine()
    {
        $this->lines[] = ['account_id' => null, 'debit' => 0, 'credit' => 0, 'description' => ''];
    }

    public function removeLine($index)
    {
        if (count($this->lines) > 2) {
            unset($this->lines[$index]);
            $this->lines = array_values($this->lines);
        }
    }

    #[Computed]
    public function periods()
    {
        return AccountingPeriod::where('status', 'open')
            ->orderBy('period_end', 'desc')
            ->get();
    }

    #[Computed]
    public function glAccounts()
    {
        return GlAccount::where('is_active', true)
            ->orderBy('account_number')
            ->get(['id', 'account_number', 'account_name', 'account_type']);
    }

    #[Computed]
    public function totalDebits()
    {
        return array_sum(array_column($this->lines, 'debit'));
    }

    #[Computed]
    public function totalCredits()
    {
        return array_sum(array_column($this->lines, 'credit'));
    }

    #[Computed]
    public function isBalanced()
    {
        return abs($this->totalDebits - $this->totalCredits) < 0.01;
    }

    public function submit()
    {
        $this->validate([
            'reference' => 'required|string|max:100|unique:gl_entries,reference',
            'periodId' => 'required|exists:accounting_periods,id',
            'description' => 'required|string|max:500',
            'entryDate' => 'required|date',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:gl_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'status' => 'in:draft,posted',
        ]);

        if (!$this->isBalanced) {
            throw ValidationException::withMessages(['lines' => 'Journal entry must be balanced (Debits = Credits)']);
        }

        try {
            $period = AccountingPeriod::findOrFail($this->periodId);

            foreach ($this->lines as $line) {
                if ($line['account_id']) {
                    GlEntry::create([
                        'accounting_period_id' => $period->id,
                        'gl_account_id' => $line['account_id'],
                        'reference' => $this->reference,
                        'debit' => (float) ($line['debit'] ?? 0),
                        'credit' => (float) ($line['credit'] ?? 0),
                        'description' => $line['description'] ?: $this->description,
                        'entry_date' => $this->entryDate,
                        'status' => $this->status,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            session()->flash('success', 'Journal entry created successfully');
            $this->reset();
            $this->redirect(route('branch-dashboard.accounting.index'));
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['general' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.accounting.manual-journal-entry');
    }
}
