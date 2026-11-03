<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Usuario.php
 * ==========================================================================
 * Acceso a datos de la tabla `usuario`.
 * ==========================================================================
 */

class Usuario
{
    public static function porCorreo(string $correo): ?array
    {
        $sql = 'SELECT u.*, r.rol AS rolNombre
                FROM usuario u
                INNER JOIN rol r ON r.idRol = u.idRol
                WHERE u.correo = :correo
                LIMIT 1';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM usuario WHERE idUsuario = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function marcarAcceso(int $id): void
    {
        $stmt = Database::getConexion()->prepare(
            'UPDATE usuario SET ultimoAcceso = NOW() WHERE idUsuario = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    public static function actualizarHash(int $id, string $hash): void
    {
        $stmt = Database::getConexion()->prepare(
            'UPDATE usuario SET contrasena = :hash WHERE idUsuario = :id'
        );
        $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public static function contar(): int
    {
        return (int) Database::getConexion()->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
    }
}
