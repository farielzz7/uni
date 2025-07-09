<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\TuristaController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\AuthController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


// Roles Routes

Route::get('/roles', [RolController::class, 'index']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::post('/roles', [RolController::class, 'store']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::patch('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);

// Permisos Routes

Route::get('/permisos', [PermisoController::class, 'index']);
Route::get('/permisos/{id}', [PermisoController::class, 'show']);
Route::post('/permisos', [PermisoController::class, 'store']);
Route::put('/permisos/{id}', [PermisoController::class, 'update']);
Route::patch('/permisos/{id}', [PermisoController::class, 'update']);
Route::delete('/permisos/{id}', [PermisoController::class, 'destroy']);

// Turistas Routes

    Route::get('/turistas', [TuristaController::class, 'index']);
    Route::post('/turistas', [TuristaController::class, 'store']);
    Route::get('/turistas/{id}', [TuristaController::class, 'show']);
    Route::put('/turistas/{id}', [TuristaController::class, 'update']);
    Route::delete('/turistas/{id}', [TuristaController::class, 'destroy']);

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});
// Servicios Routes

    Route::get('/servicios', [ServicioController::class, 'index']);
    Route::get('/servicios/{id}', [ServicioController::class, 'show']);
    Route::post('/servicios', [ServicioController::class, 'store']);
    Route::put('/servicios/{id}', [ServicioController::class, 'update']);
    Route::delete('/servicios/{id}', [ServicioController::class, 'destroy']);

  // Destino Routes

    Route::get('/destino', [DestinoController::class, 'index']);
    Route::get('/destino/{id}', [DestinoController::class, 'show']);
    Route::post('/destino', [DestinoController::class, 'store']);
    Route::put('/destino/{id}', [DestinoController::class, 'update']);
    Route::delete('/destino/{id}', [DestinoController::class, 'destroy']);

// Hoteles Routes
Route::get('hoteles', [HotelController::class, 'index']);
Route::post('hoteles', [HotelController::class, 'store']);
Route::put('hoteles/{id}', [HotelController::class, 'update']);
Route::delete('hoteles/{id}', [HotelController::class, 'destroy']);