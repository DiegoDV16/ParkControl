<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $table = 'ingreso';

    protected $primaryKey = 'idIngreso';

    public $timestamps = false;

    protected $guarded = [];
}
