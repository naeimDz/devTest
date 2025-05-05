<?php
// path to: routes/api/admin.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::prefix('users')->group(function () {


    Route::get('/', [UserController::class, 'index']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::patch('/{user}/update-role', [UserController::class, 'updateRole']);

});