<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\VillaController;
use Illuminate\Http\Request;
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

Route::get('/villas', [VillaController::class, 'index']);
Route::post('/create-villa', [VillaController::class, 'store']);
Route::get('/villa/{slug}', [VillaController::class, 'getVillaBySlug']);
Route::post('/villa/{slug}', [VillaController::class, 'update']);
Route::get('/featured_villas', [VillaController::class, 'getFeaturedVillas']);
Route::delete('/villa/{slug}', [VillaController::class, 'destroy']);

Route::post('/contact', [ContactController::class, 'submit']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
