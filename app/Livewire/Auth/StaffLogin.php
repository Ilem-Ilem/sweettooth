<?php

namespace App\Livewire\Auth;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class StaffLogin extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('required|uuid|exists:branches,id')]
    public string $branch_id = '';

    public bool $remember = false;

    public function render()
    {
        return view('livewire.auth.staff-login', [
            'branches' => Branch::where('is_active', true)->get(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */

// In login() method (building on the no-2FA version above):
public function login(): void
{
    $this->validate();

    $this->ensureIsNotRateLimited();

    $employee = $this->validateCredentials();
    // Use the custom guard
    Auth::guard('employees')->login($employee, $this->remember);
    // Store branch context in session
    Session::put('branch_id', $this->branch_id);

    RateLimiter::clear($this->throttleKey());
    Session::regenerate();

  $this->redirectIntended(
        default: route('branch_dashboard', ['b_id' => $this->branch_id], absolute: false),
        navigate: true
    );
}

// Update validateCredentials() to use the guard's provider:
protected function validateCredentials(): Employee
{
    // Retrieve employee by email and check password
    $employee = Employee::where('email', $this->email)->first();

        if (! $employee || ! Auth::guard('employees')->getProvider()->validateCredentials($employee, [
            'email' => $this->email,
            'password' => $this->password
        ])) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    // Validate branch_id matches employee's branch
    if ($employee->branch_id !== $this->branch_id) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'branch_id' => __('auth.invalid_branch'),
        ]);
    }

    return $employee;
}

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . $this->branch_id . '|' . request()->ip());
    }
}
