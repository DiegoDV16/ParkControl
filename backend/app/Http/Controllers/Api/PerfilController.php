<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Perfil del usuario autenticado.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'usuario' => new UsuarioResource($request->user()->loadMissing('rol')),
        ]);
    }

    /**
     * Actualiza los datos personales del propio usuario.
     */
    public function update(UpdatePerfilRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $usuario = $request->user();
        $usuario->fill([
            'nombres' => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'] ?? null,
        ]);
        $usuario->save();

        return response()->json([
            'usuario' => new UsuarioResource($usuario->load('rol')),
            'message' => 'Perfil actualizado correctamente.',
        ]);
    }

    /**
     * Cambia la contraseña verificando la actual.
     */
    public function password(ChangePasswordRequest $request): JsonResponse
    {
        $usuario = $request->user();

        if (! Hash::check($request->contrasena_actual, $usuario->contrasena)) {
            return response()->json([
                'message' => 'La contraseña actual no es correcta.',
                'errors' => ['contrasena_actual' => ['La contraseña actual no es correcta.']],
            ], 422);
        }

        $usuario->contrasena = Hash::make($request->contrasena);
        $usuario->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }
}
