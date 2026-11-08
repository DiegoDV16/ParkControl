<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fila extends Model
{
    protected $table = 'fila';

    protected $primaryKey = 'idFila';

    public $timestamps = false;

    protected $fillable = ['fila', 'capacidadTotal', 'estado'];

    /**
     * Ingresos que aún ocupan capacidad (sin fecha de término).
     */
    public function ingresosActivos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'idFilaEstacionamiento', 'idFila')
            ->whereNull('fechaTermino');
    }
}
