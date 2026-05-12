<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth (solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout vía GET (para el menú de AdminLTE)
Route::get('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Redirección post-login según rol (nombrada 'home' para el middleware guest)
Route::get('/home', [DashboardController::class, 'redirect'])->middleware('auth')->name('home');

// Panel Admin
Route::middleware(['auth', 'role:admin', 'no-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

// Panel Empleado
Route::middleware(['auth', 'role:empleado', 'no-back'])->prefix('empleado')->name('empleado.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'empleado'])->name('dashboard');
});
