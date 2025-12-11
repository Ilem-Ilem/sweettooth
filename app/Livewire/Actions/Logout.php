<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     * Handles both super admin (web guard) and employee (employees guard) logout
     */
    public function __invoke()
    {
        // Logout from both guards
        Auth::guard('web')->logout();
        Auth::guard('employees')->logout();

        // Destroy all session data
        Session::flush();
        
        // Regenerate token to prevent CSRF attacks
        Session::regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully');
    }
}
