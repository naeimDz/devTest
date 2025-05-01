<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



Route::middleware('role:admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
});