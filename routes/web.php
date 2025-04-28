<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckRole;

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
Route::post('/services/{serviceId}', [RequestServiceController::class, 'storeForGuest'])->name('services.explore');;
Route::get('/services/explore', [ServiceController::class, 'indexPublic'])->name('services.indexPublic');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/requests', [RequestServiceController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [RequestServiceController::class, 'show'])->name('requests.show');
    Route::get('/services', [ServiceController::class, 'indexAdmin'])->name('services.admin');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/service/{serviceId}/request', [RequestServiceController::class, 'storeForAuthenticatedUser'])->middleware('auth')->name('request.explore');
});

Route::middleware([CheckRole::class . ':admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/roles', [AdminController::class, 'roles']);
});

Route::middleware([CheckRole::class . ':service_provider'])->group(function () {
    Route::get('/services/show/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::put('/requests/{requestService}', [RequestServiceController::class, 'update'])->name('requests.update-status');

});

Route::middleware([CheckRole::class . ':user'])->group(function () {
    //
});


require __DIR__.'/auth.php';
