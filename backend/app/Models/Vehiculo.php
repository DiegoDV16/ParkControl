<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehiculo extends Model
{
    protected $table = 'vehiculo';

    protected $primaryKey = 'idVehiculo';

    public $timestamps = false;

    protected $fillable = [
        'idCliente',
        'patente',
        'anio',
        'color',
        'idModelo',
        'idTipoVehiculo',
    ];

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'idModelo', 'idModelo');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class, 'idTipoVehiculo', 'idTipoVehiculo');
    }
}
