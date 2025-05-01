<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\UserController;
use App\Notifications\goNotification;
use App\Http\Controllers\NotificationController;

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

Route::get('Errors/Blocked', function () {return Inertia::render('Blocked');})->name('blocked');

Route::middleware([ 'auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-notification', function () {
    $user = \App\Models\User::find(22);
    $user->notify(new \App\Notifications\goNotification());

    return "Notification sent!";
});
Route::middleware('auth')->group(function () {
    Route::get('/requests', [RequestServiceController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [RequestServiceController::class, 'show'])->name('requests.show');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
});
Route::get('/notifications-test', function () {
    try {
        event(new \App\Events\UserRoleUpdated(22, 1));
    
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }
    return "Notification sent! ";
    
});

Route::get('/notifications/test', function () {
    $user = \App\Models\User::find(22);
    $user->notify(new \App\Notifications\goNotification());

    return "Notification sent!";
});

Route::middleware([CheckRole::class . ':admin,service_provider'])->group(function () {
    Route::get('/services', [ServiceController::class, 'indexAdmin'])->name('services.admin');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
});





Route::middleware([CheckRole::class . ':user'])->group(function () {
    Route::post('/service/{serviceId}', [RequestServiceController::class, 'storeForAuthenticatedUser'])->name('request.explore');
    //
});


require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/service_provider.php';
