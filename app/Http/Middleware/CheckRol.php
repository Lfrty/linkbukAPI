<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol {
    /**
     * Maneja una petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Lista de roles permitidos
     */
    public function handle(Request $request, Closure $next, ...$roles): Response {
        // Usuario autenticado por Sanctum
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        foreach ($roles as $rol) {

            if ($rol === 'admin' && $user->esAdmin()) {
                return $next($request);
            }
            if ($rol === 'supervisor' && $user->esSupervisor()) {
                return $next($request);
            }
            // Si el rol_id 3 es usuario normal
            if ($rol === 'usuario' && $user->rol_id === 3) {
                return $next($request);
            }
        }

        return response()->json([
             'error' => 'Acceso denegado: No tienes permisos suficientes para realizar esta acción.'
         ], 403);
    }
}
