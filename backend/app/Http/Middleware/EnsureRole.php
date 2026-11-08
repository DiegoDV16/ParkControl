<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Restringe el acceso según el rol del usuario autenticado.
     * Uso: ->middleware('role:ADMINISTRADOR,JEFATURA')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $mapa = [
            1 => 'ADMINISTRADOR',
            2 => 'OPERADOR',
            3 => 'JEFATURA',
        ];

        $rolActual = $mapa[(int) $usuario->idRol] ?? '';
        $permitidos = array_map('strtoupper', $roles);

        if (! in_array($rolActual, $permitidos, true)) {
            return response()->json(['message' => 'No tienes permisos para esta acción.'], 403);
        }

        return $next($request);
    }
}
