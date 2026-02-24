<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $rol)
    {
        // Si el usuario no está logueado o su rol no coincide con el necesario
        if (!auth()->check() || auth()->user()->rol !== $rol) {
            // Lo mandamos al dashboard con un mensaje de error
            return redirect('/dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
        } 

        return $next($request);
    }
}