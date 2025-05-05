<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PlatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BoissonController;
use App\Http\Controllers\Admin\CommandeController;

Route::get('/', function () {
    return view('auth.login');
});

Route::resource('boissons', BoissonController::class);
Route::resource('plats', PlatController::class);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin', [AdminController::class, 'index'])->name('dashboard');
Route::get('/commandes', [CommandeController::class, 'index']);
Route::resource('/commandes', CommandeController::class);