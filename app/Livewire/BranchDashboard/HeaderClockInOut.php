<?php

namespace App\Livewire\BranchDashboard;

use Livewire\Component;
use Livewire\Attributes\{On, Url};
use App\Models\{Shift as ShiftModel, Branch, Employee};
use Carbon\Carbon;
use TallStackUi\Traits\Interactions;

class HeaderClockInOut extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $currentShift = null;
    public $hasActiveShift = false;
    public $timeWorked = '0h 0m';

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function loadCurrentShift()
    {
        $employee_id = auth('employees')->id();

        if (!$employee_id) {
            return;
        }

        // Get today's active shift for this employee
        $this->currentShift = ShiftModel::where('employee_id', $employee_id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->first();

        $this->hasActiveShift = $this->currentShift !== null;

        if ($this->hasActiveShift) {
            $this->calculateTimeWorked();
        }
    }

    public function calculateTimeWorked()
    {
        if (!$this->currentShift || !$this->currentShift->clock_in) {
            $this->timeWorked = '0h 0m';
            return;
        }

        $now = Carbon::now();
        $totalMinutes = $this->currentShift->clock_in->diffInMinutes($now);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $this->timeWorked = "{$hours}h {$minutes}m";
    }

    public function redirectToShiftPage()
    {
        $branchId = $this->b_id ?: request()->query('b_id');

        return redirect()->route('branch-dashboard.select_shift', ['b_id' => $branchId]);
    }

    public function clockOut()
    {
        try {
            if (!$this->currentShift) {
                $this->toast()->error('No active shift found!')->send();
                return;
            }

            // Update shift with clock out time
            $this->currentShift->clock_out = Carbon::now();
            $this->currentShift->status = 'closed';
            $this->currentShift->save();

            // Calculate total hours worked
            $totalHours = Carbon::parse($this->currentShift->clock_in)->diffInHours($this->currentShift->clock_out);
            $totalMinutes = Carbon::parse($this->currentShift->clock_in)->diffInMinutes($this->currentShift->clock_out) % 60;

            $this->toast()->success("Clocked out! Total time: {$totalHours}h {$totalMinutes}m")->send();

            // Dispatch event to update other components
            $this->dispatch('shift-updated');

            // Reset state
            $this->currentShift = null;
            $this->hasActiveShift = false;
            $this->timeWorked = '0h 0m';

            // Refresh the component
            $this->loadCurrentShift();

        } catch (\Exception $e) {
            $this->toast()->error('Error clocking out: ' . $e->getMessage())->send();
        }
    }

    #[On('shift-updated')]
    public function refreshShift()
    {
        $this->loadCurrentShift();
    }

    public function render()
    {
        // Update time worked on each render
        if ($this->hasActiveShift) {
            $this->calculateTimeWorked();
        }

        return view('livewire.branch-dashboard.header-clock-in-out');
    }
}
