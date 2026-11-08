<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\ServicioExtra;
use App\Models\TipoVehiculo;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    /**
     * Catálogos para el formulario de recepción: marcas con modelos,
     * servicios extra y tipos de vehículo.
     */
    public function index(): JsonResponse
    {
        $marcas = Marca::with(['modelos' => fn ($query) => $query->orderBy('modelo')])
            ->orderBy('marca')
            ->get(['idMarca', 'marca']);

        return response()->json([
            'marcas' => $marcas->map(fn (Marca $marca) => [
                'idMarca' => (int) $marca->idMarca,
                'marca' => $marca->marca,
                'modelos' => $marca->modelos->map(fn ($modelo) => [
                    'idModelo' => (int) $modelo->idModelo,
                    'modelo' => $modelo->modelo,
                ])->values(),
            ])->values(),
            'servicios' => ServicioExtra::where('activo', 1)
                ->orderBy('servicioExtra')
                ->get(['idServicioExtra', 'servicioExtra', 'valor']),
            'tipos' => TipoVehiculo::orderBy('idTipoVehiculo')
                ->get(['idTipoVehiculo', 'tipoVehiculo']),
        ]);
    }
}
