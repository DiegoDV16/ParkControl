<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuario';

    protected $primaryKey = 'idUsuario';

    public $incrementing = true;

    public $timestamps = false;

    protected $hidden = [
        'contrasena',
    ];

    protected $fillable = [
        'idRol',
        'rut',
        'nombres',
        'apellidoPaterno',
        'apellidoMaterno',
        'correo',
        'contrasena',
        'estado',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    /**
     * Laravel valida la contraseña contra esta columna.
     */
    public function getAuthPassword(): string
    {
        return (string) $this->contrasena;
    }

    public function estaActivo(): bool
    {
        return (int) $this->estado === 1;
    }
}
