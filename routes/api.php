<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// 📦 Route publique pour voir la liste des plats
Route::get('/plats', [PlatController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/plats', [PlatController::class, 'store']);
    Route::get('/plats/{id}', [PlatController::class, 'show']);
    Route::put('/plats/{id}', [PlatController::class, 'update']);
    Route::delete('/plats/{id}', [PlatController::class, 'destroy']);
});
