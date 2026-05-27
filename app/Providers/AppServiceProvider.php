<?php

namespace App\Providers;

//proveedor de servicios principal de la aplicacion
use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin-access', fn($user) => $user->hasRole('admin'));

        //gate para mostrar items de sesion solo a usuarios autenticados
        //cuando no hay sesion Gate::check retorna false y el item queda oculto
        Gate::define('auth-access', fn($user) => true);
    }
}
