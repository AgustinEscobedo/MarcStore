<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckPermissions
{
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        $user = $request->user();

        if (!$user || ($permission && !$user->hasPermission($permission))) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}