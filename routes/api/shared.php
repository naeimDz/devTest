<?php
// // path to: routes/api/public.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

/**
 * API Routes for Shared Services
 * 
 * This file contains the API routes that are shared across the application
 * Base path: api/shared/services
 * 
 * Routes:
 * - GET    /services          - List all services (admin view)
 * - PUT    /services/{service}- Update a specific service
 * - DELETE /services/{id}     - Delete a specific service
 * 
 * @package App\Routes\Api
 */

// path route ==> api/shared/services

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'indexAdmin']);
    Route::put('/{service}', [ServiceController::class, 'update']);
    Route::delete('/{id}', [ServiceController::class, 'destroy']);
});