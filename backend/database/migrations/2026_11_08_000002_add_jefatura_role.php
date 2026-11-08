<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega el rol JEFATURA al catálogo existente sin tocar los roles previos.
     * Los roles previos RECEPCION/SUPERVISOR se mapean a OPERADOR/JEFATURA
     * en la aplicación.
     */
    public function up(): void
    {
        DB::table('rol')->updateOrInsert(
            ['idRol' => 3],
            ['rol' => 'Jefatura']
        );
    }

    public function down(): void
    {
        DB::table('rol')->where('idRol', 3)->delete();
    }
};
