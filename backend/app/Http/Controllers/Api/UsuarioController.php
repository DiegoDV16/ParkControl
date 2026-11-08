<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Listado paginado con búsqueda y filtros.
     */
    public function index(Request $request): JsonResponse
    {
        $consulta = Usuario::with('rol');

        if ($buscar = trim((string) $request->query('buscar', ''))) {
            $consulta->where(function ($query) use ($buscar): void {
                $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidoPaterno', 'like', "%{$buscar}%")
                    ->orWhere('apellidoMaterno', 'like', "%{$buscar}%")
                    ->orWhere('correo', 'like', "%{$buscar}%")
                    ->orWhere('rut', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('idRol')) {
            $consulta->where('idRol', (int) $request->query('idRol'));
        }

        if ($request->query('estado') !== null && $request->query('estado') !== '') {
            $consulta->where('estado', (int) $request->query('estado'));
        }

        $usuarios = $consulta->orderBy('idUsuario')->paginate(10)->withQueryString();

        return UsuarioResource::collection($usuarios)->response();
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $usuario = Usuario::create([
            'idRol' => $datos['idRol'],
            'rut' => $datos['rut'],
            'nombres' => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'] ?? null,
            'correo' => $datos['correo'],
            'contrasena' => Hash::make($datos['contrasena']),
            'estado' => 1,
        ]);

        return (new UsuarioResource($usuario->load('rol')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $datos = $request->validated();

        if ($usuario->idUsuario === $request->user()->idUsuario
            && (int) $datos['idRol'] !== 1
            && $this->administradoresActivos($usuario->idUsuario) === 0) {
            return response()->json([
                'message' => 'No puedes quitarte el rol de administrador: eres el único activo.',
            ], 422);
        }

        if ($usuario->idUsuario === $request->user()->idUsuario
            && (int) $usuario->idRol === 1
            && (int) $datos['idRol'] !== 1
            && $this->administradoresActivos($usuario->idUsuario) === 0) {
            return response()->json([
                'message' => 'Debe existir al menos otro administrador activo.',
            ], 422);
        }

        $usuario->fill([
            'idRol' => $datos['idRol'],
            'rut' => $datos['rut'],
            'nombres' => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'] ?? null,
            'correo' => $datos['correo'],
        ]);

        if (! empty($datos['contrasena'])) {
            $usuario->contrasena = Hash::make($datos['contrasena']);
        }

        $usuario->save();

        return (new UsuarioResource($usuario->load('rol')))->response();
    }

    /**
     * Elimina un usuario solo si no tiene ingresos asociados.
     */
    public function destroy(Request $request, Usuario $usuario): JsonResponse
    {
        if ($usuario->idUsuario === $request->user()->idUsuario) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta.'], 422);
        }

        if ((int) $usuario->idRol === 1 && $this->administradoresActivos($usuario->idUsuario) === 0) {
            return response()->json(['message' => 'Debe existir al menos un administrador activo.'], 422);
        }

        $ingresos = DB::table('ingreso')->where('idUsuario', $usuario->idUsuario)->count();

        if ($ingresos > 0) {
            return response()->json([
                'message' => 'El usuario tiene ingresos registrados. Desactívalo en lugar de eliminarlo.',
            ], 409);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }

    /**
     * Activa o desactiva una cuenta sin borrarla.
     */
    public function estado(Request $request, Usuario $usuario): JsonResponse
    {
        $datos = $request->validate([
            'estado' => ['required', 'integer', 'in:0,1'],
        ]);

        $estado = (int) $datos['estado'];

        if ($usuario->idUsuario === $request->user()->idUsuario) {
            return response()->json(['message' => 'No puedes cambiar el estado de tu propia cuenta.'], 422);
        }

        if ($estado === 0 && (int) $usuario->idRol === 1
            && $this->administradoresActivos($usuario->idUsuario) === 0) {
            return response()->json(['message' => 'Debe existir al menos un administrador activo.'], 422);
        }

        $usuario->estado = $estado;
        $usuario->save();

        return (new UsuarioResource($usuario->load('rol')))->response();
    }

    private function administradoresActivos(int $excepto): int
    {
        return Usuario::where('idRol', 1)
            ->where('estado', 1)
            ->where('idUsuario', '!=', $excepto)
            ->count();
    }
}
