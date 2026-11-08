<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspeccionController extends Controller
{
    /**
     * Guarda la imagen compuesta de la inspección vehicular.
     */
    public function store(Request $request, Ingreso $ingreso): JsonResponse
    {
        $datos = $request->validate([
            'imagen' => ['required', 'file', 'mimes:png,webp', 'max:5120'],
            'sinDanios' => ['sometimes', 'boolean'],
        ]);

        $ruta = $request->file('imagen')->store('inspecciones', 'public');

        $ingreso->inspeccionRuta = $ruta;
        $ingreso->inspeccionSinDanios = (bool) ($datos['sinDanios'] ?? false);
        $ingreso->save();

        return response()->json([
            'message' => 'Inspección guardada correctamente.',
            'ruta' => $ruta,
            'url' => $request->getSchemeAndHttpHost().'/storage/'.$ruta,
            'sinDanios' => (bool) $ingreso->inspeccionSinDanios,
        ]);
    }
}
