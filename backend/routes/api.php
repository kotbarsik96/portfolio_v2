<?php

use App\Http\Controllers\WorksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['json.response'])->group(function () {
    Route::get('/works', [WorksController::class, 'index']);
    Route::get('/work/{id}', [WorksController::class, 'show']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/check-auth', [AuthController::class, 'checkAuth']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/work', [WorksController::class, 'store']);
    Route::put('/work/{id}', [WorksController::class, 'update']);
    Route::delete('/work/{id}', [WorksController::class, 'destroy']);
});