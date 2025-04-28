<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::post('/services/{serviceId}', [RequestServiceController::class, 'storeForGuest'])->name('services.explore');
Route::get('/services/explore', [ServiceController::class, 'indexPublic'])->name('services.indexPublic');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/requests', [RequestServiceController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [RequestServiceController::class, 'show'])->name('requests.show');
});


Route::middleware([CheckRole::class . ':admin,service_provider'])->group(function () {
    Route::get('/services', [ServiceController::class, 'indexAdmin'])->name('services.admin');
    Route::put('/requests/{requestService}', [RequestServiceController::class, 'update'])->name('requests.update-status');
    Route::put('/services/{id}', [ServiceController::class, 'updateStatus'])->name('services.update-status');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
});

Route::middleware([CheckRole::class . ':admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
});


Route::middleware([CheckRole::class . ':service_provider'])->group(function () {
    Route::get('/services/show/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');


});

Route::middleware([CheckRole::class . ':user'])->group(function () {
    Route::post('/service/{serviceId}/request', [RequestServiceController::class, 'storeForAuthenticatedUser'])->name('request.explore');
    //
});


require __DIR__.'/auth.php';
