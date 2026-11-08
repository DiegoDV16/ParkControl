<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspeccionFoto extends Model
{
    protected $table = 'inspeccion_foto';

    protected $primaryKey = 'idFoto';

    public $timestamps = false;

    protected $fillable = ['idIngreso', 'ruta'];

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class, 'idIngreso', 'idIngreso');
    }
}
