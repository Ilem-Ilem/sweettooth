<?php

namespace App\Livewire\BranchDashboard\ShiftManagement;

use App\Livewire\BaseComponent;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftConfiguration;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class ShiftCalendar extends BaseComponent
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $currentMonth;
    public $currentYear;
    public $selectedDate;
    public $shiftsByDate = [];
    public $employees = [];
    public $departments = [];
    public $shiftConfigurations = [];

    public $selectedDepartment = null;
    public $selectedEmployee = null;

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->selectedDate = now()->format('Y-m-d');
        
        $this->loadData();
    }

    protected function getModelClass(): string
    {
        return Shift::class;
    }

    protected function getAllSelectableIds(): array
    {
        return [];
    }

    public function loadData()
    {
        $branchId = $this->b_id ?: current_branch_id();

        // Load employees for this branch
        $this->employees = Employee::where('branch_id', $branchId)
            ->when($this->selectedDepartment, function ($query) {
                $query->where('department_id', $this->selectedDepartment);
            })
            ->get();

        // Load departments for this branch
        $this->departments = Department::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhereNull('branch_id');
        })->get();

        // Load shift configurations for this branch
        $this->shiftConfigurations = ShiftConfiguration::where('branch_id', $branchId)->get();

        // Load shifts for the current month
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        $query = Shift::where('branch_id', $branchId)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->with(['employee', 'department', 'configuration']);

        if ($this->selectedEmployee) {
            $query->where('employee_id', $this->selectedEmployee);
        }

        $shifts = $query->get();

        // Group shifts by date
        $this->shiftsByDate = $shifts->groupBy(function ($shift) {
            return $shift->shift_date->format('Y-m-d');
        });
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $date->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadData();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $date->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadData();
    }

    public function goToToday()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function updatedSelectedDepartment()
    {
        $this->loadData();
    }

    public function updatedSelectedEmployee()
    {
        $this->loadData();
    }

    public function getDaysInMonth()
    {
        return Carbon::create($this->currentYear, $this->currentMonth, 1)->daysInMonth;
    }

    public function getStartDayOfMonth()
    {
        return Carbon::create($this->currentYear, $this->currentMonth, 1)->dayOfWeek;
    }

    public function getWeekDays()
    {
        return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    }

    public function getShiftsForDate($date)
    {
        return $this->shiftsByDate->get($date, collect([]));
    }

    public function render()
    {
        $branch = current_branch();
        $this->loadData();

        return view('livewire.branch-dashboard.shift-management.shift-calendar', [
            'branch' => $branch,
        ]);
    }
}