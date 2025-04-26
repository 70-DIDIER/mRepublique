<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\BoissonController;
use App\Http\Controllers\CommandeController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// 📦 Route publique pour voir la liste des plats
Route::get('/plats', [PlatController::class, 'index']);
Route::get('/boissons', [BoissonController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/plats', [PlatController::class, 'store']);
    Route::get('/plats/{id}', [PlatController::class, 'show']);
    Route::put('/plats/{id}', [PlatController::class, 'update']);
    Route::delete('/plats/{id}', [PlatController::class, 'destroy']);
    // 📦 Routes pour les boissons
    Route::post('/boissons', [BoissonController::class, 'store']);
    Route::get('/boissons/{id}', [BoissonController::class, 'show']);   
    Route::put('/boissons/{id}', [BoissonController::class, 'update']);
    Route::delete('/boissons/{id}', [BoissonController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    
});
// la route pour faire les commandes
Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::get('/commandes/{id}', [CommandeController::class, 'show']);
});