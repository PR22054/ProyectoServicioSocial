<?php

//rutas principales de la aplicacion, estan agrupadas por autenticacion y el rol
use App\Http\Controllers\AnioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RetencionController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

//redirige la raiz directamente a retencion (acceso publico sin login)
Route::get('/', fn() => redirect()->route('retencion'));

//rutas de autenticacion, solo accesibles para usuarios no autenticados (guest)
Route::middleware('guest')->group(function () {
    Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

//logout via GET para compatibilidad con los enlaces del menu de adminlte
Route::get('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

//ruta home nombrada para que el middleware guest sepa a donde redirigir usuarios autenticados
Route::get('/home', [DashboardController::class, 'redirect'])->middleware('auth')->name('home');

//retencion es publica: no requiere autenticacion
Route::get('retencion', [RetencionController::class, 'index'])
    ->name('retencion');

Route::post('retencion/buscar', [RetencionController::class, 'buscar'])
    ->name('retencion.buscar');

Route::get('retencion/pdf/{token}', [RetencionController::class, 'verPdf'])
    ->name('retencion.pdf.ver');

//rutas exclusivas del rol admin con /admin
Route::middleware(['auth', 'role:admin', 'no-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    //gestion de roles de usuarios
    Route::get('roles',          [RolController::class, 'index'])->name('roles.index');
    Route::patch('roles/{user}', [RolController::class, 'update'])->name('roles.update');

    //CRUD de anos
    Route::get('anios',             [AnioController::class, 'index'])->name('anios.index');
    Route::post('anios',            [AnioController::class, 'store'])->name('anios.store');
    Route::get('anios/{anio}/edit', [AnioController::class, 'edit'])->name('anios.edit');
    Route::patch('anios/{anio}',    [AnioController::class, 'update'])->name('anios.update');
    Route::delete('anios/{anio}',   [AnioController::class, 'destroy'])->name('anios.destroy');

    //CRUD de users (sin la ruta show)
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    //historial de busquedas de NIT/DUI
    Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
});

//rutas exclusivas del rol empleado con prefijo /empleado
Route::middleware(['auth', 'role:empleado', 'no-back'])->prefix('empleado')->name('empleado.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'empleado'])->name('dashboard');
});
