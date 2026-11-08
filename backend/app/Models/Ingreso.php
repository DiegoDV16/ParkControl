<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $table = 'ingreso';

    protected $primaryKey = 'idIngreso';

    public $timestamps = false;

    protected $guarded = [];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente');
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'idVehiculo', 'idVehiculo');
    }

    public function fila(): BelongsTo
    {
        return $this->belongsTo(Fila::class, 'idFilaEstacionamiento', 'idFila');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(ServicioExtra::class, 'idServicioExtra', 'idServicioExtra');
    }
}
