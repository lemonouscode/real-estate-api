<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SavedVillaController;
use App\Http\Controllers\UserController;
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
Route::post('/create-villa', [VillaController::class, 'store'])->middleware('isAdmin');
Route::get('/villa/{slug}', [VillaController::class, 'getVillaBySlug']);
Route::post('/villa/{slug}', [VillaController::class, 'update'])->middleware('isAdmin');
Route::get('/featured_villas', [VillaController::class, 'getFeaturedVillas']);
Route::delete('/villa/{slug}', [VillaController::class, 'destroy'])->middleware('isAdmin');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/contact', [ContactController::class, 'submit']);
Route::get('/contact', [ContactController::class, 'index'])->middleware('isAdmin');

Route::get('/saved-villas/{id}', [UserController::class, 'show']);
Route::post('/save-villa', [UserController::class, 'store']);
Route::delete('/saved-villa/{id}', [SavedVillaController::class, 'destroy'])->middleware('isLogged');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
