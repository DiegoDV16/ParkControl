<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use App\Models\InspeccionFoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoInspeccionController extends Controller
{
    public function index(Request $request, Ingreso $ingreso): JsonResponse
    {
        return response()->json([
            'data' => $ingreso->fotos()->orderBy('idFoto')->get()->map(
                fn (InspeccionFoto $foto) => $this->formatear($request, $foto)
            )->values(),
        ]);
    }

    public function store(Request $request, Ingreso $ingreso): JsonResponse
    {
        $request->validate([
            'imagen' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        $ruta = $request->file('imagen')->store('inspecciones', 'public');

        $foto = InspeccionFoto::create([
            'idIngreso' => $ingreso->idIngreso,
            'ruta' => $ruta,
        ]);

        return response()->json(['data' => $this->formatear($request, $foto)], 201);
    }

    public function destroy(InspeccionFoto $foto): JsonResponse
    {
        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();

        return response()->json(['message' => 'Fotografía eliminada.']);
    }

    /**
     * @return array{idFoto: int, url: string}
     */
    private function formatear(Request $request, InspeccionFoto $foto): array
    {
        return [
            'idFoto' => (int) $foto->idFoto,
            'url' => $request->getSchemeAndHttpHost().'/storage/'.$foto->ruta,
        ];
    }
}
