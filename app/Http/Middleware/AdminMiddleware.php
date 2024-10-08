<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()){
            if (Auth::user()->role === 'administrador') {
                return $next($request);
            }else{
                // Redirigir o mostrar un mensaje de acceso denegado
                return redirect('/')->with('error', 'Acceso denegado.');
            }
        }

        return $next($request);
    }
}
