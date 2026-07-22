<?php

//rutas principales de la aplicacion, estan agrupadas por autenticacion y el rol
use App\Http\Controllers\AnioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RetencionController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Especies\BodegaController;
use App\Http\Controllers\Especies\CompraController as EspecieCompraController;
use App\Http\Controllers\Especies\ConfiguracionController;
use App\Http\Controllers\Especies\DistritoController as EspecieDistritoController;
use App\Http\Controllers\Especies\RealizacionController as EspecieRealizacionController;
use App\Http\Controllers\Especies\ReporteController as EspecieReporteController;
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

//rutas exclusivas del rol admin: gestion de usuarios y roles
Route::middleware(['auth', 'role:admin', 'no-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('roles',          [RolController::class, 'index'])->name('roles.index');
    Route::patch('roles/{user}', [RolController::class, 'update'])->name('roles.update');

    Route::resource('usuarios', UsuarioController::class)->except(['show']);
});

//rutas compartidas entre admin y empleado bajo el prefijo /admin
Route::middleware(['auth', 'role:admin|empleado', 'no-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    //CRUD de anos
    Route::get('anios',             [AnioController::class, 'index'])->name('anios.index');
    Route::post('anios',            [AnioController::class, 'store'])->name('anios.store');
    Route::get('anios/{anio}/edit', [AnioController::class, 'edit'])->name('anios.edit');
    Route::patch('anios/{anio}',    [AnioController::class, 'update'])->name('anios.update');
    Route::delete('anios/{anio}',   [AnioController::class, 'destroy'])->name('anios.destroy');

    //especies municipales: configuracion, compras, bodega, distritos, realizaciones y reportes
    Route::prefix('especies')->name('especies.')->group(function () {
        Route::get('configuracion/tipos',                        [ConfiguracionController::class, 'tipos'])->name('configuracion.tipos');
        Route::post('configuracion/tipos',                       [ConfiguracionController::class, 'storeTipo'])->name('configuracion.tipos.store');
        Route::patch('configuracion/tipos/{tipo}',               [ConfiguracionController::class, 'updateTipo'])->name('configuracion.tipos.update');
        Route::delete('configuracion/tipos/{tipo}',              [ConfiguracionController::class, 'destroyTipo'])->name('configuracion.tipos.destroy');

        Route::get('configuracion/denominaciones',               [ConfiguracionController::class, 'denominaciones'])->name('configuracion.denominaciones');
        Route::post('configuracion/denominaciones',              [ConfiguracionController::class, 'storeDenominacion'])->name('configuracion.denominaciones.store');
        Route::patch('configuracion/denominaciones/{denominacion}', [ConfiguracionController::class, 'updateDenominacion'])->name('configuracion.denominaciones.update');
        Route::delete('configuracion/denominaciones/{denominacion}',[ConfiguracionController::class, 'destroyDenominacion'])->name('configuracion.denominaciones.destroy');

        Route::get('ajax/denominaciones',              [ConfiguracionController::class,      'ajaxDenominaciones'])->name('ajax.denominaciones');

        Route::get('compras',                          [EspecieCompraController::class,      'historial'])->name('compras.historial');
        Route::get('compras/crear',                    [EspecieCompraController::class,      'crear'])->name('compras.crear');
        Route::post('compras',                         [EspecieCompraController::class,      'store'])->name('compras.store');
        Route::get('compras/{compra}',                 [EspecieCompraController::class,      'show'])->name('compras.show');
        Route::get('compras/{compra}/lotes/crear',     [EspecieCompraController::class,      'crearLote'])->name('compras.lotes.crear');
        Route::post('compras/{compra}/lotes',          [EspecieCompraController::class,      'storeLote'])->name('compras.lotes.store');
        Route::delete('compras/{compra}/lotes/{lote}', [EspecieCompraController::class,      'destroyLote'])->name('compras.lotes.destroy');

        Route::get('bodega/traslados/registrar',   [BodegaController::class,             'trasladoRegistrar'])->name('bodega.traslado.registrar');
        Route::get('bodega/traslados',             [BodegaController::class,             'trasladoHistorial'])->name('bodega.traslado.historial');
        Route::get('bodega/stock',                 [BodegaController::class,             'stock'])->name('bodega.stock');

        Route::get('distritos/anulaciones/registrar', [EspecieDistritoController::class, 'anulacionRegistrar'])->name('distritos.anulaciones.registrar');
        Route::get('distritos/stock',                 [EspecieDistritoController::class, 'stock'])->name('distritos.stock');

        Route::get('realizaciones/registrar',      [EspecieRealizacionController::class, 'registrar'])->name('realizaciones.registrar');

        Route::get('reportes/libro',               [EspecieReporteController::class,     'libro'])->name('reportes.libro');
        Route::get('reportes/bodega',              [EspecieReporteController::class,     'bodega'])->name('reportes.bodega');
        Route::get('reportes/distritos',           [EspecieReporteController::class,     'distritos'])->name('reportes.distritos');
        Route::get('reportes/realizaciones',       [EspecieReporteController::class,     'realizaciones'])->name('reportes.realizaciones');
        Route::get('reportes/traslados',           [EspecieReporteController::class,     'traslados'])->name('reportes.traslados');
        Route::get('reportes/mensual',             [EspecieReporteController::class,     'mensual'])->name('reportes.mensual');
    });

    //reporte de consultas: formulario, generacion de PDF y visualizacion
    Route::get('consultas',             [ConsultaController::class, 'index'])->name('consultas.index');
    Route::post('consultas/generar',    [ConsultaController::class, 'generarPdf'])->name('consultas.generar');
    Route::get('consultas/pdf/{token}', [ConsultaController::class, 'verPdf'])->name('consultas.pdf.ver');
});

//rutas del portal visitante (solo dashboard basico, retencion es publica)
Route::middleware(['auth', 'role:visitante', 'no-back'])->prefix('empleado')->name('empleado.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'empleado'])->name('dashboard');
});
