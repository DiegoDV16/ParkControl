<?php

namespace App\Services;

class TarifaService
{
    public const ESTADIA = [
        'dia_1_2' => 10000,
        'dia_3_5' => 7000,
        'fijo_6_7' => 40000,
        'fijo_8_mas' => 50000,
    ];

    public const SERVICIOS = [
        'SEDAN' => ['Lavado' => 15000, 'Lavado y aspirado' => 22000, 'Aspirado' => 8000, 'Sin servicio' => 0],
        'HATCHBACK' => ['Lavado' => 18000, 'Lavado y aspirado' => 25000, 'Aspirado' => 10000, 'Sin servicio' => 0],
        'CAMIONETA' => ['Lavado' => 22000, 'Lavado y aspirado' => 30000, 'Aspirado' => 12000, 'Sin servicio' => 0],
        'SUV' => ['Lavado' => 20000, 'Lavado y aspirado' => 28000, 'Aspirado' => 11000, 'Sin servicio' => 0],
    ];

    /**
     * Días cobrables: mínimo 1 y 5 horas de prórroga por día completo.
     */
    public static function diasCobrables(int $minutos): int
    {
        $horas = max(1, $minutos) / 60;
        $diasCompletos = (int) floor($horas / 24);
        $resto = $horas - ($diasCompletos * 24);

        if ($diasCompletos === 0) {
            return 1;
        }

        return $resto > 5 ? $diasCompletos + 1 : $diasCompletos;
    }

    public static function estadia(int $dias): float
    {
        $dias = max(1, $dias);

        if ($dias <= 2) {
            return (float) (self::ESTADIA['dia_1_2'] * $dias);
        }
        if ($dias <= 5) {
            return (float) (self::ESTADIA['dia_3_5'] * $dias);
        }
        if ($dias <= 7) {
            return (float) self::ESTADIA['fijo_6_7'];
        }

        return (float) self::ESTADIA['fijo_8_mas'];
    }

    public static function servicio(?string $tipo, ?string $servicio): float
    {
        $tipo = strtoupper(trim((string) $tipo));
        $servicio = trim((string) $servicio) !== '' ? trim((string) $servicio) : 'Sin servicio';
        $matriz = self::SERVICIOS[$tipo] ?? self::SERVICIOS['SEDAN'];

        return (float) ($matriz[$servicio] ?? 0);
    }

    public static function total(int $minutos, ?string $tipo, ?string $servicio): float
    {
        $dias = self::diasCobrables($minutos);

        return round(self::estadia($dias) + self::servicio($tipo, $servicio), 0);
    }
}
