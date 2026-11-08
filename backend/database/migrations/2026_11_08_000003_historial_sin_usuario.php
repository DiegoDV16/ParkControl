<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite eliminar usuarios conservando el historial: los ingresos y pagos
     * quedan sin usuario asociado (idUsuario NULL).
     */
    public function up(): void
    {
        Schema::table('ingreso', function (Blueprint $table): void {
            $table->dropForeign('fk_ingreso_usuario');
        });

        Schema::table('pago', function (Blueprint $table): void {
            $table->dropForeign('fk_pago_usuario');
        });

        DB::statement('ALTER TABLE ingreso MODIFY idUsuario INT(11) NULL');
        DB::statement('ALTER TABLE pago MODIFY idUsuario INT(11) NULL');

        Schema::table('ingreso', function (Blueprint $table): void {
            $table->foreign('idUsuario', 'fk_ingreso_usuario')
                ->references('idUsuario')
                ->on('usuario')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::table('pago', function (Blueprint $table): void {
            $table->foreign('idUsuario', 'fk_pago_usuario')
                ->references('idUsuario')
                ->on('usuario')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        DB::table('ingreso')->whereNull('idUsuario')->update(['idUsuario' => 1]);
        DB::table('pago')->whereNull('idUsuario')->update(['idUsuario' => 1]);

        Schema::table('ingreso', function (Blueprint $table): void {
            $table->dropForeign('fk_ingreso_usuario');
        });

        Schema::table('pago', function (Blueprint $table): void {
            $table->dropForeign('fk_pago_usuario');
        });

        DB::statement('ALTER TABLE ingreso MODIFY idUsuario INT(11) NOT NULL');
        DB::statement('ALTER TABLE pago MODIFY idUsuario INT(11) NOT NULL');

        Schema::table('ingreso', function (Blueprint $table): void {
            $table->foreign('idUsuario', 'fk_ingreso_usuario')
                ->references('idUsuario')
                ->on('usuario')
                ->onUpdate('cascade');
        });

        Schema::table('pago', function (Blueprint $table): void {
            $table->foreign('idUsuario', 'fk_pago_usuario')
                ->references('idUsuario')
                ->on('usuario')
                ->onUpdate('cascade');
        });
    }
};
