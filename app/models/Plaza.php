<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Plaza.php
 * ==========================================================================
 * Estado de ocupación de las plazas de estacionamiento.
 * ==========================================================================
 */

class Plaza
{
    /** @return array{total: int, libres: int, ocupadas: int, fuera: int} */
    public static function resumen(): array
    {
        $fila = Database::getConexion()->query(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(estado = 1), 0) AS libres,
                    COALESCE(SUM(estado = 2), 0) AS ocupadas,
                    COALESCE(SUM(estado = 0), 0) AS fuera
             FROM plaza'
        )->fetch();

        return [
            'total'    => (int) $fila['total'],
            'libres'   => (int) $fila['libres'],
            'ocupadas' => (int) $fila['ocupadas'],
            'fuera'    => (int) $fila['fuera'],
        ];
    }
}
