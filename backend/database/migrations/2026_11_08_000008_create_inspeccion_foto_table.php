<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspeccion_foto', function (Blueprint $table): void {
            $table->increments('idFoto');
            $table->integer('idIngreso');
            $table->string('ruta', 255);
            $table->timestamp('creadoEn')->useCurrent();

            $table->foreign('idIngreso')
                ->references('idIngreso')
                ->on('ingreso')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspeccion_foto');
    }
};
