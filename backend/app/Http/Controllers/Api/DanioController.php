<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanioVehiculo;
use App\Models\Ingreso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DanioController extends Controller
{
    public const TIPOS = ['Rayón', 'Abolladura', 'Golpe', 'Vidrio', 'Espejo', 'Llanta', 'Otro'];

    public function index(Ingreso $ingreso): JsonResponse
    {
        return response()->json([
            'data' => $ingreso->danios()->orderBy('idDanio')->get(),
        ]);
    }

    public function store(Request $request, Ingreso $ingreso): JsonResponse
    {
        $datos = $request->validate([
            'coordX' => ['required', 'numeric', 'min:0', 'max:100'],
            'coordY' => ['required', 'numeric', 'min:0', 'max:100'],
            'tipoDanio' => ['required', 'string', Rule::in(self::TIPOS)],
            'detalle' => ['nullable', 'string', 'max:150'],
        ]);

        $danio = $ingreso->danios()->create($datos);

        return response()->json(['data' => $danio], 201);
    }

    public function destroy(DanioVehiculo $danio): JsonResponse
    {
        $danio->delete();

        return response()->json(['message' => 'Daño eliminado correctamente.']);
    }
}
