<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('danio_vehiculo', function (Blueprint $table): void {
            $table->string('vista', 20)->default('superior')->after('idIngreso');
        });
    }

    public function down(): void
    {
        Schema::table('danio_vehiculo', function (Blueprint $table): void {
            $table->dropColumn('vista');
        });
    }
};
