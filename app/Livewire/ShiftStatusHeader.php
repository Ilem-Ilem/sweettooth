<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Shift;
use App\Services\ShiftNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShiftStatusHeader extends Component
{
    public $hasActiveShift = false;
    public $shiftType = null;
    public $timeWorked = '0h 0m';
    public $shiftWarnings = false;
    public $shiftEndingSoon = false;
    public $autoClockOutWarning = false;
    public $minutesToEnd = 0;
    public $minutesToAutoClock = 0;

    protected $listeners = [
        'shift-updated' => 'refreshShiftStatus',
        'refresh-shift-status' => 'refreshShiftStatus'
    ];

    public function mount()
    {
        $this->refreshShiftStatus();
    }

    public function refreshShiftStatus()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Get active shift
        $activeShift = Shift::where('employee_id', $user->id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->with('configuration')
            ->first();

        if ($activeShift) {
            $this->hasActiveShift = true;
            $this->shiftType = ucfirst(str_replace('_', ' ', $activeShift->shift_type));

            // Calculate time worked
            if ($activeShift->clock_in) {
                $endTime = $activeShift->clock_out ?? Carbon::now();
                $totalMinutes = $activeShift->clock_in->diffInMinutes($endTime);
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $this->timeWorked = "{$hours}h {$minutes}m";
            }

            // Check for warnings
            $this->checkShiftWarnings($activeShift);
        } else {
            $this->hasActiveShift = false;
            $this->shiftType = null;
            $this->timeWorked = '0h 0m';
            $this->shiftWarnings = false;
            $this->shiftEndingSoon = false;
            $this->autoClockOutWarning = false;
        }
    }

    protected function checkShiftWarnings(Shift $shift)
    {
        if (!$shift->configuration) {
            $this->shiftWarnings = false;
            return;
        }

        $config = $shift->configuration;
        $now = Carbon::now();

        // Calculate expected end time
        $expectedEnd = Carbon::createFromTimeString($config->end_time)
            ->setDateFrom($shift->shift_date);

        // Calculate auto clock out time
        $autoClockOutTime = $expectedEnd->copy()->addMinutes($config->auto_clock_out_minutes);

        $minutesToEnd = $now->diffInMinutes($expectedEnd, false);
        $minutesToAutoClock = $now->diffInMinutes($autoClockOutTime, false);

        // Check if warnings are needed
        $this->shiftEndingSoon = $minutesToEnd > 0 && $minutesToEnd <= 30;
        $this->autoClockOutWarning = $minutesToAutoClock > 0 && $minutesToAutoClock <= 10;
        $this->shiftWarnings = $this->shiftEndingSoon || $this->autoClockOutWarning;

        $this->minutesToEnd = max(0, $minutesToEnd);
        $this->minutesToAutoClock = max(0, $minutesToAutoClock);
    }

    public function clockOut()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $activeShift = Shift::where('employee_id', $user->id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            $activeShift->clock_out = Carbon::now();
            $activeShift->status = 'closed';
            $activeShift->save();

            // Send completion notification
            $notificationService = app(ShiftNotificationService::class);
            $notificationService->sendShiftCompletedNotification($activeShift, 'manual');

            $this->dispatch('shift-updated');
            $this->refreshShiftStatus();

            session()->flash('success', 'Successfully clocked out!');
        }
    }

    public function render()
    {
        return view('livewire.shift-status-header');
    }
}
