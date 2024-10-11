<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Redirigir dependiendo del rol del usuario
            if ($user->role === 'administrador' && !$request->routeIs('dashboard')) {
                return redirect()->route('dashboard');
            } elseif ($user->role === 'cliente' && !$request->routeIs('home')) {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
