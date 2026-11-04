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

    public static function porNombre(string $marca): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM marca WHERE marca = :marca LIMIT 1');
        $stmt->execute([':marca' => $marca]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function crear(string $marca): int
    {
        $stmt = Database::getConexion()->prepare('INSERT INTO marca (marca) VALUES (:marca)');
        $stmt->execute([':marca' => $marca]);
        return (int) Database::getConexion()->lastInsertId();
    }

    public static function existe(int $idMarca): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM marca WHERE idMarca = :id LIMIT 1');
        $stmt->execute([':id' => $idMarca]);
        return (bool) $stmt->fetchColumn();
    }
}
