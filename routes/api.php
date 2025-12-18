<?php


use App\Http\Controllers\HealthController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

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

Route::get('health', [HealthController::class, 'check']);

Route::get('/metrics', [MetricsController::class, 'index']);

Route::get('/search/{type}', [SearchController::class, 'search']);
Route::get('/details/{type}/{id}', [SearchController::class, 'details']);


