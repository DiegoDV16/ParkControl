<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Marca.php
 * ==========================================================================
 * Catálogo de marcas de vehículos.
 * ==========================================================================
 */

class Marca
{
    public static function todas(): array
    {
        return Database::getConexion()
            ->query('SELECT idMarca, marca FROM marca ORDER BY marca')
            ->fetchAll();
    }

    public static function existe(int $idMarca): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM marca WHERE idMarca = :id LIMIT 1');
        $stmt->execute([':id' => $idMarca]);
        return (bool) $stmt->fetchColumn();
    }
}
