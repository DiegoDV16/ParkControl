<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Autentica por correo y contraseña y emite un token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:65'],
            'contrasena' => ['required', 'string'],
        ]);

        $usuario = Usuario::with('rol')->where('correo', $datos['correo'])->first();

        if (! $usuario || ! $usuario->estaActivo() || ! Hash::check($datos['contrasena'], $usuario->contrasena)) {
            return response()->json([
                'message' => 'Correo o contraseña incorrectos.',
            ], 401);
        }

        $token = $usuario->createToken(
            'parkcontrol-spa',
            ['rol:'.$usuario->idRol]
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'usuario' => $this->perfil($usuario),
        ]);
    }

    /**
     * Devuelve el usuario autenticado y su rol.
     */
    public function user(Request $request): JsonResponse
    {
        $usuario = $request->user()->loadMissing('rol');

        return response()->json([
            'usuario' => $this->perfil($usuario),
        ]);
    }

    /**
     * Revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function perfil(Usuario $usuario): array
    {
        return [
            'idUsuario' => (int) $usuario->idUsuario,
            'rut' => $usuario->rut,
            'nombres' => $usuario->nombres,
            'apellidoPaterno' => $usuario->apellidoPaterno,
            'apellidoMaterno' => $usuario->apellidoMaterno,
            'correo' => $usuario->correo,
            'idRol' => (int) $usuario->idRol,
            'rol' => $usuario->rol?->rol,
            'estado' => (int) $usuario->estado,
        ];
    }
}
