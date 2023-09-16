<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorksController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SkillsController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/doubleauth', [AuthController::class, 'validate2fa']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/works', [WorksController::class, 'show']);
Route::get('/skills', [SkillsController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // test if user authorized
    Route::post('/check-auth', [AuthController::class, 'checkAuth']);

    // works
    Route::post('/work', [WorksController::class, 'store']);
    Route::put('/work/{id}', [WorksController::class, 'update']);
    Route::delete('/work/{id}', [WorksController::class, 'destroy']);

    // skills
    Route::post('/skill', [SkillsController::class, 'store']);
    Route::put('/skill/{id}', [SkillsController::class, 'update']);
    Route::delete('/skill/{id}', [SkillsController::class, 'destroy']);

    // images
    Route::post('/image', [ImagesController::class, 'store']);
    Route::put('/image/{id}', [ImagesController::class, 'update']);
    Route::delete('/image/{id}', [ImagesController::class, 'destroy']);

    // video
    Route::post('/video', [VideoController::class, 'store']);
    Route::put('/video/{id}', [VideoController::class, 'update']);
    Route::delete('/video/{id}', [VideoController::class, 'destroy']);
});