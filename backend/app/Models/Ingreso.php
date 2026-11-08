<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingreso extends Model
{
    protected $table = 'ingreso';

    protected $primaryKey = 'idIngreso';

    public $timestamps = false;

    protected $guarded = [];

    public function danios(): HasMany
    {
        return $this->hasMany(DanioVehiculo::class, 'idIngreso', 'idIngreso');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(InspeccionFoto::class, 'idIngreso', 'idIngreso');
    }

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
