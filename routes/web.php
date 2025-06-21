<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PlatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BoissonController;
use App\Http\Controllers\Admin\CommandeController;

Route::get('/', function () {
    return view('auth.login');
});
// la route des connexions
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function (){

    // la route du crud des plats et boisson!
    Route::resource('boissons', BoissonController::class);
    Route::resource('plats', PlatController::class);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/admin', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/commandes', [CommandeController::class, 'index']);
    // Route::get('/commandes/{id}', [CommandeController::class, 'show'])->name('commandes.show');
    // Route::get('/commandes/{id}/edit', [CommandeController::class, 'edit'])->name('commandes.edit');
    // Route::put('/commandes/{id}', [CommandeController::class, 'update'])->name('commandes.update');
    Route::delete('/commandes/{id}', [CommandeController::class, 'destroy'])->name('commandes.destroy');
    Route::resource('/commandes', CommandeController::class);
    Route::put('commandes/{commande}/status', [CommandeController::class, 'updateStatus'])->name('commandes.updateStatus');
});

Route::get('/create-link', function(){
    Artisan::call('storage:link');
    return 'Lien symbolique créé avec succès!';
});
