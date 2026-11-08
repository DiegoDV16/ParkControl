<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingreso', function (Blueprint $table): void {
            $table->string('inspeccionRuta', 255)->nullable()->after('observacion');
            $table->boolean('inspeccionSinDanios')->default(false)->after('inspeccionRuta');
        });
    }

    public function down(): void
    {
        Schema::table('ingreso', function (Blueprint $table): void {
            $table->dropColumn(['inspeccionRuta', 'inspeccionSinDanios']);
        });
    }
};
