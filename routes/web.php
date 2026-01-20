<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', App\Livewire\Auth\LoginOption::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Leave Management routes accessible to all authenticated users
    Route::prefix('leave')->name('leave.')->group(function () {
        Route::get('/apply', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ApplyLeave::class)->name('apply');
        Route::get('/my-leaves', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\MyLeaves::class)->name('my-leaves');
        Route::get('/balance', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\LeaveBalance::class)->name('balance');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/branch-route.php';
