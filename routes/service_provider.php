
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestServiceController;



Route::middleware('role:service_provider')->group(function () {
    Route::put('/requests/{requestService}', [RequestServiceController::class, 'update'])->name('requests.update-status');
    Route::get('/services/show/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');


});