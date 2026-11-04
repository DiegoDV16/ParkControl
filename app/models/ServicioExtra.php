<?php
/**
 * ==========================================================================
 * ParkControl · app/models/ServicioExtra.php
 * ==========================================================================
 * Catálogo de servicios adicionales (lavado, aspirado, etc.).
 * ==========================================================================
 */

class ServicioExtra
{
    public static function activos(): array
    {
        return Database::getConexion()
            ->query('SELECT idServicioExtra, servicioExtra, valor FROM servicioextra WHERE activo = 1 ORDER BY servicioExtra')
            ->fetchAll();
    }

    public static function todos(): array
    {
        return Database::getConexion()
            ->query('SELECT * FROM servicioextra ORDER BY servicioExtra')
            ->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM servicioextra WHERE idServicioExtra = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }
}
