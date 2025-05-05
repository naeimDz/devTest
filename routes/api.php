
<?php
// path to: routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\RequestServiceController;
use App\Http\Controllers\Api\Auth\RegisteredUserApiTokenController;
use App\Http\Controllers\Api\Auth\AuthenticatedApiTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
// public routes
Route::get('/services', [ServiceController::class, 'indexPublic']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/requests', [RequestServiceController::class, 'index']);
});



Route::post('/register', [RegisteredUserApiTokenController::class, 'register'])->name('register');
Route::post('/login', [AuthenticatedApiTokenController::class, 'login'])->name('login');
Route::post('/logout', [AuthenticatedApiTokenController::class, 'logout'])->name('logout');