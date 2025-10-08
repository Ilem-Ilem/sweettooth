<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:employees', 'branch'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    Route::get('/', App\Livewire\BranchDashboard\Index::class)->name('branch_dashboard');
});
