<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\BoissonController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\LivraisonController;

Route::post('/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// 📦 Route publique pour voir la liste des plats
Route::get('/plats', [PlatController::class, 'index']);
Route::get('/boissons', [BoissonController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:client|admin'])->group(function () {
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

    // 📦 Routes pour les commandes
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::get('/commandes/{id}', [CommandeController::class, 'show']);

    // les routes pour demander un paiement
    Route::post('/paiements', [PaiementController::class, 'store']);
    Route::put('/paiements/{id}', [PaiementController::class, 'updateStatus']);
    Route::get('/paiements/check/{id}', [PaiementController::class, 'check']);
    Route::get('/paiements/commande/{commandeId}', [PaiementController::class, 'show']);
    // Webhook (callback automatique de PayGate)
    Route::post('/paygate/callback', [PaiementController::class, 'callback']);

   
    
});
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/livraisons', [LivraisonController::class, 'toutes']); // admin

});
// // la route pour faire les commandes
Route::middleware(['auth:sanctum', 'role:livreur'])->group(function () {
     // 📦 Routes pour les livraisons
     Route::post('/livraisons/prendre/{commandeId}', [LivraisonController::class, 'prendre']);
     Route::post('/livraisons/livrer/{id}', [LivraisonController::class, 'livrer']);
     Route::get('/livraisons/mes', [LivraisonController::class, 'mesLivraisons']);
});

// // les routes pour demander un paiement

