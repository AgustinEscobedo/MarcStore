<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfUserIsTypeOne
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario autenticado tiene el rol tipo 1
        if (Auth::check() && Auth::user()->rol === 1) {
            return $next($request);
        }

        // Si no es de tipo 1, retornar error
        return response()->json([
            'message' => 'No tienes permiso para realizar esta acción.'
        ], 403); // Código de estado HTTP 403: Forbidden
    }
}
