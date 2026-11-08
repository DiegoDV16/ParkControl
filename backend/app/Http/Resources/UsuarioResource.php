<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idUsuario' => (int) $this->idUsuario,
            'idRol' => (int) $this->idRol,
            'rol' => $this->whenLoaded('rol', fn () => $this->rol?->rol),
            'rut' => $this->rut,
            'nombres' => $this->nombres,
            'apellidoPaterno' => $this->apellidoPaterno,
            'apellidoMaterno' => $this->apellidoMaterno,
            'correo' => $this->correo,
            'estado' => (int) $this->estado,
            'ultimoAcceso' => $this->ultimoAcceso,
            'creadoEn' => $this->creadoEn,
        ];
    }
}
