<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danio_vehiculo', function (Blueprint $table): void {
            $table->increments('idDanio');
            $table->integer('idIngreso');
            $table->decimal('coordX', 5, 2);
            $table->decimal('coordY', 5, 2);
            $table->string('tipoDanio', 30);
            $table->string('detalle', 150)->nullable();
            $table->timestamp('creadoEn')->useCurrent();

            $table->foreign('idIngreso')
                ->references('idIngreso')
                ->on('ingreso')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danio_vehiculo');
    }
};
