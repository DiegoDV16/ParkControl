<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Modelo.php
 * ==========================================================================
 * Catálogo de modelos de vehículos, agrupados por marca.
 * ==========================================================================
 */

class Modelo
{
    /** Modelos con su marca, para los selects dependientes del formulario. */
    public static function todos(): array
    {
        return Database::getConexion()
            ->query('SELECT idModelo, idMarca, modelo FROM modelo ORDER BY modelo')
            ->fetchAll();
    }

    public static function porMarca(int $idMarca): array
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT idModelo, modelo FROM modelo WHERE idMarca = :idMarca ORDER BY modelo'
        );
        $stmt->execute([':idMarca' => $idMarca]);
        return $stmt->fetchAll();
    }

    public static function existe(int $idModelo): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM modelo WHERE idModelo = :id LIMIT 1');
        $stmt->execute([':id' => $idModelo]);
        return (bool) $stmt->fetchColumn();
    }

    /** Devuelve el idMarca al que pertenece un modelo. */
    public static function marcaDe(int $idModelo): int
    {
        $stmt = Database::getConexion()->prepare('SELECT idMarca FROM modelo WHERE idModelo = :id LIMIT 1');
        $stmt->execute([':id' => $idModelo]);
        return (int) $stmt->fetchColumn();
    }
}
