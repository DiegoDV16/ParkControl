<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modelo extends Model
{
    protected $table = 'modelo';

    protected $primaryKey = 'idModelo';

    public $timestamps = false;

    protected $fillable = ['idMarca', 'modelo'];

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'idMarca', 'idMarca');
    }
}
