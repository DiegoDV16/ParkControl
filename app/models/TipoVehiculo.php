<?php
/**
 * ==========================================================================
 * ParkControl · app/models/TipoVehiculo.php
 * ==========================================================================
 * Catálogo de tipos de vehículo (SEDAN, HATCHBACK, CAMIONETA, SUV).
 * ==========================================================================
 */

class TipoVehiculo
{
    public static function todos(): array
    {
        return Database::getConexion()
            ->query('SELECT idTipoVehiculo, tipoVehiculo FROM tipovehiculo ORDER BY idTipoVehiculo')
            ->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT * FROM tipovehiculo WHERE idTipoVehiculo = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }
}
