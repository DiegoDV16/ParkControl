<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFilaRequest;
use App\Http\Resources\FilaResource;
use App\Models\Fila;
use Illuminate\Http\JsonResponse;

class FilaController extends Controller
{
    /**
     * Filas con su ocupación actual (ingresos sin fecha de término).
     */
    public function index(): JsonResponse
    {
        $filas = Fila::withCount(['ingresosActivos as ocupadas'])
            ->orderBy('fila')
            ->get();

        return FilaResource::collection($filas)->response();
    }

    /**
     * Actualiza la capacidad de una fila sin permitir sobrecupos.
     */
    public function update(UpdateFilaRequest $request, Fila $fila): JsonResponse
    {
        $datos = $request->validated();
        $ocupadas = $fila->ingresosActivos()->count();
        $nuevaCapacidad = (int) $datos['capacidadTotal'];

        if ($nuevaCapacidad < $ocupadas) {
            $mensaje = "La capacidad no puede ser menor a los {$ocupadas} vehículos activos.";

            return response()->json([
                'message' => $mensaje,
                'errors' => ['capacidadTotal' => [$mensaje]],
            ], 422);
        }

        $fila->capacidadTotal = $nuevaCapacidad;
        $fila->estado = $ocupadas >= $nuevaCapacidad ? 0 : 1;
        $fila->save();

        $fila->loadCount(['ingresosActivos as ocupadas']);

        return response()->json([
            'data' => new FilaResource($fila),
            'message' => 'Capacidad actualizada correctamente.',
        ]);
    }
}
