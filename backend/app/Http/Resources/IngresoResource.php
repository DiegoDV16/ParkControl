<?php

namespace App\Http\Resources;

use App\Services\TarifaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngresoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $inicio = Carbon::parse($this->fechaInicio.' '.$this->horaInicio);
        $horaTermino = (string) $this->horaTermino;
        $fin = $this->fechaTermino
            ? (strlen($horaTermino) > 8
                ? Carbon::parse($horaTermino)
                : Carbon::parse($this->fechaTermino.' '.$horaTermino))
            : ($this->fechaRegreso
                ? Carbon::parse($this->fechaRegreso.' '.($this->horaRegreso ?? '00:00'))
                : now());

        $minutos = max(1, (int) ceil($inicio->diffInMinutes($fin, true)));
        $dias = TarifaService::diasCobrables($minutos);

        $tipo = $this->vehiculo?->tipo?->tipoVehiculo;
        $servicioNombre = $this->servicio?->servicioExtra;
        $monto = $this->estado === 'anulado'
            ? 0
            : ($this->estado === 'cerrado' && $this->montoTotal !== null
                ? (float) $this->montoTotal
                : TarifaService::total($minutos, $tipo, $servicioNombre));

        return [
            'idIngreso' => (int) $this->idIngreso,
            'estado' => $this->estado,
            'fechaInicio' => $this->fechaInicio,
            'horaInicio' => $this->horaInicio,
            'fechaRegreso' => $this->fechaRegreso,
            'horaRegreso' => $this->horaRegreso,
            'fechaTermino' => $this->fechaTermino,
            'horaTermino' => $this->horaTermino,
            'numeroVuelo' => $this->numeroVuelo,
            'llaves' => (int) $this->llaves,
            'observacion' => $this->observacion,
            'fila' => $this->fila?->fila,
            'idFila' => (int) $this->idFilaEstacionamiento,
            'cliente' => [
                'idCliente' => (int) $this->idCliente,
                'rut' => $this->cliente?->rut,
                'nombre' => trim(implode(' ', array_filter([
                    $this->cliente?->nombres,
                    $this->cliente?->apellidoPaterno,
                    $this->cliente?->apellidoMaterno,
                ]))),
            ],
            'vehiculo' => [
                'idVehiculo' => (int) $this->idVehiculo,
                'patente' => $this->vehiculo?->patente,
                'marca' => $this->vehiculo?->modelo?->marca?->marca,
                'modelo' => $this->vehiculo?->modelo?->modelo,
                'tipo' => $tipo,
            ],
            'servicio' => $servicioNombre,
            'minutos' => $minutos,
            'dias' => $dias,
            'montoProyectado' => $monto,
            'danios' => $this->whenLoaded('danios', fn () => $this->danios->map(fn ($danio) => [
                'idDanio' => (int) $danio->idDanio,
                'vista' => $danio->vista,
                'coordX' => (float) $danio->coordX,
                'coordY' => (float) $danio->coordY,
                'tipoDanio' => $danio->tipoDanio,
                'detalle' => $danio->detalle,
            ])->values()),
            'inspeccionUrl' => $this->inspeccionRuta
                ? $request->getSchemeAndHttpHost().'/storage/'.$this->inspeccionRuta
                : null,
            'inspeccionSinDanios' => (bool) $this->inspeccionSinDanios,
        ];
    }
}
