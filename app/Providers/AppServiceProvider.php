<?php

namespace App\Providers;

//proveedor de servicios principal de la aplicacion
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //define el Gate admin-access que usa el filtro de menu de AdminLTE
        //para mostrar u ocultar items del sidebar segun el rol del usuario
        Gate::define('admin-access',  fn($user) => $user->hasRole('admin'));

        // admin y empleado comparten dashboard, anos, especies y consultas
        Gate::define('shared-access', fn($user) => $user->hasRole('admin') || $user->hasRole('empleado'));

        //gate para mostrar items de sesion solo a usuarios autenticados
        //cuando no hay sesion Gate::check retorna false y el item queda oculto
        Gate::define('auth-access',   fn($user) => true);

        //los enlaces de paginacion usan el estilo de Bootstrap 4, que es el de AdminLTE
        Paginator::useBootstrapFour();

        //la busqueda publica de constancias se limita por IP para impedir probar NIT/DUI en masa;
        //el personal con sesion iniciada genera constancias sin limite
        RateLimiter::for('retencion', fn(Request $request) => $request->user()
            ? Limit::none()
            : Limit::perMinute(20)->by($request->ip())->response(fn() => back()->withInput()
                ->with('buscar_error', 'Demasiadas búsquedas desde esta conexión. Espere un minuto e intente de nuevo.')));
    }
}
