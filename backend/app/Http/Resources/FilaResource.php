<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $capacidad = (int) $this->capacidadTotal;
        $ocupadas = (int) ($this->ocupadas ?? 0);

        return [
            'idFila' => (int) $this->idFila,
            'fila' => $this->fila,
            'capacidadTotal' => $capacidad,
            'estado' => (int) $this->estado,
            'ocupadas' => $ocupadas,
            'libres' => max(0, $capacidad - $ocupadas),
        ];
    }
}
