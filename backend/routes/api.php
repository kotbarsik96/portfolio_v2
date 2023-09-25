<?php

use App\Http\Controllers\TaxonomiesController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorksController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\DataController;

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

Route::get('/works', [WorksController::class, 'all']);
Route::get('/works/filter', [WorksController::class, 'allFiltered']);
Route::get('/work/{id}', [WorksController::class, 'single']);
Route::get('/works/count', [WorksController::class, 'count']);

Route::get('/skills', [SkillsController::class, 'all']);
Route::get('/skill/{id}', [SkillsController::class, 'single']);

Route::get('/taxonomies/{title}', [TaxonomiesController::class, 'all']);

Route::get('/alldata', [DataController::class, 'all']);

Route::middleware('auth:sanctum')->group(function () {
    // test if user is authorized
    Route::post('/check-auth', [AuthController::class, 'checkAuth']);

    // works
    Route::post('/work', [WorksController::class, 'store']);
    Route::post('/work/{id}', [WorksController::class, 'update']);
    Route::delete('/work/{id}', [WorksController::class, 'destroy']);

    // skills
    Route::post('/skill', [SkillsController::class, 'store']);
    Route::post('/skill/{id}', [SkillsController::class, 'update']);
    Route::delete('/skill/{id}', [SkillsController::class, 'destroy']);

    // images
    Route::post('/image', [ImagesController::class, 'store']);
    Route::post('/image/{id}', [ImagesController::class, 'update']);
    Route::delete('/image/{id}', [ImagesController::class, 'destroy']);

    // video
    Route::post('/video-upload', [VideoController::class, 'upload']);
    Route::post('/video/{id}', [VideoController::class, 'update']);
    Route::delete('/video/{id}', [VideoController::class, 'destroy']);

    // taxonomies
    Route::post('/taxonomy/{taxonomyTitle}', [TaxonomiesController::class, 'store']);
    Route::post('/taxonomy/{taxonomyTitle}/{id}', [TaxonomiesController::class, 'update']);
    Route::post('/taxonomies/{taxonomyTitle}', [TaxonomiesController::class, 'updateSeveral']);
    Route::delete('/taxonomy/{taxonomyTitle}/{id}', [TaxonomiesController::class, 'destroy']);
});