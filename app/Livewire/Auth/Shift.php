<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{Shift as ShiftModel, Branch, Employee, SalesShift};
use App\Services\CheckExpiredProducts;
use Carbon\Carbon;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.auth')]
class Shift extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public $b_id;

    public $shift_type;
    public $notes;

    // Current shift status
    public $currentShift = null;
    public $hasActiveShift = false;

    public function mount()
    {
        $this->loadCurrentShift();
    }

    public function loadCurrentShift()
    {
        $employee_id = auth('employees')->id();

        // Get today's active shift for this employee
        $this->currentShift = ShiftModel::where('employee_id', $employee_id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->first();

        $this->hasActiveShift = $this->currentShift !== null;
    }

    public function clockIn()
    {
        // Validate the inputs
        $this->validate([
            'shift_type' => 'required|string|in:morning,afternoon,full_time',
        ], [
            'shift_type.required' => 'Please select a shift type',
            'shift_type.in' => 'Invalid shift type selected',
        ]);

        try {
            // Get the Branch
            $branch = Branch::findOrFail($this->b_id);

            // Get the Employee
            $employee = Employee::findOrFail(auth('employees')->id());

            // Check if user already has an active shift today
            $existingShift = ShiftModel::where('employee_id', $employee->id)
                ->where('shift_date', Carbon::today())
                ->where('status', 'active')
                ->first();

            if ($existingShift) {
                $this->toast()->warning('You already have an active shift today!')->send();
                return $this->redirectToDashboard($branch);
            }

            // Create a new shift
            $shift = $this->createShift($branch, $employee);

            // Create SalesShift if employee is in sales department
            $salesShift = null;
            if ($this->isSalesDepartment($employee)) {
                $salesShift = $this->createSalesShift($branch, $employee, $shift);
            }

            // Dispatch event to update header
            $this->dispatch('shift-updated');

            $this->toast()->success('Clocked in successfully! Have a productive shift!')->send();

            // Check for expired products if in sales department
            if ($salesShift) {
                return $this->checkExpiryAndRedirect($branch, $salesShift, $employee);
            }

            return $this->redirectToDashboard($branch);

        } catch (\Exception $e) {
            $this->toast()->error('Error clocking in: ' . $e->getMessage())->send();
        }
    }
    
    public function clockOut()
    {
        try {
            if (!$this->currentShift) {
                $this->toast()->error('No active shift found to clock out!')->send();
                return;
            }

            // Update shift with clock out time
            $this->currentShift->clock_out = Carbon::now();
            $this->currentShift->status = 'closed';
            $this->currentShift->save();

            // Calculate total hours worked
            $totalHours = $this->currentShift->clock_in->diffInHours($this->currentShift->clock_out);
            $totalMinutes = $this->currentShift->clock_in->diffInMinutes($this->currentShift->clock_out) % 60;

            $this->toast()->success("Clocked out successfully! Total time: {$totalHours}h {$totalMinutes}m")->send();

            // Dispatch event to update header
            $this->dispatch('shift-updated');

            // Redirect to dashboard
            $branch = Branch::findOrFail($this->b_id);
            return $this->redirectToDashboard($branch);

        } catch (\Exception $e) {
            $this->toast()->error('Error clocking out: ' . $e->getMessage())->send();
        }
    }

    private function createShift(Branch $branch, Employee $employee)
    {
        // Generate meaningful shift number: SHFT-YYYYMMDD-XXXX
        $date = Carbon::today()->format('Ymd');
        $count = ShiftModel::whereDate('shift_date', Carbon::today())
            ->where('branch_id', $branch->id)
            ->count() + 1;
        $shiftNumber = sprintf('SHFT-%s-%04d', $date, $count);

        $shift = new ShiftModel();
        $shift->branch_id = $branch->id;
        $shift->employee_id = $employee->id;
        $shift->department_id = $employee->department_id;
        $shift->shift_number = $shiftNumber;
        $shift->shift_date = Carbon::today();
        $shift->shift_type = $this->shift_type;
        $shift->clock_in = Carbon::now();
        $shift->clock_out = null;
        $shift->status = 'active';
        $shift->notes = $this->notes;
        $shift->save();

        $this->currentShift = $shift;
        $this->hasActiveShift = true;

        return $shift;
    }

    private function redirectToDashboard(Branch $branch)
    {
        return $this->redirectIntended(
            default: route('branch-dashboard.index', ['b_id' => $branch->id], absolute: false),
            navigate: true
        );
    }

    public function getTotalHoursWorked()
    {
        if (!$this->currentShift || !$this->currentShift->clock_in) {
            return '0h 0m';
        }

        $endTime = $this->currentShift->clock_out ?? Carbon::now();
        $totalMinutes = $this->currentShift->clock_in->diffInMinutes($endTime);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function continueToWork()
    {
        $branch = Branch::findOrFail($this->b_id);
        return $this->redirectToDashboard($branch);
    }

    /**
     * Check if employee is in sales department
     */
    private function isSalesDepartment(Employee $employee): bool
    {
        // Assuming sales department name contains "sales" or has a specific ID
        // Adjust this logic based on your department naming convention
        return $employee->department &&
               (stripos($employee->department->name, 'Sales') !== false ||
                stripos($employee->department->name, 'cashier') !== false);
    }

    /**
     * Create a SalesShift record for sales department employees
     */
    private function createSalesShift(Branch $branch, Employee $employee, ShiftModel $shift): SalesShift
    {
        // Generate meaningful sales shift number: SS-YYYYMMDD-XXXX
        $date = Carbon::today()->format('Ymd');
        $count = SalesShift::whereDate('shift_date', Carbon::today())
            ->where('branch_id', $branch->id)
            ->count() + 1;
        $shiftNumber = sprintf('SS-%s-%04d', $date, $count);

        $salesShift = SalesShift::create([
            'branch_id' => $branch->id,
            'department_id' => $employee->department_id,
            'employee_id' => $employee->id,
            'shift_number' => $shiftNumber,
            'shift_date' => Carbon::today(),
            'shift_type' => $this->shift_type,
            'clock_in' => Carbon::now(),
            'status' => 'active',
            'notes' => $this->notes,
        ]);

        return $salesShift;
    }

    /**
     * Check for expired products and redirect accordingly
     */
    private function checkExpiryAndRedirect(Branch $branch, SalesShift $salesShift, Employee $employee)
    {
        $service = new CheckExpiredProducts();

        // Get expired products
        $expiredProducts = $service->getExpiredProductsForShift(
            $salesShift->id,
            $branch->id,
            $employee->department_id
        );

        // If there are expired products, redirect to expiry alerts page
        if ($expiredProducts->isNotEmpty()) {
            return $this->redirect(
                route('branch-dashboard.sales-dashboard.expiry-alerts', [
                    'b_id' => $branch->id,
                    'salesShiftId' => $salesShift->id
                ]),
                navigate: true
            );
        }

        // No expired products, proceed to dashboard
        return $this->redirectToDashboard($branch);
    }

    public function render()
    {
        return view('livewire.auth.shift');
    }
}
