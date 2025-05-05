
<?php
// path to: routes/api/user.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RequestServiceController;






Route::post('/requests/create/{serviceId}', [RequestServiceController::class, 'store']);