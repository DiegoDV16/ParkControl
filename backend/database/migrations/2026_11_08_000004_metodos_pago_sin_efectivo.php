<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ParkControl no maneja efectivo: solo débito, crédito y transferencia.
     * Los pagos históricos en efectivo se eliminan (eran datos de prueba).
     */
    public function up(): void
    {
        DB::table('pago')->where('metodo', 'efectivo')->delete();

        DB::statement(
            "ALTER TABLE pago MODIFY metodo ENUM('debito', 'credito', 'transferencia') NOT NULL DEFAULT 'debito'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE pago MODIFY metodo ENUM('efectivo', 'debito', 'credito', 'transferencia') NOT NULL DEFAULT 'efectivo'"
        );
    }
};
