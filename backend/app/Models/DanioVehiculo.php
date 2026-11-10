<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanioVehiculo extends Model
{
    protected $table = 'danio_vehiculo';

    protected $primaryKey = 'idDanio';

    public $timestamps = false;

    protected $fillable = ['idIngreso', 'vista', 'coordX', 'coordY', 'tipoDanio', 'detalle'];

    protected $casts = [
        'coordX' => 'float',
        'coordY' => 'float',
    ];

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class, 'idIngreso', 'idIngreso');
    }
}
