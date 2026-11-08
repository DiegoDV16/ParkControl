<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIngresoRequest;
use App\Http\Resources\IngresoResource;
use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Vehiculo;
use App\Services\TarifaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IngresoController extends Controller
{
    /**
     * Ingresos activos (sin fecha de término) con monto proyectado.
     */
    /**
     * Estimación de días y monto antes de registrar el ingreso.
     */
    public function estimar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'tipo' => ['nullable', 'string', 'max:15'],
            'servicio' => ['nullable', 'string', 'max:25'],
            'fechaInicio' => ['required', 'date'],
            'horaInicio' => ['required', 'date_format:H:i'],
            'fechaRegreso' => ['nullable', 'date'],
            'horaRegreso' => ['nullable', 'date_format:H:i'],
        ]);

        $inicio = Carbon::parse($datos['fechaInicio'].' '.$datos['horaInicio']);
        $fin = ! empty($datos['fechaRegreso'])
            ? Carbon::parse($datos['fechaRegreso'].' '.($datos['horaRegreso'] ?? '00:00'))
            : now();

        $minutos = max(1, (int) ceil($inicio->diffInMinutes($fin, true)));
        $dias = TarifaService::diasCobrables($minutos);

        return response()->json([
            'minutos' => $minutos,
            'dias' => $dias,
            'monto' => TarifaService::total($minutos, $datos['tipo'] ?? null, $datos['servicio'] ?? null),
        ]);
    }

    public function index(): JsonResponse
    {
        $ingresos = Ingreso::with([
            'cliente',
            'vehiculo.modelo.marca',
            'vehiculo.tipo',
            'fila',
            'servicio',
        ])
            ->whereNull('fechaTermino')
            ->orderByDesc('idIngreso')
            ->paginate(15);

        return IngresoResource::collection($ingresos)->response();
    }

    /**
     * Registra una recepción en patio de forma transaccional.
     */
    public function store(StoreIngresoRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $usuario = $request->user();

        $ingreso = DB::transaction(function () use ($datos, $usuario): Ingreso {
            $fila = DB::table('fila')
                ->where('idFila', $datos['idFila'])
                ->lockForUpdate()
                ->first();

            if (! $fila) {
                throw ValidationException::withMessages(['idFila' => 'La fila no existe.']);
            }

            $ocupadas = DB::table('ingreso')
                ->where('idFilaEstacionamiento', $fila->idFila)
                ->whereNull('fechaTermino')
                ->count();

            if ($ocupadas >= (int) $fila->capacidadTotal) {
                throw ValidationException::withMessages([
                    'idFila' => 'La fila seleccionada ya está completa.',
                ]);
            }

            $patente = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $datos['patente']) ?? '');
            $vehiculo = Vehiculo::firstOrNew(['patente' => $patente]);

            if ($vehiculo->exists) {
                $activo = Ingreso::where('idVehiculo', $vehiculo->idVehiculo)
                    ->whereNull('fechaTermino')
                    ->exists();

                if ($activo) {
                    throw ValidationException::withMessages([
                        'patente' => 'El vehículo ya tiene un ingreso activo.',
                    ]);
                }
            }

            $cliente = Cliente::firstOrNew(['rut' => $datos['rut']]);
            $cliente->fill([
                'nombres' => $datos['nombres'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
                'correo' => $datos['correo'] ?? null,
                'estado' => 1,
            ]);
            $cliente->save();

            $vehiculo->fill([
                'idCliente' => $cliente->idCliente,
                'idModelo' => $datos['idModelo'],
                'idTipoVehiculo' => $datos['idTipoVehiculo'],
            ]);
            $vehiculo->save();

            return Ingreso::create([
                'idServicioExtra' => $datos['idServicioExtra'] ?? null,
                'idUsuario' => $usuario->idUsuario,
                'idVehiculo' => $vehiculo->idVehiculo,
                'idFilaEstacionamiento' => $fila->idFila,
                'idCliente' => $cliente->idCliente,
                'fechaInicio' => $datos['fechaInicio'],
                'horaInicio' => $datos['horaInicio'],
                'fechaRegreso' => $datos['fechaRegreso'] ?? null,
                'horaRegreso' => $datos['horaRegreso'] ?? null,
                'numeroVuelo' => $datos['numeroVuelo'] ?? null,
                'llaves' => (int) ($datos['llaves'] ?? 0),
                'observacion' => $datos['observacion'] ?? null,
                'estado' => 'abierto',
            ]);
        });

        $ingreso->load(['cliente', 'vehiculo.modelo.marca', 'vehiculo.tipo', 'fila', 'servicio']);

        return (new IngresoResource($ingreso))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Ingreso $ingreso): JsonResponse
    {
        $ingreso->load(['cliente', 'vehiculo.modelo.marca', 'vehiculo.tipo', 'fila', 'servicio']);

        return (new IngresoResource($ingreso))->response();
    }
}
