<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Model
{
    protected $table = 'marca';

    protected $primaryKey = 'idMarca';

    public $timestamps = false;

    protected $fillable = ['marca'];

    public function modelos(): HasMany
    {
        return $this->hasMany(Modelo::class, 'idMarca', 'idMarca');
    }
}
