<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('roles', Index::class)->name('roles.index');
    Route::get('roles/{role}/permissions', Permissions::class)->name('roles.permissions');
    Route::get(
        '/super-admin/branches',
        \App\Livewire\SuperAdmin\BranchModule\Index::class
    )->name('branches.index');

    // Route::get(
    //     '/super-admin/branches/delete/{branch}',
    //     \App\Livewire\SuperAdmin\BranchModule\DeleteBranch::class
    // )->name('branches.delete');
});
