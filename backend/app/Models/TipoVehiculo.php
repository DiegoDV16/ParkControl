<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipovehiculo';

    protected $primaryKey = 'idTipoVehiculo';

    public $timestamps = false;

    protected $fillable = ['tipoVehiculo'];
}
