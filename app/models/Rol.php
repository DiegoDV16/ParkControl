<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Rol.php
 * ==========================================================================
 * Catálogo de roles del sistema.
 * ==========================================================================
 */

class Rol
{
    /** @return array<int, array{idRol: int, rol: string}> */
    public static function todas(): array
    {
        return Database::getConexion()
            ->query('SELECT idRol, rol FROM rol ORDER BY idRol')
            ->fetchAll();
    }

    public static function existe(int $idRol): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM rol WHERE idRol = :id LIMIT 1');
        $stmt->execute([':id' => $idRol]);
        return (bool) $stmt->fetchColumn();
    }
}
