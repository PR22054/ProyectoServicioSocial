<?php

namespace App\Http\Middleware;

//middleware que agrega cabeceras HTTP para evitar que el navegador
//almacene en cache las paginas autenticadas e impida el regreso con el boton atras
use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }
}
