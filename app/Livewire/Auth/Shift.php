<?php

namespace App\Livewire\Auth;

use App\Models\Branch;
use App\Models\SalesShift;
use App\Models\Shift as ShiftModel;
use App\Models\ShiftConfiguration;
use App\Services\CheckExpiredProducts;
use App\Services\ShiftTimingValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.auth')]
class Shift extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $shift_type;

    public $notes;

    // Current shift status
    public $currentShift = null;

    public $hasActiveShift = false;

    public function mount()
    {
        $this->b_id = session('selected_branch_id');
        if (! $this->b_id) {
            // Fallback to first active branch
            $defaultBranch = \App\Models\Branch::where('is_active', 1)->first();
            if ($defaultBranch) {
                $this->b_id = $defaultBranch->id;
                session(['selected_branch_id' => $this->b_id]);
            }
        }
        $this->loadCurrentShift();
    }

    public function loadCurrentShift()
    {
        $user_id = Auth::id();

        // Get today's active shift for this user
        $this->currentShift = ShiftModel::where('employee_id', $user_id)
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

        // Get the authenticated user
        $user = Auth::user();

        try {
            if (! $this->b_id) {
                $this->toast()->error('No branch selected. Please contact administrator.')->send();

                return;
            }
            // Get the Branch
            $branch = Branch::findOrFail($this->b_id);

            // STEP 1: STRICT TIME WINDOW VALIDATION - DISABLED FOR NOW
            $timingValidator = app(ShiftTimingValidator::class);
            /*
            $timeValidation = $timingValidator->validateStrictTimeWindows(
                $this->shift_type,
                $this->b_id
            );

            if (!$timeValidation->isValid()) {
                $this->toast()->error($timeValidation->getMessage())->send();

                // Log the violation attempt
                \Log::warning('Clock-in time violation attempt', [
                    'employee_id' => $user->id,
                    'shift_type' => $this->shift_type,
                    'branch_id' => $this->b_id,
                    'requested_time' => now()->toDateTimeString(),
                    'violation_message' => $timeValidation->getMessage()
                ]);

                return;
            }
            */

            // STEP 2: Check for conflicting shifts
            $conflictValidation = $timingValidator->validateNoConflictingShifts(
                $user->id,
                $this->shift_type,
                $this->b_id
            );

            if (! $conflictValidation->isValid()) {
                $this->toast()->error($conflictValidation->getMessage())->send();

                return;
            }

            // Create a new shift
            $shift = $this->createShift($branch, $user);

            // Create SalesShift if user is in sales department
            $salesShift = null;
            if ($this->isSalesDepartment($user)) {
                $salesShift = $this->createSalesShift($branch, $user, $shift);
            }

            // Dispatch event to update header
            $this->dispatch('shift-updated');

            $this->toast()->success('Clocked in successfully! Have a productive shift!')->send();

            // Check for expired products if in sales department
            if ($salesShift) {
                return $this->checkExpiryAndRedirect($branch, $salesShift, $user);
            }

            return $this->redirectToDashboard($branch);

        } catch (\Exception $e) {
            \Log::error('Clock-in error', [
                'employee_id' => $user->id ?? null,
                'branch_id' => $this->b_id,
                'shift_type' => $this->shift_type,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->toast()->error('Error clocking in: '.$e->getMessage())->send();
        }
    }

    public function clockOut()
    {
        try {
            if (! $this->currentShift) {
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
            \Log::error('Clock-out error: '.$e->getMessage(), [
                'employee_id' => Auth::id(),
                'shift_id' => $this->currentShift->id ?? null,
            ]);
            $this->toast()->error('Error clocking out: '.$e->getMessage())->send();
        }
    }

    private function createShift(Branch $branch, $user)
    {
        // Generate meaningful shift number: SHFT-YYYYMMDD-XXXX
        $date = Carbon::today()->format('Ymd');
        $count = ShiftModel::whereDate('shift_date', Carbon::today())
            ->where('branch_id', $branch->id)
            ->count() + 1;
        $shiftNumber = sprintf('SHFT-%s-%04d', $date, $count);

        // Get shift configuration for reference
        $config = ShiftConfiguration::forBranchAndType($branch->id, $this->shift_type)->first();

        $shift = new ShiftModel;
        $shift->branch_id = $branch->id;
        $shift->employee_id = $user->id;
        // In unified system, department is determined by role, not stored in user
        $shift->department_id = $user->department_id ?? null;
        $shift->shift_number = $shiftNumber;
        $shift->shift_date = Carbon::today();
        $shift->shift_type = $this->shift_type;
        $shift->clock_in = Carbon::now();
        $shift->clock_out = null;
        $shift->status = 'active';
        $shift->notes = $this->notes;

        // Store configuration reference for later use
        if ($config) {
            $shift->metadata = [
                'config_id' => $config->id,
                'expected_end' => $config->end_time,
                'auto_clock_out_minutes' => $config->auto_clock_out_minutes,
            ];
        }

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
        if (! $this->currentShift || ! $this->currentShift->clock_in) {
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
     * Check if user is in sales department (based on role)
     */
    private function isSalesDepartment($user): bool
    {
        // In unified system, determine sales department by role
        return $user->hasAnyRole(['cashier', 'sales-manager']);
    }

    /**
     * Create a SalesShift record for sales department users
     */
    private function createSalesShift(Branch $branch, $user, ShiftModel $shift): SalesShift
    {
        // Generate meaningful sales shift number: SS-YYYYMMDD-XXXX
        $date = Carbon::today()->format('Ymd');
        $count = SalesShift::whereDate('shift_date', Carbon::today())
            ->where('branch_id', $branch->id)
            ->count() + 1;
        $shiftNumber = sprintf('SS-%s-%04d', $date, $count);

        $salesShift = SalesShift::create([
            'branch_id' => $branch->id,
            'department_id' => null, // Department determined by role in unified system
            'employee_id' => $user->id,
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
    private function checkExpiryAndRedirect(Branch $branch, SalesShift $salesShift, $user)
    {
        $service = new CheckExpiredProducts;

        // Get expired products (department determined by role in unified system)
        $departmentId = $this->getDepartmentIdForUser($user);
        $expiredProducts = $service->getExpiredProductsForShift(
            $salesShift->id,
            $branch->id,
            $departmentId
        );

        // If there are expired products, redirect to expiry alerts page
        if ($expiredProducts->isNotEmpty()) {
            return $this->redirect(
                route('branch-dashboard.sales-dashboard.expiry-alerts', [
                    'b_id' => $branch->id,
                    'salesShiftId' => $salesShift->id,
                ]),
                navigate: true
            );
        }

        // No expired products, proceed to dashboard
        return $this->redirectToDashboard($branch);
    }

    /**
     * Get department ID for user based on role (unified system)
     */
    private function getDepartmentIdForUser($user): ?string
    {
        // In unified system, department is determined by role
        // This is a simplified mapping - adjust based on your needs
        if ($user->hasRole('cashier')) {
            // Find sales department
            return \App\Models\Department::where('name', 'like', '%sales%')->value('id');
        }

        return null;
    }

    public function render()
    {
        return view('livewire.auth.shift');
    }
}
