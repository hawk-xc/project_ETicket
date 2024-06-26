<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\apis\{

    AuthApisController,
    DeviceApisController,
    // DevicesApisController,
    ProcessApisController,
    TicketApisController,
    ProfileApisController,
    // DeviceApisController
};
use App\Http\Controllers\apis\DevicesApiController;
use App\Http\Controllers\DevicesApiController as ControllersDevicesApiController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', [AuthApisController::class, 'login']);
Route::post('/register', [AuthApisController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/profile', ProfileApisController::class);
    Route::resource('/ticket', TicketApisController::class);
    // Route::resource('/device', DeviceApisController::class);
    Route::put('/passwordupdate', [ProfileApisController::class, 'update_password']);
});

Route::get('/antrean', [TicketApisController::class, 'index'])->middleware('auth:sanctum');
Route::post('/antrean', [TicketApisController::class, 'store'])->middleware('auth:sanctum');
// Route::put('/antrean/{id}', [TicketApisController::class, 'update'])->middleware('auth:sanctum');
Route::put('/antrean/{id}', [TicketApisController::class, 'update']);
Route::delete('/antrean/{id}', [TicketApisController::class, 'destroy'])->middleware('auth:sanctum');
// Route::get('/antrean', [TicketApisController::class, 'index']);
// Route::resource('/devices', [DevicesApiController::class])->middleware('auth:sanctum');
// Route::apiResource('/devices', DevicesApisController::class)->only([
//     'index', 'store', 'show', 'update', 'destroy'
// ])->middleware('auth:sanctum');
Route::apiResource('/device', DeviceApisController::class)->only([
    'index', 'store', 'show', 'update', 'destroy'
])->middleware('auth:sanctum');
Route::apiResource('/proces', ProcessApisController::class)->only([
    'index', 'store', 'show', 'update', 'destroy'
])->middleware('auth:sanctum');

// Route::resource('/device/{id}', DeviceApisController::class);
