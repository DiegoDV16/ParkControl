<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioExtra extends Model
{
    protected $table = 'servicioextra';

    protected $primaryKey = 'idServicioExtra';

    public $timestamps = false;

    protected $fillable = ['servicioExtra', 'valor', 'activo'];
}
