<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Tarifa.php
 * ==========================================================================
 * Tarifas de ParkControl según las reglas del repositorio
 * Bd_Eva01_Base_De_Datos (REGLAS_NEGOCIO.md):
 *
 *  · Estadía: mínimo 1 día; tramos por día; prórroga de 5 horas después de
 *    cada día completo.
 *  · Servicio extra: matriz por tipo de vehículo y servicio.
 *
 * No hay montos persistidos: el cobro se calcula al momento de la salida.
 * ==========================================================================
 */

class Tarifa
{
    /** Tramos de estadía (en pesos chilenos). */
    public const ESTADIA = [
        'dia_1_2' => 10000,   // 1 a 2 días
        'dia_3_5' => 7000,    // 3 a 5 días
        'fijo_6_7' => 40000,  // 6 a 7 días
        'fijo_8_mas' => 50000, // 8 días o más
    ];

    /** Matriz de servicios por tipo de vehículo. */
    public const SERVICIOS = [
        'SEDAN'     => ['Lavado' => 15000, 'Lavado y aspirado' => 22000, 'Aspirado' => 8000, 'Sin servicio' => 0],
        'HATCHBACK' => ['Lavado' => 18000, 'Lavado y aspirado' => 25000, 'Aspirado' => 10000, 'Sin servicio' => 0],
        'CAMIONETA' => ['Lavado' => 22000, 'Lavado y aspirado' => 30000, 'Aspirado' => 12000, 'Sin servicio' => 0],
        'SUV'       => ['Lavado' => 20000, 'Lavado y aspirado' => 28000, 'Aspirado' => 11000, 'Sin servicio' => 0],
    ];

    /**
     * Convierte una duración en minutos a días cobrables.
     * Menos de 1 día = 1 día; después de cada día completo se toleran
     * 5 horas de prórroga antes de cobrar un día adicional.
     */
    public static function diasCobrables(int $minutos): int
    {
        $minutos = max(1, $minutos);
        $horas = $minutos / 60;

        $diasCompletos = (int) floor($horas / 24);
        $resto = $horas - ($diasCompletos * 24);

        if ($diasCompletos === 0) {
            return 1;
        }

        return $resto > 5 ? $diasCompletos + 1 : $diasCompletos;
    }

    /** Monto de la estadía según los días cobrables. */
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

    /** Precio del servicio extra según tipo de vehículo. */
    public static function servicio(string $tipoVehiculo, string $servicio): float
    {
        $tipo = strtoupper(trim($tipoVehiculo));
        $servicio = trim($servicio) !== '' ? trim($servicio) : 'Sin servicio';

        $matriz = self::SERVICIOS[$tipo] ?? self::SERVICIOS['SEDAN'];

        return (float) ($matriz[$servicio] ?? 0);
    }

    /** Total a cobrar por un ingreso. */
    public static function total(int $minutos, string $tipoVehiculo, string $servicio): float
    {
        $dias = self::diasCobrables($minutos);

        return round(self::estadia($dias) + self::servicio($tipoVehiculo, $servicio), 0);
    }
}
