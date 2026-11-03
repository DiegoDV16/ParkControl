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
        $sql = 'SELECT u.*, r.rol AS rolNombre
                FROM usuario u
                INNER JOIN rol r ON r.idRol = u.idRol
                WHERE u.idUsuario = :id
                LIMIT 1';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Listado con búsqueda por nombre, correo o RUT. */
    public static function listar(string $busqueda = ''): array
    {
        $sql = 'SELECT u.*, r.rol AS rolNombre
                FROM usuario u
                INNER JOIN rol r ON r.idRol = u.idRol';
        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' WHERE (u.nombres LIKE :q OR u.apellidoPaterno LIKE :q
                      OR u.apellidoMaterno LIKE :q OR u.correo LIKE :q OR u.rut LIKE :q)';
            $parametros[':q'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY u.estado DESC, u.idUsuario ASC';

        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public static function crear(array $datos): int
    {
        $sql = 'INSERT INTO usuario (idRol, rut, nombres, apellidoPaterno, apellidoMaterno, correo, contrasena, estado)
                VALUES (:idRol, :rut, :nombres, :apellidoPaterno, :apellidoMaterno, :correo, :contrasena, 1)';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':idRol'           => $datos['idRol'],
            ':rut'             => $datos['rut'],
            ':nombres'         => $datos['nombres'],
            ':apellidoPaterno' => $datos['apellidoPaterno'],
            ':apellidoMaterno' => $datos['apellidoMaterno'],
            ':correo'          => $datos['correo'],
            ':contrasena'      => $datos['contrasena'],
        ]);

        return (int) Database::getConexion()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $sql = 'UPDATE usuario
                SET idRol = :idRol, rut = :rut, nombres = :nombres,
                    apellidoPaterno = :apellidoPaterno, apellidoMaterno = :apellidoMaterno,
                    correo = :correo
                WHERE idUsuario = :id';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':idRol'           => $datos['idRol'],
            ':rut'             => $datos['rut'],
            ':nombres'         => $datos['nombres'],
            ':apellidoPaterno' => $datos['apellidoPaterno'],
            ':apellidoMaterno' => $datos['apellidoMaterno'],
            ':correo'          => $datos['correo'],
            ':id'              => $id,
        ]);
    }

    public static function cambiarEstado(int $id, int $estado): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE usuario SET estado = :estado WHERE idUsuario = :id');
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function marcarAcceso(int $id): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE usuario SET ultimoAcceso = NOW() WHERE idUsuario = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function actualizarHash(int $id, string $hash): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE usuario SET contrasena = :hash WHERE idUsuario = :id');
        $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public static function contar(): int
    {
        return (int) Database::getConexion()->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
    }

    /** Cuántos administradores activos quedan (excluyendo opcionalmente uno). */
    public static function contarAdministradoresActivos(int $excepto = 0): int
    {
        $sql = "SELECT COUNT(*)
                FROM usuario u
                INNER JOIN rol r ON r.idRol = u.idRol
                WHERE u.estado = 1 AND r.rol = 'Administrador' AND u.idUsuario <> :excepto";
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':excepto' => $excepto]);
        return (int) $stmt->fetchColumn();
    }

    public static function existeCorreo(string $correo, int $excepto = 0): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM usuario WHERE correo = :correo AND idUsuario <> :excepto LIMIT 1'
        );
        $stmt->execute([':correo' => $correo, ':excepto' => $excepto]);
        return (bool) $stmt->fetchColumn();
    }

    public static function existeRut(string $rut, int $excepto = 0): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM usuario WHERE rut = :rut AND idUsuario <> :excepto LIMIT 1'
        );
        $stmt->execute([':rut' => $rut, ':excepto' => $excepto]);
        return (bool) $stmt->fetchColumn();
    }
}
