<?php
// path to: routes/api/service_provider.php
use App\Http\Controllers\Api\RequestServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;


Route::post('/services', [ServiceController::class, 'store']);
Route::put('/requests/{requestService}', [RequestServiceController::class, 'update']);