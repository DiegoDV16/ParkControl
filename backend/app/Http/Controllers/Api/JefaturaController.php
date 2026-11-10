<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilaResource;
use App\Http\Resources\IngresoResource;
use App\Models\Fila;
use App\Models\Ingreso;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JefaturaController extends Controller
{
    /**
     * Panel de supervisión: ocupación, estadías activas, vuelos y recaudación.
     */
    public function resumen(): JsonResponse
    {
        $activos = Ingreso::with([
            'cliente',
            'vehiculo.modelo.marca',
            'vehiculo.tipo',
            'fila',
            'servicio',
            'danios',
            'fotos',
        ])
            ->whereNull('fechaTermino')
            ->orderByDesc('idIngreso')
            ->get();

        $filas = Fila::withCount(['ingresosActivos as ocupadas'])
            ->orderBy('fila')
            ->get();

        $capacidad = (int) $filas->sum('capacidadTotal');
        $ocupadas = (int) $filas->sum('ocupadas');

        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        $recaudacionHoy = (float) DB::table('pago')
            ->whereDate('fechaPago', $hoy->toDateString())
            ->sum('monto');

        $recaudacionMes = (float) DB::table('pago')
            ->where('fechaPago', '>=', $inicioMes)
            ->sum('monto');

        $porMetodo = DB::table('pago')
            ->select('metodo', DB::raw('SUM(monto) as total'))
            ->whereDate('fechaPago', $hoy->toDateString())
            ->groupBy('metodo')
            ->orderBy('metodo')
            ->get()
            ->map(fn ($fila) => [
                'metodo' => $fila->metodo,
                'total' => (float) $fila->total,
            ])
            ->values();

        $ingresosHoy = Ingreso::whereDate('creadoEn', $hoy->toDateString())->count();

        $vuelos = $activos
            ->filter(fn (Ingreso $ingreso) => filled($ingreso->numeroVuelo) || filled($ingreso->fechaRegreso))
            ->map(function (Ingreso $ingreso): array {
                $estado = $this->estadoVuelo($ingreso);
                $minutos = null;

                if ($ingreso->fechaRegreso && $ingreso->horaRegreso) {
                    $llegada = Carbon::parse($ingreso->fechaRegreso.' '.$ingreso->horaRegreso);
                    $minutos = (int) round(($llegada->getTimestamp() - Carbon::now()->getTimestamp()) / 60);
                }

                return [
                    'idIngreso' => (int) $ingreso->idIngreso,
                    'patente' => $ingreso->vehiculo?->patente,
                    'cliente' => trim(implode(' ', array_filter([
                        $ingreso->cliente?->nombres,
                        $ingreso->cliente?->apellidoPaterno,
                        $ingreso->cliente?->apellidoMaterno,
                    ]))),
                    'fila' => $ingreso->fila?->fila,
                    'numeroVuelo' => $ingreso->numeroVuelo,
                    'fechaRegreso' => $ingreso->fechaRegreso,
                    'horaRegreso' => $ingreso->horaRegreso ? substr((string) $ingreso->horaRegreso, 0, 5) : null,
                    'estado' => $estado,
                    'minutos' => $minutos,
                ];
            })
            ->values();

        return response()->json([
            'kpis' => [
                'activos' => $activos->count(),
                'capacidad' => $capacidad,
                'ocupadas' => $ocupadas,
                'libres' => max(0, $capacidad - $ocupadas),
                'ocupacionPorcentaje' => $capacidad > 0
                    ? round($ocupadas / $capacidad * 100, 1)
                    : 0,
                'recaudacionHoy' => $recaudacionHoy,
                'recaudacionMes' => $recaudacionMes,
                'ingresosHoy' => $ingresosHoy,
                'llegadasProximas' => $vuelos->where('estado', 'PRÓXIMO')->count(),
            ],
            'filas' => FilaResource::collection($filas)->resolve(),
            'estadias' => IngresoResource::collection($activos)->resolve(),
            'vuelos' => $vuelos,
            'recaudacion' => [
                'hoy' => $recaudacionHoy,
                'mes' => $recaudacionMes,
                'porMetodo' => $porMetodo,
            ],
        ]);
    }

    private function estadoVuelo(Ingreso $ingreso): string
    {
        if (! filled($ingreso->numeroVuelo)) {
            return 'SIN PROGRAMAR';
        }

        if (! filled($ingreso->fechaRegreso) || ! filled($ingreso->horaRegreso)) {
            return 'PENDIENTE';
        }

        $llegada = Carbon::parse($ingreso->fechaRegreso.' '.$ingreso->horaRegreso);
        $minutos = (int) round(($llegada->getTimestamp() - Carbon::now()->getTimestamp()) / 60);

        if ($minutos < -30) {
            return 'LLEGADA VENCIDA';
        }

        if ($minutos <= 60) {
            return 'PRÓXIMO';
        }

        return 'PROGRAMADO';
    }
}
