<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccountingPeriod;
use Carbon\Carbon;


#[Layout('components.layouts.app.branch-dashboard')]
class PeriodManagement extends Component
{
    public $periods = [];
    public $selectedPeriod = null;
    public $showCreateForm = false;
    public $createYear = null;
    public $createMonth = null;
    public $periodToClose = null;
    public $closingNotes = '';

    public function mount()
    {
        $this->loadPeriods();
        $this->createYear = now()->year;
        $this->createMonth = now()->month;
    }

    public function loadPeriods()
    {
        $this->periods = AccountingPeriod::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->getDisplayName(),
                    'year' => $p->year,
                    'month' => $p->month,
                    'status' => ucfirst($p->status),
                    'start' => $p->period_start->format('M d, Y'),
                    'end' => $p->period_end->format('M d, Y'),
                    'entries_count' => $p->entries()->where('status', 'posted')->count(),
                ];
            })
            ->toArray();
    }

    public function createPeriod()
    {
        try {
            $year = intval($this->createYear);
            $month = intval($this->createMonth);

            // Validate
            if ($month < 1 || $month > 12) {
                throw new \Exception('Invalid month');
            }

            // Check if period exists
            if (AccountingPeriod::where('year', $year)->where('month', $month)->exists()) {
                throw new \Exception('Period already exists');
            }

            // Create period
            $start = Carbon::createFromDate($year, $month, 1);
            $end = $start->copy()->endOfMonth();

            AccountingPeriod::create([
                'year' => $year,
                'month' => $month,
                'period_start' => $start,
                'period_end' => $end,
                'status' => 'open',
            ]);

            $this->dispatch('success', message: "Period {$start->format('F Y')} created successfully");
            $this->showCreateForm = false;
            $this->loadPeriods();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to create period: ' . $e->getMessage());
        }
    }

    public function closePeriod($periodId)
    {
        try {
            $period = AccountingPeriod::find($periodId);
            
            if (!$period || $period->status !== 'open') {
                throw new \Exception('Period is not open');
            }

            // Verify trial balance is balanced
            $tb = \DB::table('gl_entries')
                ->where('accounting_period_id', $periodId)
                ->where('status', 'posted')
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            if (abs($tb->total_debit - $tb->total_credit) > 0.01) {
                throw new \Exception('Cannot close period - Trial Balance is not balanced');
            }

            $period->update([
                'status' => 'closed',
                'closed_by_id' => auth()->id(),
                'closed_by_type' => get_class(auth()->user()),
                'closed_at' => now(),
                'closing_notes' => $this->closingNotes,
            ]);

            $this->dispatch('success', message: 'Period closed successfully');
            $this->periodToClose = null;
            $this->closingNotes = '';
            $this->loadPeriods();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to close period: ' . $e->getMessage());
        }
    }

    public function lockPeriod($periodId)
    {
        try {
            $period = AccountingPeriod::find($periodId);
            
            if (!$period || $period->status !== 'closed') {
                throw new \Exception('Period must be closed before locking');
            }

            $period->update(['status' => 'locked']);

            $this->dispatch('success', message: 'Period locked successfully');
            $this->loadPeriods();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to lock period: ' . $e->getMessage());
        }
    }

    public function reopenPeriod($periodId)
    {
        try {
            $period = AccountingPeriod::find($periodId);
            
            if (!$period || $period->status !== 'closed') {
                throw new \Exception('Only closed periods can be reopened');
            }

            $period->update([
                'status' => 'open',
                'closed_by_id' => null,
                'closed_by_type' => null,
                'closed_at' => null,
                'closing_notes' => null,
            ]);

            $this->dispatch('success', message: 'Period reopened successfully');
            $this->loadPeriods();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to reopen period: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.period-management');
    }
}
